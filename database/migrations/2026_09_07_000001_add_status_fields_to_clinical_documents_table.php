<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusFieldsToClinicalDocumentsTable extends Migration
{
    public function up()
    {
        Schema::table('clinical_documents', function (Blueprint $table) {
            $table->boolean('manage_status_manually')->default(false)->after('file_path');
            $table->string('manual_status')->nullable()->after('manage_status_manually');
            $table->date('valid_until')->nullable()->after('manual_status');
        });
    }

    public function down()
    {
        Schema::table('clinical_documents', function (Blueprint $table) {
            $table->dropColumn(['manage_status_manually', 'manual_status', 'valid_until']);
        });
    }
}
