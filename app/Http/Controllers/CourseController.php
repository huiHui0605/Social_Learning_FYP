<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if user is a lecturer
        if (Auth::user()->role !== 'lecturer') {
            abort(403, 'Access denied. Only lecturers can perform this action.');
        }

        $courses = Course::where('lecturer_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('lecturer.courses', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if user is a lecturer
        if (Auth::user()->role !== 'lecturer') {
            abort(403, 'Access denied. Only lecturers can perform this action.');
        }

        return view('lecturer.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user is a lecturer
        if (Auth::user()->role !== 'lecturer') {
            abort(403, 'Access denied. Only lecturers can perform this action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'course_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $courseData = $request->only(['title', 'description', 'semester', 'academic_year']);
        $courseData['lecturer_id'] = Auth::id();
        $courseData['course_code'] = Course::generateCourseCode();
        $courseData['is_active'] = true;

        if ($request->hasFile('course_image')) {
            $imagePath = $request->file('course_image')->store('courses', 'public');
            $courseData['image_path'] = $imagePath;
        }

        Course::create($courseData);

        return redirect()->route('lecturer.courses.index')
            ->with('success', 'Course created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        // Check if user is a lecturer
        if (Auth::user()->role !== 'lecturer') {
            abort(403, 'Access denied. Only lecturers can perform this action.');
        }

        // Check if the lecturer owns this course
        if ($course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this course.');
        }

        $materials = $course->materials()->where('is_active', true)->get();
        $enrollments = $course->enrollments()->with('student')->get();
        $posts = $course->posts()->with('user')->latest()->get();

        return view('lecturer.courses.show', compact('course', 'materials', 'enrollments', 'posts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        // Check if user is a lecturer
        if (Auth::user()->role !== 'lecturer') {
            abort(403, 'Access denied. Only lecturers can perform this action.');
        }

        if ($course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this course.');
        }

        return view('lecturer.courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        // Check if user is a lecturer
        if (Auth::user()->role !== 'lecturer') {
            abort(403, 'Access denied. Only lecturers can perform this action.');
        }

        if ($course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this course.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'course_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $courseData = $request->only(['title', 'description', 'semester', 'academic_year']);
        $courseData['is_active'] = $request->has('is_active');

        if ($request->hasFile('course_image')) {
            // Delete old image if exists
            if ($course->image_path) {
                Storage::disk('public')->delete($course->image_path);
            }
            
            $imagePath = $request->file('course_image')->store('courses', 'public');
            $courseData['image_path'] = $imagePath;
        }

        $course->update($courseData);

        return redirect()->route('lecturer.courses.show', $course)
            ->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        // Check if user is a lecturer
        if (Auth::user()->role !== 'lecturer') {
            abort(403, 'Access denied. Only lecturers can perform this action.');
        }

        if ($course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this course.');
        }

        $course->update(['is_active' => false]);

        return redirect()->route('lecturer.courses.index')
            ->with('success', 'Course deactivated successfully!');
    }

    /**
     * Join a course using course code.
     */
    public function join(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_code' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $course = Course::where('course_code', strtoupper($request->course_code))
                       ->where('is_active', true)
                       ->first();

        if (!$course) {
            return redirect()->back()
                ->withErrors(['course_code' => 'Invalid course code or course is not active.'])
                ->withInput();
        }

        $user = Auth::user();
        
        // Check if already enrolled
        if ($user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            return redirect()->back()
                ->withErrors(['course_code' => 'You are already enrolled in this course.'])
                ->withInput();
        }

        // Enroll the student
        CourseEnrollment::create([
            'course_id' => $course->id,
            'student_id' => $user->id,
            'status' => 'enrolled',
            'is_active' => true,
            'enrolled_at' => now(),
        ]);

        return redirect()->route('student.courses.index')
            ->with('success', 'Successfully enrolled in ' . $course->title . '!');
    }

    /**
     * Show the join course form.
     */
    public function showJoinForm()
    {
        return view('student.courses.join');
    }

    /**
     * Show the upload material form for a course.
     */
    public function showUploadMaterialForm(Course $course)
    {
        // Optionally, check if user is a lecturer and owns the course
        if (auth()->user()->role !== 'lecturer' || $course->lecturer_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this course.');
        }
        return view('lecturer.materials.upload', compact('course'));
    }

    /**
     * Upload a material for a course.
     */
    public function uploadMaterial(Request $request, Course $course)
    {
        // Check if user is a lecturer
        if (Auth::user()->role !== 'lecturer') {
            abort(403, 'Access denied. Only lecturers can perform this action.');
        }

        if ($course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this course.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:102400|mimes:pdf,doc,docx,ppt,pptx,txt,jpg,jpeg,png,gif,mp4,avi,mov' // 100MB max with specific file types
        ]);

        $file = $request->file('file');
        $filePath = $file->store('course-materials', 'public');

        CourseMaterial::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'file_name' => $file->getClientOriginalName(),
            'course_id' => $course->id,
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('success', 'Material uploaded successfully!');
    }

    /**
     * Delete a material from a course.
     */
    public function deleteMaterial(Course $course, CourseMaterial $material)
    {
        \Log::info('DeleteMaterial called', [
            'request_method' => request()->method(),
            'all' => request()->all(),
            'route_params' => [
                'course' => $course->id,
                'material' => $material->id,
            ],
        ]);
        // Check if user is a lecturer
        if (Auth::user()->role !== 'lecturer') {
            abort(403, 'Access denied. Only lecturers can perform this action.');
        }

        if ($material->uploaded_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this material.');
        }

        $material->delete();

        return back()->with('success', 'Material deleted successfully!');
    }

    /**
     * Show the form for editing a material.
     */
    public function editMaterial(Course $course, CourseMaterial $material)
    {
        if (Auth::user()->role !== 'lecturer' || $course->lecturer_id !== Auth::id() || $material->course_id !== $course->id) {
            abort(403, 'Unauthorized access to this material.');
        }
        return view('lecturer.materials.edit', compact('course', 'material'));
    }

    /**
     * Update the specified material in storage.
     */
    public function updateMaterial(Request $request, Course $course, CourseMaterial $material)
    {
        if (Auth::user()->role !== 'lecturer' || $course->lecturer_id !== Auth::id() || $material->course_id !== $course->id) {
            abort(403, 'Unauthorized access to this material.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:102400|mimes:pdf,doc,docx,ppt,pptx,txt,jpg,jpeg,png,gif,mp4,avi,mov'
        ]);

        $material->title = $request->title;
        $material->description = $request->description;

        if ($request->hasFile('file')) {
            // Delete old file
            if ($material->file_path) {
                \Storage::disk('public')->delete($material->file_path);
            }
            $file = $request->file('file');
            $filePath = $file->store('course-materials', 'public');
            $material->file_path = $filePath;
            $material->file_type = $file->getClientMimeType();
            $material->file_size = $file->getSize();
            $material->file_name = $file->getClientOriginalName();
        }

        $material->save();

        return redirect()->route('lecturer.courses.show', $course)->with('success', 'Material updated successfully!');
    }
}
