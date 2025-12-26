<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Category;
use App\Models\Config;
use App\Models\ConfigType;
use App\Models\McpServer;
use App\Models\Prompt;
use App\Models\Skill;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $query = $request->input('q', '');
        $type = $request->input('type', 'all');
        $agentId = $request->input('agent');
        $configTypeId = $request->input('config_type');
        $categoryId = $request->input('category');
        $promptCategory = $request->input('prompt_category');
        $sort = $request->input('sort', 'recent');

        $configs = collect();
        $mcpServers = collect();
        $prompts = collect();
        $skills = collect();

        $searchTerm = '%'.$query.'%';

        if ($type === 'all' || $type === 'configs') {
            $configsQuery = Config::query()
                ->with(['submitter', 'agent', 'configType', 'category'])
                ->when($query, function ($q) use ($searchTerm) {
                    $q->where(function ($sub) use ($searchTerm) {
                        $sub->where('name', 'ilike', $searchTerm)
                            ->orWhere('description', 'ilike', $searchTerm)
                            ->orWhere('source_author', 'ilike', $searchTerm);
                    });
                })
                ->when($agentId, fn ($q) => $q->where('agent_id', $agentId))
                ->when($configTypeId, fn ($q) => $q->where('config_type_id', $configTypeId))
                ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId));

            $configsQuery = match ($sort) {
                'recent' => $configsQuery->orderByDesc('created_at'),
                'top' => $configsQuery->orderByDesc('vote_score'),
                default => $configsQuery->orderByDesc('vote_score')->orderByDesc('created_at'),
            };

            $configs = $configsQuery->limit(20)->get();
        }

        if ($type === 'all' || $type === 'mcp-servers') {
            $mcpServersQuery = McpServer::query()
                ->with('submitter')
                ->when($query, function ($q) use ($searchTerm) {
                    $q->where(function ($sub) use ($searchTerm) {
                        $sub->where('name', 'ilike', $searchTerm)
                            ->orWhere('description', 'ilike', $searchTerm)
                            ->orWhere('source_author', 'ilike', $searchTerm);
                    });
                });

            $mcpServersQuery = match ($sort) {
                'recent' => $mcpServersQuery->orderByDesc('created_at'),
                'top' => $mcpServersQuery->orderByDesc('vote_score'),
                default => $mcpServersQuery->orderByDesc('vote_score')->orderByDesc('created_at'),
            };

            $mcpServers = $mcpServersQuery->limit(20)->get();
        }

        if ($type === 'all' || $type === 'prompts') {
            $promptsQuery = Prompt::query()
                ->with('submitter')
                ->when($query, function ($q) use ($searchTerm) {
                    $q->where(function ($sub) use ($searchTerm) {
                        $sub->where('name', 'ilike', $searchTerm)
                            ->orWhere('description', 'ilike', $searchTerm)
                            ->orWhere('content', 'ilike', $searchTerm)
                            ->orWhere('source_author', 'ilike', $searchTerm);
                    });
                })
                ->when($promptCategory, fn ($q) => $q->where('category', $promptCategory));

            $promptsQuery = match ($sort) {
                'recent' => $promptsQuery->orderByDesc('created_at'),
                'top' => $promptsQuery->orderByDesc('vote_score'),
                default => $promptsQuery->orderByDesc('vote_score')->orderByDesc('created_at'),
            };

            $prompts = $promptsQuery->limit(20)->get();
        }

        if ($type === 'all' || $type === 'skills') {
            $skillsQuery = Skill::query()
                ->with(['submitter', 'category'])
                ->when($query, function ($q) use ($searchTerm) {
                    $q->where(function ($sub) use ($searchTerm) {
                        $sub->where('name', 'ilike', $searchTerm)
                            ->orWhere('description', 'ilike', $searchTerm)
                            ->orWhere('content', 'ilike', $searchTerm)
                            ->orWhere('source_author', 'ilike', $searchTerm);
                    });
                })
                ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId));

            $skillsQuery = match ($sort) {
                'recent' => $skillsQuery->orderByDesc('created_at'),
                'top' => $skillsQuery->orderByDesc('vote_score'),
                default => $skillsQuery->orderByDesc('vote_score')->orderByDesc('created_at'),
            };

            $skills = $skillsQuery->limit(20)->get();
        }

        return Inertia::render('search', [
            'query' => $query,
            'filters' => [
                'type' => $type,
                'agent' => $agentId,
                'config_type' => $configTypeId,
                'category' => $categoryId,
                'prompt_category' => $promptCategory,
                'sort' => $sort,
            ],
            'results' => [
                'configs' => $configs,
                'mcpServers' => $mcpServers,
                'prompts' => $prompts,
                'skills' => $skills,
            ],
            'counts' => [
                'configs' => $configs->count(),
                'mcpServers' => $mcpServers->count(),
                'prompts' => $prompts->count(),
                'skills' => $skills->count(),
                'total' => $configs->count() + $mcpServers->count() + $prompts->count() + $skills->count(),
            ],
            'agents' => Agent::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'configTypes' => ConfigType::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug', 'config_type_id']),
            'promptCategories' => ['system', 'task', 'review', 'documentation', 'debugging', 'refactoring'],
        ]);
    }
}
