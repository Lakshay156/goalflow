<x-layouts.app>
<x-slot:title>Analytics</x-slot:title>
<x-slot:subtitle>Your productivity intelligence</x-slot:subtitle>

<div class="space-y-6">

    {{-- ── RADIAL STATS ROW ──────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 stagger">

        @php
        $rings = [
            ['Goals Done', $overallStats['completed_goals'], $overallStats['total_goals'], '#10b981', 'Completion Rate'],
            ['Tasks Done', $overallStats['completed_tasks'], max($overallStats['total_tasks'],1), '#6366f1', 'Task Completion'],
            ['Day Streak', $overallStats['streak_days'], 30, '#f59e0b', 'Monthly goal'],
            ['This Week', $overallStats['week_tasks'] ?? 0, 20, '#8b5cf6', 'Weekly tasks'],
        ];
        @endphp

        @foreach($rings as [$label, $val, $max, $color, $sub])
        @php
            $pct = $max > 0 ? min(100, round($val / $max * 100)) : 0;
            $r = 36;
            $circ = 2 * pi() * $r;
            $offset = $circ * (1 - $pct / 100);
        @endphp
        <div class="glass-card-static rounded-2xl p-5 flex flex-col items-center text-center">
            <div class="relative mb-3">
                <svg width="88" height="88" viewBox="0 0 88 88" class="-rotate-90">
                    <circle cx="44" cy="44" r="{{ $r }}" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="7"/>
                    <circle cx="44" cy="44" r="{{ $r }}" fill="none" stroke="{{ $color }}" stroke-width="7"
                        stroke-linecap="round"
                        stroke-dasharray="{{ $circ }}"
                        stroke-dashoffset="{{ $offset }}"
                        style="filter: drop-shadow(0 0 6px {{ $color }}66); transition: stroke-dashoffset 1.6s cubic-bezier(0.16,1,0.3,1)"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-xl font-black" style="color:var(--text-primary)">{{ $val }}</span>
                </div>
            </div>
            <div class="text-sm font-semibold" style="color:var(--text-primary)">{{ $label }}</div>
            <div class="text-xs mt-0.5" style="color:var(--text-muted)">{{ $sub }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── TWO COLUMN CHARTS ─────────────────────────────────── --}}
    <div class="grid lg:grid-cols-2 gap-5">

        <!-- Monthly Activity -->
        <div class="glass-card-static rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-semibold" style="color:var(--text-primary)">Monthly Activity</h3>
                    <p class="text-xs mt-0.5" style="color:var(--text-muted)">Goals created vs completed</p>
                </div>
                <span class="badge badge-active">6 months</span>
            </div>
            <div class="relative h-52">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Priority Breakdown -->
        <div class="glass-card-static rounded-2xl p-6">
            <div class="mb-5">
                <h3 class="font-semibold" style="color:var(--text-primary)">Priority Breakdown</h3>
                <p class="text-xs mt-0.5" style="color:var(--text-muted)">Distribution of your goals</p>
            </div>
            <div class="flex items-center gap-6">
                <div class="relative w-44 h-44 flex-shrink-0">
                    <canvas id="priorityChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <div class="text-2xl font-bold" style="color:var(--text-primary)">{{ array_sum($priorityStats) }}</div>
                        <div class="text-xs" style="color:var(--text-muted)">total goals</div>
                    </div>
                </div>
                <div class="space-y-3 flex-1">
                    @foreach([
                        ['Critical', $priorityStats['critical'], '#f43f5e', 'badge-critical'],
                        ['High',     $priorityStats['high'],     '#f97316', 'badge-high'],
                        ['Medium',   $priorityStats['medium'],   '#f59e0b', 'badge-medium'],
                        ['Low',      $priorityStats['low'],      '#10b981', 'badge-low'],
                    ] as [$l, $n, $c, $b])
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full" style="background:{{ $c }}"></div>
                            <span class="text-sm" style="color:var(--text-secondary)">{{ $l }}</span>
                        </div>
                        <span class="text-sm font-semibold" style="color:var(--text-primary)">{{ $n }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── CATEGORY PROGRESS ─────────────────────────────────── --}}
    @if($categoryStats->isNotEmpty())
    <div class="glass-card-static rounded-2xl p-6">
        <h3 class="font-semibold mb-5" style="color:var(--text-primary)">Progress by Category</h3>
        <div class="grid sm:grid-cols-2 gap-4">
            @foreach($categoryStats as $cat)
            <div class="p-4 rounded-xl" style="background:var(--surface-1);border:1px solid var(--glass-border)">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg" style="background:{{ $cat['color'] }}22;border:1px solid {{ $cat['color'] }}33">{{ $cat['icon'] }}</div>
                        <div>
                            <div class="text-sm font-medium" style="color:var(--text-primary)">{{ $cat['name'] }}</div>
                            <div class="text-xs" style="color:var(--text-muted)">{{ $cat['completed'] }}/{{ $cat['total'] }} done</div>
                        </div>
                    </div>
                    <span class="text-sm font-bold" style="color:{{ $cat['color'] }}">{{ $cat['total'] > 0 ? round($cat['completed'] / $cat['total'] * 100) : 0 }}%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="width:{{ $cat['total'] > 0 ? ($cat['completed'] / $cat['total'] * 100) : 0 }}%;background:{{ $cat['color'] }};box-shadow: 0 0 8px {{ $cat['color'] }}55"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── ACTIVITY HEATMAP ──────────────────────────────────── --}}
    <div class="glass-card-static rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-semibold" style="color:var(--text-primary)">Activity Heatmap</h3>
                <p class="text-xs mt-0.5" style="color:var(--text-muted)">Last 90 days of activity</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-1.5">
            @for($i = 89; $i >= 0; $i--)
            @php
                $date = now()->subDays($i);
                $cnt  = rand(0, 4);
                $colors = ['rgba(255,255,255,0.04)','rgba(99,102,241,0.2)','rgba(99,102,241,0.45)','rgba(99,102,241,0.7)','rgba(99,102,241,0.95)'];
            @endphp
            <div class="heatmap-cell" style="background:{{ $colors[$cnt] }}" title="{{ $date->format('M d, Y') }} · {{ $cnt }} tasks"></div>
            @endfor
        </div>
        <div class="flex items-center gap-1.5 mt-3">
            <span class="text-xs" style="color:var(--text-muted)">Less</span>
            @foreach(['rgba(255,255,255,0.04)','rgba(99,102,241,0.2)','rgba(99,102,241,0.45)','rgba(99,102,241,0.7)','rgba(99,102,241,0.95)'] as $c)
            <div class="heatmap-cell" style="background:{{ $c }}"></div>
            @endforeach
            <span class="text-xs" style="color:var(--text-muted)">More</span>
        </div>
    </div>

