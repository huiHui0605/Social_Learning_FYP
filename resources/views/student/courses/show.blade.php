<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            {{ $course->title }} <span class="text-base font-normal text-gray-500">({{ $course->course_code }})</span>
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8" x-data="{ 
        tab: '{{ request()->get('tab', 'general') }}' 
    }">
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
                            <div class="mb-2"><span class="font-semibold">Your Enrollment:</span> 
                                <span class="text-blue-600 font-bold">{{ ucfirst($enrollment->status) }}</span>
                            </div>
                            <div class="mb-2"><span class="font-semibold">Enrolled Date:</span> 
                                <span class="text-gray-600">{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Course Description and Classmates -->
                    <div class="md:col-span-2">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Course Description</h3>
                            <p class="text-gray-700 leading-relaxed">{{ $course->description ?? 'No description available.' }}</p>
                        </div>
                        
                        <!-- Classmates Section -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Your Classmates</h3>
                            @php
                                $classmates = $course->enrollments()->with('student')->where('student_id', '!=', Auth::id())->where('is_active', true)->get();
                            @endphp
                            @if($classmates->count() > 0)
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
                                                @foreach($classmates as $classmateEnrollment)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="flex items-center">
                                                                <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                                    {{ substr($classmateEnrollment->student->name, 0, 1) }}
                                                                </div>
                                                                <div class="ml-3">
                                                                    <div class="text-sm font-medium text-gray-900">{{ $classmateEnrollment->student->name }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $classmateEnrollment->student->email }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            @php
                                                                $statusColors = [
                                                                    'enrolled' => 'bg-green-100 text-green-800',
                                                                    'completed' => 'bg-blue-100 text-blue-800',
                                                                    'dropped' => 'bg-red-100 text-red-800'
                                                                ];
                                                                $statusColor = $statusColors[$classmateEnrollment->status] ?? 'bg-gray-100 text-gray-800';
                                                            @endphp
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                                {{ ucfirst($classmateEnrollment->status) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $classmateEnrollment->enrolled_at ? $classmateEnrollment->enrolled_at->format('M d, Y') : 'N/A' }}
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
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No classmates yet</h3>
                                    <p class="mt-1 text-sm text-gray-500">You're the first student to join this course!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignments Tab -->
            <div x-show="tab === 'assignments'">
                <h3 class="text-lg font-semibold mb-2">Assignments</h3>
                
                @if($assessments->count() === 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-yellow-800">No Assignments Available</h4>
                                <div class="mt-1 text-sm text-yellow-700">
                                    <p>Your lecturer hasn't published any assignments for this course yet. Assignments will appear here once they are published.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @forelse($assessments as $assessment)
                    <div class="mb-4 p-4 bg-gray-100 rounded">
                        <div class="flex justify-between items-start mb-2">
                            <div class="font-bold">{{ $assessment->title }}</div>
                            <a href="{{ route('student.assessments.show', $assessment) }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">View Assignment</a>
                        </div>
                        <div class="text-sm text-gray-600 mb-2">{{ Str::limit($assessment->description, 200) }}</div>
                        <div class="text-xs text-gray-500 mb-2">
                            Due: {{ $assessment->due_date ? $assessment->due_date->format('M d, Y H:i') : 'No due date' }} | 
                            Total Marks: {{ $assessment->total_marks }}
                        </div>
                        @if($assessment->file_url)
                            <a href="{{ $assessment->file_url }}" class="text-indigo-700 underline" target="_blank">Download Assignment File</a>
                        @endif

                        @php
                            $submission = $assessment->submissions->where('student_id', Auth::id())->first();
                        @endphp

                        @if($submission)
                            <div class="mt-2">
                                <span class="text-green-600 font-semibold">Submitted</span>
                                <a href="{{ route('student.assessments.view-submission', $submission) }}" class="text-indigo-700 underline ml-2">View Your Submission</a>
                            </div>
                            @if($submission->marks_obtained !== null)
                                <div class="mt-2 text-blue-700 font-bold">Grade: {{ $submission->marks_obtained }}/{{ $assessment->total_marks }} ({{ $submission->percentage }}%)</div>
                                @if($submission->feedback)
                                    <div class="mt-1 text-gray-700">Feedback: {{ $submission->feedback }}</div>
                                @endif
                            @else
                                <div class="mt-2 text-yellow-600">Pending grading</div>
                            @endif
                        @else
                            <!-- Submission Form -->
                            <form action="{{ route('student.assessments.submit', $assessment) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                                @csrf
                                <div class="mb-3">
                                    <label for="submission_content" class="block text-sm font-medium text-gray-700 mb-1">Your Answer</label>
                                    <textarea name="submission_content" id="submission_content" rows="4" required 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                              placeholder="Write your answer here..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">Attachment (Optional)</label>
                                    <input type="file" name="attachment" id="attachment" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                                    <p class="text-xs text-gray-500 mt-1">Supported formats: PDF, DOC, DOCX, TXT, JPG, PNG (Max 10MB)</p>
                                </div>
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Submit Assignment</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="text-gray-400">No assignments yet.</div>
                @endforelse
            </div>

            <!-- Materials Tab -->
            <div x-show="tab === 'materials'">
                <h3 class="text-lg font-semibold mb-2">Course Materials</h3>
                @forelse($materials as $material)
                    <div class="mb-2 p-3 bg-gray-100 rounded">
                        <div class="flex justify-between items-center">
                            <div>
                                <a href="{{ route('student.materials.download', $material) }}" class="text-indigo-700 font-bold hover:text-indigo-900">
                                    {{ $material->title }}
                                </a>
                                @if($material->description)
                                    <p class="text-sm text-gray-600 mt-1">{{ $material->description }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">
                                    File: {{ $material->file_name }} ({{ $material->file_size_human }})
                                </p>
                            </div>
                            <div class="text-right">
                                <a href="{{ route('student.materials.download', $material) }}" 
                                   class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-400">No course materials yet.</div>
                @endforelse
            </div>

            <!-- Posting Tab -->
            <div x-show="tab === 'posts'">
                <h3 class="text-lg font-semibold mb-2">Course Posts</h3>
                @forelse($posts as $post)
                    <div class="bg-white rounded shadow p-4 flex items-start space-x-4 mb-6 border border-gray-100">
                        <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr($post->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-bold">{{ $post->user->name }}</span>
                                <span class="text-xs text-gray-400">{{ $post->time_ago ?? $post->created_at->diffForHumans() }}</span>
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
                                <span>{{ $post->likes_count }} likes</span>
                                <span>{{ $post->comments_count }} comments</span>
                                <span>{{ $post->shares_count }} shares</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-12 w-full">No posts yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Alpine.js for tab switching -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</x-app-layout> 