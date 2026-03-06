<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sent_message_receipts', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('mid', 200)->nullable();
            $table->string('status', 40)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('mid');
        });

        Schema::create('sent_messages', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('provider_name', 20)->nullable();
            $table->string('mid', 200)->nullable();
            $table->string('from', 20)->nullable();
            $table->string('to', 20)->nullable();
            $table->longText('message')->nullable();
            $table->string('status', 50)->nullable();
            $table->longText('details')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('to');
            $table->index('mid');
            $table->index('created_at', 'sent_messages_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sent_messages');
        Schema::dropIfExists('sent_message_receipts');
    }
};





