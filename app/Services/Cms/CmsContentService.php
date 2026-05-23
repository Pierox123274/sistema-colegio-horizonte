<?php

namespace App\Services\Cms;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\CmsMenuLocation;
use App\Enums\CmsPublicationStatus;
use App\Models\AuditLog;
use App\Models\Cms\CmsGallery;
use App\Models\Cms\CmsGalleryImage;
use App\Models\Cms\CmsHeroSlide;
use App\Models\Cms\CmsMedia;
use App\Models\Cms\CmsMenu;
use App\Models\Cms\CmsMenuItem;
use App\Models\Cms\CmsNews;
use App\Models\Cms\CmsNewsCategory;
use App\Models\Cms\CmsPage;
use App\Models\Cms\CmsSection;
use App\Models\Cms\CmsTestimonial;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CmsContentService
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly CmsSettingService $settings,
        private readonly CmsPublicService $public,
        private readonly CmsMediaService $media,
    ) {}

    /**
     * @return array{pages: int, news: int, galleries: int, testimonials: int}
     */
    public function dashboardStats(): array
    {
        return [
            'pages' => CmsPage::query()->count(),
            'news' => CmsNews::query()->count(),
            'galleries' => CmsGallery::query()->count(),
            'testimonials' => CmsTestimonial::query()->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboardOverview(): array
    {
        $draft = CmsPublicationStatus::Draft->value;

        return [
            'stats' => $this->dashboardStats(),
            'media_count' => CmsMedia::query()->count(),
            'pending' => [
                'draft_pages' => CmsPage::query()->where('status', $draft)->count(),
                'draft_news' => CmsNews::query()->where('status', $draft)->count(),
                'inactive_galleries' => CmsGallery::query()->where('is_active', false)->count(),
            ],
            'recent_changes' => AuditLog::query()
                ->where('module', AuditModule::Cms->value)
                ->with('user:id,name')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get()
                ->map(fn ($log) => [
                    'id' => $log->id,
                    'description' => $log->description,
                    'action' => $log->action,
                    'user_name' => $log->user?->name ?? 'Sistema',
                    'created_at' => $log->created_at?->toIso8601String(),
                ])
                ->all(),
        ];
    }

    /**
     * @param  array<string, string>  $filters
     */
    public function paginatePages(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applyPublicationFilters(
            CmsPage::query()->orderBy('sort_order')->orderBy('title'),
            $filters,
        )->paginate($perPage)->withQueryString();
    }

    public function createPage(User $actor, array $data): CmsPage
    {
        $page = CmsPage::query()->create($this->normalizePageData($data));
        $this->log($actor, AuditAction::Create, $page, 'Página CMS creada');
        $this->public->clearPageCache($page->slug);

        return $page;
    }

    public function updatePage(User $actor, CmsPage $page, array $data): CmsPage
    {
        $oldSlug = $page->slug;
        $old = $page->only(array_keys($data));
        $page->update($this->normalizePageData($data));
        $this->log($actor, AuditAction::Update, $page, 'Página CMS actualizada', $old, $page->only(array_keys($data)));
        $this->public->clearPageCache($oldSlug);
        if ($page->slug !== $oldSlug) {
            $this->public->clearPageCache($page->slug);
        }

        return $page->fresh();
    }

    public function deletePage(User $actor, CmsPage $page): void
    {
        $slug = $page->slug;
        $page->delete();
        $this->log($actor, AuditAction::Delete, CmsPage::class, 'Página CMS eliminada', entityId: null, description: "slug: {$slug}");
        $this->public->clearPageCache($slug);
    }

    /**
     * @param  array<string, string>  $filters
     */
    public function paginateNews(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applyPublicationFilters(
            CmsNews::query()->with('category:id,name,slug')->orderByDesc('published_at')->orderByDesc('id'),
            $filters,
        )->paginate($perPage)->withQueryString();
    }

    public function createNews(User $actor, array $data): CmsNews
    {
        $news = CmsNews::query()->create($this->normalizeNewsData($data));
        $this->log($actor, AuditAction::Create, $news, 'Noticia CMS creada');
        $this->public->clearNewsCache($news->slug);

        return $news->load('category:id,name,slug');
    }

    public function updateNews(User $actor, CmsNews $news, array $data): CmsNews
    {
        $oldSlug = $news->slug;
        $old = $news->only(array_keys($data));
        $news->update($this->normalizeNewsData($data));
        $this->log($actor, AuditAction::Update, $news, 'Noticia CMS actualizada', $old, $news->only(array_keys($data)));
        $this->public->clearNewsCache($oldSlug);
        if ($news->slug !== $oldSlug) {
            $this->public->clearNewsCache($news->slug);
        }

        return $news->fresh(['category:id,name,slug']);
    }

    public function deleteNews(User $actor, CmsNews $news): void
    {
        $slug = $news->slug;
        $news->delete();
        $this->log($actor, AuditAction::Delete, CmsNews::class, 'Noticia CMS eliminada', entityId: null, description: "slug: {$slug}");
        $this->public->clearNewsCache($slug);
    }

    /**
     * @return Collection<int, CmsNewsCategory>
     */
    public function newsCategories(): Collection
    {
        return CmsNewsCategory::query()->orderBy('sort_order')->orderBy('name')->get();
    }

    public function createNewsCategory(User $actor, array $data): CmsNewsCategory
    {
        $category = CmsNewsCategory::query()->create([
            ...$data,
            'slug' => $data['slug'] ?? Str::slug($data['name']),
        ]);
        $this->log($actor, AuditAction::Create, $category, 'Categoría de noticias creada');
        $this->public->clearNewsCache();

        return $category;
    }

    public function updateNewsCategory(User $actor, CmsNewsCategory $category, array $data): CmsNewsCategory
    {
        $old = $category->only(array_keys($data));
        $category->update($data);
        $this->log($actor, AuditAction::Update, $category, 'Categoría de noticias actualizada', $old, $category->only(array_keys($data)));
        $this->public->clearNewsCache();

        return $category->fresh();
    }

    public function deleteNewsCategory(User $actor, CmsNewsCategory $category): void
    {
        $category->delete();
        $this->log($actor, AuditAction::Delete, $category, 'Categoría de noticias eliminada');
        $this->public->clearNewsCache();
    }

    /**
     * @param  array<string, string>  $filters
     */
    public function paginateGalleries(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = CmsGallery::query()->withCount('images')->orderBy('sort_order');

        if ($search = trim($filters['search'] ?? '')) {
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function createGallery(User $actor, array $data): CmsGallery
    {
        $gallery = CmsGallery::query()->create([
            ...$data,
            'slug' => $data['slug'] ?? Str::slug($data['title']),
        ]);
        $this->log($actor, AuditAction::Create, $gallery, 'Galería CMS creada');
        $this->public->clearCache();

        return $gallery;
    }

    public function updateGallery(User $actor, CmsGallery $gallery, array $data): CmsGallery
    {
        $old = $gallery->only(array_keys($data));
        $gallery->update($data);
        $this->log($actor, AuditAction::Update, $gallery, 'Galería CMS actualizada', $old, $gallery->only(array_keys($data)));
        $this->public->clearCache();

        return $gallery->fresh();
    }

    public function deleteGallery(User $actor, CmsGallery $gallery): void
    {
        $gallery->delete();
        $this->log($actor, AuditAction::Delete, $gallery, 'Galería CMS eliminada');
        $this->public->clearCache();
    }

    /**
     * @param  list<array<string, mixed>>  $images
     */
    public function addGalleryImages(User $actor, CmsGallery $gallery, array $images, ?UploadedFile $upload = null): CmsGallery
    {
        DB::transaction(function () use ($gallery, $images, $upload, $actor): void {
            if ($upload !== null) {
                $media = $this->media->store($upload, $actor);
                CmsGalleryImage::query()->create([
                    'gallery_id' => $gallery->id,
                    'image_path' => $media->path,
                    'sort_order' => (int) ($gallery->images()->max('sort_order') ?? 0) + 1,
                ]);
            }

            foreach ($images as $row) {
                if (empty($row['image_path'])) {
                    continue;
                }
                CmsGalleryImage::query()->create([
                    'gallery_id' => $gallery->id,
                    'image_path' => $row['image_path'],
                    'caption' => $row['caption'] ?? null,
                    'category' => $row['category'] ?? null,
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ]);
            }
        });

        $this->log($actor, AuditAction::Update, $gallery, 'Imágenes agregadas a galería');
        $this->public->clearCache();

        return $gallery->fresh(['images']);
    }

    public function updateGalleryImage(User $actor, CmsGallery $gallery, CmsGalleryImage $image, array $data): CmsGalleryImage
    {
        abort_unless($image->gallery_id === $gallery->id, 404);

        $old = $image->only(array_keys($data));
        $image->update($data);
        $this->log($actor, AuditAction::Update, $gallery, 'Imagen de galería actualizada', $old, $image->only(array_keys($data)));
        $this->public->clearCache();

        return $image->fresh();
    }

    public function deleteGalleryImage(User $actor, CmsGallery $gallery, CmsGalleryImage $image): void
    {
        abort_unless($image->gallery_id === $gallery->id, 404);

        $image->delete();
        $this->log($actor, AuditAction::Delete, $gallery, 'Imagen eliminada de galería');
        $this->public->clearCache();
    }

    /**
     * @param  list<int>  $imageIds
     */
    public function reorderGalleryImages(User $actor, CmsGallery $gallery, array $imageIds): void
    {
        foreach ($imageIds as $order => $id) {
            CmsGalleryImage::query()
                ->where('gallery_id', $gallery->id)
                ->whereKey($id)
                ->update(['sort_order' => $order]);
        }

        $this->log($actor, AuditAction::Update, $gallery, 'Orden de galería actualizado');
        $this->public->clearCache();
    }

    /**
     * @param  array<string, string>  $filters
     */
    public function paginateTestimonials(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return CmsTestimonial::query()->orderBy('sort_order')->paginate($perPage)->withQueryString();
    }

    public function createTestimonial(User $actor, array $data): CmsTestimonial
    {
        $item = CmsTestimonial::query()->create($data);
        $this->log($actor, AuditAction::Create, $item, 'Testimonio CMS creado');
        $this->public->clearCache();

        return $item;
    }

    public function updateTestimonial(User $actor, CmsTestimonial $testimonial, array $data): CmsTestimonial
    {
        $old = $testimonial->only(array_keys($data));
        $testimonial->update($data);
        $this->log($actor, AuditAction::Update, $testimonial, 'Testimonio CMS actualizado', $old, $testimonial->only(array_keys($data)));
        $this->public->clearCache();

        return $testimonial->fresh();
    }

    public function deleteTestimonial(User $actor, CmsTestimonial $testimonial): void
    {
        $testimonial->delete();
        $this->log($actor, AuditAction::Delete, $testimonial, 'Testimonio CMS eliminado');
        $this->public->clearCache();
    }

    public function paginateHeroSlides(int $perPage = 15): LengthAwarePaginator
    {
        return CmsHeroSlide::query()->orderBy('sort_order')->paginate($perPage);
    }

    public function createHeroSlide(User $actor, array $data): CmsHeroSlide
    {
        $slide = CmsHeroSlide::query()->create($data);
        $this->log($actor, AuditAction::Create, $slide, 'Slide hero creado');
        $this->public->clearCache();

        return $slide;
    }

    public function updateHeroSlide(User $actor, CmsHeroSlide $slide, array $data): CmsHeroSlide
    {
        $old = $slide->only(array_keys($data));
        $slide->update($data);
        $this->log($actor, AuditAction::Update, $slide, 'Slide hero actualizado', $old, $slide->only(array_keys($data)));
        $this->public->clearCache();

        return $slide->fresh();
    }

    public function deleteHeroSlide(User $actor, CmsHeroSlide $slide): void
    {
        $slide->delete();
        $this->log($actor, AuditAction::Delete, $slide, 'Slide hero eliminado');
        $this->public->clearCache();
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public function updateSettings(User $actor, array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->settings->set((string) $key, $value);
        }
        $this->log($actor, AuditAction::Update, CmsPage::class, 'Configuración CMS actualizada', entityId: null);
        $this->settings->clearCache();
    }

    /**
     * @return Collection<int, CmsSection>
     */
    public function homepageSections(): Collection
    {
        return CmsSection::query()
            ->where('page_key', 'home')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @param  list<array<string, mixed>>  $sections
     */
    public function syncHomepageSections(User $actor, array $sections): void
    {
        foreach ($sections as $row) {
            CmsSection::query()->updateOrCreate(
                [
                    'page_key' => 'home',
                    'section_key' => $row['section_key'],
                ],
                [
                    'title' => $row['title'] ?? null,
                    'payload' => $row['payload'] ?? [],
                    'is_active' => (bool) ($row['is_active'] ?? true),
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                ],
            );
        }
        $this->log($actor, AuditAction::Update, CmsSection::class, 'Secciones de inicio actualizadas', entityId: null);
        $this->public->clearPageCache('home');
    }

    public function menuForLocation(CmsMenuLocation $location): ?CmsMenu
    {
        return CmsMenu::query()
            ->where('location', $location->value)
            ->with(['items' => fn ($q) => $q->orderBy('sort_order'), 'items.children' => fn ($q) => $q->orderBy('sort_order')])
            ->first();
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    public function syncMenu(User $actor, CmsMenu $menu, array $items): CmsMenu
    {
        DB::transaction(function () use ($menu, $items): void {
            $menu->items()->delete();
            $this->createMenuItems($menu->id, $items);
        });

        $this->log($actor, AuditAction::Update, $menu, 'Menú CMS actualizado');
        $this->public->clearCache();

        return $menu->fresh(['items.children']);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizePageData(array $data): array
    {
        if (isset($data['status']) && is_string($data['status'])) {
            $data['status'] = CmsPublicationStatus::from($data['status']);
        }

        if (empty($data['slug']) && ! empty($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeNewsData(array $data): array
    {
        if (isset($data['status']) && is_string($data['status'])) {
            $data['status'] = CmsPublicationStatus::from($data['status']);
        }

        if (empty($data['slug']) && ! empty($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $data;
    }

    /**
     * @param  Builder<Model>  $query
     * @param  array<string, string>  $filters
     * @return Builder<Model>
     */
    private function applyPublicationFilters(Builder $query, array $filters): Builder
    {
        if ($search = trim($filters['search'] ?? '')) {
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"));
        }

        if ($status = trim($filters['status'] ?? '')) {
            $query->where('status', $status);
        }

        return $query;
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    private function createMenuItems(int $menuId, array $items, ?int $parentId = null): void
    {
        foreach ($items as $index => $item) {
            $created = CmsMenuItem::query()->create([
                'menu_id' => $menuId,
                'parent_id' => $parentId,
                'label' => $item['label'],
                'url' => $item['url'] ?? null,
                'route_name' => $item['route_name'] ?? null,
                'route_params' => $item['route_params'] ?? null,
                'target' => $item['target'] ?? '_self',
                'sort_order' => (int) ($item['sort_order'] ?? $index),
                'is_active' => (bool) ($item['is_active'] ?? true),
            ]);

            if (! empty($item['children']) && is_array($item['children'])) {
                $this->createMenuItems($menuId, $item['children'], $created->id);
            }
        }
    }

    /**
     * @param  array<string, mixed>|null  $old
     * @param  array<string, mixed>|null  $new
     */
    private function log(
        User $actor,
        AuditAction $action,
        Model|string $entity,
        string $description,
        ?array $old = null,
        ?array $new = null,
        ?int $entityId = null,
    ): void {
        $type = is_string($entity) ? $entity : $entity::class;
        $id = is_string($entity) ? $entityId : $entity->getKey();

        $this->audit->log(
            action: $action,
            module: AuditModule::Cms,
            user: $actor,
            entityType: $type,
            entityId: is_int($id) ? $id : null,
            description: $description,
            oldValues: $old,
            newValues: $new,
        );
    }
}
