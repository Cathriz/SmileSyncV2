<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->unsignedBigInteger('appointment_id')->nullable()->after('user_id'); 
            // We place it after user_id if that was added first.
            // $table->unique(['user_id', 'appointment_id']); // Unique constraint for de-duplication
        });
    }

    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            // $table->dropUnique(['user_id', 'appointment_id']);
            $table->dropColumn('appointment_id');
        });
    }
};