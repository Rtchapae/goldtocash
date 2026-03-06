<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_in_reminder_sms', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('days_after_request')->nullable();
            $table->string('message')->nullable();
            $table->boolean('is_enabled')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_in_reminder_sms');
    }
};





