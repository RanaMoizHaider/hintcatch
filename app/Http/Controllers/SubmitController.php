<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Category;
use App\Models\Config;
use App\Models\ConfigFile;
use App\Models\ConfigType;
use App\Models\McpServer;
use App\Models\Prompt;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SubmitController extends Controller
{
    /**
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

    public function index(): Response
    {
        return Inertia::render('submit/index', [
            'agents' => Agent::orderBy('name')->get(),
            'configTypes' => ConfigType::with('categories')->orderBy('name')->get(),
        ]);
    }

    public function createConfig(): Response
    {
        return Inertia::render('submit/config', [
            'agents' => Agent::orderBy('name')->get(),
            'configTypes' => ConfigType::with('categories')->orderBy('name')->get(),
        ]);
    }

    public function storeConfig(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'agent_id' => ['nullable', 'exists:agents,id'],
            'config_type_id' => ['required', 'exists:config_types,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'source_author' => ['nullable', 'string', 'max:255'],
            'files' => ['nullable', 'array'],
            'files.*.filename' => ['required', 'string', 'max:255'],
            'files.*.content' => ['required', 'string'],
            'files.*.language' => ['required', 'string', 'max:50'],
            'files.*.path' => ['nullable', 'string', 'max:500'],
        ]);

        $config = Config::create([
            'submitted_by' => $request->user()->id,
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['name'], Config::class),
            'description' => $validated['description'],
            'agent_id' => $validated['agent_id'] ?? null,
            'config_type_id' => $validated['config_type_id'],
            'category_id' => $validated['category_id'] ?? null,
            'source_url' => $validated['source_url'] ?? null,
            'source_author' => $validated['source_author'] ?? null,
            'version' => '1.0.0',
        ]);

        if (! empty($validated['files'])) {
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
        }

        return redirect()->route('configs.show', $config->slug)
            ->with('success', 'Config submitted successfully!');
    }

    public function createMcpServer(): Response
    {
        return Inertia::render('submit/mcp-server', [
            'agents' => Agent::orderBy('name')->get(),
        ]);
    }

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
            'submitted_by' => $request->user()->id,
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

    public function createPrompt(): Response
    {
        return Inertia::render('submit/prompt');
    }

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
            'submitted_by' => $request->user()->id,
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

    public function createSkill(): Response
    {
        return Inertia::render('submit/skill', [
            'categories' => Category::orderBy('name')->get(),
            'agents' => Agent::whereNotNull('skills_config_template')->orderBy('name')->get(),
        ]);
    }

    public function storeSkill(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'license' => ['nullable', 'string', 'max:100'],
            'compatibility' => ['nullable', 'array'],
            'compatibility.agents' => ['nullable', 'array'],
            'compatibility.agents.*' => ['string'],
            'metadata' => ['nullable', 'array'],
            'allowed_tools' => ['nullable', 'array'],
            'allowed_tools.*' => ['string'],
            'scripts' => ['nullable', 'array'],
            'scripts.*.filename' => ['required', 'string', 'max:255'],
            'scripts.*.content' => ['required', 'string'],
            'scripts.*.description' => ['nullable', 'string', 'max:500'],
            'references' => ['nullable', 'array'],
            'references.*.title' => ['required', 'string', 'max:255'],
            'references.*.url' => ['required', 'url', 'max:500'],
            'references.*.description' => ['nullable', 'string', 'max:500'],
            'assets' => ['nullable', 'array'],
            'assets.*.filename' => ['required', 'string', 'max:255'],
            'assets.*.content' => ['required', 'string'],
            'assets.*.description' => ['nullable', 'string', 'max:500'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'source_author' => ['nullable', 'string', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:500'],
        ]);

        $skill = Skill::create([
            'submitted_by' => $request->user()->id,
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['name'], Skill::class),
            'description' => $validated['description'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'] ?? null,
            'license' => $validated['license'] ?? null,
            'compatibility' => $validated['compatibility'] ?? null,
            'metadata' => $validated['metadata'] ?? null,
            'allowed_tools' => $validated['allowed_tools'] ?? null,
            'scripts' => $validated['scripts'] ?? null,
            'references' => $validated['references'] ?? null,
            'assets' => $validated['assets'] ?? null,
            'source_url' => $validated['source_url'] ?? null,
            'source_author' => $validated['source_author'] ?? null,
            'github_url' => $validated['github_url'] ?? null,
        ]);

        return redirect()->route('skills.show', $skill->slug)
            ->with('success', 'Skill submitted successfully!');
    }
}
