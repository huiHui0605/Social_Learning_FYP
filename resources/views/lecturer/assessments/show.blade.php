<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Assessment Details
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('L.assessment.edit', $assessment) }}"
                   class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 text-sm">
                    Edit Assessment
                </a>
                @if(request()->has('return_to_course'))
                    <a href="{{ route('lecturer.courses.show', request()->get('return_to_course')) }}?tab=assignments"
                       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                        ← Back to Course
                    </a>
                @else
                    <a href="{{ route('L.assessment.index') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                        ← Back to Assessments
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Assessment Header -->
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $assessment->title }}</h1>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 text-sm rounded-full bg-{{ $assessment->status_color }}-100 text-{{ $assessment->status_color }}-800">
                                    {{ ucfirst($assessment->status) }}
                                </span>
                                @if($assessment->isOverdue())
                                <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-800">
                                    Overdue
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Course</h3>
                                <p class="text-lg font-semibold text-gray-900">{{ $assessment->course->title }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Due Date</h3>
                                <p class="text-lg font-semibold text-gray-900">{{ $assessment->formatted_due_date }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Total Marks</h3>
                                <p class="text-lg font-semibold text-gray-900">{{ $assessment->total_marks }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Assessment Description -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Description</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $assessment->description }}</p>
                        </div>
                    </div>

                    <!-- Assessment File -->
                    @if($assessment->hasFile())
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Assignment File</h3>
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex items-center space-x-3">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div>
                                    <a href="{{ $assessment->file_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium underline">
                                        📎 Download Assignment File
                                    </a>
                                    <p class="text-sm text-gray-600 mt-1">Click to download the assignment file</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Submissions Summary -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Submissions</h3>
                            @if($assessment->submissions_count > 0)
                            <a href="{{ route('L.assessment.submissions', $assessment) }}?return_to_course={{ $assessment->course_id }}"
                               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                                View All Submissions ({{ $assessment->submissions_count }})
                            </a>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $assessment->submissions_count }}</div>
                                <div class="text-sm text-blue-600">Total Submissions</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $assessment->graded_count }}</div>
                                <div class="text-sm text-green-600">Graded</div>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ $assessment->submissions_count - $assessment->graded_count }}</div>
                                <div class="text-sm text-yellow-600">Pending</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex flex-wrap gap-3">
                            @if($assessment->status === 'draft')
                            <form action="{{ route('L.assessment.publish', $assessment) }}" method="POST" class="inline">
                                @csrf
                                @if(request()->has('return_to_course'))
                                    <input type="hidden" name="return_to_course" value="{{ request()->get('return_to_course') }}">
                                @endif
                                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                                    Publish Assessment
                                </button>
                            </form>
                            @endif
                            
                            @if($assessment->status === 'published')
                            <form action="{{ route('L.assessment.close', $assessment) }}" method="POST" class="inline">
                                @csrf
                                @if(request()->has('return_to_course'))
                                    <input type="hidden" name="return_to_course" value="{{ request()->get('return_to_course') }}">
                                @endif
                                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
                                    Close Assessment
                                </button>
                            </form>
                            @endif
                            
                            @if($assessment->submissions_count > 0)
                            <a href="{{ route('L.assessment.submissions', $assessment) }}?return_to_course={{ $assessment->course_id }}"
                               class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                Evaluate Submissions
                            </a>
                            @endif
                            
                            <form action="{{ route('L.assessment.destroy', $assessment) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this assessment? This action cannot be undone.')"
                                        class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
                                    Delete Assessment
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 