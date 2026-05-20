<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Goal completion by category
        $categoryStats = $user->categories()->with(['goals'])->get()->map(function ($cat) {
            return [
                'name'      => $cat->name,
                'icon'      => $cat->icon ?? '🎯',
                'color'     => $cat->color,
                'total'     => $cat->goals->count(),
                'completed' => $cat->goals->where('status', 'completed')->count(),
            ];
        });

        // Monthly completion over last 6 months
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyStats[] = [
                'month'     => $month->format('M'),
                'completed' => $user->goals()
                    ->whereYear('completed_at',  $month->year)
                    ->whereMonth('completed_at', $month->month)
                    ->where('status', 'completed')
                    ->count(),
                'created' => $user->goals()
                    ->whereYear('created_at',  $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        // Weekly tasks last 4 weeks
        $weeklyTasks = [];
        for ($i = 3; $i >= 0; $i--) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end   = now()->subWeeks($i)->endOfWeek();
            $weeklyTasks[] = [
                'week'      => 'W' . now()->subWeeks($i)->weekOfYear,
                'completed' => $user->tasks()
                    ->whereBetween('completed_at', [$start, $end])
                    ->where('is_completed', true)
                    ->count(),
                'total' => $user->tasks()
                    ->whereBetween('created_at', [$start, $end])
                    ->count(),
            ];
        }

        // Priority distribution
        $priorityStats = [
            'low'      => $user->goals()->where('priority', 'low')->count(),
            'medium'   => $user->goals()->where('priority', 'medium')->count(),
            'high'     => $user->goals()->where('priority', 'high')->count(),
            'critical' => $user->goals()->where('priority', 'critical')->count(),
        ];

        // Overall stats
        $overallStats = [
            'total_goals'          => $user->goals()->count(),
            'completed_goals'      => $user->goals()->where('status', 'completed')->count(),
            'total_tasks'          => $user->tasks()->count(),
            'completed_tasks'      => $user->tasks()->where('is_completed', true)->count(),
            'total_milestones'     => $user->milestones()->count(),
            'completed_milestones' => $user->milestones()->where('is_completed', true)->count(),
            'avg_progress'         => round($user->goals()->where('status', 'active')->avg('progress') ?? 0),
            'streak_days'          => $user->streak_days,
            'week_tasks'           => $user->tasks()
                ->where('is_completed', true)
                ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];

        return view('analytics.index', compact(
            'categoryStats', 'monthlyStats', 'weeklyTasks', 'priorityStats', 'overallStats'
        ));
    }
}
