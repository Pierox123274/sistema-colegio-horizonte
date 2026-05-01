<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function publicRouteProvider(): array
    {
        return [
            'home' => ['/', 'Public/Home'],
            'nosotros' => ['/nosotros', 'Public/Nosotros'],
            'niveles' => ['/niveles', 'Public/Niveles'],
            'admision' => ['/admision', 'Public/Admision'],
            'noticias' => ['/noticias', 'Public/Noticias'],
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
        $response->assertSee($component, false);
    }
}
