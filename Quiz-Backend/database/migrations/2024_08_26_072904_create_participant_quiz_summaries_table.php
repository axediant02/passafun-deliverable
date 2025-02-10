<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantQuizSummariesTable extends Migration
{
    public function up()
    {
        Schema::create('participant_quiz_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id');
            $table->foreignId('quiz_id');
            $table->integer('score');
            $table->foreignId('result_id');
            $table->string('unique_result_id');
            $table->timestamp('completed_at');
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('participant_quiz_summaries');
    }
}
