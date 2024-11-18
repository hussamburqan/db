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
        Schema::create('nclinics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('description');
            $table->string('start_date');
            $table->string('end_date');
            $table->string('status');
            $table->string('email');
            $table->integer('phone');
            $table->foreignId('major_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('patient_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nclincs');
    }
};
