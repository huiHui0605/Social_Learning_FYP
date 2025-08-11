<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Teams Chat Test
        </h2>
    </x-slot>

    <div class="p-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Teams Chat Test Page</h3>
            
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">Current User:</h4>
                    <p class="text-blue-700">{{ Auth::user()->name }} ({{ Auth::user()->role }}) - ID: {{ Auth::id() }}</p>
                </div>
                
                <div class="p-4 bg-green-50 rounded-lg">
                    <h4 class="font-semibold text-green-800 mb-2">Available Features:</h4>
                    <ul class="text-green-700 space-y-1">
                        <li>✅ Microsoft Teams-like interface</li>
                        <li>✅ Individual and group chats</li>
                        <li>✅ File uploads and sharing</li>
                        <li>✅ Real-time messaging</li>
                        <li>✅ Search functionality</li>
                        <li>✅ Modern UI with avatars</li>
                    </ul>
                </div>
                
                <div class="p-4 bg-yellow-50 rounded-lg">
                    <h4 class="font-semibold text-yellow-800 mb-2">Test Links:</h4>
                    <div class="space-y-2">
                        <a href="{{ route('message.index') }}" class="block text-yellow-700 hover:text-yellow-900 underline">
                            → Go to Teams Chat Interface
                        </a>
                        <a href="{{ route('dashboard') }}" class="block text-yellow-700 hover:text-yellow-900 underline">
                            → Back to Dashboard
                        </a>
                    </div>
                </div>
                
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold text-gray-800 mb-2">System Information:</h4>
                    <ul class="text-gray-700 space-y-1 text-sm">
                        <li>• Total Users: {{ \App\Models\User::count() }}</li>
                        <li>• Total Messages: {{ \App\Models\Message::count() }}</li>
                        <li>• Total Group Messages: {{ \App\Models\GroupMessage::count() }}</li>
                        <li>• Total Groups: {{ \App\Models\GroupChat::count() }}</li>
                        <li>• Conversation Partners: {{ $conversationPartners->count() ?? 0 }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 