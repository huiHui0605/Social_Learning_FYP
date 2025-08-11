<x-app-layout>
    <x-slot name="title">Assessment Center - Lecturer</x-slot>
    <x-slot name="header">
        @php
            $course = $course ?? (isset($assessments) && $assessments->count() > 0 ? $assessments->first()->course : null);
        @endphp
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Assessment Center
            </h2>
            <div class="flex gap-2">
                @if($course)
                    <a href="{{ route('lecturer.courses.show', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        ← Back to Course
                    </a>
                @endif
                <a href="{{ route('L.assessment.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm shadow">
                    + Create Assessment
                </a>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <p class="text-sm text-gray-600 mb-4">Manage your assessments and evaluate student submissions.</p>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <!-- Course Filter -->
        @if($courses->count() > 0)
        <div class="mb-6">
            <form method="GET" action="{{ route('L.assessment.index') }}" class="flex items-center space-x-4">
                <label for="course_id" class="text-sm font-medium text-gray-700">Filter by Course:</label>
                <select name="course_id" id="course_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                    Filter
                </button>
                @if(request('course_id'))
                <a href="{{ route('L.assessment.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">
                    Clear Filter
                </a>
                @endif
            </form>
        </div>
        @endif

        @if($assessments->count() > 0)
        <!-- Assessments Grouped by Course -->
        <div class="space-y-8">
            @foreach($assessmentsByCourse as $courseId => $courseAssessments)
            @php $course = $courseAssessments->first()->course; @endphp
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Course Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $course->title }}</h3>
                            <p class="text-sm text-gray-600">{{ $course->code }} - {{ $course->semester }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">{{ $courseAssessments->count() }} Assessment(s)</div>
                            <div class="text-sm text-gray-500">{{ $courseAssessments->sum('submissions_count') }} Total Submissions</div>
                        </div>
                    </div>
                </div>

                <!-- Assessments for this course -->
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($courseAssessments as $assessment)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h4 class="text-lg font-semibold text-gray-800">{{ $assessment->title }}</h4>
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
                                    <a href="{{ route('L.assessment.show', $assessment) }}"
                                       class="bg-blue-600 text-white text-sm px-3 py-2 rounded hover:bg-blue-700 text-center">
                                        View Details
                                    </a>
                                    
                                    @if($assessment->submissions_count > 0)
                                    <a href="{{ route('L.assessment.submissions', $assessment) }}"
                                       class="bg-gray-200 text-gray-800 text-sm px-3 py-2 rounded hover:bg-gray-300 text-center">
                                        View Submissions ({{ $assessment->submissions_count }})
                                    </a>
                                    @endif
                                    
                                    @if($assessment->status === 'draft')
                                    <form action="{{ route('L.assessment.publish', $assessment) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-600 text-white text-sm px-3 py-2 rounded hover:bg-green-700">
                                            Publish
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if($assessment->status === 'published')
                                    <form action="{{ route('L.assessment.close', $assessment) }}" method="POST" class="inline">
                                        @csrf
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
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">📝</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                @if(request('course_id'))
                    No assessments for this course yet
                @else
                    No assessments yet
                @endif
            </h3>
            <p class="text-gray-500 mb-6">
                @if(request('course_id'))
                    Create your first assessment for this course to start evaluating students.
                @else
                    Create your first assessment to start evaluating students.
                @endif
            </p>
            <a href="{{ route('L.assessment.create') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                Create Assessment
            </a>
        </div>
        @endif
    </div>
</x-app-layout>
