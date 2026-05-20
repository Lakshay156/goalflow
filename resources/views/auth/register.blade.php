<x-layouts.auth>
<div class="w-full max-w-md animate-fade-in-up">
    <div class="glass-card-static rounded-3xl p-8 shadow-glass-lg">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">Create account</h1>
            <p class="text-white/50 text-sm">Start achieving your goals today</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="glass-input w-full px-4 py-3 text-sm"
                    placeholder="Alex Rivera">
                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="glass-input w-full px-4 py-3 text-sm"
                    placeholder="you@example.com">
                @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">Password</label>
                <input type="password" name="password" required
                    class="glass-input w-full px-4 py-3 text-sm"
                    placeholder="Minimum 8 characters">
                @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="glass-input w-full px-4 py-3 text-sm"
                    placeholder="Repeat password">
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-3.5 rounded-xl text-sm mt-2">
                Create Account
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
        </form>

        <p class="text-center text-white/40 text-sm mt-6">
            Already have an account?
            <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 transition-colors font-medium">Sign in</a>
        </p>
    </div>
</div>
</x-layouts.auth>
