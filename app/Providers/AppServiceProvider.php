<?php

namespace App\Providers;

use App\Policies\AnalyticsPolicy;
use App\Policies\SecurityPolicy;
use App\Support\AnalyticsDashboard;
use App\Support\SecurityDashboard;
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

        Vite::prefetch(concurrency: 3);
    }
}
