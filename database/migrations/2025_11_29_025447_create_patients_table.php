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
            $table->engine = 'InnoDB'; 
            $table->id();
            
            // 🔴 FINAL FIX: Use a regular integer() to match users.id (int(11))
            $table->integer('user_id'); 
            
            $table->string('patient_name');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('permanent_document')->nullable();
            
            $table->timestamps();
            
            // Define the foreign key constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->unique(['user_id', 'patient_name']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};