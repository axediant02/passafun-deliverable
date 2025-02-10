<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMechanicInstructionsTable extends Migration
{
    public function up()
    {
        Schema::create('mechanic_instructions', function (Blueprint $table) {
            $table->id();
            $table->string('instruction')->nullable(); 
            $table->string('icon')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mechanic_instructions');
    }
}
