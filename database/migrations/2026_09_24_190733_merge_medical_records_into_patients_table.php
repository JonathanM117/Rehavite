<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Unifica "patients" y "medical_records" en una sola tabla: el paciente y
     * su historia clínica dejan de ser dos registros separados.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // "manual_age" en vez de "age": Patient ya tiene un accessor
            // getAgeAttribute() calculado desde birth_date; este es el valor
            // editable a mano que antes vivía en medical_records.age.
            $table->integer('manual_age')->nullable()->after('diagnosis');
            $table->string('marital_status', 50)->nullable()->after('manual_age');
            $table->string('address')->nullable()->after('marital_status');
            $table->string('laterality', 50)->nullable()->after('address');
            $table->string('occupation')->nullable()->after('laterality');
            $table->string('economic_dependency')->nullable()->after('occupation');
            $table->string('primary_caregiver')->nullable()->after('economic_dependency');
            $table->string('guardian_name')->nullable()->after('primary_caregiver');
            $table->date('guardian_birth_date')->nullable()->after('guardian_name');
            $table->integer('guardian_age')->nullable()->after('guardian_birth_date');
            $table->string('guardian_gender', 50)->nullable()->after('guardian_age');
            $table->text('observations')->nullable()->after('guardian_gender');
            $table->json('family_history')->nullable()->after('observations');
            $table->json('personal_non_pathological')->nullable()->after('family_history');
            $table->json('personal_pathological')->nullable()->after('personal_non_pathological');
            $table->json('systems_review')->nullable()->after('personal_pathological');
            $table->json('pediatric_history')->nullable()->after('systems_review');
        });

        if (Schema::hasTable('medical_records')) {
            DB::table('medical_records')->orderBy('id')->chunk(200, function ($records) {
                foreach ($records as $r) {
                    DB::table('patients')->where('id', $r->patient_id)->update([
                        'manual_age'                 => $r->age,
                        'marital_status'              => $r->marital_status,
                        'address'                     => $r->address,
                        'laterality'                  => $r->laterality,
                        'occupation'                  => $r->occupation,
                        'economic_dependency'         => $r->economic_dependency,
                        'primary_caregiver'           => $r->primary_caregiver,
                        'guardian_name'               => $r->guardian_name,
                        'guardian_birth_date'         => $r->guardian_birth_date,
                        'guardian_age'                => $r->guardian_age,
                        'guardian_gender'             => $r->guardian_gender,
                        'observations'                => $r->observations,
                        'family_history'              => $r->family_history,
                        'personal_non_pathological'   => $r->personal_non_pathological,
                        'personal_pathological'       => $r->personal_pathological,
                        'systems_review'              => $r->systems_review,
                        'pediatric_history'           => $r->pediatric_history,
                    ]);
                }
            });

            Schema::drop('medical_records');
        }
    }

    public function down(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->unique()->constrained()->onDelete('cascade');
            $table->integer('age')->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('laterality', 50)->nullable();
            $table->string('occupation')->nullable();
            $table->string('economic_dependency')->nullable();
            $table->string('primary_caregiver')->nullable();
            $table->string('guardian_name')->nullable();
            $table->date('guardian_birth_date')->nullable();
            $table->integer('guardian_age')->nullable();
            $table->string('guardian_gender', 50)->nullable();
            $table->text('observations')->nullable();
            $table->json('family_history')->nullable();
            $table->json('personal_non_pathological')->nullable();
            $table->json('personal_pathological')->nullable();
            $table->json('systems_review')->nullable();
            $table->json('pediatric_history')->nullable();
            $table->timestamps();
        });

        DB::table('patients')->whereNotNull('manual_age')
            ->orWhereNotNull('marital_status')
            ->orWhereNotNull('address')
            ->orWhereNotNull('observations')
            ->orWhereNotNull('family_history')
            ->chunkById(200, function ($patients) {
                foreach ($patients as $p) {
                    DB::table('medical_records')->insert([
                        'patient_id'                  => $p->id,
                        'age'                          => $p->manual_age,
                        'marital_status'               => $p->marital_status,
                        'address'                      => $p->address,
                        'laterality'                   => $p->laterality,
                        'occupation'                   => $p->occupation,
                        'economic_dependency'          => $p->economic_dependency,
                        'primary_caregiver'             => $p->primary_caregiver,
                        'guardian_name'                => $p->guardian_name,
                        'guardian_birth_date'           => $p->guardian_birth_date,
                        'guardian_age'                  => $p->guardian_age,
                        'guardian_gender'               => $p->guardian_gender,
                        'observations'                  => $p->observations,
                        'family_history'                => $p->family_history,
                        'personal_non_pathological'     => $p->personal_non_pathological,
                        'personal_pathological'         => $p->personal_pathological,
                        'systems_review'                => $p->systems_review,
                        'pediatric_history'              => $p->pediatric_history,
                        'created_at'                    => now(),
                        'updated_at'                    => now(),
                    ]);
                }
            });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'manual_age', 'marital_status', 'address', 'laterality', 'occupation',
                'economic_dependency', 'primary_caregiver', 'guardian_name',
                'guardian_birth_date', 'guardian_age', 'guardian_gender', 'observations',
                'family_history', 'personal_non_pathological', 'personal_pathological',
                'systems_review', 'pediatric_history',
            ]);
        });
    }
};
