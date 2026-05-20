<x-layouts.app>
<x-slot:title>Dashboard</x-slot:title>

{{-- ══ GREETING ══════════════════════════════════════════════ --}}
<div class="mb-6 flex items-end justify-between">
    <div>
        <p class="text-sm mb-1" style="color:var(--text-muted)">{{ now()->format('l, F j') }}</p>
        <h2 class="text-2xl font-bold tracking-tight" style="color:var(--text-primary)">
            {{ now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening') }},
            <span class="gradient-text">{{ explode(' ', auth()->user()->name)[0] }}</span> 👋
        </h2>
    </div>
    <a href="{{ route('goals.create') }}" class="btn-primary hidden sm:inline-flex">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
        New Goal
    </a>
</div>

{{-- ══ BENTO GRID ═══════════════════════════════════════════ --}}
{{--
  Grid layout (12-col):
  Row 1: Today Focus (6) │ Streak (3)       │ Goals Achieved (3)
  Row 2: Today Focus (6) │ Pomodoro (3)     │ Flip Clock NEW (3)  ← under Goals Achieved, right of Pomodoro
  Row 3: Active Goals(6) │ AI Suggestions(3)│ Stats (3)
  Row 4: Weekly Chart(6) │ Due Soon (3)     │ Recent Wins (3)
--}}
<div class="bento-grid stagger">

    {{-- 1. TODAY FOCUS — Large hero (2×2) --}}
    <div class="bento-2x2 bento-card today-focus p-0 flex flex-col">
        <div class="flex-1 p-6 flex flex-col justify-between">
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-2 h-2 rounded-full" style="background:#6366f1;animation:pulse 2s infinite;box-shadow:0 0 6px rgba(99,102,241,0.7)"></div>
                        <span class="text-xs font-semibold uppercase tracking-widest" style="color:rgba(99,102,241,0.8)">Today's Focus</span>
                    </div>
                    <h3 class="text-xl font-bold" style="color:var(--text-primary)">{{ $topGoal?->title ?? 'Set your first goal' }}</h3>
                </div>
                <!-- Progress Ring -->
                <div class="relative flex-shrink-0">
                    <svg width="64" height="64" viewBox="0 0 64 64" class="-rotate-90">
                        <circle cx="32" cy="32" r="26" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="5"/>
                        <circle cx="32" cy="32" r="26" fill="none" stroke="url(#focusGrad)" stroke-width="5"
                            stroke-linecap="round"
                            stroke-dasharray="{{ 2 * pi() * 26 }}"
                            stroke-dashoffset="{{ 2 * pi() * 26 * (1 - ($topGoal?->progress ?? 0) / 100) }}"
                            style="transition:stroke-dashoffset 1.4s cubic-bezier(0.16,1,0.3,1)"/>
                        <defs>
                            <linearGradient id="focusGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#6366f1"/>
                                <stop offset="100%" style="stop-color:#8b5cf6"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-sm font-bold" style="color:var(--text-primary)">{{ $topGoal?->progress ?? 0 }}%</span>
                    </div>
                </div>
            </div>

            <!-- Tasks -->
            <div class="space-y-2 mb-4">
                @forelse($todayTasks->take(3) as $task)
                <div class="flex items-center gap-3 py-2.5 px-3.5 rounded-xl" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.07)">
                    <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="flex-shrink-0">
                        @csrf @method('PATCH')
                        <button type="submit"><input type="checkbox" class="ios-checkbox pointer-events-none" {{ $task->is_completed ? 'checked' : '' }}></button>
                    </form>
                    <span class="text-sm flex-1 truncate {{ $task->is_completed ? 'line-through opacity-40' : '' }}" style="color:var(--text-primary)">{{ $task->title }}</span>
                    @if($task->due_date && $task->due_date->isToday())
                    <span class="badge badge-critical text-[10px]">Today</span>
                    @endif
                </div>
                @empty
                <div class="text-center py-4">
                    <p class="text-sm" style="color:var(--text-muted)">No tasks for today.</p>
                    @if($topGoal)
                    <a href="{{ route('goals.show', $topGoal) }}" class="text-xs text-indigo-400 hover:text-indigo-300 mt-1 inline-block">Add tasks to your goal →</a>
                    @endif
                </div>
                @endforelse

            </div>

            {{-- New Goal CTA — only when user has zero goals --}}
            @if(!$topGoal)
            <div class="flex flex-col items-center justify-center py-3 mb-2">
                <a href="{{ route('goals.create') }}"
                   class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm"
                   style="box-shadow:0 0 24px rgba(99,102,241,0.35),0 4px 16px rgba(99,102,241,0.25);animation:goalPulse 2.5s ease-in-out infinite">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                    Set your first goal
                </a>
                <p class="text-xs mt-2" style="color:var(--text-muted)">Start your journey — it only takes 30 seconds ✨</p>
            </div>
            @endif

            <!-- Quote -->

            <div class="p-3 rounded-xl" style="background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.15)">
                <p class="focus-quote" style="color:var(--text-secondary)">"{{ $quote }}"</p>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="px-6 py-3 flex items-center justify-between border-t" style="border-color:rgba(255,255,255,0.06)">
            <span class="text-sm" style="color:var(--text-muted)">{{ $todayTasks->where('is_completed', true)->count() }}/{{ $todayTasks->count() }} done</span>
            <a href="{{ route('goals.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">View all goals →</a>
        </div>
    </div>

    {{-- 2. STREAK — row 1 col 7-9 --}}
    <div class="bento-1x1 bento-card p-5 flex flex-col justify-between" style="background:linear-gradient(135deg,rgba(245,158,11,0.12),rgba(249,115,22,0.08));border-color:rgba(245,158,11,0.2)">
        <div class="text-3xl mb-2" style="animation:levitate 3s ease-in-out infinite">🔥</div>
        <div>
            <div class="text-4xl font-black mb-0.5" style="color:var(--text-primary)">{{ auth()->user()->streak_days }}</div>
            <div class="text-sm font-medium text-amber-400">day streak</div>
            <div class="text-xs mt-1" style="color:var(--text-muted)">Keep it going!</div>
        </div>
    </div>

    {{-- 3. GOALS ACHIEVED — row 1 col 10-12 --}}
    <div class="bento-1x1 bento-card p-5 flex flex-col justify-between" style="background:linear-gradient(135deg,rgba(16,185,129,0.12),rgba(6,182,212,0.06));border-color:rgba(16,185,129,0.2)">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-2" style="background:rgba(16,185,129,0.2)">
            <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="text-4xl font-black mb-0.5 counter" style="color:#34d399" data-target="{{ $completedGoals }}">0</div>
            <div class="text-sm font-medium text-emerald-400">goals achieved</div>
            <div class="text-xs mt-1" style="color:var(--text-muted)">of {{ $totalGoals }} total</div>
        </div>
    </div>

    {{-- 4. POMODORO — row 2 col 7-9 --}}
    <div class="bento-1x1 bento-card p-5 flex flex-col items-center justify-center" x-data="pomodoroTimer()">
        <div class="relative mb-3">
            <svg width="80" height="80" viewBox="0 0 80 80" class="pomodoro-ring">
                <circle cx="40" cy="40" r="32" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="5"/>
                <circle cx="40" cy="40" r="32" fill="none"
                    :stroke="mode === 'work' ? '#6366f1' : '#10b981'"
                    stroke-width="5" stroke-linecap="round"
                    :stroke-dasharray="circumference"
                    :stroke-dashoffset="dashOffset"
                    style="transition:stroke-dashoffset 1s linear"/>
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-lg font-bold font-mono" style="color:var(--text-primary)" x-text="timeDisplay"></span>
                <span class="text-[10px]" :class="mode==='work'?'text-indigo-400':'text-emerald-400'" x-text="mode==='work'?'FOCUS':'BREAK'"></span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button @click="toggle()" class="btn-primary btn-sm px-4 text-xs" x-text="running ? 'Pause' : 'Focus'"></button>
            <button @click="reset()" class="btn-icon w-7 h-7" title="Reset">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 109-9M3 3v6h6"/></svg>
            </button>
        </div>
        <div class="text-[10px] mt-2" style="color:var(--text-muted)" x-text="'Session ' + sessions + ' · ' + Math.floor(totalMinutes) + 'min today'"></div>
    </div>

    {{-- 5. LIVE FLIP CLOCK — row 2 col 10-12 (under Goals Achieved, right of Pomodoro) --}}
    <div class="bento-1x1 bento-card flex flex-col items-center justify-center py-5 px-3"
         style="background:linear-gradient(135deg,rgba(99,102,241,0.10),rgba(139,92,246,0.07));border-color:rgba(99,102,241,0.2)">

        <p class="text-[9px] font-semibold uppercase tracking-[0.15em] mb-4" style="color:rgba(99,102,241,0.75)">Live Clock</p>

        <!-- Flip digits -->
        <div class="flip-clock" id="flipClock">
            {{-- HH --}}
            <div class="flip-digit-group">
                <div class="flip-card" id="fc-h1">
                    <div class="flip-upper"><span>0</span></div>
                    <div class="flip-lower"><span>0</span></div>
                    <div class="flip-flap"><span>0</span></div>
                </div>
                <div class="flip-card" id="fc-h2">
                    <div class="flip-upper"><span>0</span></div>
                    <div class="flip-lower"><span>0</span></div>
                    <div class="flip-flap"><span>0</span></div>
                </div>
            </div>
            <div class="flip-sep"><span></span><span></span></div>
            {{-- MM --}}
            <div class="flip-digit-group">
                <div class="flip-card" id="fc-m1">
                    <div class="flip-upper"><span>0</span></div>
                    <div class="flip-lower"><span>0</span></div>
                    <div class="flip-flap"><span>0</span></div>
                </div>
                <div class="flip-card" id="fc-m2">
                    <div class="flip-upper"><span>0</span></div>
                    <div class="flip-lower"><span>0</span></div>
                    <div class="flip-flap"><span>0</span></div>
                </div>
            </div>
            <div class="flip-sep"><span></span><span></span></div>
            {{-- SS --}}
            <div class="flip-digit-group">
                <div class="flip-card" id="fc-s1">
                    <div class="flip-upper"><span>0</span></div>
                    <div class="flip-lower"><span>0</span></div>
                    <div class="flip-flap"><span>0</span></div>
                </div>
                <div class="flip-card" id="fc-s2">
                    <div class="flip-upper"><span>0</span></div>
                    <div class="flip-lower"><span>0</span></div>
                    <div class="flip-flap"><span>0</span></div>
                </div>
            </div>
        </div>

        <!-- Labels -->
        <div class="flex items-center mt-3" style="gap:33px;margin-left:4px">
            <span class="flip-label">HH</span>
            <span class="flip-label">MM</span>
            <span class="flip-label">SS</span>
        </div>

        <!-- Date badge -->
        <div class="mt-4 px-3 py-1 rounded-full text-[9px] font-semibold" style="background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.22);color:rgba(139,92,246,0.95)">
            {{ now()->format('D, M j') }}
        </div>
    </div>

    {{-- 6. ACTIVE GOALS — row 3 col 1-6 --}}
    <div class="bento-2x1 bento-card p-5 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold" style="color:var(--text-primary)">Active Goals</h3>
            <a href="{{ route('goals.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">View all →</a>
        </div>
        <div class="space-y-2.5 flex-1 overflow-hidden">
            @forelse($recentGoals->take(4) as $goal)
            <a href="{{ route('goals.show', $goal) }}" class="flex items-center gap-3 group hover:opacity-80 transition-opacity">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                     style="background:{{ $goal->category?->color ?? '#6366f1' }}20;border:1px solid {{ $goal->category?->color ?? '#6366f1' }}30">
                    {{ $goal->category?->icon ?? '🎯' }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium truncate mb-1" style="color:var(--text-primary)">{{ $goal->title }}</div>
                    <div class="progress-track" style="height:3px">
                        <div class="progress-fill" style="width:{{ $goal->progress }}%;height:100%"></div>
                    </div>
                </div>
                <span class="text-sm font-semibold flex-shrink-0" style="color:var(--text-muted)">{{ $goal->progress }}%</span>
            </a>
            @empty
            <div class="flex-1 flex items-center justify-center text-center py-4">
                <div>
                    <div class="text-3xl mb-2">🎯</div>
                    <p class="text-sm mb-3" style="color:var(--text-muted)">No active goals</p>
                    <a href="{{ route('goals.create') }}" class="btn-primary btn-sm">Create goal</a>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- 7. AI SUGGESTIONS — row 3 col 7-9 --}}
    <div class="bento-1x1 bento-card p-5 flex flex-col" style="background:linear-gradient(135deg,rgba(99,102,241,0.08),rgba(139,92,246,0.05))">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-sm" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">✦</div>
            <span class="text-sm font-semibold" style="color:var(--text-primary)">AI Suggestions</span>
        </div>
        <div class="space-y-2 flex-1">
            @foreach([
                ['💡','Break your top goal into weekly milestones'],
                ['🎯','You\'re 3 tasks away from a milestone!'],
                ['📅','Schedule 2hrs tomorrow for your project'],
            ] as $s)
            <div class="flex items-start gap-2.5 p-2.5 rounded-xl cursor-pointer hover:opacity-75 transition-opacity" style="background:var(--surface-1);border:1px solid var(--glass-border)">
                <span class="text-base flex-shrink-0 mt-0.5">{{ $s[0] }}</span>
                <p class="text-xs leading-relaxed" style="color:var(--text-secondary)">{{ $s[1] }}</p>
            </div>
            @endforeach
        </div>
        <button onclick="document.querySelector('.ai-orb')?.click()" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors mt-3 text-center w-full">Ask GoalFlow AI →</button>
    </div>

    {{-- 8. STATS — row 3 col 10-12 --}}
    <div class="bento-1x1 bento-card p-5 flex flex-col justify-between" style="background:linear-gradient(135deg,rgba(139,92,246,0.10),rgba(6,182,212,0.06));border-color:rgba(139,92,246,0.2)">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-2" style="background:rgba(139,92,246,0.2)">
            <svg class="w-5 h-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div>
            <div class="text-4xl font-black mb-0.5 counter gradient-text" data-target="{{ $activeGoals }}">0</div>
            <div class="text-sm font-medium" style="color:rgba(139,92,246,0.9)">in progress</div>
            <div class="text-xs mt-1" style="color:var(--text-muted)">{{ round($avgProgress) }}% avg completion</div>
        </div>
    </div>

    {{-- 9. WEEKLY ACTIVITY — row 4 col 1-6 --}}
    <div class="bento-2x1 bento-card p-5 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold" style="color:var(--text-primary)">Weekly Activity</h3>
            <span class="badge badge-active text-xs">Last 7 days</span>
        </div>
        <div class="flex-1 relative" style="min-height:100px">
            <canvas id="weeklyChart"></canvas>
        </div>
    </div>

    {{-- 10. DUE SOON — row 4 col 7-9 --}}
    <div class="bento-1x1 bento-card p-5 flex flex-col">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="text-sm font-semibold" style="color:var(--text-primary)">Due Soon</h3>
        </div>
        <div class="space-y-2.5 flex-1">
            @forelse($upcomingDeadlines->take(4) as $goal)
            <a href="{{ route('goals.show', $goal) }}" class="flex items-center gap-2.5 group">
                <div class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $goal->priority_color }}"></div>
                <span class="text-sm flex-1 truncate group-hover:opacity-70 transition-opacity" style="color:var(--text-secondary)">{{ $goal->title }}</span>
                <span class="text-xs flex-shrink-0 font-medium" style="color:{{ $goal->days_left <= 2 ? '#f43f5e' : 'var(--text-muted)' }}">
                    @if($goal->days_left === 0) Today
                    @elseif($goal->days_left === 1) Tomorrow
                    @else {{ $goal->days_left }}d
                    @endif
                </span>
            </a>
            @empty
            <div class="text-center py-4 text-sm" style="color:var(--text-muted)">No upcoming deadlines 🎉</div>
            @endforelse
        </div>
    </div>

    {{-- 11. RECENT WINS — row 4 col 10-12 --}}
    <div class="bento-1x1 bento-card p-5 flex flex-col">
        <div class="flex items-center gap-2 mb-4">
            <span class="text-base">🏆</span>
            <h3 class="text-sm font-semibold" style="color:var(--text-primary)">Recent Wins</h3>
        </div>
        <div class="space-y-3 flex-1">
            @forelse($recentMilestones->take(4) as $m)
            <div class="flex items-start gap-2.5">
                <div class="w-6 h-6 rounded-lg bg-emerald-500/15 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium truncate" style="color:var(--text-secondary)">{{ $m->title }}</p>
                    <p class="text-[10px] mt-0.5" style="color:var(--text-muted)">{{ $m->completed_at?->diffForHumans() ?? 'Pending' }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-center py-3" style="color:var(--text-muted)">Complete milestones to see wins</p>
            @endforelse
        </div>
    </div>

</div>

<script>
// ── Counters
document.querySelectorAll('.counter').forEach(el => {
    const target = parseInt(el.dataset.target) || 0;
    if (!target) { el.textContent = '0'; return; }
    let start = null;
    const dur = 900;
    (function step(ts) {
        if (!start) start = ts;
        const p = Math.min((ts - start) / dur, 1);
        const e = 1 - Math.pow(1 - p, 4);
        el.textContent = Math.floor(e * target);
        if (p < 1) requestAnimationFrame(step);
        else el.textContent = target;
    })(performance.now());
});

// ── Progress bars animate in
document.querySelectorAll('.progress-fill').forEach(bar => {
    const w = bar.style.width;
    bar.style.width = '0%';
    requestAnimationFrame(() => setTimeout(() => bar.style.width = w, 80));
});

// ── Weekly chart
const ctx = document.getElementById('weeklyChart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json(array_column($weeklyData, 'day')),
            datasets: [{
                data: @json(array_column($weeklyData, 'tasks_completed')),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.08)',
                borderWidth: 2,
                pointBackgroundColor: '#6366f1',
                pointRadius: 4, pointHoverRadius: 6,
                fill: true, tension: 0.45,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor:'rgba(10,15,30,0.95)', borderColor:'rgba(255,255,255,0.08)',
                    borderWidth:1, titleColor:'rgba(255,255,255,0.9)', bodyColor:'rgba(255,255,255,0.55)',
                    padding:10, cornerRadius:12,
                }
            },
            scales: {
                x: { grid:{color:'rgba(255,255,255,0.04)'}, ticks:{color:'rgba(255,255,255,0.35)',font:{size:11}}, border:{display:false} },
                y: { grid:{color:'rgba(255,255,255,0.04)'}, ticks:{color:'rgba(255,255,255,0.35)',font:{size:11},stepSize:1}, border:{display:false}, beginAtZero:true }
            }
        }
    });
}

