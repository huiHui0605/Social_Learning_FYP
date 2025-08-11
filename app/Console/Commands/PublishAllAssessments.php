<?php

namespace App\Console\Commands;

use App\Models\Assessment;
use Illuminate\Console\Command;

class PublishAllAssessments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assessments:publish-all {--course-id= : Publish assessments for specific course ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all draft assessments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Assessment::where('status', 'draft');
        
        if ($this->option('course-id')) {
            $query->where('course_id', $this->option('course-id'));
        }
        
        $draftAssessments = $query->with('course')->get();
        
        if ($draftAssessments->isEmpty()) {
            $this->info('No draft assessments found.');
            return;
        }
        
        $this->info("Found {$draftAssessments->count()} draft assessment(s):");
        
        foreach ($draftAssessments as $assessment) {
            $this->line("- {$assessment->title} (Course: {$assessment->course->title})");
        }
        
        if ($this->confirm('Do you want to publish all these assessments?')) {
            $count = 0;
            foreach ($draftAssessments as $assessment) {
                $assessment->update(['status' => 'published']);
                $count++;
                $this->info("Published: {$assessment->title}");
            }
            
            $this->info("Successfully published {$count} assessment(s)!");
        } else {
            $this->info('Operation cancelled.');
        }
    }
} 