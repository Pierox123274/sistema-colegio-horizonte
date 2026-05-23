<?php

namespace App\Support;

use App\Models\Cms\CmsGallery;
use App\Models\Cms\CmsGalleryImage;
use App\Models\Cms\CmsHeroSlide;
use App\Models\Cms\CmsMenu;
use App\Models\Cms\CmsMenuItem;
use App\Models\Cms\CmsNews;
use App\Models\Cms\CmsPage;
use App\Models\Cms\CmsSection;
use App\Models\Cms\CmsTestimonial;
use App\Services\Cms\CmsPublicService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

final class CmsPublicPresenter
{
    public function __construct(
        private readonly CmsPublicService $cms,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function settingsForFrontend(): array
    {
        $raw = $this->cms->settings();

        return [
            'schoolName' => $this->unwrap($raw->get('school_name'), 'I.E.P. Horizonte'),
            'schoolTagline' => $this->unwrap($raw->get('school_tagline'), ''),
            'contact' => $raw->get('contact') ?? [],
            'social' => $raw->get('social') ?? [],
            'logoUrl' => $this->mediaUrl($this->unwrap($raw->get('logo_path'), null)),
            'faviconUrl' => $this->mediaUrl($this->unwrap($raw->get('favicon_path'), null)),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function sharedLayoutProps(): array
    {
        $menus = $this->cms->menus();

        return [
            'cmsSettings' => $this->settingsForFrontend(),
            'cmsMenus' => [
                'header' => $menus['header'] ? $this->menuToArray($menus['header']) : null,
                'footer' => $menus['footer'] ? $this->menuToArray($menus['footer']) : null,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function homeProps(): array
    {
        $data = $this->cms->homepage();
        /** @var CmsPage|null $page */
        $page = $data['page'];
        $sections = $this->sectionsKeyed($data['sections']);
        $hero = $this->heroFromSlides() ?? $this->heroFromPage($page);

        return array_merge($this->sharedLayoutProps(), [
            'cms' => [
                'hero' => $hero,
                'sections' => $sections,
                'news' => $this->newsListForCards($this->cms->newsList(6)),
                'testimonials' => $this->testimonialsForCards($this->cms->testimonials()),
            ],
            'seo' => $this->seoFromPage($page, 'I.E.P. Horizonte — Colegio privado de excelencia', 'Institución educativa en Inicial, Primaria y Secundaria.'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function pageProps(string $slug, string $defaultTitle, string $defaultDescription = ''): array
    {
        $data = $this->cms->page($slug);
        /** @var CmsPage|null $page */
        $page = $data['page'];

        return array_merge($this->sharedLayoutProps(), [
            'cmsPage' => $page ? $this->pageToArray($page) : null,
            'cmsSections' => $this->sectionsKeyed($data['sections']),
            'cmsHero' => $this->heroFromPage($page),
            'seo' => $this->seoFromPage($page, $defaultTitle, $defaultDescription),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function newsIndexProps(): array
    {
        $data = $this->cms->page('noticias');
        /** @var CmsPage|null $page */
        $page = $data['page'];

        return array_merge($this->sharedLayoutProps(), [
            'cmsPage' => $page ? $this->pageToArray($page) : null,
            'cmsHero' => $this->heroFromPage($page),
            'news' => $this->newsListForCards($this->cms->newsList(24)),
            'categories' => $this->cms->newsList(24)
                ->pluck('category')
                ->filter()
                ->unique('id')
                ->map(fn ($c) => ['name' => $c->name, 'slug' => $c->slug])
                ->values()
                ->all(),
            'seo' => $this->seoFromPage($page, 'Noticias — I.E.P. Horizonte', 'Comunicados institucionales.'),
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function newsShowProps(string $slug): ?array
    {
        $article = $this->cms->newsBySlug($slug);

        if ($article === null) {
            return null;
        }

        $related = $this->cms->newsList(6)
            ->filter(fn (CmsNews $n) => $n->slug !== $slug)
            ->take(2);

        return array_merge($this->sharedLayoutProps(), [
            'article' => $this->newsToDetail($article),
            'related' => $this->newsListForCards($related),
            'seo' => [
                'title' => ($article->meta_title ?: $article->title).' — Noticias',
                'description' => $article->meta_description ?: $article->excerpt,
                'image' => $this->mediaUrl($article->featured_image),
                'robotsIndex' => $article->robots_index,
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function galleryProps(): array
    {
        $data = $this->cms->page('galeria');
        /** @var CmsPage|null $page */
        $page = $data['page'];

        return array_merge($this->sharedLayoutProps(), [
            'cmsPage' => $page ? $this->pageToArray($page) : null,
            'cmsHero' => $this->heroFromPage($page),
            'galleryItems' => $this->galleryItemsFlat($this->cms->galleries()),
            'seo' => $this->seoFromPage($page, 'Galería — I.E.P. Horizonte', 'Momentos del colegio.'),
        ]);
    }

    public function mediaUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        if (Str::startsWith($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        return asset($path);
    }

    /**
     * @param  Collection<int, CmsSection>  $sections
     * @return array<string, array<string, mixed>>
     */
    private function sectionsKeyed(Collection $sections): array
    {
        $out = [];

        foreach ($sections as $section) {
            $out[$section->section_key] = [
                'key' => $section->section_key,
                'title' => $section->title,
                'payload' => $section->payload ?? [],
            ];
        }

        return $out;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function heroFromSlides(): ?array
    {
        $slide = $this->cms->heroSlides()->first();

        if ($slide === null) {
            return null;
        }

        return $this->heroSlideToArray($slide);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function heroFromPage(?CmsPage $page): ?array
    {
        if ($page === null) {
            return null;
        }

        return [
            'badge' => null,
            'title' => $page->hero_title ?: $page->title,
            'subtitle' => $page->hero_subtitle ?: $page->subtitle,
            'image' => $this->mediaUrl($page->hero_image),
            'primaryCta' => null,
            'secondaryCta' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function heroSlideToArray(CmsHeroSlide $slide): array
    {
        return [
            'badge' => $slide->badge,
            'title' => $slide->title,
            'subtitle' => $slide->subtitle,
            'image' => $this->mediaUrl($slide->image_path),
            'primaryCta' => $slide->cta_primary_label ? [
                'label' => $slide->cta_primary_label,
                'href' => $this->resolveUrl($slide->cta_primary_url),
            ] : null,
            'secondaryCta' => $slide->cta_secondary_label ? [
                'label' => $slide->cta_secondary_label,
                'href' => $this->resolveUrl($slide->cta_secondary_url),
            ] : null,
        ];
    }

    private function resolveUrl(?string $url): string
    {
        if ($url === null || $url === '') {
            return route('public.home');
        }

        if (Str::startsWith($url, ['http://', 'https://', '/'])) {
            return $url;
        }

        return '/'.ltrim($url, '/');
    }

    /**
     * @return array<string, mixed>
     */
    private function pageToArray(CmsPage $page): array
    {
        return [
            'slug' => $page->slug,
            'title' => $page->title,
            'subtitle' => $page->subtitle,
            'body' => $page->body,
            'template' => $page->template,
        ];
    }

    /**
     * @param  Collection<int, CmsNews>  $news
     * @return list<array<string, mixed>>
     */
    private function newsListForCards(Collection $news): array
    {
        return $news->map(fn (CmsNews $n) => [
            'slug' => $n->slug,
            'title' => $n->title,
            'excerpt' => $n->excerpt ?? '',
            'date' => $n->published_at?->toDateString() ?? $n->created_at->toDateString(),
            'category' => $n->category?->name ?? 'General',
            'image' => $this->mediaUrl($n->featured_image) ?? '',
            'featured' => $n->is_featured,
        ])->values()->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function newsToDetail(CmsNews $news): array
    {
        $body = $news->body ?? '';
        $paragraphs = $body !== ''
            ? array_values(array_filter(preg_split('/\n\s*\n|<\/p>\s*<p>/', strip_tags($body, '<p><br>')) ?: []))
            : [];

        if ($paragraphs === [] && $body !== '') {
            $paragraphs = [strip_tags($body)];
        }

        return [
            'slug' => $news->slug,
            'title' => $news->title,
            'excerpt' => $news->excerpt ?? '',
            'date' => $news->published_at?->toDateString() ?? $news->created_at->toDateString(),
            'category' => $news->category?->name ?? 'General',
            'image' => $this->mediaUrl($news->featured_image) ?? '',
            'body' => $body,
            'paragraphs' => $paragraphs,
        ];
    }

    /**
     * @param  Collection<int, CmsTestimonial>  $items
     * @return list<array<string, mixed>>
     */
    private function testimonialsForCards(Collection $items): array
    {
        return $items->map(fn (CmsTestimonial $t) => [
            'name' => $t->name,
            'role' => $t->role,
            'org' => $t->org,
            'quote' => $t->quote,
            'photo' => $this->mediaUrl($t->photo_path),
        ])->values()->all();
    }

    /**
     * @param  Collection<int, CmsGallery>  $galleries
     * @return list<array<string, mixed>>
     */
    private function galleryItemsFlat(Collection $galleries): array
    {
        $items = [];

        foreach ($galleries as $gallery) {
            foreach ($gallery->images as $image) {
                $items[] = $this->galleryImageToArray($image, $gallery);
            }
        }

        return $items;
    }

    /**
     * @return array<string, mixed>
     */
    private function galleryImageToArray(CmsGalleryImage $image, CmsGallery $gallery): array
    {
        return [
            'id' => (string) $image->id,
            'title' => $image->caption ?: $gallery->title,
            'category' => $image->category ?: $gallery->category,
            'image' => $this->mediaUrl($image->image_path) ?? '',
            'span' => $image->sort_order === 0 ? 'md:col-span-2 md:row-span-2 min-h-[18rem]' : '',
        ];
    }

    /**
     * @return array{title: string, description: string, image: ?string, robotsIndex: bool}
     */
    private function seoFromPage(?CmsPage $page, string $defaultTitle, string $defaultDescription): array
    {
        return [
            'title' => $page?->meta_title ?: $defaultTitle,
            'description' => $page?->meta_description ?: $defaultDescription,
            'image' => $this->mediaUrl($page?->og_image ?? $page?->hero_image),
            'robotsIndex' => $page?->robots_index ?? true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function menuToArray(CmsMenu $menu): array
    {
        return [
            'name' => $menu->name,
            'items' => $menu->items
                ->whereNull('parent_id')
                ->map(fn (CmsMenuItem $item) => $this->menuItemToArray($item))
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function menuItemToArray(CmsMenuItem $item): array
    {
        return [
            'label' => $item->label,
            'href' => $this->menuItemHref($item),
            'target' => $item->target,
            'children' => $item->children
                ->map(fn (CmsMenuItem $child) => $this->menuItemToArray($child))
                ->values()
                ->all(),
        ];
    }

    private function menuItemHref(CmsMenuItem $item): string
    {
        if ($item->route_name && Route::has($item->route_name)) {
            return route($item->route_name, $item->route_params ?? []);
        }

        return $item->url ?? '#';
    }

    private function unwrap(mixed $value, mixed $default): mixed
    {
        if (is_array($value) && array_key_exists('value', $value)) {
            return $value['value'] ?? $default;
        }

        return $value ?? $default;
    }
}
