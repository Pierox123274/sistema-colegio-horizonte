<?php

namespace App\Providers;

use App\Policies\AnalyticsPolicy;
use App\Policies\SecurityPolicy;
use App\Policies\SystemOperationsPolicy;
use App\Support\AnalyticsDashboard;
use App\Support\SecurityDashboard;
use App\Support\SystemOperationsDashboard;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(AnalyticsDashboard::class, AnalyticsPolicy::class);
        Gate::policy(SecurityDashboard::class, SecurityPolicy::class);
        Gate::policy(SystemOperationsDashboard::class, SystemOperationsPolicy::class);

        Vite::prefetch(concurrency: 3);
    }
}
