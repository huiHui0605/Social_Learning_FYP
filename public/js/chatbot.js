document.addEventListener('DOMContentLoaded', () => {
    const chatbotIcon = document.getElementById('chatbotIcon');
    const chatbotWindow = document.getElementById('chatbotWindow');
    const closeChatbot = document.getElementById('closeChatbot');
    const chatbotMessages = document.getElementById('chatbotMessages');
    const chatbotInput = document.getElementById('chatbotInput');
    const sendChatbotMessage = document.getElementById('sendChatbotMessage');

    const toggleChatbot = () => {
        chatbotWindow.classList.toggle('hidden');
        if (!chatbotWindow.classList.contains('hidden')) {
            chatbotInput.focus();
            // Add robot activation effect
            chatbotIcon.style.animation = 'robotWiggle 0.8s ease-in-out';
            setTimeout(() => {
                chatbotIcon.style.animation = 'robotPulse 2s infinite';
            }, 800);
        }
    };

    chatbotIcon.addEventListener('click', toggleChatbot);
    closeChatbot.addEventListener('click', toggleChatbot);

    const addMessage = (message, sender) => {
        const messageElement = document.createElement('div');
        messageElement.classList.add('mb-4', 'flex', 'chatbot-message', sender === 'user' ? 'justify-end' : 'justify-start');
        messageElement.innerHTML = `
            <div class="p-3 rounded-lg shadow-sm ${sender === 'user' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white' : 'bg-gray-100 text-gray-800 border-l-4 border-blue-500'}">
                ${message}
            </div>
        `;
        chatbotMessages.appendChild(messageElement);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        
        // Add robot thinking animation for bot messages
        if (sender === 'bot') {
            chatbotIcon.style.animation = 'robotThink 1s ease-in-out';
            setTimeout(() => {
                chatbotIcon.style.animation = 'robotPulse 2s infinite';
            }, 1000);
        }
    };

    const addTypingIndicator = () => {
        const typingIndicator = document.createElement('div');
        typingIndicator.id = 'typingIndicator';
        typingIndicator.classList.add('mb-4', 'flex', 'justify-start');
        typingIndicator.innerHTML = `
            <div class="p-3 rounded-lg bg-gray-100 text-gray-500 border-l-4 border-blue-500">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                        <rect x="4" y="4" width="16" height="16" rx="2" fill="currentColor"/>
                        <circle cx="8" cy="10" r="1" fill="white"/>
                        <circle cx="16" cy="10" r="1" fill="white"/>
                        <path d="M8 14h8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                </div>
            </div>
        `;
        chatbotMessages.appendChild(typingIndicator);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        
        // Add robot thinking animation
        chatbotIcon.style.animation = 'robotThink 1s ease-in-out infinite';
    };

    const removeTypingIndicator = () => {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
        // Reset robot animation
        chatbotIcon.style.animation = 'robotPulse 2s infinite';
    };

    const handleSendMessage = async () => {
        const userMessage = chatbotInput.value.trim();
        if (userMessage === '') return;

        addMessage(userMessage, 'user');
        chatbotInput.value = '';
        addTypingIndicator();

        try {
            const response = await fetch('/ai-chat/ask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ question: userMessage })
            });
            removeTypingIndicator();
            if (response.ok) {
                const data = await response.json();
                addMessage(data.answer, 'bot');
            } else {
                addMessage("Sorry, I couldn't get an answer from the AI service.", 'bot');
            }
        } catch (error) {
            removeTypingIndicator();
            addMessage("Sorry, there was a problem contacting the AI service.", 'bot');
        }
    };

    sendChatbotMessage.addEventListener('click', handleSendMessage);
    chatbotInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            handleSendMessage();
        }
    });

    // Add hover effects for robot icon
    chatbotIcon.addEventListener('mouseenter', () => {
        chatbotIcon.style.transform = 'scale(1.1) rotate(5deg)';
    });

    chatbotIcon.addEventListener('mouseleave', () => {
        chatbotIcon.style.transform = 'scale(1) rotate(0deg)';
    });

    // Make chatbot window draggable
    let isDragging = false;
    let dragOffsetX = 0;
    let dragOffsetY = 0;

    chatbotWindow.addEventListener('mousedown', function(e) {
        // Only drag if clicking the header or top area
        if (e.target.closest('#chatbotWindow')) {
            isDragging = true;
            dragOffsetX = e.clientX - chatbotWindow.getBoundingClientRect().left;
            dragOffsetY = e.clientY - chatbotWindow.getBoundingClientRect().top;
            chatbotWindow.style.transition = 'none';
        }
    });
    document.addEventListener('mousemove', function(e) {
        if (isDragging) {
            chatbotWindow.style.left = (e.clientX - dragOffsetX) + 'px';
            chatbotWindow.style.top = (e.clientY - dragOffsetY) + 'px';
            chatbotWindow.style.right = 'auto';
            chatbotWindow.style.bottom = 'auto';
            chatbotWindow.style.position = 'fixed';
        }
    });
    document.addEventListener('mouseup', function() {
        isDragging = false;
        chatbotWindow.style.transition = '';
    });

    // Initial greeting with robot activation
    setTimeout(() => {
        chatbotIcon.style.animation = 'robotWiggle 1s ease-in-out';
        setTimeout(() => {
            chatbotIcon.style.animation = 'robotPulse 2s infinite';
        }, 1000);
    }, 1000);

    setTimeout(() => {
        addMessage("🤖 Hello! I'm your AI learning assistant. How can I help you today? Feel free to ask me about learning materials, study tips, or how to use the platform!", 'bot');
    }, 2000);
});
