<?php

namespace Tests\Feature\CMS;

use App\Enums\CmsPublicationStatus;
use App\Enums\IntranetRole;
use App\Models\Cms\CmsNews;
use App\Models\Cms\CmsPage;
use App\Models\User;
use Database\Seeders\CmsContentSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CmsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(CmsContentSeeder::class);
    }

    public function test_admin_can_access_cms_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles([IntranetRole::Administrador->value]);

        $this->actingAs($admin)
            ->get(route('intranet.cms.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Intranet/Cms/Dashboard'));
    }

    public function test_secretaria_can_access_cms_news(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Secretaria->value]);

        $this->actingAs($user)
            ->get(route('intranet.cms.news.index'))
            ->assertOk();
    }

    public function test_docente_cannot_access_cms(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Docente->value]);

        $this->actingAs($user)
            ->get(route('intranet.cms.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_creates_published_news(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles([IntranetRole::Administrador->value]);

        $this->actingAs($admin)->post(route('intranet.cms.news.store'), [
            'slug' => 'nueva-noticia-cms',
            'title' => 'Noticia CMS de prueba',
            'excerpt' => 'Extracto demo',
            'body' => '<p>Contenido publicado desde el panel.</p>',
            'status' => CmsPublicationStatus::Published->value,
            'published_at' => now()->toDateString(),
            'is_featured' => false,
            'robots_index' => true,
        ])->assertRedirect();

        $this->assertDatabaseHas('cms_news', [
            'slug' => 'nueva-noticia-cms',
            'title' => 'Noticia CMS de prueba',
            'status' => CmsPublicationStatus::Published->value,
        ]);
    }

    public function test_public_home_shows_cms_news(): void
    {
        $response = $this->get(route('public.home'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->has('cms.news')
            ->where('cms.news.0.slug', 'apertura-proceso-admision-2026'));
    }

    public function test_public_news_show_from_cms(): void
    {
        $this->get(route('public.noticias.show', ['slug' => 'feria-ciencias-horizonte']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/Noticias/Show')
                ->where('article.slug', 'feria-ciencias-horizonte'));
    }

    public function test_admin_updates_homepage_page(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles([IntranetRole::Administrador->value]);

        $page = CmsPage::query()->where('slug', 'home')->firstOrFail();

        $this->actingAs($admin)->put(route('intranet.cms.pages.update', $page), [
            'slug' => 'home',
            'title' => 'Inicio actualizado',
            'subtitle' => 'Bienvenida CMS',
            'hero_title' => 'Horizonte CMS',
            'hero_subtitle' => 'Subtítulo dinámico',
            'body' => null,
            'status' => CmsPublicationStatus::Published->value,
            'published_at' => now()->toDateString(),
            'meta_title' => 'Inicio | Horizonte',
            'meta_description' => 'Descripción CMS',
            'robots_index' => true,
        ])->assertRedirect();

        $this->assertDatabaseHas('cms_pages', [
            'slug' => 'home',
            'title' => 'Inicio actualizado',
        ]);
    }

    public function test_admin_can_browse_cms_media(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles([IntranetRole::Administrador->value]);

        $this->actingAs($admin)
            ->getJson(route('intranet.cms.media.browse'))
            ->assertOk()
            ->assertJsonStructure(['data', 'meta']);
    }

    public function test_secretaria_can_access_cms_media(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Secretaria->value]);

        $this->actingAs($user)
            ->get(route('intranet.cms.media.index'))
            ->assertOk();
    }

    public function test_secretaria_cannot_delete_news(): void
    {
        $secretaria = User::factory()->create();
        $secretaria->syncRoles([IntranetRole::Secretaria->value]);

        $news = CmsNews::query()->firstOrFail();

        $this->actingAs($secretaria)
            ->delete(route('intranet.cms.news.destroy', $news))
            ->assertForbidden();
    }
}
