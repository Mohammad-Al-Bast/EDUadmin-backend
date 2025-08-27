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
        Schema::table('courses_change_grade_form', function (Blueprint $table) {
            $table->decimal('quizzes_score', 5, 2)->nullable()->after('requested_grade');
            $table->decimal('tests_score', 5, 2)->nullable()->after('quizzes_score');
            $table->decimal('midterm_score', 5, 2)->nullable()->after('tests_score');
            $table->decimal('final_exam_score', 5, 2)->nullable()->after('midterm_score');
            $table->decimal('curve_adjustment', 5, 2)->default(0)->after('final_exam_score');
            $table->decimal('final_numeric_grade', 5, 2)->nullable()->after('curve_adjustment');
            $table->string('final_letter_grade', 2)->nullable()->after('final_numeric_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses_change_grade_form', function (Blueprint $table) {
            $table->dropColumn([
                'quizzes_score',
                'tests_score',
                'midterm_score',
                'final_exam_score',
                'curve_adjustment',
                'final_numeric_grade',
                'final_letter_grade'
            ]);
        });
    }
};
