<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class CourseAndSubjectSeeder extends Seeder
{
    // Copied verbatim from the frontend's hardcoded <select> option lists
    // (backend_details.md §9) so lookups aren't invented out of thin air:
    // frontend/07_gt-students.html, 08_gt-assignments.html,
    // 12_ga-students.html, 13_ga-coding-practice.html, 14_ga-final-exam.html.
    public function run(): void
    {
        $gtCourses = [
            'Web Development',
            'Python Programming',
            'Java Programming',
            'Full Stack Development',
            'Data Science',
        ];

        $gaCourses = [
            'Web Development',
            'Python Programming',
            'Java Programming',
            'Data Science',
            'Full Stack Development',
        ];

        $gtSubjects = [
            'Web Development',
            'Java',
            'Python',
            'Database Management',
            'Data Structures',
        ];

        $gaCodingTopics = [
            'HTML',
            'CSS',
            'JavaScript',
            'Python',
            'Java',
        ];

        $gaExamSubjects = [
            'HTML & CSS',
            'JavaScript',
            'Python',
            'Java',
            'Web Development',
        ];

        foreach ($gtCourses as $name) {
            Course::firstOrCreate(['org_code' => 'gt', 'name' => $name]);
        }

        foreach ($gaCourses as $name) {
            Course::firstOrCreate(['org_code' => 'ga', 'name' => $name]);
        }

        foreach ($gtSubjects as $name) {
            Subject::firstOrCreate(['org_code' => 'gt', 'kind' => 'gt_subject', 'name' => $name]);
        }

        foreach ($gaCodingTopics as $name) {
            Subject::firstOrCreate(['org_code' => 'ga', 'kind' => 'ga_coding_topic', 'name' => $name]);
        }

        foreach ($gaExamSubjects as $name) {
            Subject::firstOrCreate(['org_code' => 'ga', 'kind' => 'ga_exam_subject', 'name' => $name]);
        }
    }
}
