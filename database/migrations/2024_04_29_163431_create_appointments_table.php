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
            $table->text('date');
            $table->integer('time');
            $table->string('description');
            $table->string('status');
            $table->string('medication');
            $table->integer('price');
            $table->double('quantity');
            $table->string('instructions');
            $table->foreignId('patient_id')->constrained();
            $table->foreignId('doctor_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointsments');
    }
};
