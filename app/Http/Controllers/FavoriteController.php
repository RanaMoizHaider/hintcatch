<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Favorite;
use App\Models\McpServer;
use App\Models\Prompt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /** @var array<string, class-string> */
    private array $favoritableTypes = [
        'config' => Config::class,
        'prompt' => Prompt::class,
        'mcp-server' => McpServer::class,
    ];

    public function toggle(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'favoritable_type' => ['required', 'string', 'in:config,prompt,mcp-server'],
            'favoritable_id' => ['required', 'integer'],
        ]);

        $user = Auth::user();
        $modelClass = $this->favoritableTypes[$validated['favoritable_type']];

        $modelClass::findOrFail($validated['favoritable_id']);

        $existingFavorite = Favorite::query()
            ->where('user_id', $user->id)
            ->where('favoritable_type', $modelClass)
            ->where('favoritable_id', $validated['favoritable_id'])
            ->first();

        if ($existingFavorite) {
            $existingFavorite->delete();
            $isFavorited = false;
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'favoritable_type' => $modelClass,
                'favoritable_id' => $validated['favoritable_id'],
            ]);
            $isFavorited = true;
        }

        return back();
    }
}
