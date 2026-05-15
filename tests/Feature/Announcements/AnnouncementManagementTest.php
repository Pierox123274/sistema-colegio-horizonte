<?php

namespace Tests\Feature\Announcements;

use App\Enums\AnnouncementAudienceType;
use App\Enums\AnnouncementPriority;
use App\Enums\IntranetRole;
use App\Models\Announcement;
use App\Models\AnnouncementRead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AnnouncementManagementTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_admin_can_create_announcement(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $response = $this->actingAs($admin)->post(route('intranet.announcements.store'), [
            'title' => 'Aviso institucional',
            'content' => 'Contenido del comunicado de prueba.',
            'priority' => AnnouncementPriority::Media->value,
            'audience_type' => AnnouncementAudienceType::All->value,
            'starts_at' => now()->subMinute()->format('Y-m-d\TH:i'),
            'ends_at' => now()->addWeek()->format('Y-m-d\TH:i'),
            'is_active' => true,
        ]);

        $response->assertRedirect(route('intranet.announcements.index'));
        $this->assertDatabaseHas('announcements', [
            'title' => 'Aviso institucional',
            'created_by_user_id' => $admin->id,
        ]);
    }

    public function test_teacher_sees_teacher_audience_announcements(): void
    {
        $teacher = $this->userWithRole(IntranetRole::Docente);

        $visible = Announcement::factory()->forTeachers()->create(['title' => 'Para docentes']);
        Announcement::factory()->forStudents()->create(['title' => 'Solo estudiantes']);

        $this->actingAs($teacher)
            ->get(route('teacher.announcements.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Teacher/Announcements/Index')
                ->has('announcements.data', 1)
                ->where('announcements.data.0.title', $visible->title));
    }

    public function test_student_sees_student_audience_announcements(): void
    {
        $student = $this->userWithRole(IntranetRole::Estudiante);

        $visible = Announcement::factory()->forStudents()->create(['title' => 'Para estudiantes']);
        Announcement::factory()->forTeachers()->create(['title' => 'Solo docentes']);

        $this->actingAs($student)
            ->get(route('student.announcements.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Student/Announcements/Index')
                ->has('announcements.data', 1)
                ->where('announcements.data.0.title', $visible->title));
    }

    public function test_teacher_cannot_create_announcements(): void
    {
        $teacher = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($teacher)
            ->get(route('intranet.announcements.create'))
            ->assertForbidden();

        $this->actingAs($teacher)
            ->post(route('intranet.announcements.store'), [
                'title' => 'Intento no autorizado',
                'content' => 'No debe guardarse.',
                'priority' => AnnouncementPriority::Baja->value,
                'audience_type' => AnnouncementAudienceType::All->value,
                'starts_at' => now()->format('Y-m-d\TH:i'),
                'is_active' => true,
            ])
            ->assertForbidden();
    }

    public function test_user_can_mark_announcement_as_read(): void
    {
        $teacher = $this->userWithRole(IntranetRole::Docente);
        $announcement = Announcement::factory()->forTeachers()->create();

        $this->actingAs($teacher)
            ->post(route('teacher.announcements.read', $announcement))
            ->assertRedirect();

        $this->assertDatabaseHas('announcement_reads', [
            'announcement_id' => $announcement->id,
            'user_id' => $teacher->id,
        ]);
    }

    public function test_expired_announcement_is_not_visible_to_recipients(): void
    {
        $teacher = $this->userWithRole(IntranetRole::Docente);
        Announcement::factory()->forTeachers()->expired()->create(['title' => 'Expirado']);

        $this->actingAs($teacher)
            ->get(route('teacher.announcements.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Teacher/Announcements/Index')
                ->has('announcements.data', 0));
    }

    public function test_admin_can_filter_announcements_by_priority(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        Announcement::factory()->create([
            'title' => 'Urgente',
            'priority' => AnnouncementPriority::Urgente,
        ]);
        Announcement::factory()->create([
            'title' => 'Baja prioridad',
            'priority' => AnnouncementPriority::Baja,
        ]);

        $this->actingAs($admin)
            ->get(route('intranet.announcements.index', ['priority' => AnnouncementPriority::Urgente->value]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Announcements/Index')
                ->has('announcements.data', 1)
                ->where('announcements.data.0.title', 'Urgente'));
    }

    public function test_unread_filter_on_teacher_inbox(): void
    {
        $teacher = $this->userWithRole(IntranetRole::Docente);
        $readAnnouncement = Announcement::factory()->forTeachers()->create(['title' => 'Leído']);
        $unreadAnnouncement = Announcement::factory()->forTeachers()->create(['title' => 'Sin leer']);

        AnnouncementRead::factory()->create([
            'announcement_id' => $readAnnouncement->id,
            'user_id' => $teacher->id,
            'read_at' => now(),
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.announcements.index', ['unread_only' => '1']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('announcements.data', 1)
                ->where('announcements.data.0.title', $unreadAnnouncement->title));
    }
}
