<?php

namespace App\Http\Controllers;

use App\Services\StudentContextService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentProfileController extends Controller
{
    public function __construct(
        private readonly StudentContextService $studentContext
    ) {}

    public function show(Request $request): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $context = $this->studentContext->portalContext($user);
        $student = $context['student'];

        $profile = null;
        $enrollment = null;
        $guardians = [];
        $academicHistory = [];

        if ($student !== null) {
            $profile = [
                ...$this->studentContext->studentCard($student),
                'address' => $student->address,
                'medical_observations' => $student->medical_observations,
                'educational_level' => $student->educational_level->value,
                'grade' => $student->grade,
                'section' => $student->section,
            ];
            $enrollment = $this->studentContext->currentEnrollmentPayload($student);
            $guardians = $this->studentContext->guardiansForProfile($student);
            $academicHistory = $this->studentContext->academicHistoryEnrollments($student);
        }

        return Inertia::render('Student/Profile/Show', [
            'profile' => $profile,
            'enrollment' => $enrollment,
            'guardians' => $guardians,
            'academic_history' => $academicHistory,
            'account' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'has_student' => $context['has_student'],
            'portal_scoped' => $context['portal_scoped'],
            'empty_message' => $context['empty_message'],
            'profile_edit_href' => route('profile.edit', absolute: false),
        ]);
    }
}
