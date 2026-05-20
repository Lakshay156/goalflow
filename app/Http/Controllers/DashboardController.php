<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Task;
use App\Models\Milestone;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private array $quotes = [
        "Small daily improvements lead to staggering long-term results.",
        "Every goal is reachable if you go far enough.",
        "The secret of getting ahead is getting started.",
        "Done is better than perfect. Ship it.",
        "Focus on progress, not perfection.",
        "One task at a time. One day at a time.",
        "Discipline is choosing between what you want now and what you want most.",
        "Your future self is watching you right now through memories.",
        "Consistency compounds. Show up every day.",
        "Break it down. Show up. Level up.",
    ];

    public function index()
    {
        $user = Auth::user();

        // Core stats
        $totalGoals     = $user->goals()->count();
        $activeGoals    = $user->goals()->where('status', 'active')->count();
        $completedGoals = $user->goals()->where('status', 'completed')->count();
        $avgProgress    = round($user->goals()->where('status', 'active')->avg('progress') ?? 0);

        // Top goal for Today Focus widget
        $topGoal = $user->goals()
            ->with(['tasks', 'category'])
            ->where('status', 'active')
            ->orderByRaw('progress DESC, deadline ASC')
            ->first();

        // Today's tasks (pending, due today or no date, for the top goal & beyond)
        $todayTasks = $user->tasks()
            ->with('goal')
            ->where('is_completed', false)
            ->where(fn($q) => $q->whereDate('due_date', today())->orWhereNull('due_date'))
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // Active goals for widget
        $recentGoals = $user->goals()
            ->with('category')
            ->where('status', 'active')
            ->orderByDesc('progress')
            ->take(6)
            ->get();

        // Upcoming deadlines
        $upcomingDeadlines = $user->goals()
            ->whereNotNull('deadline')
            ->where('status', 'active')
            ->where('deadline', '>=', now())
            ->orderBy('deadline')
            ->take(5)
            ->get();

        // Recent milestone wins
        $recentMilestones = $user->milestones()
            ->with('goal')
            ->where('is_completed', true)
            ->orderByDesc('completed_at')
            ->take(5)
            ->get();

        // Weekly chart data
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyData[] = [
                'day'             => $date->format('D'),
                'tasks_completed' => $user->tasks()
                    ->whereDate('completed_at', $date)
                    ->where('is_completed', true)
                    ->count(),
            ];
        }

        // Daily motivational quote (rotates daily)
        $quote = $this->quotes[now()->dayOfYear % count($this->quotes)];

        return view('dashboard', compact(
            'totalGoals', 'activeGoals', 'completedGoals', 'avgProgress',
            'topGoal', 'todayTasks',
            'recentGoals', 'upcomingDeadlines', 'recentMilestones',
            'weeklyData', 'quote'
        ));
    }
}
