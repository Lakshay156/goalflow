<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="GoalFlow — Transform your goals into achievements.">
    <title>{{ isset($title) ? $title . ' — GoalFlow' : 'GoalFlow' }}</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body
    x-data="goalflowApp()"
    :class="theme"
    @keydown.ctrl.k.window.prevent="cmdOpen = true"
    @keydown.meta.k.window.prevent="cmdOpen = true"
    @keydown.escape.window="cmdOpen = false; aiOpen = false"
>

<!-- ══ AMBIENT BACKGROUND ══════════════════════════════════ -->
<div class="ambient-bg">
    <div class="ambient-orb orb-a"></div>
    <div class="ambient-orb orb-b"></div>
    <div class="ambient-orb orb-c"></div>
    <div class="ambient-orb orb-d"></div>
</div>
<div class="noise-overlay"></div>

<!-- ══ TOAST QUEUE ═════════════════════════════════════════ -->
<div class="fixed top-4 right-4 z-[100] flex flex-col gap-2 pointer-events-none" x-cloak>
    <template x-for="toast in toasts" :key="toast.id">
        <div class="toast pointer-events-auto" :class="'toast-' + toast.type"
             x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8 scale-95"
             x-transition:enter-end="opacity-100 translate-x-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0 translate-x-8">
            <div class="w-2 h-2 rounded-full flex-shrink-0"
                 :class="{'bg-emerald-400':toast.type==='success','bg-red-400':toast.type==='error','bg-amber-400':toast.type==='warning','bg-indigo-400':toast.type==='info'}"></div>
            <span x-text="toast.message" class="flex-1 text-sm font-medium"></span>
            <button @click="removeToast(toast.id)" class="opacity-40 hover:opacity-80 transition-opacity ml-1 flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>

<!-- ══ COMMAND PALETTE ══════════════════════════════════════ -->
<div x-show="cmdOpen" x-cloak class="cmd-backdrop"
     @click.self="cmdOpen = false"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-data="commandPalette()">
    <div class="cmd-panel"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <!-- Search Input -->
        <div class="flex items-center gap-3 px-5 py-4">
            <svg class="w-5 h-5 flex-shrink-0" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" placeholder="Search goals, navigate, create..."
                   class="cmd-input"
                   x-model="query"
                   @keydown.arrow-down.prevent="navigate(1)"
                   @keydown.arrow-up.prevent="navigate(-1)"
                   @keydown.enter.prevent="executeSelected()"
                   x-ref="cmdInput"
                   x-init="$nextTick(() => { if($el.offsetParent) $el.focus() })">
            <span class="text-xs font-mono px-2 py-1 rounded-md text-nowrap flex-shrink-0" style="background:var(--surface-1);color:var(--text-muted);border:1px solid var(--glass-border)">ESC</span>
        </div>
        <div class="cmd-divider"></div>
        <div class="cmd-results" @click="executeSelected()">
            <template x-if="query === ''">
                <div>
                    <div class="cmd-section-label">Navigation</div>
                    <template x-for="(item, i) in navItems" :key="item.label">
                        <div class="cmd-item" :class="{selected: selectedIndex === i}" @click="goto(item.url)" @mouseenter="selectedIndex = i">
                            <div class="cmd-item-icon" x-text="item.icon"></div>
                            <span x-text="item.label" class="font-medium" style="color:var(--text-primary)"></span>
                            <span class="cmd-shortcut" x-text="item.shortcut || ''"></span>
                        </div>
                    </template>
                    <div class="cmd-section-label mt-2">Quick Actions</div>
                    <template x-for="(item, i) in quickActions" :key="item.label">
                        <div class="cmd-item" :class="{selected: selectedIndex === navItems.length + i}" @mouseenter="selectedIndex = navItems.length + i">
                            <div class="cmd-item-icon" x-text="item.icon"></div>
                            <span x-text="item.label" class="font-medium" style="color:var(--text-primary)"></span>
                        </div>
                    </template>
                </div>
            </template>
            <template x-if="query !== ''">
                <div>
                    <template x-if="filteredItems.length === 0">
                        <div class="text-center py-10" style="color:var(--text-muted)">
                            <div class="text-3xl mb-2">🔍</div>
                            <p class="text-sm">No results for "<span x-text="query"></span>"</p>
                        </div>
                    </template>
                    <template x-for="(item, i) in filteredItems" :key="item.label">
                        <div class="cmd-item" :class="{selected: selectedIndex === i}" @click="goto(item.url)" @mouseenter="selectedIndex = i">
                            <div class="cmd-item-icon" x-text="item.icon"></div>
                            <div class="flex-1">
                                <div class="font-medium" style="color:var(--text-primary)" x-text="item.label"></div>
                                <div class="text-xs mt-0.5" style="color:var(--text-muted)" x-text="item.category || ''"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
        <div class="cmd-divider"></div>
        <div class="flex items-center gap-4 px-5 py-2.5">
            <span class="text-xs" style="color:var(--text-muted)">↑↓ navigate · Enter select · ESC close</span>
        </div>
    </div>
