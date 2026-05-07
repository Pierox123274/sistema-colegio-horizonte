<?php

use App\Enums\IntranetRole;
use App\Http\Controllers\Academic\ClassroomController;
use App\Http\Controllers\Academic\EducationalLevelController;
use App\Http\Controllers\Academic\GradeController;
use App\Http\Controllers\Academic\SectionController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PublicSiteController::class, 'home'])->name('public.home');
Route::get('/nosotros', [PublicSiteController::class, 'nosotros'])->name('public.nosotros');
Route::get('/niveles', [PublicSiteController::class, 'niveles'])->name('public.niveles');
Route::get('/admision', [PublicSiteController::class, 'admision'])->name('public.admision');
Route::get('/noticias', [PublicSiteController::class, 'noticias'])->name('public.noticias');
Route::get('/contacto', [PublicSiteController::class, 'contacto'])->name('public.contacto');

$intranetRoles = 'role:'.IntranetRole::middlewarePipe();

Route::middleware(['auth', 'verified', $intranetRoles])->group(function () {
    Route::get('/intranet/dashboard', function () {
        return Inertia::render('Intranet/Dashboard');
    })->name('dashboard');

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
    });

    Route::middleware(['role:Administrador|Secretaria|Docente'])->prefix('intranet/academic')->group(function () {
        Route::get('/levels/{educational_level}', [EducationalLevelController::class, 'show'])->name('intranet.academic.levels.show');
        Route::get('/grades/{grade}', [GradeController::class, 'show'])->name('intranet.academic.grades.show');
        Route::get('/sections/{section}', [SectionController::class, 'show'])->name('intranet.academic.sections.show');
        Route::get('/classrooms/{classroom}', [ClassroomController::class, 'show'])->name('intranet.academic.classrooms.show');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
