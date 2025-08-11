<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LecturerFeedbackController extends Controller
{
    /**
     * Display a listing of feedback received by lecturer.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer') {
            abort(403, 'Unauthorized');
        }

        $receivedFeedback = $user->receivedFeedback()->with(['student', 'course'])->latest()->get();
        $submittedFeedback = $user->lecturerFeedback()->latest()->get();

        return view('lecturer.feedback', compact('receivedFeedback', 'submittedFeedback'));
    }

    /**
     * Display the specified feedback received by lecturer.
     */
    public function show(Feedback $feedback)
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer' || $feedback->lecturer_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $feedback->load(['student', 'course', 'respondedBy']);
        return view('lecturer.feedback.show', compact('feedback'));
    }

    /**
     * Respond to feedback.
     */
    public function respond(Request $request, Feedback $feedback)
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer' || $feedback->lecturer_id !== $user->id) {
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

        return redirect()->route('L.feedback.show', $feedback)
            ->with('success', 'Response submitted successfully!');
    }

    /**
     * Show the form for creating feedback to admin.
     */
    public function createToAdmin()
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer') {
            abort(403, 'Unauthorized');
        }

        $admins = User::where('role', 'admin')->get();

        return view('lecturer.feedback.create-to-admin', compact('admins'));
    }

    /**
     * Store feedback to admin.
     */
    public function storeToAdmin(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'admin_id' => 'required|exists:users,id',
            'category' => 'required|in:general,technical,suggestion,complaint,other',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        // Verify the target user is an admin
        $admin = User::where('id', $request->admin_id)->where('role', 'admin')->first();
        if (!$admin) {
            return back()->withErrors(['admin_id' => 'Invalid admin selected.']);
        }

        Feedback::create([
            'title' => $request->title,
            'content' => $request->content,
            'type' => 'lecturer_to_admin',
            'category' => $request->category,
            'priority' => $request->priority,
            'lecturer_id' => $user->id,
            'admin_id' => $request->admin_id,
            'status' => 'pending',
        ]);

        return redirect()->route('L.feedback.index')->with('success', 'Feedback submitted to admin successfully!');
    }

    /**
     * Display lecturer's feedback to admin.
     */
    public function showToAdmin(Feedback $feedback)
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer' || $feedback->lecturer_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $feedback->load(['admin', 'respondedBy']);
        return view('lecturer.feedback.show-to-admin', compact('feedback'));
    }

    /**
     * Update feedback status.
     */
    public function updateStatus(Request $request, Feedback $feedback)
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer' || $feedback->lecturer_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,closed',
        ]);

        $feedback->update(['status' => $request->status]);

        return redirect()->route('L.feedback.show', $feedback)
            ->with('success', 'Status updated successfully!');
    }
}
