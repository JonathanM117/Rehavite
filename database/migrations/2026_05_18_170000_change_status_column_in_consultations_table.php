<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alter the enum column to a varchar to support the new Spanish status values
        DB::statement("ALTER TABLE consultations MODIFY COLUMN status VARCHAR(50) DEFAULT 'valoracion'");
    }

    public function down(): void
    {
        // Revert back to the old enum
        DB::statement("ALTER TABLE consultations MODIFY COLUMN status ENUM('open', 'in_progress', 'closed') DEFAULT 'open'");
    }
};
