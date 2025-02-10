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
        Schema::table('get_result_pages', function (Blueprint $table) {
            $table->foreignId('json_animation_id')->nullable();
            $table->foreignId('image_type_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('get_result_pages', function (Blueprint $table) {
            //
        });
    }
};
