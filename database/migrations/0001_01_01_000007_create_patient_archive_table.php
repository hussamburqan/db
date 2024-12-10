<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patient_archives', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('description');
            $table->string('status');
            $table->text('instructions');
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_archives');
    }
};