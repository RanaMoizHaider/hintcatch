<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirect(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the callback from the provider.
     */
    public function callback(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed. Please try again.');
        }

        $user = $this->findOrCreateUser($socialUser, $provider);

        Auth::login($user, true);

        // Check if user was trying to submit something
        $intendedUrl = session()->pull('url.intended', route('dashboard'));

        return redirect()->to($intendedUrl);
    }

    /**
     * Find or create a user based on the social provider.
     */
    private function findOrCreateUser(object $socialUser, string $provider): User
    {
        $providerIdColumn = "{$provider}_id";
        $providerUsernameColumn = "{$provider}_username";
        $providerTokenColumn = "{$provider}_token";
        $providerRefreshTokenColumn = "{$provider}_refresh_token";

        // First, try to find by provider ID
        $user = User::where($providerIdColumn, $socialUser->getId())->first();

        if ($user) {
            // Update tokens
            $user->update([
                $providerTokenColumn => $socialUser->token,
                $providerRefreshTokenColumn => $socialUser->refreshToken,
                'avatar' => $socialUser->getAvatar() ?? $user->avatar,
            ]);

            return $user;
        }

        // Try to find by email
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Link the social account to existing user
            $user->update([
                $providerIdColumn => $socialUser->getId(),
                $providerUsernameColumn => $socialUser->getNickname(),
                $providerTokenColumn => $socialUser->token,
                $providerRefreshTokenColumn => $socialUser->refreshToken,
                'avatar' => $socialUser->getAvatar() ?? $user->avatar,
            ]);

            return $user;
        }

        // Create new user
        return User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname(),
            'email' => $socialUser->getEmail(),
            'username' => $this->generateUniqueUsername($socialUser->getNickname() ?? $socialUser->getName()),
            'avatar' => $socialUser->getAvatar(),
            $providerIdColumn => $socialUser->getId(),
            $providerUsernameColumn => $socialUser->getNickname(),
            $providerTokenColumn => $socialUser->token,
            $providerRefreshTokenColumn => $socialUser->refreshToken,
            'email_verified_at' => now(),
            'password' => bcrypt(Str::random(32)),
        ]);
    }

    /**
     * Generate a unique username.
     */
    private function generateUniqueUsername(?string $base): string
    {
        $base = $base ? Str::slug($base, '') : 'user';
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base.$counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Validate the provider.
     */
    private function validateProvider(string $provider): void
    {
        if (! in_array($provider, ['github', 'gitlab'])) {
            abort(404, 'Provider not supported.');
        }
    }

    /**
     * Log the user out.
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('home');
    }
}
