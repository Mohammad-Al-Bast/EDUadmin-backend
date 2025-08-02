<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeGradeForm extends Model
{
    use HasFactory;
    protected $table = 'change_grade_form';
    protected $fillable = [
        'student_id', 'student_full_name', 'semester_year', 'major', 'campus', 'instructor_name', 'course_code', 'course_name', 'section', 'reason_for_change', 'copy_of_original_grading_report', 'copy_of_graded_final_exam', 'tuition_report', 'copy_of_first_ten_pages_final_report', 'course_grade_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function courseGrade()
    {
        return $this->belongsTo(CoursesChangeGradeForm::class, 'course_grade_id', 'course_grade_id');
    }
}
