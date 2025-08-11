<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentAssessmentController extends Controller
{


    /**
     * Show the form for submitting an assessment.
     */
    public function show(Assessment $assessment)
    {
        \Log::info('Assessment show method called', [
            'assessment_id' => $assessment->id,
            'assessment_status' => $assessment->status,
            'course_id' => $assessment->course_id,
            'student_id' => Auth::id()
        ]);
        
        $student = Auth::user();
        
        // Check if student is enrolled in the course
        if (!$student->enrolledCourses()->where('course_id', $assessment->course_id)->exists()) {
            \Log::warning('Student not enrolled in course', [
                'student_id' => $student->id,
                'course_id' => $assessment->course_id
            ]);
            abort(403, 'You are not enrolled in this course.');
        }
        
        // Check if assessment is published
        if ($assessment->status !== 'published') {
            \Log::warning('Assessment not published', [
                'assessment_id' => $assessment->id,
                'status' => $assessment->status
            ]);
            abort(403, 'This assessment is not available for submission.');
        }
        
        // Check if student has already submitted
        $existingSubmission = $student->assessmentSubmissions()
            ->where('assessment_id', $assessment->id)
            ->first();
        
        \Log::info('Assessment show method completed', [
            'assessment_id' => $assessment->id,
            'has_existing_submission' => $existingSubmission ? true : false
        ]);
        
        return view('student.assessments.show', compact('assessment', 'existingSubmission'));
    }

    /**
     * Submit an assessment.
     */
    public function submit(Request $request, Assessment $assessment)
    {
        \Log::info('Assessment submission attempt - START', [
            'assessment_id' => $assessment->id,
            'assessment_title' => $assessment->title,
            'assessment_status' => $assessment->status,
            'student_id' => Auth::id(),
            'student_name' => Auth::user()->name,
            'request_data' => $request->all(),
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'timestamp' => now()->toISOString()
        ]);

        $student = Auth::user();
        
        \Log::info('Student retrieved', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'student_email' => $student->email
        ]);
        
        // Check if student is enrolled in the course
        if (!$student->enrolledCourses()->where('course_id', $assessment->course_id)->exists()) {
            \Log::warning('Student not enrolled in course', [
                'student_id' => $student->id,
                'course_id' => $assessment->course_id
            ]);
            abort(403, 'You are not enrolled in this course.');
        }
        
        // Check if assessment is published
        if ($assessment->status !== 'published') {
            \Log::warning('Assessment not published', [
                'assessment_id' => $assessment->id,
                'status' => $assessment->status
            ]);
            abort(403, 'This assessment is not available for submission.');
        }
        
        // Check if student has already submitted
        $existingSubmission = $student->assessmentSubmissions()
            ->where('assessment_id', $assessment->id)
            ->first();
            
        if ($existingSubmission) {
            \Log::warning('Student already submitted', [
                'student_id' => $student->id,
                'assessment_id' => $assessment->id,
                'submission_id' => $existingSubmission->id
            ]);
            return back()->withErrors(['submission' => 'You have already submitted this assessment.']);
        }
        
        try {
            $request->validate([
                'submission_content' => 'required|string|min:10',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:102400', // 100MB max
            ]);
            
            $data = [
                'assessment_id' => $assessment->id,
                'student_id' => $student->id,
                'submission_content' => $request->submission_content,
                'status' => $assessment->isOverdue() ? 'late' : 'submitted',
                'submitted_at' => now(),
            ];
            
            // Handle file upload
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('submissions', $fileName, 'public');
                
                $data['attachment_path'] = $filePath;
                \Log::info('File uploaded successfully', ['file_path' => $filePath]);
            }
            
            $submission = AssessmentSubmission::create($data);
            
            \Log::info('Assessment submitted successfully', [
                'submission_id' => $submission->id,
                'assessment_id' => $assessment->id,
                'student_id' => $student->id
            ]);
            
            return redirect()->route('student.courses.show', $assessment->course)
                ->with('success', 'Assessment submitted successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Error submitting assessment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'assessment_id' => $assessment->id,
                'student_id' => $student->id
            ]);
            
            return back()->withErrors(['submission' => 'An error occurred while submitting your assessment. Please try again.']);
        }
    }

    /**
     * View a submitted assessment.
     */
    public function viewSubmission(AssessmentSubmission $submission)
    {
        $student = Auth::user();
        
        // Check if student owns this submission
        if ($submission->student_id !== $student->id) {
            abort(403, 'Unauthorized');
        }
        
        return view('student.assessments.view-submission', compact('submission'));
    }

    /**
     * Download attachment.
     */
    public function downloadAttachment(AssessmentSubmission $submission)
    {
        $student = Auth::user();
        
        // Check if student owns this submission
        if ($submission->student_id !== $student->id) {
            abort(403, 'Unauthorized');
        }
        
        if (!$submission->attachment_path) {
            abort(404, 'No attachment found.');
        }
        
        return Storage::disk('public')->download($submission->attachment_path);
    }
}
