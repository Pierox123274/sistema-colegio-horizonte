<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    /**
     * @param  array<string, mixed>  $props
     */
    private function page(string $component, array $props = []): Response
    {
        return Inertia::render($component, array_merge($this->authLinks(), $props));
    }

    public function home(): Response
    {
        return $this->page('Public/Home');
    }

    public function nosotros(): Response
    {
        return $this->page('Public/Nosotros/Index');
    }

    public function nosotrosHistoria(): Response
    {
        return $this->page('Public/Nosotros/Historia');
    }

    public function nosotrosMisionVision(): Response
    {
        return $this->page('Public/Nosotros/MisionVision');
    }

    public function nosotrosValores(): Response
    {
        return $this->page('Public/Nosotros/Valores');
    }

    public function nosotrosInfraestructura(): Response
    {
        return $this->page('Public/Nosotros/Infraestructura');
    }

    public function niveles(): Response
    {
        return $this->page('Public/Niveles/Index');
    }

    public function nivelInicial(): Response
    {
        return $this->page('Public/Niveles/LevelPage', ['level' => 'inicial']);
    }

    public function nivelPrimaria(): Response
    {
        return $this->page('Public/Niveles/LevelPage', ['level' => 'primaria']);
    }

    public function nivelSecundaria(): Response
    {
        return $this->page('Public/Niveles/LevelPage', ['level' => 'secundaria']);
    }

    public function admision(): Response
    {
        return $this->page('Public/Admision/Index');
    }

    public function admisionRequisitos(): Response
    {
        return $this->page('Public/Admision/Requisitos');
    }

    public function admisionMatricula(): Response
    {
        return $this->page('Public/Admision/Matricula');
    }

    public function vidaEscolar(): Response
    {
        return $this->page('Public/VidaEscolar/Index');
    }

    public function vidaEscolarActividades(): Response
    {
        return $this->page('Public/VidaEscolar/Actividades');
    }

    public function vidaEscolarTalleres(): Response
    {
        return $this->page('Public/VidaEscolar/Talleres');
    }

    public function vidaEscolarEventos(): Response
    {
        return $this->page('Public/VidaEscolar/Eventos');
    }

    public function galeria(): Response
    {
        return $this->page('Public/Galeria');
    }

    public function noticias(): Response
    {
        return $this->page('Public/Noticias/Index');
    }

    public function noticiaShow(string $slug): Response
    {
        $article = $this->findNewsArticle($slug);

        if ($article === null) {
            throw new NotFoundHttpException;
        }

        return $this->page('Public/Noticias/Show', [
            'article' => $article,
            'related' => $this->relatedNewsArticles($slug),
        ]);
    }

    public function contacto(): Response
    {
        return $this->page('Public/Contacto');
    }

    /**
     * @return array<int, array{slug: string, title: string, excerpt: string, date: string, category: string, image: string}>|null
     */
    private function allNewsArticles(): array
    {
        return [
            [
                'slug' => 'apertura-proceso-admision-2026',
                'title' => 'Apertura del proceso de admisión 2026',
                'excerpt' => 'Conoce fechas, requisitos y agenda tu visita guiada al campus.',
                'date' => '2026-03-01',
                'category' => 'Admisión',
                'image' => $this->newsImage('apertura-proceso-admision-2026'),
                'paragraphs' => [
                    'El I.E.P. Horizonte abre su proceso de admisión para el año escolar 2026. Las familias interesadas pueden agendar una visita guiada, conocer nuestros niveles y recibir orientación personalizada sobre documentación y evaluación diagnóstica.',
                    'Durante marzo y abril realizamos jornadas de puertas abiertas, entrevistas familiares y evaluación formativa. Nuestro equipo de admisiones acompaña cada consulta con transparencia y calidez.',
                ],
            ],
            [
                'slug' => 'feria-ciencias-horizonte',
                'title' => 'Feria de ciencias: innovación desde el aula',
                'excerpt' => 'Proyectos destacados de primaria y secundaria en nuestra feria anual.',
                'date' => '2026-02-15',
                'category' => 'Vida escolar',
                'image' => $this->newsImage('feria-ciencias-horizonte'),
                'paragraphs' => [
                    'Estudiantes de primaria y secundaria presentaron proyectos de ciencia, tecnología y emprendimiento en la feria anual del colegio, con participación de familias y jurados especializados.',
                    'Los stands abarcaron energías renovables, robótica básica y experimentos de laboratorio, demostrando el trabajo interdisciplinario de nuestro equipo docente.',
                ],
            ],
            [
                'slug' => 'reconocimiento-olimpiadas',
                'title' => 'Reconocimiento en olimpiadas regionales',
                'excerpt' => 'Nuestros estudiantes obtuvieron medallas en matemática y comunicación.',
                'date' => '2026-01-20',
                'category' => 'Logros',
                'image' => $this->newsImage('reconocimiento-olimpiadas'),
                'paragraphs' => [
                    'La delegación Horizonte obtuvo medallas de oro y plata en las olimpiadas regionales de matemática y comunicación, consolidando el trabajo académico de nuestro equipo docente.',
                    'Felicitamos a estudiantes, familias y docentes por el esfuerzo sostenido durante el año escolar.',
                ],
            ],
        ];
    }

    private function newsImage(string $slug): string
    {
        $images = [
            'apertura-proceso-admision-2026' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=1200&q=80&auto=format',
            'feria-ciencias-horizonte' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=1200&q=80&auto=format',
            'reconocimiento-olimpiadas' => 'https://images.unsplash.com/photo-1434030214721-40b671f05c15?w=1200&q=80&auto=format',
        ];

        return $images[$slug] ?? $images['apertura-proceso-admision-2026'];
    }

    /**
     * @return array{slug: string, title: string, excerpt: string, date: string, category: string, body: string, image: string, paragraphs: array<int, string>}|null
     */
    private function findNewsArticle(string $slug): ?array
    {
        foreach ($this->allNewsArticles() as $article) {
            if ($article['slug'] === $slug) {
                return [
                    ...$article,
                    'body' => implode("\n\n", $article['paragraphs']),
                ];
            }
        }

        return null;
    }

    /**
     * @return array<int, array{slug: string, title: string, excerpt: string, date: string, category: string, image: string}>
     */
    private function relatedNewsArticles(string $exceptSlug): array
    {
        $related = [];

        foreach ($this->allNewsArticles() as $article) {
            if ($article['slug'] === $exceptSlug) {
                continue;
            }

            $related[] = [
                'slug' => $article['slug'],
                'title' => $article['title'],
                'excerpt' => $article['excerpt'],
                'date' => $article['date'],
                'category' => $article['category'],
                'image' => $article['image'],
            ];

            if (count($related) >= 2) {
                break;
            }
        }

        return $related;
    }
}
