<?php

// Simple test script to verify Google AI API integration
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Test Google AI API directly
function testGoogleAI() {
    $apiKey = 'AIzaSyDx0Fq1mJtsMaIKoVz3RnQ-pBBBuxvFMMA';
    $model = 'gemini-pro';
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
    
    $testQuestion = "Hello! Can you help me with my studies?";
    
    $prompt = "You are an intelligent AI assistant for an e-learning platform. Provide a brief, helpful response to: " . $testQuestion;
    
    try {
        $response = Http::timeout(30)->withHeaders([
            'Content-Type' => 'application/json',
        ])->post($apiUrl, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => 200,
                'temperature' => 0.7,
                'topP' => 0.8,
                'topK' => 40
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $answer = $data['candidates'][0]['content']['parts'][0]['text'];
                echo "✅ Google AI API Test: SUCCESS\n";
                echo "Question: {$testQuestion}\n";
                echo "Response: {$answer}\n";
                return true;
            } else {
                echo "❌ Google AI API Test: No response text found\n";
                echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
                return false;
            }
        } else {
            echo "❌ Google AI API Test: HTTP Error\n";
            echo "Status: " . $response->status() . "\n";
            echo "Response: " . $response->body() . "\n";
            return false;
        }
    } catch (\Exception $e) {
        echo "❌ Google AI API Test: Exception\n";
        echo "Error: " . $e->getMessage() . "\n";
        return false;
    }
}

// Run the test
echo "Testing Google AI API Integration...\n";
echo "=====================================\n";
testGoogleAI();
echo "=====================================\n";
echo "Test completed. Check the results above.\n"; 