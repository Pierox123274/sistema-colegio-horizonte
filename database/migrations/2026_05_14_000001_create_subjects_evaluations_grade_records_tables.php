<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('evaluations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('educational_level_id')->constrained('educational_levels')->cascadeOnDelete();
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->string('title');
            $table->string('period');
            $table->date('evaluated_at');
            $table->decimal('max_score', 5, 2)->default(20);
            $table->decimal('weight', 5, 2)->default(1);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['subject_id', 'section_id', 'title', 'period', 'evaluated_at'], 'eval_unique_per_context');
        });

        Schema::create('grade_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->text('observations')->nullable();
            $table->foreignId('recorded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['evaluation_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_records');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('subjects');
    }
};
