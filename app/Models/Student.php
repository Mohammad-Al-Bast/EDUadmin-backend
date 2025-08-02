<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $primaryKey = 'student_id';
    protected $fillable = [
        'student_name', 'campus', 'school', 'major', 'semester', 'year', 'registered_courses_id',
    ];

    public function registeredCourse()
    {
        return $this->belongsTo(CoursesChangeGradeForm::class, 'registered_courses_id', 'course_grade_id');
    }
}
