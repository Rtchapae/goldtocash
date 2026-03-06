<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notes', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->longText('text')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('verification_codes', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('channel', 10)->nullable();
            $table->string('target', 255)->nullable();
            $table->string('code', 10)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
        Schema::dropIfExists('user_notes');
    }
};





