<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Chat Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the AI chatbot functionality.
    | You can enable/disable different AI services and configure their settings.
    |
    */

    // Enable/disable AI services
    'services' => [
        'google_ai' => env('AI_CHAT_GOOGLE_AI', true),
        'hugging_face' => env('AI_CHAT_HUGGING_FACE', true),
        'free_ai_service' => env('AI_CHAT_FREE_SERVICE', true),
        'rule_based' => env('AI_CHAT_RULE_BASED', true), // Always enabled as fallback
    ],

    // Google AI configuration
    'google_ai' => [
        'api_key' => env('GOOGLE_AI_API_KEY', 'AIzaSyDx0Fq1mJtsMaIKoVz3RnQ-pBBBuxvFMMA'),
        'model' => env('GOOGLE_AI_MODEL', 'gemini-1.5-flash'),
        'timeout' => 30,
        'max_tokens' => 1000,
        'temperature' => 0.7,
    ],

    // Hugging Face configuration
    'hugging_face' => [
        'model' => env('AI_CHAT_HF_MODEL', 'facebook/blenderbot-400M-distill'),
        'api_url' => 'https://api-inference.huggingface.co/models/',
        'timeout' => 30,
    ],

    // Free AI service configuration
    'free_ai_service' => [
        'api_url' => 'https://api.free-ai-chat.com/chat',
        'model' => 'gpt-3.5-turbo',
        'max_tokens' => 150,
        'timeout' => 10,
    ],

    // Rule-based responses configuration
    'rule_based' => [
        'enable_context' => true,
        'enable_patterns' => true,
        'default_response' => "Hi there! 👋 Welcome to our Social Learning Platform! I'm here to help you make the most of your collaborative learning experience. To best assist you, could you tell me a little more about what you'd like to know? For example, are you:
* **A student looking for information about a specific course?** Perhaps you'd like to know about the course content, assignments, deadlines, or the instructor? If so, please tell me the course name or code.
* **A student needing help navigating the platform?** I can guide you through features like accessing course materials, submitting assignments, joining group discussions, collaborating with peers, or contacting your instructors.
* **A lecturer looking for information on course management tools?** I can explain how to upload materials, grade assignments, track student progress, and communicate with your students and groups effectively.
* **A student or lecturer with a general academic or social learning question?** I can help with a wide range of topics, from research strategies to collaborative study tips. Just let me know your question!
* **Simply browsing and curious about the platform's offerings?** I can provide an overview of the courses and social features available, highlighting their benefits for collaborative learning.
I'm excited to help you on your social learning journey! Just let me know how I can assist you. ✨",
    ],

    // Chat interface settings
    'interface' => [
        'max_messages' => 50,
        'typing_delay' => 1000, // milliseconds
        'auto_scroll' => true,
    ],
]; 