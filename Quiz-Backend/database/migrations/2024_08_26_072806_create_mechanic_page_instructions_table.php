<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMechanicPageInstructionsTable extends Migration
{
    public function up()
    {
        Schema::create('mechanic_page_instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mechanic_page_id')->constrained('mechanic_page');
            $table->foreignId('instruction_id')->constrained('mechanic_instructions');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mechanic_page_instructions');
    }
}