</div>

<!-- ══ AI ASSISTANT ═════════════════════════════════════════ -->
<div class="ai-fab" x-cloak>
    <!-- Panel -->
    <div x-show="aiOpen" class="ai-panel mb-3"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         x-data="aiAssistant()">
        <!-- Header -->
        <div class="flex items-center gap-3 p-4 border-b" style="border-color:var(--glass-border)">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-base" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">✦</div>
            <div class="flex-1">
                <div class="text-sm font-semibold" style="color:var(--text-primary)">GoalFlow AI</div>
                <div class="text-xs" style="color:var(--text-muted)">Your productivity assistant</div>
            </div>
            <button @click="aiOpen = false" class="btn-icon w-7 h-7">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3" style="max-height:280px" x-ref="messages">
            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div class="max-w-[85%] text-sm rounded-2xl px-3.5 py-2.5 leading-relaxed"
                         :class="msg.role === 'user'
                             ? 'text-white rounded-br-md'
                             : 'rounded-bl-md'"
                         :style="msg.role === 'user'
                             ? 'background:linear-gradient(135deg,#6366f1,#8b5cf6)'
                             : 'background:var(--surface-1);color:var(--text-secondary);border:1px solid var(--glass-border)'"
                         x-text="msg.text"></div>
                </div>
            </template>
            <div x-show="isTyping" class="flex justify-start">
                <div class="px-4 py-3 rounded-2xl rounded-bl-md text-sm" style="background:var(--surface-1);border:1px solid var(--glass-border)">
                    <span class="ai-typing" style="color:var(--text-muted)"></span>
                </div>
            </div>
        </div>
        <!-- Suggestions -->
        <div x-show="messages.length <= 1" class="px-4 pb-3 flex flex-wrap gap-2">
            <template x-for="chip in suggestions" :key="chip">
                <button @click="sendMessage(chip)" class="text-xs px-3 py-1.5 rounded-full transition-all" style="background:var(--surface-1);color:var(--text-secondary);border:1px solid var(--glass-border)" x-text="chip"
                    onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background='var(--surface-1)'"></button>
            </template>
        </div>
        <!-- Input -->
        <div class="p-4 border-t" style="border-color:var(--glass-border)">
            <form @submit.prevent="sendMessage(aiInput); aiInput=''" class="flex gap-2">
                <input type="text" x-model="aiInput" placeholder="Ask anything..." class="glass-input flex-1 px-3 py-2.5 text-sm">
                <button type="submit" class="btn-primary btn-sm px-4">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
        </div>
    </div>
    <!-- Orb Button -->
    <button @click="aiOpen = !aiOpen" class="ai-orb" title="GoalFlow AI (⌘ Space)">
        <svg class="w-5 h-5 text-white relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/><path stroke-linecap="round" stroke-linejoin="round" d="M18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/></svg>
    </button>
