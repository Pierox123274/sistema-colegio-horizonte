<?php

namespace Database\Seeders;

use App\Enums\PaymentConceptType;
use App\Models\PaymentConcept;
use Illuminate\Database\Seeder;

class PaymentConceptSeeder extends Seeder
{
    public function run(): void
    {
        PaymentConcept::query()->firstOrCreate(
            ['code' => 'CON-PENSION-MENSUAL'],
            [
                'name' => 'Pensión mensual',
                'description' => 'Cuota mensual de enseñanza',
                'default_amount' => 350,
                'type' => PaymentConceptType::Pension,
                'is_active' => true,
            ],
        );

        PaymentConcept::query()->firstOrCreate(
            ['code' => 'CON-MATRICULA'],
            [
                'name' => 'Matrícula',
                'description' => 'Derecho de matrícula anual',
                'default_amount' => 120,
                'type' => PaymentConceptType::Matricula,
                'is_active' => true,
            ],
        );
    }
}
