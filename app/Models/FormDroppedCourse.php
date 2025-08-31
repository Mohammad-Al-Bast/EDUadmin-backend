<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormDroppedCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'course_id',
        'course_code',
        'course_name',
        'section',
        'instructor',
        'credits',
        'room',
        'schedule',
        'days',
        'time',
        'school',
    ];

    protected $casts = [
        'form_id' => 'integer',
        'course_id' => 'integer',
        'credits' => 'integer',
    ];

    /**
     * Get the form that owns this dropped course.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(RegisterDropCourse::class, 'form_id');
    }

    /**
     * Get the course details.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }
}
