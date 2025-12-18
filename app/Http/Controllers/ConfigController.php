<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Inertia\Inertia;
use Inertia\Response;

class ConfigController extends Controller
{
    public function show(Config $config): Response
    {
        $config->load([
            'user',
            'agent',
            'configType',
            'category',
            'files',
            'comments' => fn ($q) => $q->with('user')->latest()->limit(20),
        ]);

        return Inertia::render('configs/show', [
            'config' => $config,
            'relatedConfigs' => $config->allConnections()
                ->load(['user', 'agent', 'configType'])
                ->take(6),
            'moreFromUser' => Config::query()
                ->where('user_id', $config->user_id)
                ->where('id', '!=', $config->id)
                ->with(['agent', 'configType'])
                ->orderByDesc('vote_score')
                ->limit(4)
                ->get(),
        ]);
    }
}
