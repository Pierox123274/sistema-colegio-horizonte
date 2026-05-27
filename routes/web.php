<?php

use App\Enums\IntranetRole;
use App\Http\Controllers\Academic\ClassroomController;
use App\Http\Controllers\Academic\EducationalLevelController;
use App\Http\Controllers\Academic\GradeController;
use App\Http\Controllers\Academic\SectionController;
use App\Http\Controllers\AcademicGradeController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CashMovementController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\Intranet\Cms\CmsDashboardController;
use App\Http\Controllers\Intranet\Cms\CmsGalleryController;
use App\Http\Controllers\Intranet\Cms\CmsHeroSlideController;
use App\Http\Controllers\Intranet\Cms\CmsHomepageController;
use App\Http\Controllers\Intranet\Cms\CmsMediaController;
use App\Http\Controllers\Intranet\Cms\CmsMenuController;
use App\Http\Controllers\Intranet\Cms\CmsNewsCategoryController;
use App\Http\Controllers\Intranet\Cms\CmsNewsController;
use App\Http\Controllers\Intranet\Cms\CmsPageController;
use App\Http\Controllers\Intranet\Cms\CmsSettingController;
use App\Http\Controllers\Intranet\Cms\CmsTestimonialController;
use App\Http\Controllers\Intranet\IntranetVirtualMeetingController;
use App\Http\Controllers\IntranetAdaptiveAnalyticsController;
use App\Http\Controllers\IntranetAdaptiveDiagnosticExamController;
use App\Http\Controllers\IntranetAdaptiveDiagnosticResultController;
use App\Http\Controllers\IntranetAdaptiveQuestionBankController;
use App\Http\Controllers\IntranetAIAnalyticsController;
use App\Http\Controllers\IntranetAnalyticsController;
use App\Http\Controllers\IntranetAnalyticsReportsController;
use App\Http\Controllers\IntranetAnnouncementInboxController;
use App\Http\Controllers\IntranetGamificationController;
use App\Http\Controllers\IntranetLMSOverviewController;
use App\Http\Controllers\IntranetSecurityController;
use App\Http\Controllers\IntranetSystemOperationsController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentConceptController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\PensionController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReceiptController;
use App\Http\Controllers\StudentAIController;
use App\Http\Controllers\StudentAnnouncementController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentCalendarController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentDiagnosticController;
use App\Http\Controllers\StudentGamificationController;
use App\Http\Controllers\StudentGradesController;
use App\Http\Controllers\StudentLearningPathController;
use App\Http\Controllers\StudentPaymentsController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\StudentVirtualClassroomController;
use App\Http\Controllers\StudentVirtualMeetingController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherAcademicRiskController;
use App\Http\Controllers\TeacherAIController;
use App\Http\Controllers\TeacherAnalyticsController;
use App\Http\Controllers\TeacherAnnouncementController;
use App\Http\Controllers\TeacherAssignmentController;
use App\Http\Controllers\TeacherAssignmentsController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\TeacherCalendarController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\TeacherDiagnosticsController;
use App\Http\Controllers\TeacherGradesController;
use App\Http\Controllers\TeacherPedagogicalPanelController;
use App\Http\Controllers\TeacherReportsController;
use App\Http\Controllers\TeacherStudentsController;
use App\Http\Controllers\TeacherVirtualClassroomController;
use App\Http\Controllers\TeacherVirtualMeetingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PublicSiteController::class, 'home'])->name('public.home');
Route::get('/nosotros', [PublicSiteController::class, 'nosotros'])->name('public.nosotros');
Route::get('/nosotros/historia', [PublicSiteController::class, 'nosotrosHistoria'])->name('public.nosotros.historia');
Route::get('/nosotros/mision-vision', [PublicSiteController::class, 'nosotrosMisionVision'])->name('public.nosotros.mision-vision');
Route::get('/nosotros/valores', [PublicSiteController::class, 'nosotrosValores'])->name('public.nosotros.valores');
Route::get('/nosotros/infraestructura', [PublicSiteController::class, 'nosotrosInfraestructura'])->name('public.nosotros.infraestructura');
Route::get('/niveles', [PublicSiteController::class, 'niveles'])->name('public.niveles');
Route::get('/niveles/inicial', [PublicSiteController::class, 'nivelInicial'])->name('public.niveles.inicial');
Route::get('/niveles/primaria', [PublicSiteController::class, 'nivelPrimaria'])->name('public.niveles.primaria');
Route::get('/niveles/secundaria', [PublicSiteController::class, 'nivelSecundaria'])->name('public.niveles.secundaria');
Route::get('/admision', [PublicSiteController::class, 'admision'])->name('public.admision');
Route::get('/admision/requisitos', [PublicSiteController::class, 'admisionRequisitos'])->name('public.admision.requisitos');
Route::get('/admision/matricula', [PublicSiteController::class, 'admisionMatricula'])->name('public.admision.matricula');
Route::get('/vida-escolar', [PublicSiteController::class, 'vidaEscolar'])->name('public.vida-escolar');
Route::get('/vida-escolar/actividades', [PublicSiteController::class, 'vidaEscolarActividades'])->name('public.vida-escolar.actividades');
Route::get('/vida-escolar/talleres', [PublicSiteController::class, 'vidaEscolarTalleres'])->name('public.vida-escolar.talleres');
Route::get('/vida-escolar/eventos', [PublicSiteController::class, 'vidaEscolarEventos'])->name('public.vida-escolar.eventos');
Route::get('/galeria', [PublicSiteController::class, 'galeria'])->name('public.galeria');
Route::get('/noticias', [PublicSiteController::class, 'noticias'])->name('public.noticias');
Route::get('/noticias/{slug}', [PublicSiteController::class, 'noticiaShow'])->name('public.noticias.show');
Route::get('/contacto', [PublicSiteController::class, 'contacto'])->name('public.contacto');

