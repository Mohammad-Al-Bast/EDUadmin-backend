<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegisterDropCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'semester',
        'academic_year',
        'courses',
        'reason',
        'status',
        'processed_at',
        'processed_by'
    ];

    protected $casts = [
        'courses' => 'array',
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     */
    protected $appends = ['total_register_courses', 'total_drop_courses'];

    /**
     * Get the student that owns the form.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Get the user who processed the form.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get only the register courses from the courses array.
     */
    public function getRegisterCoursesAttribute()
    {
        if (!$this->courses) {
            return collect();
        }

        return collect($this->courses)->filter(function ($course) {
            return $course['action'] === 'register';
        });
    }

    /**
     * Get only the drop courses from the courses array.
     */
    public function getDropCoursesAttribute()
    {
        if (!$this->courses) {
            return collect();
        }

        return collect($this->courses)->filter(function ($course) {
            return $course['action'] === 'drop';
        });
    }

    /**
     * Get the total count of register courses.
     */
    public function getTotalRegisterCoursesAttribute()
    {
        return $this->register_courses->count();
    }

    /**
     * Get the total count of drop courses.
     */
    public function getTotalDropCoursesAttribute()
    {
        return $this->drop_courses->count();
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by semester.
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope to filter by academic year.
     */
    public function scopeByAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    /**
     * Scope to filter by student.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Check if the form can be edited.
     */
    public function canBeEdited(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the form can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Mark the form as approved.
     */
    public function approve($processedBy = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'processed_at' => now(),
            'processed_by' => $processedBy,
        ]);

        return true;
    }

    /**
     * Mark the form as rejected.
     */
    public function reject($processedBy = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'processed_by' => $processedBy,
        ]);

        return true;
    }
}