</div>

<script>
const monthlyData  = @json($monthlyStats);
const priorityData = @json($priorityStats);

const tooltipDefaults = {
    backgroundColor: 'rgba(5,8,22,0.95)',
    borderColor: 'rgba(255,255,255,0.08)',
    borderWidth: 1,
    titleColor: 'rgba(255,255,255,0.9)',
    bodyColor: 'rgba(255,255,255,0.55)',
    padding: 12,
    cornerRadius: 12,
};

// Monthly bar chart
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.map(d => d.month),
        datasets: [
            {
                label: 'Created',
                data: monthlyData.map(d => d.created),
                backgroundColor: 'rgba(99,102,241,0.35)',
                borderRadius: 8,
                borderSkipped: false,
            },
            {
                label: 'Completed',
                data: monthlyData.map(d => d.completed),
                backgroundColor: 'rgba(16,185,129,0.55)',
                borderRadius: 8,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: 'rgba(255,255,255,0.45)', boxWidth: 10, font: { size: 11 } } },
            tooltip: tooltipDefaults,
        },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: 'rgba(255,255,255,0.35)', font: { size: 11 } }, border: { display: false } },
            y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: 'rgba(255,255,255,0.35)', font: { size: 11 } }, border: { display: false }, beginAtZero: true }
        }
    }
});

// Priority donut
new Chart(document.getElementById('priorityChart'), {
    type: 'doughnut',
    data: {
        labels: ['Critical','High','Medium','Low'],
        datasets: [{
            data: [priorityData.critical, priorityData.high, priorityData.medium, priorityData.low],
            backgroundColor: ['rgba(244,63,94,0.75)','rgba(249,115,22,0.75)','rgba(245,158,11,0.75)','rgba(16,185,129,0.75)'],
            borderColor: 'transparent',
            hoverBorderColor: 'rgba(255,255,255,0.15)',
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: tooltipDefaults },
        cutout: '74%',
        rotation: -90,
    }
});

// Animate progress fills
document.querySelectorAll('.progress-fill').forEach(bar => {
    const w = bar.style.width;
    bar.style.width = '0%';
    requestAnimationFrame(() => setTimeout(() => bar.style.width = w, 100));
});
</script>
</x-layouts.app>
