<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('register_drop_courses', function (Blueprint $table) {
            // Drop existing foreign key and index
            $table->dropForeign(['student_id']);
            $table->dropIndex(['student_id', 'status']);

            // Add university_id column
            $table->unsignedBigInteger('university_id')->after('id');

            // Create foreign key reference to students table using university_id
            $table->foreign('university_id')->references('university_id')->on('students')->onDelete('cascade');

            // Add new index
            $table->index(['university_id', 'status']);
        });

        // Migrate existing data from student_id to university_id (PostgreSQL syntax)
        DB::statement('
            UPDATE register_drop_courses 
            SET university_id = s.university_id
            FROM students s 
            WHERE register_drop_courses.student_id = s.student_id
        ');

        // Drop the old student_id column
        Schema::table('register_drop_courses', function (Blueprint $table) {
            $table->dropColumn('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('register_drop_courses', function (Blueprint $table) {
            // Add back student_id column
            $table->unsignedBigInteger('student_id')->after('id');

            // Drop university_id foreign key and index
            $table->dropForeign(['university_id']);
            $table->dropIndex(['university_id', 'status']);
        });

        // Migrate data back from university_id to student_id (PostgreSQL syntax)
        DB::statement('
            UPDATE register_drop_courses 
            SET student_id = s.student_id
            FROM students s 
            WHERE register_drop_courses.university_id = s.university_id
        ');

        // Complete the rollback
        Schema::table('register_drop_courses', function (Blueprint $table) {
            // Recreate foreign key and index for student_id
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->index(['student_id', 'status']);

            // Drop university_id column
            $table->dropColumn('university_id');
        });
    }
};
