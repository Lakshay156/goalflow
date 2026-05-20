<x-layouts.app>
<x-slot:title>{{ $goal->title }}</x-slot:title>

{{-- ── BACK + ACTIONS ────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-5">
    <a href="{{ route('goals.index') }}" class="flex items-center gap-2 text-sm transition-all hover:opacity-70" style="color:var(--text-muted)">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
        Goals
    </a>
    <div class="flex items-center gap-2">
        @if($goal->status !== 'completed')
        <form method="POST" action="{{ route('goals.update', $goal) }}">
            @csrf @method('PUT')
            <input type="hidden" name="title" value="{{ $goal->title }}">
            <input type="hidden" name="priority" value="{{ $goal->priority }}">
            <input type="hidden" name="status" value="completed">
            <input type="hidden" name="progress" value="100">
            <button class="btn-glass btn-sm text-emerald-400" onclick="return confirm('Mark as complete?')">✅ Complete</button>
        </form>
        @endif
        <a href="{{ route('goals.edit', $goal) }}" class="btn-glass btn-sm">Edit</a>
        <form method="POST" action="{{ route('goals.destroy', $goal) }}" onsubmit="return confirm('Delete this goal?')">
            @csrf @method('DELETE')
            <button class="btn-danger btn-sm">Delete</button>
        </form>
    </div>
</div>

{{-- ── HERO HEADER ───────────────────────────────────────── --}}
<div class="glass-card-static rounded-2xl p-6 mb-5 relative overflow-hidden">
    {{-- Ambient glow behind --}}
    <div class="absolute top-0 right-0 w-64 h-64 rounded-full pointer-events-none" style="background:radial-gradient(circle, {{ $goal->priority_color }}18, transparent 70%);transform:translate(25%,-25%)"></div>

    <div class="relative z-10 flex flex-col sm:flex-row sm:items-start gap-4">
        {{-- Icon --}}
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl flex-shrink-0"
             style="background:{{ $goal->category?->color ?? '#6366f1' }}20;border:1px solid {{ $goal->category?->color ?? '#6366f1' }}35">
            {{ $goal->category?->icon ?? '🎯' }}
        </div>

        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="badge badge-{{ $goal->priority }}">{{ $goal->priority }}</span>
                <span class="badge badge-{{ $goal->status }}">{{ $goal->status }}</span>
                @if($goal->category)<span class="badge badge-violet">{{ $goal->category->name }}</span>@endif
                @if($goal->deadline)
                <span class="badge {{ $goal->isOverdue() ? 'badge-critical' : 'badge-medium' }}">
                    📅 {{ $goal->deadline->format('M d, Y') }}
                    @if(!$goal->isOverdue() && $goal->days_left !== null)
                    · {{ $goal->days_left === 0 ? 'Due today' : $goal->days_left . 'd left' }}
                    @elseif($goal->isOverdue())
                    · Overdue
                    @endif
                </span>
                @endif
            </div>
            <h1 class="text-2xl font-bold tracking-tight mb-2" style="color:var(--text-primary)">{{ $goal->title }}</h1>
            @if($goal->description)
            <p class="text-sm leading-relaxed" style="color:var(--text-secondary)">{{ $goal->description }}</p>
            @endif
        </div>

        {{-- Progress Ring --}}
        <div class="flex-shrink-0 text-center">
            <div class="relative inline-flex">
                <svg width="96" height="96" viewBox="0 0 96 96" class="-rotate-90">
                    <circle cx="48" cy="48" r="38" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="7"/>
                    <circle cx="48" cy="48" r="38" fill="none" stroke="{{ $goal->priority_color }}" stroke-width="7"
                        stroke-linecap="round"
                        stroke-dasharray="{{ 2 * pi() * 38 }}"
                        stroke-dashoffset="{{ 2 * pi() * 38 * (1 - $goal->progress / 100) }}"
                        style="filter:drop-shadow(0 0 8px {{ $goal->priority_color }}66);transition:stroke-dashoffset 1.6s cubic-bezier(0.16,1,0.3,1)"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-black" style="color:var(--text-primary)">{{ $goal->progress }}%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress Bar --}}
    <div class="mt-5 relative z-10">
        <div class="progress-track" style="height:6px">
            <div class="progress-fill" style="width:{{ $goal->progress }}%;background:{{ $goal->priority_color }};box-shadow:0 0 10px {{ $goal->priority_color }}55;height:100%"></div>
        </div>
        <div class="flex justify-between text-xs mt-1.5" style="color:var(--text-muted)">
            <span>{{ $goal->completedTasks()->count() }}/{{ $goal->tasks->count() }} tasks · {{ $goal->completedMilestones()->count() }}/{{ $goal->milestones->count() }} milestones</span>
            <span>Started {{ $goal->created_at->format('M d') }}</span>
        </div>
    </div>
</div>

{{-- ── MAIN GRID ─────────────────────────────────────────── --}}
<div class="grid lg:grid-cols-3 gap-5">

    {{-- LEFT: Tasks + Milestones --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- TASKS --}}
        <div class="glass-card-static rounded-2xl overflow-hidden" x-data="{ showAdd: false }">
            <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--glass-border)">
                <div class="flex items-center gap-2.5">
                    <h2 class="font-semibold" style="color:var(--text-primary)">Tasks</h2>
                    <span class="badge badge-active text-[10px]">{{ $goal->tasks->where('is_completed', false)->count() }} pending</span>
                </div>
                <button @click="showAdd = !showAdd" class="btn-glass btn-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                    Add
                </button>
            </div>

            {{-- Add Task Form --}}
            <div x-show="showAdd" x-cloak x-transition class="p-4 border-b" style="border-color:var(--glass-border);background:var(--surface-1)">
                <form method="POST" action="{{ route('tasks.store', $goal) }}" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="text" name="title" required placeholder="What needs to be done?" class="glass-input flex-1 px-3.5 py-2.5 text-sm">
                    <input type="date" name="due_date" class="glass-input px-3.5 py-2.5 text-sm">
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary btn-sm px-5">Add</button>
                        <button type="button" @click="showAdd = false" class="btn-glass btn-sm">Cancel</button>
                    </div>
                </form>
            </div>

            {{-- Task List --}}
            @if($goal->tasks->isEmpty())
            <div class="py-10 text-center">
                <div class="text-3xl mb-2">📋</div>
                <p class="text-sm" style="color:var(--text-muted)">Break this goal into smaller steps</p>
            </div>
            @else
            <div class="divide-y" style="--tw-divide-opacity:1;border-color:var(--glass-border)">
                {{-- Pending Tasks --}}
                @foreach($goal->tasks->where('is_completed', false) as $task)
                <div class="flex items-center gap-3.5 px-5 py-3 transition-all group hover:bg-white/[0.02]">
                    <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="flex-shrink-0">
                        @csrf @method('PATCH')
                        <button type="submit"><input type="checkbox" class="ios-checkbox pointer-events-none"></button>
                    </form>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate" style="color:var(--text-primary)">{{ $task->title }}</p>
                        @if($task->due_date)
                        <p class="text-xs mt-0.5 {{ $task->due_date->isPast() ? 'text-red-400' : '' }}" style="{{ $task->due_date->isPast() ? '' : 'color:var(--text-muted)' }}">
                            Due {{ $task->due_date->format('M d') }}
                        </p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="opacity-0 group-hover:opacity-100 transition-opacity">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-icon w-7 h-7 text-red-400/50 hover:text-red-400">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        </button>
                    </form>
                </div>
                @endforeach

                {{-- Completed Tasks --}}
                @if($goal->tasks->where('is_completed', true)->count() > 0)
                <div x-data="{ showDone: false }">
                    <button @click="showDone = !showDone" class="w-full flex items-center gap-2 px-5 py-3 text-xs transition-all" style="color:var(--text-muted)" onmouseover="this.style.background='var(--surface-1)'" onmouseout="this.style.background='transparent'">
                        <svg class="w-3.5 h-3.5 transition-transform" :class="showDone ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
                        <span x-text="showDone ? 'Hide' : 'Show'"></span> {{ $goal->tasks->where('is_completed', true)->count() }} completed
                    </button>
                    <div x-show="showDone" x-cloak x-transition>
                        @foreach($goal->tasks->where('is_completed', true) as $task)
                        <div class="flex items-center gap-3.5 px-5 py-3 opacity-50">
                            <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="flex-shrink-0">
                                @csrf @method('PATCH')
                                <button type="submit"><input type="checkbox" checked class="ios-checkbox pointer-events-none"></button>
                            </form>
                            <span class="text-sm line-through flex-1 truncate" style="color:var(--text-muted)">{{ $task->title }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- MILESTONES TIMELINE --}}
        <div class="glass-card-static rounded-2xl overflow-hidden" x-data="{ showAdd: false }">
            <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--glass-border)">
                <div class="flex items-center gap-2.5">
                    <h2 class="font-semibold" style="color:var(--text-primary)">Milestones</h2>
                    <span class="badge badge-completed text-[10px]">{{ $goal->completedMilestones()->count() }}/{{ $goal->milestones->count() }}</span>
                </div>
                <button @click="showAdd = !showAdd" class="btn-glass btn-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                    Add
                </button>
            </div>

            {{-- Add Milestone Form --}}
            <div x-show="showAdd" x-cloak x-transition class="p-4 border-b" style="border-color:var(--glass-border);background:var(--surface-1)">
                <form method="POST" action="{{ route('milestones.store', $goal) }}" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="text" name="title" required placeholder="Milestone title..." class="glass-input flex-1 px-3.5 py-2.5 text-sm">
                    <input type="date" name="target_date" class="glass-input px-3.5 py-2.5 text-sm">
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary btn-sm px-5">Add</button>
                        <button type="button" @click="showAdd = false" class="btn-glass btn-sm">Cancel</button>
                    </div>
                </form>
            </div>

            {{-- Milestones Timeline --}}
            @if($goal->milestones->isEmpty())
            <div class="py-10 text-center">
                <div class="text-3xl mb-2">🏁</div>
                <p class="text-sm" style="color:var(--text-muted)">Add checkpoints to track key achievements</p>
            </div>
            @else
            <div class="p-5 space-y-3 relative">
                {{-- Vertical line --}}
                <div class="absolute left-8 top-5 bottom-5 w-px" style="background:linear-gradient(to bottom, var(--glass-border), transparent)"></div>

                @foreach($goal->milestones as $ms)
                <div class="flex items-start gap-4 group relative pl-1">
                    {{-- Dot --}}
                    <div class="w-5 h-5 rounded-full flex-shrink-0 mt-0.5 flex items-center justify-center z-10 transition-transform group-hover:scale-110"
                         style="{{ $ms->is_completed ? 'background:#10b981;border:2px solid #10b981' : 'background:var(--bg-elevated);border:2px solid var(--glass-border-md)' }}">
                        @if($ms->is_completed)
                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </div>
                    {{-- Content --}}
                    <div class="flex-1 p-3.5 rounded-xl transition-all {{ $ms->is_completed ? 'opacity-70' : '' }}"
                         style="background:var(--surface-1);border:1px solid var(--glass-border)">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-medium {{ $ms->is_completed ? 'line-through' : '' }}" style="color:var(--text-primary)">{{ $ms->title }}</p>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form method="POST" action="{{ route('milestones.toggle', $ms) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs px-2.5 py-1 rounded-lg transition-all" style="color:var(--text-muted)" onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background='transparent'">
                                        {{ $ms->is_completed ? 'Reopen' : 'Complete' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('milestones.destroy', $ms) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon w-6 h-6 text-red-400/50 hover:text-red-400">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mt-1 text-xs" style="color:var(--text-muted)">
                            @if($ms->target_date)<span>🗓 {{ $ms->target_date->format('M d, Y') }}</span>@endif
                            @if($ms->is_completed && $ms->completed_at)<span class="text-emerald-400/70">✓ {{ $ms->completed_at->diffForHumans() }}</span>@endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="space-y-5">

        {{-- Quick Stats --}}
        <div class="glass-card-static rounded-2xl p-5">
            <h3 class="text-sm font-semibold mb-4" style="color:var(--text-primary)">Goal Stats</h3>
            <div class="space-y-3">
                @foreach([
                    ['Created', $goal->created_at->format('M d, Y'), '📅'],
                    ['Status', ucfirst($goal->status), '📌'],
                    ['Priority', ucfirst($goal->priority), '⚡'],
                    ['Tasks Done', $goal->completedTasks()->count() . '/' . $goal->tasks->count(), '✅'],
                    ['Milestones', $goal->completedMilestones()->count() . '/' . $goal->milestones->count(), '🏆'],
                ] as [$label, $val, $icon])
                <div class="flex items-center justify-between py-2 border-b last:border-0" style="border-color:var(--glass-border)">
                    <div class="flex items-center gap-2 text-sm" style="color:var(--text-muted)">
                        <span>{{ $icon }}</span>
                        <span>{{ $label }}</span>
                    </div>
                    <span class="text-sm font-medium" style="color:var(--text-primary)">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Progress Update Slider --}}
        @if($goal->status !== 'completed')
        <div class="glass-card-static rounded-2xl p-5" x-data="{ p: {{ $goal->progress }} }">
            <h3 class="text-sm font-semibold mb-3" style="color:var(--text-primary)">Update Progress</h3>
            <div class="mb-3 text-center">
                <span class="text-3xl font-black gradient-text" x-text="p + '%'"></span>
            </div>
            <input type="range" x-model="p" min="0" max="100" class="w-full mb-4" style="accent-color:#6366f1">
            <form method="POST" action="{{ route('goals.update', $goal) }}">
                @csrf @method('PUT')
                <input type="hidden" name="title" value="{{ $goal->title }}">
                <input type="hidden" name="priority" value="{{ $goal->priority }}">
                <input type="hidden" name="status" value="{{ $goal->status }}">
                <input type="hidden" name="progress" :value="p">
                <button type="submit" class="btn-primary btn-sm w-full justify-center">Save Progress</button>
            </form>
        </div>
        @endif

        {{-- Change Status --}}
        <div class="glass-card-static rounded-2xl p-5">
            <h3 class="text-sm font-semibold mb-3" style="color:var(--text-primary)">Change Status</h3>
            <form method="POST" action="{{ route('goals.update', $goal) }}" class="space-y-2">
                @csrf @method('PUT')
                <input type="hidden" name="title" value="{{ $goal->title }}">
                <input type="hidden" name="priority" value="{{ $goal->priority }}">
                <input type="hidden" name="progress" value="{{ $goal->progress }}">
                <select name="status" class="glass-input w-full px-3.5 py-2.5 text-sm mb-2">
                    @foreach(['active','paused','completed','archived'] as $s)
                    <option value="{{ $s }}" {{ $goal->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-glass btn-sm w-full justify-center">Update</button>
            </form>
        </div>
    </div>
</div>

<script>
// Animate progress bar on load
document.querySelectorAll('.progress-fill').forEach(bar => {
    const w = bar.style.width; bar.style.width = '0%';
    requestAnimationFrame(() => setTimeout(() => bar.style.width = w, 80));
});
</script>
</x-layouts.app>
