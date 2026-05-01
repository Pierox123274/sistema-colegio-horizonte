<?php

use App\Enums\IntranetRole;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSiteController;
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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
