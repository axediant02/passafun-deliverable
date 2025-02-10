<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('json_animations', function (Blueprint $table) {
            $table->json('json_data')->nullable()->after('name');
            $table->dropColumn('path'); 
        });
    }

    public function down(): void
    {
        Schema::table('json_animations', function (Blueprint $table) {
            $table->string('path')->nullable()->after('name'); 
            $table->dropColumn('json_data'); 
        });
    }
};
