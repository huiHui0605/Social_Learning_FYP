<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                {{ $course->title }} <span class="text-base font-normal text-gray-500">({{ $course->course_code }})</span>
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('lecturer.courses.edit', $course) }}" 
                   class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm shadow">
                    Edit Course
                </a>
                <a href="{{ route('lecturer.courses.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm shadow">
                    ← Back to Courses
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8" x-data="{ tab: 'general' }">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tab Navigation -->
        <nav class="flex space-x-2 border-b-2 border-gray-200 mb-6 bg-white rounded-t-lg shadow-sm">
            <button class="px-6 py-3 text-sm font-semibold focus:outline-none"
                :class="tab === 'general' ? 'border-b-4 border-indigo-600 text-indigo-700 bg-gray-50' : 'text-gray-600 hover:bg-gray-100'"
                @click="tab = 'general'">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                General
            </button>
            <button class="px-6 py-3 text-sm font-semibold focus:outline-none"
                :class="tab === 'assignments' ? 'border-b-4 border-indigo-600 text-indigo-700 bg-gray-50' : 'text-gray-600 hover:bg-gray-100'"
                @click="tab = 'assignments'">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Assignments
            </button>
            <button class="px-6 py-3 text-sm font-semibold focus:outline-none"
                :class="tab === 'materials' ? 'border-b-4 border-indigo-600 text-indigo-700 bg-gray-50' : 'text-gray-600 hover:bg-gray-100'"
                @click="tab = 'materials'">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 20h9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19.5 3 21l1.5-4L16.5 3.5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Materials
            </button>
            <button class="px-6 py-3 text-sm font-semibold focus:outline-none"
                :class="tab === 'posts' ? 'border-b-4 border-indigo-600 text-indigo-700 bg-gray-50' : 'text-gray-600 hover:bg-gray-100'"
                @click="tab = 'posts'">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8h2a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V10a2 2 0 012-2h2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 3h-6a2 2 0 00-2 2v3a2 2 0 002 2h6a2 2 0 002-2V5a2 2 0 00-2-2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Posting
            </button>
        </nav>

        <!-- Tab Panels -->
        <div class="bg-white rounded-b-lg shadow-sm p-6 min-h-[300px]">
            <!-- General Tab -->
            <div x-show="tab === 'general'">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Course Image and Info -->
                    <div class="md:col-span-1">
                        <img src="{{ $course->image_url }}" alt="{{ $course->title }}" class="w-full h-48 object-cover rounded shadow mb-4">
                        <div class="bg-gray-50 rounded p-4">
                            <div class="mb-2"><span class="font-semibold">Semester:</span> {{ $course->semester }}</div>
                            <div class="mb-2"><span class="font-semibold">Academic Year:</span> {{ $course->academic_year }}</div>
                            <div class="mb-2"><span class="font-semibold">Lecturer:</span> {{ $course->lecturer->name ?? 'N/A' }}</div>
                            <div class="mb-2"><span class="font-semibold">Created:</span> {{ $course->created_at->format('M d, Y') }}</div>
                            <div class="mb-2"><span class="font-semibold">Status:</span> 
                                <span class="{{ $course->is_active ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $course->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="mb-2"><span class="font-semibold">Enrolled Students:</span> 
                                <span class="text-blue-600 font-bold">{{ $enrollments->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Course Description and Enrolled Students -->
                    <div class="md:col-span-2">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Course Description</h3>
                            <p class="text-gray-700 leading-relaxed">{{ $course->description ?? 'No description available.' }}</p>
                        </div>
                        
                        <!-- Enrolled Students Section -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Enrolled Students</h3>
                            @if($enrollments->count() > 0)
                                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrolled Date</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($enrollments as $enrollment)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="flex items-center">
                                                                <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                                    {{ substr($enrollment->student->name, 0, 1) }}
                                                                </div>
                                                                <div class="ml-3">
                                                                    <div class="text-sm font-medium text-gray-900">{{ $enrollment->student->name }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $enrollment->student->email }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            @php
                                                                $statusColors = [
                                                                    'enrolled' => 'bg-green-100 text-green-800',
                                                                    'completed' => 'bg-blue-100 text-blue-800',
                                                                    'dropped' => 'bg-red-100 text-red-800'
                                                                ];
                                                                $statusColor = $statusColors[$enrollment->status] ?? 'bg-gray-100 text-gray-800';
                                                            @endphp
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                                {{ ucfirst($enrollment->status) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'N/A' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No students enrolled</h3>
                                    <p class="mt-1 text-sm text-gray-500">Students will appear here once they join the course.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignments Tab -->
            <div x-show="tab === 'assignments'">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Assignments</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('L.assessment.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Assessment Center</a>
                        <a href="{{ route('L.assessment.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">Create Assignment</a>
                    </div>
                </div>
                
                <!-- Workflow Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">Assignment Workflow</h4>
                            <div class="mt-1 text-sm text-blue-700">
                                <p><strong>Draft:</strong> Only you can see this assignment. Students cannot access it yet.</p>
                                <p><strong>Published:</strong> Students can view and submit this assignment.</p>
                                <p><strong>Closed:</strong> Students can no longer submit, but can view their submissions.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @forelse($course->assessments as $assessment)
                    <div class="mb-4 p-4 bg-gray-100 rounded">
                        <div class="flex justify-between items-start mb-2">
                            <div class="font-bold">{{ $assessment->title }}</div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $assessment->status_color }}-100 text-{{ $assessment->status_color }}-800">
                                    {{ ucfirst($assessment->status) }}
                                </span>
                                @if($assessment->status === 'draft')
                                    <span class="text-xs text-red-600 font-semibold">(Students cannot see this)</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 mb-2">{{ Str::limit($assessment->description, 200) }}</div>
                        <div class="text-xs text-gray-500 mb-2">
                            Due: {{ $assessment->due_date ? $assessment->due_date->format('M d, Y H:i') : 'No due date' }} | 
                            Total Marks: {{ $assessment->total_marks }}
                        </div>
                        @if($assessment->file_url)
                            <a href="{{ $assessment->file_url }}" class="text-indigo-700 underline" target="_blank">Download Assignment File</a>
                        @endif
                        <div class="mt-2 flex space-x-2">
                            <a href="{{ route('L.assessment.edit', $assessment) }}?return_to_course={{ $course->id }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Edit</a>
                            @if($assessment->status === 'draft')
                                <form action="{{ route('L.assessment.publish', $assessment) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="return_to_course" value="{{ $course->id }}">
                                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Publish</button>
                                </form>
                            @elseif($assessment->status === 'published')
                                <a href="{{ route('L.assessment.submissions', $assessment) }}?return_to_course={{ $course->id }}" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">View Submissions</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-gray-400">No assignments yet.</div>
                @endforelse
            </div>

            <!-- Materials Tab -->
            <div x-show="tab === 'materials'">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Course Materials</h3>
                    <a href="{{ route('lecturer.courses.materials.upload.form', $course) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">
                        Upload Material
                    </a>
                </div>
                @forelse($materials as $material)
                    <div class="mb-2 p-3 bg-gray-100 rounded">
                        <div class="flex justify-between items-center">
                            <a href="{{ $material->file_url }}" class="text-indigo-700 font-bold" target="_blank">{{ $material->title }}</a>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('lecturer.courses.materials.edit', [$course, $material]) }}" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600">Edit</a>
                                <form action="{{ route('lecturer.courses.materials.delete', [$course, $material]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this material?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-400">No course materials yet.</div>
                @endforelse
            </div>

            <!-- Posting Tab -->
            <div x-show="tab === 'posts'">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Course Posts</h3>
                    <a href="#" onclick="showPostForm()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">
                        Create Post
                    </a>
                </div>
                @forelse($posts as $post)
                    <div class="bg-white rounded shadow p-4 flex items-start space-x-4 mb-6 border border-gray-100">
                        <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr($post->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-bold">{{ $post->user->name }}</span>
                                <span class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="mt-2 text-gray-800">{{ $post->content }}</div>
                            @if($post->media_path)
                                <div class="mt-2">
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
                            <div class="flex items-center justify-between text-xs text-gray-500 mt-2">
                                <span>{{ $post->likes_count ?? 0 }} likes</span>
                                <span>{{ $post->comments_count ?? 0 }} comments</span>
                                <span>{{ $post->shares_count ?? 0 }} shares</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-12">No posts yet.</div>
                @endforelse

                <!-- Post Creation Form (Hidden by default) -->
                <div id="postForm" class="hidden mt-6 p-4 bg-gray-50 rounded border">
                    <h4 class="text-lg font-semibold mb-4">Create New Post</h4>
                    <form action="{{ route('course.posts.store', $course) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Post Content</label>
                            <textarea name="content" id="content" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="What would you like to share with your students?" required></textarea>
                        </div>
                        <div class="flex space-x-3">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Create Post</button>
                            <button type="button" onclick="hidePostForm()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showPostForm() {
            document.getElementById('postForm').classList.remove('hidden');
        }
        
        function hidePostForm() {
            document.getElementById('postForm').classList.add('hidden');
        }
    </script>

    <!-- Alpine.js for tab switching -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</x-app-layout> 