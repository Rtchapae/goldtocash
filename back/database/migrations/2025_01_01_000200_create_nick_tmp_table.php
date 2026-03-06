<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nick_tmp', function (Blueprint $table): void {
            $table->timestamp('time')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('new_status')->nullable();
            $table->string('derived_code', 30)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nick_tmp');
    }
};





