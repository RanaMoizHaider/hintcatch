<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Config;
use App\Models\McpServer;
use App\Models\Prompt;
use App\Models\Vote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    /** @var array<string, class-string> */
    private array $votableTypes = [
        'config' => Config::class,
        'prompt' => Prompt::class,
        'mcp-server' => McpServer::class,
        'comment' => Comment::class,
    ];

    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'votable_type' => ['required', 'string', 'in:config,prompt,mcp-server,comment'],
            'votable_id' => ['required', 'integer'],
            'value' => ['required', 'integer', 'in:-1,1'],
        ]);

        $user = Auth::user();
        $modelClass = $this->votableTypes[$validated['votable_type']];

        /** @var Config|Prompt|McpServer|Comment $votable */
        $votable = $modelClass::findOrFail($validated['votable_id']);

        return DB::transaction(function () use ($user, $votable, $validated) {
            $existingVote = Vote::query()
                ->where('user_id', $user->id)
                ->where('votable_type', $votable::class)
                ->where('votable_id', $votable->id)
                ->first();

            $newValue = (int) $validated['value'];

            if ($existingVote) {
                if ($existingVote->value === $newValue) {
                    $existingVote->delete();
                    $userVote = null;
                } else {
                    $existingVote->update(['value' => $newValue]);
                    $userVote = $newValue;
                }
            } else {
                Vote::create([
                    'user_id' => $user->id,
                    'votable_type' => $votable::class,
                    'votable_id' => $votable->id,
                    'value' => $newValue,
                ]);
                $userVote = $newValue;
            }

            $voteScore = Vote::query()
                ->where('votable_type', $votable::class)
                ->where('votable_id', $votable->id)
                ->sum('value');

            if (property_exists($votable, 'vote_score') || $votable->getConnection()->getSchemaBuilder()->hasColumn($votable->getTable(), 'vote_score')) {
                $votable->update(['vote_score' => $voteScore]);
            }

            return response()->json([
                'vote_score' => (int) $voteScore,
                'user_vote' => $userVote,
            ]);
        });
    }
}
