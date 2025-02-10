<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantQuizzesTable extends Migration
{
    public function up()
    {
        Schema::create('participant_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('participants');
            $table->foreignId('quiz_id')->constrained('quizzes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('participant_quizzes');
    }
}
