<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table for sharing patients with collaborator physiotherapists
        Schema::create('patient_collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Collaborator
            $table->foreignId('granted_by')->constrained('users')->onDelete('cascade'); // Who granted access
            $table->timestamps();

            $table->unique(['patient_id', 'user_id']); // Prevent duplicate assignments
        });

        // Audit log for tracking who modified what and when
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // created, updated, deleted
            $table->string('model_type'); // e.g. App\Models\Consultation
            $table->unsignedBigInteger('model_id');
            $table->string('description')->nullable(); // Human-readable description
            $table->json('changes')->nullable(); // JSON of old/new values
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('patient_collaborators');
    }
};