$intranetRoles = 'role:'.IntranetRole::middlewarePipe();

Route::middleware(['auth', 'verified', $intranetRoles])->group(function () use ($intranetRoles) {
    Route::get('/intranet/dashboard', function (Request $request) {
        $user = $request->user();
        if ($user !== null
            && $user->hasRole(IntranetRole::Docente->value)
            && ! $user->hasRole(IntranetRole::Administrador->value)
        ) {
            return redirect()->route('teacher.dashboard');
        }

        if ($user !== null
            && $user->hasRole(IntranetRole::Estudiante->value)
            && ! $user->hasRole(IntranetRole::Administrador->value)
        ) {
            return redirect()->route('student.dashboard');
        }

        return Inertia::render('Intranet/Dashboard');
    })->name('dashboard');

    Route::middleware(['role:Estudiante|Administrador'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::middleware('throttle:ai')->group(function () {
            Route::get('/ai-tutor', [StudentAIController::class, 'tutor'])->name('ai-tutor.index');
            Route::post('/ai-tutor/message', [StudentAIController::class, 'message'])->name('ai-tutor.message');
            Route::get('/recommendations', [StudentAIController::class, 'recommendations'])->name('recommendations.index');
        });
        Route::get('/grades', [StudentGradesController::class, 'index'])->name('grades.index');
        Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/payments', [StudentPaymentsController::class, 'index'])->name('payments.index');
        Route::get('/profile', [StudentProfileController::class, 'show'])->name('profile.show');
        Route::get('/announcements', [StudentAnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/{announcement}', [StudentAnnouncementController::class, 'show'])->name('announcements.show');
        Route::post('/announcements/{announcement}/read', [StudentAnnouncementController::class, 'markRead'])->name('announcements.read');
        Route::get('/diagnostic/attempt/{attempt}', [StudentDiagnosticController::class, 'attempt'])->name('diagnostic.attempt');
        Route::post('/diagnostic/attempt/{attempt}/answer', [StudentDiagnosticController::class, 'answer'])->name('diagnostic.answer');
        Route::post('/diagnostic/{exam}/start', [StudentDiagnosticController::class, 'start'])->name('diagnostic.start');
        Route::get('/diagnostic/{exam}', [StudentDiagnosticController::class, 'show'])->name('diagnostic.show');
        Route::get('/diagnostic', [StudentDiagnosticController::class, 'index'])->name('diagnostic.index');
        Route::get('/learning-path', [StudentLearningPathController::class, 'index'])->name('learning-path.index');
        Route::get('/gamification', [StudentGamificationController::class, 'index'])->name('gamification.index');
        Route::get('/classrooms', [StudentVirtualClassroomController::class, 'index'])->name('classrooms.index');
        Route::get('/classrooms/{classroom}', [StudentVirtualClassroomController::class, 'show'])->name('classrooms.show');
        Route::post('/classrooms/{classroom}/assignments/{assignment}/submit', [StudentVirtualClassroomController::class, 'submitAssignment'])->name('classrooms.assignments.submit');
        Route::post('/classrooms/exams/{exam}/start', [StudentVirtualClassroomController::class, 'startExam'])->name('classrooms.exams.start');
        Route::get('/classrooms/exam-attempt/{attempt}', [StudentVirtualClassroomController::class, 'examAttempt'])->name('classrooms.exam-attempt');
        Route::post('/classrooms/exam-attempt/{attempt}/answer', [StudentVirtualClassroomController::class, 'answerExam'])->name('classrooms.exam-attempt.answer');
        Route::get('/calendar', [StudentCalendarController::class, 'index'])->name('calendar.index');
        Route::get('/meetings', [StudentVirtualMeetingController::class, 'index'])->name('meetings.index');
        Route::get('/meetings/{meeting}', [StudentVirtualMeetingController::class, 'show'])->name('meetings.show');
        Route::get('/meetings/{meeting}/join', [StudentVirtualMeetingController::class, 'join'])->name('meetings.join');
    });

    Route::middleware(['role:Docente|Administrador'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/assignments', [TeacherAssignmentsController::class, 'index'])->name('assignments.index');
        Route::get('/attendance', [TeacherAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/register', [TeacherAttendanceController::class, 'create'])->name('attendance.create');
        Route::get('/attendance/{date}/{section}', [TeacherAttendanceController::class, 'sectionDate'])->whereNumber('section')->name('attendance.section-date');
        Route::post('/attendance', [TeacherAttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/grades', [TeacherGradesController::class, 'index'])->name('grades.index');
        Route::get('/grades/records', [TeacherGradesController::class, 'records'])->name('grades.records');
        Route::post('/grades/records', [TeacherGradesController::class, 'store'])->name('grades.records.store');
        Route::get('/students', [TeacherStudentsController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [TeacherStudentsController::class, 'show'])->name('students.show');
        Route::get('/reports', [TeacherReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/attendance/pdf', [TeacherReportsController::class, 'attendancePdf'])->name('reports.attendance.pdf');
        Route::get('/reports/attendance/excel', [TeacherReportsController::class, 'attendanceExcel'])->name('reports.attendance.excel');
        Route::get('/reports/grades/pdf', [TeacherReportsController::class, 'gradesPdf'])->name('reports.grades.pdf');
        Route::get('/reports/grades/excel', [TeacherReportsController::class, 'gradesExcel'])->name('reports.grades.excel');
        Route::get('/announcements', [TeacherAnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/{announcement}', [TeacherAnnouncementController::class, 'show'])->name('announcements.show');
        Route::post('/announcements/{announcement}/read', [TeacherAnnouncementController::class, 'markRead'])->name('announcements.read');
        Route::get('/pedagogical-panel', [TeacherPedagogicalPanelController::class, 'index'])->name('pedagogical-panel.index');
        Route::get('/diagnostics', [TeacherDiagnosticsController::class, 'index'])->name('diagnostics.index');
        Route::get('/diagnostics/create', [TeacherDiagnosticsController::class, 'create'])->name('diagnostics.create');
        Route::post('/diagnostics', [TeacherDiagnosticsController::class, 'store'])->name('diagnostics.store');
        Route::get('/diagnostics/{exam}', [TeacherDiagnosticsController::class, 'show'])->name('diagnostics.show');
        Route::get('/diagnostics/{exam}/results', [TeacherDiagnosticsController::class, 'results'])->name('diagnostics.results');
        Route::get('/academic-risk', [TeacherAcademicRiskController::class, 'index'])->name('academic-risk.index');
        Route::get('/analytics', [TeacherAnalyticsController::class, 'index'])->name('analytics.index');
        Route::middleware('throttle:ai')->group(function () {
            Route::get('/ai-insights', [TeacherAIController::class, 'insights'])->name('ai-insights.index');
            Route::get('/students-risk', [TeacherAIController::class, 'studentsRisk'])->name('students-risk.index');
        });
        Route::get('/adaptive-learning', fn () => redirect()->route('teacher.pedagogical-panel.index'))->name('adaptive-learning.index');
        Route::get('/diagnostic-results', fn () => redirect()->route('teacher.diagnostics.index'))->name('diagnostic-results.index');
        Route::get('/classrooms', [TeacherVirtualClassroomController::class, 'index'])->name('classrooms.index');
        Route::get('/classrooms/create', [TeacherVirtualClassroomController::class, 'create'])->name('classrooms.create');
        Route::post('/classrooms', [TeacherVirtualClassroomController::class, 'store'])->name('classrooms.store');
        Route::get('/classrooms/{classroom}', [TeacherVirtualClassroomController::class, 'show'])->name('classrooms.show');
        Route::post('/classrooms/{classroom}/assignments', [TeacherVirtualClassroomController::class, 'storeAssignment'])->name('classrooms.assignments.store');
        Route::post('/classrooms/{classroom}/assignments/{assignment}/submissions/{submission}/grade', [TeacherVirtualClassroomController::class, 'gradeSubmission'])->name('classrooms.submissions.grade');
        Route::post('/classrooms/{classroom}/exams', [TeacherVirtualClassroomController::class, 'storeExam'])->name('classrooms.exams.store');
        Route::get('/calendar', [TeacherCalendarController::class, 'index'])->name('calendar.index');
        Route::get('/meetings', [TeacherVirtualMeetingController::class, 'index'])->name('meetings.index');
        Route::get('/meetings/create', [TeacherVirtualMeetingController::class, 'create'])->name('meetings.create');
        Route::post('/meetings', [TeacherVirtualMeetingController::class, 'store'])->name('meetings.store');
        Route::get('/meetings/{meeting}', [TeacherVirtualMeetingController::class, 'show'])->name('meetings.show');
        Route::get('/meetings/{meeting}/join', [TeacherVirtualMeetingController::class, 'join'])->name('meetings.join');
        Route::post('/meetings/{meeting}/start', [TeacherVirtualMeetingController::class, 'start'])->name('meetings.start');
        Route::post('/meetings/{meeting}/cancel', [TeacherVirtualMeetingController::class, 'cancel'])->name('meetings.cancel');
    });

    Route::middleware($intranetRoles)->prefix('intranet/announcements/inbox')->name('intranet.announcements.inbox.')->group(function () {
        Route::get('/', [IntranetAnnouncementInboxController::class, 'index'])->name('index');
        Route::get('/{announcement}', [IntranetAnnouncementInboxController::class, 'show'])->name('show');
        Route::post('/{announcement}/read', [IntranetAnnouncementInboxController::class, 'markRead'])->name('read');
    });

    Route::middleware(['role:Administrador'])->prefix('intranet/announcements')->name('intranet.announcements.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
        Route::post('/', [AnnouncementController::class, 'store'])->name('store');
        Route::get('/{announcement}', [AnnouncementController::class, 'show'])->name('show');
        Route::get('/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('edit');
        Route::put('/{announcement}', [AnnouncementController::class, 'update'])->name('update');
        Route::patch('/{announcement}', [AnnouncementController::class, 'update']);
        Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])->name('destroy');
        Route::post('/{announcement}/deactivate', [AnnouncementController::class, 'deactivate'])->name('deactivate');
        Route::post('/{announcement}/resend', [AnnouncementController::class, 'resend'])->name('resend');
    });

    Route::middleware(['role:Administrador|Secretaria'])->prefix('intranet/analytics')->name('intranet.analytics.')->group(function () {
        Route::get('/', [IntranetAnalyticsController::class, 'index'])->name('index');
    });

    Route::middleware(['role:Administrador|Secretaria'])->prefix('intranet/reports/analytics')->name('intranet.reports.analytics.')->group(function () {
        Route::get('/', [IntranetAnalyticsReportsController::class, 'index'])->name('index');
        Route::get('/{type}/pdf', [IntranetAnalyticsReportsController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/{type}/csv', [IntranetAnalyticsReportsController::class, 'exportCsv'])->name('export.csv');
        Route::get('/{type}', [IntranetAnalyticsReportsController::class, 'show'])->name('show');
    });

    Route::middleware(['role:Administrador|Secretaria|Docente'])->prefix('intranet/security')->name('intranet.security.')->group(function () {
        Route::get('/audit-logs', [IntranetSecurityController::class, 'auditLogs'])->name('audit-logs.index');
        Route::post('/sessions/revoke-others', [IntranetSecurityController::class, 'revokeOtherSessions'])->name('sessions.revoke-others');
    });

    Route::middleware(['role:Administrador|Secretaria'])->prefix('intranet/security')->name('intranet.security.')->group(function () {
        Route::get('/access-monitor', [IntranetSecurityController::class, 'accessMonitor'])->name('access-monitor.index');
    });

    Route::middleware(['role:Administrador'])->prefix('intranet/system')->name('intranet.system.')->group(function () {
        Route::get('/health', [IntranetSystemOperationsController::class, 'health'])->name('health.index');
        Route::get('/jobs', [IntranetSystemOperationsController::class, 'jobs'])->name('jobs.index');
        Route::get('/backups', [IntranetSystemOperationsController::class, 'backups'])->name('backups.index');
        Route::post('/backups', [IntranetSystemOperationsController::class, 'dispatchBackup'])->name('backups.store');
    });

    Route::middleware(['role:Administrador', 'throttle:ai'])->prefix('intranet/ai-analytics')->name('intranet.ai-analytics.')->group(function () {
        Route::get('/', [IntranetAIAnalyticsController::class, 'index'])->name('index');
        Route::post('/refresh', [IntranetAIAnalyticsController::class, 'refresh'])->name('refresh');
    });

    Route::middleware(['role:Administrador'])->prefix('intranet/adaptive-analytics')->name('intranet.adaptive-analytics.')->group(function () {
        Route::get('/', [IntranetAdaptiveAnalyticsController::class, 'index'])->name('index');
    });

    Route::middleware(['role:Administrador'])->prefix('intranet/lms')->name('intranet.lms.')->group(function () {
        Route::get('/', [IntranetLMSOverviewController::class, 'index'])->name('overview');
    });

    Route::middleware(['role:Administrador'])->prefix('intranet/gamification')->name('intranet.gamification.')->group(function () {
        Route::get('/', [IntranetGamificationController::class, 'index'])->name('index');
    });

    Route::middleware(['role:Administrador'])->prefix('intranet/meetings')->name('intranet.meetings.')->group(function () {
        Route::get('/', [IntranetVirtualMeetingController::class, 'index'])->name('index');
        Route::get('/{meeting}', [IntranetVirtualMeetingController::class, 'show'])->name('show');
    });

    Route::middleware(['role:Administrador|Secretaria'])->prefix('intranet/adaptive')->name('intranet.adaptive.')->group(function () {
        Route::get('/diagnostic-exams', [IntranetAdaptiveDiagnosticExamController::class, 'index'])->name('diagnostic-exams.index');
        Route::get('/diagnostic-exams/create', [IntranetAdaptiveDiagnosticExamController::class, 'create'])->name('diagnostic-exams.create');
        Route::post('/diagnostic-exams', [IntranetAdaptiveDiagnosticExamController::class, 'store'])->name('diagnostic-exams.store');
        Route::get('/diagnostic-exams/{diagnostic_exam}', [IntranetAdaptiveDiagnosticExamController::class, 'show'])->name('diagnostic-exams.show');
        Route::get('/diagnostic-exams/{diagnostic_exam}/edit', [IntranetAdaptiveDiagnosticExamController::class, 'edit'])->name('diagnostic-exams.edit');
        Route::put('/diagnostic-exams/{diagnostic_exam}', [IntranetAdaptiveDiagnosticExamController::class, 'update'])->name('diagnostic-exams.update');
        Route::patch('/diagnostic-exams/{diagnostic_exam}', [IntranetAdaptiveDiagnosticExamController::class, 'update']);

        Route::get('/questions', [IntranetAdaptiveQuestionBankController::class, 'index'])->name('questions.index');
        Route::get('/results', [IntranetAdaptiveDiagnosticResultController::class, 'index'])->name('results.index');
    });

    Route::middleware(['role:Administrador'])->prefix('intranet/security')->name('intranet.security.')->group(function () {
        Route::get('/sessions', [IntranetSecurityController::class, 'sessions'])->name('sessions.index');
        Route::get('/login-attempts', [IntranetSecurityController::class, 'loginAttempts'])->name('login-attempts.index');
        Route::post('/sessions/{userSession}/revoke', [IntranetSecurityController::class, 'revokeSession'])->name('sessions.revoke');
    });

    Route::middleware(['role:Administrador|Secretaria|Docente'])->group(function () {
        Route::get('/intranet/students', [StudentController::class, 'index'])->name('intranet.students.index');
    });

    Route::middleware(['role:Administrador|Secretaria'])->group(function () {
        Route::get('/intranet/students/create', [StudentController::class, 'create'])->name('intranet.students.create');
        Route::post('/intranet/students', [StudentController::class, 'store'])->name('intranet.students.store');
        Route::get('/intranet/students/{student}/edit', [StudentController::class, 'edit'])->name('intranet.students.edit');
        Route::put('/intranet/students/{student}', [StudentController::class, 'update'])->name('intranet.students.update');
        Route::patch('/intranet/students/{student}', [StudentController::class, 'update']);
    });

    Route::middleware(['role:Administrador|Secretaria|Docente'])->group(function () {
        Route::get('/intranet/students/{student}', [StudentController::class, 'show'])->name('intranet.students.show');
    });

    Route::middleware(['role:Administrador|Secretaria|Docente'])->group(function () {
        Route::get('/intranet/guardians', [GuardianController::class, 'index'])->name('intranet.guardians.index');
    });

    Route::middleware(['role:Administrador|Secretaria'])->group(function () {
        Route::get('/intranet/guardians/create', [GuardianController::class, 'create'])->name('intranet.guardians.create');
        Route::post('/intranet/guardians', [GuardianController::class, 'store'])->name('intranet.guardians.store');
        Route::get('/intranet/guardians/{guardian}/edit', [GuardianController::class, 'edit'])->name('intranet.guardians.edit');
        Route::put('/intranet/guardians/{guardian}', [GuardianController::class, 'update'])->name('intranet.guardians.update');
        Route::patch('/intranet/guardians/{guardian}', [GuardianController::class, 'update']);
    });

    Route::middleware(['role:Administrador|Secretaria|Docente'])->group(function () {
        Route::get('/intranet/guardians/{guardian}', [GuardianController::class, 'show'])->name('intranet.guardians.show');
    });

    Route::middleware(['role:Administrador|Secretaria|Docente'])->prefix('intranet/academic')->group(function () {
        Route::get('/levels', [EducationalLevelController::class, 'index'])->name('intranet.academic.levels.index');
        Route::get('/grades', [GradeController::class, 'index'])->name('intranet.academic.grades.index');
        Route::get('/sections', [SectionController::class, 'index'])->name('intranet.academic.sections.index');
        Route::get('/classrooms', [ClassroomController::class, 'index'])->name('intranet.academic.classrooms.index');
        Route::get('/subjects', [SubjectController::class, 'index'])->name('intranet.academic.subjects.index');
        Route::get('/evaluations', [EvaluationController::class, 'index'])->name('intranet.academic.evaluations.index');
        Route::get('/grades/records', [AcademicGradeController::class, 'index'])->name('intranet.academic.grades.records.index');
        Route::get('/grades/history', [AcademicGradeController::class, 'historyIndex'])->name('intranet.academic.grades.history.index');
        Route::get('/grades/students/{student}', [AcademicGradeController::class, 'studentHistory'])->whereNumber('student')->name('intranet.academic.grades.students.show');
        Route::get('/grades/reports', [AcademicGradeController::class, 'reportsIndex'])->name('intranet.academic.grades.reports.index');
        Route::get('/grades/reports/pdf', [AcademicGradeController::class, 'exportPdf'])->name('intranet.academic.grades.reports.export.pdf');
        Route::get('/grades/reports/excel', [AcademicGradeController::class, 'exportExcel'])->name('intranet.academic.grades.reports.export.excel');
    });

    Route::middleware(['role:Administrador'])->prefix('intranet/academic')->group(function () {
        Route::get('/levels/create', [EducationalLevelController::class, 'create'])->name('intranet.academic.levels.create');
        Route::post('/levels', [EducationalLevelController::class, 'store'])->name('intranet.academic.levels.store');
        Route::get('/levels/{educational_level}/edit', [EducationalLevelController::class, 'edit'])->name('intranet.academic.levels.edit');
        Route::put('/levels/{educational_level}', [EducationalLevelController::class, 'update'])->name('intranet.academic.levels.update');
        Route::patch('/levels/{educational_level}', [EducationalLevelController::class, 'update']);
        Route::delete('/levels/{educational_level}', [EducationalLevelController::class, 'destroy'])->name('intranet.academic.levels.destroy');

        Route::get('/grades/create', [GradeController::class, 'create'])->name('intranet.academic.grades.create');
        Route::post('/grades', [GradeController::class, 'store'])->name('intranet.academic.grades.store');
        Route::get('/grades/{grade}/edit', [GradeController::class, 'edit'])->name('intranet.academic.grades.edit');
        Route::put('/grades/{grade}', [GradeController::class, 'update'])->name('intranet.academic.grades.update');
        Route::patch('/grades/{grade}', [GradeController::class, 'update']);
        Route::delete('/grades/{grade}', [GradeController::class, 'destroy'])->name('intranet.academic.grades.destroy');

        Route::get('/sections/create', [SectionController::class, 'create'])->name('intranet.academic.sections.create');
        Route::post('/sections', [SectionController::class, 'store'])->name('intranet.academic.sections.store');
        Route::get('/sections/{section}/edit', [SectionController::class, 'edit'])->name('intranet.academic.sections.edit');
        Route::put('/sections/{section}', [SectionController::class, 'update'])->name('intranet.academic.sections.update');
        Route::patch('/sections/{section}', [SectionController::class, 'update']);
        Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('intranet.academic.sections.destroy');

        Route::get('/classrooms/create', [ClassroomController::class, 'create'])->name('intranet.academic.classrooms.create');
        Route::post('/classrooms', [ClassroomController::class, 'store'])->name('intranet.academic.classrooms.store');
        Route::get('/classrooms/{classroom}/edit', [ClassroomController::class, 'edit'])->name('intranet.academic.classrooms.edit');
        Route::put('/classrooms/{classroom}', [ClassroomController::class, 'update'])->name('intranet.academic.classrooms.update');
        Route::patch('/classrooms/{classroom}', [ClassroomController::class, 'update']);
        Route::delete('/classrooms/{classroom}', [ClassroomController::class, 'destroy'])->name('intranet.academic.classrooms.destroy');

        Route::get('/subjects/create', [SubjectController::class, 'create'])->name('intranet.academic.subjects.create');
        Route::post('/subjects', [SubjectController::class, 'store'])->name('intranet.academic.subjects.store');
        Route::get('/subjects/{subject}/edit', [SubjectController::class, 'edit'])->whereNumber('subject')->name('intranet.academic.subjects.edit');
        Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->whereNumber('subject')->name('intranet.academic.subjects.update');
        Route::patch('/subjects/{subject}', [SubjectController::class, 'update'])->whereNumber('subject');

        Route::get('/evaluations/create', [EvaluationController::class, 'create'])->name('intranet.academic.evaluations.create');
        Route::post('/evaluations', [EvaluationController::class, 'store'])->name('intranet.academic.evaluations.store');
        Route::get('/evaluations/{evaluation}/edit', [EvaluationController::class, 'edit'])->whereNumber('evaluation')->name('intranet.academic.evaluations.edit');
        Route::put('/evaluations/{evaluation}', [EvaluationController::class, 'update'])->whereNumber('evaluation')->name('intranet.academic.evaluations.update');
        Route::patch('/evaluations/{evaluation}', [EvaluationController::class, 'update'])->whereNumber('evaluation');
    });

    Route::middleware(['role:Administrador|Secretaria|Docente'])->prefix('intranet/academic')->group(function () {
        Route::get('/levels/{educational_level}', [EducationalLevelController::class, 'show'])->name('intranet.academic.levels.show');
        Route::get('/grades/{grade}', [GradeController::class, 'show'])->name('intranet.academic.grades.show');
        Route::get('/sections/{section}', [SectionController::class, 'show'])->name('intranet.academic.sections.show');
        Route::get('/classrooms/{classroom}', [ClassroomController::class, 'show'])->name('intranet.academic.classrooms.show');
        Route::get('/subjects/{subject}', [SubjectController::class, 'show'])->whereNumber('subject')->name('intranet.academic.subjects.show');
        Route::get('/evaluations/{evaluation}', [EvaluationController::class, 'show'])->whereNumber('evaluation')->name('intranet.academic.evaluations.show');
    });

    Route::middleware(['role:Administrador|Secretaria'])->group(function () {
        Route::get('/intranet/academic-years/create', [AcademicYearController::class, 'create'])->name('intranet.academic-years.create');
        Route::post('/intranet/academic-years', [AcademicYearController::class, 'store'])->name('intranet.academic-years.store');
        Route::get('/intranet/academic-years/{academic_year}/edit', [AcademicYearController::class, 'edit'])->name('intranet.academic-years.edit');
        Route::put('/intranet/academic-years/{academic_year}', [AcademicYearController::class, 'update'])->name('intranet.academic-years.update');
        Route::patch('/intranet/academic-years/{academic_year}', [AcademicYearController::class, 'update']);

        Route::get('/intranet/enrollments/students/search', [EnrollmentController::class, 'searchStudents'])->name('intranet.enrollments.students.search');
        Route::get('/intranet/enrollments/students/{student}/preview', [EnrollmentController::class, 'studentPreview'])->name('intranet.enrollments.students.preview');

        Route::get('/intranet/enrollments/create', [EnrollmentController::class, 'create'])->name('intranet.enrollments.create');
        Route::post('/intranet/enrollments', [EnrollmentController::class, 'store'])->name('intranet.enrollments.store');
        Route::get('/intranet/enrollments/{enrollment}/edit', [EnrollmentController::class, 'edit'])->name('intranet.enrollments.edit');
        Route::put('/intranet/enrollments/{enrollment}', [EnrollmentController::class, 'update'])->name('intranet.enrollments.update');
        Route::patch('/intranet/enrollments/{enrollment}', [EnrollmentController::class, 'update']);
    });

    Route::middleware(['role:Administrador|Secretaria'])->group(function () {
        Route::get('/intranet/academic-years', [AcademicYearController::class, 'index'])->name('intranet.academic-years.index');
    });

    Route::middleware(['role:Administrador|Secretaria|Docente'])->group(function () {
        Route::get('/intranet/enrollments', [EnrollmentController::class, 'index'])->name('intranet.enrollments.index');
        Route::get('/intranet/enrollments/{enrollment}', [EnrollmentController::class, 'show'])->name('intranet.enrollments.show');
        Route::get('/intranet/attendance', [AttendanceController::class, 'index'])->name('intranet.attendance.index');
        Route::get('/intranet/attendance/reports', [AttendanceController::class, 'reports'])->name('intranet.attendance.reports.index');
        Route::get('/intranet/attendance/students/{student}', [AttendanceController::class, 'studentHistory'])->whereNumber('student')->name('intranet.attendance.students.show');
        Route::get('/intranet/attendance/reports/export/pdf', [AttendanceController::class, 'exportPdf'])->name('intranet.attendance.reports.export.pdf');
        Route::get('/intranet/attendance/reports/export/excel', [AttendanceController::class, 'exportExcel'])->name('intranet.attendance.reports.export.excel');
    });

    Route::middleware(['role:Administrador|Secretaria'])->group(function () {
        Route::get('/intranet/payment-concepts/create', [PaymentConceptController::class, 'create'])->name('intranet.payment-concepts.create');
        Route::post('/intranet/payment-concepts', [PaymentConceptController::class, 'store'])->name('intranet.payment-concepts.store');
        Route::get('/intranet/payment-concepts/{payment_concept}/edit', [PaymentConceptController::class, 'edit'])->name('intranet.payment-concepts.edit');
        Route::put('/intranet/payment-concepts/{payment_concept}', [PaymentConceptController::class, 'update'])->name('intranet.payment-concepts.update');
        Route::patch('/intranet/payment-concepts/{payment_concept}', [PaymentConceptController::class, 'update']);
        Route::delete('/intranet/payment-concepts/{payment_concept}', [PaymentConceptController::class, 'destroy'])->name('intranet.payment-concepts.destroy');

        Route::get('/intranet/pensions/create', [PensionController::class, 'create'])->name('intranet.pensions.create');
        Route::post('/intranet/pensions', [PensionController::class, 'store'])->name('intranet.pensions.store');
        Route::get('/intranet/pensions/{pension}/edit', [PensionController::class, 'edit'])->name('intranet.pensions.edit');
        Route::put('/intranet/pensions/{pension}', [PensionController::class, 'update'])->name('intranet.pensions.update');
        Route::patch('/intranet/pensions/{pension}', [PensionController::class, 'update']);

        Route::get('/intranet/payments/students/search', [PaymentController::class, 'searchStudents'])->name('intranet.payments.students.search');
        Route::get('/intranet/payments/students/{student}/summary', [PaymentController::class, 'studentSummary'])->name('intranet.payments.students.summary');

        Route::get('/intranet/payments/create', [PaymentController::class, 'create'])->name('intranet.payments.create');
        Route::post('/intranet/payments', [PaymentController::class, 'store'])->name('intranet.payments.store');
        Route::post('/intranet/payments/{payment}/cancel', [PaymentController::class, 'cancel'])->name('intranet.payments.cancel');
    });

    Route::middleware(['role:Administrador|Secretaria'])->group(function () {
        Route::get('/intranet/payment-concepts', [PaymentConceptController::class, 'index'])->name('intranet.payment-concepts.index');
        Route::get('/intranet/payment-concepts/{payment_concept}', [PaymentConceptController::class, 'show'])->name('intranet.payment-concepts.show');

        Route::get('/intranet/pensions', [PensionController::class, 'index'])->name('intranet.pensions.index');
        Route::get('/intranet/pensions/{pension}', [PensionController::class, 'show'])->name('intranet.pensions.show');

        Route::get('/intranet/payments', [PaymentController::class, 'index'])->name('intranet.payments.index');
        Route::get('/intranet/payments/{payment}', [PaymentController::class, 'show'])->name('intranet.payments.show');
        Route::get('/intranet/payments/{payment}/receipt', [PaymentReceiptController::class, 'show'])->name('intranet.payments.receipt');
        Route::get('/intranet/payments/{payment}/receipt/pdf', [PaymentReceiptController::class, 'pdf'])->name('intranet.payments.receipt.pdf');
        Route::get('/intranet/payments/{payment}/receipt/ticket', [PaymentReceiptController::class, 'ticket'])->name('intranet.payments.receipt.ticket');

        Route::get('/intranet/inventory/categories', [ProductCategoryController::class, 'index'])->name('intranet.inventory.categories.index');
        Route::get('/intranet/inventory/categories/{product_category}', [ProductCategoryController::class, 'show'])
            ->whereNumber('product_category')
            ->name('intranet.inventory.categories.show');

        Route::get('/intranet/inventory/products', [ProductController::class, 'index'])->name('intranet.inventory.products.index');
        Route::get('/intranet/inventory/products/{product}', [ProductController::class, 'show'])
            ->whereNumber('product')
            ->name('intranet.inventory.products.show');

        Route::get('/intranet/inventory/movements', [InventoryMovementController::class, 'index'])->name('intranet.inventory.movements.index');
        Route::get('/intranet/inventory/movements/{inventory_movement}', [InventoryMovementController::class, 'show'])
            ->whereNumber('inventory_movement')
            ->name('intranet.inventory.movements.show');

        Route::get('/intranet/sales/cash-registers', [CashRegisterController::class, 'index'])->name('intranet.sales.cash-registers.index');
        Route::get('/intranet/sales', [SaleController::class, 'index'])->name('intranet.sales.sales.index');
        Route::get('/intranet/sales/{sale}', [SaleController::class, 'show'])->whereNumber('sale')->name('intranet.sales.sales.show');
        Route::get('/intranet/sales/{sale}/receipt', [SaleReceiptController::class, 'show'])->whereNumber('sale')->name('intranet.sales.sales.receipt');
        Route::get('/intranet/sales/{sale}/receipt/pdf', [SaleReceiptController::class, 'pdf'])->whereNumber('sale')->name('intranet.sales.sales.receipt.pdf');
        Route::get('/intranet/sales/{sale}/receipt/ticket', [SaleReceiptController::class, 'ticket'])->whereNumber('sale')->name('intranet.sales.sales.receipt.ticket');
        Route::get('/intranet/sales/cash-movements', [CashMovementController::class, 'index'])->name('intranet.sales.cash-movements.index');
    });

    Route::middleware(['role:Administrador|Secretaria'])->group(function () {
        Route::post('/intranet/sales/cash-registers/open', [CashRegisterController::class, 'open'])->name('intranet.sales.cash-registers.open');
        Route::post('/intranet/sales/cash-registers/{cash_register}/close', [CashRegisterController::class, 'close'])->whereNumber('cash_register')->name('intranet.sales.cash-registers.close');
        Route::get('/intranet/sales/students/search', [SaleController::class, 'searchStudents'])->name('intranet.sales.students.search');
        Route::get('/intranet/sales/students/{student}/preview', [SaleController::class, 'studentPreview'])->whereNumber('student')->name('intranet.sales.students.preview');
        Route::get('/intranet/sales/create', [SaleController::class, 'create'])->name('intranet.sales.sales.create');
        Route::post('/intranet/sales', [SaleController::class, 'store'])->name('intranet.sales.sales.store');
        Route::post('/intranet/sales/{sale}/cancel', [SaleController::class, 'cancel'])->whereNumber('sale')->name('intranet.sales.sales.cancel');
        Route::get('/intranet/sales/reports/export/pdf', [SaleController::class, 'exportPdf'])->name('intranet.sales.reports.export.pdf');
        Route::get('/intranet/sales/reports/export/excel', [SaleController::class, 'exportExcel'])->name('intranet.sales.reports.export.excel');
    });

    Route::middleware(['role:Administrador|Docente'])->group(function () {
        Route::get('/intranet/attendance/create', [AttendanceController::class, 'create'])->name('intranet.attendance.create');
        Route::get('/intranet/attendance/{date}/{section}', [AttendanceController::class, 'sectionDate'])->whereNumber('section')->name('intranet.attendance.section-date');
        Route::post('/intranet/attendance', [AttendanceController::class, 'store'])->name('intranet.attendance.store');
        Route::post('/intranet/academic/grades/records', [AcademicGradeController::class, 'store'])->name('intranet.academic.grades.records.store');
    });

    Route::middleware(['role:Administrador'])->group(function () {
        Route::get('/intranet/inventory/categories/create', [ProductCategoryController::class, 'create'])->name('intranet.inventory.categories.create');
        Route::post('/intranet/inventory/categories', [ProductCategoryController::class, 'store'])->name('intranet.inventory.categories.store');
        Route::get('/intranet/inventory/categories/{product_category}/edit', [ProductCategoryController::class, 'edit'])
            ->whereNumber('product_category')
            ->name('intranet.inventory.categories.edit');
        Route::put('/intranet/inventory/categories/{product_category}', [ProductCategoryController::class, 'update'])
            ->whereNumber('product_category')
            ->name('intranet.inventory.categories.update');
        Route::patch('/intranet/inventory/categories/{product_category}', [ProductCategoryController::class, 'update'])
            ->whereNumber('product_category');
        Route::post('/intranet/inventory/categories/{product_category}/deactivate', [ProductCategoryController::class, 'deactivate'])
            ->whereNumber('product_category')
            ->name('intranet.inventory.categories.deactivate');

        Route::get('/intranet/inventory/products/create', [ProductController::class, 'create'])->name('intranet.inventory.products.create');
        Route::post('/intranet/inventory/products', [ProductController::class, 'store'])->name('intranet.inventory.products.store');
        Route::get('/intranet/inventory/products/{product}/edit', [ProductController::class, 'edit'])
            ->whereNumber('product')
            ->name('intranet.inventory.products.edit');
        Route::put('/intranet/inventory/products/{product}', [ProductController::class, 'update'])
            ->whereNumber('product')
            ->name('intranet.inventory.products.update');
        Route::patch('/intranet/inventory/products/{product}', [ProductController::class, 'update'])
            ->whereNumber('product');
        Route::post('/intranet/inventory/products/{product}/deactivate', [ProductController::class, 'deactivate'])
            ->whereNumber('product')
            ->name('intranet.inventory.products.deactivate');

        Route::get('/intranet/inventory/movements/create', [InventoryMovementController::class, 'create'])->name('intranet.inventory.movements.create');
        Route::post('/intranet/inventory/movements', [InventoryMovementController::class, 'store'])->name('intranet.inventory.movements.store');
        Route::post('/intranet/inventory/movements/{inventory_movement}/cancel', [InventoryMovementController::class, 'cancel'])
            ->whereNumber('inventory_movement')
            ->name('intranet.inventory.movements.cancel');
    });

    Route::middleware(['role:Administrador'])->prefix('intranet/admin')->name('intranet.admin.')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::match(['put', 'patch'], '/users/{user}', [AdminUserController::class, 'update'])->name('users.update');

        Route::get('/teacher-assignments', [TeacherAssignmentController::class, 'index'])->name('teacher-assignments.index');
        Route::get('/teacher-assignments/create', [TeacherAssignmentController::class, 'create'])->name('teacher-assignments.create');
        Route::post('/teacher-assignments', [TeacherAssignmentController::class, 'store'])->name('teacher-assignments.store');
        Route::get('/teacher-assignments/{assignment}/edit', [TeacherAssignmentController::class, 'edit'])->name('teacher-assignments.edit');
        Route::match(['put', 'patch'], '/teacher-assignments/{assignment}', [TeacherAssignmentController::class, 'update'])->name('teacher-assignments.update');
    });

    Route::middleware(['role:Administrador|Secretaria'])->prefix('intranet/cms')->name('intranet.cms.')->group(function () {
        Route::get('/', [CmsDashboardController::class, 'index'])->name('dashboard');

        Route::resource('pages', CmsPageController::class)->except(['show']);
        Route::resource('news', CmsNewsController::class)->except(['show']);
        Route::get('news-categories', [CmsNewsCategoryController::class, 'index'])->name('news-categories.index');
        Route::post('news-categories', [CmsNewsCategoryController::class, 'store'])->name('news-categories.store');
        Route::put('news-categories/{category}', [CmsNewsCategoryController::class, 'update'])->name('news-categories.update');
        Route::delete('news-categories/{category}', [CmsNewsCategoryController::class, 'destroy'])->name('news-categories.destroy');

        Route::resource('galleries', CmsGalleryController::class)->except(['show']);
        Route::post('galleries/{gallery}/images', [CmsGalleryController::class, 'uploadImages'])->name('galleries.images.store');
        Route::patch('galleries/{gallery}/images/{image}', [CmsGalleryController::class, 'updateImage'])->name('galleries.images.update');
        Route::delete('galleries/{gallery}/images/{image}', [CmsGalleryController::class, 'destroyImage'])->name('galleries.images.destroy');
        Route::post('galleries/{gallery}/images/reorder', [CmsGalleryController::class, 'reorderImages'])->name('galleries.images.reorder');

        Route::resource('testimonials', CmsTestimonialController::class)->except(['show']);

        Route::get('media', [CmsMediaController::class, 'index'])->name('media.index');
        Route::get('media/browse', [CmsMediaController::class, 'browse'])->name('media.browse');
        Route::post('media', [CmsMediaController::class, 'store'])->name('media.store');

        Route::middleware(['role:Administrador'])->group(function () {
            Route::get('settings', [CmsSettingController::class, 'index'])->name('settings.index');
            Route::put('settings', [CmsSettingController::class, 'update'])->name('settings.update');

            Route::get('menus', [CmsMenuController::class, 'index'])->name('menus.index');
            Route::get('menus/{location}/edit', [CmsMenuController::class, 'edit'])->name('menus.edit');
            Route::put('menus/{menu}', [CmsMenuController::class, 'update'])->name('menus.update');

            Route::resource('hero-slides', CmsHeroSlideController::class)->except(['show'])->parameters([
                'hero-slides' => 'hero_slide',
            ]);

            Route::get('homepage/edit', [CmsHomepageController::class, 'edit'])->name('homepage.edit');
            Route::put('homepage', [CmsHomepageController::class, 'update'])->name('homepage.update');

            Route::delete('media/{cms_medium}', [CmsMediaController::class, 'destroy'])->name('media.destroy');
        });
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('/{notification}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
    });

    Route::prefix('settings/notifications')->name('settings.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'settings'])->name('edit');
        Route::put('/', [NotificationController::class, 'updateSettings'])->name('update');
    });
});

require __DIR__.'/auth.php';