// ── Pomodoro
function pomodoroTimer() {
    const WORK = 25*60, BREAK = 5*60;
    return {
        running: false, mode: 'work', timeLeft: WORK,
        sessions: 0, totalMinutes: 0, _t: null,
        circumference: 2 * Math.PI * 32,
        get dashOffset() { return this.circumference * (1 - this.timeLeft / (this.mode==='work'?WORK:BREAK)); },
        get timeDisplay() { return String(Math.floor(this.timeLeft/60)).padStart(2,'0') + ':' + String(this.timeLeft%60).padStart(2,'0'); },
        toggle() {
            this.running = !this.running;
            if (this.running) {
                this._t = setInterval(() => {
                    if (this.timeLeft > 0) { this.timeLeft--; if(this.mode==='work') this.totalMinutes+=1/60; }
                    else {
                        clearInterval(this._t); this.running = false;
                        if(this.mode==='work'){this.sessions++;this.mode='break';this.timeLeft=BREAK;}
                        else{this.mode='work';this.timeLeft=WORK;}
                    }
                }, 1000);
            } else clearInterval(this._t);
        },
        reset() { clearInterval(this._t); this.running=false; this.mode='work'; this.timeLeft=WORK; }
    };
}

// ── Misc keyframes
const _ks = document.createElement('style');
_ks.textContent = `@keyframes levitate{0%,100%{transform:translateY(0) rotate(-5deg)}50%{transform:translateY(-6px) rotate(5deg)}} @keyframes pulse{0%,100%{opacity:1}50%{opacity:0.4}}`;
document.head.appendChild(_ks);

