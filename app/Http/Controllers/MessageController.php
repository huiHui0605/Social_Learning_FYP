<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\GroupChat;
use App\Models\GroupMessage;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    /**
     * Display the messaging interface.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get conversation partners based on user role
        if ($user->role === 'lecturer') {
            // Lecturers can message students from their courses
            $enrolledStudentIds = $user->taughtCourses()
                ->with('enrollments')
                ->get()
                ->flatMap(function ($course) {
                    return $course->enrollments->pluck('student_id');
                })
                ->unique();
            
            $conversationPartners = User::whereIn('id', $enrolledStudentIds)
                ->where('role', 'student')
                ->get();
        } elseif ($user->role === 'student') {
            // Students can message lecturers from their enrolled courses
            $enrolledCourseIds = $user->enrolledCourses()->pluck('course_id');
            $lecturerIds = User::where('role', 'lecturer')
                ->whereHas('taughtCourses', function($query) use ($enrolledCourseIds) {
                    $query->whereIn('id', $enrolledCourseIds);
                })
                ->pluck('id');
            
            $conversationPartners = User::whereIn('id', $lecturerIds)->get();
        } else {
            // Admin can message anyone
            $conversationPartners = User::where('id', '!=', $user->id)->get();
        }

        // Get user's group chats
        $groupChats = $user->groupChats()->where('group_members.is_active', true)->get();

        return view('message.index', compact('conversationPartners', 'groupChats'));
    }

    /**
     * Get messages between two users.
     */
    public function getMessages(Request $request, $receiverId)
    {
        $user = Auth::user();
        $receiver = User::findOrFail($receiverId);
        
        // Verify user can message this receiver
        if (!$this->canMessageUser($user, $receiver)) {
            abort(403, 'Unauthorized to message this user.');
        }

        // Get messages between the two users
        $messages = Message::where(function($query) use ($user, $receiver) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $receiver->id);
        })->orWhere(function($query) use ($user, $receiver) {
            $query->where('sender_id', $receiver->id)
                  ->where('receiver_id', $user->id);
        })->with(['sender', 'receiver'])
          ->orderBy('created_at', 'asc')
          ->get();

        // Mark messages as read
        Message::where('sender_id', $receiver->id)
               ->where('receiver_id', $user->id)
               ->where('is_read', false)
               ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'messages' => $messages,
            'receiver' => $receiver
        ]);
    }

    /**
     * Send a message.
     */
    public function sendMessage(Request $request, $receiverId)
    {
        $user = Auth::user();
        $receiver = User::findOrFail($receiverId);
        
        // Verify user can message this receiver
        if (!$this->canMessageUser($user, $receiver)) {
            abort(403, 'Unauthorized to message this user.');
        }

        $request->validate([
            'content' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $messageData = [
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'message_type' => 'text',
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('messages', $fileName, 'public');
            
            $messageData['file_path'] = $filePath;
            $messageData['file_name'] = $file->getClientOriginalName();
            $messageData['file_type'] = $file->getMimeType();
            $messageData['file_size'] = $file->getSize();
            $messageData['message_type'] = $this->getMessageType($file->getMimeType());
        }

        // Handle text content
        if ($request->filled('content')) {
            $messageData['content'] = $request->content;
        }

        // Ensure at least one of content or file is provided
        if (empty($messageData['content']) && empty($messageData['file_path'])) {
            return response()->json(['error' => 'Message must contain text or file.'], 422);
        }

        $message = Message::create($messageData);
        $message->load(['sender', 'receiver']);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Download a file from a message.
     */
    public function downloadFile(Message $message)
    {
        $user = Auth::user();
        
        // Verify user can access this message
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403, 'Unauthorized to access this file.');
        }

        if (!$message->file_path || !Storage::disk('public')->exists($message->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($message->file_path, $message->file_name);
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead(Request $request, $senderId)
    {
        $user = Auth::user();
        
        Message::where('sender_id', $senderId)
               ->where('receiver_id', $user->id)
               ->where('is_read', false)
               ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread messages count.
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user->unread_messages_count;
        
        return response()->json(['count' => $count]);
    }

    // Group Chat Methods

    /**
     * Create a new group chat.
     */
    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:public,private',
            'max_members' => 'required|integer|min:2|max:100',
            'members' => 'nullable|string', // JSON string of user IDs
            'image' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();

        $groupData = [
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => $user->id,
            'type' => $request->type,
            'max_members' => $request->max_members,
        ];

        // Handle group image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('group_chats', $imageName, 'public');
            $groupData['image_path'] = $imagePath;
        }

        $groupChat = GroupChat::create($groupData);
        
        // Add creator as admin
        $groupChat->addMember($user, 'admin');

        // Add selected members
        if ($request->filled('members')) {
            $memberIds = json_decode($request->members, true);
            if (is_array($memberIds)) {
                foreach ($memberIds as $memberId) {
                    $memberUser = User::find($memberId);
                    if ($memberUser && !$groupChat->isFull()) {
                        $groupChat->addMember($memberUser, 'member');
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'group' => $groupChat->load(['creator', 'members']),
            'message' => 'Group created successfully'
        ]);
    }

    /**
     * Get group chat messages.
     */
    public function getGroupMessages(Request $request, $groupId)
    {
        $user = Auth::user();
        $groupChat = GroupChat::findOrFail($groupId);

        // Verify user is a member
        if (!$groupChat->isMember($user)) {
            abort(403, 'You are not a member of this group.');
        }

        $messages = $groupChat->messages()
            ->with(['sender'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages,
            'group' => $groupChat->load('creator')
        ]);
    }

    /**
     * Send a message to group chat.
     */
    public function sendGroupMessage(Request $request, $groupId)
    {
        $user = Auth::user();
        $groupChat = GroupChat::findOrFail($groupId);

        // Verify user is a member
        if (!$groupChat->isMember($user)) {
            abort(403, 'You are not a member of this group.');
        }

        $request->validate([
            'content' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $messageData = [
            'group_chat_id' => $groupChat->id,
            'sender_id' => $user->id,
            'message_type' => 'text',
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('group_messages', $fileName, 'public');
            
            $messageData['file_path'] = $filePath;
            $messageData['file_name'] = $file->getClientOriginalName();
            $messageData['file_type'] = $file->getMimeType();
            $messageData['file_size'] = $file->getSize();
            $messageData['message_type'] = $this->getMessageType($file->getMimeType());
        }

        // Handle text content
        if ($request->filled('content')) {
            $messageData['content'] = $request->content;
        }

        // Ensure at least one of content or file is provided
        if (empty($messageData['content']) && empty($messageData['file_path'])) {
            return response()->json(['error' => 'Message must contain text or file.'], 422);
        }

        $message = GroupMessage::create($messageData);
        $message->load(['sender', 'groupChat']);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Join a group chat.
     */
    public function joinGroup(Request $request, $groupId)
    {
        $user = Auth::user();
        $groupChat = GroupChat::findOrFail($groupId);

        // Check if group is full
        if ($groupChat->isFull()) {
            return response()->json(['error' => 'Group is full.'], 422);
        }

        // Check if user is already a member
        if ($groupChat->isMember($user)) {
            return response()->json(['error' => 'You are already a member of this group.'], 422);
        }

        $groupChat->addMember($user);

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined the group.'
        ]);
    }

    /**
     * Leave a group chat.
     */
    public function leaveGroup(Request $request, $groupId)
    {
        $user = Auth::user();
        $groupChat = GroupChat::findOrFail($groupId);
        
        // Check if user is a member of the group
        $member = $groupChat->members()->where('user_id', $user->id)->first();
        if (!$member) {
            return response()->json(['error' => 'You are not a member of this group.'], 403);
        }
        
        // If user is the creator, they cannot leave (they must delete the group)
        if ($groupChat->created_by === $user->id) {
            return response()->json(['error' => 'Group creator cannot leave. Please delete the group instead.'], 403);
        }
        
        // Remove user from group
        $member->update(['is_active' => false]);
        
        return response()->json(['success' => true, 'message' => 'Successfully left the group.']);
    }

    /**
     * Delete a group chat (only creator can delete).
     */
    public function deleteGroup(Request $request, $groupId)
    {
        $user = Auth::user();
        $groupChat = GroupChat::findOrFail($groupId);
        
        // Allow group deletion if user is an active member
        $isMember = $groupChat->memberships()->where('user_id', $user->id)->where('is_active', true)->exists();
        if (!$isMember) {
            return response()->json(['error' => 'Only group members can delete the group.'], 403);
        }
        
        try {
            // Soft delete the group chat
            $groupChat->update(['is_active' => false]);
            
            // Soft delete all group members
            $groupChat->members()->update(['is_active' => false]);
            
            // Hard delete all group messages (since they don't have is_active column)
            $groupChat->messages()->delete();
            
            return response()->json(['success' => true, 'message' => 'Group deleted successfully.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting group: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while deleting the group.'], 500);
        }
    }

    /**
     * Check if the current user is the creator of a group.
     */
    public function checkGroupCreator(Request $request, $groupId)
    {
        $user = Auth::user();
        $groupChat = GroupChat::findOrFail($groupId);
        
        $isCreator = $groupChat->created_by === $user->id;
        
        return response()->json(['is_creator' => $isCreator]);
    }

    /**
     * Get available groups to join.
     */
    public function getAvailableGroups()
    {
        $user = Auth::user();
        
        $groups = GroupChat::where('group_chats.is_active', true)
            ->where('type', 'public')
            ->whereDoesntHave('members', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['creator', 'members'])
            ->get();

        return response()->json(['groups' => $groups]);
    }

    /**
     * Check if user can message another user.
     */
    private function canMessageUser($sender, $receiver): bool
    {
        // Admin can message anyone
        if ($sender->role === 'admin') {
            return true;
        }

        // Same user cannot message themselves
        if ($sender->id === $receiver->id) {
            return false;
        }

        // Allow same role messaging (lecturer to lecturer, student to student)
        if ($sender->role === $receiver->role) {
            return true;
        }

        // Allow cross-role messaging (lecturer to student, student to lecturer)
        if (($sender->role === 'lecturer' && $receiver->role === 'student') ||
            ($sender->role === 'student' && $receiver->role === 'lecturer')) {
            return true;
        }

        return false;
    }

    /**
     * Determine message type based on MIME type.
     */
    private function getMessageType($mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'photo';
        }
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }
        return 'file';
    }

    /**
     * Get available users for group creation.
     */
    public function getAvailableUsers()
    {
        $user = Auth::user();
        
        // Get users based on role permissions with more flexibility
        if ($user->role === 'admin') {
            // Admin can see all users
            $users = User::where('id', '!=', $user->id)->get();
        } elseif ($user->role === 'lecturer') {
            // Lecturer can see all students and other lecturers
            $studentIds = User::where('role', 'student')->pluck('id');
            $lecturerIds = User::where('role', 'lecturer')
                ->where('id', '!=', $user->id)
                ->pluck('id');
            
            $users = User::whereIn('id', $studentIds->merge($lecturerIds))->get();
        } else {
            // Student can see all lecturers and other students
            $lecturerIds = User::where('role', 'lecturer')->pluck('id');
            $studentIds = User::where('role', 'student')
                ->where('id', '!=', $user->id)
                ->pluck('id');
            
            $users = User::whereIn('id', $lecturerIds->merge($studentIds))->get();
        }
        
        return response()->json(['users' => $users]);
    }

    /**
     * Search users by username/name for messaging.
     */
    public function searchUsers(Request $request)
    {
        $user = Auth::user();
        $searchTerm = $request->get('search', '');
        
        if (empty($searchTerm)) {
            return response()->json(['users' => []]);
        }
        
        // Search users based on role permissions
        if ($user->role === 'admin') {
            // Admin can search all users
            $users = User::where('id', '!=', $user->id)
                ->where(function($query) use ($searchTerm) {
                    $query->where('name', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('email', 'LIKE', "%{$searchTerm}%");
                })
                ->get();
        } elseif ($user->role === 'lecturer') {
            // Lecturer can search all students and other lecturers
            $studentIds = User::where('role', 'student')->pluck('id');
            $lecturerIds = User::where('role', 'lecturer')
                ->where('id', '!=', $user->id)
                ->pluck('id');
            
            $users = User::whereIn('id', $studentIds->merge($lecturerIds))
                ->where(function($query) use ($searchTerm) {
                    $query->where('name', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('email', 'LIKE', "%{$searchTerm}%");
                })
                ->get();
        } else {
            // Student can search all lecturers and other students
            $lecturerIds = User::where('role', 'lecturer')->pluck('id');
            $studentIds = User::where('role', 'student')
                ->where('id', '!=', $user->id)
                ->pluck('id');
            
            $users = User::whereIn('id', $lecturerIds->merge($studentIds))
                ->where(function($query) use ($searchTerm) {
                    $query->where('name', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('email', 'LIKE', "%{$searchTerm}%");
                })
                ->get();
        }
        
        return response()->json(['users' => $users]);
    }

    /**
     * Get available users for adding to existing group.
     */
    public function getAvailableUsersForGroup($groupId)
    {
        $user = Auth::user();
        $groupChat = GroupChat::findOrFail($groupId);
        
        // Check if user is admin of the group
        if (!$groupChat->isAdmin($user)) {
            abort(403, 'Only group admins can add members.');
        }
        
        // Get current group members
        $currentMemberIds = $groupChat->members()->pluck('users.id');
        
        // Get available users (same logic as getAvailableUsers but exclude current members)
        if ($user->role === 'admin') {
            $users = User::where('id', '!=', $user->id)
                ->whereNotIn('id', $currentMemberIds)
                ->get();
        } elseif ($user->role === 'lecturer') {
            $enrolledStudentIds = $user->taughtCourses()
                ->with('enrollments')
                ->get()
                ->flatMap(function ($course) {
                    return $course->enrollments->pluck('student_id');
                })
                ->unique();
            
            $lecturerIds = User::where('role', 'lecturer')
                ->where('id', '!=', $user->id)
                ->pluck('id');
            
            $availableIds = $enrolledStudentIds->merge($lecturerIds)->diff($currentMemberIds);
            $users = User::whereIn('id', $availableIds)->get();
        } else {
            $enrolledCourseIds = $user->enrolledCourses()->pluck('course_id');
            $lecturerIds = User::where('role', 'lecturer')
                ->whereHas('taughtCourses', function($query) use ($enrolledCourseIds) {
                    $query->whereIn('id', $enrolledCourseIds);
                })
                ->pluck('id');
            
            $studentIds = User::where('role', 'student')
                ->where('id', '!=', $user->id)
                ->pluck('id');
            
            $availableIds = $lecturerIds->merge($studentIds)->diff($currentMemberIds);
            $users = User::whereIn('id', $availableIds)->get();
        }
        
        return response()->json(['users' => $users]);
    }

    /**
     * Add members to existing group.
     */
    public function addMembersToGroup(Request $request, $groupId)
    {
        $user = Auth::user();
        $groupChat = GroupChat::findOrFail($groupId);
        
        // Check if user is admin of the group
        if (!$groupChat->isAdmin($user)) {
            abort(403, 'Only group admins can add members.');
        }
        
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);
        
        $addedMembers = [];
        $failedMembers = [];
        
        foreach ($request->user_ids as $userId) {
            $memberUser = User::find($userId);
            
            // Check if group is full
            if ($groupChat->isFull()) {
                $failedMembers[] = [
                    'user' => $memberUser,
                    'reason' => 'Group is full'
                ];
                continue;
            }
            
            // Check if user is already a member
            if ($groupChat->isMember($memberUser)) {
                $failedMembers[] = [
                    'user' => $memberUser,
                    'reason' => 'Already a member'
                ];
                continue;
            }
            
            // Add member
            $groupChat->addMember($memberUser);
            $addedMembers[] = $memberUser;
        }
        
        return response()->json([
            'success' => true,
            'added_members' => $addedMembers,
            'failed_members' => $failedMembers,
            'message' => count($addedMembers) . ' member(s) added successfully'
        ]);
    }

    /**
     * Get individual chat messages between current user and another user.
     */
    public function getIndividualMessages($userId)
    {
        $currentUser = auth()->user();
        $otherUser = User::findOrFail($userId);
        
        // Verify user can message this receiver
        if (!$this->canMessageUser($currentUser, $otherUser)) {
            abort(403, 'Unauthorized to message this user.');
        }
        
        // Get messages between the two users
        $messages = Message::where(function($query) use ($currentUser, $userId) {
            $query->where('sender_id', $currentUser->id)
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($currentUser, $userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $currentUser->id);
        })
        ->with(['sender:id,name,role'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark messages as read
        Message::where('sender_id', $userId)
               ->where('receiver_id', $currentUser->id)
               ->where('is_read', false)
               ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'messages' => $messages,
            'chat_type' => 'individual',
            'other_user_id' => $userId,
            'other_user' => $otherUser
        ]);
    }

    /**
     * Send individual message to a specific user.
     */
    public function sendIndividualMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $currentUser = auth()->user();
        $receiver = User::findOrFail($request->receiver_id);
        
        // Don't allow sending message to self
        if ($currentUser->id == $request->receiver_id) {
            return response()->json(['error' => 'Cannot send message to yourself'], 400);
        }

        // Verify user can message this receiver
        if (!$this->canMessageUser($currentUser, $receiver)) {
            return response()->json(['error' => 'Unauthorized to message this user'], 403);
        }

        $messageData = [
            'sender_id' => $currentUser->id,
            'receiver_id' => $request->receiver_id,
            'message_type' => 'text',
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('messages', $fileName, 'public');
            
            $messageData['file_path'] = $filePath;
            $messageData['file_name'] = $file->getClientOriginalName();
            $messageData['file_type'] = $file->getMimeType();
            $messageData['file_size'] = $file->getSize();
            $messageData['message_type'] = $this->getMessageType($file->getMimeType());
        }

        // Handle text content
        if ($request->filled('content')) {
            $messageData['content'] = $request->content;
        }

        // Ensure at least one of content or file is provided
        if (empty($messageData['content']) && empty($messageData['file_path'])) {
            return response()->json(['error' => 'Message must contain text or file.'], 422);
        }

        $message = Message::create($messageData);
        $message->load(['sender:id,name,role', 'receiver:id,name,role']);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Delete individual chat conversation (delete all messages between two users).
     */
    public function deleteIndividualChat(Request $request, $otherUserId)
    {
        $user = Auth::user();
        
        // Validate the other user exists
        $otherUser = User::findOrFail($otherUserId);
        
        // Users cannot delete conversations with themselves
        if ($user->id === $otherUserId) {
            return response()->json(['error' => 'Cannot delete conversation with yourself.'], 400);
        }
        
        try {
            // Delete all messages between the two users (both sent and received)
            Message::where(function($query) use ($user, $otherUserId) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $otherUserId);
            })->orWhere(function($query) use ($user, $otherUserId) {
                $query->where('sender_id', $otherUserId)
                      ->where('receiver_id', $user->id);
            })->delete();
            
            return response()->json(['success' => true, 'message' => 'Chat conversation deleted successfully.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting individual chat: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while deleting the conversation.'], 500);
        }
    }
}
