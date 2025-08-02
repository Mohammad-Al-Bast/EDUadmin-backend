<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses_change_grade_form', function (Blueprint $table) {
            $table->id('course_grade_id');
            $table->unsignedBigInteger('courses_id_change_grade_form');
            $table->string('grade_type');
            $table->decimal('grade_percentage', 5, 2);
            $table->timestamps();

            // Foreign key to courses table
            $table->foreign('courses_id_change_grade_form')->references('course_id')->on('courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses_change_grade_form');
    }
};
