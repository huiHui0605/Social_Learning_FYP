<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Create New Course
            </h2>
            <a href="{{ route('lecturer.courses.index') }}"
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">
                ← Back to Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('lecturer.courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Course Title *
                                </label>
                                <input type="text" name="title" id="title" required
                                       value="{{ old('title') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                                    Semester *
                                </label>
                                <select name="semester" id="semester" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Semester</option>
                                    <option value="Semester 1" {{ old('semester') == 'Semester 1' ? 'selected' : '' }}>Semester 1</option>
                                    <option value="Semester 2" {{ old('semester') == 'Semester 2' ? 'selected' : '' }}>Semester 2</option>
                                    <option value="Semester 3" {{ old('semester') == 'Semester 3' ? 'selected' : '' }}>Semester 3</option>
                                </select>
                                @error('semester')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">
                                Academic Year *
                            </label>
                            <input type="text" name="academic_year" id="academic_year" required
                                   value="{{ old('academic_year') }}"
                                   placeholder="e.g., 2024/2025"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            @error('academic_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Course Description
                            </label>
                            <textarea name="description" id="description" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Enter course description...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                Course Image (Optional)
                            </label>
                            <input type="file" name="image" id="image" accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="mt-1 text-sm text-gray-500">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB</p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-4 pt-6">
                            <a href="{{ route('lecturer.courses') }}"
                               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition duration-200">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition duration-200">
                                Create Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 