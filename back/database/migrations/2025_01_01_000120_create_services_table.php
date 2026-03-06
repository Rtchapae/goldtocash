<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->boolean('is_enabled')->default(false);
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};





