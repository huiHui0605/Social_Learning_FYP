<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Assessments - {{ $course->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('L.assessment.create') }}?course_id={{ $course->id }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm shadow">
                    + Create Assessment
                </a>
                <a href="{{ route('lecturer.courses.show', $course) }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                    ← Back to Course
                </a>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <!-- Course Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $course->title }}</h3>
                        <p class="text-sm text-gray-600">{{ $course->code }} - {{ $course->semester }}</p>
                        @if($course->description)
                        <p class="text-sm text-gray-500 mt-2">{{ Str::limit($course->description, 200) }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">{{ $assessments->count() }} Assessment(s)</div>
                        <div class="text-sm text-gray-500">{{ $assessments->sum('submissions_count') }} Total Submissions</div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if($assessments->count() > 0)
        <div class="space-y-4">
            @foreach($assessments as $assessment)
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $assessment->title }}</h3>
                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $assessment->status_color }}-100 text-{{ $assessment->status_color }}-800">
                                {{ ucfirst($assessment->status) }}
                            </span>
                            @if($assessment->isOverdue())
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                Overdue
                            </span>
                            @endif
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($assessment->description, 150) }}</p>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Due Date:</span>
                                <span class="font-medium">{{ $assessment->formatted_due_date }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Total Marks:</span>
                                <span class="font-medium">{{ $assessment->total_marks }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Submissions:</span>
                                <span class="font-medium">{{ $assessment->submissions_count }} ({{ $assessment->graded_count }} graded)</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Created:</span>
                                <span class="font-medium">{{ $assessment->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col space-y-2 ml-4">
                        <a href="{{ route('L.assessment.show', $assessment) }}?return_to_course={{ $course->id }}"
                           class="bg-blue-600 text-white text-sm px-3 py-2 rounded hover:bg-blue-700 text-center">
                            View Details
                        </a>
                        
                        @if($assessment->submissions_count > 0)
                        <a href="{{ route('L.assessment.submissions', $assessment) }}?return_to_course={{ $course->id }}"
                           class="bg-gray-200 text-gray-800 text-sm px-3 py-2 rounded hover:bg-gray-300 text-center">
                            View Submissions ({{ $assessment->submissions_count }})
                        </a>
                        @endif
                        
                        @if($assessment->status === 'draft')
                        <form action="{{ route('L.assessment.publish', $assessment) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="return_to_course" value="{{ $course->id }}">
                            <button type="submit" class="w-full bg-green-600 text-white text-sm px-3 py-2 rounded hover:bg-green-700">
                                Publish
                            </button>
                        </form>
                        @endif
                        
                        @if($assessment->status === 'published')
                        <form action="{{ route('L.assessment.close', $assessment) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="return_to_course" value="{{ $course->id }}">
                            <button type="submit" class="w-full bg-red-600 text-white text-sm px-3 py-2 rounded hover:bg-red-700">
                                Close
                            </button>
                        </form>
                        @endif
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('L.assessment.edit', $assessment) }}"
                               class="bg-yellow-600 text-white text-sm px-3 py-1 rounded hover:bg-yellow-700">
                                Edit
                            </a>
                            <form action="{{ route('L.assessment.destroy', $assessment) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this assessment?')"
                                        class="bg-red-600 text-white text-sm px-3 py-1 rounded hover:bg-red-700">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">📝</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No assessments for this course yet</h3>
            <p class="text-gray-500 mb-6">Create your first assessment for this course to start evaluating students.</p>
            <a href="{{ route('L.assessment.create') }}?course_id={{ $course->id }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                Create Assessment
            </a>
        </div>
        @endif
    </div>
</x-app-layout> 