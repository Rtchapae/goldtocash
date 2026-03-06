<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trace_events', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('trace_hash', 100)->nullable();
            $table->string('name')->nullable();
            $table->json('context')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('trace_hash', 'trace_events_trace_hash_index');
            $table->index(['created_at', 'name'], 'trace_events_created_at_name_index');
        });

        Schema::create('traces', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->string('hash', 100)->nullable();
            $table->json('source')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('user_id', 'traces_user_id_index');
            $table->index('hash');
            $table->index('created_at', 'traces_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traces');
        Schema::dropIfExists('trace_events');
    }
};





