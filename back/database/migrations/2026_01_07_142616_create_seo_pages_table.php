<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seo_pages', function (Blueprint $table) {
            $table->id();
            $table->string('route_name')->unique(); // Route name like 'home', 'about', etc.
            $table->string('page_url')->unique(); // URL path like '/', '/about'
            $table->string('page_title'); // Human readable page title
            $table->string('meta_title')->nullable(); // SEO meta title
            $table->text('meta_description')->nullable(); // SEO meta description
            $table->json('meta_keywords')->nullable(); // SEO keywords as JSON array
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['route_name', 'is_active']);
            $table->index('page_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_pages');
    }
};
