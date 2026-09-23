<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Who requested deletion (nullable FK to users)
            $table->unsignedBigInteger('deletion_requested_by')->nullable()->after('status');
            $table->timestamp('deletion_requested_at')->nullable()->after('deletion_requested_by');

            $table->foreign('deletion_requested_by')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });

        // Add new status values to enum
        // MySQL: ALTER TABLE to extend the enum
        DB::statement("ALTER TABLE patients MODIFY COLUMN status ENUM('active','inactive','discharged','pending_deletion','archived') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        // Remove new status values (revert to original enum)
        DB::statement("ALTER TABLE patients MODIFY COLUMN status ENUM('active','inactive','discharged') NOT NULL DEFAULT 'active'");

        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['deletion_requested_by']);
            $table->dropColumn(['deletion_requested_by', 'deletion_requested_at']);
        });
    }
};
