<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['login' => 'Google login failed. Please try again.']);
        }

        // Find by google_id first, then by email
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user && $googleUser->getEmail()) {
            $user = User::where('email', $googleUser->getEmail())->first();
        }

        if ($user) {
            // Update google_id if not set
            if (!$user->google_id) {
                $user->update([
                    'google_id'     => $googleUser->getId(),
                    'google_avatar' => $googleUser->getAvatar(),
                ]);
            }
        } else {
            // Create new user — phone is required/unique in DB, so use a temp
            // placeholder ('gtmp_' prefix) until the user completes their profile
            do {
                $tempPhone = 'gtmp_' . Str::random(15);
            } while (User::where('phone', $tempPhone)->exists());

            $user = User::create([
                'name'          => $googleUser->getName(),
                'email'         => $googleUser->getEmail(),
                'phone'         => $tempPhone,
                'google_id'     => $googleUser->getId(),
                'google_avatar' => $googleUser->getAvatar(),
                'status'        => 'active',
                'password'      => null,
            ]);

            // Assign default customer role if Spatie roles exist
            try {
                $user->assignRole('customer');
            } catch (\Exception $e) {
                // Role may not exist — skip
            }
        }

        if ($user->status === 'blocked') {
            return redirect()->route('login')->withErrors(['login' => 'Your account has been blocked.']);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended(route('customer.dashboard'));
    }
}
