<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssessmentController extends Controller
{
    /**
     * Display a listing of assessments.
     */
    public function index(Request $request)
    {
        $lecturerId = Auth::id();
        
        // Get lecturer's courses
        $courses = Course::where('lecturer_id', $lecturerId)->get();
        
        // Get assessments with course filtering
        $query = Assessment::with(['course', 'submissions'])
            ->where('lecturer_id', $lecturerId);
            
        // Filter by course if specified
        if ($request->has('course_id') && $request->course_id) {
            $query->where('course_id', $request->course_id);
        }
        
        $assessments = $query->latest()->get();
        
        // Group assessments by course
        $assessmentsByCourse = $assessments->groupBy('course_id');
        
        return view('lecturer.assessment', compact('assessments', 'assessmentsByCourse', 'courses'));
    }

    /**
     * Show the form for creating a new assessment.
     */
    public function create(Request $request)
    {
        $courses = Course::where('lecturer_id', Auth::id())->get();
        $selectedCourseId = $request->get('course_id');
        
        return view('lecturer.assessments.create', compact('courses', 'selectedCourseId'));
    }

    /**
     * Store a newly created assessment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'due_date' => 'required|date|after:today',
            'total_marks' => 'required|integer|min:1|max:100',
            'assignment_file' => 'nullable|file|mimes:pdf,doc,docx,txt,ppt,pptx|max:10240',
        ]);

        // Ensure lecturer owns the course
        $course = Course::where('id', $request->course_id)
                       ->where('lecturer_id', Auth::id())
                       ->first();
        
        if (!$course) {
            return back()->withErrors(['course_id' => 'You can only create assessments for your own courses.']);
        }

        $assessmentData = [
            'title' => $request->title,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'lecturer_id' => Auth::id(),
            'due_date' => $request->due_date,
            'total_marks' => $request->total_marks,
            'status' => 'draft',
        ];

        // Handle file upload
        if ($request->hasFile('assignment_file')) {
            $filePath = $request->file('assignment_file')->store('assessment-files', 'public');
            $assessmentData['file_path'] = $filePath;
        }

        Assessment::create($assessmentData);

        // If created from a specific course, redirect back to that course
        if ($request->has('course_id')) {
            return redirect()->route('lecturer.courses.show', $request->course_id)
                ->with('success', 'Assessment created successfully!');
        }
        
        return redirect()->route('L.assessment.index')->with('success', 'Assessment created successfully!');
    }

    /**
     * Display the specified assessment.
     */
    public function show(Assessment $assessment)
    {
        // Check if lecturer owns this assessment
        if ($assessment->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $assessment->load(['course', 'submissions.student']);
        
        return view('lecturer.assessments.show', compact('assessment'));
    }

    /**
     * Show the form for editing the specified assessment.
     */
    public function edit(Assessment $assessment)
    {
        // Check if lecturer owns this assessment
        if ($assessment->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $courses = Course::where('lecturer_id', Auth::id())->get();
        return view('lecturer.assessments.edit', compact('assessment', 'courses'));
    }

    /**
     * Update the specified assessment.
     */
    public function update(Request $request, Assessment $assessment)
    {
        // Check if lecturer owns this assessment
        if ($assessment->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'due_date' => 'required|date',
            'total_marks' => 'required|integer|min:1|max:100',
            'status' => 'required|in:draft,published,closed',
            'assignment_file' => 'nullable|file|mimes:pdf,doc,docx,txt,ppt,pptx|max:10240',
        ]);

        // Ensure lecturer owns the course
        $course = Course::where('id', $request->course_id)
                       ->where('lecturer_id', Auth::id())
                       ->first();
        
        if (!$course) {
            return back()->withErrors(['course_id' => 'You can only assign assessments to your own courses.']);
        }

        $assessmentData = $request->only(['title', 'description', 'course_id', 'due_date', 'total_marks', 'status']);

        // Handle file upload
        if ($request->hasFile('assignment_file')) {
            // Delete old file if exists
            if ($assessment->file_path) {
                Storage::disk('public')->delete($assessment->file_path);
            }
            
            $filePath = $request->file('assignment_file')->store('assessment-files', 'public');
            $assessmentData['file_path'] = $filePath;
        }

        $assessment->update($assessmentData);

        // If updated from a specific course context, redirect back to that course
        if ($request->has('return_to_course')) {
            return redirect()->route('lecturer.courses.show', $request->return_to_course)
                ->with('success', 'Assessment updated successfully!');
        }
        
        return redirect()->route('L.assessment.index')->with('success', 'Assessment updated successfully!');
    }

    /**
     * Remove the specified assessment.
     */
    public function destroy(Assessment $assessment)
    {
        // Check if lecturer owns this assessment
        if ($assessment->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $course = $assessment->course;
        $assessment->delete();

        return redirect()->route('lecturer.courses.index')
            ->with('success', 'Assessment deleted successfully!');
    }

    /**
     * Show submissions for an assessment.
     */
    public function submissions(Assessment $assessment)
    {
        // Check if lecturer owns this assessment
        if ($assessment->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $submissions = $assessment->submissions()->with('student')->latest('submitted_at')->get();
        
        return view('lecturer.assessments.submissions', compact('assessment', 'submissions'));
    }

    /**
     * Show a specific submission for evaluation.
     */
    public function evaluateSubmission(AssessmentSubmission $submission)
    {
        // Check if lecturer owns the assessment
        if ($submission->assessment->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('lecturer.assessments.evaluate', compact('submission'));
    }

    /**
     * Grade a submission.
     */
    public function gradeSubmission(Request $request, AssessmentSubmission $submission)
    {
        // Check if lecturer owns the assessment
        if ($submission->assessment->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'marks_obtained' => 'required|integer|min:0|max:' . $submission->assessment->total_marks,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'marks_obtained' => $request->marks_obtained,
            'feedback' => $request->feedback,
            'status' => 'graded',
            'graded_at' => now(),
        ]);

        $redirectRoute = route('L.assessment.submissions', $submission->assessment);
        
        // If there's a return_to_course parameter, add it to the redirect
        if ($request->has('return_to_course')) {
            $redirectRoute .= '?return_to_course=' . $request->get('return_to_course');
        }
        
        return redirect($redirectRoute)
            ->with('success', 'Submission graded successfully!');
    }

    /**
     * Publish an assessment.
     */
    public function publish(Assessment $assessment)
    {
        // Check if lecturer owns this assessment
        if ($assessment->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $assessment->update(['status' => 'published']);
        $course = $assessment->course;

        // If published from a specific course context, redirect back to that course
        if (request()->has('return_to_course')) {
            return redirect()->route('lecturer.courses.show', request()->get('return_to_course'))
                ->with('success', 'Assessment published successfully!');
        }

        return redirect()->route('lecturer.courses.index')
            ->with('success', 'Assessment published successfully!');
    }

    /**
     * Close an assessment.
     */
    public function close(Assessment $assessment)
    {
        // Check if lecturer owns this assessment
        if ($assessment->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $assessment->update(['status' => 'closed']);
        $course = $assessment->course;

        // If closed from a specific course context, redirect back to that course
        if (request()->has('return_to_course')) {
            return redirect()->route('lecturer.courses.show', request()->get('return_to_course'))
                ->with('success', 'Assessment closed successfully!');
        }

        return redirect()->route('lecturer.courses.index')
            ->with('success', 'Assessment closed successfully!');
    }

    /**
     * Show assessments for a specific course.
     */
    public function courseAssessments(Course $course)
    {
        // Check if lecturer owns this course
        if ($course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $assessments = Assessment::with(['submissions'])
            ->where('course_id', $course->id)
            ->where('lecturer_id', Auth::id())
            ->latest()
            ->get();

        return view('lecturer.assessments.course', compact('course', 'assessments'));
    }

    /**
     * Download submission attachment.
     */
    public function downloadSubmissionAttachment(AssessmentSubmission $submission)
    {
        // Check if lecturer owns the assessment
        if ($submission->assessment->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        if (!$submission->attachment_path) {
            abort(404, 'No attachment found.');
        }
        
        return Storage::disk('public')->download($submission->attachment_path);
    }
}
