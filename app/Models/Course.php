<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $primaryKey = 'course_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'course_code',
        'course_name',
        'instructor',
        'section',
        'credits',
        'room',
        'schedule',
        'days',
        'time',
        'school',
    ];

    public function changeGradeForms()
    {
        return $this->hasMany(CoursesChangeGradeForm::class, 'courses_id_change_grade_form', 'course_id');
    }
}
