<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Category;
use App\Models\ConfigType;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): Response
    {
        return Inertia::render('admin/dashboard', [
            'stats' => Inertia::defer(fn () => [
                'totalAgents' => Agent::count(),
                'totalCategories' => Category::count(),
                'totalConfigTypes' => ConfigType::count(),
            ]),
            'recentAgents' => Inertia::defer(fn () => Agent::latest()->limit(5)->get()),
            'recentCategories' => Inertia::defer(fn () => Category::with('configType')->latest()->limit(5)->get()),
            'recentConfigTypes' => Inertia::defer(fn () => ConfigType::latest()->limit(5)->get()),
        ]);
    }
}
