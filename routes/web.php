<?php

use App\Models\{Prompt, Category, AiModel, Platform, User};
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Home page with prompts
Volt::route('/', 'home')->name('home');

Volt::route('/explore', 'explore')
    ->name('explore');

// Categories routes
Volt::route('/categories', 'categories.index')
    ->name('categories.index');

Volt::route('/categories/{category}', 'categories.show')
    ->name('categories.show');

// AI Models routes
Volt::route('/models', 'models.index')
    ->name('models.index');

Volt::route('/models/{model}', 'models.show')
    ->name('models.show');

// Platforms routes
Volt::route('/platforms', 'platforms.index')
    ->name('platforms.index');

Volt::route('/platforms/{platform}', 'platforms.show')
    ->name('platforms.show');

// Prompt routes
Volt::route('/prompts/{prompt}', 'prompts.show')
    ->name('prompts.show');

// Profile routes
Volt::route('/profile/{user}', 'profile.show')
    ->name('profile.show');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Admin routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Volt::route('/dashboard', 'admin.dashboard')->name('dashboard');
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
