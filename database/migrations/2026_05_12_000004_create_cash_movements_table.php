<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cash_register_id')->constrained('cash_registers')->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->string('type', 30);
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->timestamp('moved_at');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['cash_register_id', 'moved_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};
