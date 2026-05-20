<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function store(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
        ]);

        $maxOrder = $goal->tasks()->max('sort_order') ?? 0;

        $goal->tasks()->create([
            ...$validated,
            'user_id'    => Auth::id(),
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Task added!');
    }

    public function toggle(Task $task)
    {
        $this->authorize('update', $task->goal);

        $task->update([
            'is_completed' => !$task->is_completed,
            'completed_at' => !$task->is_completed ? now() : null,
        ]);

        // Auto-update goal progress based on tasks
        $goal = $task->goal;
        $totalTasks = $goal->tasks()->count();
        if ($totalTasks > 0) {
            $completedTasks = $goal->completedTasks()->count();
            $progress = round(($completedTasks / $totalTasks) * 100);
            $goal->update(['progress' => $progress]);
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success'      => true,
                'is_completed' => $task->is_completed,
                'progress'     => $task->goal->fresh()->progress,
            ]);
        }

        return back();
    }

    public function destroy(Task $task)
    {
        $this->authorize('update', $task->goal);
        $task->delete();
        return back()->with('success', 'Task removed.');
    }

    public function reorder(Request $request)
    {
        $request->validate(['tasks' => 'required|array']);

        foreach ($request->tasks as $order => $taskId) {
            Task::where('id', $taskId)
                ->where('user_id', Auth::id())
                ->update(['sort_order' => $order]);
        }

        return response()->json(['success' => true]);
    }

    public function index()
    {
        $user  = Auth::user();
        $tasks = $user->tasks()
            ->with('goal')
            ->where('is_completed', false)
            ->orderBy('due_date')
            ->get();

        $completedTasks = $user->tasks()
            ->with('goal')
            ->where('is_completed', true)
            ->orderBy('completed_at', 'desc')
            ->take(20)
            ->get();

        return view('tasks.index', compact('tasks', 'completedTasks'));
    }
}
