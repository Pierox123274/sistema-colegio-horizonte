<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 64);
            $table->string('event_type', 128)->nullable();
            $table->string('status', 32)->default('received');
            $table->string('signature_valid', 8)->nullable();
            $table->unsignedSmallInteger('replay_count')->default(0);
            $table->json('payload_summary')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['provider', 'status']);
            $table->index('created_at');
        });

        Schema::create('integration_email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('mailable_class', 191);
            $table->string('recipient_hash', 64);
            $table->string('subject', 255)->nullable();
            $table->string('status', 32)->default('queued');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->string('mailer', 32)->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_email_logs');
        Schema::dropIfExists('integration_webhook_logs');
    }
};
