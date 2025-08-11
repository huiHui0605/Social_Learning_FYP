<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // ✅ Added to allow mass assignment of role
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the courses taught by this lecturer.
     */
    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'lecturer_id');
    }

    /**
     * Get the courses this student is enrolled in.
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_enrollments', 'student_id', 'course_id')
                    ->withPivot('status', 'enrolled_at')
                    ->withTimestamps();
    }

    /**
     * Get the courses this student is enrolled in (alias for enrolledCourses).
     */
    public function courses(): BelongsToMany
    {
        return $this->enrolledCourses();
    }

    /**
     * Check if user is a lecturer.
     */
    public function isLecturer(): bool
    {
        return $this->role === 'lecturer';
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Get the posts created by this user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the comments made by this user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    /**
     * Get the posts liked by this user.
     */
    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes')->withTimestamps();
    }

    /**
     * Get the assessments created by this lecturer.
     */
    public function createdAssessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'lecturer_id');
    }

    /**
     * Get the assessment submissions made by this student.
     */
    public function assessmentSubmissions(): HasMany
    {
        return $this->hasMany(AssessmentSubmission::class, 'student_id');
    }

    /**
     * Get feedback submitted by this student.
     */
    public function submittedFeedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'student_id');
    }

    /**
     * Get feedback received by this lecturer.
     */
    public function receivedFeedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'lecturer_id');
    }

    /**
     * Get feedback submitted by this lecturer to admin.
     */
    public function lecturerFeedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'lecturer_id')->where('type', 'lecturer_to_admin');
    }

    /**
     * Get feedback received by this admin.
     */
    public function adminFeedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'admin_id');
    }

    /**
     * Get feedback responses made by this user.
     */
    public function feedbackResponses(): HasMany
    {
        return $this->hasMany(Feedback::class, 'responded_by');
    }

    /**
     * Get messages sent by this user.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get messages received by this user.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get unread messages count.
     */
    public function getUnreadMessagesCountAttribute(): int
    {
        return $this->receivedMessages()->where('is_read', false)->count();
    }

    /**
     * Get conversation partners (users with whom this user has exchanged messages).
     */
    public function getConversationPartners()
    {
        $sentUserIds = $this->sentMessages()->pluck('receiver_id');
        $receivedUserIds = $this->receivedMessages()->pluck('sender_id');
        
        return User::whereIn('id', $sentUserIds->merge($receivedUserIds))
            ->where('id', '!=', $this->id)
            ->distinct()
            ->get();
    }

    /**
     * Get group chats created by this user.
     */
    public function createdGroupChats(): HasMany
    {
        return $this->hasMany(GroupChat::class, 'created_by');
    }

    /**
     * Get group chats this user is a member of.
     */
    public function groupChats(): BelongsToMany
    {
        return $this->belongsToMany(GroupChat::class, 'group_members', 'user_id', 'group_chat_id')
                    ->withPivot('role', 'is_active', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Get group memberships.
     */
    public function groupMemberships(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Get group messages sent by this user.
     */
    public function groupMessages(): HasMany
    {
        return $this->hasMany(GroupMessage::class, 'sender_id');
    }

    /**
     * Check if user is currently online.
     */
    public function isOnline(): bool
    {
        // Consider user online if they've been active in the last 5 minutes
        return $this->last_seen_at && $this->last_seen_at->diffInMinutes(now()) < 5;
    }

    /**
     * Update user's last seen timestamp.
     */
    public function updateLastSeen(): void
    {
        $this->update(['last_seen_at' => now()]);
    }

    /**
     * Get online users.
     */
    public static function getOnlineUsers()
    {
        return static::where('last_seen_at', '>=', now()->subMinutes(5))
            ->where('id', '!=', auth()->id())
            ->orderBy('last_seen_at', 'desc')
            ->get();
    }
}
