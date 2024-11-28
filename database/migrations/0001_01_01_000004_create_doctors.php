<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->integer('experience_years');
            $table->string('specialization');
            $table->string('education');
            $table->string('photo')->nullable();
            $table->time('start_work_time');
            $table->time('end_work_time');            
            $table->integer('default_time_reservations');
            $table->text('bio');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('major_id')->constrained()->onDelete('cascade');
            $table->foreignId('nclinic_id')->constrained('nclinics')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['nclinic_id']);
            $table->dropColumn('nclinic_id');
        });
    }
};
