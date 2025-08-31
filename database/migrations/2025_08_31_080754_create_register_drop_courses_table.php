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
        Schema::create('register_drop_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('semester', 50);
            $table->string('academic_year', 20);
            $table->json('courses'); // Store array of course objects with action (register/drop)
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for better performance
            $table->index(['student_id', 'status']);
            $table->index(['semester', 'academic_year']);
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_drop_courses');
    }
};
