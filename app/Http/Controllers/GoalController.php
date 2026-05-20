<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $query  = $user->goals()->with('category', 'tasks', 'milestones');

        if ($request->filled('status'))   $query->where('status',   $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('category')) $query->where('category_id', $request->category);
        if ($request->filled('search'))   $query->where('title', 'like', '%' . $request->search . '%');

        $goals      = $query->latest()->paginate(12);
        $categories = $user->categories()->get();

        $stats = [
            'total'     => $user->goals()->count(),
            'active'    => $user->goals()->where('status', 'active')->count(),
            'completed' => $user->goals()->where('status', 'completed')->count(),
            'paused'    => $user->goals()->where('status', 'paused')->count(),
        ];

        return view('goals.index', compact('goals', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = Auth::user()->categories()->get();
        return view('goals.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'priority'    => 'required|in:low,medium,high,critical',
            'deadline'    => 'nullable|date|after:today',
            'progress'    => 'integer|min:0|max:100',
        ]);

        $goal = Auth::user()->goals()->create($validated);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Goal created successfully! 🎯');
    }

    public function show(Goal $goal)
    {
        $this->authorize('view', $goal);
        $goal->load('category', 'tasks', 'milestones');
        $categories = Auth::user()->categories()->get();
        return view('goals.show', compact('goal', 'categories'));
    }

    public function edit(Goal $goal)
    {
        $this->authorize('update', $goal);
        $categories = Auth::user()->categories()->get();
        return view('goals.edit', compact('goal', 'categories'));
    }

    public function update(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'priority'    => 'required|in:low,medium,high,critical',
            'status'      => 'required|in:active,completed,paused,archived',
            'deadline'    => 'nullable|date',
            'progress'    => 'integer|min:0|max:100',
        ]);

        if ($validated['status'] === 'completed' && $goal->status !== 'completed') {
            $validated['completed_at'] = now();
            $validated['progress'] = 100;
        }

        $goal->update($validated);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Goal updated successfully! ✨');
    }

    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);
        $goal->delete();
        return redirect()->route('goals.index')
            ->with('success', 'Goal deleted.');
    }

    public function updateProgress(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);
        $request->validate(['progress' => 'required|integer|min:0|max:100']);
        $goal->update(['progress' => $request->progress]);
        return response()->json(['success' => true, 'progress' => $goal->progress]);
    }
}
