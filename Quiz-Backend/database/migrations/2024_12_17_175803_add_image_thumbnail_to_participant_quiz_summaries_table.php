<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageThumbnailToParticipantQuizSummariesTable extends Migration
{
    public function up()
    {
        Schema::table('participant_quiz_summaries', function (Blueprint $table) {
            $table->string('image_thumbnail')->nullable();
        });
    }

    public function down()
    {
        Schema::table('participant_quiz_summaries', function (Blueprint $table) {
            $table->dropColumn('image_thumbnail');

        });
    }
}
