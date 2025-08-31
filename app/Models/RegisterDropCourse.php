<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegisterDropCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'semester',
        'academic_year',
        'reason',
        'status',
        'processed_at',
        'processed_by'
    ];

    protected $casts = [
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
        return $this->belongsTo(Student::class, 'university_id', 'university_id');
    }

    /**
     * Get the user who processed the form.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the registered courses for this form.
     */
    public function registeredCourses()
    {
        return $this->hasMany(FormRegisteredCourse::class, 'form_id');
    }

    /**
     * Get the dropped courses for this form.
     */
    public function droppedCourses()
    {
        return $this->hasMany(FormDroppedCourse::class, 'form_id');
    }

    /**
     * Get only the register courses from the courses array.
     */
    public function getRegisterCoursesAttribute()
    {
        return $this->registeredCourses;
    }

    /**
     * Get only the drop courses from the courses array.
     */
    public function getDropCoursesAttribute()
    {
        return $this->droppedCourses;
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
    public function scopeByStudent($query, $universityId)
    {
        return $query->where('university_id', $universityId);
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
