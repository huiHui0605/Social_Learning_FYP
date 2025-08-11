<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Social Learning Hub' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/chatbot.js') }}" defer></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex">
        <!-- ✅ Optional Sidebar Slot (set in your page) -->
        @isset($sidebar)
            <aside class="w-64 bg-blue-600 text-white p-6">
                {{ $sidebar }}
            </aside>
        @endisset

        <!-- ✅ Page Content Area -->
        <div class="flex-1 flex flex-col">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-1 p-4">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- AI Chatbot -->
    <div id="aiChatbot" class="fixed bottom-4 right-4 z-50">
        <div id="chatbotIcon" class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
            <!-- Robot Face Icon -->
            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                <!-- Robot Head -->
                <rect x="4" y="4" width="16" height="16" rx="2" fill="currentColor"/>
                <!-- Eyes -->
                <circle cx="8" cy="10" r="1.5" fill="white"/>
                <circle cx="16" cy="10" r="1.5" fill="white"/>
                <!-- Eye pupils -->
                <circle cx="8" cy="10" r="0.5" fill="currentColor"/>
                <circle cx="16" cy="10" r="0.5" fill="currentColor"/>
                <!-- Mouth -->
                <path d="M8 14h8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <!-- Antenna -->
                <line x1="12" y1="4" x2="12" y2="2" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <circle cx="12" cy="2" r="1" fill="white"/>
                <!-- Side details -->
                <rect x="2" y="8" width="2" height="2" rx="1" fill="white" opacity="0.7"/>
                <rect x="20" y="8" width="2" height="2" rx="1" fill="white" opacity="0.7"/>
            </svg>
        </div>
        <div id="chatbotWindow" class="hidden fixed bottom-24 right-4 w-96 bg-white rounded-lg shadow-xl border border-gray-200 flex flex-col" style="height: 500px; left: auto; top: auto; cursor: move;">
            <div class="p-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-t-lg flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <!-- Robot Face in Header -->
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <rect x="4" y="4" width="16" height="16" rx="2" fill="currentColor"/>
                        <circle cx="8" cy="10" r="1" fill="white"/>
                        <circle cx="16" cy="10" r="1" fill="white"/>
                        <path d="M8 14h8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <h3 class="text-lg font-semibold">AI Assistant</h3>
                </div>
                <button id="closeChatbot" class="text-white hover:text-gray-200 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="chatbotMessages" class="flex-1 p-4 overflow-y-auto">
                <!-- Messages will appear here -->
            </div>
            <div class="p-4 border-t border-gray-200">
                <div class="flex space-x-2">
                    <input type="text" id="chatbotInput" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Ask a question...">
                    <button id="sendChatbotMessage" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <span>Send</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Add styles for the typing indicator */
        @keyframes typing {
            0% { opacity: 0.5; }
            50% { opacity: 1; }
            100% { opacity: 0.5; }
        }
        .typing-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            margin: 0 2px;
            background-color: #9ca3af;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }
        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
    </style>
</body>
</html>
