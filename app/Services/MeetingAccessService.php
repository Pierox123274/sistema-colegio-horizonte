<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Enums\MeetingParticipantRole;
use App\Enums\MeetingStatus;
use App\Models\MeetingParticipant;
use App\Models\Student;
use App\Models\User;
use App\Models\VirtualMeeting;
use Illuminate\Database\Eloquent\Builder;

final class MeetingAccessService
{
    public function __construct(
        private readonly VirtualClassroomAccessService $classroomAccess,
    ) {}

    public function isAdministrator(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function userIsParticipant(User $user, VirtualMeeting $meeting): bool
    {
        if ($this->isAdministrator($user)) {
            return true;
        }

        if ($meeting->host_user_id === $user->id || $meeting->created_by_user_id === $user->id) {
            return true;
        }

        return MeetingParticipant::query()
            ->where('virtual_meeting_id', $meeting->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function userMayManage(User $user, VirtualMeeting $meeting): bool
    {
        if ($this->isAdministrator($user)) {
            return true;
        }

        if ($meeting->host_user_id === $user->id || $meeting->created_by_user_id === $user->id) {
            return true;
        }

        if ($user->hasRole(IntranetRole::Docente->value) && $meeting->virtual_classroom_id !== null) {
            $meeting->loadMissing('virtualClassroom');

            return $meeting->virtualClassroom !== null
                && $this->classroomAccess->teacherMayManageClassroom($user, $meeting->virtualClassroom);
        }

        return false;
    }

    /**
     * @return Builder<VirtualMeeting>
     */
    public function queryMeetingsForUser(User $user): Builder
    {
        if ($this->isAdministrator($user)) {
            return VirtualMeeting::query()->orderByDesc('scheduled_at');
        }

        return VirtualMeeting::query()
            ->where(function (Builder $q) use ($user): void {
                $q->where('host_user_id', $user->id)
                    ->orWhere('created_by_user_id', $user->id)
                    ->orWhereHas('participants', fn (Builder $p) => $p->where('user_id', $user->id));
            })
            ->orderByDesc('scheduled_at');
    }

    /**
     * @return Builder<VirtualMeeting>
     */
    public function queryUpcomingForUser(User $user, int $limit = 5): Builder
    {
        return $this->queryMeetingsForUser($user)
            ->whereIn('status', [MeetingStatus::Scheduled->value, MeetingStatus::Live->value])
            ->where('ends_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit($limit);
    }

    /**
     * @return list<int>
     */
    public function resolveParticipantUserIds(VirtualMeeting $meeting, array $explicitUserIds = []): array
    {
        $ids = collect($explicitUserIds)->filter()->map(fn ($id) => (int) $id)->unique()->values();

        $meeting->loadMissing('virtualClassroom');
        $classroom = $meeting->virtualClassroom;

        if ($classroom !== null) {
            $ids->push($classroom->teacher_user_id);

            $studentUserIds = Student::query()
                ->whereHas('enrollments', function (Builder $q) use ($classroom): void {
                    $q->where('academic_year_id', $classroom->academic_year_id)
                        ->where('section_id', $classroom->section_id)
                        ->where('status', EnrollmentStatus::Matriculado->value);
                })
                ->whereNotNull('user_id')
                ->pluck('user_id');

            $ids = $ids->merge($studentUserIds);
        }

        $ids->push($meeting->host_user_id);

        return $ids->unique()->filter()->values()->all();
    }

    public function syncParticipants(VirtualMeeting $meeting, array $userIds): void
    {
        foreach ($userIds as $userId) {
            $role = $userId === $meeting->host_user_id
                ? MeetingParticipantRole::Host
                : MeetingParticipantRole::Participant;

            MeetingParticipant::query()->updateOrCreate(
                [
                    'virtual_meeting_id' => $meeting->id,
                    'user_id' => $userId,
                ],
                [
                    'role' => $role,
                    'invited_at' => now(),
                ],
            );
        }
    }
}
