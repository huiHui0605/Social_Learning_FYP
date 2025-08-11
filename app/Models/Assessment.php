<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Assessment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'course_id',
        'lecturer_id',
        'due_date',
        'total_marks',
        'status',
        'file_path',
    ];

    protected $casts = [
        'due_date' => 'date',
        'total_marks' => 'integer',
    ];

    /**
     * Get the course that owns the assessment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lecturer who created the assessment.
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Get the submissions for this assessment.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(AssessmentSubmission::class);
    }

    /**
     * Check if assessment is overdue.
     */
    public function isOverdue(): bool
    {
        return Carbon::now()->isAfter($this->due_date);
    }

    /**
     * Get the number of submissions.
     */
    public function getSubmissionsCountAttribute(): int
    {
        return $this->submissions()->count();
    }

    /**
     * Get the number of graded submissions.
     */
    public function getGradedCountAttribute(): int
    {
        return $this->submissions()->where('status', 'graded')->count();
    }

    /**
     * Get formatted due date.
     */
    public function getFormattedDueDateAttribute(): string
    {
        return $this->due_date->format('M d, Y');
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'published' => 'blue',
            'closed' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }
        
        return asset('storage/' . $this->file_path);
    }

    /**
     * Check if assessment has a file.
     */
    public function hasFile(): bool
    {
        return !empty($this->file_path);
    }
}
