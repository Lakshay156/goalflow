<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $user       = Auth::user();
        $categories = $user->categories()->withCount('goals')->get();
        return view('settings.index', compact('user', 'categories'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio'   => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully! ✨');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|confirmed|min:8',
        ]);

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully!');
    }

    public function updateTheme(Request $request)
    {
        $request->validate(['theme' => 'required|in:dark,midnight,frost']);
        Auth::user()->update(['theme' => $request->theme]);
        return back()->with('success', 'Theme updated!');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'color' => 'required|string',
            'icon'  => 'required|string|max:10',
        ]);

        Auth::user()->categories()->create($validated);
        return back()->with('success', 'Category created!');
    }

    public function destroyCategory(Category $category)
    {
        if ($category->user_id !== Auth::id()) abort(403);
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
