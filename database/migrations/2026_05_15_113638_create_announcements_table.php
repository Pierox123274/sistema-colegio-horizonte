<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('priority');
            $table->string('audience_type');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('has_attachment')->default(false);
            $table->string('attachment_path')->nullable();
            $table->string('attachment_mime')->nullable();
            $table->string('attachment_original_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
            $table->index('audience_type');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
