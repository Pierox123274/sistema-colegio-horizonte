<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_classroom_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('host_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('meeting_type');
            $table->string('provider');
            $table->string('status')->default('scheduled');
            $table->dateTime('scheduled_at');
            $table->dateTime('ends_at');
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->string('join_url');
            $table->string('external_meeting_id')->nullable();
            $table->string('join_password')->nullable();
            $table->boolean('waiting_room_enabled')->default(true);
            $table->boolean('recording_allowed')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->json('recurrence_rule')->nullable();
            $table->boolean('is_private')->default(true);
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->json('provider_metadata')->nullable();
            $table->timestamps();

            $table->index(['status', 'scheduled_at']);
            $table->index(['host_user_id', 'scheduled_at']);
        });

        Schema::create('meeting_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('role')->default('participant');
            $table->dateTime('invited_at')->nullable();
            $table->timestamps();

            $table->unique(['virtual_meeting_id', 'user_id'], 'meeting_user_unique');
        });

        Schema::create('meeting_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('joined_at');
            $table->dateTime('left_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamps();

            $table->index(['virtual_meeting_id', 'user_id']);
        });

        Schema::create('meeting_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_meeting_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('url');
            $table->dateTime('recorded_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_recordings');
        Schema::dropIfExists('meeting_attendances');
        Schema::dropIfExists('meeting_participants');
        Schema::dropIfExists('virtual_meetings');
    }
};
