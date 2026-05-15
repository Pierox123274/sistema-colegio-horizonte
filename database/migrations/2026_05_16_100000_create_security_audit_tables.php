<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_role', 80)->nullable();
            $table->string('action', 40);
            $table->string('module', 40);
            $table->string('entity_type', 120)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('description', 500)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('result', 20)->default('success');
            $table->string('severity', 20)->default('info');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'action']);
            $table->index(['entity_type', 'entity_id']);
            $table->index('created_at');
        });

        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('successful')->default(false);
            $table->string('failure_reason', 120)->nullable();
            $table->timestamp('attempted_at')->useCurrent();

            $table->index(['email', 'attempted_at']);
            $table->index(['ip_address', 'attempted_at']);
        });

        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('session_id', 120)->unique();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_label', 200)->nullable();
            $table->string('device_fingerprint', 64)->nullable();
            $table->timestamp('logged_in_at')->useCurrent();
            $table->timestamp('last_activity_at')->useCurrent();
            $table->timestamp('logged_out_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_suspicious')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index('last_activity_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('audit_logs');
    }
};
