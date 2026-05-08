<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('product_type', 30)->default('otro')->after('description');
            $table->string('size', 20)->default('unico')->after('product_type');
            $table->string('color', 50)->nullable()->after('size');
            $table->string('gender_target', 20)->default('no_aplica')->after('color');

            $table->index(['product_type', 'size']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropIndex(['product_type', 'size']);
            $table->dropColumn(['product_type', 'size', 'color', 'gender_target']);
        });
    }
};
