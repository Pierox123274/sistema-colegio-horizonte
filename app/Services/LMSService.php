<?php

namespace App\Services;

use App\Enums\AcademicCalendarEventType;
use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Enums\VirtualResourceType;
use App\Models\AcademicCalendarEvent;
use App\Models\Assignment;
use App\Models\OnlineExam;
use App\Models\User;
use App\Models\VirtualClassroom;
use App\Models\VirtualClassroomAnnouncement;
use App\Models\VirtualResource;

final class LMSService
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly VirtualClassroomAccessService $access,
    ) {}

    public function createClassroom(User $user, array $data): VirtualClassroom
    {
        $classroom = VirtualClassroom::query()->create([
            ...$data,
            'teacher_user_id' => $data['teacher_user_id'] ?? $user->id,
            'created_by_user_id' => $user->id,
            'is_active' => $data['is_active'] ?? true,
        ]);

        $this->audit->log(
            AuditAction::Create,
            AuditModule::Lms,
            $user,
            VirtualClassroom::class,
            $classroom->id,
            'Creación de aula virtual',
            null,
            ['title' => $classroom->title],
            AuditResult::Success,
        );

        return $classroom;
    }

    public function publishAnnouncement(User $user, VirtualClassroom $classroom, array $data): VirtualClassroomAnnouncement
    {
        $announcement = VirtualClassroomAnnouncement::query()->create([
            'virtual_classroom_id' => $classroom->id,
            'user_id' => $user->id,
            'title' => $data['title'],
            'body' => $data['body'],
            'published_at' => now(),
        ]);

        $this->audit->log(
            AuditAction::Create,
            AuditModule::Lms,
            $user,
            VirtualClassroomAnnouncement::class,
            $announcement->id,
            'Publicación en aula virtual',
            null,
            ['title' => $announcement->title],
            AuditResult::Success,
        );

        return $announcement;
    }

    public function addResource(User $user, VirtualClassroom $classroom, array $data): VirtualResource
    {
        $resource = VirtualResource::query()->create([
            'virtual_classroom_id' => $classroom->id,
            'title' => $data['title'],
            'resource_type' => VirtualResourceType::from($data['resource_type']),
            'file_path' => $data['file_path'] ?? null,
            'external_url' => $data['external_url'] ?? null,
            'topic' => $data['topic'] ?? null,
            'competency' => $data['competency'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'created_by_user_id' => $user->id,
        ]);

        $this->audit->log(
            AuditAction::Create,
            AuditModule::Lms,
            $user,
            VirtualResource::class,
            $resource->id,
            'Material educativo publicado',
            null,
            ['title' => $resource->title],
            AuditResult::Success,
        );

        return $resource;
    }

    public function syncCalendarForAssignment(Assignment $assignment): void
    {
        $assignment->loadMissing('virtualClassroom');
        $classroom = $assignment->virtualClassroom;

        AcademicCalendarEvent::query()->updateOrCreate(
            [
                'related_type' => Assignment::class,
                'related_id' => $assignment->id,
            ],
            [
                'academic_year_id' => $classroom->academic_year_id,
                'section_id' => $classroom->section_id,
                'subject_id' => $classroom->subject_id,
                'student_id' => null,
                'event_type' => AcademicCalendarEventType::Assignment,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'starts_at' => $assignment->due_at ?? now(),
                'ends_at' => $assignment->due_at,
            ],
        );
    }

    public function syncCalendarForExam(OnlineExam $exam): void
    {
        $exam->loadMissing('virtualClassroom');
        $classroom = $exam->virtualClassroom;

        AcademicCalendarEvent::query()->updateOrCreate(
            [
                'related_type' => OnlineExam::class,
                'related_id' => $exam->id,
            ],
            [
                'academic_year_id' => $classroom->academic_year_id,
                'section_id' => $classroom->section_id,
                'subject_id' => $classroom->subject_id,
                'student_id' => null,
                'event_type' => AcademicCalendarEventType::Exam,
                'title' => $exam->title,
                'description' => $exam->description,
                'starts_at' => $exam->available_from ?? now(),
                'ends_at' => $exam->available_until,
            ],
        );
    }
}
