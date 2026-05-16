<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Entornos donde `2026_05_17_120000_add_scope_and_thresholds_to_diagnostic_exams` no se aplicó
 * o falló dejan `diagnostic_exams` sin `created_by_user_id`, rompiendo consultas en
 * DiagnosticExamAccessService::queryExamsForTeacher.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('diagnostic_exams')) {
            return;
        }

        if (Schema::hasColumn('diagnostic_exams', 'created_by_user_id')) {
            return;
        }

        Schema::table('diagnostic_exams', function (Blueprint $table): void {
            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('diagnostic_exams')) {
            return;
        }

        if (! Schema::hasColumn('diagnostic_exams', 'created_by_user_id')) {
            return;
        }

        Schema::table('diagnostic_exams', function (Blueprint $table): void {
            $table->dropForeign(['created_by_user_id']);
        });
    }
};
