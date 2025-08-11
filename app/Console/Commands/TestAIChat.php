<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AIChatController;
use Illuminate\Http\Request;

class TestAIChat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:test {question?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the AI chatbot with Google AI API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $question = $this->argument('question') ?? 'Hello! Can you help me with my studies?';
        
        $this->info("Testing AI Chatbot with Google AI API...");
        $this->info("Question: {$question}");
        $this->info("=====================================");
        
        try {
            $controller = new AIChatController();
            $request = new Request(['question' => $question]);
            
            $response = $controller->ask($request);
            $data = json_decode($response->getContent(), true);
            
            if (isset($data['answer'])) {
                $this->info("✅ AI Response:");
                $this->line($data['answer']);
                $this->info("✅ Test completed successfully!");
            } else {
                $this->error("❌ No answer received from AI service");
            }
        } catch (\Exception $e) {
            $this->error("❌ Error testing AI chatbot: " . $e->getMessage());
        }
    }
} 