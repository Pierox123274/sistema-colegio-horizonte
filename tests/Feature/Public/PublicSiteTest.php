<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
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
        $response->assertInertia(fn (Assert $page) => $page->component($component));
    }
}
