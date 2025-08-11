<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Feedback Details
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
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Response</h3>
                
                <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400">
                    <p class="text-gray-800 whitespace-pre-wrap">{{ $feedback->response }}</p>
                </div>
                
                <div class="mt-4 text-sm text-gray-500">
                    Responded on {{ $feedback->formatted_responded_date }}
                </div>
            </div>
            @else
            <!-- Response Form -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Respond to Feedback</h3>
                
                <form action="{{ route('L.feedback.respond', $feedback) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="response" value="Your Response" />
                            <textarea id="response" name="response" rows="6" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                      placeholder="Write your response to the student..." required></textarea>
                            <x-input-error :messages="$errors->get('response')" class="mt-2" />
                            <p class="text-sm text-gray-500 mt-1">Minimum 10 characters required.</p>
                        </div>

                        <div>
                            <x-input-label for="status" value="Update Status" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="pending" {{ $feedback->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ $feedback->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $feedback->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $feedback->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>
                                Submit Response
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-app-layout> 