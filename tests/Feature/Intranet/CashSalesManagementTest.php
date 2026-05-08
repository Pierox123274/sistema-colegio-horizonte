<?php

namespace Tests\Feature\Intranet;

use App\Enums\IntranetRole;
use App\Models\CashRegister;
use App\Models\Guardian;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sale;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CashSalesManagementTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_admin_abre_caja(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $this->actingAs($admin)->post(route('intranet.sales.cash-registers.open'), [
            'opening_balance' => '120.00',
        ])->assertRedirect(route('intranet.sales.cash-registers.index'));

        $this->assertDatabaseHas('cash_registers', [
            'user_id' => $admin->id,
            'status' => 'abierta',
        ]);
    }

    public function test_secretaria_abre_caja(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);
        $this->actingAs($secretaria)->post(route('intranet.sales.cash-registers.open'), [
            'opening_balance' => '50.00',
        ])->assertRedirect(route('intranet.sales.cash-registers.index'));
    }

    public function test_secretaria_registra_venta(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'product_category_id' => $category->id,
            'current_stock' => 10,
            'sale_price' => 20,
            'is_active' => true,
        ]);

        CashRegister::factory()->create([
            'user_id' => $secretaria->id,
            'status' => 'abierta',
        ]);

        $this->actingAs($secretaria)->post(route('intranet.sales.sales.store'), [
            'payment_method' => 'efectivo',
            'sold_at' => now()->toDateTimeString(),
            'items' => [
                ['product_id' => $product->id, 'quantity' => '2.00', 'unit_price' => '20.00'],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('sales', ['status' => 'registrada']);
    }

    public function test_venta_descuenta_stock(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $product = Product::factory()->create(['current_stock' => 5, 'is_active' => true, 'sale_price' => 15]);
        CashRegister::factory()->create(['user_id' => $admin->id, 'status' => 'abierta']);

        $this->actingAs($admin)->post(route('intranet.sales.sales.store'), [
            'payment_method' => 'yape',
            'sold_at' => now()->toDateTimeString(),
            'items' => [['product_id' => $product->id, 'quantity' => '3.00', 'unit_price' => '15.00']],
        ])->assertRedirect();

        $product->refresh();
        $this->assertSame('2.00', (string) $product->current_stock);
    }

    public function test_venta_sin_stock_falla(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $product = Product::factory()->create(['current_stock' => 1, 'is_active' => true, 'sale_price' => 15]);
        CashRegister::factory()->create(['user_id' => $admin->id, 'status' => 'abierta']);

        $this->actingAs($admin)->post(route('intranet.sales.sales.store'), [
            'payment_method' => 'yape',
            'sold_at' => now()->toDateTimeString(),
            'items' => [['product_id' => $product->id, 'quantity' => '3.00', 'unit_price' => '15.00']],
        ])->assertSessionHasErrors();
    }

    public function test_anular_venta_devuelve_stock(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $product = Product::factory()->create(['current_stock' => 4, 'is_active' => true, 'sale_price' => 10]);
        CashRegister::factory()->create(['user_id' => $admin->id, 'status' => 'abierta']);

        $this->actingAs($admin)->post(route('intranet.sales.sales.store'), [
            'payment_method' => 'efectivo',
            'sold_at' => now()->toDateTimeString(),
            'items' => [['product_id' => $product->id, 'quantity' => '2.00', 'unit_price' => '10.00']],
        ])->assertRedirect();

        $saleId = (int) Sale::query()->latest('id')->value('id');
        $this->actingAs($admin)->post(route('intranet.sales.sales.cancel', $saleId))->assertRedirect();

        $product->refresh();
        $this->assertSame('4.00', (string) $product->current_stock);
    }

    public function test_caja_cerrada_no_permite_venta(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $product = Product::factory()->create(['current_stock' => 4, 'is_active' => true, 'sale_price' => 10]);

        $this->actingAs($admin)->post(route('intranet.sales.sales.store'), [
            'payment_method' => 'efectivo',
            'sold_at' => now()->toDateTimeString(),
            'items' => [['product_id' => $product->id, 'quantity' => '1.00', 'unit_price' => '10.00']],
        ])->assertSessionHasErrors();
    }

    public function test_docente_no_accede(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);
        $this->actingAs($docente)->get(route('intranet.sales.sales.index'))->assertForbidden();
    }

    public function test_venta_con_estudiante_registra_correctamente(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);
        $student = Student::factory()->create();
        $product = Product::factory()->create(['current_stock' => 5, 'is_active' => true, 'sale_price' => 20]);
        CashRegister::factory()->create(['user_id' => $secretaria->id, 'status' => 'abierta']);

        $this->actingAs($secretaria)->post(route('intranet.sales.sales.store'), [
            'student_id' => (string) $student->id,
            'payment_method' => 'efectivo',
            'sold_at' => now()->toDateTimeString(),
            'items' => [['product_id' => $product->id, 'quantity' => '1.00', 'unit_price' => '20.00']],
        ])->assertRedirect();

        $this->assertDatabaseHas('sales', [
            'student_id' => $student->id,
            'guardian_id' => null,
        ]);
    }

    public function test_venta_con_estudiante_y_apoderado_registra_correctamente(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $student = Student::factory()->create();
        $guardian = Guardian::factory()->create();
        $student->guardians()->attach($guardian->id, [
            'relationship' => 'Padre',
            'is_primary' => true,
            'is_financial_responsible' => true,
            'emergency_priority' => 1,
        ]);
        $product = Product::factory()->create(['current_stock' => 5, 'is_active' => true, 'sale_price' => 20]);
        CashRegister::factory()->create(['user_id' => $admin->id, 'status' => 'abierta']);

        $this->actingAs($admin)->post(route('intranet.sales.sales.store'), [
            'student_id' => (string) $student->id,
            'guardian_id' => (string) $guardian->id,
            'payment_method' => 'efectivo',
            'sold_at' => now()->toDateTimeString(),
            'items' => [['product_id' => $product->id, 'quantity' => '1.00', 'unit_price' => '20.00']],
        ])->assertRedirect();

        $this->assertDatabaseHas('sales', [
            'student_id' => $student->id,
            'guardian_id' => $guardian->id,
        ]);
    }

    public function test_venta_sin_estudiante_registra_correctamente(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $product = Product::factory()->create(['current_stock' => 5, 'is_active' => true, 'sale_price' => 20]);
        CashRegister::factory()->create(['user_id' => $admin->id, 'status' => 'abierta']);

        $this->actingAs($admin)->post(route('intranet.sales.sales.store'), [
            'payment_method' => 'efectivo',
            'sold_at' => now()->toDateTimeString(),
            'items' => [['product_id' => $product->id, 'quantity' => '1.00', 'unit_price' => '20.00']],
        ])->assertRedirect();

        $this->assertDatabaseHas('sales', [
            'student_id' => null,
            'guardian_id' => null,
        ]);
    }

    public function test_apoderado_no_vinculado_al_estudiante_falla(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $student = Student::factory()->create();
        $guardian = Guardian::factory()->create();
        $product = Product::factory()->create(['current_stock' => 5, 'is_active' => true, 'sale_price' => 20]);
        CashRegister::factory()->create(['user_id' => $admin->id, 'status' => 'abierta']);

        $this->actingAs($admin)->post(route('intranet.sales.sales.store'), [
            'student_id' => (string) $student->id,
            'guardian_id' => (string) $guardian->id,
            'payment_method' => 'efectivo',
            'sold_at' => now()->toDateTimeString(),
            'items' => [['product_id' => $product->id, 'quantity' => '1.00', 'unit_price' => '20.00']],
        ])->assertSessionHasErrors(['guardian_id']);
    }

    public function test_export_pdf_responde_correctamente(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $this->actingAs($admin)
            ->get(route('intranet.sales.reports.export.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_excel_responde_correctamente(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $this->actingAs($admin)
            ->get(route('intranet.sales.reports.export.excel'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_ticket_termico_de_venta_responde_correctamente(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $sale = Sale::factory()->create();

        $this->actingAs($admin)
            ->get(route('intranet.sales.sales.receipt.ticket', $sale))
            ->assertOk()
            ->assertSee('Imprimir ticket');
    }

    public function test_detalle_venta_renderiza_pagina_con_acciones_de_comprobante(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $sale = Sale::factory()->create();

        $this->actingAs($admin)
            ->get(route('intranet.sales.sales.show', $sale))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Sales/Sales/Show')
                ->has('sale.id'));
    }

    public function test_nueva_venta_renderiza_pagina_con_navegacion_a_listado(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($admin)
            ->get(route('intranet.sales.sales.create'))
            ->assertInertia(fn (Assert $page) => $page->component('Intranet/Sales/Sales/Create'))
            ->assertDontSee('Ver listado');
    }

    public function test_timezone_configurada_para_hora_local(): void
    {
        $this->assertSame('America/Lima', config('app.timezone'));
    }
}
