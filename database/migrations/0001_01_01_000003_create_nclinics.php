<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nclinics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('photo')->nullable();
            $table->text('description');
            $table->time('opening_time');
            $table->time('closing_time');
            $table->string('status');
            $table->string('email');
            $table->string('phone');
            $table->foreignId('major_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nclinics');
    }
};
