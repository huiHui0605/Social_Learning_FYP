<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     */
    public function index()
    {
        // Get all posts with user and comment relationships
        $posts = Post::with(['user', 'comments.user', 'likes'])
            ->latest()
            ->get();

        // Get online users
        $onlineUsers = User::getOnlineUsers();

        // Update current user's last seen
        Auth::user()->updateLastSeen();

        // Determine which view to return based on user role
        $user = Auth::user();
        $view = match($user->role) {
            'student' => 'student.dashboard',
            'lecturer' => 'lecturer.dashboard',
            default => 'lecturer.dashboard'
        };

        return view($view, compact('posts', 'onlineUsers'));
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:102400', // 100MB max
        ]);

        $post = new Post();
        $post->content = $request->content;
        $post->user_id = Auth::id();

        // Handle media upload
        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Determine media type
            $mediaType = in_array($file->getClientOriginalExtension(), ['mp4', 'mov', 'avi']) ? 'video' : 'image';
            $post->media_type = $mediaType;
            
            // Store file
            $path = $file->storeAs('posts', $fileName, 'public');
            $post->media_path = $path;
        }

        $post->save();

        return redirect()->back()->with('success', 'Post created successfully!');
    }

    /**
     * Like or unlike a post.
     */
    public function toggleLike(Post $post)
    {
        $user = Auth::user();
        $existingLike = PostLike::where('post_id', $post->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $post->decrement('likes_count');
            $message = 'Post unliked';
        } else {
            // Like
            PostLike::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
            ]);
            $post->increment('likes_count');
            $message = 'Post liked';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'likes_count' => $post->fresh()->likes_count,
            'is_liked' => !$existingLike,
        ]);
    }

    /**
     * Add a comment to a post.
     */
    public function comment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $comment = PostComment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        $post->increment('comments_count');

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user_name' => $comment->user->name,
                'time_ago' => $comment->time_ago,
            ],
            'comments_count' => $post->fresh()->comments_count,
        ]);
    }

    /**
     * Share a post (increment share count).
     */
    public function share(Post $post)
    {
        $post->increment('shares_count');

        return response()->json([
            'success' => true,
            'message' => 'Post shared successfully!',
            'shares_count' => $post->fresh()->shares_count,
        ]);
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post)
    {
        // Check if user owns the post
        if ($post->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only delete your own posts.');
        }

        // Delete media file if exists
        if ($post->media_path && Storage::disk('public')->exists($post->media_path)) {
            Storage::disk('public')->delete($post->media_path);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Post deleted successfully!');
    }

    /**
     * Delete a comment.
     */
    public function deleteComment(PostComment $comment)
    {
        // Check if user owns the comment or the post
        if ($comment->user_id !== Auth::id() && $comment->post->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own comments or comments on your posts.'
            ], 403);
        }

        $post = $comment->post;
        $comment->delete();
        $post->decrement('comments_count');

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully!',
            'comments_count' => $post->fresh()->comments_count,
        ]);
    }

    /**
     * Get users available for sharing posts.
     */
    public function getUsersForSharing()
    {
        $currentUser = Auth::user();
        
        // Get all users except the current user
        $users = User::where('id', '!=', $currentUser->id)
            ->select('id', 'name', 'role')
            ->get();
        
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    /**
     * Test method to verify share functionality.
     */
    public function testShare($postId)
    {
        try {
            $post = Post::find($postId);
            $currentUser = Auth::user();
            
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'post' => [
                    'id' => $post->id,
                    'content' => $post->content,
                    'shares_count' => $post->shares_count
                ],
                'current_user' => [
                    'id' => $currentUser->id,
                    'name' => $currentUser->name
                ],
                'available_users' => User::where('id', '!=', $currentUser->id)
                    ->select('id', 'name', 'role')
                    ->get()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Share a post to specific users via chat.
     */
    public function shareToUsers(Request $request, $postId)
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to share posts'
                ], 401);
            }

            // Find the post manually to avoid route model binding issues
            $post = Post::find($postId);
            
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }
            
            $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'integer|exists:users,id'
            ]);

            $currentUser = Auth::user();
            $userIds = $request->user_ids;
            
            // Filter out invalid user IDs
            $validUserIds = array_filter($userIds, function($userId) {
                return User::where('id', $userId)->exists();
            });
            
            if (empty($validUserIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid users selected to share with'
                ], 400);
            }
            
            // Create messages for each selected user
            $messagesCreated = 0;
            foreach ($validUserIds as $userId) {
                // Skip if trying to share with self
                if ($userId == $currentUser->id) {
                    continue;
                }
                
                // Create a message with the shared post content
                $messageContent = "Shared post from {$currentUser->name}:\n\n{$post->content}";
                
                if ($post->media_path) {
                    $messageContent .= "\n\n[Post includes media]";
                }
                
                // Create the message
                $message = \App\Models\Message::create([
                    'sender_id' => $currentUser->id,
                    'receiver_id' => $userId,
                    'content' => $messageContent,
                    'message_type' => 'shared_post',
                    'related_post_id' => $post->id
                ]);
                
                $messagesCreated++;
            }
            
            // Increment the post's share count
            $post->increment('shares_count');
            
            return response()->json([
                'success' => true,
                'shares_count' => $post->fresh()->shares_count,
                'message' => "Post shared with " . $messagesCreated . " user(s)",
                'users_shared_with' => $messagesCreated
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to share post: ' . $e->getMessage()
            ], 500);
        }
    }
}
