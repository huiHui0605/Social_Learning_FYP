<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Edit Course: {{ $course->title }}
            </h2>
            <a href="{{ route('lecturer.courses.index') }}"
               class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm shadow">
                ← Back to Courses
            </a>
        </div>
    </x-slot>

    <div class="p-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('lecturer.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Course Title *</label>
                        <input type="text" name="title" id="title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               value="{{ old('title', $course->title) }}">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('description', $course->description) }}</textarea>
                    </div>
                    
                    <div>
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                        <select name="semester" id="semester"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Select Semester</option>
                            <option value="Semester 1" {{ old('semester', $course->semester) == 'Semester 1' ? 'selected' : '' }}>Semester 1</option>
                            <option value="Semester 2" {{ old('semester', $course->semester) == 'Semester 2' ? 'selected' : '' }}>Semester 2</option>
                            <option value="Semester 3" {{ old('semester', $course->semester) == 'Semester 3' ? 'selected' : '' }}>Semester 3</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
                        <input type="text" name="academic_year" id="academic_year"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               value="{{ old('academic_year', $course->academic_year) }}"
                               placeholder="e.g., 2024-2025">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="course_image" class="block text-sm font-medium text-gray-700 mb-2">Course Image</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <img src="{{ $course->image_url }}" alt="Current course image" 
                                     class="h-20 w-20 object-cover rounded border">
                            </div>
                            <div class="flex-1">
                                <input type="file" name="course_image" id="course_image" accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <p class="text-xs text-gray-500 mt-1">Upload a new image (JPEG, PNG, JPG, GIF, max 2MB)</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="is_active" class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                   {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Course is active (students can enroll)</span>
                        </label>
                    </div>
                </div>
                
                <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Course Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Course Code:</span>
                            <span class="text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">{{ $course->course_code }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Created:</span>
                            <span class="text-gray-900">{{ $course->created_at->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Last Updated:</span>
                            <span class="text-gray-900">{{ $course->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Enrolled Students:</span>
                            <span class="text-gray-900">{{ $course->students->count() }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('lecturer.courses.index') }}"
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Update Course
                    </button>
                </div>
            </form>
            <!-- Delete Button -->
            <form action="{{ route('lecturer.courses.destroy', $course) }}" method="POST" class="mt-4 flex justify-end" onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Delete Course
                </button>
            </form>
        </div>
    </div>
</x-app-layout> 