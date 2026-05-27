<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_xp')->default(0);
            $table->unsignedInteger('current_level')->default(1);
            $table->unsignedInteger('xp_to_next_level')->default(300);
            $table->unsignedInteger('engagement_score')->default(0);
            $table->timestamps();
            $table->unique('student_id');
        });

        Schema::create('achievements', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('type', 40);
            $table->string('title');
            $table->text('description');
            $table->string('icon')->nullable();
            $table->string('color', 40)->default('navy');
            $table->string('rarity', 40)->default('common');
            $table->unsignedInteger('xp_reward')->default(0);
            $table->json('criteria')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_achievements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->timestamp('unlocked_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'achievement_id']);
        });

        Schema::create('experience_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('source', 60);
            $table->integer('points');
            $table->string('description')->nullable();
            $table->nullableMorphs('reference');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('student_streaks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40);
            $table->unsignedInteger('current_count')->default(0);
            $table->unsignedInteger('best_count')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'type']);
        });

        Schema::create('challenges', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('type', 40);
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('target_value')->default(1);
            $table->unsignedInteger('xp_reward')->default(50);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('student_challenges', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('progress_value')->default(0);
            $table->string('status', 40)->default('active');
            $table->timestamp('completed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'challenge_id']);
        });

        Schema::create('leaderboard_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->string('scope', 40)->default('institutional');
            $table->date('snapshot_date');
            $table->json('payload');
            $table->timestamps();
            $table->index(['scope', 'snapshot_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboard_snapshots');
        Schema::dropIfExists('student_challenges');
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('student_streaks');
        Schema::dropIfExists('experience_transactions');
        Schema::dropIfExists('student_achievements');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('gamification_profiles');
    }
};
