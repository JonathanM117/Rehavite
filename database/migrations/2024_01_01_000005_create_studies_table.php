<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->onDelete('cascade');

            $table->date('date');
            $table->string('file_path');
            $table->text('description')->nullable();
            $table->enum('type', ['radiografia', 'resonancia', 'tomografia', 'ultrasonido', 'laboratorio', 'otro'])->default('otro');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studies');
    }
};
