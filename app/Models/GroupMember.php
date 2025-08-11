<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMember extends Model
{
    protected $fillable = [
        'group_chat_id',
        'user_id',
        'role',
        'is_active',
        'joined_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'joined_at' => 'datetime',
    ];

    /**
     * Get the group chat this membership belongs to.
     */
    public function groupChat(): BelongsTo
    {
        return $this->belongsTo(GroupChat::class);
    }

    /**
     * Get the user this membership belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if member is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if member is a moderator.
     */
    public function isModerator(): bool
    {
        return in_array($this->role, ['admin', 'moderator']);
    }

    /**
     * Check if member is a regular member.
     */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }
}
