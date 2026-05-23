<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value');
            $table->timestamps();
        });

        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('hero_title')->nullable();
            $table->string('hero_subtitle')->nullable();
            $table->longText('body')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('robots_index')->default(true);
            $table->string('status');
            $table->timestamp('published_at')->nullable();
            $table->string('template')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['status', 'published_at']);
        });

        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id();
            $table->string('page_key');
            $table->string('section_key');
            $table->string('title')->nullable();
            $table->json('payload');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['page_key', 'section_key']);
            $table->index(['page_key', 'is_active', 'sort_order']);
        });

        Schema::create('cms_hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image_path')->nullable();
            $table->string('badge')->nullable();
            $table->string('cta_primary_label')->nullable();
            $table->string('cta_primary_url')->nullable();
            $table->string('cta_secondary_label')->nullable();
            $table->string('cta_secondary_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('cms_news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('cms_news_categories')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('featured_image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status');
            $table->timestamp('published_at')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('robots_index')->default(true);
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('is_featured');
        });

        Schema::create('cms_galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_gallery_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_id')->constrained('cms_galleries')->cascadeOnDelete();
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->string('category')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['gallery_id', 'is_active', 'sort_order']);
        });

        Schema::create('cms_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->string('org')->nullable();
            $table->text('quote');
            $table->string('photo_path')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->timestamps();

            $table->unique(['name', 'location']);
            $table->index('location');
        });

        Schema::create('cms_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('cms_menus')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('cms_menu_items')->nullOnDelete();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->json('route_params')->nullable();
            $table->string('target')->default('_self');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['menu_id', 'parent_id', 'sort_order']);
        });

        Schema::create('cms_media', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('disk')->default('public');
            $table->string('filename');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('alt')->nullable();
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_media');
        Schema::dropIfExists('cms_menu_items');
        Schema::dropIfExists('cms_menus');
        Schema::dropIfExists('cms_testimonials');
        Schema::dropIfExists('cms_gallery_images');
        Schema::dropIfExists('cms_galleries');
        Schema::dropIfExists('cms_news');
        Schema::dropIfExists('cms_news_categories');
        Schema::dropIfExists('cms_hero_slides');
        Schema::dropIfExists('cms_sections');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('cms_settings');
    }
};
