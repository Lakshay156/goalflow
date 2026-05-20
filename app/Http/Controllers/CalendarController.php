<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $events = collect();

        // Goals with deadlines
        $user->goals()->whereNotNull('deadline')->get()->each(function ($goal) use ($events) {
            $events->push([
                'id'    => 'goal-' . $goal->id,
                'title' => $goal->title,
                'date'  => $goal->deadline->format('Y-m-d'),
                'type'  => 'goal',
                'color' => $goal->priority_color,
                'url'   => route('goals.show', $goal),
                'status' => $goal->status,
            ]);
        });

        // Tasks with due dates
        $user->tasks()->whereNotNull('due_date')->get()->each(function ($task) use ($events) {
            $events->push([
                'id'    => 'task-' . $task->id,
                'title' => $task->title,
                'date'  => $task->due_date->format('Y-m-d'),
                'type'  => 'task',
                'color' => $task->is_completed ? '#10b981' : '#6366f1',
                'url'   => route('goals.show', $task->goal_id),
                'status' => $task->is_completed ? 'completed' : 'pending',
            ]);
        });

        // Milestones with target dates
        $user->milestones()->whereNotNull('target_date')->get()->each(function ($milestone) use ($events) {
            $events->push([
                'id'    => 'milestone-' . $milestone->id,
                'title' => $milestone->title,
                'date'  => $milestone->target_date->format('Y-m-d'),
                'type'  => 'milestone',
                'color' => $milestone->is_completed ? '#10b981' : '#f59e0b',
                'url'   => route('goals.show', $milestone->goal_id),
                'status' => $milestone->is_completed ? 'completed' : 'pending',
            ]);
        });

        return view('calendar.index', ['events' => $events->values()]);
    }
}
