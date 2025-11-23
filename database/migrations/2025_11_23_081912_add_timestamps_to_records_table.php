<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('records', function (Blueprint $table) {
        // These lines use Laravel's shorthand method to add both timestamps
        $table->timestamps(); 
    });
}

public function down(): void
{
    Schema::table('records', function (Blueprint $table) {
        $table->dropTimestamps();
    });
}
};
