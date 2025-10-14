<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Player;
use App\Models\Club;
use App\Observers\PlayerObserver;
use App\Observers\ClubObserver;

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
        // Force HTTP locally to avoid HTTPS redirects during local Docker usage
        if (in_array(config('app.env'), ['local', 'docker', 'development'], true)) {
            URL::forceScheme('http');
            $this->app['request']->server->set('HTTPS', false);
        }
        
        // Register model observers for automatic cache clearing
        Player::observe(PlayerObserver::class);
        Club::observe(ClubObserver::class);
        
        // Set PHP session garbage collection maxlifetime to match Laravel session lifetime
        // This prevents PHP from expiring sessions before Laravel expects them to expire
        if (config('session.force_php_gc_maxlifetime', false)) {
            $sessionLifetime = config('session.lifetime', 480) * 60; // Convert minutes to seconds
            ini_set('session.gc_maxlifetime', $sessionLifetime);
        }
    }
}
