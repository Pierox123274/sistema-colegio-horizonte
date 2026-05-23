<?php

namespace App\Services\Cms;

use App\Enums\CmsMenuLocation;
use App\Enums\CmsPublicationStatus;
use App\Models\Cms\CmsGallery;
use App\Models\Cms\CmsHeroSlide;
use App\Models\Cms\CmsMenu;
use App\Models\Cms\CmsNews;
use App\Models\Cms\CmsPage;
use App\Models\Cms\CmsSection;
use App\Models\Cms\CmsTestimonial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class CmsPublicService
{
    private const TTL = 3600;

    public function __construct(
        private readonly CmsSettingService $settings,
    ) {}

    /**
     * @return Collection<string, mixed>
     */
    public function settings(): Collection
    {
        return $this->settings->all();
    }

    /**
     * @return array{page: CmsPage|null, sections: Collection<int, CmsSection>}
     */
    public function homepage(): array
    {
        return Cache::remember('cms:public:homepage', self::TTL, function (): array {
            return [
                'page' => $this->publishedPage('home'),
                'sections' => CmsSection::query()
                    ->where('page_key', 'home')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get(),
            ];
        });
    }

    /**
     * @return array{page: CmsPage|null, sections: Collection<int, CmsSection>}
     */
    public function page(string $slug): array
    {
        return Cache::remember("cms:public:page:{$slug}", self::TTL, function () use ($slug): array {
            return [
                'page' => $this->publishedPage($slug),
                'sections' => CmsSection::query()
                    ->where('page_key', $slug)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get(),
            ];
        });
    }

    /**
     * @return Collection<int, CmsNews>
     */
    public function newsList(int $limit = 12): Collection
    {
        return Cache::remember("cms:public:news:list:{$limit}", self::TTL, fn () => $this->publishedNewsQuery()
            ->with('category:id,name,slug')
            ->limit($limit)
            ->get());
    }

    public function newsBySlug(string $slug): ?CmsNews
    {
        return Cache::remember("cms:public:news:{$slug}", self::TTL, fn () => $this->publishedNewsQuery()
            ->with('category:id,name,slug')
            ->where('slug', $slug)
            ->first());
    }

    /**
     * @return Collection<int, CmsGallery>
     */
    public function galleries(): Collection
    {
        return Cache::remember('cms:public:galleries', self::TTL, fn () => CmsGallery::query()
            ->where('is_active', true)
            ->with(['images' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get());
    }

    /**
     * @return Collection<int, CmsTestimonial>
     */
    public function testimonials(): Collection
    {
        return Cache::remember('cms:public:testimonials', self::TTL, fn () => CmsTestimonial::query()
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get());
    }

    /**
     * @return Collection<int, CmsHeroSlide>
     */
    public function heroSlides(): Collection
    {
        return Cache::remember('cms:public:hero-slides', self::TTL, function (): Collection {
            return CmsHeroSlide::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->filter(fn (CmsHeroSlide $s): bool => $s->isVisibleNow())
                ->values();
        });
    }

    /**
     * @return array{header: mixed, footer: mixed}
     */
    public function menus(): array
    {
        return Cache::remember('cms:public:menus', self::TTL, function (): array {
            $load = fn (CmsMenuLocation $loc) => CmsMenu::query()
                ->where('location', $loc->value)
                ->with(['items' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'), 'items.children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                ->first();

            return [
                'header' => $load(CmsMenuLocation::Header),
                'footer' => $load(CmsMenuLocation::Footer),
            ];
        });
    }

    public function clearCache(): void
    {
        $keys = [
            'cms:public:homepage',
            'cms:public:galleries',
            'cms:public:testimonials',
            'cms:public:hero-slides',
            'cms:public:menus',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    public function clearPageCache(?string $slug = null): void
    {
        $this->clearCache();

        if ($slug !== null) {
            Cache::forget("cms:public:page:{$slug}");
        }

        if ($slug === null || $slug === 'home') {
            Cache::forget('cms:public:homepage');
        }
    }

    public function clearNewsCache(?string $slug = null): void
    {
        $this->clearCache();

        if ($slug !== null) {
            Cache::forget("cms:public:news:{$slug}");
        }

        for ($i = 6; $i <= 24; $i += 6) {
            Cache::forget("cms:public:news:list:{$i}");
        }
    }

    private function publishedPage(string $slug): ?CmsPage
    {
        return CmsPage::query()
            ->where('slug', $slug)
            ->where('status', CmsPublicationStatus::Published)
            ->where(function ($q): void {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->first();
    }

    /**
     * @return Builder<CmsNews>
     */
    private function publishedNewsQuery()
    {
        return CmsNews::query()
            ->where('status', CmsPublicationStatus::Published)
            ->where(function ($q): void {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }
}
