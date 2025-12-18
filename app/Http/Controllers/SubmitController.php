<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Config;
use App\Models\ConfigFile;
use App\Models\ConfigType;
use App\Models\McpServer;
use App\Models\Prompt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SubmitController extends Controller
{
    /**
     * Generate a unique slug for a model.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $modelClass
     */
    protected function generateUniqueSlug(string $name, string $modelClass): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while ($modelClass::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Show the submission type selection page.
     */
    public function index(): Response
    {
        return Inertia::render('submit/index', [
            'agents' => Agent::orderBy('name')->get(),
            'configTypes' => ConfigType::with('categories')->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the config submission form.
     */
    public function createConfig(): Response
    {
        return Inertia::render('submit/config', [
            'agents' => Agent::orderBy('name')->get(),
            'configTypes' => ConfigType::with('categories')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a new config.
     */
    public function storeConfig(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'agent_id' => ['required', 'exists:agents,id'],
            'config_type_id' => ['required', 'exists:config_types,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'source_author' => ['nullable', 'string', 'max:255'],
            'files' => ['required', 'array', 'min:1'],
            'files.*.filename' => ['required', 'string', 'max:255'],
            'files.*.content' => ['required', 'string'],
            'files.*.language' => ['required', 'string', 'max:50'],
            'files.*.path' => ['nullable', 'string', 'max:500'],
        ]);

        $config = Config::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['name'], Config::class),
            'description' => $validated['description'],
            'agent_id' => $validated['agent_id'],
            'config_type_id' => $validated['config_type_id'],
            'category_id' => $validated['category_id'] ?? null,
            'source_url' => $validated['source_url'] ?? null,
            'source_author' => $validated['source_author'] ?? null,
            'version' => '1.0.0',
        ]);

        foreach ($validated['files'] as $index => $file) {
            ConfigFile::create([
                'config_id' => $config->id,
                'filename' => $file['filename'],
                'content' => $file['content'],
                'language' => $file['language'],
                'path' => $file['path'] ?? null,
                'is_primary' => $index === 0,
                'order' => $index,
            ]);
        }

        return redirect()->route('configs.show', $config->slug)
            ->with('success', 'Config submitted successfully!');
    }

    /**
     * Show the MCP server submission form.
     */
    public function createMcpServer(): Response
    {
        return Inertia::render('submit/mcp-server', [
            'agents' => Agent::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a new MCP server.
     */
    public function storeMcpServer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'type' => ['required', Rule::in(['remote', 'local'])],
            'url' => ['nullable', 'required_if:type,remote', 'url', 'max:500'],
            'command' => ['nullable', 'required_if:type,local', 'string', 'max:500'],
            'args' => ['nullable', 'array'],
            'args.*' => ['string'],
            'env' => ['nullable', 'array'],
            'headers' => ['nullable', 'array'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'source_author' => ['nullable', 'string', 'max:255'],
        ]);

        $mcpServer = McpServer::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['name'], McpServer::class),
            'description' => $validated['description'],
            'type' => $validated['type'],
            'url' => $validated['url'] ?? null,
            'command' => $validated['command'] ?? null,
            'args' => $validated['args'] ?? null,
            'env' => $validated['env'] ?? null,
            'headers' => $validated['headers'] ?? null,
            'source_url' => $validated['source_url'] ?? null,
            'source_author' => $validated['source_author'] ?? null,
        ]);

        return redirect()->route('mcp-servers.show', $mcpServer->slug)
            ->with('success', 'MCP Server submitted successfully!');
    }

    /**
     * Show the prompt submission form.
     */
    public function createPrompt(): Response
    {
        return Inertia::render('submit/prompt');
    }

    /**
     * Store a new prompt.
     */
    public function storePrompt(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'category' => ['required', Rule::in(['system', 'task', 'review', 'documentation', 'debugging', 'refactoring'])],
            'source_url' => ['nullable', 'url', 'max:500'],
            'source_author' => ['nullable', 'string', 'max:255'],
        ]);

        $prompt = Prompt::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['name'], Prompt::class),
            'description' => $validated['description'],
            'content' => $validated['content'],
            'category' => $validated['category'],
            'source_url' => $validated['source_url'] ?? null,
            'source_author' => $validated['source_author'] ?? null,
        ]);

        return redirect()->route('prompts.show', $prompt->slug)
            ->with('success', 'Prompt submitted successfully!');
    }
}
