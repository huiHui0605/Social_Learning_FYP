<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'course_code',
        'title',
        'description',
        'lecturer_id',
        'semester',
        'academic_year',
        'is_active',
        'image_path'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the lecturer that owns the course.
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Get the students enrolled in this course.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_enrollments', 'course_id', 'student_id')
                    ->withPivot('status', 'enrolled_at')
                    ->withTimestamps();
    }

    /**
     * Get the enrollments for this course.
     */
    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * Get the materials for this course.
     */
    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class);
    }

    /**
     * Get the assessments for this course.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * Get the posts for this course.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(\App\Models\Post::class);
    }

    /**
     * Generate a unique course code.
     */
    public static function generateCourseCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 6));
        } while (self::where('course_code', $code)->exists());

        return $code;
    }

    /**
     * Get the course image URL.
     */
    public function getImageUrlAttribute()
    {
        // If no image path is set, return placeholder
        if (!$this->image_path) {
            return 'https://via.placeholder.com/400x200?text=' . urlencode($this->title);
        }
        
        // Check if file exists in storage
        if (Storage::disk('public')->exists($this->image_path)) {
            return asset('storage/' . $this->image_path);
        }
        
        // If file doesn't exist but path is set, log for debugging
        \Log::warning('Course image not found', [
            'course_id' => $this->id,
            'course_title' => $this->title,
            'image_path' => $this->image_path,
            'full_path' => storage_path('app/public/' . $this->image_path)
        ]);
        
        // Return placeholder if file doesn't exist
        return 'https://via.placeholder.com/400x200?text=' . urlencode($this->title);
    }

    /**
     * Get available semester options.
     */
    public static function getSemesterOptions()
    {
        return [
            'Semester 1' => 'Semester 1',
            'Semester 2' => 'Semester 2',
            'Semester 3' => 'Semester 3',
        ];
    }

    /**
     * Check if course has available enrollment slots.
     */
    public function hasAvailableSlots(): bool
    {
        // Default limit of 50 students per course
        $maxStudents = 50;
        $currentEnrollments = $this->enrollments()->where('is_active', true)->count();
        
        return $currentEnrollments < $maxStudents;
    }

    /**
     * Get current enrollment count.
     */
    public function getCurrentEnrollmentCountAttribute(): int
    {
        return $this->enrollments()->where('is_active', true)->count();
    }

    /**
     * Check if course has a valid image.
     */
    public function hasImage(): bool
    {
        return $this->image_path && Storage::disk('public')->exists($this->image_path);
    }

    /**
     * Get image file size in human readable format.
     */
    public function getImageSizeAttribute(): string
    {
        if (!$this->hasImage()) {
            return 'No image';
        }
        
        $size = Storage::disk('public')->size($this->image_path);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Delete the course image when the course is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($course) {
            if ($course->image_path) {
                Storage::disk('public')->delete($course->image_path);
            }
        });
    }
}
