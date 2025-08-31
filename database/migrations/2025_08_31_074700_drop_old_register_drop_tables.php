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
        // Drop old tables related to the previous implementation
        Schema::dropIfExists('register_courses');
        Schema::dropIfExists('drop_courses');
        Schema::dropIfExists('register_drop_courses_form');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We don't recreate the old tables as they are obsolete
        // If you need to rollback, restore from backup
    }
};
