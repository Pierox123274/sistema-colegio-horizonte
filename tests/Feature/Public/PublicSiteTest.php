<?php

namespace Tests\Feature\Public;

use Database\Seeders\CmsContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsContentSeeder::class);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function publicRouteProvider(): array
    {
        return [
            'home' => ['/', 'Public/Home'],
            'nosotros' => ['/nosotros', 'Public/Nosotros/Index'],
            'nosotros historia' => ['/nosotros/historia', 'Public/Nosotros/Historia'],
            'nosotros mision' => ['/nosotros/mision-vision', 'Public/Nosotros/MisionVision'],
            'nosotros valores' => ['/nosotros/valores', 'Public/Nosotros/Valores'],
            'nosotros infra' => ['/nosotros/infraestructura', 'Public/Nosotros/Infraestructura'],
            'niveles' => ['/niveles', 'Public/Niveles/Index'],
            'nivel inicial' => ['/niveles/inicial', 'Public/Niveles/LevelPage'],
            'nivel primaria' => ['/niveles/primaria', 'Public/Niveles/LevelPage'],
            'nivel secundaria' => ['/niveles/secundaria', 'Public/Niveles/LevelPage'],
            'admision' => ['/admision', 'Public/Admision/Index'],
            'admision requisitos' => ['/admision/requisitos', 'Public/Admision/Requisitos'],
            'admision matricula' => ['/admision/matricula', 'Public/Admision/Matricula'],
            'vida escolar' => ['/vida-escolar', 'Public/VidaEscolar/Index'],
            'vida actividades' => ['/vida-escolar/actividades', 'Public/VidaEscolar/Actividades'],
            'vida talleres' => ['/vida-escolar/talleres', 'Public/VidaEscolar/Talleres'],
            'vida eventos' => ['/vida-escolar/eventos', 'Public/VidaEscolar/Eventos'],
            'galeria' => ['/galeria', 'Public/Galeria'],
            'noticias' => ['/noticias', 'Public/Noticias/Index'],
            'noticia show' => ['/noticias/apertura-proceso-admision-2026', 'Public/Noticias/Show'],
            'contacto' => ['/contacto', 'Public/Contacto'],
        ];
    }

    #[DataProvider('publicRouteProvider')]
    public function test_public_pages_return_ok_and_inertia_component(
        string $path,
        string $component,
    ): void {
        $response = $this->get($path);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page->component($component));
    }

    public function test_unknown_news_slug_returns_404(): void
    {
        $this->get('/noticias/no-existe')->assertNotFound();
    }
}
