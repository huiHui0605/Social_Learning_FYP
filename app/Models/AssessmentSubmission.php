<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AssessmentSubmission extends Model
{
    protected $fillable = [
        'assessment_id',
        'student_id',
        'submission_content',
        'attachment_path',
        'marks_obtained',
        'feedback',
        'status',
        'submitted_at',
        'graded_at',
    ];

    protected $casts = [
        'marks_obtained' => 'integer',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    /**
     * Get the assessment that owns the submission.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the student who made the submission.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Check if submission is late.
     */
    public function isLate(): bool
    {
        return $this->submitted_at->isAfter($this->assessment->due_date);
    }

    /**
     * Get the percentage score.
     */
    public function getPercentageAttribute(): float
    {
        if ($this->marks_obtained === null || $this->assessment->total_marks === 0) {
            return 0;
        }
        return round(($this->marks_obtained / $this->assessment->total_marks) * 100, 1);
    }

    /**
     * Get formatted submitted date.
     */
    public function getFormattedSubmittedDateAttribute(): string
    {
        return $this->submitted_at->format('M d, Y g:i A');
    }

    /**
     * Get formatted graded date.
     */
    public function getFormattedGradedDateAttribute(): string
    {
        return $this->graded_at ? $this->graded_at->format('M d, Y g:i A') : 'Not graded';
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'submitted' => 'blue',
            'graded' => 'green',
            'late' => 'red',
            default => 'gray',
        };
    }
}