</div>

<!-- ══ FLOATING DOCK NAVIGATION ════════════════════════════ -->
<nav class="dock hidden md:flex" :class="dockExpanded ? 'expanded' : ''" role="navigation" aria-label="Main navigation">
    <!-- Logo -->
    <div class="dock-logo mb-2 flex-shrink-0" :class="dockExpanded ? 'w-8 h-8' : ''">
        <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" fill="none" class="w-5 h-5">
            <defs>
                <linearGradient id="dockLogoGrad" x1="10" y1="90" x2="90" y2="10" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#7C3AED"/>
                    <stop offset="55%" stop-color="#818CF8"/>
                    <stop offset="100%" stop-color="#22D3EE"/>
                </linearGradient>
            </defs>
            <path d="M 84 50 A 34 34 0 1 1 67 21" stroke="url(#dockLogoGrad)" stroke-width="15" stroke-linecap="round"/>
            <path d="M 51 58 L 79 58 L 89 44" stroke="url(#dockLogoGrad)" stroke-width="13" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M 82 17 L 83.8 23.2 L 90 25 L 83.8 26.8 L 82 33 L 80.2 26.8 L 74 25 L 80.2 23.2 Z" fill="#22D3EE"/>
        </svg>
    </div>
    <template x-if="dockExpanded">
        <span class="text-sm font-bold mb-3 px-1 tracking-tight" style="background:linear-gradient(90deg,#818cf8,#22d3ee);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">GoalFlow</span>
    </template>

    <div class="dock-divider"></div>

    @php
        $navItems = [
            ['route' => 'dashboard',       'icon' => '<svg class="w-5 h-5 dock-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>', 'label' => 'Dashboard'],
            ['route' => 'goals.index',     'icon' => '<svg class="w-5 h-5 dock-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>', 'label' => 'Goals'],
            ['route' => 'tasks.index',     'icon' => '<svg class="w-5 h-5 dock-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>', 'label' => 'Tasks'],
            ['route' => 'milestones.index','icon' => '<svg class="w-5 h-5 dock-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>', 'label' => 'Milestones'],
            ['route' => 'analytics.index', 'icon' => '<svg class="w-5 h-5 dock-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>', 'label' => 'Analytics'],
            ['route' => 'calendar.index',  'icon' => '<svg class="w-5 h-5 dock-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>', 'label' => 'Calendar'],
        ];
    @endphp

    @foreach($navItems as $item)
    <a href="{{ route($item['route']) }}"
       class="dock-item {{ request()->routeIs($item['route']) ? 'active' : '' }}"
       title="{{ $item['label'] }}">
        {!! $item['icon'] !!}
        <template x-if="dockExpanded">
            <span class="text-sm truncate">{{ $item['label'] }}</span>
        </template>
        <span class="dock-tooltip">{{ $item['label'] }}</span>
    </a>
    @endforeach

    <div class="dock-divider"></div>

    <!-- Cmd palette trigger -->
    <button @click="cmdOpen = true" class="dock-item" title="Command Palette (Ctrl+K)">
        <svg class="w-5 h-5 dock-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <template x-if="dockExpanded"><span class="text-sm">Search</span></template>
        <span class="dock-tooltip">Search ⌘K</span>
    </button>

    <!-- Settings -->
    <a href="{{ route('settings.index') }}" class="dock-item {{ request()->routeIs('settings.*') ? 'active' : '' }}" title="Settings">
        <svg class="w-5 h-5 dock-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
        <template x-if="dockExpanded"><span class="text-sm">Settings</span></template>
        <span class="dock-tooltip">Settings</span>
    </a>

    <!-- Expand toggle -->
    <button @click="dockExpanded = !dockExpanded" class="dock-item mt-auto" title="Toggle Sidebar">
        <svg class="w-5 h-5 dock-icon transition-transform duration-300" :class="dockExpanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
        <template x-if="dockExpanded"><span class="text-sm">Collapse</span></template>
        <span class="dock-tooltip">Toggle sidebar</span>
    </button>
