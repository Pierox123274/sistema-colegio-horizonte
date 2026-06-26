<?php

namespace Tests\Feature\Intranet;

use App\Enums\IntranetRole;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_administrador_crea_categoria(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $response = $this->actingAs($admin)->post(route('intranet.inventory.categories.store'), [
            'code' => 'CAT-TST',
            'name' => 'Categoría prueba',
            'description' => null,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_categories', [
            'code' => 'CAT-TST',
            'name' => 'Categoría prueba',
        ]);
    }

    public function test_administrador_crea_producto(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $category = ProductCategory::factory()->create();

        $response = $this->actingAs($admin)->post(route('intranet.inventory.products.store'), [
            'product_category_id' => $category->id,
            'code' => 'PRD-TST',
            'name' => 'Producto prueba',
            'description' => null,
            'product_type' => 'uniforme',
            'size' => 'M',
            'color' => 'Azul',
            'gender_target' => 'unisex',
            'unit' => 'unidad',
            'purchase_price' => '10.00',
            'sale_price' => '15.00',
            'current_stock' => '8.00',
            'minimum_stock' => '2.00',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'code' => 'PRD-TST',
            'product_category_id' => $category->id,
            'product_type' => 'uniforme',
            'size' => 'M',
        ]);
    }

    public function test_producto_agenda_sin_talla_especifica_se_crea_correctamente(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $category = ProductCategory::factory()->create();

        $response = $this->actingAs($admin)->post(route('intranet.inventory.products.store'), [
            'product_category_id' => $category->id,
            'code' => 'AGENDA-01',
            'name' => 'Agenda 2026',
            'description' => null,
            'product_type' => 'agenda',
            'size' => 'no_aplica',
            'color' => null,
            'gender_target' => 'no_aplica',
            'unit' => 'unidad',
            'purchase_price' => '12.00',
            'sale_price' => '18.00',
            'current_stock' => '10.00',
            'minimum_stock' => '2.00',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'code' => 'AGENDA-01',
            'product_type' => 'agenda',
            'size' => 'no_aplica',
        ]);
    }

    public function test_administrador_registra_entrada(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $product = Product::factory()->create([
            'current_stock' => 5,
        ]);

        $response = $this->actingAs($admin)->post(route('intranet.inventory.movements.store'), [
            'product_id' => $product->id,
            'type' => 'entrada',
            'quantity' => '4.00',
            'reason' => 'Reposición',
            'observations' => null,
        ]);

        $response->assertRedirect();
        $product->refresh();

        $this->assertSame('9.00', (string) $product->current_stock);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'entrada',
            'previous_stock' => '5.00',
            'new_stock' => '9.00',
        ]);
    }

    public function test_administrador_registra_salida(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $product = Product::factory()->create([
            'current_stock' => 10,
        ]);

        $response = $this->actingAs($admin)->post(route('intranet.inventory.movements.store'), [
            'product_id' => $product->id,
            'type' => 'salida',
            'quantity' => '3.00',
            'reason' => 'Consumo interno',
            'observations' => null,
        ]);

        $response->assertRedirect();
        $product->refresh();

        $this->assertSame('7.00', (string) $product->current_stock);
    }

    public function test_no_permite_stock_negativo(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $product = Product::factory()->create([
            'current_stock' => 2,
        ]);

        $response = $this->actingAs($admin)->post(route('intranet.inventory.movements.store'), [
            'product_id' => $product->id,
            'type' => 'salida',
            'quantity' => '5.00',
            'reason' => 'Error',
            'observations' => null,
        ]);

        $response->assertSessionHasErrors(['quantity']);
    }

    public function test_movimiento_recalcula_stock(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $product = Product::factory()->create([
            'current_stock' => 4,
        ]);

        $this->actingAs($admin)->post(route('intranet.inventory.movements.store'), [
            'product_id' => $product->id,
            'type' => 'ajuste',
            'quantity' => '11.00',
            'reason' => 'Conteo físico',
            'observations' => null,
        ])->assertRedirect();

        $product->refresh();
        $this->assertSame('11.00', (string) $product->current_stock);
    }

    public function test_secretaria_solo_visualiza(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'product_category_id' => $category->id,
        ]);

        $this->actingAs($secretaria)
            ->get(route('intranet.inventory.categories.index'))
            ->assertOk();

        $this->actingAs($secretaria)
            ->get(route('intranet.inventory.products.index'))
            ->assertOk();

        $this->actingAs($secretaria)
            ->post(route('intranet.inventory.categories.store'), [
                'code' => 'X-001',
                'name' => 'No debe',
            ])
            ->assertForbidden();

        $this->actingAs($secretaria)
            ->post(route('intranet.inventory.movements.store'), [
                'product_id' => $product->id,
                'type' => 'entrada',
                'quantity' => '2',
                'reason' => 'No debe',
            ])
            ->assertForbidden();
    }

    public function test_docente_no_accede(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);
        $this->actingAs($docente)
            ->get(route('intranet.inventory.products.index'))
            ->assertForbidden();
    }

    public function test_estudiante_no_accede(): void
    {
        $estudiante = $this->userWithRole(IntranetRole::Estudiante);
        $this->actingAs($estudiante)
            ->get(route('intranet.inventory.products.index'))
            ->assertForbidden();
    }

    public function test_apoderado_no_accede(): void
    {
        $apoderado = $this->userWithRole(IntranetRole::Apoderado);
        $this->actingAs($apoderado)
            ->get(route('intranet.inventory.products.index'))
            ->assertForbidden();
    }

    public function test_categoria_se_desactiva(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $category = ProductCategory::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->post(route('intranet.inventory.categories.deactivate', $category))
            ->assertRedirect(route('intranet.inventory.categories.index'));

        $this->assertDatabaseHas('product_categories', [
            'id' => $category->id,
            'is_active' => false,
        ]);
    }

    public function test_producto_se_desactiva(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->post(route('intranet.inventory.products.deactivate', $product))
            ->assertRedirect(route('intranet.inventory.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => false,
        ]);
    }

    public function test_no_se_elimina_producto_con_movimientos(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($admin)->post(route('intranet.inventory.movements.store'), [
            'product_id' => $product->id,
            'type' => 'entrada',
            'quantity' => '2.00',
            'reason' => 'Inicial',
            'observations' => null,
        ])->assertRedirect();

        $this->actingAs($admin)
            ->post(route('intranet.inventory.products.deactivate', $product))
            ->assertRedirect(route('intranet.inventory.products.index'));

        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->assertDatabaseHas('inventory_movements', ['product_id' => $product->id]);
    }

    public function test_ruta_crear_categoria_funciona(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $this->actingAs($admin)
            ->get(route('intranet.inventory.categories.create'))
            ->assertOk();
    }

    public function test_administrador_accede_formularios_y_detalle_producto(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'product_category_id' => $category->id,
            'name' => 'Polo institucional',
        ]);

        $this->actingAs($admin)
            ->get(route('intranet.inventory.products.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Inventory/Products/Create')
                ->has('catalog.categories'));

        $this->actingAs($admin)
            ->get(route('intranet.inventory.products.show', $product))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Inventory/Products/Show')
                ->where('product.id', $product->id)
                ->where('product.name', 'Polo institucional'));

        $this->actingAs($admin)
            ->get(route('intranet.inventory.products.edit', $product))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Inventory/Products/Edit')
                ->where('product.id', $product->id));
    }

    public function test_administrador_actualiza_producto(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'product_category_id' => $category->id,
            'name' => 'Nombre anterior',
            'product_type' => 'uniforme',
            'size' => 'M',
            'gender_target' => 'unisex',
            'sale_price' => 10,
        ]);

        $this->actingAs($admin)
            ->put(route('intranet.inventory.products.update', $product), [
                'product_category_id' => $category->id,
                'code' => $product->code,
                'name' => 'Nombre actualizado',
                'description' => 'Descripción nueva',
                'product_type' => 'uniforme',
                'size' => 'M',
                'color' => $product->color,
                'gender_target' => 'unisex',
                'unit' => $product->unit,
                'purchase_price' => '10.00',
                'sale_price' => '18.00',
                'current_stock' => '5.00',
                'minimum_stock' => '2.00',
                'is_active' => true,
            ])
            ->assertRedirect(route('intranet.inventory.products.show', $product))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Nombre actualizado',
            'sale_price' => '18.00',
        ]);
    }
}
