<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite cannot drop FK-constrained columns. The cause_id column remains
        // in the initiatives table but is no longer used (removed from model fillable).
        // On MySQL/PostgreSQL this migration can be safely run manually if desired.
    }

    public function down(): void
    {
        //
    }
};