</nav>

<!-- ══ MOBILE BOTTOM NAV ════════════════════════════════════ -->
<nav class="dock md:hidden" role="navigation" aria-label="Mobile navigation">
    @foreach([
        ['dashboard', '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>', 'Home'],
        ['goals.index', '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>', 'Goals'],
        ['analytics.index', '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>', 'Analytics'],
        ['calendar.index', '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>', 'Calendar'],
        ['settings.index', '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>', 'Settings'],
    ] as [$route, $svg, $label])
    <a href="{{ route($route) }}" class="dock-item {{ request()->routeIs($route) ? 'active' : '' }}">
        {!! $svg !!}
        <span class="text-[10px] font-medium mt-0.5" style="color:inherit">{{ $label }}</span>
    </a>
    @endforeach
</nav>

<!-- ══ MAIN CONTENT ═════════════════════════════════════════ -->
<div class="min-h-screen transition-all duration-300 ease-spring"
     :style="dockExpanded ? 'padding-left: 212px' : 'padding-left: 84px'"
     style="padding-left: 212px">

    <!-- Top Bar -->
    <header class="topbar sticky top-0 z-30 flex items-center justify-between gap-4 px-6"
            style="background:var(--topbar-bg);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);">

        <!-- Left: Page title + optional breadcrumb -->
        <div class="flex items-center gap-2.5 min-w-0 py-3.5">
            @if(isset($breadcrumb))
            <a href="{{ $breadcrumb['url'] }}" class="text-sm transition-colors hidden sm:block flex-shrink-0"
               style="color:var(--text-muted)"
               onmouseover="this.style.color='var(--text-secondary)'"
               onmouseout="this.style.color='var(--text-muted)'">{{ $breadcrumb['label'] }}</a>
            <svg class="w-3 h-3 flex-shrink-0 hidden sm:block" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            @endif
            <h1 class="text-base font-semibold truncate" style="color:var(--text-primary)">{{ $title ?? 'Dashboard' }}</h1>
            @if(isset($subtitle))
            <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0 hidden sm:inline-flex"
                  style="background:var(--surface-1);border:1px solid var(--glass-border);color:var(--text-muted)">{{ $subtitle }}</span>
            @endif
        </div>

        <!-- Right: Action cluster -->
        <div class="flex items-center gap-2 flex-shrink-0 py-3.5">

            <!-- Search -->
            <button @click="cmdOpen = true" id="topbar-search-btn"
                    class="topbar-search hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <span class="text-xs">Search</span>
                <kbd class="topbar-kbd text-[10px] font-mono px-1.5 py-0.5 rounded">⌘K</kbd>
            </button>

            <!-- Theme switcher -->
            <div class="relative" x-data="{ themeOpen: false }">
                <button @click="themeOpen = !themeOpen" class="btn-icon" title="Switch theme" id="topbar-theme-btn">
                    <svg x-show="theme === 'theme-dark'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                    <svg x-show="theme === 'theme-light'" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                    <svg x-show="theme === 'theme-frost'" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                    <svg x-show="theme === 'theme-midnight'" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                </button>
                <div x-show="themeOpen" @click.outside="themeOpen = false" x-cloak
                     class="absolute right-0 top-full mt-2 p-1.5 rounded-xl z-50 min-w-[150px]"
                     style="background:var(--bg-elevated);border:1px solid var(--glass-border);box-shadow:0 16px 48px rgba(0,0,0,0.4)"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                    @foreach([['theme-dark','🌙','Dark'],['theme-light','☀️','Light'],['theme-frost','❄️','Frost'],['theme-midnight','⚫','Midnight']] as [$t,$e,$l])
                    <button @click="setTheme('{{ $t }}'); themeOpen = false"
                            class="flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-sm transition-all"
                            style="color:var(--text-secondary)"
                            onmouseover="this.style.background='var(--surface-1)'"
                            onmouseout="this.style.background='transparent'"
                            :class="{'font-medium': theme === '{{ $t }}'}">
                        <span>{{ $e }}</span><span>{{ $l }}</span>
                        <svg x-show="theme === '{{ $t }}'" class="w-3.5 h-3.5 ml-auto text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Streak Badge (enhanced) -->
            <div class="topbar-streak flex items-center gap-1.5 px-3 py-1.5 rounded-full" title="{{ auth()->user()->streak_days }} day streak">
                <span class="text-sm leading-none">🔥</span>
                <span class="text-sm font-bold text-amber-400 tabular-nums">{{ auth()->user()->streak_days }}</span>
                <span class="text-xs hidden sm:inline" style="color:rgba(251,191,36,0.65)">day streak</span>
            </div>

            <!-- Notification Bell -->
            @php $unreadCount = auth()->user()->notifications()->where('is_read', false)->count(); @endphp
            <div class="relative" x-data="{ notifOpen: false }">
                <button @click="notifOpen = !notifOpen" id="topbar-notif-btn"
                        class="btn-icon relative"
                        title="Notifications"
                        :class="notifOpen ? 'border-[var(--glass-border-md)] bg-[var(--surface-2)]' : ''">
                    <svg class="w-4 h-4 transition-transform duration-200" :class="notifOpen ? 'rotate-12' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unreadCount > 0)
                    <span class="notif-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>

                <!-- Notification Dropdown -->
                <div x-show="notifOpen" @click.outside="notifOpen = false" x-cloak
                     class="absolute right-0 top-full mt-2 rounded-2xl z-50 overflow-hidden"
                     style="width:320px;background:var(--bg-elevated);border:1px solid var(--glass-border);box-shadow:0 24px 64px rgba(0,0,0,0.5)"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-4 py-3" style="border-bottom:1px solid var(--glass-border)">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold" style="color:var(--text-primary)">Notifications</span>
                            @if($unreadCount > 0)
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full text-white" style="background:var(--accent-1)">{{ $unreadCount }}</span>
                            @endif
                        </div>
                        @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.markAllRead') }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs font-medium transition-colors" style="color:var(--accent-1)" onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">Mark all read</button>
                        </form>
                        @endif
                    </div>
                    <!-- Items -->
                    @php $notifications = auth()->user()->notifications()->latest()->take(5)->get(); @endphp
                    <div class="max-h-[300px] overflow-y-auto">
                        @forelse($notifications as $notif)
                        <div class="flex items-start gap-3 px-4 py-3 transition-colors {{ !$notif->is_read ? 'notif-unread' : '' }}"
                             style="border-bottom:1px solid var(--glass-border)"
                             onmouseover="this.style.background='var(--surface-1)'"
                             onmouseout="this.style.background='{{ !$notif->is_read ? 'rgba(99,102,241,0.05)' : 'transparent' }}'">
                            <div class="mt-1.5 flex-shrink-0 w-2 h-2 rounded-full {{ !$notif->is_read ? 'bg-indigo-400 shadow-[0_0_6px_rgba(99,102,241,0.6)]' : '' }}" style="{{ $notif->is_read ? 'background:var(--glass-border)' : '' }}"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium truncate" style="color:{{ $notif->is_read ? 'var(--text-secondary)' : 'var(--text-primary)' }}">{{ $notif->title }}</p>
                                <p class="text-xs mt-0.5 line-clamp-1" style="color:var(--text-muted)">{{ $notif->body }}</p>
                                <p class="text-[10px] mt-1 font-medium" style="color:var(--text-muted)">{{ $notif->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-10">
                            <div class="text-3xl mb-2">🔔</div>
                            <p class="text-sm font-medium" style="color:var(--text-muted)">You're all caught up!</p>
                            <p class="text-xs mt-1" style="color:var(--text-muted)">No new notifications</p>
                        </div>
                        @endforelse
                    </div>
                    @if($notifications->count() > 0)
                    <div class="px-4 py-2.5 text-center" style="border-top:1px solid var(--glass-border)">
                        <a href="{{ route('settings.index') }}" class="text-xs font-medium transition-colors" style="color:var(--text-muted)" onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color='var(--text-muted)'">View all → </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- New Goal CTA -->
            <a href="{{ route('goals.create') }}" class="btn-primary btn-sm hidden sm:inline-flex" id="topbar-new-goal-btn">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                New Goal
            </a>

            <!-- Avatar -->
            <div class="relative" x-data="{menuOpen:false}">
                <button @click="menuOpen = !menuOpen" class="avatar-btn" id="topbar-avatar-btn" title="{{ auth()->user()->name }}">
                    <span class="text-white text-xs font-bold relative z-10">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                    <span class="avatar-online-dot"></span>
                </button>
                <div x-show="menuOpen" @click.outside="menuOpen = false" x-cloak
                     class="absolute right-0 top-full mt-2 p-1.5 rounded-2xl min-w-[210px] z-50"
                     style="background:var(--bg-elevated);border:1px solid var(--glass-border);box-shadow:0 24px 64px rgba(0,0,0,0.45)"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                    <!-- User Info Card -->
                    <div class="flex items-center gap-3 px-3 py-3 mb-1 rounded-xl" style="background:var(--surface-1)">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                             style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold truncate" style="color:var(--text-primary)">{{ auth()->user()->name }}</div>
                            <div class="text-xs truncate" style="color:var(--text-muted)">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="h-px my-1.5" style="background:var(--glass-border)"></div>
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm transition-all" style="color:var(--text-secondary)" onmouseover="this.style.background='var(--surface-1)'" onmouseout="this.style.background='transparent'">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                        Settings
                    </a>
                    <div class="h-px my-1.5" style="background:var(--glass-border)"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm w-full text-left transition-all text-red-400"
                                onmouseover="this.style.background='rgba(244,63,94,0.08)'"
                                onmouseout="this.style.background='transparent'">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash / Session toast -->
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('show-toast', {
                detail: { message: @json(session('success')), type: 'success' }
            }));
        });
    </script>
    @endif

    <!-- Page Content -->
    <main class="p-5 lg:p-7 pb-24 md:pb-8">
        {{ $slot }}
    </main>
