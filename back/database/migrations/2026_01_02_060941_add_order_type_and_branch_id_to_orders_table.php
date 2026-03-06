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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_type')->default('online')->after('status');
            $table->foreignId('branch_id')->nullable()->after('order_type')->constrained('branches')->onDelete('set null');
            
            // Add index for better query performance
            $table->index('order_type');
            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropIndex(['order_type']);
            $table->dropIndex(['branch_id']);
            $table->dropColumn(['order_type', 'branch_id']);
        });
    }
};
