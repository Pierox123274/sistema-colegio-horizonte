<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class PublicSiteController extends Controller
{
    /**
     * @return array{canLogin: bool, canRegister: bool}
     */
    private function authLinks(): array
    {
        return [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ];
    }

    public function home(): Response
    {
        return Inertia::render('Public/Home', $this->authLinks());
    }

    public function nosotros(): Response
    {
        return Inertia::render('Public/Nosotros', $this->authLinks());
    }

    public function niveles(): Response
    {
        return Inertia::render('Public/Niveles', $this->authLinks());
    }

    public function admision(): Response
    {
        return Inertia::render('Public/Admision', $this->authLinks());
    }

    public function noticias(): Response
    {
        return Inertia::render('Public/Noticias', $this->authLinks());
    }

    public function contacto(): Response
    {
        return Inertia::render('Public/Contacto', $this->authLinks());
    }
}
