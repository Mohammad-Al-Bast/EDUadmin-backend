<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id('student_id');
            $table->string('student_name');
            $table->string('campus');
            $table->string('school');
            $table->string('major');
            $table->string('semester');
            $table->integer('year');
            $table->unsignedBigInteger('registered_courses_id')->nullable();
            $table->timestamps();

            // Foreign key to courses_change_grade_form
            $table->foreign('registered_courses_id')->references('course_grade_id')->on('courses_change_grade_form')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
