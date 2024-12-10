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
           $table->string('invoice_number')->unique();
           $table->decimal('amount', 10, 2);
           $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'insurance']);
           $table->enum('payment_status', ['pending', 'paid', 'cancelled', 'refunded']);
           $table->timestamp('paid_at')->nullable();
           $table->text('notes')->nullable(); 
           $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
           $table->foreignId('nclinic_id')->constrained('nclinics');
           $table->timestamps();
       });
   }

   public function down()
   {
       Schema::dropIfExists('invoices');
   }
};