<x-layouts.auth>
<div class="w-full max-w-md animate-fade-in-up">
    <div class="glass-card-static rounded-3xl p-8 shadow-glass-lg">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-indigo-500/15 border border-indigo-500/25 flex items-center justify-center text-3xl mx-auto mb-4">🔑</div>
            <h1 class="text-2xl font-bold text-white mb-2">Forgot Password?</h1>
            <p class="text-white/50 text-sm">Enter your email and we'll send a reset link</p>
        </div>

        @if(session('status'))
        <div class="badge badge-completed mb-6 text-sm w-full justify-center">✅ {{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="glass-input w-full px-4 py-3 text-sm" placeholder="you@example.com">
                @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn-primary w-full justify-center py-3.5 rounded-xl">
                Send Reset Link
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 text-sm transition-colors">← Back to Sign In</a>
        </div>
    </div>
</div>
</x-layouts.auth>