</div>

<!-- ══ ALPINE DATA ══════════════════════════════════════════ -->
<script>
function goalflowApp() {
    return {
        theme: localStorage.getItem('gf-theme') || 'theme-dark',
        cmdOpen: false,
        aiOpen: false,
        dockExpanded: true,
        toasts: [],
        _toastId: 0,

        setTheme(t) {
            this.theme = t;
            localStorage.setItem('gf-theme', t);
            // Sync to server
            fetch('/settings/theme', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,'X-HTTP-Method-Override':'PATCH'},
                body: JSON.stringify({ theme: t.replace('theme-', '') })
            });
        },

        addToast(message, type = 'info', duration = 4000) {
            const id = ++this._toastId;
            this.toasts.push({ id, message, type, visible: true });
            setTimeout(() => this.removeToast(id), duration);
        },

        removeToast(id) {
            const t = this.toasts.find(x => x.id === id);
            if (t) t.visible = false;
            setTimeout(() => this.toasts = this.toasts.filter(x => x.id !== id), 350);
        },

        init() {
            // Apply stored theme on load
            window.addEventListener('show-toast', e => this.addToast(e.detail.message, e.detail.type));
        }
    };
}

function commandPalette() {
    return {
        query: '',
        selectedIndex: 0,
        navItems: [
            { label: 'Dashboard',  icon: '🏠', url: '{{ route("dashboard") }}', shortcut: 'G D', category: 'Navigation' },
            { label: 'Goals',      icon: '🎯', url: '{{ route("goals.index") }}', shortcut: 'G G', category: 'Navigation' },
            { label: 'Tasks',      icon: '✅', url: '{{ route("tasks.index") }}', shortcut: 'G T', category: 'Navigation' },
            { label: 'Milestones', icon: '🏆', url: '{{ route("milestones.index") }}', category: 'Navigation' },
            { label: 'Analytics',  icon: '📊', url: '{{ route("analytics.index") }}', category: 'Navigation' },
            { label: 'Calendar',   icon: '📅', url: '{{ route("calendar.index") }}', category: 'Navigation' },
            { label: 'Settings',   icon: '⚙️', url: '{{ route("settings.index") }}', category: 'Navigation' },
        ],
        quickActions: [
            { label: 'New Goal',  icon: '✨', url: '{{ route("goals.create") }}', category: 'Action' },
            { label: 'Dark Mode',   icon: '🌙', action: () => Alpine.store && null, category: 'Appearance' },
        ],
        get allItems() {
            return [...this.navItems, ...this.quickActions];
        },
        get filteredItems() {
            if (!this.query) return [];
            const q = this.query.toLowerCase();
            return this.allItems.filter(i => i.label.toLowerCase().includes(q) || (i.category||'').toLowerCase().includes(q));
        },
        navigate(dir) {
            const len = this.query ? this.filteredItems.length : (this.navItems.length + this.quickActions.length);
            this.selectedIndex = Math.max(0, Math.min(len - 1, this.selectedIndex + dir));
        },
        executeSelected() {
            const items = this.query ? this.filteredItems : [...this.navItems, ...this.quickActions];
            const item = items[this.selectedIndex];
            if (item) this.goto(item.url);
        },
        goto(url) {
            if (url) { window.location.href = url; }
            this.$dispatch('close-cmd');
        }
    };
}

