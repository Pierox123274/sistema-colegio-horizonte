<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cash_register_id')->constrained('cash_registers')->restrictOnDelete();
            $table->string('sale_code', 60)->unique();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('guardian_id')->nullable()->constrained('guardians')->nullOnDelete();
            $table->string('payment_method', 30);
            $table->string('status', 20)->default('registrada');
            $table->decimal('total', 10, 2);
            $table->timestamp('sold_at');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('canceled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('canceled_at')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index(['cash_register_id', 'sold_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
