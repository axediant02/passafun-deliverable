<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMechanicPageTable extends Migration
{
    public function up()
    {
        Schema::create('mechanic_page', function (Blueprint $table) {
            $table->id();
            $table->string('background_image')->nullable(); 
            $table->string('header')->nullable();
            $table->string('button_text')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mechanic_page');
    }
}
