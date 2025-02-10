<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesTable extends Migration
{
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); 
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->foreignId('quiz_status_id')->nullable();
            $table->foreignId('theme_id')->nullable();
            $table->foreignId('landing_page_id')->nullable();
            $table->foreignId('mechanic_page_id')->nullable();
            $table->foreignId('admin_id')->nullable();
            $table->string('uid')->unique()->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quizzes');
    }
}
