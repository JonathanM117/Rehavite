<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label')->nullable();
            $table->string('type')->default('text'); // text, number, select
            $table->timestamps();
        });

        // Seed default values
        DB::table('system_settings')->insert([
            ['key' => 'trash_days', 'value' => '15', 'label' => 'Días en Papelera antes de eliminación', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'audit_group_minutes', 'value' => '10', 'label' => 'Minutos para agrupar acciones en Historial', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
}
