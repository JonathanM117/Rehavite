<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modify status column in patients table to VARCHAR(50) and add phone fields
        Schema::table('patients', function (Blueprint $table) {
            $table->string('phone_own', 20)->nullable()->after('phone');
            $table->string('phone_confirmation', 20)->nullable()->after('phone_own');
        });

        // Use raw SQL to change the status column from ENUM to VARCHAR
        DB::statement("ALTER TABLE patients MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'activo'");

        // 2. Add columns to consultations table
        Schema::table('consultations', function (Blueprint $table) {
            $table->text('limitations_restrictions')->nullable()->after('diagnosis');
            $table->json('clinical_flags')->nullable()->after('limitations_restrictions');
        });

        // 3. Add google_drive_link column to other tables if they exist
        if (Schema::hasTable('clinical_documents')) {
            Schema::table('clinical_documents', function (Blueprint $table) {
                $table->string('google_drive_link', 255)->nullable()->after('file_path');
            });
        }
        if (Schema::hasTable('studies')) {
            Schema::table('studies', function (Blueprint $table) {
                $table->string('google_drive_link', 255)->nullable()->after('file_path');
            });
        }
        if (Schema::hasTable('exercises')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->string('google_drive_link', 255)->nullable()->after('file_path');
            });
        }

        // 4. Correct typo in users table if exists
        try {
            DB::table('users')->where('name', 'aministrador')->update(['name' => 'Administrador']);
            DB::table('users')->where('name', 'Aministrador')->update(['name' => 'Administrador']);
        } catch (\Exception $e) {
            // Ignore if users table doesn't have name or is otherwise inaccessible in seed stage
        }
    }

    public function down(): void
    {
        // Revert google_drive_link columns
        if (Schema::hasTable('clinical_documents') && Schema::hasColumn('clinical_documents', 'google_drive_link')) {
            Schema::table('clinical_documents', function (Blueprint $table) {
                $table->dropColumn('google_drive_link');
            });
        }
        if (Schema::hasTable('studies') && Schema::hasColumn('studies', 'google_drive_link')) {
            Schema::table('studies', function (Blueprint $table) {
                $table->dropColumn('google_drive_link');
            });
        }
        if (Schema::hasTable('exercises') && Schema::hasColumn('exercises', 'google_drive_link')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->dropColumn('google_drive_link');
            });
        }

        // Revert consultations table additions
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn(['limitations_restrictions', 'clinical_flags']);
        });

        // Revert patients table modifications
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['phone_own', 'phone_confirmation']);
        });

        // Revert status to ENUM
        DB::statement("ALTER TABLE patients MODIFY COLUMN status ENUM('active','inactive','discharged','pending_deletion','archived') NOT NULL DEFAULT 'active'");
    }
};
