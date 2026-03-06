<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table): void {
            $table->unsignedInteger('id', true);
            $table->string('subject');
            $table->string('slug')->nullable()->comment('Unique slug for social media sharing. MD5 hashed string');
            $table->integer('max_participants')->nullable()->comment('Max number of participants allowed');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('avatar')->nullable()->comment('Profile picture for the conversation');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};