function aiAssistant() {
    const responses = {
        default: [
            "I can help you break down your goals into actionable steps!",
            "Try asking me to plan your study schedule or create milestones for a project.",
            "I can generate a 30-day roadmap for any goal you have in mind.",
            "Want me to analyze your current goal progress and suggest improvements?",
        ],
        fitness: ["Start with 3 days/week training. Week 1-2: Build habit. Week 3-4: Increase intensity. Set a milestone every 2 weeks to track your progress!"],
        study: ["Break your subject into topics. Allocate 2-3 hours per topic with Pomodoro sessions. Create a milestone for each chapter completed!"],
        project: ["Phase 1: Research & Planning (Week 1). Phase 2: Core Development (Weeks 2-4). Phase 3: Testing & Launch (Week 5). Add milestones at each phase!"],
    };
    return {
        messages: [{ id: 1, role: 'assistant', text: "Hey! I'm your GoalFlow AI. Ask me to plan goals, generate milestones, or give productivity advice. ✨" }],
        aiInput: '',
        isTyping: false,
        suggestions: ['Plan my fitness goal 🏋️', 'Create study schedule 📚', 'Break down my project 💡', 'Weekly review tips ✅'],
        _id: 2,

        sendMessage(text) {
            if (!text.trim()) return;
            this.messages.push({ id: this._id++, role: 'user', text });
            this.isTyping = true;
            const self = this;
            setTimeout(() => {
                self.isTyping = false;
                let reply = responses.default[Math.floor(Math.random() * responses.default.length)];
                const t = text.toLowerCase();
                if (t.includes('fit') || t.includes('gym') || t.includes('run')) reply = responses.fitness[0];
                else if (t.includes('study') || t.includes('exam') || t.includes('learn') || t.includes('dsa')) reply = responses.study[0];
                else if (t.includes('project') || t.includes('launch') || t.includes('build')) reply = responses.project[0];
                self.messages.push({ id: self._id++, role: 'assistant', text: reply });
                self.$nextTick(() => {
                    const el = self.$refs.messages;
                    if (el) el.scrollTop = el.scrollHeight;
                });
            }, 1200 + Math.random() * 500);
        }
    };
}
</script>

</body>
</html>
