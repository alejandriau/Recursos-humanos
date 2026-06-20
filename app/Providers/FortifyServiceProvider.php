<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use App\Services\CorrespondenciaAuthService; //nuevo service par alogin con correspondencia
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
                // 👇 NUEVO: autenticación combinada local + correspondencia
        Fortify::authenticateUsing(function (Request $request) {
            $login    = $request->input('email'); // se usa como "usuario"
            $password = $request->input('password');

            // 1) Admins locales
            $localUser = User::where('origen', 'local')
                ->where(function ($q) use ($login) {
                    $q->where('email', $login)->orWhere('usuario', $login);
                })
                ->first();

            if ($localUser && Hash::check($password, $localUser->password)) {
                return $localUser;
            }

            // 2) Empleados vía API de correspondencia
            return app(CorrespondenciaAuthService::class)
                ->autenticarEmpleado($login, $password);
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
