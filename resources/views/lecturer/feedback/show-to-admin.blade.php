<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Feedback to Admin Details
            </h2>
            <a href="{{ route('L.feedback.index') }}"
               class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                ← Back to Feedback
            </a>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-4xl mx-auto">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            <!-- Feedback Details -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">{{ $feedback->title }}</h3>
                    <div class="flex space-x-2">
                        <span class="px-3 py-1 text-sm rounded-full bg-{{ $feedback->priority_color }}-100 text-{{ $feedback->priority_color }}-800">
                            {{ ucfirst($feedback->priority) }}
                        </span>
                        <span class="px-3 py-1 text-sm rounded-full bg-{{ $feedback->status_color }}-100 text-{{ $feedback->status_color }}-800">
                            {{ ucfirst(str_replace('_', ' ', $feedback->status)) }}
                        </span>
                        <span class="px-3 py-1 text-sm rounded-full bg-{{ $feedback->category_color }}-100 text-{{ $feedback->category_color }}-800">
                            {{ ucfirst($feedback->category) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Feedback Information</h4>
                        <div class="space-y-2 text-sm">
                            <div>
                                <span class="text-gray-500">Submitted to:</span>
                                <span class="font-medium">{{ $feedback->admin->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Submitted on:</span>
                                <span class="font-medium">{{ $feedback->formatted_created_date }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Status:</span>
                                <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $feedback->status)) }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Response Information</h4>
                        <div class="space-y-2 text-sm">
                            @if($feedback->hasResponse())
                            <div>
                                <span class="text-gray-500">Responded by:</span>
                                <span class="font-medium">{{ $feedback->respondedBy->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Responded on:</span>
                                <span class="font-medium">{{ $feedback->formatted_responded_date }}</span>
                            </div>
                            @else
                            <div class="text-gray-500 italic">No response yet</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h4 class="font-medium text-gray-700 mb-3">Feedback Content</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-800 whitespace-pre-wrap">{{ $feedback->content }}</p>
                    </div>
                </div>
            </div>

            <!-- Response Section -->
            @if($feedback->hasResponse())
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Response from {{ $feedback->admin->name }}</h3>
                
                <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-400">
                    <p class="text-gray-800 whitespace-pre-wrap">{{ $feedback->response }}</p>
                </div>
                
                <div class="mt-4 text-sm text-gray-500">
                    Responded on {{ $feedback->formatted_responded_date }}
                </div>
            </div>
            @else
            <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                <div class="flex items-center">
                    <div class="text-yellow-600 text-2xl mr-3">⏳</div>
                    <div>
                        <h3 class="text-lg font-medium text-yellow-800">Waiting for Response</h3>
                        <p class="text-yellow-700">Your feedback has been submitted and is waiting for a response from {{ $feedback->admin->name }}.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout> 