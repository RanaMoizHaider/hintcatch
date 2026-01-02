<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AgentRequest;
use App\Models\Agent;
use App\Models\ConfigType;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('admin/agents/index', [
            'agents' => Agent::query()
                ->withCount('configs')
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/agents/form', [
            'agent' => null,
            'configTypes' => ConfigType::all(['id', 'name', 'slug']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AgentRequest $request): RedirectResponse
    {
        Agent::create($request->validated());

        return redirect()
            ->route('admin.agents.index')
            ->with('success', 'Agent created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agent $agent): Response
    {
        return Inertia::render('admin/agents/form', [
            'agent' => $agent,
            'configTypes' => ConfigType::all(['id', 'name', 'slug']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AgentRequest $request, Agent $agent): RedirectResponse
    {
        $agent->update($request->validated());

        return redirect()
            ->route('admin.agents.index')
            ->with('success', 'Agent updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agent $agent): RedirectResponse
    {
        $agent->delete();

        return redirect()
            ->route('admin.agents.index')
            ->with('success', 'Agent deleted successfully.');
    }
}
