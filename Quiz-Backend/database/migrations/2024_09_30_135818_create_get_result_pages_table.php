
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGetResultPagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('get_result_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->string('header')->nullable();
            $table->string('background_image')->nullable();
            $table->string('get_result_page_image')->nullable();
            $table->string('button_text')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('get_result_pages');
    }
}


