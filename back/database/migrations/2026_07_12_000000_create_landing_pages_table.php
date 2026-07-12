<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('title');
            $table->string('path')->unique();
            $table->json('blocks');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['path', 'active']);
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
