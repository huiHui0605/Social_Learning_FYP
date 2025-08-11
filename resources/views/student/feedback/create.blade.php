<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Submit Feedback
            </h2>
            <a href="{{ route('feedback.index') }}"
               class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                ← Back to Feedback
            </a>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Submit Feedback to Lecturer</h3>
                
                <form action="{{ route('feedback.store') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <x-input-label for="title" value="Title" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" 
                                          value="{{ old('title') }}" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Lecturer -->
                        <div>
                            <x-input-label for="lecturer_id" value="Lecturer" />
                            <select id="lecturer_id" name="lecturer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select a lecturer</option>
                                @foreach($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}" {{ old('lecturer_id') == $lecturer->id ? 'selected' : '' }}>
                                        {{ $lecturer->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('lecturer_id')" class="mt-2" />
                        </div>

                        <!-- Course (Optional) -->
                        <div>
                            <x-input-label for="course_id" value="Course (Optional)" />
                            <select id="course_id" name="course_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select a course (optional)</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category" value="Category" />
                            <select id="category" name="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select a category</option>
                                <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General</option>
                                <option value="course" {{ old('category') == 'course' ? 'selected' : '' }}>Course Related</option>
                                <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Technical Issue</option>
                                <option value="suggestion" {{ old('category') == 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                                <option value="complaint" {{ old('category') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- Priority -->
                        <div>
                            <x-input-label for="priority" value="Priority" />
                            <select id="priority" name="priority" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select priority</option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>

                        <!-- Content -->
                        <div>
                            <x-input-label for="content" value="Feedback Content" />
                            <textarea id="content" name="content" rows="6" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                      placeholder="Please provide detailed feedback..." required>{{ old('content') }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                            <p class="text-sm text-gray-500 mt-1">Minimum 10 characters required.</p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('feedback.index') }}"
                               class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                                Cancel
                            </a>
                            <x-primary-button>
                                Submit Feedback
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout> 