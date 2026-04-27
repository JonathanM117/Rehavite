<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->unique()->constrained()->onDelete('cascade');

            // Datos personales del paciente (sección 0)
            $table->integer('age')->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('laterality', 50)->nullable();
            $table->string('occupation')->nullable();
            $table->string('economic_dependency')->nullable();
            $table->string('primary_caregiver')->nullable();

            // Representante legal (sección 1)
            $table->string('guardian_name')->nullable();
            $table->date('guardian_birth_date')->nullable();
            $table->integer('guardian_age')->nullable();
            $table->string('guardian_gender', 50)->nullable();

            // Observaciones generales (sección 2)
            $table->text('observations')->nullable();

            // Antecedentes heredofamiliares (sección 3) — JSON
            // Estructura: { diabetes, cardiacas, vasculares, pulmonares, neoplasias,
            //               epilepsia, sindromes, sistema_nervioso, autoinmunes, otros }
            $table->json('family_history')->nullable();

            // Antecedentes personales no patológicos (sección 4) — JSON
            // Estructura: { lugar_nacimiento, residencia_actual, casa_habitacion, transporte,
            //               nivel_socioeconomico, animales_domesticos, dieta, avd,
            //               estado_salud, habitos_sueno, otros }
            $table->json('personal_non_pathological')->nullable();

            // Antecedentes personales patológicos (sección 5) — JSON
            // Estructura: { hospitalizacion, cirugias, habitos_toxicos, alergias,
            //               traumatismos, convulsiones, neoplasias, hipertension,
            //               diabetes, reumatologicos, psiquiatricos, enfermedades_congenitas,
            //               medicamentos_actuales, otros }
            $table->json('personal_pathological')->nullable();

            // Interrogatorio por aparatos y sistemas (sección 6) — JSON
            // Estructura: { visual, auditivo, sensorial, musculoesqueletico, sistema_nervioso,
            //               metabolico, respiratorio, cardiaco, gastrointestinal,
            //               genitourinario, piel, otros }
            $table->json('systems_review')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
