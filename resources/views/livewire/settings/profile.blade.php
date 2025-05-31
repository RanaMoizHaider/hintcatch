<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $username = '';
    public string $bio = '';
    public string $location = '';
    public string $website = '';
    public array $social_links = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->username = $user->username ?? '';
        $this->bio = $user->bio ?? '';
        $this->location = $user->location ?? '';
        $this->website = $user->website ?? '';
        $this->social_links = $user->social_links ?? [
            'twitter' => '',
            'linkedin' => '',
            'github' => '',
            'website' => '',
        ];
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
            'username' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique(User::class)->ignore($user->id)
            ],
            'bio' => ['nullable', 'string', 'max:500'],
            'location' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'social_links.twitter' => ['nullable', 'string', 'max:255'],
            'social_links.linkedin' => ['nullable', 'string', 'max:255'],
            'social_links.github' => ['nullable', 'string', 'max:255'],
            'social_links.website' => ['nullable', 'url', 'max:255'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your profile information')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <!-- Basic Information -->
            <div class="space-y-4">
                <flux:subheading class="text-base font-medium">Basic Information</flux:subheading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:input wire:model="name" label="Full Name" type="text" required autofocus autocomplete="name" />
                        <flux:error name="name" />
                    </flux:field>
                    <flux:field>
                        <flux:input wire:model="username" label="Username" type="text" autocomplete="username" placeholder="Optional username" />
                        <flux:error name="username" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:input wire:model="email" label="Email" type="email" required autocomplete="email" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:textarea wire:model="bio" label="Bio" placeholder="Tell us about yourself..." rows="3" />
                    <flux:error name="bio" />
                    <flux:description>Maximum 500 characters</flux:description>
                </flux:field>
            </div>

            <!-- Location & Website -->
            <div class="space-y-4">
                <flux:subheading class="text-base font-medium">Location & Contact</flux:subheading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:input wire:model="location" label="Location" type="text" placeholder="City, Country" />
                        <flux:error name="location" />
                    </flux:field>
                    <flux:field>
                        <flux:input wire:model="website" label="Website" type="url" placeholder="https://your-website.com" />
                        <flux:error name="website" />
                    </flux:field>
                </div>
            </div>

            <!-- Social Links -->
            <div class="space-y-4">
                <flux:subheading class="text-base font-medium">Social Links</flux:subheading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:input wire:model="social_links.twitter" label="Twitter" type="text" placeholder="@username or full URL" />
                        <flux:error name="social_links.twitter" />
                    </flux:field>
                    <flux:field>
                        <flux:input wire:model="social_links.linkedin" label="LinkedIn" type="text" placeholder="Profile URL" />
                        <flux:error name="social_links.linkedin" />
                    </flux:field>
                    <flux:field>
                        <flux:input wire:model="social_links.github" label="GitHub" type="text" placeholder="@username or full URL" />
                        <flux:error name="social_links.github" />
                    </flux:field>
                    <flux:field>
                        <flux:input wire:model="social_links.website" label="Personal Website" type="url" placeholder="https://your-site.com" />
                        <flux:error name="social_links.website" />
                    </flux:field>
                </div>
            </div>

            </div>

            <!-- Email Verification Notice -->
            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-950">
                    <flux:text class="text-amber-800 dark:text-amber-200">
                        Your email address is unverified.
                        <flux:link class="text-sm cursor-pointer underline" wire:click.prevent="resendVerificationNotification">
                            Click here to re-send the verification email.
                        </flux:link>
                    </flux:text>

                    @if (session('status') === 'verification-link-sent')
                        <flux:text class="mt-2 font-medium text-green-600 dark:text-green-400">
                            A new verification link has been sent to your email address.
                        </flux:text>
                    @endif
                </div>
            @endif

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">Save Profile</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    Saved.
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
