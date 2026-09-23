<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalDocumentConsultationTable extends Migration
{
    /**
     * Tabla pivote: un documento clínico (repositorio del paciente) puede
     * referenciarse desde ninguno, uno o varios episodios (consultas).
     * El episodio no almacena el archivo, solo lo referencia.
     */
    public function up()
    {
        Schema::create('clinical_document_consultation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_document_id')->constrained()->onDelete('cascade');
            $table->foreignId('consultation_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['clinical_document_id', 'consultation_id'], 'doc_consultation_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clinical_document_consultation');
    }
}
