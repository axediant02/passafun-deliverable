<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantAnswersTable extends Migration
{
    public function up()
    {
        Schema::create('participant_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('participants');
            $table->foreignId('quiz_id')->constrained('quizzes');
            $table->foreignId('question_id')->constrained('quiz_questions');
            $table->foreignId('choice_id')->nullable()->references('id')->on('choices');
            $table->text('answer_text')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('participant_answers');
    }
}
