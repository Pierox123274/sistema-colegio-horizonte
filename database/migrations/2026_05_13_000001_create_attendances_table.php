<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('educational_level_id')->constrained()->restrictOnDelete();
            $table->foreignId('grade_id')->constrained()->restrictOnDelete();
            $table->foreignId('section_id')->constrained()->restrictOnDelete();
            $table->date('attendance_date');
            $table->string('status', 20);
            $table->text('observation')->nullable();
            $table->foreignId('recorded_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'attendance_date', 'section_id'], 'attendances_student_date_section_unique');
            $table->index(['attendance_date', 'section_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
