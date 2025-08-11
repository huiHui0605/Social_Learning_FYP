# Environment Configuration for AI Chatbot

## Google AI API Setup

Your AI chatbot is now configured to use Google's Gemini AI API for enhanced responses. Here's how to set it up:

### 1. Environment Variables

Add these variables to your `.env` file:

```env
# Google AI Configuration
GOOGLE_AI_API_KEY=AIzaSyDx0Fq1mJtsMaIKoVz3RnQ-pBBBuxvFMMA
GOOGLE_AI_MODEL=gemini-pro

# AI Chat Services Configuration
AI_CHAT_GOOGLE_AI=true
AI_CHAT_HUGGING_FACE=true
AI_CHAT_FREE_SERVICE=true
AI_CHAT_RULE_BASED=true
```

### 2. API Key Security

**Important**: Keep your API key secure:
- Never commit the `.env` file to version control
- Use environment variables in production
- Monitor API usage to avoid unexpected costs

### 3. Service Priority

The chatbot will try services in this order:
1. **Google AI (Gemini)** - Primary service with your API key
2. **Hugging Face** - Free AI models (fallback)
3. **Free AI Services** - Alternative free endpoints
4. **Rule-based** - Always available as final fallback

### 4. Testing the Configuration

After adding the environment variables:

1. Clear Laravel cache:
```bash
php artisan config:clear
php artisan cache:clear
```

2. Test the chatbot:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

3. Open the chatbot and ask questions to test the Google AI integration.

### 5. Monitoring and Logs

Check Laravel logs for API responses:
```bash
tail -f storage/logs/laravel.log
```

### 6. Customization Options

You can customize the AI behavior in `config/ai-chat.php`:

```php
'google_ai' => [
    'api_key' => env('GOOGLE_AI_API_KEY', 'your-api-key'),
    'model' => env('GOOGLE_AI_MODEL', 'gemini-pro'),
    'max_tokens' => 1000,
    'temperature' => 0.7,
],
```

### 7. Troubleshooting

If the Google AI service isn't working:

1. **Check API Key**: Verify the key is correct and active
2. **Check Logs**: Look for error messages in Laravel logs
3. **Test API**: Try a simple curl request to test the API
4. **Fallback**: The system will automatically use other services

### 8. API Usage Monitoring

Monitor your Google AI API usage:
- Check Google Cloud Console
- Set up billing alerts
- Monitor request counts and costs

## Benefits of Google AI Integration

✅ **Enhanced Responses** - More accurate and detailed answers  
✅ **Educational Focus** - Better understanding of academic content  
✅ **Context Awareness** - Improved conversation flow  
✅ **Professional Quality** - ChatGPT-like response quality  
✅ **Reliable Fallbacks** - Multiple backup services ensure availability  

## Ready to Use!

Your AI chatbot is now powered by Google's Gemini AI and will provide:
- More accurate and detailed responses
- Better understanding of educational content
- Professional-quality interactions
- Reliable service with multiple fallbacks

Start chatting to experience the enhanced AI capabilities! 🚀 