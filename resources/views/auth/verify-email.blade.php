<x-layouts.auth>
<div class="w-full max-w-md animate-fade-in-up">
    <div class="glass-card-static rounded-3xl p-8 shadow-glass-lg">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-amber-500/15 border border-amber-500/25 flex items-center justify-center text-3xl mx-auto mb-4">✉️</div>
            <h1 class="text-2xl font-bold text-white mb-2">Verify your email</h1>
            <p class="text-white/50 text-sm leading-relaxed">
                Thanks for signing up! Please verify your email address by clicking the link we just sent you.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
        <div class="badge badge-completed w-full justify-center mb-5 text-sm py-2.5">
            ✅ A new verification link has been sent to your email.
        </div>
        @endif

        <div class="flex flex-col gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-primary w-full justify-center py-3.5 rounded-xl">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-glass w-full justify-center py-3 rounded-xl text-sm">
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</div>
</x-layouts.auth>
