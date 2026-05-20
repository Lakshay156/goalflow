<x-layouts.app>
<x-slot:title>Calendar</x-slot:title>

<div class="mb-6 animate-fade-in-up">
    <h2 class="text-2xl font-bold text-white tracking-tight">Calendar</h2>
    <p class="text-white/50 text-sm mt-1">Goals, tasks, and milestones in one view</p>
</div>

<div class="grid lg:grid-cols-3 gap-6" x-data="calendarApp(@json($events))">
    <!-- Calendar Grid -->
    <div class="lg:col-span-2 glass-card-static rounded-2xl p-6 animate-fade-in-up">
        <!-- Month Navigation -->
        <div class="flex items-center justify-between mb-6">
            <button @click="prevMonth()" class="btn-glass btn-sm px-3">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <h3 class="text-white font-semibold text-lg" x-text="monthName + ' ' + year"></h3>
            <button @click="nextMonth()" class="btn-glass btn-sm px-3">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        <!-- Day headers -->
        <div class="calendar-grid mb-2">
            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
            <div class="text-center text-xs text-white/30 py-2">{{ $day }}</div>
            @endforeach
        </div>

        <!-- Day cells -->
        <div class="calendar-grid">
            <template x-for="(day, index) in calendarDays" :key="index">
                <div class="calendar-day"
                     :class="{
                         'today': day.isToday,
                         'selected': selectedDate === day.date,
                         'opacity-30': !day.currentMonth,
                     }"
                     @click="day.date && selectDate(day.date)">
                    <span class="text-sm mb-1 font-medium"
                          :class="day.isToday ? 'text-indigo-300' : 'text-white/70'"
                          x-text="day.day"></span>
                    <!-- Event dots -->
                    <div class="flex flex-wrap gap-0.5 justify-center">
                        <template x-for="event in getEventsForDate(day.date)" :key="event.id">
                            <div class="w-1.5 h-1.5 rounded-full" :style="'background:' + event.color"></div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Event List for Selected Day -->
    <div class="space-y-4">
        <!-- Legend -->
        <div class="glass-card-static rounded-2xl p-4 animate-fade-in" style="animation-delay:0.1s;animation-fill-mode:both">
            <h4 class="text-white/70 text-sm font-medium mb-3">Legend</h4>
            @foreach([['Goal Deadline','#6366f1'],['Task Due','#8b5cf6'],['Milestone','#f59e0b']] as $l)
            <div class="flex items-center gap-2 mb-2">
                <div class="w-3 h-3 rounded-full" style="background:{{ $l[1] }}"></div>
                <span class="text-white/50 text-xs">{{ $l[0] }}</span>
            </div>
            @endforeach
        </div>

        <!-- Selected Day Events -->
        <div class="glass-card-static rounded-2xl p-4 animate-fade-in" style="animation-delay:0.15s;animation-fill-mode:both">
            <h4 class="text-white font-medium mb-3" x-text="selectedDate ? formatSelectedDate() : 'Select a day'"></h4>
            <div x-show="!selectedDate" class="text-white/40 text-sm text-center py-4">Click a date to see events</div>
            <div class="space-y-2" x-show="selectedDate">
                <template x-for="event in selectedEvents" :key="event.id">
                    <a :href="event.url" class="flex items-center gap-3 p-3 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] border border-white/[0.06] transition-all">
                        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="'background:' + event.color"></div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm text-white/80 truncate" x-text="event.title"></div>
                            <div class="text-xs text-white/40 capitalize" x-text="event.type + ' · ' + event.status"></div>
                        </div>
                    </a>
                </template>
                <div x-show="selectedEvents.length === 0" class="text-white/40 text-sm text-center py-4">No events this day</div>
            </div>
        </div>

        <!-- Upcoming events -->
        <div class="glass-card-static rounded-2xl p-4 animate-fade-in" style="animation-delay:0.2s;animation-fill-mode:both">
            <h4 class="text-white font-medium mb-3">Upcoming</h4>
            <div class="space-y-2">
                <template x-for="event in upcomingEvents.slice(0, 6)" :key="event.id">
                    <a :href="event.url" class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/[0.05] transition-all">
                        <div class="w-2 h-2 rounded-full flex-shrink-0" :style="'background:' + event.color"></div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs text-white/70 truncate" x-text="event.title"></div>
                            <div class="text-xs text-white/30" x-text="event.date"></div>
                        </div>
                    </a>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function calendarApp(events) {
    return {
        events,
        today: new Date(),
        currentDate: new Date(),
        selectedDate: null,
        selectedEvents: [],

        get year() { return this.currentDate.getFullYear(); },
        get month() { return this.currentDate.getMonth(); },
        get monthName() { return this.currentDate.toLocaleString('default', { month: 'long' }); },

        get calendarDays() {
            const first = new Date(this.year, this.month, 1);
            const last  = new Date(this.year, this.month + 1, 0);
            const days  = [];

            for (let i = 0; i < first.getDay(); i++) {
                const d = new Date(this.year, this.month, -first.getDay() + i + 1);
                days.push({ day: d.getDate(), date: null, currentMonth: false, isToday: false });
            }

            for (let d = 1; d <= last.getDate(); d++) {
                const date = new Date(this.year, this.month, d);
                const iso  = date.toISOString().split('T')[0];
                const isToday = date.toDateString() === this.today.toDateString();
                days.push({ day: d, date: iso, currentMonth: true, isToday });
            }

            return days;
        },

        get upcomingEvents() {
            const today = this.today.toISOString().split('T')[0];
            return this.events
                .filter(e => e.date >= today)
                .sort((a, b) => a.date.localeCompare(b.date));
        },

        getEventsForDate(date) {
            if (!date) return [];
            return this.events.filter(e => e.date === date);
        },

        selectDate(date) {
            this.selectedDate = date;
            this.selectedEvents = this.getEventsForDate(date);
        },

        formatSelectedDate() {
            if (!this.selectedDate) return '';
            return new Date(this.selectedDate + 'T00:00:00').toLocaleDateString('default', { weekday: 'long', month: 'long', day: 'numeric' });
        },

        prevMonth() { this.currentDate = new Date(this.year, this.month - 1, 1); },
        nextMonth() { this.currentDate = new Date(this.year, this.month + 1, 1); },
    }
}
</script>
</x-layouts.app>
