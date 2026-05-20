<x-layouts.guest>

<!-- Floating Navbar -->
<nav class="fixed top-0 left-0 right-0 z-50 px-6 py-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="text-white font-semibold text-xl tracking-tight">GoalFlow</span>
        </div>
        <div class="hidden md:flex items-center gap-8">
            <a href="#features" class="text-white/50 hover:text-white/90 transition-colors text-sm">Features</a>
            <a href="#preview" class="text-white/50 hover:text-white/90 transition-colors text-sm">Preview</a>
            <a href="#testimonials" class="text-white/50 hover:text-white/90 transition-colors text-sm">Reviews</a>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="btn-glass btn-sm">Sign In</a>
            <a href="{{ route('register') }}" class="btn-primary btn-sm">Get Started</a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="relative min-h-screen flex items-center justify-center px-6 pt-20">
    <div class="hero-gradient absolute inset-0 pointer-events-none"></div>

    <div class="max-w-5xl mx-auto text-center relative z-10">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-card-static mb-8 animate-fade-in">
            <div class="w-2 h-2 rounded-full bg-emerald-400 animate-ping-slow"></div>
            <span class="text-sm text-white/70">AI-Powered Goal Intelligence</span>
        </div>

        <!-- Main headline -->
        <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-bold tracking-tight leading-[1.05] mb-6 animate-fade-in-up" style="animation-delay:0.1s;animation-fill-mode:both">
            <span class="text-white">Transform</span><br>
            <span class="gradient-text">Goals Into</span><br>
            <span class="text-white">Achievements.</span>
        </h1>

        <!-- Subheadline -->
        <p class="text-lg sm:text-xl text-white/50 max-w-2xl mx-auto leading-relaxed mb-10 animate-fade-in-up" style="animation-delay:0.2s;animation-fill-mode:both">
            The most beautiful goal tracking platform ever built. Set ambitious goals, track progress with stunning analytics, and achieve more — every single day.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16 animate-fade-in-up" style="animation-delay:0.3s;animation-fill-mode:both">
            <a href="{{ route('register') }}" class="btn-primary text-base px-8 py-3.5 rounded-2xl">
                Start for Free
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <a href="{{ route('login') }}" class="btn-glass text-base px-8 py-3.5 rounded-2xl">
                Sign In to Dashboard
            </a>
        </div>

        <!-- Floating Dashboard Preview -->
        <div class="relative animate-fade-in-up" style="animation-delay:0.4s;animation-fill-mode:both">
            <div class="absolute inset-0 bg-gradient-to-t from-space-900 via-transparent to-transparent z-10 pointer-events-none"></div>
            <div class="glass-card-static p-2 rounded-3xl shadow-glass-lg mx-auto max-w-4xl">
                <div class="rounded-2xl overflow-hidden bg-space-800 p-6">
                    <!-- Mini Dashboard Preview -->
                    <div class="grid grid-cols-4 gap-3 mb-4">
                        @foreach([['Total Goals','12','🎯','indigo'],['Completed','8','✅','emerald'],['In Progress','3','⚡','violet'],['Streak','14d','🔥','amber']] as $stat)
                        <div class="glass-card-static p-3 rounded-xl">
                            <div class="text-lg mb-1">{{ $stat[2] }}</div>
                            <div class="text-xl font-bold text-white">{{ $stat[1] }}</div>
                            <div class="text-xs text-white/40">{{ $stat[0] }}</div>
                        </div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach([['Run Marathon','65','high'],['Launch SaaS','40','critical'],['Read 24 Books','75','medium']] as $g)
                        <div class="glass-card-static p-4 rounded-xl">
                            <div class="text-sm font-medium text-white/80 mb-2">{{ $g[0] }}</div>
                            <div class="progress-track mb-1"><div class="progress-fill {{ $g[2]==='critical'?'progress-fill-rose':($g[2]==='medium'?'progress-fill-cyan':'') }}" style="width:{{ $g[1] }}%"></div></div>
                            <div class="text-xs text-white/40">{{ $g[1] }}% complete</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-24 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 reveal">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full glass-card-static text-indigo-400 text-sm mb-4">✦ Features</div>
            <h2 class="text-4xl sm:text-5xl font-bold text-white mb-4 tracking-tight">Everything you need to</h2>
            <p class="text-4xl sm:text-5xl font-bold gradient-text tracking-tight">reach your full potential.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 stagger-children">
            @php $features = [
                ['🎯','Goal Intelligence','Create beautifully organized goals with priorities, deadlines, and smart categories. Track every step of your journey.'],
                ['⚡','Task Breakdown','Break any goal into actionable tasks. Drag-and-drop reordering with iOS-inspired checkboxes that feel satisfying to complete.'],
                ['🏆','Milestone Tracking','Celebrate progress with achievement milestones. Build momentum with a visual timeline of your wins.'],
                ['📊','Rich Analytics','Beautiful charts and heatmaps show your productivity patterns. Weekly insights to keep you on track.'],
                ['📅','Smart Calendar','See all your deadlines, tasks, and milestones in one beautiful calendar view inspired by Apple Calendar.'],
                ['🔥','Daily Streaks','Build habits with daily streaks. Never miss a day with smart reminders and momentum tracking.'],
            ] @endphp

            @foreach($features as $f)
            <div class="feature-card reveal">
                <div class="text-4xl mb-4">{{ $f[0] }}</div>
                <h3 class="text-lg font-semibold text-white mb-2">{{ $f[1] }}</h3>
                <p class="text-white/50 text-sm leading-relaxed">{{ $f[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="py-24 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16 reveal">
            <h2 class="text-4xl font-bold text-white mb-3">Loved by achievers</h2>
            <p class="text-white/50">Join thousands who transformed their ambitions into reality.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6 stagger-children">
            @php $testimonials = [
                ['"GoalFlow completely changed how I track my goals. The interface feels like it was designed by Apple itself. I hit my marathon goal after 6 months of tracking!"','Sarah K.','Marathon Runner & Entrepreneur','SK'],
                ['"The analytics dashboard is insane. I can actually see my productivity patterns and know exactly when I\'m most effective. Game changer."','Marcus T.','Software Engineer','MT'],
                ['"I\'ve tried every productivity app. GoalFlow is the only one that feels premium enough to open every day. The glassmorphism UI is stunning."','Priya M.','Product Designer','PM'],
            ] @endphp
            @foreach($testimonials as $t)
            <div class="glass-card-static p-6 rounded-2xl reveal">
                <div class="flex gap-1 mb-4">
                    @for($i=0;$i<5;$i++)<span class="text-amber-400 text-sm">★</span>@endfor
                </div>
                <p class="text-white/70 text-sm leading-relaxed mb-6">{{ $t[0] }}</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-sm font-semibold">{{ $t[3] }}</div>
                    <div>
                        <div class="text-white font-medium text-sm">{{ $t[1] }}</div>
                        <div class="text-white/40 text-xs">{{ $t[2] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-24 px-6">
    <div class="max-w-4xl mx-auto">
        <div class="glass-card-static rounded-3xl p-12 text-center relative overflow-hidden reveal">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 via-violet-500/10 to-cyan-500/10 pointer-events-none"></div>
            <div class="relative z-10">
                <h2 class="text-4xl sm:text-5xl font-bold text-white mb-4 tracking-tight">Ready to achieve<br><span class="gradient-text">your biggest goals?</span></h2>
                <p class="text-white/50 mb-8 max-w-md mx-auto">Start free today. No credit card required. Begin tracking your first goal in under 60 seconds.</p>
                <a href="{{ route('register') }}" class="btn-primary text-base px-10 py-4 rounded-2xl">
                    Get Started Free
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="py-12 px-6 border-t border-white/5">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span class="text-white/60 font-medium">GoalFlow</span>
        </div>
        <p class="text-white/30 text-sm">© {{ date('Y') }} GoalFlow. Built for ambitious minds.</p>
        <div class="flex gap-6">
            <a href="{{ route('login') }}" class="text-white/40 hover:text-white/70 text-sm transition-colors">Login</a>
            <a href="{{ route('register') }}" class="text-white/40 hover:text-white/70 text-sm transition-colors">Register</a>
        </div>
    </div>
</footer>

<script>
// Scroll reveal
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

</x-layouts.guest>
