<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Inertia\Inertia;
use Inertia\Response;

class PromptController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('prompts/index', [
            'prompts' => Prompt::query()
                ->with('user')
                ->orderByDesc('vote_score')
                ->paginate(24),
            'featuredPrompts' => Prompt::query()
                ->with('user')
                ->where('is_featured', true)
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get(),
            'categories' => Prompt::query()
                ->select('category')
                ->distinct()
                ->whereNotNull('category')
                ->pluck('category'),
        ]);
    }

    public function show(Prompt $prompt): Response
    {
        $prompt->load([
            'user',
            'comments' => fn ($q) => $q->with('user')->latest()->limit(20),
        ]);

        return Inertia::render('prompts/show', [
            'prompt' => $prompt,
            'relatedPrompts' => Prompt::query()
                ->where('category', $prompt->category)
                ->where('id', '!=', $prompt->id)
                ->with('user')
                ->orderByDesc('vote_score')
                ->limit(4)
                ->get(),
            'moreFromUser' => Prompt::query()
                ->where('user_id', $prompt->user_id)
                ->where('id', '!=', $prompt->id)
                ->orderByDesc('vote_score')
                ->limit(4)
                ->get(),
        ]);
    }
}
