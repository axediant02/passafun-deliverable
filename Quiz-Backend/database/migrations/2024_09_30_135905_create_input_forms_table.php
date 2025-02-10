<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInputFormsTable extends Migration
{
    public function up(): void
    {
        Schema::create('input_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('get_result_page_id')->constrained('get_result_pages')->onDelete('cascade');
            $table->string('type')->nullable(); 
            $table->string('label')->nullable();
            $table->boolean('is_required')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('input_forms');
    }
}
