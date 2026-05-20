<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Goal;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@goalflow.app'],
            [
                'name'        => 'Alex Rivera',
                'password'    => Hash::make('password'),
                'bio'         => 'Building the future, one goal at a time.',
                'theme'       => 'dark',
                'streak_days' => 12,
                'last_active_at' => now(),
            ]
        );

        $categories = [
            ['name' => 'Health & Fitness',  'color' => '#10b981', 'icon' => '💪'],
            ['name' => 'Career & Work',     'color' => '#6366f1', 'icon' => '🚀'],
            ['name' => 'Finance',           'color' => '#f59e0b', 'icon' => '💰'],
            ['name' => 'Personal Growth',   'color' => '#8b5cf6', 'icon' => '🧠'],
            ['name' => 'Learning',          'color' => '#06b6d4', 'icon' => '📚'],
            ['name' => 'Relationships',     'color' => '#ec4899', 'icon' => '❤️'],
        ];

        $createdCategories = [];
        foreach ($categories as $cat) {
            $createdCategories[] = $user->categories()->firstOrCreate(['name' => $cat['name']], $cat);
        }

        $goalsData = [
            [
                'title'       => 'Run a Half Marathon',
                'description' => 'Train consistently and complete a 21km half marathon race by end of year.',
                'priority'    => 'high',
                'status'      => 'active',
                'progress'    => 65,
                'deadline'    => now()->addMonths(3)->format('Y-m-d'),
                'category'    => 0,
                'tasks' => [
                    ['title' => 'Run 5km three times a week', 'is_completed' => true],
                    ['title' => 'Complete 10km training run', 'is_completed' => true],
                    ['title' => 'Buy proper running shoes', 'is_completed' => true],
                    ['title' => 'Complete 15km training run', 'is_completed' => false],
                    ['title' => 'Register for the race', 'is_completed' => false],
                    ['title' => 'Complete 21km race', 'is_completed' => false],
                ],
                'milestones' => [
                    ['title' => 'First 5km under 30 min',    'is_completed' => true,  'target_date' => now()->subMonths(2)->format('Y-m-d')],
                    ['title' => 'Complete 10km run',          'is_completed' => true,  'target_date' => now()->subMonth()->format('Y-m-d')],
                    ['title' => 'Complete 15km training run', 'is_completed' => false, 'target_date' => now()->addMonth()->format('Y-m-d')],
                    ['title' => 'Race day completion',        'is_completed' => false, 'target_date' => now()->addMonths(3)->format('Y-m-d')],
                ],
            ],
            [
                'title'       => 'Launch SaaS Product',
                'description' => 'Build and launch a profitable SaaS product with 100 paying customers.',
                'priority'    => 'critical',
                'status'      => 'active',
                'progress'    => 40,
                'deadline'    => now()->addMonths(6)->format('Y-m-d'),
                'category'    => 1,
                'tasks' => [
                    ['title' => 'Define product vision and MVP scope', 'is_completed' => true],
                    ['title' => 'Build MVP backend API', 'is_completed' => true],
                    ['title' => 'Design UI/UX mockups', 'is_completed' => false],
                    ['title' => 'Build frontend application', 'is_completed' => false],
                    ['title' => 'Set up payment processing', 'is_completed' => false],
                    ['title' => 'Beta launch to 50 users', 'is_completed' => false],
                    ['title' => 'Reach 100 paying customers', 'is_completed' => false],
                ],
                'milestones' => [
                    ['title' => 'MVP completed',          'is_completed' => true,  'target_date' => now()->subWeeks(2)->format('Y-m-d')],
                    ['title' => 'First paying customer',  'is_completed' => false, 'target_date' => now()->addMonths(2)->format('Y-m-d')],
                    ['title' => '100 customers reached',  'is_completed' => false, 'target_date' => now()->addMonths(6)->format('Y-m-d')],
                ],
            ],
            [
                'title'       => 'Save $20,000 Emergency Fund',
                'description' => 'Build a 6-month emergency fund for financial security and peace of mind.',
                'priority'    => 'high',
                'status'      => 'active',
                'progress'    => 55,
                'deadline'    => now()->addMonths(8)->format('Y-m-d'),
                'category'    => 2,
                'tasks' => [
                    ['title' => 'Open high-yield savings account', 'is_completed' => true],
                    ['title' => 'Set up automatic transfers ($500/month)', 'is_completed' => true],
                    ['title' => 'Reach $5,000 milestone', 'is_completed' => true],
                    ['title' => 'Reach $10,000 milestone', 'is_completed' => true],
                    ['title' => 'Reach $15,000 milestone', 'is_completed' => false],
                    ['title' => 'Reach $20,000 goal', 'is_completed' => false],
                ],
                'milestones' => [
                    ['title' => '$5,000 saved',  'is_completed' => true,  'target_date' => now()->subMonths(3)->format('Y-m-d')],
                    ['title' => '$10,000 saved', 'is_completed' => true,  'target_date' => now()->subMonth()->format('Y-m-d')],
                    ['title' => '$15,000 saved', 'is_completed' => false, 'target_date' => now()->addMonths(3)->format('Y-m-d')],
                    ['title' => '$20,000 saved', 'is_completed' => false, 'target_date' => now()->addMonths(8)->format('Y-m-d')],
                ],
            ],
            [
                'title'       => 'Read 24 Books This Year',
                'description' => 'Read 2 books per month across business, philosophy, and science.',
                'priority'    => 'medium',
                'status'      => 'active',
                'progress'    => 75,
                'deadline'    => now()->endOfYear()->format('Y-m-d'),
                'category'    => 4,
                'tasks' => [
                    ['title' => 'Atomic Habits — James Clear', 'is_completed' => true],
                    ['title' => 'Deep Work — Cal Newport', 'is_completed' => true],
                    ['title' => 'The Lean Startup — Eric Ries', 'is_completed' => true],
                    ['title' => 'Thinking Fast and Slow', 'is_completed' => true],
                    ['title' => 'Zero to One — Peter Thiel', 'is_completed' => false],
                    ['title' => 'The Psychology of Money', 'is_completed' => false],
                ],
                'milestones' => [
                    ['title' => 'First 6 books read',  'is_completed' => true,  'target_date' => now()->subMonths(6)->format('Y-m-d')],
                    ['title' => '12 books completed',  'is_completed' => true,  'target_date' => now()->subMonths(3)->format('Y-m-d')],
                    ['title' => '18 books completed',  'is_completed' => false, 'target_date' => now()->addMonths(2)->format('Y-m-d')],
                    ['title' => '24 books completed!', 'is_completed' => false, 'target_date' => now()->endOfYear()->format('Y-m-d')],
                ],
            ],
            [
                'title'       => 'Master Spanish Language',
                'description' => 'Achieve B2 proficiency in Spanish through daily practice and immersion.',
                'priority'    => 'medium',
                'status'      => 'active',
                'progress'    => 30,
                'deadline'    => now()->addYear()->format('Y-m-d'),
                'category'    => 4,
                'tasks' => [
                    ['title' => 'Complete Duolingo A1 course', 'is_completed' => true],
                    ['title' => '30 minutes daily practice', 'is_completed' => true],
                    ['title' => 'Complete A2 grammar course', 'is_completed' => false],
                    ['title' => 'Find a language exchange partner', 'is_completed' => false],
                    ['title' => 'Watch 5 Spanish movies', 'is_completed' => false],
                    ['title' => 'Take B2 proficiency exam', 'is_completed' => false],
                ],
                'milestones' => [
                    ['title' => 'A1 proficiency achieved',  'is_completed' => true,  'target_date' => now()->subMonth()->format('Y-m-d')],
                    ['title' => 'A2 proficiency achieved',  'is_completed' => false, 'target_date' => now()->addMonths(4)->format('Y-m-d')],
                    ['title' => 'B1 proficiency achieved',  'is_completed' => false, 'target_date' => now()->addMonths(8)->format('Y-m-d')],
                    ['title' => 'B2 certification passed!', 'is_completed' => false, 'target_date' => now()->addYear()->format('Y-m-d')],
                ],
            ],
            [
                'title'       => 'Meditate Daily for 1 Year',
                'description' => 'Build a consistent daily meditation practice for mental clarity and focus.',
                'priority'    => 'low',
                'status'      => 'completed',
                'progress'    => 100,
                'deadline'    => now()->subMonth()->format('Y-m-d'),
                'category'    => 3,
                'tasks' => [
                    ['title' => 'Start 10-minute daily sessions', 'is_completed' => true],
                    ['title' => 'Complete 30-day streak', 'is_completed' => true],
                    ['title' => 'Try guided meditation apps', 'is_completed' => true],
                    ['title' => 'Complete 100-day streak', 'is_completed' => true],
                    ['title' => 'Complete 365-day streak', 'is_completed' => true],
                ],
                'milestones' => [
                    ['title' => '30-day streak! 🔥',  'is_completed' => true, 'target_date' => now()->subMonths(10)->format('Y-m-d')],
                    ['title' => '100-day streak! 💫', 'is_completed' => true, 'target_date' => now()->subMonths(7)->format('Y-m-d')],
                    ['title' => '365-day streak! 🏆', 'is_completed' => true, 'target_date' => now()->subMonth()->format('Y-m-d')],
                ],
            ],
        ];

        foreach ($goalsData as $goalData) {
            $tasksData      = $goalData['tasks'];
            $milestonesData = $goalData['milestones'];
            $categoryIndex  = $goalData['category'];

            unset($goalData['tasks'], $goalData['milestones'], $goalData['category']);

            $goal = $user->goals()->firstOrCreate(
                ['title' => $goalData['title']],
                [
                    ...$goalData,
                    'category_id' => $createdCategories[$categoryIndex]->id,
                    'completed_at' => $goalData['status'] === 'completed' ? now()->subMonth() : null,
                ]
            );

            foreach ($tasksData as $index => $taskData) {
                $goal->tasks()->firstOrCreate(
                    ['title' => $taskData['title']],
                    [
                        ...$taskData,
                        'user_id'      => $user->id,
                        'sort_order'   => $index,
                        'completed_at' => $taskData['is_completed'] ? now()->subDays(rand(1, 30)) : null,
                    ]
                );
            }

            foreach ($milestonesData as $milestoneData) {
                $goal->milestones()->firstOrCreate(
                    ['title' => $milestoneData['title']],
                    [
                        ...$milestoneData,
                        'user_id'      => $user->id,
                        'completed_at' => $milestoneData['is_completed'] ? now()->subDays(rand(1, 60)) : null,
                    ]
                );
            }
        }
    }
}
