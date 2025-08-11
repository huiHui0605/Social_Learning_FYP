<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Display a listing of feedback for students.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'student') {
            $feedback = $user->submittedFeedback()->with(['lecturer', 'course'])->latest()->get();
            return view('student.feedback', compact('feedback'));
        }
        
        abort(403, 'Unauthorized');
    }

    /**
     * Show the form for creating a new feedback.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            abort(403, 'Unauthorized');
        }

        // Get lecturers from courses the student is enrolled in
        $enrolledCourseIds = $user->enrolledCourses()->pluck('course_id');
        $lecturers = User::where('role', 'lecturer')
            ->whereHas('taughtCourses', function($query) use ($enrolledCourseIds) {
                $query->whereIn('id', $enrolledCourseIds);
            })
            ->get();

        $courses = $user->enrolledCourses()->get();

        return view('student.feedback.create', compact('lecturers', 'courses'));
    }

    /**
     * Store a newly created feedback.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'lecturer_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'category' => 'required|in:general,course,technical,suggestion,complaint,other',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        // Verify lecturer teaches a course the student is enrolled in
        $enrolledCourseIds = $user->enrolledCourses()->pluck('course_id');
        $lecturer = User::where('id', $request->lecturer_id)
            ->where('role', 'lecturer')
            ->whereHas('taughtCourses', function($query) use ($enrolledCourseIds) {
                $query->whereIn('id', $enrolledCourseIds);
            })
            ->first();

        if (!$lecturer) {
            return back()->withErrors(['lecturer_id' => 'You can only submit feedback to lecturers of your enrolled courses.']);
        }

        Feedback::create([
            'title' => $request->title,
            'content' => $request->content,
            'type' => 'student_to_lecturer',
            'category' => $request->category,
            'priority' => $request->priority,
            'student_id' => $user->id,
            'lecturer_id' => $request->lecturer_id,
            'course_id' => $request->course_id,
            'status' => 'pending',
        ]);

        return redirect()->route('student.feedback.index')->with('success', 'Feedback submitted successfully!');
    }

    /**
     * Display the specified feedback.
     */
    public function show(Feedback $feedback)
    {
        $user = Auth::user();
        
        if ($user->role === 'student' && $feedback->student_id === $user->id) {
            $feedback->load(['lecturer', 'course', 'respondedBy']);
            return view('student.feedback.show', compact('feedback'));
        }
        
        abort(403, 'Unauthorized');
    }

    /**
     * Show the form for editing the specified feedback.
     */
    public function edit(Feedback $feedback)
    {
        $user = Auth::user();
        
        if ($user->role !== 'student' || $feedback->student_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Only allow editing if feedback hasn't been responded to
        if ($feedback->hasResponse()) {
                    return redirect()->route('student.feedback.show', $feedback)
            ->with('error', 'Cannot edit feedback that has been responded to.');
        }

        $enrolledCourseIds = $user->enrolledCourses()->pluck('course_id');
        $lecturers = User::where('role', 'lecturer')
            ->whereHas('taughtCourses', function($query) use ($enrolledCourseIds) {
                $query->whereIn('id', $enrolledCourseIds);
            })
            ->get();

        $courses = $user->enrolledCourses()->get();

        return view('student.feedback.edit', compact('feedback', 'lecturers', 'courses'));
    }

    /**
     * Update the specified feedback.
     */
    public function update(Request $request, Feedback $feedback)
    {
        $user = Auth::user();
        
        if ($user->role !== 'student' || $feedback->student_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Only allow updating if feedback hasn't been responded to
        if ($feedback->hasResponse()) {
                    return redirect()->route('student.feedback.show', $feedback)
            ->with('error', 'Cannot update feedback that has been responded to.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'lecturer_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'category' => 'required|in:general,course,technical,suggestion,complaint,other',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        // Verify lecturer teaches a course the student is enrolled in
        $enrolledCourseIds = $user->enrolledCourses()->pluck('course_id');
        $lecturer = User::where('id', $request->lecturer_id)
            ->where('role', 'lecturer')
            ->whereHas('taughtCourses', function($query) use ($enrolledCourseIds) {
                $query->whereIn('id', $enrolledCourseIds);
            })
            ->first();

        if (!$lecturer) {
            return back()->withErrors(['lecturer_id' => 'You can only submit feedback to lecturers of your enrolled courses.']);
        }

        $feedback->update($request->all());

        return redirect()->route('student.feedback.index')->with('success', 'Feedback updated successfully!');
    }

    /**
     * Remove the specified feedback.
     */
    public function destroy(Feedback $feedback)
    {
        $user = Auth::user();
        
        if ($user->role !== 'student' || $feedback->student_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Only allow deletion if feedback hasn't been responded to
        if ($feedback->hasResponse()) {
            return redirect()->route('feedback.show', $feedback)
                ->with('error', 'Cannot delete feedback that has been responded to.');
        }

        $feedback->delete();

        return redirect()->route('feedback.index')->with('success', 'Feedback deleted successfully!');
    }
}
