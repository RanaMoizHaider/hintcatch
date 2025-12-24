<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Favorite;
use App\Models\McpServer;
use App\Models\Prompt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /** @var array<string, class-string> */
    private array $favorableTypes = [
        'config' => Config::class,
        'prompt' => Prompt::class,
        'mcp-server' => McpServer::class,
    ];

    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'favorable_type' => ['required', 'string', 'in:config,prompt,mcp-server'],
            'favorable_id' => ['required', 'integer'],
        ]);

        $user = Auth::user();
        $modelClass = $this->favorableTypes[$validated['favorable_type']];

        $modelClass::findOrFail($validated['favorable_id']);

        $existingFavorite = Favorite::query()
            ->where('user_id', $user->id)
            ->where('favorable_type', $modelClass)
            ->where('favorable_id', $validated['favorable_id'])
            ->first();

        if ($existingFavorite) {
            $existingFavorite->delete();
            $isFavorited = false;
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'favorable_type' => $modelClass,
                'favorable_id' => $validated['favorable_id'],
            ]);
            $isFavorited = true;
        }

        $favoritesCount = Favorite::query()
            ->where('favorable_type', $modelClass)
            ->where('favorable_id', $validated['favorable_id'])
            ->count();

        return response()->json([
            'is_favorited' => $isFavorited,
            'favorites_count' => $favoritesCount,
        ]);
    }
}
