<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Relations\Relation;

use App\Models\Pasivouno;
use App\Models\Pasivodos;
use App\Models\Persona;
use Carbon\Carbon;
class AppServiceProvider extends ServiceProvider
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
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        Relation::morphMap([
            'pasivouno' => Pasivouno::class,
            'pasivodos' => Pasivodos::class,
            'persona' => Persona::class,
        ]);
        Carbon::setLocale('es');
        
    }
}
