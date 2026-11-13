<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $newColumns = [
        // Identificación
        'body_regions',
        // Contacto
        'phone_owner', 'alt_contact_name', 'alt_contact_relationship', 'alt_contact_phone',
        // Fuente de referencia
        'referral_source', 'referral_source_other', 'referring_doctor',
        // Contacto de emergencia
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
        // Representante legal
        'guardian_required',
        // Entorno y situación social
        'birth_place', 'street_address', 'neighborhood', 'municipality', 'zone',
        'housing_type', 'transportation', 'architectural_barriers', 'education_level',
        // Antecedentes personales no patológicos
        'physical_build', 'stress_level', 'work_demand', 'diet', 'adl_independence',
        'perceived_health', 'sleep_habits', 'pets', 'non_pathological_notes',
        // Antecedentes (matriz familia/paciente)
        'medical_history_matrix', 'medical_history_deny_family', 'medical_history_deny_patient', 'medical_history_notes',
        // Interrogatorio por síntomas
        'symptoms_review',
        // Eventos y medicación
        'toxic_habits', 'allergies', 'allergies_detail',
        'hospitalizations', 'surgeries', 'traumas', 'current_medications', 'events_notes',
    ];

    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->json('body_regions')->nullable()->after('diagnosis');

            $table->string('phone_owner')->nullable()->after('phone');
            $table->string('alt_contact_name')->nullable()->after('phone_owner');
            $table->string('alt_contact_relationship')->nullable()->after('alt_contact_name');
            $table->string('alt_contact_phone')->nullable()->after('alt_contact_relationship');

            $table->string('referral_source')->nullable()->after('alt_contact_phone');
            $table->string('referral_source_other')->nullable()->after('referral_source');
            $table->string('referring_doctor')->nullable()->after('referral_source_other');

            $table->string('emergency_contact_name')->nullable()->after('referring_doctor');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');

            $table->boolean('guardian_required')->default(false)->after('emergency_contact_relationship');

            $table->string('birth_place')->nullable()->after('address');
            $table->string('street_address')->nullable()->after('birth_place');
            $table->string('neighborhood')->nullable()->after('street_address');
            $table->string('municipality')->nullable()->after('neighborhood');
            $table->string('zone')->nullable()->after('municipality');
            $table->string('housing_type')->nullable()->after('zone');
            $table->string('transportation')->nullable()->after('housing_type');
            $table->json('architectural_barriers')->nullable()->after('transportation');
            $table->string('education_level')->nullable()->after('architectural_barriers');

            $table->string('physical_build')->nullable()->after('economic_dependency');
            $table->unsignedTinyInteger('stress_level')->nullable()->after('physical_build');
            $table->string('work_demand')->nullable()->after('stress_level');
            $table->string('diet')->nullable()->after('work_demand');
            $table->string('adl_independence')->nullable()->after('diet');
            $table->string('perceived_health')->nullable()->after('adl_independence');
            $table->string('sleep_habits')->nullable()->after('perceived_health');
            $table->json('pets')->nullable()->after('sleep_habits');
            $table->text('non_pathological_notes')->nullable()->after('pets');

            $table->json('medical_history_matrix')->nullable()->after('family_history');
            $table->boolean('medical_history_deny_family')->default(false)->after('medical_history_matrix');
            $table->boolean('medical_history_deny_patient')->default(false)->after('medical_history_deny_family');
            $table->text('medical_history_notes')->nullable()->after('medical_history_deny_patient');

            $table->json('symptoms_review')->nullable()->after('systems_review');

            $table->json('toxic_habits')->nullable()->after('personal_pathological');
            $table->json('allergies')->nullable()->after('toxic_habits');
            $table->text('allergies_detail')->nullable()->after('allergies');
            $table->json('hospitalizations')->nullable()->after('allergies_detail');
            $table->json('surgeries')->nullable()->after('hospitalizations');
            $table->json('traumas')->nullable()->after('surgeries');
            $table->json('current_medications')->nullable()->after('traumas');
            $table->text('events_notes')->nullable()->after('current_medications');
        });

        $this->migrateExistingData();
    }

    private function migrateExistingData(): void
    {
        DB::table('patients')->orderBy('id')->chunkById(50, function ($patients) {
            foreach ($patients as $patient) {
                $npat = json_decode($patient->personal_non_pathological ?? '{}', true) ?: [];
                $pat = json_decode($patient->personal_pathological ?? '{}', true) ?: [];
                $fam = json_decode($patient->family_history ?? '{}', true) ?: [];
                $sys = json_decode($patient->systems_review ?? '{}', true) ?: [];

                $updates = [
                    'emergency_contact_name' => $npat['emergencia_nombre'] ?? null,
                    'emergency_contact_phone' => $npat['emergencia_telefono'] ?? null,
                    'emergency_contact_relationship' => $npat['emergencia_parentesco'] ?? null,
                    'referral_source_other' => $npat['referencia_como_entero'] ?? null,
                    'referring_doctor' => $npat['referencia_medico'] ?? null,
                    'alt_contact_phone' => $patient->phone_confirmation ?: null,
                    'birth_place' => $npat['lugar_nacimiento'] ?? null,
                    'municipality' => $npat['residencia_actual'] ?? null,
                    'housing_type' => $npat['casa_habitacion'] ?? null,
                    'transportation' => $npat['transporte'] ?? null,
                    'physical_build' => $npat['complexion_fisica'] ?? null,
                    'work_demand' => $npat['exigencia_laboral'] ?? null,
                    'diet' => $npat['dieta'] ?? null,
                    'adl_independence' => $npat['avd'] ?? null,
                    'perceived_health' => $npat['estado_salud'] ?? null,
                    'sleep_habits' => $npat['habitos_sueno'] ?? null,
                    'non_pathological_notes' => $npat['otros'] ?? null,
                    'allergies_detail' => $pat['alergias'] ?? null,
                    'events_notes' => $pat['otros'] ?? null,
                ];

                $stress = $npat['nivel_estres'] ?? null;
                $updates['stress_level'] = (is_numeric($stress) && $stress >= 0 && $stress <= 10) ? (int) $stress : null;

                foreach (['hospitalizacion' => 'hospitalizations', 'cirugias' => 'surgeries', 'traumatismos' => 'traumas'] as $oldKey => $newKey) {
                    $value = trim((string) ($pat[$oldKey] ?? ''));
                    $updates[$newKey] = json_encode($value !== '' ? [['detalle' => $value, 'mes' => '', 'cuando' => '']] : []);
                }
                $medValue = trim((string) ($pat['medicamentos_actuales'] ?? ''));
                $updates['current_medications'] = json_encode($medValue !== '' ? [['detalle' => $medValue, 'dosis' => '']] : []);

                $matrix = [];
                $famMap = ['diabetes' => 'diabetes', 'cardiacas' => 'card', 'vasculares' => 'vasc', 'pulmonares' => 'pulm', 'neoplasias' => 'neo', 'metabolicos' => 'metab', 'sindromes' => 'congen', 'sistema_nervioso' => 'neuro', 'autoinmunes' => 'autoinm'];
                foreach ($famMap as $oldKey => $newKey) {
                    $value = trim((string) ($fam[$oldKey] ?? ''));
                    if ($value !== '') {
                        $matrix[$newKey]['fam'] = true;
                        $matrix[$newKey]['fam_detalle'] = $value;
                    }
                }
                $pacMap = ['diabetes' => 'diabetes', 'hipertension' => 'hta', 'convulsiones' => 'convul', 'neoplasias' => 'neo', 'reumatologicos' => 'autoinm', 'psiquiatricos' => 'psiq', 'enfermedades_congenitas' => 'congen'];
                foreach ($pacMap as $oldKey => $newKey) {
                    $value = trim((string) ($pat[$oldKey] ?? ''));
                    if ($value !== '') {
                        $matrix[$newKey]['pac'] = true;
                        $matrix[$newKey]['pac_detalle'] = $value;
                    }
                }
                $updates['medical_history_matrix'] = json_encode($matrix);
                $updates['medical_history_notes'] = $fam['otros'] ?? null;

                $symptoms = [];
                $sysMap = ['visual' => 'visual', 'auditivo' => 'auditivo', 'sensorial' => 'sensib', 'musculoesqueletico' => 'musculo', 'gastrointestinal' => 'gastro', 'genitourinario' => 'genito', 'piel' => 'piel', 'otros' => 'otros', 'respiratorio' => 'disnea', 'cardiaco' => 'torax'];
                foreach ($sysMap as $oldKey => $newKey) {
                    $value = trim((string) ($sys[$oldKey] ?? ''));
                    if ($value !== '') {
                        $symptoms[$newKey] = ['presente' => true, 'detalle' => $value];
                    }
                }
                $updates['symptoms_review'] = json_encode($symptoms);

                DB::table('patients')->where('id', $patient->id)->update($updates);
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn($this->newColumns);
        });
    }
};
