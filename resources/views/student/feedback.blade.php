<x-app-layout>
    <x-slot name="title">Feedback & Support - Student</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Feedback & Support
            </h2>
            <a href="{{ route('feedback.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm shadow">
                + Submit Feedback
            </a>
        </div>
    </x-slot>

    <div class="p-6">
        <p class="text-sm text-gray-600 mb-4">Submit feedback to your lecturers and track responses.</p>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        @if($feedback->count() > 0)
        <div class="space-y-4">
            @foreach($feedback as $item)
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $item->title }}</h3>
                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $item->priority_color }}-100 text-{{ $item->priority_color }}-800">
                                {{ ucfirst($item->priority) }}
                            </span>
                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $item->status_color }}-100 text-{{ $item->status_color }}-800">
                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                            </span>
                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $item->category_color }}-100 text-{{ $item->category_color }}-800">
                                {{ ucfirst($item->category) }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($item->content, 150) }}</p>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">To:</span>
                                <span class="font-medium">{{ $item->lecturer->name }}</span>
                            </div>
                            @if($item->course)
                            <div>
                                <span class="text-gray-500">Course:</span>
                                <span class="font-medium">{{ $item->course->title }}</span>
                            </div>
                            @endif
                            <div>
                                <span class="text-gray-500">Submitted:</span>
                                <span class="font-medium">{{ $item->formatted_created_date }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Response:</span>
                                <span class="font-medium">{{ $item->hasResponse() ? 'Yes' : 'No' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col space-y-2 ml-4">
                        <a href="{{ route('feedback.show', $item) }}"
                           class="bg-blue-600 text-white text-sm px-3 py-2 rounded hover:bg-blue-700 text-center">
                            View Details
                        </a>
                        
                        @if(!$item->hasResponse())
                        <div class="flex space-x-2">
                            <a href="{{ route('feedback.edit', $item) }}"
                               class="bg-yellow-600 text-white text-sm px-3 py-1 rounded hover:bg-yellow-700">
                                Edit
                            </a>
                            <form action="{{ route('feedback.destroy', $item) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this feedback?')"
                                        class="bg-red-600 text-white text-sm px-3 py-1 rounded hover:bg-red-700">
                                    Delete
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">💬</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No feedback submitted yet</h3>
            <p class="text-gray-500 mb-6">Submit your first feedback to communicate with your lecturers.</p>
            <a href="{{ route('feedback.create') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                Submit Feedback
            </a>
        </div>
        @endif
    </div>
</x-app-layout>
