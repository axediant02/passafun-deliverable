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
        Schema::table('json_animations', function (Blueprint $table) {
            // Add new columns
            $table->string('filename')->nullable()->after('id');
            $table->string('filepath')->nullable()->after('filename'); 

            // Drop old columns
            if (Schema::hasColumn('json_animations', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('json_animations', 'json_data')) {
                $table->dropColumn('json_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('json_animations', function (Blueprint $table) {
            // Add old columns back
            $table->string('name')->nullable()->after('id');
            $table->json('json_data')->nullable()->after('name'); // Assuming it was a JSON column

            // Drop the new columns
            if (Schema::hasColumn('json_animations', 'filename')) {
                $table->dropColumn('filename');
            }
            if (Schema::hasColumn('json_animations', 'filepath')) {
                $table->dropColumn('filepath');
            }
        });
    }
};
