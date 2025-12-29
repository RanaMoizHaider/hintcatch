<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ConfigTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\McpServerController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\SubmitController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\VoteController;
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
Route::get('/config-types', [ConfigTypeController::class, 'index'])->name('config-types.index');
Route::get('/configs/{configType:slug}', [ConfigTypeController::class, 'show'])->name('config-types.show');

// Configs
Route::get('/configs', [ConfigController::class, 'index'])->name('configs.index');
Route::get('/c/{config:slug}', [ConfigController::class, 'show'])->name('configs.show');

// MCP Servers
Route::get('/mcps', [McpServerController::class, 'index'])->name('mcp-servers.index');
Route::get('/mcps/{mcpServer:slug}', [McpServerController::class, 'show'])->name('mcp-servers.show');

// Prompts
Route::get('/prompts', [PromptController::class, 'index'])->name('prompts.index');
Route::get('/prompts/{prompt:slug}', [PromptController::class, 'show'])->name('prompts.show');

// Skills
Route::get('/skills', [SkillController::class, 'index'])->name('skills.index');
Route::get('/skills/{skill:slug}', [SkillController::class, 'show'])->name('skills.show');

// User Profiles
Route::get('/u/{user:username}', [UserProfileController::class, 'show'])->name('users.show');

// Social Authentication
Route::get('/login', function () {
    return Inertia::render('auth/login', [
        'socialProviders' => [
            'github' => config('services.github.client_id') !== null,
            'gitlab' => config('services.gitlab.client_id') !== null,
        ],
    ]);
})->name('login');
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
Route::post('/logout', [SocialAuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Submit routes
    Route::get('submit', [SubmitController::class, 'index'])->name('submit.index');
    Route::get('submit/config', [SubmitController::class, 'createConfig'])->name('submit.config');
    Route::post('submit/config', [SubmitController::class, 'storeConfig'])->name('submit.config.store');
    Route::get('submit/mcp-server', [SubmitController::class, 'createMcpServer'])->name('submit.mcp-server');
    Route::post('submit/mcp-server', [SubmitController::class, 'storeMcpServer'])->name('submit.mcp-server.store');
    Route::get('submit/prompt', [SubmitController::class, 'createPrompt'])->name('submit.prompt');
    Route::post('submit/prompt', [SubmitController::class, 'storePrompt'])->name('submit.prompt.store');
    Route::get('submit/skill', [SubmitController::class, 'createSkill'])->name('submit.skill');
    Route::post('submit/skill', [SubmitController::class, 'storeSkill'])->name('submit.skill.store');

    // Interaction routes (votes, favorites, comments)
    Route::post('votes', [VoteController::class, 'toggle'])->name('votes.toggle');
    Route::post('favorites', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('comments', [CommentController::class, 'store'])->name('comments.store');
    Route::patch('comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

require __DIR__.'/settings.php';
