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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('address');
            $table->integer('age');
            $table->string('email');
            $table->string('blood_type');
            $table->string('gender');
            $table->string('disease_type');
            $table->text('medical_history');
            $table->text('medical_recommendations');
            //$table->foreignId('nclinic_id')->constrained();
            //$table->foreignId('user_id')->constrained();
            //$table->foreignId('doctor_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
