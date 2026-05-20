<x-layouts.auth>
<div class="w-full max-w-md animate-fade-in-up">
    <div class="glass-card-static rounded-3xl p-8 shadow-glass-lg">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-indigo-500/15 border border-indigo-500/25 flex items-center justify-center text-3xl mx-auto mb-4">🔐</div>
            <h1 class="text-2xl font-bold text-white mb-2">Reset Password</h1>
            <p class="text-white/50 text-sm">Enter your new password below</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', $request->email) }}" required
                    class="glass-input w-full px-4 py-3 text-sm" autofocus autocomplete="username">
                @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">New Password</label>
                <input type="password" name="password" required
                    class="glass-input w-full px-4 py-3 text-sm" autocomplete="new-password">
                @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="glass-input w-full px-4 py-3 text-sm" autocomplete="new-password">
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-3.5 rounded-xl mt-2">
                Reset Password
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
        </form>
    </div>
</div>
</x-layouts.auth>
