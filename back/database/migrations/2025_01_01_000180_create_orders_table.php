<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->text('description')->nullable();
            $table->decimal('amount', 8, 2)->default(0.00);
            $table->unsignedInteger('status')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->boolean('welcome')->default(true);
            $table->boolean('send_label')->default(false);
            $table->decimal('shipping', 8, 2)->default(0.00);
            $table->string('submission_url')->nullable();

            $table->index('user_id', 'orders_user_id_index');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('status')->references('id')->on('statuses');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};





