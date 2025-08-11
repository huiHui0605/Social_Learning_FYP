<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Upload Material for {{ $course->title }}
            </h2>
            <a href="{{ route('lecturer.courses.show', $course) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">← Back to Course</a>
        </div>
    </x-slot>
    <div class="max-w-xl mx-auto py-8">
        <form action="{{ route('lecturer.courses.materials.upload', $course) }}" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white p-6 rounded shadow">
            @csrf
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" id="title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div>
                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File</label>
                <input type="file" name="file" id="file" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Upload</button>
        </form>
    </div>
</x-app-layout>
