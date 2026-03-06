<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trustpilot', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->longText('trustpilot_id')->nullable();
            $table->string('author_name')->nullable();
            $table->string('author_type')->nullable();
            $table->longText('author_url')->nullable();
            $table->string('title')->nullable();
            $table->longText('review')->nullable();
            $table->unsignedInteger('rating')->nullable();
            $table->timestamp('date_published');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trustpilot');
    }
};