/* ══ FLIP CLOCK ENGINE ═══════════════════════════════════════
   Uses position:absolute + height:200% clipping (no line-height trick).
   Only flips when the digit actually changes.
   ═══════════════════════════════════════════════════════════ */
(function flipClock() {
    const IDS = ['fc-h1','fc-h2','fc-m1','fc-m2','fc-s1','fc-s2'];
    const cards = {};

    IDS.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        cards[id] = {
            el,
            upper:  el.querySelector('.flip-upper span'),
            lower:  el.querySelector('.flip-lower span'),
            flap:   el.querySelector('.flip-flap span'),
            cur:    null,
        };
    });

    function setDigit(c, digit) {
        const d = String(digit);
        if (c.cur === d) return;          // nothing changed — no flip
        const prev = c.cur ?? d;
        c.cur = d;

        // Upper holds the old digit (visible top half)
        c.upper.textContent = prev;
        // Flap also shows old digit, will rotate away
        c.flap.textContent = prev;
        // Lower already shows the incoming digit (bottom half)
        c.lower.textContent = d;

        // Restart animation
        c.el.classList.remove('flipping');
        void c.el.offsetWidth;            // force reflow
        c.el.classList.add('flipping');

        // After flap exits, snap upper to new digit silently
        setTimeout(() => {
            c.upper.textContent = d;
            c.el.classList.remove('flipping');
        }, 290);
    }

    function tick() {
        const now = new Date();
        const hh = String(now.getHours()).padStart(2,'0');
        const mm = String(now.getMinutes()).padStart(2,'0');
        const ss = String(now.getSeconds()).padStart(2,'0');
        [hh[0],hh[1],mm[0],mm[1],ss[0],ss[1]].forEach((d, i) => {
            const c = cards[IDS[i]];
            if (c) setDigit(c, d);
        });
    }

    tick();
    setInterval(tick, 1000);
}());
</script>
</x-layouts.app>
