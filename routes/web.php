<?php

use App\Models\Prompt;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Public routes
Volt::route('/', 'home')->name('home');
Volt::route('/explore', 'explore')->name('explore');

// Categories routes
Volt::route('/categories', 'categories.index')->name('categories.index');
Volt::route('/categories/{category}', 'categories.show')->name('categories.show');

// AI Models routes
Volt::route('/models', 'models.index')->name('models.index');
Volt::route('/models/{model}', 'models.show')->name('models.show');

// Platforms routes
Volt::route('/platforms', 'platforms.index')->name('platforms.index');
Volt::route('/platforms/{platform}', 'platforms.show')->name('platforms.show');

// Prompt routes
Volt::route('/prompts/{prompt}', 'prompts.show')->name('prompts.show');

// Profile routes
Volt::route('/profile/{user}', 'profile.show')->name('profile.show');

// Dashboard redirect based on user type
Route::get('/dashboard', function () {
    if (auth()->user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('user.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Volt::route('/dashboard', 'admin.dashboard')->name('dashboard');

    // Categories Management
    Volt::route('/categories', 'admin.categories.index')->name('categories.index');
    Volt::route('/categories/create', 'admin.categories.create')->name('categories.create');
    Volt::route('/categories/{category}/edit', 'admin.categories.edit')->name('categories.edit');

    // Providers Management
    Volt::route('/providers', 'admin.providers.index')->name('providers.index');
    Volt::route('/providers/create', 'admin.providers.create')->name('providers.create');
    Volt::route('/providers/{provider}/edit', 'admin.providers.edit')->name('providers.edit');

    // AI Models Management
    Volt::route('/ai-models', 'admin.ai-models.index')->name('ai-models.index');
    Volt::route('/ai-models/create', 'admin.ai-models.create')->name('ai-models.create');
    Volt::route('/ai-models/{aiModel}/edit', 'admin.ai-models.edit')->name('ai-models.edit');

    // Platforms Management
    Volt::route('/platforms', 'admin.platforms.index')->name('platforms.index');
    Volt::route('/platforms/create', 'admin.platforms.create')->name('platforms.create');
    Volt::route('/platforms/{platform}/edit', 'admin.platforms.edit')->name('platforms.edit');

    // Prompts Management
    Volt::route('/prompts', 'admin.prompts.index')->name('prompts.index');
    Volt::route('/prompts/create', 'admin.prompts.create')->name('prompts.create');
    Volt::route('/prompts/{prompt}/edit', 'admin.prompts.edit')->name('prompts.edit');
});

// User routes
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    // User Dashboard
    Volt::route('/dashboard', 'user.dashboard')->name('dashboard');

    // User Prompts Management
    Volt::route('/prompts', 'user.prompts.index')->name('prompts.index');
    Volt::route('/prompts/create', 'user.prompts.create')->name('prompts.create');
    Volt::route('/prompts/{prompt}/edit', 'user.prompts.edit')->name('prompts.edit');
});

// Settings routes
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
