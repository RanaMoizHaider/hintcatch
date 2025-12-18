<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ConfigTypeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\McpServerController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\SubmitController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public pages
Route::get('/', HomeController::class)->name('home');
Route::get('/about', AboutController::class)->name('about');
Route::get('/search', SearchController::class)->name('search');

// Agents
Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
Route::get('/agents/{agent:slug}', [AgentController::class, 'show'])->name('agents.show');
Route::get('/agents/{agent:slug}/configs/{configType:slug}', [AgentController::class, 'configs'])->name('agents.configs')->withoutScopedBindings();

// Config Types
Route::get('/configs', [ConfigTypeController::class, 'index'])->name('config-types.index');
Route::get('/configs/{configType:slug}', [ConfigTypeController::class, 'show'])->name('config-types.show');

// Single Config
Route::get('/c/{config:slug}', [ConfigController::class, 'show'])->name('configs.show');

// MCP Servers
Route::get('/mcps', [McpServerController::class, 'index'])->name('mcp-servers.index');
Route::get('/mcps/{mcpServer:slug}', [McpServerController::class, 'show'])->name('mcp-servers.show');

// Prompts
Route::get('/prompts', [PromptController::class, 'index'])->name('prompts.index');
Route::get('/prompts/{prompt:slug}', [PromptController::class, 'show'])->name('prompts.show');

// User Profiles
Route::get('/u/{user:username}', [UserProfileController::class, 'show'])->name('users.show');

// Social Authentication
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Submit routes
    Route::get('submit', [SubmitController::class, 'index'])->name('submit.index');
    Route::get('submit/config', [SubmitController::class, 'createConfig'])->name('submit.config');
    Route::post('submit/config', [SubmitController::class, 'storeConfig'])->name('submit.config.store');
    Route::get('submit/mcp-server', [SubmitController::class, 'createMcpServer'])->name('submit.mcp-server');
    Route::post('submit/mcp-server', [SubmitController::class, 'storeMcpServer'])->name('submit.mcp-server.store');
    Route::get('submit/prompt', [SubmitController::class, 'createPrompt'])->name('submit.prompt');
    Route::post('submit/prompt', [SubmitController::class, 'storePrompt'])->name('submit.prompt.store');
});

require __DIR__.'/settings.php';
