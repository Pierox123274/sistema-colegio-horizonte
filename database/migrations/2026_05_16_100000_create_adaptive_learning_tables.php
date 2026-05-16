<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('topic');
            $table->string('question_type');
            $table->string('difficulty');
            $table->json('competencies')->nullable();
            $table->text('stem');
            $table->text('explanation')->nullable();
            $table->boolean('true_false_answer')->nullable();
            $table->string('short_answer_expected')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_banks')->cascadeOnDelete();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->string('label', 16)->nullable();
            $table->text('body');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        Schema::create('diagnostic_exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('mode');
            $table->boolean('is_active')->default(true);
            $table->boolean('prevent_retake_after_completion')->default(false);
            $table->unsignedSmallInteger('adaptive_question_count')->default(8);
            $table->timestamps();
        });

        Schema::create('diagnostic_exam_question_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_bank_id')->constrained('question_banks')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->unsignedSmallInteger('points')->default(1);
            $table->unique(['diagnostic_exam_id', 'question_bank_id'], 'diag_exam_question_unique');
        });

        Schema::create('diagnostic_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('diagnostic_exam_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->string('mode_snapshot');
            $table->decimal('score_percent', 6, 2)->nullable();
            $table->string('classified_level')->nullable();
            $table->json('answers')->nullable();
            $table->json('weakness_by_topic')->nullable();
            $table->json('adaptive_state')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamps();
        });

        Schema::create('learning_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('source');
            $table->string('title');
            $table->text('body');
            $table->string('topic')->nullable();
            $table->unsignedTinyInteger('priority')->default(3);
            $table->unsignedTinyInteger('estimated_weeks_to_improve')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('student_adaptive_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('last_classified_level')->nullable();
            $table->decimal('last_diagnostic_score', 6, 2)->nullable();
            $table->json('weakness_topics')->nullable();
            $table->json('learning_path')->nullable();
            $table->timestamp('last_diagnostic_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_adaptive_profiles');
        Schema::dropIfExists('learning_recommendations');
        Schema::dropIfExists('diagnostic_attempts');
        Schema::dropIfExists('diagnostic_exam_question_bank');
        Schema::dropIfExists('diagnostic_exams');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('question_banks');
    }
};
