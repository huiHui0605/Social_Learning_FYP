<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'course_id',
        'student_id',
        'status',
        'enrolled_at',
        'is_active'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the course for this enrollment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the student for this enrollment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get available status options.
     */
    public static function getStatusOptions()
    {
        return [
            'enrolled' => 'Enrolled',
            'completed' => 'Completed',
            'dropped' => 'Dropped',
        ];
    }

    /**
     * Check if enrollment is active.
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->status === 'enrolled';
    }

    /**
     * Get enrollment duration in days.
     */
    public function getEnrollmentDurationAttribute(): int
    {
        return $this->enrolled_at->diffInDays(now());
    }
}
