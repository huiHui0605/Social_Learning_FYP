<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    protected $fillable = [
        'content',
        'media_path',
        'media_type',
        'user_id',
        'likes_count',
        'comments_count',
        'shares_count',
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
    ];

    /**
     * Get the user who created the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for this post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class)->latest();
    }

    /**
     * Get the users who liked this post.
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes')->withTimestamps();
    }

    /**
     * Check if a user has liked this post.
     */
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the media URL.
     */
    public function getMediaUrlAttribute()
    {
        if ($this->media_path && Storage::disk('public')->exists($this->media_path)) {
            // Use Laravel route to serve media files instead of direct storage URL
            $url = route('media.serve', ['path' => $this->media_path]);
            // Add cache-busting parameter to prevent browser caching issues
            return $url . '?v=' . $this->updated_at->timestamp;
        }
        return null;
    }

    /**
     * Get the media type icon.
     */
    public function getMediaIconAttribute()
    {
        return $this->media_type === 'video' ? '🎥' : '📷';
    }

    /**
     * Get formatted time ago.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
