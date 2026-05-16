<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnostic_exams', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('subject_id')->constrained()->nullOnDelete();
            $table->foreignId('educational_level_id')->nullable()->after('academic_year_id')->constrained()->nullOnDelete();
            $table->foreignId('grade_id')->nullable()->after('educational_level_id')->constrained()->nullOnDelete();
            $table->foreignId('section_id')->nullable()->after('grade_id')->constrained()->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->after('section_id')->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('threshold_basic_percent')->default(40)->after('adaptive_question_count');
            $table->unsignedTinyInteger('threshold_intermediate_percent')->default(70)->after('threshold_basic_percent');
        });
    }

    public function down(): void
    {
        Schema::table('diagnostic_exams', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['educational_level_id']);
            $table->dropForeign(['grade_id']);
            $table->dropForeign(['section_id']);
            $table->dropForeign(['created_by_user_id']);
            $table->dropColumn([
                'academic_year_id',
                'educational_level_id',
                'grade_id',
                'section_id',
                'created_by_user_id',
                'threshold_basic_percent',
                'threshold_intermediate_percent',
            ]);
        });
    }
};
