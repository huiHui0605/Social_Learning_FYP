<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Evaluate Submission
            </h2>
            @if(request()->has('return_to_course'))
                <a href="{{ route('L.assessment.submissions', $submission->assessment) }}?return_to_course={{ request()->get('return_to_course') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                    ← Back to Submissions
                </a>
            @else
                <a href="{{ route('L.assessment.submissions', $submission->assessment) }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                    ← Back to Submissions
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Student and Assessment Info -->
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-lg font-medium text-gray-700">
                                            {{ strtoupper(substr($submission->student->name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $submission->student->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $submission->student->email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Submitted</div>
                                <div class="font-medium">{{ $submission->formatted_submitted_date }}</div>
                                @if($submission->isLate())
                                <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                    Late Submission
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Assessment:</span>
                                <span class="font-medium">{{ $submission->assessment->title }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Course:</span>
                                <span class="font-medium">{{ $submission->assessment->course->title }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Total Marks:</span>
                                <span class="font-medium">{{ $submission->assessment->total_marks }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Content -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Submission Content</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $submission->submission_content }}</p>
                        </div>
                    </div>

                    <!-- Attachment -->
                    @if($submission->attachment_path)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Attachment</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ basename($submission->attachment_path) }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Student uploaded file
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('L.assessment.download-submission', $submission) }}" 
                                   target="_blank"
                                   class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    View File
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Grading Form -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ $submission->status === 'graded' ? 'Update Grade' : 'Grade Submission' }}
                        </h3>
                        
                        <form method="POST" action="{{ route('L.assessment.grade', $submission) }}">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <x-input-label for="marks_obtained" value="Marks Obtained" />
                                    <x-text-input id="marks_obtained" 
                                                 class="block mt-1 w-full" 
                                                 type="number" 
                                                 name="marks_obtained" 
                                                 :value="old('marks_obtained', $submission->marks_obtained)" 
                                                 min="0" 
                                                 max="{{ $submission->assessment->total_marks }}" 
                                                 required />
                                    <p class="text-sm text-gray-500 mt-1">Maximum: {{ $submission->assessment->total_marks }} marks</p>
                                    <x-input-error :messages="$errors->get('marks_obtained')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="percentage" value="Percentage" />
                                    <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                        <span class="text-lg font-semibold" id="percentage-display">
                                            @if($submission->marks_obtained !== null)
                                                {{ round(($submission->marks_obtained / $submission->assessment->total_marks) * 100, 1) }}%
                                            @else
                                                0%
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <x-input-label for="feedback" value="Feedback (Optional)" />
                                <textarea id="feedback" 
                                          name="feedback" 
                                          rows="4" 
                                          class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                          placeholder="Provide constructive feedback to the student...">{{ old('feedback', $submission->feedback) }}</textarea>
                                <x-input-error :messages="$errors->get('feedback')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end space-x-3">
                                <x-secondary-button type="button" onclick="window.location.href='{{ route('L.assessment.submissions', $submission->assessment) }}'">
                                    Cancel
                                </x-secondary-button>
                                <x-primary-button>
                                    {{ $submission->status === 'graded' ? 'Update Grade' : 'Grade Submission' }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>

                    @if($submission->status === 'graded')
                    <!-- Current Grade Display -->
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Grade</h3>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <span class="text-sm text-gray-500">Marks:</span>
                                    <div class="text-lg font-semibold text-green-600">{{ $submission->marks_obtained }}/{{ $submission->assessment->total_marks }}</div>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Percentage:</span>
                                    <div class="text-lg font-semibold text-green-600">{{ $submission->percentage }}%</div>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Graded on:</span>
                                    <div class="text-sm text-gray-600">{{ $submission->formatted_graded_date }}</div>
                                </div>
                            </div>
                            @if($submission->feedback)
                            <div class="mt-4">
                                <span class="text-sm text-gray-500">Feedback:</span>
                                <div class="mt-1 text-gray-700">{{ $submission->feedback }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Calculate percentage in real-time
        document.getElementById('marks_obtained').addEventListener('input', function() {
            const marks = parseInt(this.value) || 0;
            const totalMarks = {{ $submission->assessment->total_marks }};
            const percentage = totalMarks > 0 ? ((marks / totalMarks) * 100).toFixed(1) : 0;
            document.getElementById('percentage-display').textContent = percentage + '%';
        });
    </script>
</x-app-layout> 