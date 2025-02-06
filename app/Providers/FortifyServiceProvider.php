<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(LoginResponse::class, new class implements LoginResponse
        {
            public function toResponse($request): RedirectResponse
            {
                $authenticated_user = auth()->user();

                if ($authenticated_user->hasRole('Observer')) {
                    $can_access_admin = true;
                } else {
                    // Staff members
                    $can_access_admin = (
                        str_ends_with($authenticated_user->email, '@nlrc.ph') || str_ends_with($authenticated_user->email, '@nlrc.fi') || $authenticated_user->email == 'storm_iz_here@yahoo.com'
                    ) && in_array($authenticated_user->role_id, ([1, 2, 6, 7]));
                }

                $user_route = match($can_access_admin) {
                    true => 'admin',
                    false => route('dashboard')
                };

                return redirect($user_route);
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                if ($user->restricted == 1) {
                    throw ValidationException::withMessages(['email' => 'Your account is restricted from logging in. Please contact the administrator.']);
                }

                return $user;
            }
        });
    }
}
