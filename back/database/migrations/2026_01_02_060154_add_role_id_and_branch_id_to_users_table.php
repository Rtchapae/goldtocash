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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained('roles')->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->after('role_id')->constrained('branches')->onDelete('set null');
            
            // Add index for better query performance
            $table->index('role_id');
            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['branch_id']);
            $table->dropIndex(['role_id']);
            $table->dropIndex(['branch_id']);
            $table->dropColumn(['role_id', 'branch_id']);
        });
    }
};
