<x-layouts.app>
<x-slot:title>Tasks</x-slot:title>

<div class="flex items-center justify-between mb-8 animate-fade-in-up">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">All Tasks</h2>
        <p class="text-white/50 text-sm mt-1">{{ $tasks->count() }} pending · {{ $completedTasks->count() }} completed</p>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <!-- Pending Tasks -->
    <div class="glass-card-static rounded-2xl p-6 animate-fade-in-up">
        <h3 class="text-white font-semibold mb-5 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
            Pending Tasks
            <span class="badge badge-active ml-1">{{ $tasks->count() }}</span>
        </h3>
        @if($tasks->isEmpty())
        <div class="text-center py-10">
            <div class="text-4xl mb-3">✅</div>
            <p class="text-white/50 text-sm">All caught up! No pending tasks.</p>
        </div>
        @else
        <div class="space-y-2">
            @foreach($tasks as $task)
            <div class="flex items-center gap-3 p-3 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] border border-white/[0.05] transition-all group">
                <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="flex-shrink-0">
                    @csrf @method('PATCH')
                    <button type="submit"><input type="checkbox" class="ios-checkbox pointer-events-none"></button>
                </form>
                <div class="flex-1 min-w-0">
                    <div class="text-sm text-white/80">{{ $task->title }}</div>
                    <a href="{{ route('goals.show', $task->goal) }}" class="text-xs text-indigo-400/70 hover:text-indigo-400 transition-colors">{{ $task->goal->title }}</a>
                    @if($task->due_date)
                    <div class="text-xs text-white/30 mt-0.5 {{ $task->due_date->isPast() ? 'text-red-400/70' : '' }}">
                        Due {{ $task->due_date->format('M d') }}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Completed Tasks -->
    <div class="glass-card-static rounded-2xl p-6 animate-fade-in-up" style="animation-delay:0.1s;animation-fill-mode:both">
        <h3 class="text-white font-semibold mb-5 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            Completed
            <span class="badge badge-completed ml-1">{{ $completedTasks->count() }}</span>
        </h3>
        @if($completedTasks->isEmpty())
        <div class="text-center py-10">
            <p class="text-white/40 text-sm">No completed tasks yet.</p>
        </div>
        @else
        <div class="space-y-2">
            @foreach($completedTasks as $task)
            <div class="flex items-center gap-3 p-3 rounded-xl bg-white/[0.02] border border-white/[0.04] opacity-70 group">
                <form method="POST" action="{{ route('tasks.toggle', $task) }}">
                    @csrf @method('PATCH')
                    <button type="submit"><input type="checkbox" checked class="ios-checkbox pointer-events-none"></button>
                </form>
                <div class="flex-1 min-w-0">
                    <div class="text-sm text-white/50 line-through">{{ $task->title }}</div>
                    <div class="text-xs text-white/25">{{ $task->goal->title }}</div>
                    @if($task->completed_at)
                    <div class="text-xs text-emerald-400/50 mt-0.5">Done {{ $task->completed_at->diffForHumans() }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
</x-layouts.app>
