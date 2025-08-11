<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                My Submission
            </h2>
            <a href="{{ route('student.courses.show', $submission->assessment->course) }}?tab=assignments"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ← Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Assessment Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="border-b border-gray-200 pb-4 mb-4">
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $submission->assessment->title }}</h1>
                        <div class="flex items-center space-x-4 text-sm">
                            <span class="text-gray-500">Course: <span class="font-medium">{{ $submission->assessment->course->title }}</span></span>
                            <span class="text-gray-500">Due: <span class="font-medium">{{ $submission->assessment->formatted_due_date }}</span></span>
                            <span class="text-gray-500">Total Marks: <span class="font-medium">{{ $submission->assessment->total_marks }}</span></span>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Assessment Description</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $submission->assessment->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submission Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Your Submission</h3>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 text-sm rounded-full bg-{{ $submission->status_color }}-100 text-{{ $submission->status_color }}-800">
                                {{ ucfirst($submission->status) }}
                            </span>
                            @if($submission->isLate())
                            <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-800">
                                Late Submission
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 text-sm">
                        <div>
                            <span class="text-gray-500">Submitted:</span>
                            <span class="font-medium">{{ $submission->formatted_submitted_date }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Status:</span>
                            <span class="font-medium">{{ ucfirst($submission->status) }}</span>
                        </div>
                        @if($submission->graded_at)
                        <div>
                            <span class="text-gray-500">Graded:</span>
                            <span class="font-medium">{{ $submission->formatted_graded_date }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Submission Content -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Your Answer/Work</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $submission->submission_content }}</p>
                        </div>
                    </div>

                    <!-- Attachment -->
                    @if($submission->attachment_path)
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Attachment</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <a href="{{ route('student.assessments.download', $submission) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Download Attachment
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Grade and Feedback -->
            @if($submission->status === 'graded')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Grade & Feedback</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $submission->marks_obtained }}/{{ $submission->assessment->total_marks }}</div>
                            <div class="text-sm text-green-600">Marks Obtained</div>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $submission->percentage }}%</div>
                            <div class="text-sm text-blue-600">Percentage</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500">Graded on</div>
                            <div class="text-sm font-medium text-gray-700">{{ $submission->formatted_graded_date }}</div>
                        </div>
                    </div>

                    @if($submission->feedback)
                    <div>
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Lecturer Feedback</h4>
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $submission->feedback }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <!-- Not Graded Yet -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-4xl mb-4">⏳</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Not Graded Yet</h3>
                        <p class="text-gray-500">Your submission is being reviewed by your lecturer. You will see your grade and feedback here once it's been evaluated.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout> 