<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('thumbnail_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->nullable();
            $table->string('prefix_text')->nullable();
            $table->string('prefix_text_color')->nullable();
            $table->string('header_text_color')->nullable();
            $table->string('description_text_color')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_color')->nullable();
            $table->string('button_text_color')->nullable();
            $table->enum('background_type', ['color', 'image']);
            $table->string('background_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thumbnail_customizations');
    }
};