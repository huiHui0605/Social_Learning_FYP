<x-app-layout>
    <x-slot name="title">Social Learning Hub - Student</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Social Learning Hub
        </h2>
    </x-slot>

    <div class="flex h-screen bg-gray-50 overflow-hidden">
        <!-- Left Sidebar - Profile -->
        <aside class="w-80 bg-white shadow-sm border-r border-gray-200 p-6">
            <!-- Profile Header -->
            <div class="text-center mb-6">
                <div class="relative inline-block">
                    <div class="w-24 h-24 bg-green-500 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <!-- Online Status Indicator -->
                    <div class="absolute bottom-6 right-6 w-6 h-6 bg-green-500 border-4 border-white rounded-full"></div>
                </div>
                <h3 class="text-xl font-semibold text-gray-900">{{ Auth::user()->name }}</h3>
                <div class="flex items-center justify-center space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <p class="text-green-600 text-sm font-medium">Online</p>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="space-y-4">
                <div class="border-b border-gray-200 pb-4">
                    <h4 class="font-medium text-gray-900 mb-3">Profile Information</h4>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Name</p>
                                <p class="text-sm text-gray-600">{{ Auth::user()->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Email</p>
                                <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Role</p>
                                <p class="text-sm text-gray-600 capitalize">{{ Auth::user()->role }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Member Since</p>
                                <p class="text-sm text-gray-600">{{ Auth::user()->created_at->format('M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="border-b border-gray-200 pb-4">
                    <h4 class="font-medium text-gray-900 mb-3">Academic Stats</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ Auth::user()->enrolledCourses()->count() }}</div>
                            <div class="text-sm text-gray-600">Enrolled Courses</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ Auth::user()->posts()->count() }}</div>
                            <div class="text-sm text-gray-600">Posts</div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 overflow-y-auto">
            <!-- Welcome -->
            <p class="text-lg font-medium mb-6">
                Welcome, {{ Auth::user()->name }} (Student)!
            </p>

            <!-- Create Post Section -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6" id="createPostForm">
                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <textarea 
                                name="content" 
                                placeholder="What's on your mind?" 
                                class="w-full p-3 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                rows="3"
                                required
                            ></textarea>
                            
                            <!-- Media Preview -->
                            <div id="mediaPreview" class="mt-3 hidden">
                                <div class="relative inline-block">
                                    <img id="previewImage" class="max-w-xs rounded-lg" alt="Preview">
                                    <button type="button" id="removeMedia" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm hover:bg-red-600">
                                        ×
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-4">
                                <div class="flex items-center space-x-4">
                                    <!-- File Upload -->
                                    <label class="cursor-pointer flex items-center space-x-2 text-gray-600 hover:text-green-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>Photo</span>
                                        <input type="file" name="media" id="mediaInput" class="hidden" accept="image/*,video/*">
                                    </label>
                                </div>
                                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-medium">
                                    Post
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Posts Feed -->
            <div class="space-y-6" id="postsContainer">
                @foreach($posts ?? [] as $post)
                <div class="bg-white shadow-sm rounded-lg overflow-hidden" data-post-id="{{ $post->id }}">
                    <!-- Post Header -->
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr($post->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $post->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $post->time_ago }}</div>
                                </div>
                            </div>
                            @if($post->user_id === Auth::id())
                            <div class="relative">
                                <button class="text-gray-400 hover:text-gray-600" onclick="togglePostMenu({{ $post->id }})">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                    </svg>
                                </button>
                                <div id="postMenu{{ $post->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                            Delete Post
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Post Content -->
                    <div class="p-4">
                        <p class="text-gray-800 mb-4">{{ $post->content }}</p>
                        
                        @if($post->media_path)
                        <div class="mb-4">
                            @if($post->media_type === 'video')
                            <video controls class="w-full rounded-lg">
                                <source src="{{ $post->media_url }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            @else
                            <img src="{{ $post->media_url }}" alt="Post media" class="w-full rounded-lg">
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Post Stats -->
                    <div class="px-4 py-2 border-t border-gray-100 bg-gray-50">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <div class="flex items-center space-x-4">
                                <span id="likesCount{{ $post->id }}">{{ $post->likes_count }} likes</span>
                                <span id="commentsCount{{ $post->id }}">{{ $post->comments_count }} comments</span>
                                <span id="sharesCount{{ $post->id }}">{{ $post->shares_count }} shares</span>
                            </div>
                        </div>
                    </div>

                    <!-- Post Actions -->
                    <div class="px-4 py-2 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <button 
                                onclick="toggleLike({{ $post->id }})" 
                                class="flex-1 flex items-center justify-center space-x-2 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                id="likeButton{{ $post->id }}"
                            >
                                <svg class="w-5 h-5" fill="{{ $post->isLikedBy(Auth::user()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                </svg>
                                <span id="likeText{{ $post->id }}">{{ $post->isLikedBy(Auth::user()) ? 'Liked' : 'Like' }}</span>
                            </button>
                            
                            <button 
                                onclick="toggleComments({{ $post->id }})" 
                                class="flex-1 flex items-center justify-center space-x-2 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span id="commentButtonText{{ $post->id }}">{{ $post->comments_count > 0 ? $post->comments_count . ' Comments' : 'Comment' }}</span>
                            </button>

                            <button 
                                onclick="sharePost({{ $post->id }})" 
                                class="flex-1 flex items-center justify-center space-x-2 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                </svg>
                                <span>Share</span>
                            </button>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div id="commentsSection{{ $post->id }}" class="hidden border-t border-gray-100">
                        <div class="p-4">
                            <!-- Add Comment -->
                            <div class="flex items-start space-x-3 mb-4">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <form onsubmit="addComment(event, {{ $post->id }})" class="flex space-x-2">
                                        <input 
                                            type="text" 
                                            placeholder="Write a comment..." 
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                            id="commentInput{{ $post->id }}"
                                            required
                                        >
                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-full hover:bg-green-700 text-sm">
                                            Post
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Comments List -->
                            <div id="commentsList{{ $post->id }}" class="space-y-3">
                                @foreach($post->comments as $comment)
                                <div class="flex items-start space-x-3" id="comment{{ $comment->id }}">
                                    <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                        {{ substr($comment->user->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-gray-100 rounded-lg px-3 py-2">
                                            <div class="flex items-center justify-between">
                                                <div class="font-semibold text-sm">{{ $comment->user->name }}</div>
                                                @if($comment->user_id === Auth::id() || $post->user_id === Auth::id())
                                                <button 
                                                    onclick="deleteComment({{ $comment->id }}, {{ $post->id }})" 
                                                    class="text-red-500 hover:text-red-700 text-xs"
                                                    title="Delete comment"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                                @endif
                                            </div>
                                            <div class="text-gray-800">{{ $comment->content }}</div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">{{ $comment->time_ago }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Comment Preview (shown when comments exist but section is collapsed) -->
                    @if($post->comments_count > 0)
                    <div id="commentPreview{{ $post->id }}" class="border-t border-gray-100 p-3 bg-gray-50">
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-gray-400 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                {{ substr($post->comments->first()->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="text-sm">
                                    <span class="font-semibold text-gray-700">{{ $post->comments->first()->user->name }}</span>
                                    <span class="text-gray-600">{{ Str::limit($post->comments->first()->content, 100) }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $post->comments->first()->time_ago }}
                                    @if($post->comments_count > 1)
                                        • <span class="text-green-600 cursor-pointer" onclick="toggleComments({{ $post->id }})">View all {{ $post->comments_count }} comments</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- No Posts Message -->
            @if(empty($posts ?? []))
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">📝</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No posts yet</h3>
                <p class="text-gray-500">Be the first to share something!</p>
            </div>
            @endif
        </main>

        <!-- Right Sidebar - Navigation & Online Users -->
        <aside class="w-64 bg-white shadow-sm border-l border-gray-200 p-6">
            <!-- Logo/Brand -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-green-600">E-Learning</h2>
                <p class="text-sm text-gray-600">Student Portal</p>
            </div>

            <!-- Online Users -->
            <div class="mb-8">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Online Users</h3>
                <div class="space-y-3">
                    @forelse($onlineUsers ?? [] as $user)
                    <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50">
                        <div class="relative">
                            <div class="w-8 h-8 bg-{{ $user->role === 'lecturer' ? 'blue' : 'green' }}-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-green-600">{{ ucfirst($user->role) }} • Online</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-500">No other users online</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="mb-8">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Academic Stats</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-gray-700">Enrolled Courses</span>
                        </div>
                        <span class="font-semibold text-green-600">{{ Auth::user()->enrolledCourses()->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-sm text-gray-700">Total Posts</span>
                        </div>
                        <span class="font-semibold text-blue-600">{{ Auth::user()->posts()->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                            <span class="text-sm text-gray-700">Assessments</span>
                        </div>
                        <span class="font-semibold text-purple-600">5</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mb-8">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Recent Activity</h3>
                <div class="space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-700">Enrolled in new course</p>
                            <p class="text-xs text-gray-500">2 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-700">Submitted assignment</p>
                            <p class="text-xs text-gray-500">4 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-700">Post shared</p>
                            <p class="text-xs text-gray-500">6 hours ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <!-- Share Modal -->
    <div id="shareModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Share Post</h3>
                    <button onclick="closeShareModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <input type="hidden" id="sharePostId" value="">
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-3">Select users to share this post with via chat:</p>
                    <div id="shareUserList" class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg">
                        <!-- Users will be loaded here -->
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeShareModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Cancel
                    </button>
                    <button onclick="sendSharedPost()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Share
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Media upload preview
        document.getElementById('mediaInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                    document.getElementById('mediaPreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('removeMedia').addEventListener('click', function() {
            document.getElementById('mediaInput').value = '';
            document.getElementById('mediaPreview').classList.add('hidden');
        });

        // Post menu toggle
        function togglePostMenu(postId) {
            const menu = document.getElementById(`postMenu${postId}`);
            menu.classList.toggle('hidden');
        }

        // Like/Unlike functionality
        function toggleLike(postId) {
            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`likesCount${postId}`).textContent = `${data.likes_count} likes`;
                    document.getElementById(`likeText${postId}`).textContent = data.is_liked ? 'Liked' : 'Like';
                    
                    const likeButton = document.getElementById(`likeButton${postId}`);
                    const svg = likeButton.querySelector('svg');
                    if (data.is_liked) {
                        svg.setAttribute('fill', 'currentColor');
                        likeButton.classList.add('text-green-600');
                    } else {
                        svg.setAttribute('fill', 'none');
                        likeButton.classList.remove('text-green-600');
                    }
                }
            });
        }

        // Toggle comments section
        function toggleComments(postId) {
            const commentsSection = document.getElementById(`commentsSection${postId}`);
            const commentPreview = document.getElementById(`commentPreview${postId}`);
            
            commentsSection.classList.toggle('hidden');
            
            // Hide comment preview when comments section is shown
            if (commentPreview) {
                commentPreview.classList.toggle('hidden');
            }
        }

        // Add comment
        function addComment(event, postId) {
            event.preventDefault();
            const input = document.getElementById(`commentInput${postId}`);
            const content = input.value.trim();
            
            if (!content) return;

            fetch(`/posts/${postId}/comment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ content: content }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add new comment to the list
                    const commentsList = document.getElementById(`commentsList${postId}`);
                    const newComment = document.createElement('div');
                    newComment.className = 'flex items-start space-x-3';
                    newComment.innerHTML = `
                        <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            ${data.comment.user_name.charAt(0)}
                        </div>
                        <div class="flex-1">
                            <div class="bg-gray-100 rounded-lg px-3 py-2">
                                <div class="font-semibold text-sm">${data.comment.user_name}</div>
                                <div class="text-gray-800">${data.comment.content}</div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">${data.comment.time_ago}</div>
                        </div>
                    `;
                    commentsList.appendChild(newComment);
                    
                    // Update comment count
                    document.getElementById(`commentsCount${postId}`).textContent = `${data.comments_count} comments`;
                    
                    // Update comment button text
                    const commentButtonText = document.getElementById(`commentButtonText${postId}`);
                    if (commentButtonText) {
                        commentButtonText.textContent = data.comments_count > 0 ? `${data.comments_count} Comments` : 'Comment';
                    }
                    
                    // Clear input
                    input.value = '';
                }
            });
        }

        // Share post
        function sharePost(postId) {
            // Show the share modal
            document.getElementById('shareModal').classList.remove('hidden');
            document.getElementById('sharePostId').value = postId;
            
            // Load users for sharing
            loadUsersForSharing();
        }

        // Load users for sharing
        function loadUsersForSharing() {
            fetch('/api/users-for-sharing')
                .then(response => response.json())
                .then(data => {
                    const userList = document.getElementById('shareUserList');
                    userList.innerHTML = '';
                    
                    if (data.users.length === 0) {
                        userList.innerHTML = '<p class="text-gray-500 text-center py-4">No users available for sharing</p>';
                        return;
                    }
                    
                    data.users.forEach(user => {
                        const userDiv = document.createElement('div');
                        userDiv.className = 'flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg cursor-pointer';
                        userDiv.onclick = () => selectUserForSharing(user.id, user.name, user.role);
                        
                        const roleColor = user.role === 'lecturer' ? 'blue' : 'green';
                        userDiv.innerHTML = `
                            <div class="w-10 h-10 bg-${roleColor}-500 rounded-full flex items-center justify-center text-white font-semibold">
                                ${user.name.charAt(0)}
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">${user.name}</div>
                                <div class="text-sm text-gray-500 capitalize">${user.role}</div>
                            </div>
                            <div class="w-5 h-5 border-2 border-gray-300 rounded-full" id="userSelect${user.id}"></div>
                        `;
                        userList.appendChild(userDiv);
                    });
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                    document.getElementById('shareUserList').innerHTML = '<p class="text-red-500 text-center py-4">Error loading users</p>';
                });
        }

        // Select user for sharing
        function selectUserForSharing(userId, userName, userRole) {
            const checkbox = document.getElementById(`userSelect${userId}`);
            const isSelected = checkbox.classList.contains('bg-green-500');
            
            if (isSelected) {
                checkbox.classList.remove('bg-green-500', 'border-green-500');
                checkbox.classList.add('border-gray-300');
            } else {
                checkbox.classList.add('bg-green-500', 'border-green-500');
                checkbox.classList.remove('border-gray-300');
            }
        }

        // Send shared post to selected users
        function sendSharedPost() {
            const postId = document.getElementById('sharePostId').value;
            const selectedUsers = [];
            
            console.log('Starting share process for post:', postId);
            
            // Get all selected users
            document.querySelectorAll('[id^="userSelect"]').forEach(checkbox => {
                if (checkbox.classList.contains('bg-green-500')) {
                    const userId = checkbox.id.replace('userSelect', '');
                    selectedUsers.push(userId);
                }
            });
            
            console.log('Selected users:', selectedUsers);
            
            if (selectedUsers.length === 0) {
                alert('Please select at least one user to share with.');
                return;
            }
            
            const requestData = { user_ids: selectedUsers };
            console.log('Request data:', requestData);
            
            fetch(`/posts/${postId}/share-to-users`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData),
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    document.getElementById(`sharesCount${postId}`).textContent = `${data.shares_count} shares`;
                    alert(`Post shared successfully with ${selectedUsers.length} user(s)!`);
                    closeShareModal();
                } else {
                    alert(data.message || 'Failed to share post');
                }
            })
            .catch(error => {
                console.error('Error sharing post:', error);
                if (error.response) {
                    error.response.json().then(data => {
                        alert('Failed to share post: ' + (data.message || error.message));
                    }).catch(() => {
                        alert('Failed to share post: ' + error.message);
                    });
                } else {
                    alert('Failed to share post: ' + error.message);
                }
            });
        }

        // Close share modal
        function closeShareModal() {
            document.getElementById('shareModal').classList.add('hidden');
            document.getElementById('sharePostId').value = '';
        }

        // Delete comment
        function deleteComment(commentId, postId) {
            if (confirm('Are you sure you want to delete this comment?')) {
                fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove comment from the list
                        const commentElement = document.getElementById(`comment${commentId}`);
                        if (commentElement) {
                            commentElement.remove();
                        }
                        
                        // Update comment count
                        document.getElementById(`commentsCount${postId}`).textContent = `${data.comments_count} comments`;
                        
                        // Update comment button text
                        const commentButtonText = document.getElementById(`commentButtonText${postId}`);
                        if (commentButtonText) {
                            commentButtonText.textContent = data.comments_count > 0 ? `${data.comments_count} Comments` : 'Comment';
                        }
                    } else {
                        alert(data.message || 'Failed to delete comment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete comment');
                });
            }
        }

        // Close post menus when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.relative')) {
                document.querySelectorAll('[id^="postMenu"]').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        // Refresh online users every 30 seconds
        function refreshOnlineUsers() {
            fetch('/api/online-users')
                .then(response => response.json())
                .then(data => {
                    const onlineUsersContainer = document.querySelector('.mb-8 .space-y-3');
                    if (onlineUsersContainer && data.users) {
                        let html = '';
                        if (data.users.length === 0) {
                            html = '<div class="text-center py-4"><p class="text-sm text-gray-500">No other users online</p></div>';
                        } else {
                            data.users.forEach(user => {
                                const roleColor = user.role === 'lecturer' ? 'blue' : 'green';
                                html += `
                                    <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50">
                                        <div class="relative">
                                            <div class="w-8 h-8 bg-${roleColor}-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                                ${user.name.charAt(0)}
                                            </div>
                                            <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">${user.name}</p>
                                            <p class="text-xs text-green-600">${user.role.charAt(0).toUpperCase() + user.role.slice(1)} • Online</p>
                                        </div>
                                    </div>
                                `;
                            });
                        }
                        onlineUsersContainer.innerHTML = html;
                    }
                })
                .catch(error => console.error('Error refreshing online users:', error));
        }

        // Refresh online users every 30 seconds
        setInterval(refreshOnlineUsers, 30000);
    </script>
</x-app-layout>
