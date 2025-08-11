<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Message extends Model
{
    protected $fillable = [
        'content',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'message_type',
        'sender_id',
        'receiver_id',
        'is_read',
        'read_at',
        'related_post_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the sender of the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Check if message is a file upload.
     */
    public function isFile(): bool
    {
        return $this->message_type === 'file';
    }

    /**
     * Check if message is a photo.
     */
    public function isPhoto(): bool
    {
        return $this->message_type === 'photo';
    }

    /**
     * Check if message is a video.
     */
    public function isVideo(): bool
    {
        return $this->message_type === 'video';
    }

    /**
     * Check if message is text only.
     */
    public function isText(): bool
    {
        return $this->message_type === 'text';
    }

    /**
     * Check if message is a shared post.
     */
    public function isSharedPost(): bool
    {
        return $this->message_type === 'shared_post';
    }

    /**
     * Get file extension.
     */
    public function getFileExtensionAttribute(): string
    {
        if (!$this->file_name) return '';
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return '';
        
        $size = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Get file icon based on type.
     */
    public function getFileIconAttribute(): string
    {
        if (!$this->file_type) return '📄';
        
        $type = strtolower($this->file_type);
        
        if (str_contains($type, 'image')) return '🖼️';
        if (str_contains($type, 'video')) return '🎥';
        if (str_contains($type, 'pdf')) return '📕';
        if (str_contains($type, 'word') || str_contains($type, 'document')) return '📘';
        if (str_contains($type, 'excel') || str_contains($type, 'spreadsheet')) return '📗';
        if (str_contains($type, 'powerpoint') || str_contains($type, 'presentation')) return '📙';
        if (str_contains($type, 'zip') || str_contains($type, 'rar')) return '📦';
        if (str_contains($type, 'audio')) return '🎵';
        
        return '📄';
    }

    /**
     * Get formatted time.
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('g:i A');
    }

    /**
     * Get formatted date.
     */
    public function getFormattedDateAttribute(): string
    {
        $now = Carbon::now();
        $messageDate = $this->created_at;
        
        if ($messageDate->isToday()) {
            return 'Today';
        } elseif ($messageDate->isYesterday()) {
            return 'Yesterday';
        } elseif ($messageDate->diffInDays($now) < 7) {
            return $messageDate->format('l');
        } else {
            return $messageDate->format('M d, Y');
        }
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}
