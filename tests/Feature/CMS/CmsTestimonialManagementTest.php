<?php

namespace Tests\Feature\CMS;

use App\Enums\IntranetRole;
use App\Models\Cms\CmsTestimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CmsTestimonialManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Administrador->value]);

        return $user;
    }

    public function test_admin_lists_testimonials(): void
    {
        CmsTestimonial::query()->create([
            'name' => 'María López',
            'role' => 'Apoderada',
            'org' => 'Primaria',
            'quote' => 'Excelente institución.',
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($this->admin())
            ->get(route('intranet.cms.testimonials.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Cms/Testimonials/Index')
                ->has('testimonials.data', 1));
    }

    public function test_admin_creates_testimonial(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('intranet.cms.testimonials.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Intranet/Cms/Testimonials/Create'));

        $response = $this->actingAs($admin)->post(route('intranet.cms.testimonials.store'), [
            'name' => 'Carlos Ruiz',
            'role' => 'Exalumno',
            'org' => 'Secundaria',
            'quote' => 'Formación de calidad.',
            'is_visible' => true,
            'sort_order' => 2,
        ]);

        $testimonial = CmsTestimonial::query()->where('name', 'Carlos Ruiz')->first();
        $this->assertNotNull($testimonial);
        $response->assertRedirect(route('intranet.cms.testimonials.edit', $testimonial));
    }

    public function test_admin_updates_and_deletes_testimonial(): void
    {
        $admin = $this->admin();
        $testimonial = CmsTestimonial::query()->create([
            'name' => 'Ana P.',
            'role' => 'Docente',
            'org' => null,
            'quote' => 'Texto inicial',
            'is_visible' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('intranet.cms.testimonials.edit', $testimonial))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Cms/Testimonials/Edit')
                ->where('testimonial.id', $testimonial->id));

        $this->actingAs($admin)
            ->put(route('intranet.cms.testimonials.update', $testimonial), [
                'name' => 'Ana Pérez',
                'role' => 'Docente',
                'org' => 'Horizonte',
                'quote' => 'Texto actualizado',
                'is_visible' => false,
                'sort_order' => 5,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('cms_testimonials', [
            'id' => $testimonial->id,
            'name' => 'Ana Pérez',
            'is_visible' => false,
        ]);

        $this->actingAs($admin)
            ->delete(route('intranet.cms.testimonials.destroy', $testimonial))
            ->assertRedirect(route('intranet.cms.testimonials.index'));

        $this->assertDatabaseMissing('cms_testimonials', ['id' => $testimonial->id]);
    }

    public function test_docente_cannot_manage_testimonials(): void
    {
        $docente = User::factory()->create();
        $docente->syncRoles([IntranetRole::Docente->value]);

        $this->actingAs($docente)
            ->get(route('intranet.cms.testimonials.index'))
            ->assertForbidden();
    }
}
