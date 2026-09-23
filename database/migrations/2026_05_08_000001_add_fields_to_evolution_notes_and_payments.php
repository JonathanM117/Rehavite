<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToEvolutionNotesAndPayments extends Migration
{
    public function up()
    {
        // Add user_id to evolution_notes
        Schema::table('evolution_notes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('consultation_id');
        });

        // Add concept to payments (to differentiate payment sessions)
        Schema::table('payments', function (Blueprint $table) {
            $table->string('concept')->nullable()->after('date');
        });
    }

    public function down()
    {
        Schema::table('evolution_notes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('concept');
        });
    }
}
