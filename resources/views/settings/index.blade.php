<x-layouts.app>
<x-slot:title>Settings</x-slot:title>

<div class="mb-8 animate-fade-in-up">
    <h2 class="text-2xl font-bold text-white tracking-tight">Settings</h2>
    <p class="text-white/50 text-sm mt-1">Manage your account and preferences</p>
</div>

<div class="grid lg:grid-cols-3 gap-6" x-data="{ activeTab: 'profile' }">
    <!-- Sidebar Tabs -->
    <div class="lg:col-span-1 space-y-2 animate-slide-in-left">
        <div class="glass-card-static rounded-2xl p-3">
            @foreach([['profile','Profile','👤'],['theme','Appearance','🎨'],['security','Security','🔒'],['categories','Categories','🗂️']] as $tab)
            <button @click="activeTab = '{{ $tab[0] }}'"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-left"
                    :class="activeTab === '{{ $tab[0] }}' ? 'bg-indigo-500/15 text-indigo-300 border border-indigo-500/25' : 'text-white/60 hover:text-white/80 hover:bg-white/[0.04]'">
                <span>{{ $tab[2] }}</span>
                <span class="font-medium">{{ $tab[1] }}</span>
            </button>
            @endforeach
        </div>

        <!-- User Card -->
        <div class="glass-card-static rounded-2xl p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-bold text-lg">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div class="text-white font-medium">{{ $user->name }}</div>
                    <div class="text-white/40 text-xs">{{ $user->email }}</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge badge-active">🔥 {{ $user->streak_days }} day streak</span>
            </div>
        </div>
    </div>

    <!-- Settings Content -->
    <div class="lg:col-span-2 animate-fade-in-up">

        <!-- Profile Tab -->
        <div x-show="activeTab === 'profile'" class="glass-card-static rounded-2xl p-6">
            <h3 class="text-white font-semibold text-lg mb-6">Profile Information</h3>
            <form method="POST" action="{{ route('settings.profile') }}" class="space-y-4">
                @csrf @method('PATCH')
                @if(session('success'))<div class="badge badge-completed mb-4 text-sm">✅ {{ session('success') }}</div>@endif
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="glass-input w-full px-4 py-3 text-sm">
                    @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="glass-input w-full px-4 py-3 text-sm">
                    @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">Bio</label>
                    <textarea name="bio" rows="3" class="glass-input w-full px-4 py-3 text-sm resize-none" placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                </div>
                <button type="submit" class="btn-primary">Save Profile</button>
            </form>
        </div>

        <!-- Theme Tab -->
        <div x-show="activeTab === 'theme'" x-cloak class="glass-card-static rounded-2xl p-6">
            <h3 class="text-white font-semibold text-lg mb-6">Appearance</h3>
            <form method="POST" action="{{ route('settings.theme') }}" class="space-y-4">
                @csrf @method('PATCH')
                <div class="grid grid-cols-3 gap-4">
                    @foreach([
                        ['dark',    'Dark Mode',    '#050816', 'Space black with glowing accents'],
                        ['midnight','Midnight',     '#000000', 'Pure black, ultra minimal'],
                        ['frost',   'Frost Mode',  '#e8edf5', 'Clean white glassmorphism'],
                    ] as $theme)
                    <label class="cursor-pointer">
                        <input type="radio" name="theme" value="{{ $theme[0] }}" class="sr-only" {{ $user->theme === $theme[0] ? 'checked' : '' }}>
                        <div class="rounded-2xl p-4 border-2 transition-all {{ $user->theme === $theme[0] ? 'border-indigo-500 bg-indigo-500/10' : 'border-white/10 bg-white/[0.03]' }} hover:border-indigo-500/50">
                            <div class="w-full h-16 rounded-xl mb-3" style="background: {{ $theme[1] }}; border: 1px solid rgba(255,255,255,0.1)"></div>
                            <div class="text-sm text-white font-medium">{{ $theme[1] }}</div>
                            <div class="text-xs text-white/40 mt-1">{{ $theme[3] }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
                <button type="submit" class="btn-primary">Apply Theme</button>
            </form>
        </div>

        <!-- Security Tab -->
        <div x-show="activeTab === 'security'" x-cloak class="glass-card-static rounded-2xl p-6">
            <h3 class="text-white font-semibold text-lg mb-6">Security</h3>
            <form method="POST" action="{{ route('settings.password') }}" class="space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">Current Password</label>
                    <input type="password" name="current_password" class="glass-input w-full px-4 py-3 text-sm">
                    @error('current_password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">New Password</label>
                    <input type="password" name="password" class="glass-input w-full px-4 py-3 text-sm">
                    @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="glass-input w-full px-4 py-3 text-sm">
                </div>
                <button type="submit" class="btn-primary">Update Password</button>
            </form>
        </div>

        <!-- Categories Tab -->
        <div x-show="activeTab === 'categories'" x-cloak class="glass-card-static rounded-2xl p-6">
            <h3 class="text-white font-semibold text-lg mb-6">Goal Categories</h3>

            <!-- Add Category Form -->
            <form method="POST" action="{{ route('settings.categories.store') }}" class="glass-card-static rounded-xl p-4 mb-5 space-y-3">
                @csrf
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-1">
                        <label class="block text-xs text-white/50 mb-1">Icon</label>
                        <input type="text" name="icon" value="{{ old('icon', '🎯') }}" maxlength="2" class="glass-input w-full px-3 py-2.5 text-sm text-center" placeholder="🎯">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-xs text-white/50 mb-1">Color</label>
                        <input type="color" name="color" value="{{ old('color', '#6366f1') }}" class="glass-input w-full px-2 py-2 h-10">
                    </div>
                    <div class="col-span-1 flex items-end">
                        <button type="submit" class="btn-primary btn-sm w-full justify-center">Add</button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-white/50 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="glass-input w-full px-3 py-2.5 text-sm" placeholder="Category name">
                </div>
            </form>

            <!-- Existing Categories -->
            @if($categories->isEmpty())
            <p class="text-white/40 text-sm text-center py-4">No categories yet</p>
            @else
            <div class="space-y-2">
                @foreach($categories as $cat)
                <div class="flex items-center gap-3 p-3 rounded-xl bg-white/[0.04] border border-white/[0.06]">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: {{ $cat->color }}22; border: 1px solid {{ $cat->color }}44">
                        <span>{{ $cat->icon }}</span>
                    </div>
                    <span class="flex-1 text-sm text-white/80">{{ $cat->name }}</span>
                    <span class="text-xs text-white/30">{{ $cat->goals_count }} goals</span>
                    <form method="POST" action="{{ route('settings.categories.destroy', $cat) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-white/30 hover:text-red-400 transition-colors p-1" onclick="return confirm('Delete this category?')">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>
</x-layouts.app>
