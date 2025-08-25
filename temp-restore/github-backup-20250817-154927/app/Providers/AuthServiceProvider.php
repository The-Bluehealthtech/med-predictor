<?php

namespace App\Providers;

use App\Models\PlayerLicense;
use App\Models\User;
use App\Models\Player;
use App\Models\Club;
use App\Models\MatchModel;
use App\Policies\PlayerLicensePolicy;
use App\Policies\UserPolicy;
use App\Policies\PlayerPolicy;
use App\Policies\ClubPolicy;
use App\Policies\MatchPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PlayerLicense::class => PlayerLicensePolicy::class,
        User::class => UserPolicy::class,
        Player::class => PlayerPolicy::class,
        Club::class => ClubPolicy::class,
        MatchModel::class => MatchPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function register(): void
    {
        // Manual policy registration removed to avoid conflicts
    }

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
} 