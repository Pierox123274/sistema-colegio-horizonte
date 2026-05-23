<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Cms\CmsGallery;
use App\Models\Cms\CmsHeroSlide;
use App\Models\Cms\CmsMedia;
use App\Models\Cms\CmsMenu;
use App\Models\Cms\CmsNews;
use App\Models\Cms\CmsNewsCategory;
use App\Models\Cms\CmsPage;
use App\Models\Cms\CmsSection;
use App\Models\Cms\CmsSetting;
use App\Models\Cms\CmsTestimonial;
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
use App\Policies\Cms\CmsGalleryPolicy;
use App\Policies\Cms\CmsHeroSlidePolicy;
use App\Policies\Cms\CmsMediaPolicy;
use App\Policies\Cms\CmsMenuPolicy;
use App\Policies\Cms\CmsNewsCategoryPolicy;
use App\Policies\Cms\CmsNewsPolicy;
use App\Policies\Cms\CmsPagePolicy;
use App\Policies\Cms\CmsSectionPolicy;
use App\Policies\Cms\CmsSettingPolicy;
use App\Policies\Cms\CmsTestimonialPolicy;
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
        Gate::policy(CmsPage::class, CmsPagePolicy::class);
        Gate::policy(CmsNews::class, CmsNewsPolicy::class);
        Gate::policy(CmsNewsCategory::class, CmsNewsCategoryPolicy::class);
        Gate::policy(CmsGallery::class, CmsGalleryPolicy::class);
        Gate::policy(CmsTestimonial::class, CmsTestimonialPolicy::class);
        Gate::policy(CmsHeroSlide::class, CmsHeroSlidePolicy::class);
        Gate::policy(CmsSetting::class, CmsSettingPolicy::class);
        Gate::policy(CmsMenu::class, CmsMenuPolicy::class);
        Gate::policy(CmsMedia::class, CmsMediaPolicy::class);
        Gate::policy(CmsSection::class, CmsSectionPolicy::class);

        RateLimiter::for('ai', function (Request $request): Limit {
            $max = max(1, (int) config('ai.rate_limit_per_minute', 20));

            return Limit::perMinute($max)->by((string) ($request->user()?->getAuthIdentifier() ?? $request->ip()));
        });

        Vite::prefetch(concurrency: 3);
    }
}
