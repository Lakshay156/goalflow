<x-layouts.auth>
<div class="w-full max-w-md animate-fade-in-up">
    <div class="glass-card-static rounded-3xl p-8 shadow-glass-lg">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-violet-500/15 border border-violet-500/25 flex items-center justify-center text-3xl mx-auto mb-4">🛡️</div>
            <h1 class="text-2xl font-bold text-white mb-2">Confirm Password</h1>
            <p class="text-white/50 text-sm">This is a secure area. Please confirm your password to continue.</p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">Password</label>
                <input type="password" name="password" required autofocus
                    class="glass-input w-full px-4 py-3 text-sm" autocomplete="current-password">
                @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn-primary w-full justify-center py-3.5 rounded-xl">
                Confirm
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </button>
        </form>
    </div>
</div>
</x-layouts.auth>
