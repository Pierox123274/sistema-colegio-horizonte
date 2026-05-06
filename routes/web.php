<?php

use App\Enums\IntranetRole;
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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
