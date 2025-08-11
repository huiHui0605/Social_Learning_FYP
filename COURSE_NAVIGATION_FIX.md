# Course Navigation Fix - Assessment Flow

## Problem Identified

When navigating from a course's assignment tab to view submissions, then clicking "Back to Assessment", the system was redirecting to a different course instead of staying within the current course context.

### Root Cause
The navigation flow was broken because:
1. From course view → "View Submissions" → submissions view
2. From submissions view → "Back to Assessment" → assessment show view  
3. From assessment show view → "Back to Assessments" → general assessment list (wrong!)

The issue was that the navigation wasn't context-aware - it didn't remember which course the user came from.

## Solution Implemented

### 1. Context-Aware Navigation Parameters
Added `return_to_course` parameter to track the course context throughout the navigation flow:

- **Course View → Submissions**: `?return_to_course={{ $course->id }}`
- **Submissions → Assessment**: Preserves the return parameter
- **Assessment → Course**: Uses the return parameter to go back to the correct course

### 2. Updated Navigation Links

#### Course Show View (`lecturer/courses/show.blade.php`)
```php
// Before
<a href="{{ route('L.assessment.submissions', $assessment) }}">View Submissions</a>

// After  
<a href="{{ route('L.assessment.submissions', $assessment) }}?return_to_course={{ $course->id }}">View Submissions</a>
```

#### Submissions View (`lecturer/assessments/submissions.blade.php`)
```php
// Before
<a href="{{ route('L.assessment.show', $assessment) }}">← Back to Assessment</a>

// After
@if(request()->has('return_to_course'))
    <a href="{{ route('lecturer.courses.show', request()->get('return_to_course')) }}?tab=assignments">← Back to Course</a>
@else
    <a href="{{ route('L.assessment.show', $assessment) }}">← Back to Assessment</a>
@endif
```

#### Assessment Show View (`lecturer/assessments/show.blade.php`)
```php
// Before
<a href="{{ route('L.assessment.index') }}">← Back to Assessments</a>

// After
@if(request()->has('return_to_course'))
    <a href="{{ route('lecturer.courses.show', request()->get('return_to_course')) }}?tab=assignments">← Back to Course</a>
@else
    <a href="{{ route('L.assessment.index') }}">← Back to Assessments</a>
@endif
```

### 3. Updated Controller Methods

#### AssessmentController Updates
- **store()**: Redirects back to course if created from course context
- **update()**: Handles return_to_course parameter
- **publish()**: Redirects back to course if published from course context
- **close()**: Redirects back to course if closed from course context
- **gradeSubmission()**: Preserves return_to_course parameter

### 4. Form Updates
Added hidden input fields to preserve context in forms:

```php
@if(request()->has('return_to_course'))
    <input type="hidden" name="return_to_course" value="{{ request()->get('return_to_course') }}">
@endif
```

### 5. Evaluate Submission Flow
Updated the evaluate submission view to preserve the return parameter:

```php
@if(request()->has('return_to_course'))
    <a href="{{ route('L.assessment.submissions', $submission->assessment) }}?return_to_course={{ request()->get('return_to_course') }}">← Back to Submissions</a>
@else
    <a href="{{ route('L.assessment.submissions', $submission->assessment) }}">← Back to Submissions</a>
@endif
```

## Navigation Flow Fixed

### Before (Broken)
```
Course View → View Submissions → Back to Assessment → Back to Assessments → General Assessment List (WRONG!)
```

### After (Fixed)
```
Course View → View Submissions → Back to Course → Course View (Assignments Tab) ✓
```

## Files Modified

1. **Views:**
   - `resources/views/lecturer/courses/show.blade.php`
   - `resources/views/lecturer/assessments/submissions.blade.php`
   - `resources/views/lecturer/assessments/show.blade.php`
   - `resources/views/lecturer/assessments/evaluate.blade.php`
   - `resources/views/lecturer/assessments/course.blade.php`
   - `resources/views/lecturer/assessments/create.blade.php`

2. **Controllers:**
   - `app/Http/Controllers/AssessmentController.php`

## Testing

To test the fix:

1. **Clear caches**: `php artisan route:clear && php artisan config:clear`
2. **Navigate to a course** as a lecturer
3. **Go to Assignments tab**
4. **Click "View Submissions"** on any assessment
5. **Click "Back to Assessment"** - should now go back to the course's assignments tab
6. **Test the full flow**: Course → Submissions → Evaluate → Grade → Back to Course

## Benefits

- **Context Preservation**: Users stay within the course context
- **Better UX**: Intuitive navigation flow
- **No More Confusion**: Users won't be redirected to wrong courses
- **Consistent Behavior**: All assessment-related actions respect the course context

The fix ensures that when you're working with assessments within a specific course, all navigation actions will keep you within that course's context rather than redirecting to the general assessment list. 