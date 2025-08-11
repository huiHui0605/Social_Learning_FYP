<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feedback;
use App\Models\User;
use App\Models\Course;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users and courses for seeding
        $students = User::where('role', 'student')->take(3)->get();
        $lecturers = User::where('role', 'lecturer')->take(2)->get();
        $admins = User::where('role', 'admin')->take(1)->get();
        $courses = Course::take(2)->get();

        if ($students->isEmpty() || $lecturers->isEmpty() || $admins->isEmpty() || $courses->isEmpty()) {
            return; // Skip seeding if no users/courses exist
        }

        // Student to Lecturer Feedback
        $studentFeedbackData = [
            [
                'title' => 'Course Material Request',
                'content' => 'I would like to request additional study materials for the database concepts. The current materials are helpful but I need more examples to better understand the relationships.',
                'category' => 'course',
                'priority' => 'medium',
                'status' => 'pending',
            ],
            [
                'title' => 'Technical Issue with Upload',
                'content' => 'I am having trouble uploading my assignment file. The system keeps showing an error message. Could you please help me resolve this issue?',
                'category' => 'technical',
                'priority' => 'high',
                'status' => 'in_progress',
            ],
            [
                'title' => 'Suggestion for Course Improvement',
                'content' => 'I think it would be great if we could have more interactive sessions during the lectures. The current format is good but adding some hands-on exercises would make it even better.',
                'category' => 'suggestion',
                'priority' => 'low',
                'status' => 'resolved',
            ],
        ];

        foreach ($studentFeedbackData as $index => $data) {
            $student = $students[$index % $students->count()];
            $lecturer = $lecturers[$index % $lecturers->count()];
            $course = $courses[$index % $courses->count()];

            $feedback = Feedback::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'type' => 'student_to_lecturer',
                'category' => $data['category'],
                'priority' => $data['priority'],
                'status' => $data['status'],
                'student_id' => $student->id,
                'lecturer_id' => $lecturer->id,
                'course_id' => $course->id,
            ]);

            // Add response for resolved feedback
            if ($data['status'] === 'resolved') {
                $feedback->update([
                    'response' => 'Thank you for your suggestion! I will definitely consider adding more interactive elements to the course. I appreciate your feedback and will work on incorporating hands-on exercises in future sessions.',
                    'responded_at' => now(),
                    'responded_by' => $lecturer->id,
                ]);
            }
        }

        // Lecturer to Admin Feedback
        $lecturerFeedbackData = [
            [
                'title' => 'System Performance Issue',
                'content' => 'The course management system has been running slowly during peak hours. This affects my ability to upload materials and respond to student queries efficiently.',
                'category' => 'technical',
                'priority' => 'high',
                'status' => 'pending',
            ],
            [
                'title' => 'Request for Additional Features',
                'content' => 'I would like to suggest adding a feature for bulk email notifications to students. This would help me communicate important updates more effectively.',
                'category' => 'suggestion',
                'priority' => 'medium',
                'status' => 'in_progress',
            ],
        ];

        foreach ($lecturerFeedbackData as $index => $data) {
            $lecturer = $lecturers[$index % $lecturers->count()];
            $admin = $admins->first();

            $feedback = Feedback::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'type' => 'lecturer_to_admin',
                'category' => $data['category'],
                'priority' => $data['priority'],
                'status' => $data['status'],
                'lecturer_id' => $lecturer->id,
                'admin_id' => $admin->id,
            ]);

            // Add response for in_progress feedback
            if ($data['status'] === 'in_progress') {
                $feedback->update([
                    'response' => 'Thank you for your suggestion about bulk email notifications. We are currently evaluating this feature and will implement it in the next system update. I will keep you updated on the progress.',
                    'responded_at' => now(),
                    'responded_by' => $admin->id,
                ]);
            }
        }
    }
}
