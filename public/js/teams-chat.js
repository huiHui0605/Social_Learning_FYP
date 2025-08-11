class TeamsChat {
    constructor() {
        console.log('TeamsChat constructor called');
        this.currentChat = null;
        this.currentChatType = null;
        this.currentChatId = null;
        this.currentUserId = this.getCurrentUserId();
        console.log('Current user ID:', this.currentUserId);
        this.messagesContainer = document.getElementById('messagesContainer');
        this.messageInput = document.getElementById('messageInput');
        this.sendMessageBtn = document.getElementById('sendMessageBtn');
        this.fileInput = document.getElementById('fileInput');
        this.searchInput = document.getElementById('searchChats');
        
        // Group creation and member management
        this.selectedMembers = new Set();
        this.availableUsers = [];
        this.currentGroupId = null;
        
        // Scroll tracking
        this.isNearBottom = true;
        this.hasNewMessages = false;
        
        console.log('Initializing event listeners...');
        this.initializeEventListeners();
        this.initializeAutoResize();
        this.initializeFileInput();
        this.loadAvailableUsers();
        this.initializeGroupCreation();
        this.initializeMemberManagement();
        this.initializeNewChat();
        
        // Initialize delete group with a small delay to ensure DOM is ready
        setTimeout(() => {
            this.initializeDeleteGroup();
        }, 100);
        
        console.log('TeamsChat constructor completed');
    }

    initializeEventListeners() {
        // Message sending
        this.messageInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        this.sendMessageBtn.addEventListener('click', () => {
            this.sendMessage();
        });

        // File upload
        this.fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                this.uploadFile(e.target.files[0]);
            }
        });

        // Search functionality for existing conversations
        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => {
                this.filterChats(e.target.value);
            });
        }

        // Search functionality for new chat modal
        const individualUserSearch = document.getElementById('individualUserSearch');
        const newGroupUserSearch = document.getElementById('newGroupUserSearch');

        if (individualUserSearch) {
            let searchTimeout;
            individualUserSearch.addEventListener('input', (e) => {
                const searchTerm = e.target.value.trim();
                
                // Clear previous timeout
                clearTimeout(searchTimeout);
                
                // Set new timeout for debounced search
                searchTimeout = setTimeout(() => {
                    if (searchTerm.length >= 2) {
                        this.searchUsers(searchTerm);
                    } else if (searchTerm.length === 0) {
                        this.loadAvailableUsersForNewChat();
                    }
                }, 300); // 300ms delay
            });
        }

        if (newGroupUserSearch) {
            let searchTimeout;
            newGroupUserSearch.addEventListener('input', (e) => {
                const searchTerm = e.target.value.trim();
                
                // Clear previous timeout
                clearTimeout(searchTimeout);
                
                // Set new timeout for debounced search
                searchTimeout = setTimeout(() => {
                    if (searchTerm.length >= 2) {
                        this.searchUsers(searchTerm);
                    } else if (searchTerm.length === 0) {
                        this.loadAvailableUsersForNewChat();
                    }
                }, 300); // 300ms delay
            });
        }

        // Auto-scroll to bottom when new messages arrive
        this.messagesContainer.addEventListener('scroll', () => {
            this.handleScroll();
        });

        // Chat item click listeners
        this.initializeChatItemListeners();

        // Group creation and member management
        this.initializeGroupCreation();
        this.initializeMemberManagement();
        this.initializeNewChat();
    }

    initializeAutoResize() {
        this.messageInput.addEventListener('input', () => {
            this.messageInput.style.height = 'auto';
            this.messageInput.style.height = Math.min(this.messageInput.scrollHeight, 120) + 'px';
        });
    }

    // Initialize chat item click listeners
    initializeChatItemListeners() {
        // Use event delegation for chat items
        document.addEventListener('click', (e) => {
            const chatItem = e.target.closest('.chat-item');
            if (!chatItem) return;

            const chatType = chatItem.dataset.chatType;
            const userId = chatItem.dataset.userId;
            const groupId = chatItem.dataset.groupId;
            const userName = chatItem.dataset.userName;
            const groupName = chatItem.dataset.groupName;

            if (chatType === 'individual' && userId) {
                this.selectIndividualChat(userId, userName);
            } else if (chatType === 'group' && groupId) {
                this.selectGroupChat(groupId, groupName);
            }
        });
    }

    // Select individual chat
    async selectIndividualChat(userId, userName) {
        this.currentChatType = 'individual';
        this.currentChatId = userId;
        
        // Update UI
        this.updateChatHeader(userName, 'Individual chat', 'blue');
        this.showChatArea();
        
        // Show delete button for individual chats
        const chatInfoBtn = document.getElementById('chatInfoBtn');
        if (chatInfoBtn) {
            chatInfoBtn.classList.remove('hidden');
            // Update button text and functionality for individual chat
            chatInfoBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            `;
            chatInfoBtn.title = 'Delete conversation';
        }
        
        // Load messages
        await this.loadIndividualChat(userId);
        
        // Update active state
        this.updateActiveChatItem(userId, 'individual');
    }

    // Select group chat
    async selectGroupChat(groupId, groupName) {
        console.log('Selecting group chat:', groupId, groupName);
        
        this.currentChatType = 'group';
        this.currentChatId = groupId;
        
        // Update UI
        this.updateChatHeader(groupName, 'Group chat', 'green');
        this.showChatArea();
        
        // Show delete button for group chats
        const chatInfoBtn = document.getElementById('chatInfoBtn');
        console.log('Delete button element:', chatInfoBtn);
        
        if (chatInfoBtn) {
            console.log('Showing delete button...');
            chatInfoBtn.classList.remove('hidden');
            // Update button text and functionality for group chat
            chatInfoBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            `;
            chatInfoBtn.title = 'Delete group';
            console.log('Delete button classes after showing:', chatInfoBtn.className);
        } else {
            console.error('Delete button not found in selectGroupChat!');
        }
        
        // Load messages
        await this.loadGroupChat(groupId);
        
        // Update active state
        this.updateActiveChatItem(groupId, 'group');
        
        console.log('Group chat selection completed');
    }

    // Update chat header
    updateChatHeader(title, subtitle, color = 'blue') {
        const chatTitle = document.getElementById('chatTitle');
        const chatSubtitle = document.getElementById('chatSubtitle');
        const chatAvatar = document.getElementById('chatAvatar');
        const chatHeader = document.getElementById('chatHeader');

        if (chatTitle) chatTitle.textContent = title;
        if (chatSubtitle) chatSubtitle.textContent = subtitle;
        if (chatAvatar) {
            chatAvatar.textContent = title.charAt(0).toUpperCase();
            chatAvatar.className = `w-10 h-10 bg-${color}-500 rounded-full flex items-center justify-center text-white font-semibold`;
        }
        if (chatHeader) chatHeader.classList.remove('hidden');
    }

    // Show chat area
    showChatArea() {
        const welcomeScreen = document.getElementById('welcomeScreen');
        const chatArea = document.getElementById('chatArea');

        if (welcomeScreen) welcomeScreen.classList.add('hidden');
        if (chatArea) chatArea.classList.remove('hidden');
    }

    // Update active chat item
    updateActiveChatItem(id, type) {
        // Remove active class from all chat items
        document.querySelectorAll('.chat-item').forEach(item => {
            item.classList.remove('bg-blue-50', 'border-blue-200');
        });

        // Add active class to selected chat item
        const activeItem = document.querySelector(`[data-${type === 'individual' ? 'user' : 'group'}-id="${id}"]`);
        if (activeItem) {
            activeItem.classList.add('bg-blue-50', 'border-blue-200');
        }
    }

    // Load individual chat
    async loadIndividualChat(userId) {
        try {
            const response = await fetch(`/messages/individual/${userId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.currentChatType = 'individual';
                this.currentChatId = userId;
                this.renderMessages(data.messages);
                this.scrollToBottom();
            } else {
                console.error('Failed to load individual chat');
            }
        } catch (error) {
            console.error('Error loading individual chat:', error);
        }
    }

    // Load group chat
    async loadGroupChat(groupId) {
        try {
            const response = await fetch(`/messages/group/${groupId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.currentChatType = 'group';
                this.currentChatId = groupId;
                this.renderMessages(data.messages);
                this.scrollToBottom();
            } else {
                console.error('Failed to load group chat');
            }
        } catch (error) {
            console.error('Error loading group chat:', error);
        }
    }

    // Display messages in the chat area
    displayMessages(messages, chatType) {
        this.messagesContainer.innerHTML = '';
        
        if (messages.length === 0) {
            this.messagesContainer.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p class="text-sm">No messages yet</p>
                    <p class="text-xs mt-1">Start the conversation!</p>
                </div>
            `;
            return;
        }

        let currentDate = null;
        
        messages.forEach(message => {
            const messageDate = new Date(message.created_at).toDateString();
            
            // Add date separator if it's a new date
            if (currentDate !== messageDate) {
                currentDate = messageDate;
                this.addDateSeparator(message.created_at);
            }
            
            this.addMessage(message, chatType);
        });

        this.scrollToBottom();
    }

    // Add a single message to the chat
    addMessage(message, chatType) {
        const currentUserId = this.getCurrentUserId();
        const isOwnMessage = message.sender_id == currentUserId;
        const messageElement = document.createElement('div');
        
        messageElement.className = `flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mb-4`;
        
        if (isOwnMessage) {
            messageElement.innerHTML = `
                <div class="max-w-xs lg:max-w-md">
                    <div class="bg-blue-600 text-white rounded-lg px-4 py-2 shadow-sm">
                        ${this.formatMessageContent(message)}
                        <div class="text-xs text-blue-200 mt-1">${this.formatTime(message.created_at)}</div>
                    </div>
                </div>
            `;
        } else {
            messageElement.innerHTML = `
                <div class="flex items-start space-x-2 max-w-xs lg:max-w-md">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                        ${message.sender.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="bg-gray-200 rounded-lg px-4 py-2 shadow-sm">
                        <div class="text-xs text-gray-600 mb-1">${message.sender.name}</div>
                        ${this.formatMessageContent(message)}
                        <div class="text-xs text-gray-500 mt-1">${this.formatTime(message.created_at)}</div>
                    </div>
                </div>
            `;
        }
        
        this.messagesContainer.appendChild(messageElement);
    }

    // Format message content based on type
    formatMessageContent(message) {
        if (message.content) {
            return `<div class="whitespace-pre-wrap">${this.escapeHtml(message.content)}</div>`;
        }
        
        if (message.file_path) {
            return this.formatFileMessage(message);
        }
        
        return '<div class="text-gray-400">Empty message</div>';
    }

    // Format file message
    formatFileMessage(message) {
        const fileIcon = this.getFileIcon(message.file_type);
        const fileName = message.file_name || 'Unknown file';
        const fileSize = this.formatFileSize(message.file_size);
        
        return `
            <div class="flex items-center space-x-2 p-2 bg-white bg-opacity-20 rounded">
                <span class="text-lg">${fileIcon}</span>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium truncate">${this.escapeHtml(fileName)}</div>
                    <div class="text-xs opacity-75">${fileSize}</div>
                </div>
                <a href="/messages/download/${message.id}" class="text-blue-200 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </a>
            </div>
        `;
    }

    // Get file icon based on type
    getFileIcon(mimeType) {
        if (!mimeType) return '📄';
        
        const type = mimeType.toLowerCase();
        
        if (type.includes('image')) return '🖼️';
        if (type.includes('video')) return '🎥';
        if (type.includes('pdf')) return '📕';
        if (type.includes('word') || type.includes('document')) return '📘';
        if (type.includes('excel') || type.includes('spreadsheet')) return '📗';
        if (type.includes('powerpoint') || type.includes('presentation')) return '📙';
        if (type.includes('zip') || type.includes('rar')) return '📦';
        if (type.includes('audio')) return '🎵';
        
        return '📄';
    }

    // Format file size
    formatFileSize(bytes) {
        if (!bytes) return '';
        
        const size = parseInt(bytes);
        const units = ['B', 'KB', 'MB', 'GB'];
        
        let i = 0;
        let fileSize = size;
        
        while (fileSize >= 1024 && i < units.length - 1) {
            fileSize /= 1024;
            i++;
        }
        
        return `${fileSize.toFixed(1)} ${units[i]}`;
    }

    // Add date separator
    addDateSeparator(dateString) {
        const date = new Date(dateString);
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);
        
        let dateText;
        if (date.toDateString() === today.toDateString()) {
            dateText = 'Today';
        } else if (date.toDateString() === yesterday.toDateString()) {
            dateText = 'Yesterday';
        } else {
            dateText = date.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        }
        
        const separatorElement = document.createElement('div');
        separatorElement.className = 'flex justify-center my-4';
        separatorElement.innerHTML = `
            <div class="bg-gray-200 text-gray-600 text-xs px-3 py-1 rounded-full">
                ${dateText}
            </div>
        `;
        
        this.messagesContainer.appendChild(separatorElement);
    }

    // Send message
    async sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const fileInput = document.getElementById('fileInput');
        const content = messageInput.value.trim();
        const file = fileInput.files[0];

        if (!content && !file) {
            return;
        }

        if (!this.currentChatType || !this.currentChatId) {
            alert('Please select a chat first');
            return;
        }

        const formData = new FormData();
        if (content) {
            formData.append('content', content);
        }
        if (file) {
            formData.append('file', file);
        }

        try {
            let response;
            if (this.currentChatType === 'individual') {
                formData.append('receiver_id', this.currentChatId);
                response = await fetch('/messages/individual/send', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
            } else {
                formData.append('group_id', this.currentChatId);
                response = await fetch(`/messages/group/${this.currentChatId}/send`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
            }

            if (response.ok) {
                const data = await response.json();
                console.log('Message sent successfully, response:', data);
                
                // Check if the response contains the expected data
                if (data.success && data.message) {
                    console.log('Adding message to chat:', data.message);
                    // Add message to chat
                    this.addMessageToChat(data.message);
                    
                    // Add user to sidebar if it's an individual chat and user not already in sidebar
                    if (this.currentChatType === 'individual' && this.currentChatId) {
                        const receiverName = data.message.receiver ? data.message.receiver.name : 'Unknown User';
                        this.addUserToSidebar(this.currentChatId, receiverName);
                    }
                    
                    // Clear inputs
                    messageInput.value = '';
                    fileInput.value = '';
                    this.updateFileInputLabel();
                    
                    // Scroll to bottom
                    this.scrollToBottom();
                } else {
                    console.error('Invalid response format:', data);
                    alert('Message sent but there was an issue displaying it. Please refresh the page.');
                }
            } else {
                const errorData = await response.json().catch(() => ({}));
                console.error('Failed to send message:', errorData);
                alert(errorData.error || 'Failed to send message. Please try again.');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            // Only show error if it's a network error, not a response parsing error
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                alert('Error sending message. Please check your connection and try again.');
            } else {
                console.error('Unexpected error:', error);
            }
        }
    }

    // Update file input label
    updateFileInputLabel() {
        const fileInput = document.getElementById('fileInput');
        if (fileInput && fileInput.files.length > 0) {
            // Reset file input
            fileInput.value = '';
        }
    }

    // Upload file
    async uploadFile(file) {
        if (!this.currentChat) {
            alert('Please select a chat first');
            return;
        }

        // Show upload progress
        this.showUploadProgress(file.name);

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        try {
            const url = this.currentChatType === 'individual' 
                ? `/messages/${this.currentChat}` 
                : `/group-messages/${this.currentChat}`;

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                this.addMessage(data.message, this.currentChatType);
                this.scrollToBottom();
                this.hideUploadProgress();
            } else {
                console.error('Failed to upload file');
                this.hideUploadProgress();
                alert('Failed to upload file. Please try again.');
            }
        } catch (error) {
            console.error('Error uploading file:', error);
            this.hideUploadProgress();
            alert('Error uploading file. Please check your connection and try again.');
        }

        // Clear file input
        this.fileInput.value = '';
    }

    // Show upload progress
    showUploadProgress(fileName) {
        const progressElement = document.createElement('div');
        progressElement.id = 'uploadProgress';
        progressElement.className = 'fixed bottom-4 right-4 bg-blue-600 text-white p-4 rounded-lg shadow-lg z-50';
        progressElement.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                <div>
                    <div class="text-sm font-medium">Uploading...</div>
                    <div class="text-xs opacity-75">${fileName}</div>
                </div>
            </div>
        `;
        document.body.appendChild(progressElement);
    }

    // Hide upload progress
    hideUploadProgress() {
        const progressElement = document.getElementById('uploadProgress');
        if (progressElement) {
            progressElement.remove();
        }
    }

    // Enhanced file input for mobile devices
    initializeFileInput() {
        const fileUploadBtn = document.getElementById('fileUploadBtn');
        
        if (fileUploadBtn) {
            fileUploadBtn.addEventListener('click', () => {
                // Create a more comprehensive file input for mobile
                const mobileFileInput = document.createElement('input');
                mobileFileInput.type = 'file';
                mobileFileInput.accept = 'image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,audio/*';
                mobileFileInput.multiple = false;
                mobileFileInput.capture = 'environment'; // For mobile camera access
                
                mobileFileInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        this.uploadFile(e.target.files[0]);
                    }
                });
                
                mobileFileInput.click();
            });
        }
    }

    // Filter chats based on search
    filterChats(searchTerm) {
        const chatItems = document.querySelectorAll('.chat-item');
        const searchLower = searchTerm.toLowerCase();

        chatItems.forEach(item => {
            const name = item.dataset.userName || item.dataset.groupName || '';
            if (name.toLowerCase().includes(searchLower)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Handle scroll events
    handleScroll() {
        if (this.messagesContainer) {
            const { scrollTop, scrollHeight, clientHeight } = this.messagesContainer;
            
            // Check if user is near the bottom (within 100px)
            this.isNearBottom = (scrollHeight - scrollTop - clientHeight) < 100;
            
            // If user is near the top, we could load more messages
            if (scrollTop < 100) {
                // TODO: Implement load more messages functionality
                console.log('User scrolled to top, could load more messages');
            }
            
            // Hide scroll indicator if user is near bottom
            if (this.isNearBottom) {
                this.hideScrollIndicator();
            }
        }
    }

    // Show scroll indicator
    showScrollIndicator() {
        if (this.isNearBottom) return; // Don't show if user is already at bottom
        
        let indicator = document.getElementById('scrollIndicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'scrollIndicator';
            indicator.className = 'fixed bottom-20 right-4 bg-blue-600 text-white p-3 rounded-full shadow-lg cursor-pointer z-40 transition-all duration-300 hover:bg-blue-700';
            indicator.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            `;
            indicator.addEventListener('click', () => this.scrollToBottom());
            document.body.appendChild(indicator);
        }
        indicator.style.display = 'block';
    }

    // Hide scroll indicator
    hideScrollIndicator() {
        const indicator = document.getElementById('scrollIndicator');
        if (indicator) {
            indicator.style.display = 'none';
        }
    }

    // Scroll to bottom without animation (for initial load)
    scrollToBottomInstant() {
        if (this.messagesContainer) {
            this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        }
    }

    // Scroll to bottom with animation (for new messages)
    scrollToBottom() {
        setTimeout(() => {
            if (this.messagesContainer) {
                this.messagesContainer.scrollTo({
                    top: this.messagesContainer.scrollHeight,
                    behavior: 'smooth'
                });
            }
        }, 100);
    }

    // Format time
    formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
    }

    // Escape HTML to prevent XSS
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Get current user ID
    getCurrentUserId() {
        // Try to get from meta tag first
        const userIdMeta = document.querySelector('meta[name="user-id"]');
        if (userIdMeta) {
            return userIdMeta.getAttribute('content');
        }
        
        // Try to get from data attribute on body
        const body = document.body;
        if (body.dataset.userId) {
            return body.dataset.userId;
        }
        
        // Try to get from a hidden input
        const userIdInput = document.getElementById('currentUserId');
        if (userIdInput) {
            return userIdInput.value;
        }
        
        // Fallback: try to extract from the page URL or other elements
        // This is a fallback and might not work in all cases
        console.warn('Could not find current user ID. Using fallback method.');
        return null;
    }

    // Create new group
    async createGroup(formData) {
        try {
            const response = await fetch('/groups', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            if (response.ok) {
                const data = await response.json();
                // Refresh the page or update the groups list
                window.location.reload();
            } else {
                console.error('Failed to create group');
            }
        } catch (error) {
            console.error('Error creating group:', error);
        }
    }

    // Initialize group creation
    initializeGroupCreation() {
        const createGroupBtn = document.getElementById('createGroupBtn');
        const createGroupModal = document.getElementById('createGroupModal');
        const closeCreateGroupModal = document.getElementById('closeCreateGroupModal');
        const cancelCreateGroup = document.getElementById('cancelCreateGroup');
        const createGroupForm = document.getElementById('createGroupForm');
        const memberSearch = document.getElementById('memberSearch');
        const uploadGroupImage = document.getElementById('uploadGroupImage');
        const groupImage = document.getElementById('groupImage');

        // Open create group modal
        if (createGroupBtn) {
            createGroupBtn.addEventListener('click', () => {
                createGroupModal.classList.remove('hidden');
                this.loadAvailableUsers();
            });
        }

        // Close modal
        if (closeCreateGroupModal) {
            closeCreateGroupModal.addEventListener('click', () => {
                createGroupModal.classList.add('hidden');
                this.resetGroupForm();
            });
        }

        if (cancelCreateGroup) {
            cancelCreateGroup.addEventListener('click', () => {
                createGroupModal.classList.add('hidden');
                this.resetGroupForm();
            });
        }

        // Member search
        if (memberSearch) {
            memberSearch.addEventListener('input', (e) => {
                this.filterAvailableUsers(e.target.value);
            });
        }

        // Group image upload
        if (uploadGroupImage && groupImage) {
            uploadGroupImage.addEventListener('click', () => {
                groupImage.click();
            });

            groupImage.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    this.previewGroupImage(e.target.files[0]);
                }
            });
        }

        // Create group form submission
        if (createGroupForm) {
            createGroupForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.createGroup();
            });
        }
    }

    // Load available users for group creation
    async loadAvailableUsers() {
        try {
            const response = await fetch('/users/available', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.availableUsers = data.users;
                this.renderAvailableUsers();
            } else {
                console.error('Failed to load available users');
            }
        } catch (error) {
            console.error('Error loading available users:', error);
        }
    }

    // Render available users in the create group modal
    renderAvailableUsers() {
        const availableUsersContainer = document.getElementById('availableUsers');
        if (!availableUsersContainer) return;

        availableUsersContainer.innerHTML = '';

        this.availableUsers.forEach(user => {
            const userElement = document.createElement('div');
            userElement.className = 'flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer';
            userElement.innerHTML = `
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                    ${user.name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">${user.name}</div>
                    <div class="text-xs text-gray-500">${user.role}</div>
                </div>
                <button type="button" class="add-member-btn text-blue-600 hover:text-blue-800" data-user-id="${user.id}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            `;

            const addBtn = userElement.querySelector('.add-member-btn');
            addBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.addMemberToSelection(user);
            });

            availableUsersContainer.appendChild(userElement);
        });
    }

    // Filter available users
    filterAvailableUsers(searchTerm) {
        const availableUsersContainer = document.getElementById('availableUsers');
        if (!availableUsersContainer) return;

        const searchLower = searchTerm.toLowerCase();
        const userElements = availableUsersContainer.querySelectorAll('.flex');

        userElements.forEach(element => {
            const userName = element.querySelector('.text-sm.font-medium').textContent.toLowerCase();
            const userRole = element.querySelector('.text-xs.text-gray-500').textContent.toLowerCase();
            
            if (userName.includes(searchLower) || userRole.includes(searchLower)) {
                element.style.display = 'flex';
            } else {
                element.style.display = 'none';
            }
        });
    }

    // Add member to selection
    addMemberToSelection(user) {
        if (this.selectedMembers.has(user.id)) return;

        this.selectedMembers.add(user.id);
        this.renderSelectedMembers();
        this.updateSelectedCount();
    }

    // Remove member from selection
    removeMemberFromSelection(userId) {
        this.selectedMembers.delete(userId);
        this.renderSelectedMembers();
        this.updateSelectedCount();
    }

    // Render selected members
    renderSelectedMembers() {
        const selectedMembersContainer = document.getElementById('selectedMembers');
        if (!selectedMembersContainer) return;

        selectedMembersContainer.innerHTML = '';

        this.selectedMembers.forEach(userId => {
            const user = this.availableUsers.find(u => u.id == userId);
            if (!user) return;

            const memberElement = document.createElement('div');
            memberElement.className = 'flex items-center space-x-3 p-2 bg-blue-50 rounded';
            memberElement.innerHTML = `
                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                    ${user.name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">${user.name}</div>
                    <div class="text-xs text-gray-500">${user.role}</div>
                </div>
                <button type="button" class="remove-member-btn text-red-600 hover:text-red-800" data-user-id="${user.id}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;

            const removeBtn = memberElement.querySelector('.remove-member-btn');
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.removeMemberFromSelection(user.id);
            });

            selectedMembersContainer.appendChild(memberElement);
        });
    }

    // Update selected count
    updateSelectedCount() {
        const selectedCount = document.getElementById('selectedCount');
        if (selectedCount) {
            selectedCount.textContent = this.selectedMembers.size;
        }
    }

    // Preview group image
    previewGroupImage(file) {
        const preview = document.getElementById('groupImagePreview');
        if (!preview) return;

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-lg">`;
            };
            reader.readAsDataURL(file);
        }
    }

    // Create group with selected members
    async createGroup() {
        const formData = new FormData();
        
        // Group details
        formData.append('name', document.getElementById('groupName').value);
        formData.append('description', document.getElementById('groupDescription').value);
        formData.append('type', document.getElementById('groupType').value);
        formData.append('max_members', document.getElementById('maxMembers').value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Selected members
        formData.append('members', JSON.stringify(Array.from(this.selectedMembers)));

        // Group image
        const groupImage = document.getElementById('groupImage');
        if (groupImage.files.length > 0) {
            formData.append('image', groupImage.files[0]);
        }

        try {
            const response = await fetch('/groups', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                console.log('Group created successfully:', data);
                
                // Close modal and refresh
                document.getElementById('createGroupModal').classList.add('hidden');
                this.resetGroupForm();
                window.location.reload();
            } else {
                console.error('Failed to create group');
            }
        } catch (error) {
            console.error('Error creating group:', error);
        }
    }

    // Reset group form
    resetGroupForm() {
        this.selectedMembers.clear();
        this.updateSelectedCount();
        
        const form = document.getElementById('createGroupForm');
        if (form) form.reset();
        
        const preview = document.getElementById('groupImagePreview');
        if (preview) {
            preview.innerHTML = `
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            `;
        }
        
        this.renderSelectedMembers();
    }

    // Load available users for adding to existing group
    async loadAvailableUsersForGroup() {
        if (!this.currentGroupId) return;

        try {
            const response = await fetch(`/groups/${this.currentGroupId}/available-users`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.renderAddMemberUsers(data.users);
            } else {
                console.error('Failed to load available users for group');
            }
        } catch (error) {
            console.error('Error loading available users for group:', error);
        }
    }

    // Render users for adding to existing group
    renderAddMemberUsers(users) {
        const usersContainer = document.getElementById('addMemberUsers');
        if (!usersContainer) return;

        usersContainer.innerHTML = '';

        users.forEach(user => {
            const userElement = document.createElement('div');
            userElement.className = 'flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer';
            userElement.innerHTML = `
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                    ${user.name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">${user.name}</div>
                    <div class="text-xs text-gray-500">${user.role}</div>
                </div>
                <button type="button" class="add-to-group-btn text-blue-600 hover:text-blue-800" data-user-id="${user.id}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            `;

            const addBtn = userElement.querySelector('.add-to-group-btn');
            addBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.addUserToGroupSelection(user);
            });

            usersContainer.appendChild(userElement);
        });
    }

    // Add user to group selection
    addUserToGroupSelection(user) {
        const selectedContainer = document.getElementById('addMemberSelected');
        const countElement = document.getElementById('addMemberCount');
        
        if (!selectedContainer || !countElement) return;
        
        // Check if user is already selected
        if (selectedContainer.querySelector(`[data-user-id="${user.id}"]`)) return;
        
        const userElement = document.createElement('div');
        userElement.className = 'flex items-center space-x-3 p-2 bg-blue-50 rounded';
        userElement.setAttribute('data-user-id', user.id);
        userElement.innerHTML = `
            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                ${user.name.charAt(0).toUpperCase()}
            </div>
            <div class="flex-1">
                <div class="text-sm font-medium text-gray-900">${user.name}</div>
                <div class="text-xs text-gray-500">${user.role}</div>
            </div>
            <button type="button" class="remove-from-group-btn text-red-600 hover:text-red-800" data-user-id="${user.id}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;

        const removeBtn = userElement.querySelector('.remove-from-group-btn');
        removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userElement.remove();
            this.updateAddMemberCount();
        });

        selectedContainer.appendChild(userElement);
        this.updateAddMemberCount();
    }

    // Update add member count
    updateAddMemberCount() {
        const selectedContainer = document.getElementById('addMemberSelected');
        const countElement = document.getElementById('addMemberCount');
        
        if (selectedContainer && countElement) {
            const count = selectedContainer.children.length;
            countElement.textContent = count;
        }
    }

    // Add members to existing group
    async addMembersToGroup() {
        if (!this.currentGroupId) return;

        const selectedContainer = document.getElementById('addMemberSelected');
        if (!selectedContainer) return;

        const selectedUserIds = Array.from(selectedContainer.children).map(el => el.getAttribute('data-user-id'));
        
        if (selectedUserIds.length === 0) {
            alert('Please select users to add');
            return;
        }

        try {
            const response = await fetch(`/groups/${this.currentGroupId}/add-members`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    user_ids: selectedUserIds,
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                })
            });

            if (response.ok) {
                const data = await response.json();
                console.log('Members added successfully:', data);
                
                // Close modal and refresh
                document.getElementById('addMembersModal').classList.add('hidden');
                this.resetAddMembersForm();
                
                // Show success message
                alert(data.message);
                
                // Refresh the page to show updated group
                window.location.reload();
            } else {
                console.error('Failed to add members');
                alert('Failed to add members. Please try again.');
            }
        } catch (error) {
            console.error('Error adding members:', error);
            alert('Error adding members. Please check your connection and try again.');
        }
    }

    // Reset add members form
    resetAddMembersForm() {
        const selectedContainer = document.getElementById('addMemberSelected');
        const searchInput = document.getElementById('addMemberSearch');
        
        if (selectedContainer) selectedContainer.innerHTML = '';
        if (searchInput) searchInput.value = '';
        
        this.updateAddMemberCount();
    }

    // Filter add member users
    filterAddMemberUsers(searchTerm) {
        const usersContainer = document.getElementById('addMemberUsers');
        if (!usersContainer) return;

        const searchLower = searchTerm.toLowerCase();
        const userElements = usersContainer.querySelectorAll('.flex');

        userElements.forEach(element => {
            const userName = element.querySelector('.text-sm.font-medium').textContent.toLowerCase();
            const userRole = element.querySelector('.text-xs.text-gray-500').textContent.toLowerCase();
            
            if (userName.includes(searchLower) || userRole.includes(searchLower)) {
                element.style.display = 'flex';
            } else {
                element.style.display = 'none';
            }
        });
    }

    // Initialize member management
    initializeMemberManagement() {
        const addMembersBtn = document.getElementById('addMembersBtn');
        const addMembersModal = document.getElementById('addMembersModal');
        const closeAddMembersModal = document.getElementById('closeAddMembersModal');
        const cancelAddMembers = document.getElementById('cancelAddMembers');
        const confirmAddMembers = document.getElementById('confirmAddMembers');
        const addMemberSearch = document.getElementById('addMemberSearch');

        // Add members to existing group
        if (addMembersBtn) {
            addMembersBtn.addEventListener('click', () => {
                addMembersModal.classList.remove('hidden');
                this.loadAvailableUsersForGroup();
            });
        }

        // Close add members modal
        if (closeAddMembersModal) {
            closeAddMembersModal.addEventListener('click', () => {
                addMembersModal.classList.add('hidden');
                this.resetAddMembersForm();
            });
        }

        if (cancelAddMembers) {
            cancelAddMembers.addEventListener('click', () => {
                addMembersModal.classList.add('hidden');
                this.resetAddMembersForm();
            });
        }

        // Add member search
        if (addMemberSearch) {
            addMemberSearch.addEventListener('input', (e) => {
                this.filterAddMemberUsers(e.target.value);
            });
        }

        // Confirm adding members
        if (confirmAddMembers) {
            confirmAddMembers.addEventListener('click', () => {
                this.addMembersToGroup();
            });
        }
    }

    // Initialize new chat functionality
    initializeNewChat() {
        const newChatBtn = document.getElementById('newChatBtn');
        const newChatModal = document.getElementById('newChatModal');
        const closeNewChatModal = document.getElementById('closeNewChatModal');
        const cancelNewChat = document.getElementById('cancelNewChat');
        const individualChatTab = document.getElementById('individualChatTab');
        const groupChatTab = document.getElementById('groupChatTab');
        const individualChatSection = document.getElementById('individualChatSection');
        const groupChatSection = document.getElementById('groupChatSection');
        const startNewChat = document.getElementById('startNewChat');

        // Open new chat modal
        if (newChatBtn) {
            newChatBtn.addEventListener('click', () => {
                newChatModal.classList.remove('hidden');
                this.loadAvailableUsersForNewChat();
            });
        }

        // Close modal
        if (closeNewChatModal) {
            closeNewChatModal.addEventListener('click', () => {
                newChatModal.classList.add('hidden');
                this.resetNewChatForm();
            });
        }

        if (cancelNewChat) {
            cancelNewChat.addEventListener('click', () => {
                newChatModal.classList.add('hidden');
                this.resetNewChatForm();
            });
        }

        // Tab switching
        if (individualChatTab && groupChatTab) {
            individualChatTab.addEventListener('click', () => {
                this.switchToIndividualChat();
            });

            groupChatTab.addEventListener('click', () => {
                this.switchToGroupChat();
            });
        }

        // Start new chat
        if (startNewChat) {
            startNewChat.addEventListener('click', () => {
                this.startNewChat();
            });
        }

        // Search functionality for new chat modal
        const individualUserSearch = document.getElementById('individualUserSearch');
        const newGroupUserSearch = document.getElementById('newGroupUserSearch');

        if (individualUserSearch) {
            let searchTimeout;
            individualUserSearch.addEventListener('input', (e) => {
                const searchTerm = e.target.value.trim();
                
                // Clear previous timeout
                clearTimeout(searchTimeout);
                
                // Set new timeout for debounced search
                searchTimeout = setTimeout(() => {
                    if (searchTerm.length >= 2) {
                        this.searchUsers(searchTerm);
                    } else if (searchTerm.length === 0) {
                        this.loadAvailableUsersForNewChat();
                    }
                }, 300); // 300ms delay
            });
        }

        if (newGroupUserSearch) {
            let searchTimeout;
            newGroupUserSearch.addEventListener('input', (e) => {
                const searchTerm = e.target.value.trim();
                
                // Clear previous timeout
                clearTimeout(searchTimeout);
                
                // Set new timeout for debounced search
                searchTimeout = setTimeout(() => {
                    if (searchTerm.length >= 2) {
                        this.searchUsers(searchTerm);
                    } else if (searchTerm.length === 0) {
                        this.loadAvailableUsersForNewChat();
                    }
                }, 300); // 300ms delay
            });
        }
    }

    // Switch to individual chat tab
    switchToIndividualChat() {
        const individualChatTab = document.getElementById('individualChatTab');
        const groupChatTab = document.getElementById('groupChatTab');
        const individualChatSection = document.getElementById('individualChatSection');
        const groupChatSection = document.getElementById('groupChatSection');
        const startNewChat = document.getElementById('startNewChat');

        if (individualChatTab && groupChatTab && individualChatSection && groupChatSection && startNewChat) {
            individualChatTab.classList.add('text-blue-600', 'border-blue-600');
            individualChatTab.classList.remove('text-gray-500');
            groupChatTab.classList.remove('text-blue-600', 'border-blue-600');
            groupChatTab.classList.add('text-gray-500');
            
            individualChatSection.classList.remove('hidden');
            groupChatSection.classList.add('hidden');
            
            startNewChat.textContent = 'Start Individual Chat';
        }
    }

    // Switch to group chat tab
    switchToGroupChat() {
        console.log('switchToGroupChat called');
        
        const individualChatTab = document.getElementById('individualChatTab');
        const groupChatTab = document.getElementById('groupChatTab');
        const individualChatSection = document.getElementById('individualChatSection');
        const groupChatSection = document.getElementById('groupChatSection');
        const startNewChat = document.getElementById('startNewChat');

        console.log('Elements found:', {
            individualChatTab: !!individualChatTab,
            groupChatTab: !!groupChatTab,
            individualChatSection: !!individualChatSection,
            groupChatSection: !!groupChatSection,
            startNewChat: !!startNewChat
        });

        if (individualChatTab && groupChatTab && individualChatSection && groupChatSection && startNewChat) {
            console.log('Switching to group chat tab');
            groupChatTab.classList.add('text-blue-600', 'border-blue-600');
            groupChatTab.classList.remove('text-gray-500');
            individualChatTab.classList.remove('text-blue-600', 'border-blue-600');
            individualChatTab.classList.add('text-gray-500');
            
            groupChatSection.classList.remove('hidden');
            individualChatSection.classList.add('hidden');
            
            startNewChat.textContent = 'Create Group Chat';
            console.log('Group chat tab activated');
        } else {
            console.error('Some elements not found for group chat tab switching');
        }
    }

    // Load available users for new chat
    async loadAvailableUsersForNewChat() {
        console.log('Loading available users for new chat...');
        try {
            const response = await fetch('/users/available', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            console.log('Response status:', response.status);
            if (response.ok) {
                const data = await response.json();
                console.log('Available users data:', data);
                console.log('Users count:', data.users ? data.users.length : 0);
                
                if (data.users && data.users.length > 0) {
                    this.renderIndividualUsers(data.users);
                    this.renderNewGroupUsers(data.users);
                    console.log('Users rendered successfully');
                } else {
                    console.warn('No users available');
                    this.showNoUsersMessage();
                }
            } else {
                console.error('Failed to load available users, status:', response.status);
                this.showNoUsersMessage();
            }
        } catch (error) {
            console.error('Error loading available users:', error);
            this.showNoUsersMessage();
        }
    }

    // Show no users message
    showNoUsersMessage() {
        const individualUsersContainer = document.getElementById('individualUsers');
        const newGroupUsersContainer = document.getElementById('newGroupUsers');
        
        if (individualUsersContainer) {
            individualUsersContainer.innerHTML = `
                <div class="text-center text-gray-500 py-4">
                    <p class="text-sm">No users available</p>
                    <p class="text-xs mt-1">Please check your course enrollments</p>
                </div>
            `;
        }
        
        if (newGroupUsersContainer) {
            newGroupUsersContainer.innerHTML = `
                <div class="text-center text-gray-500 py-4">
                    <p class="text-sm">No users available</p>
                    <p class="text-xs mt-1">Please check your course enrollments</p>
                </div>
            `;
        }
    }

    // Render individual users
    renderIndividualUsers(users) {
        console.log('Rendering individual users:', users);
        const usersContainer = document.getElementById('individualUsers');
        if (!usersContainer) {
            console.error('individualUsers container not found');
            return;
        }

        console.log('Clearing individual users container');
        usersContainer.innerHTML = '';

        if (!users || users.length === 0) {
            console.warn('No users to render');
            usersContainer.innerHTML = `
                <div class="text-center text-gray-500 py-4">
                    <p class="text-sm">No users available</p>
                </div>
            `;
            return;
        }

        console.log(`Rendering ${users.length} individual users`);
        users.forEach((user, index) => {
            console.log(`Rendering user ${index + 1}:`, user);
            const userElement = document.createElement('div');
            userElement.className = 'flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer';
            userElement.innerHTML = `
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                    ${user.name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">${user.name}</div>
                    <div class="text-xs text-gray-500">${user.role}</div>
                </div>
                <button type="button" class="select-individual-user text-blue-600 hover:text-blue-800" data-user-id="${user.id}" data-user-name="${user.name}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
            `;

            const selectBtn = userElement.querySelector('.select-individual-user');
            selectBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                console.log('Individual user selected:', user);
                this.selectIndividualUser(user);
            });

            usersContainer.appendChild(userElement);
        });
        
        console.log('Individual users rendering completed');
    }

    // Render new group users
    renderNewGroupUsers(users) {
        const usersContainer = document.getElementById('newGroupUsers');
        if (!usersContainer) return;

        usersContainer.innerHTML = '';

        users.forEach(user => {
            const userElement = document.createElement('div');
            userElement.className = 'flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer';
            userElement.innerHTML = `
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                    ${user.name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">${user.name}</div>
                    <div class="text-xs text-gray-500">${user.role}</div>
                </div>
                <button type="button" class="add-to-new-group text-blue-600 hover:text-blue-800" data-user-id="${user.id}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            `;

            const addBtn = userElement.querySelector('.add-to-new-group');
            addBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.addUserToNewGroup(user);
            });

            usersContainer.appendChild(userElement);
        });
    }

    // Select individual user
    selectIndividualUser(user) {
        console.log('Selecting individual user:', user);
        const selectedContainer = document.getElementById('selectedIndividualUser');
        if (!selectedContainer) {
            console.error('selectedIndividualUser container not found');
            return;
        }

        // Clear any previous selections
        this.clearSelectedIndividualUser();

        selectedContainer.innerHTML = `
            <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                    ${user.name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">${user.name}</div>
                    <div class="text-xs text-gray-500">${user.role}</div>
                </div>
                <button type="button" class="remove-individual-user text-red-600 hover:text-red-800" data-user-id="${user.id}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        const removeBtn = selectedContainer.querySelector('.remove-individual-user');
        removeBtn.addEventListener('click', () => {
            this.clearSelectedIndividualUser();
        });

        // Store selected user
        this.selectedIndividualUser = user;
        console.log('Individual user selected and stored:', this.selectedIndividualUser);
        
        // Update the start chat button to show it's ready
        const startChatBtn = document.getElementById('startChatBtn');
        if (startChatBtn) {
            startChatBtn.textContent = `Start Chat with ${user.name}`;
            startChatBtn.disabled = false;
            startChatBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            startChatBtn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
        }
    }

    // Clear selected individual user
    clearSelectedIndividualUser() {
        const selectedContainer = document.getElementById('selectedIndividualUser');
        if (selectedContainer) {
            selectedContainer.innerHTML = '<p class="text-sm text-gray-500">No user selected</p>';
        }
        this.selectedIndividualUser = null;
        
        // Reset the start chat button
        const startChatBtn = document.getElementById('startChatBtn');
        if (startChatBtn) {
            startChatBtn.textContent = 'Start Chat';
            startChatBtn.disabled = true;
            startChatBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
            startChatBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
        }
    }

    // Add user to new group
    addUserToNewGroup(user) {
        const selectedContainer = document.getElementById('newGroupSelectedMembers');
        const countElement = document.getElementById('newGroupSelectedCount');
        
        if (!selectedContainer || !countElement) return;
        
        // Check if user is already selected
        if (selectedContainer.querySelector(`[data-user-id="${user.id}"]`)) return;
        
        const userElement = document.createElement('div');
        userElement.className = 'flex items-center space-x-3 p-2 bg-blue-50 rounded';
        userElement.setAttribute('data-user-id', user.id);
        userElement.innerHTML = `
            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                ${user.name.charAt(0).toUpperCase()}
            </div>
            <div class="flex-1">
                <div class="text-sm font-medium text-gray-900">${user.name}</div>
                <div class="text-xs text-gray-500">${user.role}</div>
            </div>
            <button type="button" class="remove-from-new-group text-red-600 hover:text-red-800" data-user-id="${user.id}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;

        const removeBtn = userElement.querySelector('.remove-from-new-group');
        removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userElement.remove();
            this.updateNewGroupSelectedCount();
        });

        selectedContainer.appendChild(userElement);
        this.updateNewGroupSelectedCount();
    }

    // Update new group selected count
    updateNewGroupSelectedCount() {
        const selectedContainer = document.getElementById('newGroupSelectedMembers');
        const countElement = document.getElementById('newGroupSelectedCount');
        
        if (selectedContainer && countElement) {
            const count = selectedContainer.children.length;
            countElement.textContent = count;
        }
    }

    // Filter individual users
    filterIndividualUsers(searchTerm) {
        const usersContainer = document.getElementById('individualUsers');
        if (!usersContainer) return;

        const searchLower = searchTerm.toLowerCase();
        const userElements = usersContainer.querySelectorAll('.flex');

        userElements.forEach(element => {
            const userName = element.querySelector('.text-sm.font-medium').textContent.toLowerCase();
            const userRole = element.querySelector('.text-xs.text-gray-500').textContent.toLowerCase();
            
            if (userName.includes(searchLower) || userRole.includes(searchLower)) {
                element.style.display = 'flex';
            } else {
                element.style.display = 'none';
            }
        });
    }

    // Filter new group users
    filterNewGroupUsers(searchTerm) {
        const usersContainer = document.getElementById('newGroupUsers');
        if (!usersContainer) return;

        const searchLower = searchTerm.toLowerCase();
        const userElements = usersContainer.querySelectorAll('.flex');

        userElements.forEach(element => {
            const userName = element.querySelector('.text-sm.font-medium').textContent.toLowerCase();
            const userRole = element.querySelector('.text-xs.text-gray-500').textContent.toLowerCase();
            
            if (userName.includes(searchLower) || userRole.includes(searchLower)) {
                element.style.display = 'flex';
            } else {
                element.style.display = 'none';
            }
        });
    }

    // Start new chat
    async startNewChat() {
        console.log('startNewChat called');
        
        const individualChatTab = document.getElementById('individualChatTab');
        const isIndividualChat = individualChatTab.classList.contains('text-blue-600');
        
        console.log('Individual chat tab:', individualChatTab);
        console.log('Is individual chat:', isIndividualChat);

        if (isIndividualChat) {
            console.log('Starting individual chat...');
            await this.startIndividualChat();
        } else {
            console.log('Starting group chat...');
            await this.createNewGroupChat();
        }
    }

    // Start individual chat
    async startIndividualChat() {
        console.log('Starting individual chat...');
        console.log('Selected individual user:', this.selectedIndividualUser);
        
        if (!this.selectedIndividualUser) {
            console.error('No user selected for individual chat');
            // Remove the alert since the chat is working
            return;
        }

        // Store the selected user before resetting the form
        const selectedUser = this.selectedIndividualUser;
        console.log('Starting chat with user:', selectedUser.name);

        // Close modal
        document.getElementById('newChatModal').classList.add('hidden');
        this.resetNewChatForm();

        // Update the chat interface
        const chatTitle = document.getElementById('chatTitle');
        const chatSubtitle = document.getElementById('chatSubtitle');
        const chatAvatar = document.getElementById('chatAvatar');
        const welcomeScreen = document.getElementById('welcomeScreen');
        const chatArea = document.getElementById('chatArea');
        const chatHeader = document.getElementById('chatHeader');

        if (chatTitle) chatTitle.textContent = selectedUser.name;
        if (chatSubtitle) chatSubtitle.textContent = 'Individual chat';
        if (chatAvatar) {
            chatAvatar.textContent = selectedUser.name.charAt(0).toUpperCase();
            chatAvatar.className = 'w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold';
        }

        if (welcomeScreen) welcomeScreen.classList.add('hidden');
        if (chatArea) chatArea.classList.remove('hidden');
        if (chatHeader) chatHeader.classList.remove('hidden');

        // Load the individual chat
        await this.loadIndividualChat(selectedUser.id);
        
        // Add user to sidebar if not already there
        this.addUserToSidebar(selectedUser.id, selectedUser.name);
        
        console.log('Individual chat started successfully');
    }

    // Create new group chat
    async createNewGroupChat() {
        console.log('createNewGroupChat called');
        
        const groupName = document.getElementById('newGroupName').value.trim();
        console.log('Group name:', groupName);
        
        if (!groupName) {
            alert('Please enter a group name');
            return;
        }

        const selectedContainer = document.getElementById('newGroupSelectedMembers');
        console.log('Selected container:', selectedContainer);
        console.log('Selected container children length:', selectedContainer ? selectedContainer.children.length : 'container not found');
        
        if (!selectedContainer || selectedContainer.children.length === 0) {
            alert('Please select at least one member');
            return;
        }

        const formData = new FormData();
        formData.append('name', groupName);
        formData.append('description', document.getElementById('newGroupDescription').value);
        formData.append('type', document.getElementById('newGroupType').value);
        formData.append('max_members', document.getElementById('newGroupMaxMembers').value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Get selected member IDs
        const selectedUserIds = Array.from(selectedContainer.children).map(el => el.getAttribute('data-user-id'));
        console.log('Selected user IDs:', selectedUserIds);
        formData.append('members', JSON.stringify(selectedUserIds));

        console.log('Sending request to /groups');
        try {
            const response = await fetch('/groups', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });

            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);

            if (response.ok) {
                const data = await response.json();
                console.log('Group created successfully:', data);
                
                // Close modal and refresh
                document.getElementById('newChatModal').classList.add('hidden');
                this.resetNewChatForm();
                window.location.reload();
            } else {
                const errorText = await response.text();
                console.error('Failed to create group. Response:', errorText);
                alert('Failed to create group. Please try again.');
            }
        } catch (error) {
            console.error('Error creating group:', error);
            alert('Error creating group. Please check your connection and try again.');
        }
    }

    // Reset new chat form
    resetNewChatForm() {
        this.selectedIndividualUser = null;
        this.clearSelectedIndividualUser();
        
        const newGroupSelectedMembers = document.getElementById('newGroupSelectedMembers');
        if (newGroupSelectedMembers) newGroupSelectedMembers.innerHTML = '';
        
        this.updateNewGroupSelectedCount();
        
        // Reset form fields
        const newGroupName = document.getElementById('newGroupName');
        const newGroupDescription = document.getElementById('newGroupDescription');
        const individualUserSearch = document.getElementById('individualUserSearch');
        const newGroupUserSearch = document.getElementById('newGroupUserSearch');
        
        if (newGroupName) newGroupName.value = '';
        if (newGroupDescription) newGroupDescription.value = '';
        if (individualUserSearch) individualUserSearch.value = '';
        if (newGroupUserSearch) newGroupUserSearch.value = '';
        
        // Switch back to individual chat tab
        this.switchToIndividualChat();
    }

    // Add message to chat
    addMessageToChat(message) {
        console.log('addMessageToChat called with:', message);
        const messagesContainer = document.getElementById('messagesContainer');
        if (!messagesContainer) {
            console.error('Messages container not found');
            return;
        }

        // Remove "no messages" placeholder if it exists
        const noMessagesPlaceholder = messagesContainer.querySelector('.text-center.text-gray-500');
        if (noMessagesPlaceholder) {
            noMessagesPlaceholder.remove();
        }

        const messageElement = this.createMessageElement(message);
        messagesContainer.appendChild(messageElement);
        
        // Check if user is near bottom before scrolling
        const { scrollTop, scrollHeight, clientHeight } = messagesContainer;
        const isNearBottom = (scrollHeight - scrollTop - clientHeight) < 100;
        
        if (isNearBottom) {
            // User is near bottom, scroll to show new message
            this.scrollToBottom();
        } else {
            // User is not near bottom, show scroll indicator
            this.showScrollIndicator();
        }
        
        console.log('Message added to chat successfully');
    }

    // Create message element
    createMessageElement(message) {
        const messageDiv = document.createElement('div');
        const isOwnMessage = message.sender_id == this.currentUserId;
        
        messageDiv.className = `flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mb-4`;
        
        let messageContent = '';
        
        if (message.message_type === 'file') {
            messageContent = `
                <div class="flex items-center space-x-2 p-2 bg-gray-100 rounded">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                    <a href="/messages/${message.id}/download" class="text-blue-600 hover:text-blue-800 text-sm">
                        ${message.file_name}
                    </a>
                </div>
            `;
        } else {
            messageContent = `<p class="text-gray-800">${message.content}</p>`;
        }

        // Safely get sender name
        const senderName = message.sender && message.sender.name ? message.sender.name : 'Unknown User';
        const senderInitial = senderName.charAt(0).toUpperCase();

        messageDiv.innerHTML = `
            <div class="flex items-end space-x-2 max-w-xs lg:max-w-md">
                ${!isOwnMessage ? `
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                        ${senderInitial}
                    </div>
                ` : ''}
                <div class="${isOwnMessage ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800'} rounded-lg px-4 py-2 shadow-sm">
                    ${!isOwnMessage ? `<p class="text-xs text-gray-500 mb-1">${senderName}</p>` : ''}
                    ${messageContent}
                    <p class="text-xs ${isOwnMessage ? 'text-blue-100' : 'text-gray-500'} mt-1">
                        ${new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                    </p>
                </div>
            </div>
        `;

        return messageDiv;
    }

    // Render messages
    renderMessages(messages) {
        const messagesContainer = document.getElementById('messagesContainer');
        if (!messagesContainer) return;

        messagesContainer.innerHTML = '';

        if (messages.length === 0) {
            messagesContainer.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p class="text-sm">No messages yet</p>
                    <p class="text-xs mt-1">Start the conversation!</p>
                </div>
            `;
            return;
        }

        messages.forEach(message => {
            const messageElement = this.createMessageElement(message);
            messagesContainer.appendChild(messageElement);
        });

        // Use instant scroll for initial load
        this.scrollToBottomInstant();
    }

    // Search users in real-time
    async searchUsers(searchTerm) {
        if (!searchTerm || searchTerm.length < 2) {
            // If search term is too short, load all available users
            this.loadAvailableUsersForNewChat();
            return;
        }

        try {
            const response = await fetch(`/users/search?search=${encodeURIComponent(searchTerm)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                console.log('Search results:', data);
                
                if (data.users && data.users.length > 0) {
                    this.renderIndividualUsers(data.users);
                    this.renderNewGroupUsers(data.users);
                } else {
                    this.showNoSearchResults(searchTerm);
                }
            } else {
                console.error('Search failed');
                this.showNoSearchResults(searchTerm);
            }
        } catch (error) {
            console.error('Search error:', error);
            this.showNoSearchResults(searchTerm);
        }
    }

    // Show no search results message
    showNoSearchResults(searchTerm) {
        const individualUsersContainer = document.getElementById('individualUsers');
        const newGroupUsersContainer = document.getElementById('newGroupUsers');
        
        if (individualUsersContainer) {
            individualUsersContainer.innerHTML = `
                <div class="text-center text-gray-500 py-4">
                    <p class="text-sm">No users found for "${searchTerm}"</p>
                    <p class="text-xs mt-1">Try a different search term</p>
                </div>
            `;
        }
        
        if (newGroupUsersContainer) {
            newGroupUsersContainer.innerHTML = `
                <div class="text-center text-gray-500 py-4">
                    <p class="text-sm">No users found for "${searchTerm}"</p>
                    <p class="text-xs mt-1">Try a different search term</p>
                </div>
            `;
        }
    }

    // Debug function to check current state
    debugCurrentState() {
        console.log('=== DEBUG CURRENT STATE ===');
        console.log('Selected individual user:', this.selectedIndividualUser);
        console.log('Current chat type:', this.currentChatType);
        console.log('Current chat ID:', this.currentChatId);
        console.log('Current user ID:', this.currentUserId);
        
        const selectedContainer = document.getElementById('selectedIndividualUser');
        console.log('Selected container exists:', !!selectedContainer);
        if (selectedContainer) {
            console.log('Selected container HTML:', selectedContainer.innerHTML);
        }
        
        const modal = document.getElementById('newChatModal');
        console.log('Modal exists:', !!modal);
        if (modal) {
            console.log('Modal hidden:', modal.classList.contains('hidden'));
        }
        
        return {
            selectedIndividualUser: this.selectedIndividualUser,
            currentChatType: this.currentChatType,
            currentChatId: this.currentChatId,
            selectedContainerExists: !!selectedContainer,
            modalExists: !!modal
        };
    }

    // Add user to sidebar after sending message
    addUserToSidebar(userId, userName) {
        console.log('Adding user to sidebar:', userId, userName);
        
        const individualChatsContainer = document.getElementById('individualChats');
        if (!individualChatsContainer) {
            console.error('Individual chats container not found');
            return;
        }
        
        // Check if user is already in sidebar
        const existingChat = individualChatsContainer.querySelector(`[data-user-id="${userId}"]`);
        if (existingChat) {
            console.log('User already exists in sidebar, moving to top');
            // Move existing chat to top
            individualChatsContainer.insertBefore(existingChat, individualChatsContainer.firstChild);
            return;
        }
        
        // Create new chat item
        const chatItem = document.createElement('div');
        chatItem.className = 'chat-item p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer';
        chatItem.setAttribute('data-user-id', userId);
        chatItem.setAttribute('data-user-name', userName);
        chatItem.setAttribute('data-chat-type', 'individual');
        
        chatItem.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                    ${userName.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900 truncate">${userName}</p>
                        <span class="text-xs text-gray-500">User</span>
                    </div>
                    <p class="text-xs text-gray-500 truncate">Click to continue chatting</p>
                </div>
            </div>
        `;
        
        // Add click event listener
        chatItem.addEventListener('click', () => {
            this.selectIndividualChat(userId, userName);
        });
        
        // Add to the beginning of the list
        individualChatsContainer.insertBefore(chatItem, individualChatsContainer.firstChild);
        
        console.log('User added to sidebar successfully');
    }

    // Initialize delete group functionality
    initializeDeleteGroup() {
        console.log('Initializing delete chat functionality...');
        
        // Get all required elements
        const chatInfoBtn = document.getElementById('chatInfoBtn');
        const deleteGroupModal = document.getElementById('deleteGroupModal');
        const closeDeleteGroupModal = document.getElementById('closeDeleteGroupModal');
        const cancelDeleteGroup = document.getElementById('cancelDeleteGroup');
        const confirmDeleteGroup = document.getElementById('confirmDeleteGroup');
        const deleteModalTitle = document.getElementById('deleteModalTitle');
        const deleteModalMessage = document.getElementById('deleteModalMessage');

        console.log('Elements found:', {
            chatInfoBtn: !!chatInfoBtn,
            deleteGroupModal: !!deleteGroupModal,
            closeDeleteGroupModal: !!closeDeleteGroupModal,
            cancelDeleteGroup: !!cancelDeleteGroup,
            confirmDeleteGroup: !!confirmDeleteGroup,
            deleteModalTitle: !!deleteModalTitle,
            deleteModalMessage: !!deleteModalMessage
        });

        // Add click event to delete button
        if (chatInfoBtn) {
            console.log('Adding click event to delete button...');
            chatInfoBtn.onclick = () => {
                console.log('Delete button clicked!');
                console.log('Current chat type:', this.currentChatType);
                console.log('Current chat ID:', this.currentChatId);
                
                if (this.currentChatType && this.currentChatId) {
                    console.log('Showing delete modal...');
                    if (deleteGroupModal) {
                        // Update modal content based on chat type
                        if (this.currentChatType === 'individual') {
                            if (deleteModalTitle) deleteModalTitle.textContent = 'Delete Conversation';
                            if (deleteModalMessage) {
                                deleteModalMessage.innerHTML = `
                                    <p class="text-gray-600 mb-4">Are you sure you want to delete this conversation?</p>
                                    <p class="text-sm text-gray-500">This action cannot be undone. All messages between you and this user will be permanently removed.</p>
                                `;
                            }
                            if (confirmDeleteGroup) confirmDeleteGroup.textContent = 'Delete Conversation';
                        } else if (this.currentChatType === 'group') {
                            if (deleteModalTitle) deleteModalTitle.textContent = 'Delete Group';
                            if (deleteModalMessage) {
                                deleteModalMessage.innerHTML = `
                                    <p class="text-gray-600 mb-4">Are you sure you want to delete this group?</p>
                                    <p class="text-sm text-gray-500">This action cannot be undone. All group messages and members will be permanently removed.</p>
                                `;
                            }
                            if (confirmDeleteGroup) confirmDeleteGroup.textContent = 'Delete Group';
                        }
                        
                        deleteGroupModal.classList.remove('hidden');
                    } else {
                        console.error('Delete modal not found');
                    }
                } else {
                    console.log('No chat selected');
                    alert('Please select a chat to delete');
                }
            };
        } else {
            console.error('Delete button not found!');
        }

        // Add click event to close button
        if (closeDeleteGroupModal) {
            closeDeleteGroupModal.onclick = () => {
                console.log('Closing delete modal...');
                deleteGroupModal.classList.add('hidden');
            };
        }

        // Add click event to cancel button
        if (cancelDeleteGroup) {
            cancelDeleteGroup.onclick = () => {
                console.log('Canceling delete...');
                deleteGroupModal.classList.add('hidden');
            };
        }

        // Add click event to confirm button
        if (confirmDeleteGroup) {
            confirmDeleteGroup.onclick = () => {
                console.log('Confirming delete...');
                this.deleteChat();
            };
        }

        // Close modal when clicking outside
        if (deleteGroupModal) {
            deleteGroupModal.onclick = (e) => {
                if (e.target === deleteGroupModal) {
                    console.log('Closing modal by clicking outside...');
                    deleteGroupModal.classList.add('hidden');
                }
            };
        }
        
        console.log('Delete chat functionality initialized');
    }

    // Delete chat method (handles both individual and group chats)
    async deleteChat() {
        if (!this.currentChatType || !this.currentChatId) {
            console.error('No chat selected for deletion');
            return;
        }

        try {
            let response;
            let endpoint;
            
            if (this.currentChatType === 'individual') {
                endpoint = `/chats/individual/${this.currentChatId}`;
            } else if (this.currentChatType === 'group') {
                endpoint = `/groups/${this.currentChatId}`;
            } else {
                console.error('Invalid chat type');
                return;
            }

            response = await fetch(endpoint, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                console.log('Chat deleted successfully:', data);
                
                // Close modal
                const deleteGroupModal = document.getElementById('deleteGroupModal');
                if (deleteGroupModal) {
                    deleteGroupModal.classList.add('hidden');
                }
                
                // Remove chat from sidebar
                if (this.currentChatType === 'group') {
                    this.removeGroupFromSidebar(this.currentChatId);
                } else {
                    this.removeIndividualChatFromSidebar(this.currentChatId);
                }
                
                // Reset current chat
                this.currentChatType = null;
                this.currentChatId = null;
                
                // Show welcome screen
                this.showWelcomeScreen();
                
                // Show success message
                const successMessage = this.currentChatType === 'individual' ? 'Conversation deleted successfully' : 'Group deleted successfully';
                this.showNotification(successMessage, 'success');
                
                // Reload page to refresh chat list
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
                
            } else {
                const errorData = await response.json();
                console.error('Failed to delete chat:', errorData);
                this.showNotification(errorData.error || 'Failed to delete chat', 'error');
            }
        } catch (error) {
            console.error('Error deleting chat:', error);
            this.showNotification('An error occurred while deleting the chat', 'error');
        }
    }

    // Remove individual chat from sidebar
    removeIndividualChatFromSidebar(userId) {
        const individualChatsContainer = document.getElementById('individualChats');
        if (!individualChatsContainer) return;

        const chatItem = individualChatsContainer.querySelector(`[data-user-id="${userId}"]`);
        if (chatItem) {
            chatItem.remove();
        }
    }

    // Remove group from sidebar
    removeGroupFromSidebar(groupId) {
        const groupChatsContainer = document.getElementById('groupChats');
        if (!groupChatsContainer) return;

        const groupItem = groupChatsContainer.querySelector(`[data-group-id="${groupId}"]`);
        if (groupItem) {
            groupItem.remove();
        }
    }

    // Show welcome screen
    showWelcomeScreen() {
        const welcomeScreen = document.getElementById('welcomeScreen');
        const chatArea = document.getElementById('chatArea');
        const chatHeader = document.getElementById('chatHeader');

        if (welcomeScreen) welcomeScreen.classList.remove('hidden');
        if (chatArea) chatArea.classList.add('hidden');
        if (chatHeader) chatHeader.classList.add('hidden');
    }

    // Show notification
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
        
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        notification.className += ` ${bgColor} text-white`;
        
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 hover:opacity-75">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }
}

// Initialize Teams Chat when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing TeamsChat...');
    window.teamsChat = new TeamsChat();
    console.log('TeamsChat initialized successfully');
    
    // Make loadIndividualChat and loadGroupChat available globally
    window.loadIndividualChat = (userId) => window.teamsChat.loadIndividualChat(userId);
    window.loadGroupChat = (groupId) => window.teamsChat.loadGroupChat(groupId);
}); 