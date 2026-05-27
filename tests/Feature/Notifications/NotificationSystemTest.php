<?php

namespace Tests\Feature\Notifications;

use App\Enums\IntranetRole;
use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Enums\PensionStatus;
use App\Jobs\SendFinancialRemindersJob;
use App\Models\Enrollment;
use App\Models\Pension;
use App\Models\Student;
use App\Models\User;
use App\Notifications\InstitutionalCommunicationNotification;
use App\Services\UserNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Administrador->value]);

        return $user;
    }

    public function test_creates_in_app_notification_for_user(): void
    {
        $user = $this->admin();

        app(UserNotificationService::class)->notifyUser(
            user: $user,
            title: 'Prueba notificación',
            message: 'Mensaje de prueba',
            category: NotificationCategory::System,
            priority: NotificationPriority::Medium
        );

        $this->assertDatabaseCount('notifications', 1);
        $this->assertSame(1, $user->fresh()->notifications()->count());
    }

    public function test_user_can_view_and_mark_notifications_as_read(): void
    {
        $user = $this->admin();
        app(UserNotificationService::class)->notifyUser(
            user: $user,
            title: 'Pendiente',
            message: 'Debe leerse',
            category: NotificationCategory::Academic
        );

        $notification = $user->fresh()->notifications()->first();
        $this->assertNotNull($notification);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Notifications/Center')
                ->has('notifications', 1));

        $this->actingAs($user)
            ->patch(route('notifications.read', $notification->id))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_update_notification_preferences(): void
    {
        $user = $this->admin();

        $this->actingAs($user)
            ->put(route('settings.notifications.update'), [
                'in_app_enabled' => true,
                'email_enabled' => false,
                'frequency' => 'daily_digest',
                'category_settings' => [
                    'academic' => true,
                    'financial' => true,
                    'security' => true,
                    'lms' => false,
                    'ai' => true,
                    'gamification' => true,
                    'system' => true,
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('user_notification_preferences', [
            'user_id' => $user->id,
            'email_enabled' => false,
            'frequency' => 'daily_digest',
        ]);
    }

    public function test_notification_service_can_send_email_channel_when_enabled(): void
    {
        Notification::fake();
        $user = $this->admin();

        app(UserNotificationService::class)->notifyUser(
            user: $user,
            title: 'Canal email',
            message: 'Validación de canal',
            category: NotificationCategory::System,
            forceEmail: true
        );

        Notification::assertSentTo($user, InstitutionalCommunicationNotification::class);
    }

    public function test_financial_reminder_job_generates_notifications(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Estudiante->value]);
        $student = Student::factory()->create(['user_id' => $user->id]);
        $enrollment = Enrollment::factory()->create(['student_id' => $student->id]);

        Pension::factory()->create([
            'enrollment_id' => $enrollment->id,
            'status' => PensionStatus::Vencido->value,
            'due_date' => now()->subDay()->toDateString(),
        ]);

        app(SendFinancialRemindersJob::class)->handle(app(UserNotificationService::class));

        $this->assertGreaterThan(0, $user->fresh()->notifications()->count());
    }

    public function test_notification_routes_are_protected_for_guests(): void
    {
        $this->get(route('notifications.index'))->assertRedirect(route('login'));
        $this->get(route('settings.notifications.edit'))->assertRedirect(route('login'));
    }
}
