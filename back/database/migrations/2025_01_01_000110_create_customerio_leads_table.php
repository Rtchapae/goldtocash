<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customerio_leads', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('last_transmission_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customerio_leads');
    }
};





