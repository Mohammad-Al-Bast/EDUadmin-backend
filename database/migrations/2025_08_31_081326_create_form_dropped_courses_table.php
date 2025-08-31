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
        Schema::create('form_dropped_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id'); // Links to register_drop_courses table
            $table->unsignedBigInteger('course_id'); // Links to courses table
            $table->string('course_code');
            $table->string('course_name');
            $table->string('section');
            $table->string('instructor');
            $table->integer('credits');
            $table->string('room')->nullable();
            $table->string('schedule')->nullable();
            $table->string('days')->nullable();
            $table->string('time')->nullable();
            $table->string('school')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('form_id')->references('id')->on('register_drop_courses')->onDelete('cascade');
            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');

            // Indexes
            $table->index(['form_id', 'course_id']);
            $table->index('course_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_dropped_courses');
    }
};
