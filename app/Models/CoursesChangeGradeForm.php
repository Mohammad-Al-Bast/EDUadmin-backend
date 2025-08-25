<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursesChangeGradeForm extends Model
{
    use HasFactory;
    protected $table = 'courses_change_grade_form';
    protected $primaryKey = 'course_grade_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'courses_id_change_grade_form',
        'grade_type',
        'grade_percentage',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'courses_id_change_grade_form', 'course_id');
    }
}
