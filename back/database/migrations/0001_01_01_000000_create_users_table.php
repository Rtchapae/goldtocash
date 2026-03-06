<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Users table (from legacy gold2cash.sql)
        Schema::create('users', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('zip')->nullable();
            $table->string('state')->nullable();
            $table->text('address')->nullable();
            $table->text('city')->nullable();
            $table->string('phone', 30)->nullable();
            $table->integer('role')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_method_params')->nullable();
            $table->string('government_id')->nullable();
            $table->string('government_id_params')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('verify')->default(0);
        });

        // Password reset tokens (legacy `password_resets` table)
        Schema::create('password_resets', function (Blueprint $table): void {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Legacy sessions table (not Laravel's default session payload table)
        Schema::create('sessions', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedInteger('times')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('ip', 'sessions_ip_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('users');
    }
};
