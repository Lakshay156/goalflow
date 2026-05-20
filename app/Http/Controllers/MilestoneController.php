<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $milestones = $user->milestones()
            ->with('goal')
            ->orderBy('target_date')
            ->get()
            ->groupBy(fn($m) => $m->is_completed ? 'completed' : 'pending');

        return view('milestones.index', compact('milestones'));
    }

    public function store(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'nullable|date',
        ]);

        $goal->milestones()->create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Milestone added! 🏆');
    }

    public function toggle(Milestone $milestone)
    {
        $this->authorize('update', $milestone->goal);

        $milestone->update([
            'is_completed' => !$milestone->is_completed,
            'completed_at' => !$milestone->is_completed ? now() : null,
        ]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'is_completed' => $milestone->is_completed]);
        }

        return back()->with('success', $milestone->is_completed ? '🎉 Milestone achieved!' : 'Milestone reopened.');
    }

    public function destroy(Milestone $milestone)
    {
        $this->authorize('update', $milestone->goal);
        $milestone->delete();
        return back()->with('success', 'Milestone removed.');
    }
}
