<x-layouts.app>
<x-slot:title>Goals</x-slot:title>
<x-slot:subtitle>{{ $stats['active'] }} active · {{ $stats['completed'] }} completed</x-slot:subtitle>

{{-- ── HEADER + FILTERS ──────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row gap-4 mb-6">
    <form method="GET" action="{{ route('goals.index') }}" class="flex flex-wrap gap-2 flex-1" id="filter-form">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search goals..."
               class="glass-input px-4 py-2.5 text-sm flex-1 min-w-[160px]"
               onchange="this.form.submit()">

        @foreach([
            ['status',   ['active','completed','paused','archived']],
            ['priority', ['low','medium','high','critical']],
        ] as [$name, $opts])
        <select name="{{ $name }}" class="glass-input px-3 py-2.5 text-sm" onchange="this.form.submit()">
            <option value="">{{ ucfirst($name) }}</option>
            @foreach($opts as $o)
            <option value="{{ $o }}" {{ request($name) === $o ? 'selected' : '' }}>{{ ucfirst($o) }}</option>
            @endforeach
        </select>
        @endforeach

        <select name="category" class="glass-input px-3 py-2.5 text-sm" onchange="this.form.submit()">
            <option value="">Category</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
            @endforeach
        </select>

        @if(request()->hasAny(['search','status','priority','category']))
        <a href="{{ route('goals.index') }}" class="btn-glass btn-sm px-4">✕ Clear</a>
        @endif
    </form>

    <a href="{{ route('goals.create') }}" class="btn-primary flex-shrink-0">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
        New Goal
    </a>
</div>

{{-- ── QUICK STATS ROW ───────────────────────────────────── --}}
<div class="grid grid-cols-4 gap-3 mb-6">
    @foreach([
        ['All', $stats['total'],     'text-primary-color',  'var(--glass-border)'],
        ['Active', $stats['active'], 'text-[#818cf8]',      'rgba(99,102,241,0.3)'],
        ['Done', $stats['completed'],'text-emerald-400',    'rgba(16,185,129,0.3)'],
        ['Paused', $stats['paused'], 'text-amber-400',      'rgba(245,158,11,0.2)'],
    ] as [$l, $n, $cls, $border])
    <div class="glass-card-static rounded-xl p-3 text-center" style="border-color:{{ $border }}">
        <div class="text-2xl font-black {{ $cls }}">{{ $n }}</div>
        <div class="text-xs mt-0.5" style="color:var(--text-muted)">{{ $l }}</div>
    </div>
    @endforeach
</div>

{{-- ── GOALS GRID ────────────────────────────────────────── --}}
@if($goals->isEmpty())
<div class="flex flex-col items-center justify-center py-24 text-center">
    <div class="text-6xl mb-4 animate-bounce-sm">🎯</div>
    <h3 class="text-xl font-bold mb-2" style="color:var(--text-primary)">
        {{ request()->hasAny(['search','status','priority','category']) ? 'No goals found' : 'Set your first goal' }}
    </h3>
    <p class="text-sm mb-6 max-w-xs" style="color:var(--text-muted)">
        {{ request()->hasAny(['search','status','priority','category']) ? 'Try adjusting your filters.' : 'Every achievement starts with a single goal. Make it count.' }}
    </p>
    <a href="{{ route('goals.create') }}" class="btn-primary">Create Goal</a>
</div>
@else
<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4 stagger">
    @foreach($goals as $goal)
    <div class="goal-card group" style="--card-accent: {{ $goal->priority_color }}">
        {{-- Accent left border --}}
        <div class="absolute left-0 top-0 bottom-0 w-0.5 rounded-l-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"
             style="background:linear-gradient(180deg, {{ $goal->priority_color }}, transparent)"></div>

        <div class="p-5">
            {{-- Top Row --}}
            <div class="flex items-start justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl flex-shrink-0"
                         style="background:{{ $goal->category?->color ?? '#6366f1' }}18;border:1px solid {{ $goal->category?->color ?? '#6366f1' }}30">
                        {{ $goal->category?->icon ?? '🎯' }}
                    </div>
                    <div>
                        <span class="badge badge-{{ $goal->status }} text-[10px]">{{ $goal->status }}</span>
                    </div>
                </div>
                <span class="badge badge-{{ $goal->priority }} text-[10px] flex-shrink-0">{{ $goal->priority }}</span>
            </div>

            {{-- Title --}}
            <h3 class="font-semibold text-base mb-1 line-clamp-1" style="color:var(--text-primary)">{{ $goal->title }}</h3>

            {{-- Emotional progress description --}}
            @php
                $p = $goal->progress;
                $mood = match(true) {
                    $p >= 100 => ['🏆 Achieved!', 'text-emerald-400'],
                    $p >= 80  => ['🔥 Almost there!', 'text-amber-400'],
                    $p >= 50  => ['⚡ Great momentum!', 'text-indigo-400'],
                    $p >= 20  => ['🌱 Building up...', 'text-cyan-400'],
                    default   => ['✨ Just started', 'text-violet-400'],
                };
            @endphp
            <p class="text-xs font-medium mb-3 {{ $mood[1] }}">{{ $mood[0] }}</p>

            {{-- Progress --}}
            <div class="mb-4">
                <div class="flex justify-between text-xs mb-1.5">
                    <span style="color:var(--text-muted)">Progress</span>
                    <span class="font-semibold" style="color:var(--text-primary)">{{ $goal->progress }}%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="width:{{ $goal->progress }}%"></div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 text-xs" style="color:var(--text-muted)">
                    <span>{{ $goal->tasks->count() }} tasks</span>
                    <span>{{ $goal->milestones->count() }} milestones</span>
                </div>
                @if($goal->deadline)
                <span class="text-xs {{ $goal->isOverdue() ? 'text-red-400' : '' }}" style="{{ $goal->isOverdue() ? '' : 'color:var(--text-muted)' }}">
                    {{ $goal->deadline->format('M d') }}
                    @if($goal->days_left !== null && !$goal->isOverdue() && $goal->days_left <= 7)
                    <span class="text-amber-400">· {{ $goal->days_left }}d</span>
                    @endif
                </span>
                @endif
            </div>
        </div>

        {{-- Hover quick actions --}}
        <div class="absolute inset-x-0 bottom-0 opacity-0 group-hover:opacity-100 transition-all duration-200 translate-y-1 group-hover:translate-y-0">
            <div class="flex border-t" style="border-color:var(--glass-border)">
                <a href="{{ route('goals.show', $goal) }}" class="flex-1 py-2.5 text-xs font-medium text-center transition-all" style="color:var(--text-secondary)" onmouseover="this.style.background='var(--surface-1)'" onmouseout="this.style.background='transparent'">View</a>
                <div class="w-px" style="background:var(--glass-border)"></div>
                <a href="{{ route('goals.edit', $goal) }}" class="flex-1 py-2.5 text-xs font-medium text-center transition-all" style="color:var(--text-secondary)" onmouseover="this.style.background='var(--surface-1)'" onmouseout="this.style.background='transparent'">Edit</a>
                <div class="w-px" style="background:var(--glass-border)"></div>
                <form method="POST" action="{{ route('goals.destroy', $goal) }}" onsubmit="return confirm('Delete this goal?')" class="flex-1">
                    @csrf @method('DELETE')
                    <button class="w-full py-2.5 text-xs font-medium text-red-400 transition-all" onmouseover="this.style.background='rgba(244,63,94,0.08)'" onmouseout="this.style.background='transparent'">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-8">{{ $goals->links() }}</div>
@endif

<script>
document.querySelectorAll('.progress-fill').forEach(bar => {
    const w = bar.style.width;
    bar.style.width = '0%';
    requestAnimationFrame(() => setTimeout(() => bar.style.width = w, 60));
});
</script>
</x-layouts.app>
