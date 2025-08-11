<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Edit Assessment: {{ $assessment->title }}
            </h2>
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
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('L.assessment.update', $assessment) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="title" value="Assessment Title" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $assessment->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="course_id" value="Course" />
                            <select id="course_id" name="course_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select a course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', $assessment->course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }} ({{ $course->code ?? $course->course_code }} - {{ $course->semester }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">You can only assign assessments to courses you teach.</p>
                            <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('description', $assessment->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="due_date" value="Due Date" />
                                <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date', $assessment->due_date->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="total_marks" value="Total Marks" />
                                <x-text-input id="total_marks" class="block mt-1 w-full" type="number" name="total_marks" :value="old('total_marks', $assessment->total_marks)" min="1" max="100" required />
                                <x-input-error :messages="$errors->get('total_marks')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="status" value="Status" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="draft" {{ old('status', $assessment->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $assessment->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="closed" {{ old('status', $assessment->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="assignment_file" value="Assignment File" />
                            @if($assessment->hasFile())
                                <div class="mb-2 p-3 bg-gray-50 rounded border">
                                    <p class="text-sm text-gray-600 mb-2">Current file:</p>
                                    <a href="{{ $assessment->file_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">
                                        📎 Download Current File
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="assignment_file" id="assignment_file" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" accept=".pdf,.doc,.docx,.txt,.ppt,.pptx" />
                            <p class="text-sm text-gray-500 mt-1">
                                @if($assessment->hasFile())
                                    Upload a new file to replace the current one (PDF, DOC, DOCX, TXT, PPT, PPTX, max 10MB)
                                @else
                                    Upload assignment file (PDF, DOC, DOCX, TXT, PPT, PPTX, max 10MB)
                                @endif
                            </p>
                            <x-input-error :messages="$errors->get('assignment_file')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            @if(request()->has('return_to_course'))
                                <x-secondary-button type="button" onclick="window.location.href='{{ route('lecturer.courses.show', request()->get('return_to_course')) }}?tab=assignments'" class="mr-3">
                                    Cancel
                                </x-secondary-button>
                            @else
                                <x-secondary-button type="button" onclick="window.location.href='{{ route('L.assessment.index') }}'" class="mr-3">
                                    Cancel
                                </x-secondary-button>
                            @endif
                            <x-primary-button>
                                Update Assessment
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 