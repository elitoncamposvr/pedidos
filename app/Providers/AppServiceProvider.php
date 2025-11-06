<?php

namespace App\Providers;

use App\Models\PurchaseRequest;
use App\Policies\PurchaseRequestPolicy;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\StatusTransitionService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ActivityLogger::class);
        $this->app->singleton(StatusTransitionService::class);
    }

    public function boot(): void
    {
        /**
         * Registro da Policy do sistema de pedidos
         */
        Gate::policy(PurchaseRequest::class, PurchaseRequestPolicy::class);

        /**
         * Gate global — SUPERVISOR tem acesso total
         */
        Gate::before(static function (User $user, string $ability): ?bool {
            return $user->role->value === 'SUPERVISOR' ? true : null;
        });

        /**
         * Gates auxiliares (apenas SUPERVISOR)
         */
        Gate::define('manageUsers', static fn (User $user): bool =>
            $user->role->value === 'SUPERVISOR'
        );

        Gate::define('manageShops', static fn (User $user): bool =>
            $user->role->value === 'SUPERVISOR'
        );
    }
}
