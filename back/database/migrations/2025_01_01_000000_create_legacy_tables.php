<?php

// NOTE:
// This file previously contained a "mega" migration that created all legacy tables
// from gold2cash.sql in one place. At the user's request, that logic has been
// split into separate per-table migrations with proper foreign keys & indexes.
//
// We keep this migration as a no-op to avoid breaking existing migration history.

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // no-op
    }

    public function down(): void
    {
        // no-op
    }
};
