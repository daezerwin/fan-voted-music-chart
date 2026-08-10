<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthenticateSocialiteUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SocialiteController extends Controller
{
    /**
     * @var list<string>
     */
    public const SUPPORTED_PROVIDERS = ['facebook'];

    public function redirect(string $provider): RedirectResponse|Response
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, Request $request, AuthenticateSocialiteUser $authenticate): RedirectResponse
    {
        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (Throwable $e) {
            Log::warning('Socialite authentication failed.', [
                'provider' => $provider,
                'exception' => $e->getMessage(),
            ]);

            return redirect()->route('login')->with('error', 'Sign-in failed. Please try again.');
        }

        $user = $authenticate($provider, $socialiteUser);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }
}
