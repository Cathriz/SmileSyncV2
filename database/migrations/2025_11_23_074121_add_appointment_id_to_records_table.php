<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // In database/migrations/2025_11_23_074121_add_appointment_id_to_records_table.php

public function up(): void
{
    Schema::table('records', function (Blueprint $table) {
        $table->unsignedBigInteger('appointment_id')->nullable()->after('user_id'); 
        // REMOVE THIS LINE:
        // $table->unique(['user_id', 'appointment_id']); 
    });
}

public function down(): void
{
    Schema::table('records', function (Blueprint $table) {
        // $table->dropUnique(['user_id', 'appointment_id']); // REMOVE THIS LINE
        $table->dropColumn('appointment_id');
    });
}
};