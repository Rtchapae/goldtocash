<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_inbounds', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('from', 20)->nullable();
            $table->string('to', 20)->nullable();
            $table->longText('message')->nullable();
            $table->boolean('considered_optout')->default(false);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('from');
        });

        Schema::create('sms_lookups', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('phone', 20)->nullable();
            $table->tinyInteger('is_mobile')->nullable();
            $table->tinyInteger('is_assumed')->nullable();
            $table->mediumText('assumption_reason')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('phone', 'sms_lookups_phone_index');
        });

        Schema::create('sms_optins', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('phone', 20)->nullable();
            $table->string('method', 100)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('phone');
        });

        Schema::create('sms_optouts', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('phone', 20)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_optouts');
        Schema::dropIfExists('sms_optins');
        Schema::dropIfExists('sms_lookups');
        Schema::dropIfExists('sms_inbounds');
    }
};





