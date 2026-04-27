<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');

            // 7. Motivo de consulta
            $table->text('reason');

            // 8. Padecimiento actual — JSON
            // Estructura: { inicio, mecanismo_lesion, evolucion, tratamiento_actual,
            //               limitacion_movimiento, eva, fuerza, actividad_fisica,
            //               comportamiento_sintomas, cambio_peso, cambios_apetito,
            //               debilidad_astenia, incontinencia, otros }
            $table->json('current_condition')->nullable();

            // 9. Estudios de imagen previos
            $table->text('previous_studies_notes')->nullable();

            // 10. Exploración física — JSON
            // Estructura: { temperatura, frecuencia_cardiaca, tension_arterial,
            //               frecuencia_respiratoria, peso, altura, traslado,
            //               tipo_marcha, pruebas_complementarias, otros }
            $table->json('physical_exam')->nullable();

            // 11. Diagnóstico fisioterapéutico
            $table->text('diagnosis')->nullable();

            // 12. Pronóstico
            $table->text('prognosis')->nullable();

            // 13. Objetivos de tratamiento
            $table->text('treatment_objectives')->nullable();

            // 15. Recomendaciones al alta
            $table->text('discharge_recommendations')->nullable();

            // 16. Motivo de alta
            $table->text('discharge_reason')->nullable();

            // Estado de la consulta
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
