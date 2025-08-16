<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'student_id';
    
    protected $fillable = [
        'student_name', 'university_id', 'campus', 'school', 'major', 'semester', 'year', 'registered_courses_id',
    ];

    protected $casts = [
        'university_id' => 'integer',
        'year' => 'integer',
        'registered_courses_id' => 'integer',
    ];

    protected $hidden = [
        'student_id', // Hide the internal student_id from JSON responses
    ];

    public function getRouteKeyName()
    {
        return 'university_id'; // Use university_id for route model binding
    }

    public function registeredCourse()
    {
        return $this->belongsTo(CoursesChangeGradeForm::class, 'registered_courses_id', 'course_grade_id');
    }
}
