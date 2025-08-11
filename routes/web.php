<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentCourseController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController; // <-- Add this line
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\StudentAssessmentController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\LecturerFeedbackController;
use App\Http\Controllers\AdminFeedbackController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\CoursePostController;
use Illuminate\Http\Request; // Added this import for the new test route
use Illuminate\Support\Facades\Storage; // Added this import for file serving

Route::get('/', function () {
    return view('welcome');
});

// Fix: General dashboard redirect route
Route::get('/dashboard', function () {
    $role = Auth::user()->role;

    if ($role === 'student') {
        return redirect()->route('student.dashboard');
    } elseif ($role === 'lecturer') {
        return redirect()->route('lecturer.dashboard');
    } elseif ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } else {
        abort(403, 'Unauthorized');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Role-based dashboards
Route::middleware(['auth', 'track.activity'])->group(function () {
    // Student routes
    Route::get('/student/dashboard', [PostController::class, 'index'])->name('student.dashboard');

    Route::get('/student/courses', [StudentCourseController::class, 'index'])->name('student.courses.index');
    Route::get('/student/courses/join', [StudentCourseController::class, 'showJoinForm'])->name('student.courses.join');
    Route::post('/student/courses/join', [StudentCourseController::class, 'join'])->name('student.courses.join.store');
    Route::get('/student/courses/{course}', [StudentCourseController::class, 'show'])->name('student.courses.show');
    Route::delete('/student/courses/{course}/leave', [StudentCourseController::class, 'leave'])->name('student.courses.leave');



    Route::get('/student/messages', [MessageController::class, 'index'])->name('student.messages.index');

    // Student feedback routes
    Route::get('/student/feedback', [FeedbackController::class, 'index'])->name('student.feedback.index');
    Route::get('/student/feedback/create', [FeedbackController::class, 'create'])->name('student.feedback.create');
    Route::post('/student/feedback', [FeedbackController::class, 'store'])->name('student.feedback.store');
    Route::get('/student/feedback/{feedback}', [FeedbackController::class, 'show'])->name('student.feedback.show');
    Route::get('/student/feedback/{feedback}/edit', [FeedbackController::class, 'edit'])->name('student.feedback.edit');
    Route::put('/student/feedback/{feedback}', [FeedbackController::class, 'update'])->name('student.feedback.update');
    Route::delete('/student/feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('student.feedback.destroy');

    // Lecturer routes
    Route::get('/lecturer/dashboard', [PostController::class, 'index'])->name('lecturer.dashboard');

    Route::get('/lecturer/assessments', function () {
        return view('lecturer.assessment');
    })->name('L.assessment.index');

    Route::get('/lecturer/courses', [CourseController::class, 'index'])->name('lecturer.courses.index');
    Route::get('/lecturer/courses/create', [CourseController::class, 'create'])->name('lecturer.courses.create');
    Route::post('/lecturer/courses', [CourseController::class, 'store'])->name('lecturer.courses.store');
    Route::get('/lecturer/courses/{course}/edit', [CourseController::class, 'edit'])->name('lecturer.courses.edit');
    Route::put('/lecturer/courses/{course}', [CourseController::class, 'update'])->name('lecturer.courses.update');
    Route::delete('/lecturer/courses/{course}', [CourseController::class, 'destroy'])->name('lecturer.courses.destroy');
    Route::get('/lecturer/courses/{course}', [CourseController::class, 'show'])->name('lecturer.courses.show');
    Route::get('/lecturer/courses/{course}/materials/upload', [CourseController::class, 'showUploadMaterialForm'])->name('lecturer.courses.materials.upload.form');
    
    // Lecturer feedback routes are handled by LecturerFeedbackController

    Route::get('/lecturer/message', function () {
        return view('lecturer.message');
    })->name('L.message.index');

    Route::post('/lecturer/courses/{course}/materials', [CourseController::class, 'uploadMaterial'])->name('lecturer.courses.materials.upload');
    Route::delete('/lecturer/courses/{course}/materials/{material}', [CourseController::class, 'deleteMaterial'])->name('lecturer.courses.materials.delete');
    Route::get('/lecturer/courses/{course}/materials/{material}/edit', [CourseController::class, 'editMaterial'])->name('lecturer.courses.materials.edit');
    Route::post('/lecturer/courses/{course}/materials/{material}/edit', [CourseController::class, 'updateMaterial'])->name('lecturer.courses.materials.update');

    // Assessment routes
    Route::get('/lecturer/assessments', [AssessmentController::class, 'index'])->name('L.assessment.index');
    Route::get('/lecturer/assessments/create', [AssessmentController::class, 'create'])->name('L.assessment.create');
    Route::post('/lecturer/assessments', [AssessmentController::class, 'store'])->name('L.assessment.store');
    Route::get('/lecturer/assessments/{assessment}', [AssessmentController::class, 'show'])->name('L.assessment.show');
    Route::get('/lecturer/assessments/{assessment}/edit', [AssessmentController::class, 'edit'])->name('L.assessment.edit');
    Route::put('/lecturer/assessments/{assessment}', [AssessmentController::class, 'update'])->name('L.assessment.update');
    Route::delete('/lecturer/assessments/{assessment}', [AssessmentController::class, 'destroy'])->name('L.assessment.destroy');
    Route::get('/lecturer/assessments/{assessment}/submissions', [AssessmentController::class, 'submissions'])->name('L.assessment.submissions');
    Route::get('/lecturer/submissions/{submission}/evaluate', [AssessmentController::class, 'evaluateSubmission'])->name('L.assessment.evaluate');
    Route::post('/lecturer/submissions/{submission}/grade', [AssessmentController::class, 'gradeSubmission'])->name('L.assessment.grade');
    Route::get('/lecturer/submissions/{submission}/download', [AssessmentController::class, 'downloadSubmissionAttachment'])->name('L.assessment.download-submission');
    Route::post('/lecturer/assessments/{assessment}/publish', [AssessmentController::class, 'publish'])->name('L.assessment.publish');
    Route::post('/lecturer/assessments/{assessment}/close', [AssessmentController::class, 'close'])->name('L.assessment.close');
    Route::get('/lecturer/courses/{course}/assessments', [AssessmentController::class, 'courseAssessments'])->name('L.assessment.course');

    // Student Assessment routes (keeping individual assessment and submission routes for course integration)
    Route::get('/student/assessments/{assessment}', [StudentAssessmentController::class, 'show'])->name('student.assessments.show');
    Route::post('/student/assessments/{assessment}/submit', [StudentAssessmentController::class, 'submit'])->name('student.assessments.submit');
    Route::get('/student/submissions/{submission}', [StudentAssessmentController::class, 'viewSubmission'])->name('student.assessments.view-submission');
    Route::get('/student/submissions/{submission}/download', [StudentAssessmentController::class, 'downloadAttachment'])->name('student.assessments.download');
    
    // Course material download route for students
    Route::get('/student/materials/{material}/download', function(\App\Models\CourseMaterial $material) {
        // Check if student is enrolled in the course
        if (!Auth::user()->enrolledCourses()->where('course_id', $material->course_id)->exists()) {
            abort(403, 'You are not enrolled in this course.');
        }
        
        // Check if material is active
        if (!$material->is_active) {
            abort(404, 'Material not found.');
        }
        
        // Check if file exists
        if (!Storage::disk('public')->exists($material->file_path)) {
            abort(404, 'File not found.');
        }
        
        return Storage::disk('public')->download($material->file_path, $material->file_name);
    })->name('student.materials.download');

    // Test route for debugging
    Route::post('/test/assessment-submission', function(Request $request) {
        \Log::info('Test assessment submission received', $request->all());
        return response()->json(['message' => 'Test submission received', 'data' => $request->all()]);
    })->name('test.assessment.submission');

    // Feedback routes
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/feedback/create', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/feedback/{feedback}', [FeedbackController::class, 'show'])->name('feedback.show');
    Route::get('/feedback/{feedback}/edit', [FeedbackController::class, 'edit'])->name('feedback.edit');
    Route::put('/feedback/{feedback}', [FeedbackController::class, 'update'])->name('feedback.update');
    Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');

    // Lecturer Feedback routes
    Route::get('/lecturer/feedback', [LecturerFeedbackController::class, 'index'])->name('L.feedback.index');
    Route::get('/lecturer/feedback/{feedback}', [LecturerFeedbackController::class, 'show'])->name('L.feedback.show');
    Route::post('/lecturer/feedback/{feedback}/respond', [LecturerFeedbackController::class, 'respond'])->name('L.feedback.respond');
    Route::get('/lecturer/feedback/admin/create', [LecturerFeedbackController::class, 'createToAdmin'])->name('L.feedback.create-to-admin');
    Route::post('/lecturer/feedback/admin', [LecturerFeedbackController::class, 'storeToAdmin'])->name('L.feedback.store-to-admin');
    Route::get('/lecturer/feedback/admin/{feedback}', [LecturerFeedbackController::class, 'showToAdmin'])->name('L.feedback.show-to-admin');
    Route::put('/lecturer/feedback/{feedback}/status', [LecturerFeedbackController::class, 'updateStatus'])->name('L.feedback.update-status');

    // Admin Feedback routes
    Route::get('/admin/feedback', [AdminFeedbackController::class, 'index'])->name('admin.feedback.index');
    Route::get('/admin/feedback/{feedback}', [AdminFeedbackController::class, 'show'])->name('admin.feedback.show');
    Route::post('/admin/feedback/{feedback}/respond', [AdminFeedbackController::class, 'respond'])->name('admin.feedback.respond');
    Route::get('/admin/feedback/student/{feedback}', [AdminFeedbackController::class, 'showStudentFeedback'])->name('admin.feedback.show-student');
    Route::put('/admin/feedback/{feedback}/status', [AdminFeedbackController::class, 'updateStatus'])->name('admin.feedback.update-status');

    // Chat routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{receiverId}', [MessageController::class, 'getMessages'])->name('messages.get');
    Route::post('/messages/{receiverId}', [MessageController::class, 'sendMessage'])->name('messages.send');
    Route::get('/messages/individual/{userId}', [MessageController::class, 'getIndividualMessages'])->name('messages.individual');
    Route::get('/messages/group/{groupId}', [MessageController::class, 'getGroupMessages'])->name('messages.group');
    Route::post('/messages/individual/send', [MessageController::class, 'sendIndividualMessage'])->name('messages.send.individual');
    Route::post('/messages/group/{groupId}/send', [MessageController::class, 'sendGroupMessage'])->name('messages.send.group');
    Route::get('/messages/{message}/download', [MessageController::class, 'downloadFile'])->name('messages.download');
    Route::post('/messages/{senderId}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    Route::get('/messages/unread/count', [MessageController::class, 'getUnreadCount'])->name('messages.unread.count');

    // Group Chat routes
    Route::post('/groups', [MessageController::class, 'createGroup'])->name('group.create');
    Route::get('/group-messages/{groupId}', [MessageController::class, 'getGroupMessages'])->name('group.messages');
    Route::post('/group-messages/{groupId}', [MessageController::class, 'sendGroupMessage'])->name('group.send');
    Route::post('/groups/{groupId}/join', [MessageController::class, 'joinGroup'])->name('group.join');
    Route::post('/groups/{groupId}/leave', [MessageController::class, 'leaveGroup'])->name('group.leave');
    Route::delete('/groups/{groupId}', [MessageController::class, 'deleteGroup'])->name('group.delete');
    Route::get('/groups/{groupId}/check-creator', [MessageController::class, 'checkGroupCreator'])->name('group.check-creator');
    Route::get('/groups/available', [MessageController::class, 'getAvailableGroups'])->name('group.available');
    Route::get('/users/available', [MessageController::class, 'getAvailableUsers'])->name('users.available');
    Route::get('/users/search', [MessageController::class, 'searchUsers'])->name('users.search');
    Route::get('/groups/{groupId}/available-users', [MessageController::class, 'getAvailableUsersForGroup'])->name('group.available-users');
    Route::post('/groups/{groupId}/add-members', [MessageController::class, 'addMembersToGroup'])->name('group.add-members');

    // Post routes
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/{post}/comment', [PostController::class, 'comment'])->name('posts.comment');
    Route::post('/posts/{post}/share', [PostController::class, 'share'])->name('posts.share');
    Route::post('/posts/{postId}/share-to-users', [PostController::class, 'shareToUsers'])->name('posts.share-to-users');
    Route::get('/api/users-for-sharing', [PostController::class, 'getUsersForSharing'])->name('api.users-for-sharing');
    Route::get('/api/test-share/{postId}', [PostController::class, 'testShare'])->name('api.test-share');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::delete('/comments/{comment}', [PostController::class, 'deleteComment'])->name('comments.destroy');

    // Chat deletion routes
    Route::delete('/chats/individual/{userId}', [MessageController::class, 'deleteIndividualChat'])->name('chats.individual.delete');

    // Online users API
    Route::get('/api/online-users', function () {
        return response()->json([
            'users' => \App\Models\User::getOnlineUsers()
        ]);
    })->name('api.online-users');

    // Admin route
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Admin user management routes
    Route::get('/admin/users/{user}', [AdminController::class, 'show'])->name('admin.users.show');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');

    // Course Post routes
    Route::post('/courses/{course}/posts', [CoursePostController::class, 'store'])->name('course.posts.store');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// AI Chatbot endpoint
Route::post('/ai-chat/ask', [\App\Http\Controllers\AIChatController::class, 'ask'])->middleware('auth');

// Test route for share functionality
Route::get('/test-share/{post}', function(\App\Models\Post $post) {
    return response()->json([
        'post_id' => $post->id,
        'content' => $post->content,
        'shares_count' => $post->shares_count,
        'users' => \App\Models\User::where('id', '!=', auth()->id())->select('id', 'name', 'role')->get()
    ]);
})->middleware('auth');

// Debug media route
Route::get('/debug-media', function() {
    return view('debug-media');
})->middleware('auth');

// Media file serving route
Route::get('/media/{path}', function($path) {
    $fullPath = storage_path('app/public/' . $path);
    
    if (!file_exists($fullPath)) {
        abort(404, 'File not found: ' . $path);
    }
    
    return response()->file($fullPath);
})->where('path', '.*')->name('media.serve');

require __DIR__.'/auth.php';
