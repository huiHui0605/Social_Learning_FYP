<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Submissions - {{ $assessment->title }}
            </h2>
            @if(request()->has('return_to_course'))
                <a href="{{ route('lecturer.courses.show', request()->get('return_to_course')) }}?tab=assignments"
                   class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                    ← Back to Course
                </a>
            @else
                <a href="{{ route('L.assessment.show', $assessment) }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                    ← Back to Assessment
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Assessment Info -->
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Course:</span>
                                <span class="font-medium">{{ $assessment->course->title }}</span>
                            </div>
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
                                <span class="font-medium">{{ $submissions->count() }}</span>
                            </div>
                        </div>
                    </div>

                    @if($submissions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Student
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Submitted
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Marks
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($submissions as $submission)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ strtoupper(substr($submission->student->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $submission->student->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $submission->student->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $submission->formatted_submitted_date }}
                                        @if($submission->isLate())
                                        <span class="ml-2 px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                            Late
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $submission->status_color }}-100 text-{{ $submission->status_color }}-800">
                                            {{ ucfirst($submission->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($submission->marks_obtained !== null)
                                        <div>
                                            <span class="font-medium">{{ $submission->marks_obtained }}/{{ $assessment->total_marks }}</span>
                                            <span class="text-gray-500">({{ $submission->percentage }}%)</span>
                                        </div>
                                        @else
                                        <span class="text-gray-500">Not graded</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('L.assessment.evaluate', $submission) }}?return_to_course={{ request()->get('return_to_course') }}"
                                           class="text-blue-600 hover:text-blue-900">
                                            {{ $submission->status === 'graded' ? 'View & Edit' : 'Evaluate' }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Stats -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $submissions->count() }}</div>
                            <div class="text-sm text-blue-600">Total Submissions</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $submissions->where('status', 'graded')->count() }}</div>
                            <div class="text-sm text-green-600">Graded</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">{{ $submissions->where('status', 'submitted')->count() }}</div>
                            <div class="text-sm text-yellow-600">Pending</div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">{{ $submissions->where('status', 'late')->count() }}</div>
                            <div class="text-sm text-red-600">Late</div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">📝</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No submissions yet</h3>
                        <p class="text-gray-500">Students haven't submitted any work for this assessment yet.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 