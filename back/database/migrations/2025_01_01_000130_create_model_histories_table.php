<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_histories', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('user_type')->nullable();
            $table->string('message');
            $table->text('meta')->nullable();
            $table->timestamp('performed_at');

            $table->index(['model_type', 'model_id'], 'model_histories_model_type_model_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_histories');
    }
};





