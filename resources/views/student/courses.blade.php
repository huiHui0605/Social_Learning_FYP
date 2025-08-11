<x-app-layout>
    <x-slot name="title">My Learning Journey - Student</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                My Learning Journey
            </h2>
            <button onclick="openJoinModal()"
                   class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm shadow">
                + Enroll in New Course
            </button>
        </div>
    </x-slot>

    <div class="p-6 text-gray-900">
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
            <h3 class="text-lg font-semibold text-indigo-700">Enrolled Courses</h3>
            <p class="text-sm text-gray-600">Here you can view all your active and completed courses.</p>
        </div>

        @if($enrolledCourses->count() > 0)
            <!-- Courses Table -->
            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-indigo-600 text-white">
                        <tr>
                            <th class="px-4 py-2 text-left">Course Code</th>
                            <th class="px-4 py-2 text-left">Course Title</th>
                            <th class="px-4 py-2 text-left">Lecturer</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($enrolledCourses as $enrollment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $enrollment->course_code }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('student.courses.show', $enrollment) }}" 
                                       class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        {{ $enrollment->title }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">{{ $enrollment->lecturer->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    @if($enrollment->pivot->status === 'enrolled')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @elseif($enrollment->pivot->status === 'completed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            Dropped
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('student.courses.show', $enrollment) }}" 
                                       class="inline-block bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-sm">
                                        View Course
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 mb-4">You haven't enrolled in any courses yet.</p>
                <button onclick="openJoinModal()"
                       class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                    Enroll in Your First Course
                </button>
            </div>
        @endif
    </div>

    <!-- Join Course Modal -->
    <div id="joinCourseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Join Course</h3>
                <form action="{{ route('student.courses.join.store') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label for="course_code" class="block text-sm font-medium text-gray-700 mb-2">Course Code</label>
                        <input type="text" name="course_code" id="course_code" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                               placeholder="Enter course code (e.g., ABC123)"
                               value="{{ old('course_code') }}">
                        <p class="text-xs text-gray-500 mt-1">Ask your lecturer for the course code to join.</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeJoinModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                            Join Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function openJoinModal() {
            document.getElementById('joinCourseModal').classList.remove('hidden');
        }
        function closeJoinModal() {
            document.getElementById('joinCourseModal').classList.add('hidden');
        }
        // Close modal when clicking outside
        document.getElementById('joinCourseModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeJoinModal();
            }
        });
    </script>
</x-app-layout>
