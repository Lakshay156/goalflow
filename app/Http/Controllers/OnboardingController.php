<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function show()
    {
        if (Auth::user()->onboarding_completed) {
            return redirect()->route('dashboard');
        }
        return view('onboarding');
    }

    public function complete(Request $request)
    {
        $validated = $request->validate([
            'goalType' => 'nullable|string|max:50',
            'style'    => 'nullable|string|max:50',
            'theme'    => 'nullable|string|max:30',
        ]);

        $user = Auth::user();
        $user->update([
            'onboarding_completed' => true,
            'goal_type'           => $validated['goalType'] ?? null,
            'productivity_style'  => $validated['style']   ?? null,
        ]);

        // Update theme preference
        if (!empty($validated['theme'])) {
            $themeName = str_replace('theme-', '', $validated['theme']);
            $user->update(['theme' => $themeName]);
        }

        return response()->json(['success' => true]);
    }
}
