<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIChatController extends Controller
{
    public function ask(Request $request)
    {
        $question = $request->input('question');
        $courseTitle = $request->input('course_title');
        $courseDescription = $request->input('course_description');
        
        // 如果有课程信息，生成互动问题
        if ($courseTitle && $courseDescription) {
            $prompt = "你是一个智能学习助手。请根据以下课程内容，生成一个能帮助学生思考的互动问题。\n课程名称：$courseTitle\n简介：$courseDescription";
            // 直接用主AI服务生成问题
            $answer = null;
            if (config('ai-chat.services.google_ai')) {
                $answer = $this->tryGoogleAI($prompt);
            }
            if (!$answer && config('ai-chat.services.hugging_face')) {
                $answer = $this->tryAdvancedHuggingFaceAPI($prompt);
            }
            if (!$answer && config('ai-chat.services.free_ai_service')) {
                $answer = $this->tryAdvancedFreeAIService($prompt);
            }
            if (!$answer && config('ai-chat.services.rule_based')) {
                $answer = $this->tryAdvancedRuleBasedResponse($prompt);
            }
            return response()->json(['answer' => $answer]);
        }

        if (!$question) {
            return response()->json(['answer' => "Please provide a question."], 400);
        }

        // Try multiple AI services in order of preference
        $answer = null;
        
        // Primary: Google AI (Gemini)
        if (config('ai-chat.services.google_ai')) {
            $answer = $this->tryGoogleAI($question);
        }
        
        // Secondary: Hugging Face
        if (!$answer && config('ai-chat.services.hugging_face')) {
            $answer = $this->tryAdvancedHuggingFaceAPI($question);
        }
        
        // Tertiary: Free AI Service
        if (!$answer && config('ai-chat.services.free_ai_service')) {
            $answer = $this->tryAdvancedFreeAIService($question);
        }

        // Fallback: Rule-based responses
        if (!$answer && config('ai-chat.services.rule_based')) {
            $answer = $this->tryAdvancedRuleBasedResponse($question);
        }

        return response()->json(['answer' => $answer]);
    }

    private function tryGoogleAI($question)
    {
        try {
            $config = config('ai-chat.google_ai');
            $apiKey = $config['api_key'];
            $model = $config['model'];
            $apiUrl = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$apiKey}";
            
            // Enhanced prompt for educational context
            $enhancedPrompt = $this->createEducationalPrompt($question);
            
            $response = Http::timeout($config['timeout'])->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $enhancedPrompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => $config['max_tokens'],
                    'temperature' => $config['temperature'],
                    'topP' => 0.8,
                    'topK' => 40
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $answer = $data['candidates'][0]['content']['parts'][0]['text'];
                    return $this->cleanAndEnhanceResponse($answer, $question);
                }
            } else {
                \Log::error('Google AI API Error: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            \Log::error('Google AI API Exception: ' . $e->getMessage());
        }

        return null;
    }

    private function createEducationalPrompt($question)
    {
        $context = "You are an intelligent AI assistant for an e-learning platform. ";
        $context .= "Your role is to provide comprehensive, educational, and helpful responses to students and lecturers. ";
        $context .= "Focus on providing detailed explanations, practical guidance, and educational insights. ";
        $context .= "If the question is about the platform, courses, or education, provide comprehensive information. ";
        $context .= "If it's a general academic question, provide informative and well-structured answers. ";
        $context .= "Always be helpful, friendly, educational, and professional in your responses. ";
        $context .= "Use clear formatting, bullet points, and emojis when appropriate to make responses engaging and easy to read. ";
        $context .= "Keep your answer short and concise.\n\n";
        $context .= "User Question: " . $question . "\n\n";
        return $context;
    }

    private function tryAdvancedHuggingFaceAPI($question)
    {
        try {
            $config = config('ai-chat.hugging_face');
            $model = $config['model'];
            $apiUrl = $config['api_url'] . $model;
            
            // Enhanced prompt for better responses
            $enhancedPrompt = "You are an intelligent AI assistant for an e-learning platform. Provide detailed, helpful, and educational responses. If the question is about the platform, courses, or education, provide comprehensive information. If it's a general question, provide informative and well-structured answers. Always be helpful, friendly, and educational.\n\nUser Question: " . $question . "\n\nPlease provide a detailed and helpful response:";
            
            $response = Http::timeout($config['timeout'])->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'inputs' => $enhancedPrompt,
                'options' => [
                    'wait_for_model' => true
                ],
                'parameters' => [
                    'max_new_tokens' => 500,
                    'temperature' => 0.7,
                    'do_sample' => true
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data[0]['generated_text'])) {
                    $answer = $data[0]['generated_text'];
                    return $this->cleanAndEnhanceResponse($answer, $question);
                }
            }
        } catch (\Exception $e) {
            // Fall back to other services
        }

        return null;
    }

    private function tryAdvancedFreeAIService($question)
    {
        try {
            $config = config('ai-chat.free_ai_service');
            
            // Try multiple advanced free AI endpoints
            $endpoints = [
                [
                    'url' => 'https://api.free-ai-chat.com/chat',
                    'data' => [
                        'message' => $this->createAdvancedPrompt($question),
                        'model' => 'gpt-3.5-turbo',
                        'max_tokens' => 500,
                        'temperature' => 0.7
                    ]
                ],
                [
                    'url' => 'https://api.deepai.org/text-generator',
                    'data' => [
                        'text' => $this->createAdvancedPrompt($question),
                        'model' => 'text-generator',
                        'max_tokens' => 500
                    ]
                ],
                [
                    'url' => 'https://api.aiapi.io/chat',
                    'data' => [
                        'message' => $this->createAdvancedPrompt($question),
                        'model' => 'gpt-3.5-turbo',
                        'max_tokens' => 500
                    ]
                ]
            ];

            foreach ($endpoints as $endpoint) {
                try {
                    $response = Http::timeout($config['timeout'])->post($endpoint['url'], $endpoint['data']);

                    if ($response->successful()) {
                        $data = $response->json();
                        if (isset($data['response']) || isset($data['output']) || isset($data['text'])) {
                            $answer = $data['response'] ?? $data['output'] ?? $data['text'];
                            return $this->cleanAndEnhanceResponse($answer, $question);
                        }
                    }
                } catch (\Exception $e) {
                    continue; // Try next endpoint
                }
            }
        } catch (\Exception $e) {
            // Fall back to rule-based responses
        }

        return null;
    }

    private function createAdvancedPrompt($question)
    {
        $context = "You are an intelligent AI assistant for an e-learning platform. ";
        $context .= "Provide detailed, helpful, and educational responses. ";
        $context .= "If the question is about the platform, courses, or education, provide comprehensive information. ";
        $context .= "If it's a general question, provide informative and well-structured answers. ";
        $context .= "Always be helpful, friendly, and educational in your responses.\n\n";
        $context .= "User Question: " . $question . "\n\n";
        $context .= "Please provide a detailed and helpful response:";

        return $context;
    }

    private function cleanAndEnhanceResponse($response, $originalQuestion)
    {
        // Clean up the response
        $response = trim($response);
        
        // Remove any prompt artifacts
        $response = preg_replace('/^(User Question:|Please provide|Response:)/i', '', $response);
        $response = trim($response);
        
        // If response is too short, enhance it
        if (strlen($response) < 50) {
            $enhanced = $this->enhanceShortResponse($response, $originalQuestion);
            if ($enhanced) {
                return $enhanced;
            }
        }
        
        return $response ?: null;
    }

    private function enhanceShortResponse($shortResponse, $originalQuestion)
    {
        // Enhance short responses with more context
        $question = strtolower($originalQuestion);
        
        if (strpos($question, 'course') !== false) {
            return $shortResponse . "\n\nIn our e-learning platform, you can access courses through your dashboard. Each course contains materials, assignments, and communication tools. You can also interact with other students and lecturers through the messaging system.";
        }
        
        if (strpos($question, 'assignment') !== false) {
            return $shortResponse . "\n\nFor assignments in our platform, you can submit them through the assessment section of each course. Make sure to check the due dates and requirements before submitting. You can also contact your lecturer if you need clarification.";
        }
        
        if (strpos($question, 'study') !== false) {
            return $shortResponse . "\n\nEffective study strategies include creating a schedule, taking regular breaks, using active learning techniques, and reviewing materials consistently. Our platform also offers discussion forums where you can collaborate with other students.";
        }
        
        return $shortResponse;
    }

    private function tryAdvancedRuleBasedResponse($question)
    {
        $question = strtolower(trim($question));
        
        // Enhanced e-learning specific responses with more detail
        $responses = [
            'hello' => "Hello! I'm your intelligent AI learning assistant, designed to provide comprehensive support for your educational journey. I can help you with detailed explanations of course concepts, study strategies, technical support, and much more. How can I assist you today?",
            
            'hi' => "Hi there! I'm your AI learning companion, designed to help you succeed in your studies. I can answer questions about our e-learning platform, provide study guidance, help with technical issues, and offer educational insights. What would you like to know?",
            
            'help' => "I'm here to provide comprehensive assistance with your e-learning experience! Here's what I can help you with:\n\n" .
                     "📚 **Course Information**: Find details about your courses, materials, and schedules\n" .
                     "📝 **Assignment Support**: Get help with submissions, deadlines, and requirements\n" .
                     "🎯 **Study Guidance**: Receive personalized study tips and learning strategies\n" .
                     "🔧 **Technical Support**: Troubleshoot platform issues and navigation\n" .
                     "💬 **Communication**: Learn how to contact lecturers and fellow students\n" .
                     "📖 **Educational Content**: Get explanations on various academic topics\n\n" .
                     "What specific area would you like assistance with?",
            
            'course' => "Our e-learning platform offers a comprehensive course management system. Here's what you need to know:\n\n" .
                       "• **Access**: Courses are available in your main dashboard\n" .
                       "• **Content**: Each course includes materials, assignments, and communication tools\n" .
                       "• **Navigation**: Use the course menu to access different sections\n" .
                       "• **Interaction**: Participate in discussions and group activities\n" .
                       "• **Progress**: Track your learning progress and achievements\n\n" .
                       "What specific course information are you looking for?",
            
            'assignment' => "Assignment management is a key feature of our platform. Here's a comprehensive guide:\n\n" .
                           "📋 **Finding Assignments**: Check the assessment section of each course\n" .
                           "⏰ **Deadlines**: Due dates are clearly displayed with reminders\n" .
                           "📤 **Submission**: Upload files through the designated submission area\n" .
                           "📊 **Grading**: Receive feedback and grades through the platform\n" .
                           "🔄 **Resubmission**: Some assignments allow multiple attempts\n\n" .
                           "Do you need help with a specific assignment or submission process?",
            
            'study' => "Here are comprehensive study strategies to enhance your learning experience:\n\n" .
                      "📅 **Time Management**: Create a structured study schedule with regular breaks\n" .
                      "🎯 **Active Learning**: Engage with materials through note-taking and discussions\n" .
                      "🔄 **Regular Review**: Consistently review previous materials to reinforce learning\n" .
                      "👥 **Collaboration**: Use our discussion forums to learn from peers\n" .
                      "📚 **Resource Utilization**: Take advantage of all available course materials\n" .
                      "🧠 **Memory Techniques**: Use mnemonic devices and spaced repetition\n" .
                      "💡 **Question Practice**: Test your understanding through practice questions\n\n" .
                      "Would you like specific tips for any of these areas?",
            
            'password' => "If you've forgotten your password, here's how to reset it:\n\n" .
                         "1. Go to the login page\n" .
                         "2. Click the 'Forgot Password' link\n" .
                         "3. Enter your registered email address\n" .
                         "4. Check your email for reset instructions\n" .
                         "5. Follow the link to create a new password\n\n" .
                         "If you're still having issues, contact our support team for assistance.",
            
            'login' => "To access your e-learning account:\n\n" .
                      "1. Navigate to the login page\n" .
                      "2. Enter your registered email address\n" .
                      "3. Type your password\n" .
                      "4. Click 'Login' to access your dashboard\n\n" .
                      "**Troubleshooting**:\n" .
                      "• Ensure your email and password are correct\n" .
                      "• Check that Caps Lock is off\n" .
                      "• Try resetting your password if needed\n" .
                      "• Contact support for persistent issues",
            
            'register' => "Creating an account is simple and quick:\n\n" .
                         "1. Click the 'Register' link on the login page\n" .
                         "2. Fill in your personal information:\n" .
                         "   • Full name\n" .
                         "   • Email address\n" .
                         "   • Password (with confirmation)\n" .
                         "   • Role (student/lecturer)\n" .
                         "3. Accept the terms and conditions\n" .
                         "4. Click 'Register' to create your account\n" .
                         "5. Verify your email if required\n\n" .
                         "Once registered, you can access all platform features!",
            
            'contact' => "There are several ways to get in touch:\n\n" .
                        "👨‍🏫 **Lecturers**: Use the messaging system in your dashboard\n" .
                        "📧 **Email**: Contact lecturers directly via email\n" .
                        "💬 **Discussion Forums**: Engage with other students\n" .
                        "📝 **Feedback System**: Submit suggestions and report issues\n" .
                        "🆘 **Support**: Contact administrators for technical issues\n\n" .
                        "What type of assistance do you need?",
            
            'feedback' => "Your feedback is valuable for improving our platform:\n\n" .
                         "📊 **Course Feedback**: Share your thoughts on course content and delivery\n" .
                         "🔧 **Technical Feedback**: Report bugs or suggest improvements\n" .
                         "💡 **Feature Requests**: Suggest new features or enhancements\n" .
                         "📈 **Performance Feedback**: Help us understand your learning experience\n\n" .
                         "Use the feedback section in your dashboard to submit your comments.",
            
            'technical' => "For technical issues, try these troubleshooting steps:\n\n" .
                          "🔄 **Refresh**: Reload the page to clear temporary issues\n" .
                          "🧹 **Clear Cache**: Clear your browser cache and cookies\n" .
                          "🌐 **Browser**: Try using a different web browser\n" .
                          "📱 **Device**: Test on a different device if possible\n" .
                          "📶 **Connection**: Check your internet connection\n" .
                          "⏰ **Timing**: Some features may have peak usage times\n\n" .
                          "If issues persist, contact our technical support team.",
            
            'download' => "Downloading course materials is straightforward:\n\n" .
                         "📁 **Location**: Materials are in the course content section\n" .
                         "⬇️ **Download**: Click the download button next to each material\n" .
                         "📱 **Compatibility**: Files work on all devices and operating systems\n" .
                         "💾 **Storage**: Save files to your preferred location\n" .
                         "📖 **Formats**: Materials are available in various formats (PDF, DOC, etc.)\n\n" .
                         "Is there a specific material you're trying to download?",
            
            'upload' => "Submitting assignments through our platform:\n\n" .
                       "📝 **Preparation**: Ensure your file meets the requirements\n" .
                       "📁 **Format**: Use accepted file formats (PDF, DOC, etc.)\n" .
                       "📤 **Upload**: Go to the assessment section and click 'Submit'\n" .
                       "✅ **Confirmation**: You'll receive a submission confirmation\n" .
                       "📊 **Tracking**: Monitor your submission status\n" .
                       "🔄 **Resubmission**: Some assignments allow multiple attempts\n\n" .
                       "Need help with a specific submission?",
            
            'grade' => "Understanding your grades and feedback:\n\n" .
                      "📊 **Access**: Grades are available in the assessment section\n" .
                      "⏰ **Timeline**: Grades are typically posted within 1-2 weeks\n" .
                      "📝 **Feedback**: Detailed feedback accompanies most grades\n" .
                      "📈 **Progress**: Track your overall course performance\n" .
                      "🎯 **Improvement**: Use feedback to enhance future submissions\n\n" .
                      "Contact your lecturer if you have questions about specific grades.",
            
            'deadline' => "Managing assignment deadlines effectively:\n\n" .
                         "📅 **Visibility**: Deadlines are clearly displayed in course dashboards\n" .
                         "⏰ **Reminders**: Set up notifications for upcoming deadlines\n" .
                         "📝 **Planning**: Start assignments early to avoid last-minute stress\n" .
                         "🔄 **Extensions**: Contact lecturers for deadline extensions if needed\n" .
                         "📊 **Tracking**: Monitor your progress toward deadlines\n\n" .
                         "Need help planning your assignment schedule?",
            
            'extension' => "Requesting deadline extensions:\n\n" .
                          "📧 **Contact**: Reach out to your lecturer through the messaging system\n" .
                          "📝 **Request**: Provide a clear reason for the extension\n" .
                          "⏰ **Timing**: Request extensions well before the deadline\n" .
                          "📋 **Documentation**: Include any relevant documentation if applicable\n" .
                          "✅ **Approval**: Wait for lecturer approval before assuming extension\n\n" .
                          "Remember that extensions are granted at the lecturer's discretion.",
            
            'group' => "Group work and collaboration features:\n\n" .
                      "👥 **Teams**: Form groups for collaborative projects\n" .
                      "💬 **Communication**: Use group chat features for coordination\n" .
                      "📁 **Sharing**: Share documents and resources within groups\n" .
                      "📊 **Progress**: Track group project progress\n" .
                      "🎯 **Roles**: Assign specific roles and responsibilities\n" .
                      "📝 **Submission**: Submit group assignments collectively\n\n" .
                      "Need help setting up or managing a group project?",
            
            'forum' => "Participating in course discussions:\n\n" .
                      "💬 **Access**: Forums are available in each course section\n" .
                      "📝 **Posting**: Create new discussion threads or reply to existing ones\n" .
                      "🤝 **Engagement**: Respond to other students' posts\n" .
                      "📚 **Learning**: Use forums to deepen your understanding\n" .
                      "👨‍🏫 **Guidance**: Lecturers often participate in discussions\n" .
                      "📊 **Participation**: Active forum participation may contribute to grades\n\n" .
                      "Ready to start a discussion or join an existing one?",
            
            'library' => "Accessing course materials and resources:\n\n" .
                        "📚 **Course Materials**: Primary resources in each course\n" .
                        "📖 **Additional Resources**: Supplementary materials provided by lecturers\n" .
                        "🔍 **Search**: Use the search function to find specific content\n" .
                        "📱 **Mobile Access**: Materials are accessible on all devices\n" .
                        "💾 **Offline**: Download materials for offline study\n" .
                        "🔄 **Updates**: Materials are regularly updated by lecturers\n\n" .
                        "Looking for specific resources or materials?",
            
            'tutorial' => "Learning to use our platform effectively:\n\n" .
                         "📖 **Guides**: Comprehensive tutorials available in the help section\n" .
                         "🎥 **Videos**: Step-by-step video guides for key features\n" .
                         "📝 **FAQs**: Frequently asked questions with detailed answers\n" .
                         "🆘 **Support**: Contact support for personalized assistance\n" .
                         "💡 **Tips**: Regular tips and best practices shared\n" .
                         "🔄 **Updates**: Tutorials updated with new features\n\n" .
                         "What specific feature would you like to learn about?",
        ];

        // Check for exact matches first
        foreach ($responses as $keyword => $response) {
            if ($question === $keyword) {
                return $response;
            }
        }

        // Check for partial matches with enhanced context
        foreach ($responses as $keyword => $response) {
            if (strpos($question, $keyword) !== false) {
                return $response;
            }
        }

        // Enhanced pattern matching for more intelligent responses
        if (strpos($question, 'how') !== false) {
            if (strpos($question, 'login') !== false || strpos($question, 'sign in') !== false) {
                return "Here's a detailed guide on how to login to your e-learning account:\n\n" .
                       "1. **Navigate to Login Page**: Go to the main login page of our platform\n" .
                       "2. **Enter Credentials**: Input your registered email address and password\n" .
                       "3. **Security Check**: Complete any security verification if prompted\n" .
                       "4. **Access Dashboard**: Click 'Login' to access your personalized dashboard\n\n" .
                       "**Troubleshooting Tips**:\n" .
                       "• Double-check your email and password spelling\n" .
                       "• Ensure Caps Lock is turned off\n" .
                       "• Try the 'Forgot Password' option if needed\n" .
                       "• Contact support for persistent login issues\n\n" .
                       "Once logged in, you'll have access to all your courses and features!";
            }
            if (strpos($question, 'register') !== false || strpos($question, 'sign up') !== false) {
                return "Creating your e-learning account is simple and secure:\n\n" .
                       "1. **Access Registration**: Click the 'Register' link on the login page\n" .
                       "2. **Personal Information**: Fill in your details:\n" .
                       "   • Full legal name\n" .
                       "   • Valid email address\n" .
                       "   • Strong password (with confirmation)\n" .
                       "   • Select your role (student/lecturer)\n" .
                       "3. **Terms Agreement**: Read and accept the terms of service\n" .
                       "4. **Account Creation**: Click 'Register' to create your account\n" .
                       "5. **Email Verification**: Verify your email address if required\n\n" .
                       "**Security Tips**:\n" .
                       "• Use a strong, unique password\n" .
                       "• Provide accurate information\n" .
                       "• Keep your login credentials secure\n\n" .
                       "Welcome to our e-learning community!";
            }
            if (strpos($question, 'contact') !== false) {
                return "There are multiple ways to get in touch with our team:\n\n" .
                       "**For Course-Related Questions**:\n" .
                       "• Use the messaging system to contact your lecturer directly\n" .
                       "• Participate in course discussion forums\n" .
                       "• Send emails to your course instructors\n\n" .
                       "**For Technical Support**:\n" .
                       "• Use the feedback form in your dashboard\n" .
                       "• Contact our technical support team\n" .
                       "• Check our FAQ section for common issues\n\n" .
                       "**For General Inquiries**:\n" .
                       "• Reach out to administrators through the contact form\n" .
                       "• Use the help section for guidance\n\n" .
                       "We're here to help you succeed in your learning journey!";
            }
            if (strpos($question, 'download') !== false) {
                return "Downloading course materials is easy and convenient:\n\n" .
                       "1. **Navigate to Course**: Go to your specific course page\n" .
                       "2. **Find Materials**: Locate the materials section\n" .
                       "3. **Select File**: Click on the material you want to download\n" .
                       "4. **Download**: Click the download button (usually a downward arrow)\n" .
                       "5. **Save**: Choose your preferred download location\n\n" .
                       "**File Information**:\n" .
                       "• Materials are available in various formats (PDF, DOC, PPT, etc.)\n" .
                       "• Files are optimized for all devices and operating systems\n" .
                       "• You can download materials multiple times\n" .
                       "• Some materials may require course enrollment\n\n" .
                       "Need help finding specific materials?";
            }
            if (strpos($question, 'submit') !== false || strpos($question, 'upload') !== false) {
                return "Submitting assignments through our platform is straightforward:\n\n" .
                       "**Before Submission**:\n" .
                       "• Review assignment requirements carefully\n" .
                       "• Ensure your file meets format specifications\n" .
                       "• Check that your work is complete and ready\n\n" .
                       "**Submission Process**:\n" .
                       "1. Go to the assessment section of your course\n" .
                       "2. Find the specific assignment\n" .
                       "3. Click 'Submit Assignment' or 'Upload'\n" .
                       "4. Select your file from your device\n" .
                       "5. Add any required comments or notes\n" .
                       "6. Click 'Submit' to complete the process\n\n" .
                       "**After Submission**:\n" .
                       "• You'll receive a confirmation message\n" .
                       "• Track your submission status\n" .
                       "• Some assignments allow resubmission\n\n" .
                       "Need help with a specific submission?";
            }
        }

        if (strpos($question, 'what') !== false) {
            if (strpos($question, 'course') !== false) {
                return "Our e-learning platform offers comprehensive course management:\n\n" .
                       "**Course Features**:\n" .
                       "• **Content**: Rich multimedia materials and resources\n" .
                       "• **Assignments**: Structured assessments and projects\n" .
                       "• **Communication**: Built-in messaging and discussion tools\n" .
                       "• **Progress Tracking**: Monitor your learning journey\n" .
                       "• **Collaboration**: Group work and peer interaction features\n\n" .
                       "**Course Structure**:\n" .
                       "• Materials are organized in logical sections\n" .
                       "• Assignments have clear deadlines and requirements\n" .
                       "• Feedback is provided to enhance learning\n" .
                       "• Support is available throughout your course\n\n" .
                       "Each course is designed to provide an engaging and effective learning experience!";
            }
            if (strpos($question, 'assignment') !== false) {
                return "Assignments are a key component of your learning experience:\n\n" .
                       "**Types of Assignments**:\n" .
                       "• **Individual Projects**: Personal work and research\n" .
                       "• **Group Work**: Collaborative projects and presentations\n" .
                       "• **Quizzes**: Knowledge assessment and testing\n" .
                       "• **Essays**: Written analysis and critical thinking\n" .
                       "• **Presentations**: Oral communication and demonstration\n\n" .
                       "**Assignment Features**:\n" .
                       "• Clear instructions and requirements\n" .
                       "• Specific deadlines and submission guidelines\n" .
                       "• Grading criteria and rubrics\n" .
                       "• Feedback and improvement opportunities\n" .
                       "• Support resources and guidance\n\n" .
                       "Assignments help you apply and demonstrate your learning!";
            }
            if (strpos($question, 'grade') !== false) {
                return "Understanding your academic performance:\n\n" .
                       "**Grading System**:\n" .
                       "• **Assessment Types**: Various assignment types contribute to your grade\n" .
                       "• **Weighting**: Different assignments may have different weights\n" .
                       "• **Feedback**: Detailed feedback accompanies most grades\n" .
                       "• **Timeline**: Grades are typically posted within 1-2 weeks\n\n" .
                       "**Grade Access**:\n" .
                       "• View grades in the assessment section\n" .
                       "• Track your progress over time\n" .
                       "• Compare performance across assignments\n" .
                       "• Identify areas for improvement\n\n" .
                       "**Grade Interpretation**:\n" .
                       "• Use feedback to understand your performance\n" .
                       "• Identify strengths and areas for growth\n" .
                       "• Plan strategies for improvement\n" .
                       "• Discuss concerns with your lecturer\n\n" .
                       "Grades are tools for learning and improvement!";
            }
        }

        if (strpos($question, 'where') !== false) {
            if (strpos($question, 'course') !== false) {
                return "Finding your courses is easy:\n\n" .
                       "**Main Dashboard**:\n" .
                       "• All your enrolled courses are displayed on the main dashboard\n" .
                       "• Courses are organized by semester or category\n" .
                       "• Click on any course to access its content\n\n" .
                       "**Navigation Menu**:\n" .
                       "• Use the course menu in the sidebar\n" .
                       "• Browse all available courses\n" .
                       "• Filter courses by category or status\n\n" .
                       "**Course Access**:\n" .
                       "• Enrolled courses are immediately accessible\n" .
                       "• Some courses may require prerequisites\n" .
                       "• Contact your lecturer for enrollment issues\n\n" .
                       "Your learning journey starts with easy course access!";
            }
            if (strpos($question, 'material') !== false) {
                return "Course materials are organized for easy access:\n\n" .
                       "**Material Locations**:\n" .
                       "• **Course Pages**: Materials are in dedicated course sections\n" .
                       "• **Content Tabs**: Organized by topic or week\n" .
                       "• **Resource Library**: Additional materials and references\n" .
                       "• **Downloads**: Accessible from multiple locations\n\n" .
                       "**Material Types**:\n" .
                       "• **Readings**: Text documents and articles\n" .
                       "• **Videos**: Multimedia content and lectures\n" .
                       "• **Presentations**: Slides and visual materials\n" .
                       "• **Resources**: Links and external references\n\n" .
                       "**Organization**:\n" .
                       "• Materials are logically sequenced\n" .
                       "• Search functions help find specific content\n" .
                       "• Materials are regularly updated\n\n" .
                       "Everything you need is just a click away!";
            }
            if (strpos($question, 'assignment') !== false) {
                return "Assignments are strategically placed for easy access:\n\n" .
                       "**Primary Location**:\n" .
                       "• **Assessment Section**: Main assignment hub in each course\n" .
                       "• **Clear Organization**: Assignments listed by due date or topic\n" .
                       "• **Status Tracking**: See which assignments are pending or completed\n\n" .
                       "**Additional Access Points**:\n" .
                       "• **Course Dashboard**: Overview of all course activities\n" .
                       "• **Calendar View**: Timeline of upcoming assignments\n" .
                       "• **Notifications**: Reminders for approaching deadlines\n\n" .
                       "**Assignment Details**:\n" .
                       "• Click on any assignment for full details\n" .
                       "• View requirements, deadlines, and submission instructions\n" .
                       "• Access related materials and resources\n\n" .
                       "Never miss an assignment with our organized system!";
            }
        }

        // Enhanced default response for unrecognized questions
        return "I'm your intelligent AI learning assistant, designed to provide comprehensive support for your educational journey! Here's how I can help you:\n\n" .
               "🎓 **Academic Support**:\n" .
               "• Detailed explanations of course concepts\n" .
               "• Study strategies and learning techniques\n" .
               "• Assignment guidance and feedback\n" .
               "• Research and writing assistance\n\n" .
               "💻 **Platform Navigation**:\n" .
               "• Course access and material organization\n" .
               "• Assignment submission and tracking\n" .
               "• Communication tools and features\n" .
               "• Technical troubleshooting\n\n" .
               "📚 **Educational Resources**:\n" .
               "• Learning materials and references\n" .
               "• Study guides and tutorials\n" .
               "• Best practices and tips\n" .
               "• Academic writing support\n\n" .
               "🔧 **Technical Assistance**:\n" .
               "• Platform usage and features\n" .
               "• File management and downloads\n" .
               "• Communication tools\n" .
               "• Troubleshooting common issues\n\n" .
               "Feel free to ask me anything about your studies, the platform, or any educational topic. I'm here to provide detailed, helpful responses to support your learning success!";
    }
}