<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class GroupChat extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image_path',
        'created_by',
        'type',
        'max_members',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_members' => 'integer',
    ];

    /**
     * Get the creator of the group.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the members of this group.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_chat_id', 'user_id')
                    ->withPivot('role', 'is_active', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Get the group memberships.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Get the messages in this group.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(GroupMessage::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the group image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }
        
        // Return a placeholder image
        return 'https://via.placeholder.com/100x100?text=' . urlencode(substr($this->name, 0, 1));
    }

    /**
     * Check if user is a member of this group.
     */
    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->where('group_members.is_active', true)->exists();
    }

    /**
     * Check if user is an admin of this group.
     */
    public function isAdmin(User $user): bool
    {
        return $this->memberships()
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if user is a moderator of this group.
     */
    public function isModerator(User $user): bool
    {
        return $this->memberships()
            ->where('user_id', $user->id)
            ->whereIn('role', ['admin', 'moderator'])
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get member count.
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->where('group_members.is_active', true)->count();
    }

    /**
     * Check if group is full.
     */
    public function isFull(): bool
    {
        return $this->member_count >= $this->max_members;
    }

    /**
     * Add a member to the group.
     */
    public function addMember(User $user, string $role = 'member'): void
    {
        if (!$this->isMember($user) && !$this->isFull()) {
            $this->members()->attach($user->id, [
                'role' => $role,
                'is_active' => true,
                'joined_at' => now(),
            ]);
        }
    }

    /**
     * Remove a member from the group.
     */
    public function removeMember(User $user): void
    {
        $this->members()->detach($user->id);
    }

    /**
     * Update member role.
     */
    public function updateMemberRole(User $user, string $role): void
    {
        $this->memberships()
            ->where('user_id', $user->id)
            ->update(['role' => $role]);
    }
}
