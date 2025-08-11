<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Feedback extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'category',
        'priority',
        'status',
        'student_id',
        'lecturer_id',
        'course_id',
        'admin_id',
        'response',
        'responded_at',
        'responded_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($feedback) {
            // Set default type based on the context
            if (!$feedback->type) {
                if ($feedback->student_id && $feedback->lecturer_id) {
                    $feedback->type = 'student_to_lecturer';
                } elseif ($feedback->lecturer_id && $feedback->admin_id) {
                    $feedback->type = 'lecturer_to_admin';
                }
            }
        });
    }

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    /**
     * Get the student who submitted the feedback.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the lecturer who received the feedback.
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Get the course related to the feedback.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the admin who received the feedback.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the user who responded to the feedback.
     */
    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * Get priority badge color.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'blue',
            'high' => 'yellow',
            'urgent' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'in_progress' => 'blue',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get category badge color.
     */
    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'general' => 'blue',
            'course' => 'green',
            'technical' => 'purple',
            'suggestion' => 'indigo',
            'complaint' => 'red',
            'other' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Check if feedback has been responded to.
     */
    public function hasResponse(): bool
    {
        return !is_null($this->response);
    }

    /**
     * Get formatted responded date.
     */
    public function getFormattedRespondedDateAttribute(): string
    {
        return $this->responded_at ? $this->responded_at->format('M d, Y g:i A') : 'Not responded';
    }

    /**
     * Get formatted created date.
     */
    public function getFormattedCreatedDateAttribute(): string
    {
        return $this->created_at->format('M d, Y g:i A');
    }

    /**
     * Check if feedback is overdue (more than 7 days old and still pending).
     */
    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->created_at->diffInDays(now()) > 7;
    }

    /**
     * Escalate priority if feedback is overdue.
     */
    public function escalateIfOverdue(): void
    {
        if ($this->isOverdue() && $this->priority !== 'urgent') {
            $this->update(['priority' => 'urgent']);
        }
    }
}
