<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\ConfigType;
use App\Services\SeoService;
use Inertia\Inertia;
use Inertia\Response;

class ConfigTypeController extends Controller
{
    public function index(): Response
    {
        $configTypes = ConfigType::query()
            ->whereNotIn('slug', ['mcp-servers', 'prompts'])
            ->orderBy('name')
            ->get();

        return Inertia::render('config-types/index', [
            'seo' => SeoService::forConfigTypeIndex(),
            'configTypes' => $configTypes,
            'configTypeCounts' => Inertia::defer(fn () => ConfigType::query()
                ->whereNotIn('slug', ['mcp-servers', 'prompts'])
                ->withCount(['configs', 'categories'])
                ->get()
                ->mapWithKeys(fn ($type) => [
                    $type->id => [
                        'configs_count' => $type->configs_count,
                        'categories_count' => $type->categories_count,
                    ],
                ])),
        ]);
    }

    public function show(ConfigType $configType): Response
    {
        $search = request('search');

        $configs = $configType->configs()
            ->with(['submitter', 'agent', 'category'])
            ->when($search, function ($query, $search) {
                $searchLower = mb_strtolower($search);
                $query->where(function ($q) use ($searchLower) {
                    $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                        ->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchLower}%"]);
                });
            })
            ->orderByDesc('vote_score')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('config-types/show', [
            'seo' => SeoService::forConfigType($configType),
            'configType' => $configType->loadCount(['configs', 'categories']),
            'configs' => Inertia::scroll(fn () => $configs),
            'filters' => ['search' => $search],
            'agents' => Inertia::defer(fn () => Agent::query()
                ->withCount(['configs' => fn ($q) => $q->where('config_type_id', $configType->id)])
                ->get()
                ->filter(fn ($agent) => $agent->configs_count > 0)
                ->sortByDesc('configs_count')
                ->values()),
        ]);
    }
}
