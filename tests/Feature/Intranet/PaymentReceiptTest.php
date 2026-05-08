<?php

namespace Tests\Feature\Intranet;

use App\Enums\IntranetRole;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentReceiptTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_administrador_puede_ver_comprobante(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $payment = Payment::factory()->create([
            'created_by_user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('intranet.payments.receipt', $payment))
            ->assertOk()
            ->assertSee('Comprobante de Pago');
    }

    public function test_secretaria_puede_ver_comprobante(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);
        $payment = Payment::factory()->create([
            'created_by_user_id' => $secretaria->id,
        ]);

        $this->actingAs($secretaria)
            ->get(route('intranet.payments.receipt', $payment))
            ->assertOk()
            ->assertSee('Comprobante de Pago');
    }

    public function test_docente_no_accede_comprobante(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);
        $payment = Payment::factory()->create();

        $this->actingAs($docente)
            ->get(route('intranet.payments.receipt', $payment))
            ->assertForbidden();
    }

    public function test_pdf_responde_correctamente(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $payment = Payment::factory()->create([
            'created_by_user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('intranet.payments.receipt.pdf', $payment))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_ticket_responde_correctamente(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $payment = Payment::factory()->create([
            'created_by_user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('intranet.payments.receipt.ticket', $payment))
            ->assertOk()
            ->assertSee('Imprimir ticket');
    }
}
