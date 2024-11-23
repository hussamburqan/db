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
            $table->string('name');
            $table->string('education');
            $table->string('photo')->nullable();
            $table->text('bio');
            $table->foreignId('major_id')->constrained()->onDelete('cascade');
            $table->foreignId('n_clinic_id')->constrained('nclinics')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['n_clinic_id']);
            $table->dropColumn('n_clinic_id');
        });
    }
};
