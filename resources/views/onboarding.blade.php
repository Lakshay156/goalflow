<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Welcome to GoalFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="theme-dark min-h-screen flex items-center justify-center p-4" x-data="onboarding()">
<div class="ambient-bg">
    <div class="ambient-orb orb-a"></div>
    <div class="ambient-orb orb-b"></div>
    <div class="ambient-orb orb-c"></div>
</div>

<div class="w-full max-w-xl relative z-10">

    {{-- Progress Indicator --}}
    <div class="flex items-center gap-2 justify-center mb-8">
        <template x-for="i in totalSteps" :key="i">
            <div class="h-1 rounded-full transition-all duration-500"
                 :class="i <= step ? 'bg-indigo-500' : 'bg-white/10'"
                 :style="i <= step ? 'width: ' + (i === step ? '24px' : '24px') : 'width: 24px'"></div>
        </template>
    </div>

    {{-- Card --}}
    <div class="glass-card-static rounded-3xl p-8 shadow-float">

        {{-- STEP 1 — Welcome --}}
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="text-center mb-8">
                <div class="w-20 h-20 rounded-3xl flex items-center justify-center text-4xl mx-auto mb-5" style="background:linear-gradient(135deg,rgba(99,102,241,0.25),rgba(139,92,246,0.2));border:1px solid rgba(99,102,241,0.3)">⚡</div>
                <h1 class="text-3xl font-bold mb-2" style="color:var(--text-primary)">Welcome to GoalFlow</h1>
                <p class="text-base leading-relaxed" style="color:var(--text-secondary)">Let's set up your personal workspace in 60 seconds.</p>
            </div>
            <div class="space-y-3 mb-8">
                @foreach([['🎯','Smart goal tracking','Break ambitious goals into achievable steps'],['📊','Beautiful analytics','See your progress with stunning visual insights'],['🤖','AI assistance','Get personalized suggestions and plans']] as [$e,$t,$d])
                <div class="flex items-center gap-4 p-4 rounded-2xl" style="background:var(--surface-1);border:1px solid var(--glass-border)">
                    <span class="text-2xl flex-shrink-0">{{ $e }}</span>
                    <div>
                        <div class="text-sm font-semibold" style="color:var(--text-primary)">{{ $t }}</div>
                        <div class="text-xs mt-0.5" style="color:var(--text-muted)">{{ $d }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <button @click="step++" class="btn-primary w-full justify-center py-3.5 text-base rounded-2xl">
                Get Started
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
        </div>

        {{-- STEP 2 — Goal Type --}}
        <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-indigo-400 mb-2">Step 1 of 3</p>
                <h2 class="text-2xl font-bold" style="color:var(--text-primary)">What's your main focus?</h2>
                <p class="text-sm mt-1" style="color:var(--text-muted)">We'll personalize your dashboard based on this.</p>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-6">
                @foreach([
                    ['study',   '📚', 'Study & Learning', 'Exams, courses, skills'],
                    ['fitness', '💪', 'Health & Fitness',  'Workouts, diet, wellness'],
                    ['career',  '💼', 'Career & Work',     'Projects, promotions, skills'],
                    ['creative','🎨', 'Creative Projects',  'Art, music, writing'],
                    ['finance', '💰', 'Finance & Savings',  'Budget, investments'],
                    ['personal','🌱', 'Personal Growth',    'Habits, mindfulness'],
                ] as [$v, $e, $t, $d])
                <button @click="data.goalType = '{{ $v }}'"
                        class="onboard-option text-left transition-all"
                        :class="data.goalType === '{{ $v }}' ? 'selected' : ''">
                    <span class="text-2xl block mb-2">{{ $e }}</span>
                    <span class="text-sm font-semibold block" style="color:var(--text-primary)">{{ $t }}</span>
                    <span class="text-xs" style="color:var(--text-muted)">{{ $d }}</span>
                </button>
                @endforeach
            </div>
            <div class="flex gap-3">
                <button @click="step--" class="btn-glass flex-1 justify-center py-3">Back</button>
                <button @click="step++" :disabled="!data.goalType" class="btn-primary flex-1 justify-center py-3" :class="!data.goalType ? 'opacity-40 cursor-not-allowed' : ''">Continue</button>
            </div>
        </div>

        {{-- STEP 3 — Productivity Style --}}
        <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-indigo-400 mb-2">Step 2 of 3</p>
                <h2 class="text-2xl font-bold" style="color:var(--text-primary)">Your work style?</h2>
                <p class="text-sm mt-1" style="color:var(--text-muted)">How do you prefer to tackle tasks?</p>
            </div>
            <div class="space-y-3 mb-6">
                @foreach([
                    ['deep',   '🧘', 'Deep Focus Sessions', '2-4 hour concentrated work blocks'],
                    ['sprint', '⚡', 'Sprint & Rest',       'Pomodoro-style 25min work cycles'],
                    ['daily',  '📅', 'Daily Habits',         'Consistent small daily actions'],
                    ['flex',   '🌊', 'Flexible Flow',        'Work whenever inspiration strikes'],
                ] as [$v, $e, $t, $d])
                <button @click="data.style = '{{ $v }}'"
                        class="onboard-option w-full text-left flex items-center gap-4"
                        :class="data.style === '{{ $v }}' ? 'selected' : ''">
                    <span class="text-2xl flex-shrink-0">{{ $e }}</span>
                    <div>
                        <span class="text-sm font-semibold block" style="color:var(--text-primary)">{{ $t }}</span>
                        <span class="text-xs" style="color:var(--text-muted)">{{ $d }}</span>
                    </div>
                    <div class="ml-auto w-5 h-5 rounded-full border-2 transition-all flex-shrink-0" :class="data.style === '{{ $v }}' ? 'border-indigo-500 bg-indigo-500' : 'border-white/20'">
                        <svg x-show="data.style === '{{ $v }}'" class="w-full h-full text-white p-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                    </div>
                </button>
                @endforeach
            </div>
            <div class="flex gap-3">
                <button @click="step--" class="btn-glass flex-1 justify-center py-3">Back</button>
                <button @click="step++" :disabled="!data.style" class="btn-primary flex-1 justify-center py-3" :class="!data.style ? 'opacity-40 cursor-not-allowed' : ''">Continue</button>
            </div>
        </div>

        {{-- STEP 4 — Theme --}}
        <div x-show="step === 4" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-indigo-400 mb-2">Step 3 of 3</p>
                <h2 class="text-2xl font-bold" style="color:var(--text-primary)">Pick your vibe</h2>
                <p class="text-sm mt-1" style="color:var(--text-muted)">Choose your visual experience. You can change it anytime.</p>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-6">
                @foreach([
                    ['theme-dark',     '🌙', 'Dark Mode',    '#050816', 'Deep space with glowing accents'],
                    ['theme-midnight', '⚫', 'Midnight',     '#000000', 'Pure black, ultra minimal'],
                    ['theme-light',    '☀️', 'Light Mode',   '#f0f2f8', 'Clean and bright'],
                    ['theme-frost',    '❄️', 'Frost',        '#dde4f0', 'VisionOS translucent'],
                ] as [$v, $e, $t, $bg, $d])
                <button @click="data.theme = '{{ $v }}'"
                        class="onboard-option text-left"
                        :class="data.theme === '{{ $v }}' ? 'selected' : ''">
                    <div class="w-full h-12 rounded-xl mb-3" style="background:{{ $bg }};border:1px solid rgba(255,255,255,0.1)"></div>
                    <div class="flex items-center gap-1.5 mb-0.5">
                        <span>{{ $e }}</span>
                        <span class="text-sm font-semibold" style="color:var(--text-primary)">{{ $t }}</span>
                    </div>
                    <span class="text-xs" style="color:var(--text-muted)">{{ $d }}</span>
                </button>
                @endforeach
            </div>
            <div class="flex gap-3">
                <button @click="step--" class="btn-glass flex-1 justify-center py-3">Back</button>
                <button @click="completeOnboarding()" class="btn-primary flex-1 justify-center py-3.5 rounded-xl">
                    <span x-text="loading ? 'Setting up...' : 'Launch GoalFlow ✨'"></span>
                </button>
            </div>
        </div>

    </div>

    {{-- Skip link --}}
    <div class="text-center mt-4" x-show="step > 1">
        <a href="{{ route('dashboard') }}" class="text-xs transition-opacity hover:opacity-80" style="color:var(--text-muted)">Skip setup →</a>
    </div>
</div>

<script>
function onboarding() {
    return {
        step: 1,
        totalSteps: 4,
        loading: false,
        data: {
            goalType: '',
            style: '',
            theme: 'theme-dark',
        },
        async completeOnboarding() {
            this.loading = true;
            localStorage.setItem('gf-theme', this.data.theme);
            try {
                await fetch('/onboarding/complete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify(this.data),
                });
            } catch(e) {}
            window.location.href = '{{ route("dashboard") }}';
        }
    };
}
</script>
</body>
</html>
