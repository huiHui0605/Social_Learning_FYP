<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;

class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a lecturer and some courses
        $lecturer = User::where('role', 'lecturer')->first();
        $courses = Course::where('lecturer_id', $lecturer->id)->get();
        $students = User::where('role', 'student')->get();

        if ($courses->isEmpty() || $students->isEmpty()) {
            return;
        }

        // Create sample assessments
        $assessments = [
            [
                'title' => 'Midterm Assignment',
                'description' => 'Complete a comprehensive analysis of the course materials covered in the first half of the semester. Your submission should include:\n\n1. A detailed summary of key concepts\n2. Critical analysis of the main topics\n3. Personal reflections on the learning process\n4. References to relevant course materials\n\nPlease ensure your work is well-structured and properly formatted.',
                'course_id' => $courses->first()->id,
                'due_date' => Carbon::now()->addDays(7),
                'total_marks' => 50,
                'status' => 'published',
            ],
            [
                'title' => 'Final Project',
                'description' => 'Create a final project that demonstrates your understanding of the course concepts. The project should be innovative and showcase your skills in applying the theoretical knowledge to practical scenarios.\n\nRequirements:\n- Minimum 2000 words\n- Include diagrams or visual elements\n- Proper citations and references\n- Original work with no plagiarism',
                'course_id' => $courses->first()->id,
                'due_date' => Carbon::now()->addDays(14),
                'total_marks' => 100,
                'status' => 'published',
            ],
            [
                'title' => 'Weekly Quiz',
                'description' => 'A short quiz to test your understanding of this week\'s materials. The quiz consists of multiple-choice questions and short answer responses.',
                'course_id' => $courses->last()->id,
                'due_date' => Carbon::now()->addDays(3),
                'total_marks' => 20,
                'status' => 'draft',
            ],
        ];

        foreach ($assessments as $assessmentData) {
            $assessment = Assessment::create([
                'title' => $assessmentData['title'],
                'description' => $assessmentData['description'],
                'course_id' => $assessmentData['course_id'],
                'lecturer_id' => $lecturer->id,
                'due_date' => $assessmentData['due_date'],
                'total_marks' => $assessmentData['total_marks'],
                'status' => $assessmentData['status'],
            ]);

            // Create sample submissions for published assessments
            if ($assessment->status === 'published') {
                foreach ($students->take(3) as $student) {
                    $isLate = rand(0, 1);
                    $submittedAt = $isLate 
                        ? Carbon::parse($assessment->due_date)->addDays(rand(1, 3))
                        : Carbon::parse($assessment->due_date)->subDays(rand(0, 2));

                    $submission = AssessmentSubmission::create([
                        'assessment_id' => $assessment->id,
                        'student_id' => $student->id,
                        'submission_content' => "This is a sample submission from {$student->name} for the {$assessment->title}. 

The student has provided a comprehensive response to the assessment requirements, demonstrating understanding of the course materials and concepts covered throughout the semester.

Key points addressed:
1. Analysis of course concepts
2. Application of theoretical knowledge
3. Critical thinking and problem-solving
4. Proper formatting and structure

The student believes this submission meets all the requirements outlined in the assessment description and demonstrates their understanding of the subject matter.",
                        'status' => $isLate ? 'late' : 'submitted',
                        'submitted_at' => $submittedAt,
                    ]);

                    // Grade some submissions
                    if (rand(0, 1)) {
                        $marks = rand(60, $assessment->total_marks);
                        $submission->update([
                            'marks_obtained' => $marks,
                            'feedback' => "Good work! You demonstrated solid understanding of the concepts. Areas for improvement: consider providing more detailed analysis in future submissions.",
                            'status' => 'graded',
                            'graded_at' => Carbon::now(),
                        ]);
                    }
                }
            }
        }
    }
}
