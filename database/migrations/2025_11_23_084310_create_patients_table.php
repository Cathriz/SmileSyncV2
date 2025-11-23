<?php
// database/migrations/YYYY_MM_DD_HHMMSS_create_patients_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Links to your users/clinic table
            $table->string('patient_name')->unique(); // Links to the patient name in your records
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('permanent_document')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
