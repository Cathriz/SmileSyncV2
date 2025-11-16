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
    Schema::create('appointments', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id'); // ← who created/owns this appointment
    $table->string('patient')->nullable();
    $table->string('doctor');
    $table->date('date');
    $table->time('time');
    $table->string('status');
    $table->string('type');
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});

}

};
