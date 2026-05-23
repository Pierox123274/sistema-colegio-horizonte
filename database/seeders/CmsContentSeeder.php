<?php

namespace Database\Seeders;

use App\Enums\CmsMenuLocation;
use App\Enums\CmsPublicationStatus;
use App\Models\Cms\CmsGallery;
use App\Models\Cms\CmsGalleryImage;
use App\Models\Cms\CmsHeroSlide;
use App\Models\Cms\CmsMenu;
use App\Models\Cms\CmsMenuItem;
use App\Models\Cms\CmsNews;
use App\Models\Cms\CmsNewsCategory;
use App\Models\Cms\CmsPage;
use App\Models\Cms\CmsSection;
use App\Models\Cms\CmsSetting;
use App\Models\Cms\CmsTestimonial;
use Illuminate\Database\Seeder;

class CmsContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedPages();
        $this->seedHomeSections();
        $this->seedNewsCategories();
        $this->seedNews();
        $this->seedTestimonials();
        $this->seedGallery();
        $this->seedHeroSlide();
        $this->seedMenus();
    }

    private function seedSettings(): void
    {
        $settings = [
            'school_name' => ['value' => 'I.E.P. Horizonte'],
            'school_tagline' => [
                'value' => 'Excelencia académica y formación integral en Inicial, Primaria y Secundaria.',
            ],
            'contact' => [
                'phone' => '+51 1 234 5678',
                'email' => 'contacto@horizonte.edu.pe',
                'address' => 'Av. Educación 123, Lima, Perú',
                'hours' => 'Lun–Vie 7:30–16:30',
            ],
            'social' => [
                'facebook' => 'https://facebook.com/iep-horizonte',
                'instagram' => 'https://instagram.com/iep_horizonte',
                'youtube' => 'https://youtube.com/@iep-horizonte',
            ],
        ];

        foreach ($settings as $key => $value) {
            CmsSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    private function seedPages(): void
    {
        $pages = [
            ['slug' => 'home', 'title' => 'Inicio', 'template' => 'home', 'sort_order' => 0],
            ['slug' => 'nosotros', 'title' => 'Nosotros', 'subtitle' => 'Presentación institucional'],
            ['slug' => 'nosotros-historia', 'title' => 'Historia', 'subtitle' => 'Tradición y legado'],
            ['slug' => 'nosotros-mision-vision', 'title' => 'Misión y visión', 'subtitle' => 'Propósito institucional'],
            ['slug' => 'nosotros-valores', 'title' => 'Valores', 'subtitle' => 'Principios de convivencia'],
            ['slug' => 'nosotros-infraestructura', 'title' => 'Infraestructura', 'subtitle' => 'Campus y espacios'],
            ['slug' => 'niveles', 'title' => 'Niveles educativos', 'subtitle' => 'Inicial, Primaria y Secundaria'],
            ['slug' => 'niveles-inicial', 'title' => 'Nivel Inicial', 'template' => 'level'],
            ['slug' => 'niveles-primaria', 'title' => 'Nivel Primaria', 'template' => 'level'],
            ['slug' => 'niveles-secundaria', 'title' => 'Nivel Secundaria', 'template' => 'level'],
            ['slug' => 'admision', 'title' => 'Admisión', 'subtitle' => 'Proceso 2026'],
            ['slug' => 'admision-requisitos', 'title' => 'Requisitos de admisión'],
            ['slug' => 'admision-matricula', 'title' => 'Matrícula 2026'],
            ['slug' => 'vida-escolar', 'title' => 'Vida escolar'],
            ['slug' => 'vida-escolar-actividades', 'title' => 'Actividades'],
            ['slug' => 'vida-escolar-talleres', 'title' => 'Talleres'],
            ['slug' => 'vida-escolar-eventos', 'title' => 'Eventos'],
            ['slug' => 'galeria', 'title' => 'Galería', 'subtitle' => 'Momentos del colegio'],
            ['slug' => 'noticias', 'title' => 'Noticias', 'subtitle' => 'Actualidad institucional'],
            ['slug' => 'contacto', 'title' => 'Contacto', 'subtitle' => 'Escríbenos o agenda una visita'],
        ];

        foreach ($pages as $index => $page) {
            CmsPage::query()->updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'subtitle' => $page['subtitle'] ?? null,
                    'hero_title' => $page['title'],
                    'meta_title' => $page['title'].' | I.E.P. Horizonte',
                    'meta_description' => 'I.E.P. Horizonte — '.$page['title'],
                    'status' => CmsPublicationStatus::Published,
                    'published_at' => now()->subDays(30),
                    'template' => $page['template'] ?? 'default',
                    'sort_order' => $page['sort_order'] ?? $index,
                    'robots_index' => true,
                ],
            );
        }
    }

    private function seedHomeSections(): void
    {
        $sections = [
            [
                'section_key' => 'stats',
                'title' => 'Indicadores institucionales',
                'payload' => [
                    'items' => [
                        ['value' => 15, 'suffix' => '+', 'label' => 'Años de experiencia'],
                        ['value' => 3, 'suffix' => '', 'label' => 'Niveles educativos'],
                        ['value' => 850, 'suffix' => '+', 'label' => 'Familias en comunidad'],
                        ['value' => 45, 'suffix' => '+', 'label' => 'Docentes especializados'],
                    ],
                ],
                'sort_order' => 0,
            ],
            [
                'section_key' => 'teaser_nosotros',
                'title' => 'Teaser Nosotros',
                'payload' => [
                    'eyebrow' => 'Nosotros',
                    'title' => 'Una comunidad que aprende y crece junta',
                    'description' => 'Más de quince años formando estudiantes íntegros, competentes y comprometidos.',
                    'route_name' => 'public.nosotros',
                    'link_label' => 'Conocer el colegio',
                ],
                'sort_order' => 1,
            ],
            [
                'section_key' => 'teaser_niveles',
                'title' => 'Teaser Niveles',
                'payload' => [
                    'eyebrow' => 'Niveles educativos',
                    'title' => 'Tres etapas, una misma excelencia',
                    'description' => 'Progresión curricular con acompañamiento personalizado.',
                    'route_name' => 'public.niveles',
                    'link_label' => 'Explorar niveles',
                ],
                'sort_order' => 2,
            ],
            [
                'section_key' => 'cta_admision',
                'title' => 'CTA Admisión',
                'payload' => [
                    'title' => 'Admisión 2026 — cupos limitados',
                    'description' => 'Proceso claro, visitas guiadas y acompañamiento para tu familia.',
                    'primary_route' => 'public.admision',
                    'primary_label' => 'Postular ahora',
                    'secondary_route' => 'public.admision.requisitos',
                    'secondary_label' => 'Ver requisitos',
                ],
                'sort_order' => 3,
            ],
        ];

        foreach ($sections as $section) {
            CmsSection::query()->updateOrCreate(
                ['page_key' => 'home', 'section_key' => $section['section_key']],
                [
                    'title' => $section['title'],
                    'payload' => $section['payload'],
                    'is_active' => true,
                    'sort_order' => $section['sort_order'],
                ],
            );
        }
    }

    private function seedNewsCategories(): void
    {
        $categories = [
            ['name' => 'Admisión', 'slug' => 'admision', 'sort_order' => 0],
            ['name' => 'Vida escolar', 'slug' => 'vida-escolar', 'sort_order' => 1],
            ['name' => 'Logros', 'slug' => 'logros', 'sort_order' => 2],
        ];

        foreach ($categories as $cat) {
            CmsNewsCategory::query()->updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }

    private function seedNews(): void
    {
        $admision = CmsNewsCategory::query()->where('slug', 'admision')->first();
        $vida = CmsNewsCategory::query()->where('slug', 'vida-escolar')->first();
        $logros = CmsNewsCategory::query()->where('slug', 'logros')->first();

        $items = [
            [
                'slug' => 'apertura-proceso-admision-2026',
                'title' => 'Apertura del proceso de admisión 2026',
                'excerpt' => 'Conoce fechas, requisitos y agenda tu visita guiada al campus.',
                'body' => '<p>Iniciamos el proceso de admisión 2026. Las familias interesadas pueden agendar visitas guiadas y conocer nuestros niveles educativos.</p>',
                'category_id' => $admision?->id,
                'featured_image' => 'images/public/news-admision.jpg',
                'is_featured' => true,
                'published_at' => '2026-03-01',
            ],
            [
                'slug' => 'feria-ciencias-horizonte',
                'title' => 'Feria de ciencias: innovación desde el aula',
                'excerpt' => 'Proyectos destacados de primaria y secundaria en nuestra feria anual.',
                'body' => '<p>La feria de ciencias reunió proyectos STEAM de todos los niveles. Felicitamos a estudiantes y docentes mentores.</p>',
                'category_id' => $vida?->id,
                'featured_image' => 'images/public/news-feria.jpg',
                'is_featured' => false,
                'published_at' => '2026-02-15',
            ],
            [
                'slug' => 'reconocimiento-olimpiadas',
                'title' => 'Reconocimiento en olimpiadas regionales',
                'excerpt' => 'Nuestros estudiantes obtuvieron medallas en matemática y comunicación.',
                'body' => '<p>Celebramos el esfuerzo de nuestros representantes en las olimpiadas regionales de matemática y comunicación.</p>',
                'category_id' => $logros?->id,
                'featured_image' => 'images/public/news-olimpiadas.jpg',
                'is_featured' => false,
                'published_at' => '2026-01-20',
            ],
        ];

        foreach ($items as $item) {
            CmsNews::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    ...$item,
                    'status' => CmsPublicationStatus::Published,
                    'meta_title' => $item['title'],
                    'meta_description' => $item['excerpt'],
                    'robots_index' => true,
                ],
            );
        }
    }

    private function seedTestimonials(): void
    {
        $items = [
            [
                'name' => 'Sra. Carmen Vela',
                'role' => 'Apoderada',
                'org' => 'Comunidad Horizonte',
                'quote' => 'Horizonte ha sido un segundo hogar para mis hijos. Valoro la cercanía del equipo docente y la comunicación constante con las familias.',
                'sort_order' => 0,
            ],
            [
                'name' => 'Prof. Luis Mendoza',
                'role' => 'Docente de Secundaria',
                'org' => 'Área de Ciencias',
                'quote' => 'Aquí la excelencia académica se vive con calidez humana. Mis estudiantes se sienten acompañados y desafiados cada día.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Diego Ramírez',
                'role' => 'Egresado 2024',
                'org' => 'Ingeniería — universidad nacional',
                'quote' => 'La formación integral y los valores institucionales se notan en cada promoción. Es un colegio en el que confiamos plenamente.',
                'sort_order' => 2,
            ],
        ];

        foreach ($items as $item) {
            CmsTestimonial::query()->updateOrCreate(
                ['name' => $item['name'], 'role' => $item['role']],
                [...$item, 'is_visible' => true],
            );
        }
    }

    private function seedGallery(): void
    {
        $gallery = CmsGallery::query()->updateOrCreate(
            ['slug' => 'campus-horizonte'],
            [
                'title' => 'Campus Horizonte',
                'description' => 'Momentos e infraestructura del colegio.',
                'category' => 'Institucional',
                'is_active' => true,
                'sort_order' => 0,
            ],
        );

        $images = [
            ['image_path' => 'images/public/galeria-01.jpg', 'caption' => 'Campus al amanecer', 'category' => 'Infraestructura', 'sort_order' => 0],
            ['image_path' => 'images/public/galeria-02.jpg', 'caption' => 'Biblioteca', 'category' => 'Académico', 'sort_order' => 1],
            ['image_path' => 'images/public/galeria-03.jpg', 'caption' => 'Educación física', 'category' => 'Deportes', 'sort_order' => 2],
            ['image_path' => 'images/public/galeria-04.jpg', 'caption' => 'Aula inicial', 'category' => 'Inicial', 'sort_order' => 3],
            ['image_path' => 'images/public/galeria-05.jpg', 'caption' => 'Laboratorio', 'category' => 'Ciencia', 'sort_order' => 4],
            ['image_path' => 'images/public/galeria-06.jpg', 'caption' => 'Ensayo general', 'category' => 'Arte', 'sort_order' => 5],
        ];

        foreach ($images as $img) {
            CmsGalleryImage::query()->updateOrCreate(
                ['gallery_id' => $gallery->id, 'image_path' => $img['image_path']],
                [...$img, 'is_active' => true],
            );
        }
    }

    private function seedHeroSlide(): void
    {
        CmsHeroSlide::query()->updateOrCreate(
            ['title' => 'Excelencia académica con valores que perduran'],
            [
                'subtitle' => 'Colegio privado en Inicial, Primaria y Secundaria.',
                'image_path' => 'images/public/hero.jpg',
                'badge' => 'Admisión 2026',
                'cta_primary_label' => 'Admisión 2026',
                'cta_primary_url' => '/admision',
                'cta_secondary_label' => 'Conocer niveles',
                'cta_secondary_url' => '/niveles',
                'is_active' => true,
                'sort_order' => 0,
            ],
        );
    }

    private function seedMenus(): void
    {
        $header = CmsMenu::query()->updateOrCreate(
            ['name' => 'Principal', 'location' => CmsMenuLocation::Header->value],
            ['name' => 'Principal', 'location' => CmsMenuLocation::Header->value],
        );

        $footer = CmsMenu::query()->updateOrCreate(
            ['name' => 'Pie institucional', 'location' => CmsMenuLocation::Footer->value],
            ['name' => 'Pie institucional', 'location' => CmsMenuLocation::Footer->value],
        );

        $header->items()->delete();
        $footer->items()->delete();

        $headerItems = [
            ['label' => 'Inicio', 'route_name' => 'public.home', 'sort_order' => 0],
            ['label' => 'Nosotros', 'route_name' => 'public.nosotros', 'sort_order' => 1],
            ['label' => 'Niveles', 'route_name' => 'public.niveles', 'sort_order' => 2],
            ['label' => 'Admisión', 'route_name' => 'public.admision', 'sort_order' => 3],
            ['label' => 'Vida escolar', 'route_name' => 'public.vida-escolar', 'sort_order' => 4],
            ['label' => 'Galería', 'route_name' => 'public.galeria', 'sort_order' => 5],
            ['label' => 'Noticias', 'route_name' => 'public.noticias', 'sort_order' => 6],
            ['label' => 'Contacto', 'route_name' => 'public.contacto', 'sort_order' => 7],
        ];

        foreach ($headerItems as $item) {
            CmsMenuItem::query()->create([
                'menu_id' => $header->id,
                'label' => $item['label'],
                'route_name' => $item['route_name'],
                'sort_order' => $item['sort_order'],
                'is_active' => true,
                'target' => '_self',
            ]);
        }

        $footerItems = [
            ['label' => 'Nosotros', 'route_name' => 'public.nosotros', 'sort_order' => 0],
            ['label' => 'Admisión', 'route_name' => 'public.admision', 'sort_order' => 1],
            ['label' => 'Noticias', 'route_name' => 'public.noticias', 'sort_order' => 2],
            ['label' => 'Contacto', 'route_name' => 'public.contacto', 'sort_order' => 3],
            ['label' => 'Iniciar sesión', 'route_name' => 'login', 'sort_order' => 4],
        ];

        foreach ($footerItems as $item) {
            CmsMenuItem::query()->create([
                'menu_id' => $footer->id,
                'label' => $item['label'],
                'route_name' => $item['route_name'],
                'sort_order' => $item['sort_order'],
                'is_active' => true,
                'target' => '_self',
            ]);
        }
    }
}
