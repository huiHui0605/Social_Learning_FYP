<?php

namespace App\Console\Commands;

use App\Models\Feedback;
use Illuminate\Console\Command;

class EscalateOverdueFeedback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedback:escalate-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Escalate priority of overdue feedback';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $overdueFeedback = Feedback::where('status', 'pending')
            ->where('created_at', '<', now()->subDays(7))
            ->where('priority', '!=', 'urgent')
            ->get();

        $count = 0;
        foreach ($overdueFeedback as $feedback) {
            $feedback->escalateIfOverdue();
            $count++;
        }

        $this->info("Escalated {$count} overdue feedback items.");
    }
} 