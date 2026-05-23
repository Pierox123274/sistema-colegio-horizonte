<?php

namespace App\Http\Controllers;

use App\Support\CmsPublicPresenter;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PublicSiteController extends Controller
{
    public function __construct(
        private readonly CmsPublicPresenter $cms,
    ) {}

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

    /**
     * @param  array<string, mixed>  $props
     */
    private function page(string $component, array $props = []): Response
    {
        return Inertia::render($component, array_merge($this->authLinks(), $props));
    }

    public function home(): Response
    {
        return $this->page('Public/Home', $this->cms->homeProps());
    }

    public function nosotros(): Response
    {
        return $this->page('Public/Nosotros/Index', $this->cms->pageProps(
            'nosotros',
            'Nosotros — I.E.P. Horizonte',
            'Conoce nuestra institución educativa.',
        ));
    }

    public function nosotrosHistoria(): Response
    {
        return $this->page('Public/Nosotros/Historia', $this->cms->pageProps(
            'nosotros-historia',
            'Historia — I.E.P. Horizonte',
            'Tradición y legado institucional.',
        ));
    }

    public function nosotrosMisionVision(): Response
    {
        return $this->page('Public/Nosotros/MisionVision', $this->cms->pageProps(
            'nosotros-mision-vision',
            'Misión y visión — I.E.P. Horizonte',
            'Propósito y horizonte institucional.',
        ));
    }

    public function nosotrosValores(): Response
    {
        return $this->page('Public/Nosotros/Valores', $this->cms->pageProps(
            'nosotros-valores',
            'Valores — I.E.P. Horizonte',
            'Principios que guían nuestra comunidad.',
        ));
    }

    public function nosotrosInfraestructura(): Response
    {
        return $this->page('Public/Nosotros/Infraestructura', $this->cms->pageProps(
            'nosotros-infraestructura',
            'Infraestructura — I.E.P. Horizonte',
            'Campus y espacios de aprendizaje.',
        ));
    }

    public function niveles(): Response
    {
        return $this->page('Public/Niveles/Index', $this->cms->pageProps(
            'niveles',
            'Niveles educativos — I.E.P. Horizonte',
            'Inicial, Primaria y Secundaria.',
        ));
    }

    public function nivelInicial(): Response
    {
        return $this->page('Public/Niveles/LevelPage', array_merge(
            ['level' => 'inicial'],
            $this->cms->pageProps('niveles-inicial', 'Nivel Inicial — I.E.P. Horizonte', ''),
        ));
    }

    public function nivelPrimaria(): Response
    {
        return $this->page('Public/Niveles/LevelPage', array_merge(
            ['level' => 'primaria'],
            $this->cms->pageProps('niveles-primaria', 'Nivel Primaria — I.E.P. Horizonte', ''),
        ));
    }

    public function nivelSecundaria(): Response
    {
        return $this->page('Public/Niveles/LevelPage', array_merge(
            ['level' => 'secundaria'],
            $this->cms->pageProps('niveles-secundaria', 'Nivel Secundaria — I.E.P. Horizonte', ''),
        ));
    }

    public function admision(): Response
    {
        return $this->page('Public/Admision/Index', $this->cms->pageProps(
            'admision',
            'Admisión 2026 — I.E.P. Horizonte',
            'Proceso de ingreso.',
        ));
    }

    public function admisionRequisitos(): Response
    {
        return $this->page('Public/Admision/Requisitos', $this->cms->pageProps(
            'admision-requisitos',
            'Requisitos de admisión — I.E.P. Horizonte',
            '',
        ));
    }

    public function admisionMatricula(): Response
    {
        return $this->page('Public/Admision/Matricula', $this->cms->pageProps(
            'admision-matricula',
            'Matrícula 2026 — I.E.P. Horizonte',
            '',
        ));
    }

    public function vidaEscolar(): Response
    {
        return $this->page('Public/VidaEscolar/Index', $this->cms->pageProps(
            'vida-escolar',
            'Vida escolar — I.E.P. Horizonte',
            'Actividades y convivencia.',
        ));
    }

    public function vidaEscolarActividades(): Response
    {
        return $this->page('Public/VidaEscolar/Actividades', $this->cms->pageProps(
            'vida-escolar-actividades',
            'Actividades — Vida escolar',
            '',
        ));
    }

    public function vidaEscolarTalleres(): Response
    {
        return $this->page('Public/VidaEscolar/Talleres', $this->cms->pageProps(
            'vida-escolar-talleres',
            'Talleres — Vida escolar',
            '',
        ));
    }

    public function vidaEscolarEventos(): Response
    {
        return $this->page('Public/VidaEscolar/Eventos', $this->cms->pageProps(
            'vida-escolar-eventos',
            'Eventos — Vida escolar',
            '',
        ));
    }

    public function galeria(): Response
    {
        return $this->page('Public/Galeria', $this->cms->galleryProps());
    }

    public function noticias(): Response
    {
        return $this->page('Public/Noticias/Index', $this->cms->newsIndexProps());
    }

    public function noticiaShow(string $slug): Response
    {
        $props = $this->cms->newsShowProps($slug);

        if ($props === null) {
            throw new NotFoundHttpException;
        }

        return $this->page('Public/Noticias/Show', $props);
    }

    public function contacto(): Response
    {
        return $this->page('Public/Contacto', $this->cms->pageProps(
            'contacto',
            'Contacto — I.E.P. Horizonte',
            'Escríbenos o agenda una visita.',
        ));
    }
}
