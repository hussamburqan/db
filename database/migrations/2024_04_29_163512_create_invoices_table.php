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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->integer('payment_method');
            $table->integer('payment_status');
            $table->integer('time');
            $table->foreignId('patient_id')->constrained();
            $table->foreignId('doctor_id')->constrained();
            $table->foreignId('nclinic_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
