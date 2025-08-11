<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            {{ Auth::user()->role === 'lecturer' ? 'Lecturer' : (Auth::user()->role === 'student' ? 'Student' : 'Admin') }} – Teams Chat
        </h2>
    </x-slot>

    <!-- Hidden input for current user ID -->
    <input type="hidden" id="currentUserId" value="{{ Auth::id() }}">

    <div class="h-screen bg-gray-50">
        <!-- Teams-like Chat Interface -->
        <div class="flex h-full">
            <!-- Left Sidebar - Conversations -->
            <div class="w-80 bg-white border-r border-gray-200 flex flex-col h-full">
                <!-- Header -->
                <div class="p-4 border-b border-gray-200 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Chats</h3>
                        <div class="flex items-center space-x-2">
                            <!-- Debug button -->
                            <button id="debugBtn" class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-full" title="Debug Info">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </button>
                            <button id="newChatBtn" class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Search -->
                    <div class="mt-3">
                        <div class="relative">
                            <input type="text" id="searchChats" placeholder="Search chats..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex border-b border-gray-200 flex-shrink-0">
                    <button id="individualTab" class="flex-1 py-3 px-4 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                        Individual
                    </button>
                    <button id="groupTab" class="flex-1 py-3 px-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                        Groups
                    </button>
                </div>

                <!-- Individual Chats -->
                <div id="individualChats" class="flex-1 overflow-y-auto min-h-0">
                    @if($conversationPartners->count() > 0)
                        @foreach($conversationPartners as $partner)
                            <div class="chat-item p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer" 
                                 data-user-id="{{ $partner->id }}" data-user-name="{{ $partner->name }}" data-chat-type="individual">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($partner->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $partner->name }}</p>
                                            <span class="text-xs text-gray-500">{{ ucfirst($partner->role) }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 truncate">Click to start chatting</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-4 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="text-sm font-medium">No conversation partners yet</p>
                            <p class="text-xs mt-1">Use "Start New Chat" to begin messaging with other users</p>
                        </div>
                    @endif
                </div>

                <!-- Group Chats -->
                <div id="groupChats" class="flex-1 overflow-y-auto min-h-0 hidden">
                    @if($groupChats->count() > 0)
                        @foreach($groupChats as $group)
                            <div class="chat-item p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer" 
                                 data-group-id="{{ $group->id }}" data-group-name="{{ $group->name }}" data-chat-type="group">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($group->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $group->name }}</p>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs text-gray-500">{{ $group->member_count }}/{{ $group->max_members }}</span>
                                                <!-- Debug info -->
                                                <span class="text-xs text-gray-400">(Creator: {{ $group->created_by }}, You: {{ Auth::id() }})</span>
                                                <!-- Delete button for group creator -->
                                                @if($group->created_by == Auth::id())
                                                    <button class="p-1 text-red-600 hover:text-red-700 hover:bg-red-50 rounded" 
                                                            onclick="event.stopPropagation(); openDeleteGroupModal({{ $group->id }})" 
                                                            title="Delete group">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                @else
                                                    <!-- Debug: Show if user is not creator -->
                                                    <span class="text-xs text-gray-400">(Not creator)</span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 truncate">{{ $group->description ?: 'Group chat' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-4 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="text-sm">No groups yet</p>
                            <button id="createGroupBtn" class="mt-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                                Create Group
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="flex-1 flex flex-col h-full">
                <!-- Chat Header -->
                <div id="chatHeader" class="bg-white border-b border-gray-200 p-4 hidden flex-shrink-0">
                    <div class="flex items-center space-x-3">
                        <div id="chatAvatar" class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                            <!-- Avatar will be set dynamically -->
                        </div>
                        <div class="flex-1">
                            <h3 id="chatTitle" class="text-lg font-semibold text-gray-900">Select a chat</h3>
                            <p id="chatSubtitle" class="text-sm text-gray-500">Choose a conversation to start messaging</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button id="chatInfoBtn" class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-full" onclick="handleDeleteClick()">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            <button id="addMembersBtn" class="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-full hidden">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Welcome Screen -->
                <div id="welcomeScreen" class="flex-1 flex items-center justify-center bg-gray-50">
                    <div class="text-center">
                        <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Welcome to Teams Chat</h3>
                        <p class="text-gray-500 mb-6">Select a conversation from the sidebar to start messaging</p>
                        <div class="flex justify-center space-x-4">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600">Individual Chats</p>
                            </div>
                            <div class="text-center">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600">Group Chats</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages Area -->
                <div id="chatArea" class="flex-1 flex flex-col hidden h-full">
                    <!-- Messages Container -->
                    <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4 min-h-0">
                        <!-- Messages will be loaded here -->
                    </div>

                    <!-- Message Input -->
                    <div class="bg-white border-t border-gray-200 p-4 flex-shrink-0 shadow-sm">
                        <div class="flex items-end space-x-3">
                            <!-- File Upload Button -->
                            <button id="fileUploadBtn" class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </button>
                            
                            <!-- Message Input -->
                            <div class="flex-1">
                                <textarea id="messageInput" rows="1" placeholder="Type a message..." 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none transition-colors duration-200"></textarea>
                            </div>
                            
                            <!-- Send Button -->
                            <button id="sendMessageBtn" class="p-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- File Upload Input (Hidden) -->
                        <input type="file" id="fileInput" class="hidden" accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                        
                        <!-- Test connection -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const fileUploadBtn = document.getElementById('fileUploadBtn');
                                const fileInput = document.getElementById('fileInput');
                                
                                if (fileUploadBtn && fileInput) {
                                    fileUploadBtn.addEventListener('click', function() {
                                        fileInput.click();
                                    });
                                    
                                    fileInput.addEventListener('change', function() {
                                        console.log('File selected:', this.files[0]?.name);
                                    });
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Group Modal -->
    <div id="createGroupModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Create New Group</h3>
                        <button id="closeCreateGroupModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form id="createGroupForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Group Details -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Group Name *</label>
                                    <input type="text" id="groupName" required 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea id="groupDescription" rows="3" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                    <select id="groupType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="public">Public</option>
                                        <option value="private">Private</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Members</label>
                                    <input type="number" id="maxMembers" value="50" min="2" max="100" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Group Image</label>
                                    <div class="mt-1 flex items-center space-x-4">
                                        <div id="groupImagePreview" class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <input type="file" id="groupImage" accept="image/*" class="hidden">
                                            <button type="button" id="uploadGroupImage" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                                Upload Image
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Member Selection -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Add Members</label>
                                    <div class="relative">
                                        <input type="text" id="memberSearch" placeholder="Search users..." 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <svg class="absolute right-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Available Users -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Available Users</h4>
                                    <div id="availableUsers" class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-2">
                                        <!-- Users will be loaded here -->
                                    </div>
                                </div>

                                <!-- Selected Members -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Members (<span id="selectedCount">0</span>)</h4>
                                    <div id="selectedMembers" class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-2">
                                        <!-- Selected members will appear here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                            <button type="button" id="cancelCreateGroup" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Create Group
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Members Modal -->
    <div id="addMembersModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Add Members to Group</h3>
                        <button id="closeAddMembersModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
                            <input type="text" id="addMemberSearch" placeholder="Search users..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Available Users</h4>
                            <div id="addMemberUsers" class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-2">
                                <!-- Users will be loaded here -->
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Users (<span id="addMemberCount">0</span>)</h4>
                            <div id="addMemberSelected" class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-2">
                                <!-- Selected users will appear here -->
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button" id="cancelAddMembers" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="button" id="confirmAddMembers" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Add Members
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Chat Modal -->
    <div id="newChatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Start New Chat</h3>
                        <button id="closeNewChatModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Chat Type Selection -->
                    <div class="mb-6">
                        <div class="flex space-x-4 border-b border-gray-200">
                            <button id="individualChatTab" class="py-2 px-4 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                                Individual Chat
                            </button>
                            <button id="groupChatTab" class="py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                                Group Chat
                            </button>
                        </div>
                    </div>

                    <!-- Individual Chat Section -->
                    <div id="individualChatSection" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search Users to Chat With</label>
                            <div class="relative">
                                <input type="text" id="individualUserSearch" placeholder="Search by name or email..." 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <svg class="absolute right-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Type at least 2 characters to search</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Available Users</h4>
                            <div id="individualUsers" class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-2">
                                <div class="text-center text-gray-500 py-4">
                                    <p class="text-sm">Start typing to search users</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Selected User</h4>
                            <div id="selectedIndividualUser" class="p-3 border border-gray-200 rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-500">No user selected</p>
                            </div>
                        </div>
                    </div>

                    <!-- Group Chat Section -->
                    <div id="groupChatSection" class="space-y-4 hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Group Name *</label>
                            <input type="text" id="newGroupName" placeholder="Enter group name..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="newGroupDescription" rows="2" placeholder="Enter group description..." 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select id="newGroupType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Max Members</label>
                                <input type="number" id="newGroupMaxMembers" value="50" min="2" max="100" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search Members</label>
                            <div class="relative">
                                <input type="text" id="newGroupUserSearch" placeholder="Search by name or email..." 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <svg class="absolute right-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Type at least 2 characters to search</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Available Users</h4>
                            <div id="newGroupUsers" class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-2">
                                <div class="text-center text-gray-500 py-4">
                                    <p class="text-sm">Start typing to search users</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Members (<span id="newGroupSelectedCount">0</span>)</h4>
                            <div id="newGroupSelectedMembers" class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-2">
                                <!-- Selected members will appear here -->
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button" id="cancelNewChat" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="button" id="startNewChat" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Start Chat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Chat Confirmation Modal -->
    <div id="deleteGroupModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" onclick="if(event.target === this) closeDeleteModal()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 id="deleteModalTitle" class="text-lg font-semibold text-gray-900">Delete Group</h3>
                        <button id="closeDeleteGroupModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div id="deleteModalMessage" class="mb-6">
                        <p class="text-gray-600 mb-4">Are you sure you want to delete this group?</p>
                        <p class="text-sm text-gray-500">This action cannot be undone. All group messages and members will be permanently removed.</p>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button id="cancelDeleteGroup" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200" onclick="closeDeleteModal()">
                            Cancel
                        </button>
                        <button id="confirmDeleteGroup" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200" onclick="confirmDeleteGroup()">
                            Delete Group
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Teams Chat JavaScript -->
    <script src="{{ asset('js/teams-chat.js') }}"></script>
    
    <!-- Debug script for troubleshooting -->
    <script src="{{ asset('js/debug-messaging.js') }}"></script>
    
    <!-- Add user ID meta tag for JavaScript -->
    <meta name="user-id" content="{{ Auth::id() }}">
    
    <style>
        /* Custom scrollbar styling */
        #messagesContainer::-webkit-scrollbar {
            width: 6px;
        }
        
        #messagesContainer::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        #messagesContainer::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        #messagesContainer::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Ensure proper flex behavior */
        .flex-1 {
            flex: 1 1 0%;
        }
        
        .min-h-0 {
            min-height: 0;
        }
        
        /* Smooth scrolling for the entire chat area */
        #chatArea {
            scroll-behavior: smooth;
        }
        
        /* Ensure messages container takes full height */
        #messagesContainer {
            height: 100%;
            max-height: 100%;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const individualTab = document.getElementById('individualTab');
        const groupTab = document.getElementById('groupTab');
        const individualChats = document.getElementById('individualChats');
        const groupChats = document.getElementById('groupChats');

        if (individualTab && groupTab && individualChats && groupChats) {
            individualTab.addEventListener('click', function() {
                individualChats.classList.remove('hidden');
                groupChats.classList.add('hidden');
                individualTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                groupTab.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                groupTab.classList.add('text-gray-500');
            });
            groupTab.addEventListener('click', function() {
                groupChats.classList.remove('hidden');
                individualChats.classList.add('hidden');
                groupTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                individualTab.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                individualTab.classList.add('text-gray-500');
            });
        }
    });
    </script>
</x-app-layout> 