<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminFeedbackController extends Controller
{
    /**
     * Display a listing of feedback for admin.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $receivedFeedback = $user->adminFeedback()->with(['lecturer'])->latest()->get();
        $allStudentFeedback = Feedback::where('type', 'student_to_lecturer')
            ->with(['student', 'lecturer', 'course'])
            ->latest()
            ->get();

        return view('admin.feedback', compact('receivedFeedback', 'allStudentFeedback'));
    }

    /**
     * Display the specified feedback received by admin.
     */
    public function show(Feedback $feedback)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' || $feedback->admin_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $feedback->load(['lecturer', 'respondedBy']);
        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Respond to feedback.
     */
    public function respond(Request $request, Feedback $feedback)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' || $feedback->admin_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'response' => 'required|string|min:10',
            'status' => 'required|in:pending,in_progress,resolved,closed',
        ]);

        $feedback->update([
            'response' => $request->response,
            'status' => $request->status,
            'responded_at' => now(),
            'responded_by' => $user->id,
        ]);

        return redirect()->route('admin.feedback.show', $feedback)
            ->with('success', 'Response submitted successfully!');
    }

    /**
     * Display student feedback details (admin can view all student feedback).
     */
    public function showStudentFeedback(Feedback $feedback)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' || $feedback->type !== 'student_to_lecturer') {
            abort(403, 'Unauthorized');
        }

        $feedback->load(['student', 'lecturer', 'course', 'respondedBy']);
        return view('admin.feedback.show-student', compact('feedback'));
    }

    /**
     * Update feedback status.
     */
    public function updateStatus(Request $request, Feedback $feedback)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' || $feedback->admin_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,closed',
        ]);

        $feedback->update(['status' => $request->status]);

        return redirect()->route('admin.feedback.show', $feedback)
            ->with('success', 'Status updated successfully!');
    }
}
