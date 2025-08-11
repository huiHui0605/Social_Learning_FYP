<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentCourseController extends Controller
{
    public function index()
    {
        // Check if user is a student
        if (Auth::user()->role !== 'student') {
            abort(403, 'Access denied. Only students can perform this action.');
        }

        $enrolledCourses = Auth::user()->courses()
            ->where('courses.is_active', true)
            ->where('course_enrollments.is_active', true)
            ->orderBy('courses.created_at', 'desc')
            ->get();
        
        return view('student.courses', compact('enrolledCourses'));
    }

    public function showJoinForm()
    {
        // Check if user is a student
        if (Auth::user()->role !== 'student') {
            abort(403, 'Access denied. Only students can perform this action.');
        }

        return view('student.courses.join');
    }

    public function join(Request $request)
    {
        // Check if user is a student
        if (Auth::user()->role !== 'student') {
            abort(403, 'Access denied. Only students can perform this action.');
        }

        $request->validate([
            'course_code' => 'required|string|exists:courses,course_code'
        ]);

        $course = Course::where('course_code', $request->course_code)
            ->where('is_active', true)
            ->first();

        if (!$course) {
            return back()->with('error', 'Course not found or inactive.');
        }

        // Check if course has available slots
        if (!$course->hasAvailableSlots()) {
            return back()->with('error', 'This course is full and cannot accept new enrollments.');
        }

        // Check if student is already enrolled
        $existingEnrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('student_id', Auth::id())
            ->first();

        if ($existingEnrollment) {
            if ($existingEnrollment->is_active) {
                return back()->with('error', 'You are already enrolled in this course.');
            } else {
                // Reactivate enrollment
                $existingEnrollment->update([
                    'is_active' => true,
                    'status' => 'enrolled'
                ]);
                return redirect()->route('student.courses.index')
                    ->with('success', 'Successfully re-enrolled in the course!');
            }
        }

        // Create new enrollment
        CourseEnrollment::create([
            'course_id' => $course->id,
            'student_id' => Auth::id(),
            'status' => 'enrolled',
            'is_active' => true,
            'enrolled_at' => now(),
        ]);

        return redirect()->route('student.courses.index')
            ->with('success', 'Successfully joined the course!');
    }

    public function show(Course $course)
    {
        // Check if user is a student
        if (Auth::user()->role !== 'student') {
            abort(403, 'Access denied. Only students can perform this action.');
        }

        // Check if student is enrolled
        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('student_id', Auth::id())
            ->where('is_active', true)
            ->first();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this course.');
        }

        $materials = $course->materials()->where('is_active', true)->get();
        $assessments = $course->assessments()->where('status', 'published')->with(['submissions' => function($query) {
            $query->where('student_id', Auth::id());
        }])->latest()->get();
        $posts = $course->posts()->with('user')->latest()->get();

        return view('student.courses.show', compact('course', 'materials', 'enrollment', 'assessments', 'posts'));
    }

    public function leave(Course $course)
    {
        // Check if user is a student
        if (Auth::user()->role !== 'student') {
            abort(403, 'Access denied. Only students can perform this action.');
        }

        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('student_id', Auth::id())
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'You are not enrolled in this course.');
        }

        $enrollment->update([
            'is_active' => false,
            'status' => 'dropped'
        ]);

        return redirect()->route('student.courses.index')
            ->with('success', 'Successfully left the course.');
    }
} 