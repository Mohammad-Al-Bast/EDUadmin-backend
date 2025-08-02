<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('change_grade_form', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('student_full_name');
            $table->string('semester_year');
            $table->string('major');
            $table->string('campus');
            $table->string('instructor_name');
            $table->string('course_code');
            $table->string('course_name');
            $table->string('section');
            $table->text('reason_for_change');
            $table->text('copy_of_original_grading_report')->nullable();
            $table->text('copy_of_graded_final_exam')->nullable();
            $table->text('tuition_report')->nullable();
            $table->text('copy_of_first_ten_pages_final_report')->nullable();
            $table->unsignedBigInteger('course_grade_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('course_grade_id')->references('course_grade_id')->on('courses_change_grade_form')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_grade_form');
    }
};
