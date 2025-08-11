<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CourseMaterial extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'file_name',
        'course_id',
        'uploaded_by',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    /**
     * Get the course that owns the material.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lecturer who uploaded the material.
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->url($this->file_path);
        }
        return null;
    }

    /**
     * Get file size in human readable format.
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Delete the file when the material is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($material) {
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
        });
    }
}
