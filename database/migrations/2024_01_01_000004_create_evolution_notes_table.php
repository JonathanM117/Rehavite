<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evolution_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->onDelete('cascade');

            $table->date('date');
            $table->integer('session_number')->nullable();
            $table->text('description')->nullable();

            // Signos vitales
            $table->json('vital_signs')->nullable();
            // Estructura: { temp_start, temp_end, saturation_start, saturation_end,
            //               bp_start, bp_end, hr_start, hr_end }

            $table->text('plan')->nullable();
            $table->text('treatment')->nullable();
            $table->text('additional_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evolution_notes');
    }
};
