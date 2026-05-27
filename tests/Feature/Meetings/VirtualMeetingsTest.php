<?php

namespace Tests\Feature\Meetings;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Enums\MeetingProvider;
use App\Enums\MeetingStatus;
use App\Enums\MeetingType;
use App\Models\AcademicYear;
use App\Models\EducationalLevel;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\MeetingParticipant;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Models\VirtualClassroom;
use App\Models\VirtualMeeting;
use App\Services\VirtualMeetingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class VirtualMeetingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{teacher: User, studentUser: User, student: Student, classroom: VirtualClassroom}
     */
    private function setupScenario(): array
    {
        $year = AcademicYear::factory()->active()->create();
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $subject = Subject::factory()->create();

        $teacher = User::factory()->create();
        $teacher->syncRoles([IntranetRole::Docente->value]);

        TeacherAssignment::factory()->create([
            'user_id' => $teacher->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'is_active' => true,
        ]);

        $studentUser = User::factory()->create();
        $studentUser->syncRoles([IntranetRole::Estudiante->value]);
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $classroom = VirtualClassroom::factory()->create([
            'academic_year_id' => $year->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'teacher_user_id' => $teacher->id,
            'created_by_user_id' => $teacher->id,
        ]);

        return compact('teacher', 'studentUser', 'student', 'classroom');
    }

    public function test_teacher_can_create_meeting_linked_to_classroom(): void
    {
        $scenario = $this->setupScenario();

        $meetLink = 'https://meet.google.com/abc-defg-hij';

        $this->actingAs($scenario['teacher'])
            ->post(route('teacher.meetings.store'), [
                'title' => 'Clase de matemáticas',
                'description' => 'Repaso de fracciones',
                'meeting_type' => MeetingType::VirtualClass->value,
                'provider' => MeetingProvider::GoogleMeet->value,
                'join_url' => $meetLink,
                'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
                'duration_minutes' => 45,
                'virtual_classroom_id' => $scenario['classroom']->id,
                'waiting_room_enabled' => true,
                'recording_allowed' => false,
            ])
            ->assertRedirect();

        $meeting = VirtualMeeting::query()->first();
        $this->assertNotNull($meeting);
        $this->assertSame($meetLink, $meeting->join_url);
        $this->assertSame('teacher_provided', $meeting->provider_metadata['mode'] ?? null);
        $this->assertTrue(
            MeetingParticipant::query()
                ->where('virtual_meeting_id', $meeting->id)
                ->where('user_id', $scenario['studentUser']->id)
                ->exists()
        );

        $this->assertDatabaseHas('audit_logs', [
            'module' => 'meetings',
            'action' => 'create',
        ]);
    }

    public function test_manual_provider_requires_join_url(): void
    {
        $scenario = $this->setupScenario();

        $this->actingAs($scenario['teacher'])
            ->from(route('teacher.meetings.create'))
            ->post(route('teacher.meetings.store'), [
                'title' => 'Sin enlace',
                'meeting_type' => MeetingType::VirtualClass->value,
                'provider' => MeetingProvider::Manual->value,
                'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
                'duration_minutes' => 60,
            ])
            ->assertSessionHasErrors('join_url');
    }

    public function test_google_meet_uses_institutional_fallback_when_join_url_empty(): void
    {
        config(['meetings.google_meet.configured_room_code' => 'institucional-fija']);

        $scenario = $this->setupScenario();

        $this->actingAs($scenario['teacher'])
            ->post(route('teacher.meetings.store'), [
                'title' => 'Clase con fallback',
                'meeting_type' => MeetingType::VirtualClass->value,
                'provider' => MeetingProvider::GoogleMeet->value,
                'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
                'duration_minutes' => 60,
                'virtual_classroom_id' => $scenario['classroom']->id,
            ])
            ->assertRedirect();

        $meeting = VirtualMeeting::query()->first();
        $this->assertNotNull($meeting);
        $this->assertStringContainsString('institucional-fija', $meeting->join_url);
        $this->assertSame('generated_link', $meeting->provider_metadata['mode'] ?? null);
    }

    public function test_student_can_view_meetings_index_when_participant(): void
    {
        $scenario = $this->setupScenario();

        $meeting = app(VirtualMeetingService::class)->create($scenario['teacher'], [
            'title' => 'Sesión demo',
            'meeting_type' => MeetingType::VirtualClass->value,
            'provider' => MeetingProvider::GoogleMeet->value,
            'join_url' => 'https://meet.google.com/demo-link',
            'scheduled_at' => now()->addHours(2)->toDateTimeString(),
            'duration_minutes' => 60,
            'virtual_classroom_id' => $scenario['classroom']->id,
        ]);

        $this->assertTrue(
            MeetingParticipant::query()
                ->where('virtual_meeting_id', $meeting->id)
                ->where('user_id', $scenario['studentUser']->id)
                ->exists()
        );

        $this->actingAs($scenario['studentUser'])
            ->get(route('student.meetings.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Student/Meetings/Index')
                ->has('upcoming', 1));
    }

    public function test_student_cannot_view_meeting_they_are_not_invited_to(): void
    {
        $scenario = $this->setupScenario();
        $other = User::factory()->create();
        $other->syncRoles([IntranetRole::Estudiante->value]);

        $meeting = VirtualMeeting::factory()->create([
            'host_user_id' => $scenario['teacher']->id,
            'created_by_user_id' => $scenario['teacher']->id,
            'scheduled_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHour(),
        ]);

        MeetingParticipant::query()->create([
            'virtual_meeting_id' => $meeting->id,
            'user_id' => $scenario['teacher']->id,
            'role' => 'host',
            'invited_at' => now(),
        ]);

        $this->actingAs($other)
            ->get(route('student.meetings.show', $meeting))
            ->assertForbidden();
    }

    public function test_join_records_attendance_and_redirects(): void
    {
        $scenario = $this->setupScenario();

        $meeting = VirtualMeeting::factory()->create([
            'virtual_classroom_id' => $scenario['classroom']->id,
            'host_user_id' => $scenario['teacher']->id,
            'created_by_user_id' => $scenario['teacher']->id,
            'scheduled_at' => now()->subMinutes(5),
            'ends_at' => now()->addHour(),
            'status' => MeetingStatus::Scheduled,
            'join_url' => 'https://meet.google.com/test-room',
        ]);

        MeetingParticipant::query()->create([
            'virtual_meeting_id' => $meeting->id,
            'user_id' => $scenario['studentUser']->id,
            'role' => 'participant',
            'invited_at' => now(),
        ]);

        $this->actingAs($scenario['studentUser'])
            ->get(route('student.meetings.join', $meeting))
            ->assertRedirect('https://meet.google.com/test-room');

        $this->assertDatabaseHas('meeting_attendances', [
            'virtual_meeting_id' => $meeting->id,
            'user_id' => $scenario['studentUser']->id,
        ]);
    }

    public function test_admin_can_access_institution_meetings_panel(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles([IntranetRole::Administrador->value]);

        VirtualMeeting::factory()->count(2)->create([
            'host_user_id' => $admin->id,
            'created_by_user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('intranet.meetings.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Meetings/Index')
                ->has('metrics'));
    }

    public function test_meeting_creation_notifies_participants(): void
    {
        $scenario = $this->setupScenario();

        app(VirtualMeetingService::class)->create($scenario['teacher'], [
            'title' => 'Clase con notificación',
            'meeting_type' => MeetingType::Tutoring->value,
            'provider' => MeetingProvider::GoogleMeet->value,
            'join_url' => 'https://meet.google.com/notify-test',
            'scheduled_at' => now()->addDay()->toDateTimeString(),
            'duration_minutes' => 30,
            'virtual_classroom_id' => $scenario['classroom']->id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $scenario['studentUser']->id,
        ]);
    }
}
