<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\DiagnosticAttempt;
use App\Models\DiagnosticExam;
use App\Models\OnlineExam;
use App\Models\OnlineExamAttempt;
use App\Models\QuestionBank;
use App\Models\VirtualClassroom;
use App\Policies\AdaptiveLearningPolicy;
use App\Policies\AIPolicy;
use App\Policies\AnalyticsPolicy;
use App\Policies\AssignmentPolicy;
use App\Policies\AssignmentSubmissionPolicy;
use App\Policies\DiagnosticAttemptPolicy;
use App\Policies\DiagnosticExamPolicy;
use App\Policies\OnlineExamAttemptPolicy;
use App\Policies\OnlineExamPolicy;
use App\Policies\QuestionBankPolicy;
use App\Policies\SecurityPolicy;
use App\Policies\SystemOperationsPolicy;
use App\Policies\VirtualClassroomPolicy;
use App\Support\AdaptiveLearningDashboard;
use App\Support\AIDashboard;
use App\Support\AnalyticsDashboard;
use App\Support\SecurityDashboard;
use App\Support\SystemOperationsDashboard;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        Gate::policy(AdaptiveLearningDashboard::class, AdaptiveLearningPolicy::class);
        Gate::policy(DiagnosticAttempt::class, DiagnosticAttemptPolicy::class);
        Gate::policy(DiagnosticExam::class, DiagnosticExamPolicy::class);
        Gate::policy(QuestionBank::class, QuestionBankPolicy::class);
        Gate::policy(VirtualClassroom::class, VirtualClassroomPolicy::class);
        Gate::policy(Assignment::class, AssignmentPolicy::class);
        Gate::policy(AssignmentSubmission::class, AssignmentSubmissionPolicy::class);
        Gate::policy(OnlineExam::class, OnlineExamPolicy::class);
        Gate::policy(OnlineExamAttempt::class, OnlineExamAttemptPolicy::class);
        Gate::policy(AIDashboard::class, AIPolicy::class);
        Gate::policy(AnalyticsDashboard::class, AnalyticsPolicy::class);
        Gate::policy(SecurityDashboard::class, SecurityPolicy::class);
        Gate::policy(SystemOperationsDashboard::class, SystemOperationsPolicy::class);

        RateLimiter::for('ai', function (Request $request): Limit {
            $max = max(1, (int) config('ai.rate_limit_per_minute', 20));

            return Limit::perMinute($max)->by((string) ($request->user()?->getAuthIdentifier() ?? $request->ip()));
        });

        Vite::prefetch(concurrency: 3);
    }
}
