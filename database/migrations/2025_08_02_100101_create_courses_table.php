<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id('course_id');
            $table->string('course_code');
            $table->string('course_name');
            $table->string('instructor');
            $table->string('section');
            $table->integer('credits');
            $table->string('room');
            $table->string('schedule');
            $table->string('days');
            $table->string('time');
            $table->string('school');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
