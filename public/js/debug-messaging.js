// Debug script for messaging system
console.log('Debug script loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - starting debug checks');
    
    // Check if TeamsChat is loaded
    if (typeof TeamsChat === 'undefined') {
        console.error('❌ TeamsChat class not found');
    } else {
        console.log('✅ TeamsChat class found');
    }
    
    // Check if teams-chat.js is loaded
    if (typeof window.teamsChat === 'undefined') {
        console.error('❌ window.teamsChat not initialized');
    } else {
        console.log('✅ window.teamsChat initialized');
    }
    
    // Check for required DOM elements
    const requiredElements = [
        'newChatBtn',
        'searchChats',
        'individualTab',
        'groupTab',
        'individualChats',
        'groupChats',
        'welcomeScreen',
        'chatArea',
        'chatHeader',
        'chatTitle',
        'chatSubtitle',
        'chatAvatar',
        'messagesContainer',
        'messageInput',
        'sendMessageBtn',
        'fileUploadBtn'
    ];
    
    console.log('Checking required DOM elements:');
    requiredElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            console.log(`✅ ${elementId} found`);
        } else {
            console.error(`❌ ${elementId} NOT found`);
        }
    });
    
    // Check for conversation partners
    const chatItems = document.querySelectorAll('.chat-item');
    console.log(`Found ${chatItems.length} chat items`);
    
    chatItems.forEach((item, index) => {
        const chatType = item.dataset.chatType;
        const userId = item.dataset.userId;
        const groupId = item.dataset.groupId;
        const userName = item.dataset.userName;
        const groupName = item.dataset.groupName;
        
        console.log(`Chat item ${index + 1}:`, {
            type: chatType,
            userId: userId,
            groupId: groupId,
            userName: userName,
            groupName: groupName
        });
    });
    
    // Test click handlers
    console.log('Testing click handlers...');
    chatItems.forEach((item, index) => {
        item.addEventListener('click', function(e) {
            console.log(`Chat item ${index + 1} clicked:`, {
                type: this.dataset.chatType,
                userId: this.dataset.userId,
                groupId: this.dataset.groupId,
                userName: this.dataset.userName,
                groupName: this.dataset.groupName
            });
        });
    });
    
    // Test new chat button
    const newChatBtn = document.getElementById('newChatBtn');
    if (newChatBtn) {
        newChatBtn.addEventListener('click', function() {
            console.log('New chat button clicked');
        });
    }
    
    // Debug button functionality
    const debugBtn = document.getElementById('debugBtn');
    if (debugBtn) {
        debugBtn.addEventListener('click', function() {
            console.log('=== DEBUG INFO ===');
            console.log('Current user ID:', document.getElementById('currentUserId')?.value);
            console.log('TeamsChat instance:', window.teamsChat);
            console.log('Chat items count:', document.querySelectorAll('.chat-item').length);
            console.log('New chat modal:', document.getElementById('newChatModal'));
            console.log('Modal hidden:', document.getElementById('newChatModal')?.classList.contains('hidden'));
            
            // Call TeamsChat debug function if available
            if (window.teamsChat && typeof window.teamsChat.debugCurrentState === 'function') {
                console.log('=== TEAMSCHAT DEBUG STATE ===');
                const state = window.teamsChat.debugCurrentState();
                console.log('TeamsChat debug state:', state);
            }
            
            // Test API call
            fetch('/users/available', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                console.log('API Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('API Response data:', data);
                alert(`Debug Info:\n- TeamsChat loaded: ${!!window.teamsChat}\n- Chat items: ${document.querySelectorAll('.chat-item').length}\n- Available users: ${data.users ? data.users.length : 0}\n- Check console for details`);
            })
            .catch(error => {
                console.error('API Error:', error);
                alert(`Debug Info:\n- TeamsChat loaded: ${!!window.teamsChat}\n- Chat items: ${document.querySelectorAll('.chat-item').length}\n- API Error: ${error.message}\n- Check console for details`);
            });
        });
    }
    
    // Test API endpoints
    console.log('Testing API endpoints...');
    
    // Test getAvailableUsers
    fetch('/users/available', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => {
        console.log('getAvailableUsers response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('getAvailableUsers data:', data);
        console.log('Available users count:', data.users ? data.users.length : 0);
    })
    .catch(error => {
        console.error('getAvailableUsers error:', error);
    });
    
    console.log('Debug checks completed');
}); 