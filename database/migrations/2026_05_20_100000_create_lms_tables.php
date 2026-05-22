<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['academic_year_id', 'section_id', 'subject_id', 'teacher_user_id'], 'virtual_classroom_scope_unique');
        });

        Schema::create('virtual_classroom_announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('virtual_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_classroom_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('resource_type');
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->string('topic')->nullable();
            $table->string('competency')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_classroom_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('max_score', 6, 2)->default(20);
            $table->timestamp('due_at')->nullable();
            $table->json('rubric')->nullable();
            $table->boolean('is_published')->default(true);
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->string('file_path')->nullable();
            $table->text('student_comment')->nullable();
            $table->text('teacher_feedback')->nullable();
            $table->decimal('score', 6, 2)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['assignment_id', 'student_id'], 'assignment_student_unique');
        });

        Schema::create('online_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_classroom_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('grading_mode')->default('automatic');
            $table->unsignedSmallInteger('time_limit_minutes')->nullable();
            $table->unsignedTinyInteger('max_attempts')->default(1);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('show_results_after')->default(true);
            $table->boolean('is_published')->default(true);
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('online_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_exam_id')->constrained()->cascadeOnDelete();
            $table->string('question_type');
            $table->text('stem');
            $table->json('options')->nullable();
            $table->json('correct_answer')->nullable();
            $table->unsignedSmallInteger('points')->default(1);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('topic')->nullable();
            $table->timestamps();
        });

        Schema::create('online_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->string('status');
            $table->json('answers')->nullable();
            $table->decimal('score_percent', 6, 2)->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('academic_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->timestamps();
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_calendar_events');
        Schema::dropIfExists('online_exam_attempts');
        Schema::dropIfExists('online_exam_questions');
        Schema::dropIfExists('online_exams');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('virtual_resources');
        Schema::dropIfExists('virtual_classroom_announcements');
        Schema::dropIfExists('virtual_classrooms');
    }
};
