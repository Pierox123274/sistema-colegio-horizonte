<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table): void {
            $table->foreignId('evaluation_id')
                ->nullable()
                ->after('virtual_classroom_id')
                ->constrained('evaluations')
                ->nullOnDelete();
        });

        Schema::table('online_exams', function (Blueprint $table): void {
            $table->foreignId('evaluation_id')
                ->nullable()
                ->after('virtual_classroom_id')
                ->constrained('evaluations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('online_exams', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('evaluation_id');
        });

        Schema::table('assignments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('evaluation_id');
        });
    }
};
