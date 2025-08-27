<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeGradeForm extends Model
{
    use HasFactory;

    protected $table = 'change_grade_form';

    protected $fillable = [
        'student_id',
        'student_full_name',
        'semester_year',
        'major',
        'campus',
        'instructor_name',
        'course_code',
        'course_name',
        'section',
        'reason_for_change',
        'copy_of_original_grading_report',
        'copy_of_graded_final_exam',
        'tuition_report',
        'copy_of_first_ten_pages_final_report',
        'course_grade_id',
    ];

    protected $hidden = [
        'student_id', // Hide the internal student_id from JSON responses
    ];

    protected $appends = [
        'university_id', // Add university_id as an appended attribute
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function courseGrade()
    {
        return $this->belongsTo(CoursesChangeGradeForm::class, 'course_grade_id', 'course_grade_id');
    }

    // Accessor to get university_id from the related student
    public function getUniversityIdAttribute()
    {
        return $this->student ? $this->student->university_id : null;
    }

    /**
     * Get the report URL for this form
     */
    public function getReportUrlAttribute()
    {
        return route('api.change-grade-forms.report', $this->id);
    }
}
