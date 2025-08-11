<x-app-layout>
    <x-slot name="title">Student Feedback Hub - Lecturer</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Student Feedback Hub
            </h2>
            <a href="{{ route('L.feedback.create-to-admin') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm shadow">
                + Submit to Admin
            </a>
        </div>
    </x-slot>

    <div class="p-6">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <!-- Received Feedback Section -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Feedback from Students</h3>
            
            @if($receivedFeedback->count() > 0)
            <div class="space-y-4">
                @foreach($receivedFeedback as $feedback)
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h4 class="text-lg font-semibold text-gray-800">{{ $feedback->title }}</h4>
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $feedback->priority_color }}-100 text-{{ $feedback->priority_color }}-800">
                                    {{ ucfirst($feedback->priority) }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $feedback->status_color }}-100 text-{{ $feedback->status_color }}-800">
                                    {{ ucfirst(str_replace('_', ' ', $feedback->status)) }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $feedback->category_color }}-100 text-{{ $feedback->category_color }}-800">
                                    {{ ucfirst($feedback->category) }}
                                </span>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($feedback->content, 150) }}</p>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">From:</span>
                                    <span class="font-medium">{{ optional($feedback->student)->name ?? 'N/A' }}</span>
                                </div>
                                @if($feedback->course)
                                <div>
                                    <span class="text-gray-500">Course:</span>
                                    <span class="font-medium">{{ $feedback->course->title }}</span>
                                </div>
                                @endif
                                <div>
                                    <span class="text-gray-500">Submitted:</span>
                                    <span class="font-medium">{{ $feedback->formatted_created_date }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Response:</span>
                                    <span class="font-medium">{{ $feedback->hasResponse() ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col space-y-2 ml-4">
                            <a href="{{ route('L.feedback.show', $feedback) }}"
                               class="bg-blue-600 text-white text-sm px-3 py-2 rounded hover:bg-blue-700 text-center">
                                {{ $feedback->hasResponse() ? 'View Details' : 'Respond' }}
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 bg-gray-50 rounded-lg">
                <div class="text-gray-400 text-4xl mb-3">📝</div>
                <h4 class="text-lg font-medium text-gray-900 mb-2">No feedback received yet</h4>
                <p class="text-gray-500">Students can submit feedback to you through their dashboard.</p>
            </div>
            @endif
        </div>

        <!-- Submitted Feedback Section -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Feedback Submitted to Admin</h3>
            
            @if($submittedFeedback->count() > 0)
            <div class="space-y-4">
                @foreach($submittedFeedback as $feedback)
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h4 class="text-lg font-semibold text-gray-800">{{ $feedback->title }}</h4>
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $feedback->priority_color }}-100 text-{{ $feedback->priority_color }}-800">
                                    {{ ucfirst($feedback->priority) }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $feedback->status_color }}-100 text-{{ $feedback->status_color }}-800">
                                    {{ ucfirst(str_replace('_', ' ', $feedback->status)) }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $feedback->category_color }}-100 text-{{ $feedback->category_color }}-800">
                                    {{ ucfirst($feedback->category) }}
                                </span>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($feedback->content, 150) }}</p>
                            
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">To:</span>
                                    <span class="font-medium">{{ $feedback->admin->name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Submitted:</span>
                                    <span class="font-medium">{{ $feedback->formatted_created_date }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Response:</span>
                                    <span class="font-medium">{{ $feedback->hasResponse() ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col space-y-2 ml-4">
                            <a href="{{ route('L.feedback.show-to-admin', $feedback) }}"
                               class="bg-blue-600 text-white text-sm px-3 py-2 rounded hover:bg-blue-700 text-center">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 bg-gray-50 rounded-lg">
                <div class="text-gray-400 text-4xl mb-3">📤</div>
                <h4 class="text-lg font-medium text-gray-900 mb-2">No feedback submitted to admin yet</h4>
                <p class="text-gray-500 mb-4">Submit feedback to admin for system improvements or support.</p>
                <a href="{{ route('L.feedback.create-to-admin') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Submit to Admin
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
