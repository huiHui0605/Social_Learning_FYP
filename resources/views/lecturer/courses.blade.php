<x-app-layout>
    <x-slot name="title">Course Management - Lecturer</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Course Management
            </h2>
            <button onclick="openCreateModal()"
                   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm shadow">
                + Add New Course
            </button>
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

        <!-- Intro Section -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-indigo-700">My Courses</h3>
            <p class="text-sm text-gray-600">Here you can view and manage all your created courses.</p>
        </div>

        @if($courses->count() > 0)
            <!-- Courses Table -->
            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-indigo-600 text-white">
                        <tr>
                            <th class="px-4 py-2 text-left">Course Code</th>
                            <th class="px-4 py-2 text-left">Course Title</th>
                            <th class="px-4 py-2 text-left">Semester</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($courses as $course)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $course->course_code }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('lecturer.courses.show', $course) }}" 
                                       class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        {{ $course->title }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">{{ $course->semester ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    @if($course->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex space-x-2 justify-center">
                                        <a href="{{ route('lecturer.courses.show', $course) }}" 
                                           class="inline-block bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-sm">
                                            View Course
                                        </a>
                                        <a href="{{ route('lecturer.courses.edit', $course) }}" 
                                           class="inline-block bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 mb-4">No courses created yet.</p>
                <button onclick="openCreateModal()"
                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Create Your First Course
                </button>
            </div>
        @endif
    </div>

    <!-- Create Course Modal -->
    <div id="createCourseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Course</h3>
                <form action="{{ route('lecturer.courses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Course Title</label>
                        <input type="text" name="title" id="title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               value="{{ old('title') }}">
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('description') }}</textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                        <select name="semester" id="semester"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Select Semester</option>
                            <option value="Semester 1" {{ old('semester') == 'Semester 1' ? 'selected' : '' }}>Semester 1</option>
                            <option value="Semester 2" {{ old('semester') == 'Semester 2' ? 'selected' : '' }}>Semester 2</option>
                            <option value="Semester 3" {{ old('semester') == 'Semester 3' ? 'selected' : '' }}>Semester 3</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
                        <input type="text" name="academic_year" id="academic_year"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               value="{{ old('academic_year') }}"
                               placeholder="e.g., 2024-2025">
                    </div>

                    <div class="mb-6">
                        <label for="course_image" class="block text-sm font-medium text-gray-700 mb-2">Course Image</label>
                        <input type="file" name="course_image" id="course_image" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Upload an image for your course (JPEG, PNG, JPG, GIF, max 2MB)</p>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeCreateModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Create Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createCourseModal').classList.remove('hidden');
        }
        
        function closeCreateModal() {
            document.getElementById('createCourseModal').classList.add('hidden');
        }
        
        // Close modal when clicking outside
        document.getElementById('createCourseModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreateModal();
            }
        });
    </script>
</x-app-layout>
