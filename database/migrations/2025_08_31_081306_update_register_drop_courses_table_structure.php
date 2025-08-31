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
        Schema::table('register_drop_courses', function (Blueprint $table) {
            // Remove the JSON courses field since we'll use separate tables
            $table->dropColumn('courses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('register_drop_courses', function (Blueprint $table) {
            // Add back the JSON courses field
            $table->json('courses')->after('academic_year');
        });
    }
};
