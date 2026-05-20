<x-layouts.app>
<x-slot:title>Milestones</x-slot:title>

<div class="mb-8 animate-fade-in-up">
    <h2 class="text-2xl font-bold text-white tracking-tight">Milestones</h2>
    <p class="text-white/50 text-sm mt-1">Your key achievements and upcoming targets</p>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <!-- Pending Milestones -->
    <div class="glass-card-static rounded-2xl p-6 animate-fade-in-up">
        <h3 class="text-white font-semibold mb-5 flex items-center gap-2">
            <span class="text-lg">⭕</span> Upcoming
            <span class="badge badge-active">{{ $milestones->get('pending', collect())->count() }}</span>
        </h3>
        @if($milestones->get('pending', collect())->isEmpty())
        <div class="text-center py-10 text-white/40 text-sm">No pending milestones</div>
        @else
        <div class="space-y-3">
            @foreach($milestones->get('pending', collect()) as $milestone)
            <div class="flex items-start gap-4 p-4 rounded-xl bg-white/[0.04] border border-white/[0.06] hover:bg-white/[0.07] transition-all group">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/15 border border-indigo-500/25 flex items-center justify-center flex-shrink-0">
                    <span class="text-lg">🎯</span>
                </div>
                <div class="flex-1">
                    <div class="text-sm text-white/80 font-medium mb-1">{{ $milestone->title }}</div>
                    <a href="{{ route('goals.show', $milestone->goal) }}" class="text-xs text-indigo-400/70 hover:text-indigo-400 transition-colors block mb-1">{{ $milestone->goal->title }}</a>
                    @if($milestone->target_date)
                    <div class="text-xs text-white/30">Target: {{ $milestone->target_date->format('M d, Y') }}</div>
                    @endif
                </div>
                <form method="POST" action="{{ route('milestones.toggle', $milestone) }}" class="opacity-0 group-hover:opacity-100 transition-opacity">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-glass btn-sm text-xs">Complete</button>
                </form>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Completed Milestones -->
    <div class="glass-card-static rounded-2xl p-6 animate-fade-in-up" style="animation-delay:0.1s;animation-fill-mode:both">
        <h3 class="text-white font-semibold mb-5 flex items-center gap-2">
            <span class="text-lg">🏆</span> Achieved
            <span class="badge badge-completed">{{ $milestones->get('completed', collect())->count() }}</span>
        </h3>
        @if($milestones->get('completed', collect())->isEmpty())
        <div class="text-center py-10 text-white/40 text-sm">No completed milestones yet</div>
        @else
        <div class="space-y-3">
            @foreach($milestones->get('completed', collect()) as $milestone)
            <div class="flex items-start gap-4 p-4 rounded-xl bg-emerald-500/[0.05] border border-emerald-500/[0.15] group">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                    <span class="text-lg">🏆</span>
                </div>
                <div class="flex-1">
                    <div class="text-sm text-white/70 font-medium mb-1">{{ $milestone->title }}</div>
                    <a href="{{ route('goals.show', $milestone->goal) }}" class="text-xs text-indigo-400/70 hover:text-indigo-400 transition-colors block mb-1">{{ $milestone->goal->title }}</a>
                    @if($milestone->completed_at)
                    <div class="text-xs text-emerald-400/60">Achieved {{ $milestone->completed_at->diffForHumans() }}</div>
                    @endif
                </div>
                <form method="POST" action="{{ route('milestones.toggle', $milestone) }}" class="opacity-0 group-hover:opacity-100 transition-opacity">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-glass btn-sm text-xs">Reopen</button>
                </form>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
</x-layouts.app>
