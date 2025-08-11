<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;

class ShowCourseImages extends Command
{
    protected $signature = 'debug:course-images';
    protected $description = 'Show all courses with their image_path';

    public function handle()
    {
        $courses = Course::all();
        foreach ($courses as $c) {
            $this->line('ID: ' . $c->id . ' | Title: ' . $c->title . ' | Image Path: ' . $c->image_path);
        }
        return 0;
    }
}