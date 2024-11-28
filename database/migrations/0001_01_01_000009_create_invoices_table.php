<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_archive_id');
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method');
            $table->string('payment_status');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('nclinic_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->foreign('patient_archive_id')
            ->references('id')
            ->on('patient_archives')
            ->onDelete('cascade'); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};