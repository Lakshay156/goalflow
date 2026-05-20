<x-layouts.app>
<x-slot:title>Create Goal</x-slot:title>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6 animate-fade-in-down">
        <a href="{{ route('goals.index') }}" class="text-white/50 hover:text-white/80 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-white">Create New Goal</h2>
    </div>

    <div class="glass-card-static rounded-2xl p-8 animate-fade-in-up">
        <form method="POST" action="{{ route('goals.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">Goal Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="glass-input w-full px-4 py-3 text-sm" placeholder="What do you want to achieve?">
                @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">Description</label>
                <textarea name="description" rows="3"
                    class="glass-input w-full px-4 py-3 text-sm resize-none"
                    placeholder="Describe your goal in detail...">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">Category</label>
                    <select name="category_id" class="glass-input w-full px-4 py-3 text-sm">
                        <option value="">No category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">Priority <span class="text-red-400">*</span></label>
                    <select name="priority" required class="glass-input w-full px-4 py-3 text-sm">
                        @foreach(['low' => 'Low','medium' => 'Medium','high' => 'High','critical' => 'Critical'] as $val => $label)
                        <option value="{{ $val }}" {{ old('priority', 'medium') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">Deadline</label>
                    <input type="date" name="deadline" value="{{ old('deadline') }}"
                        class="glass-input w-full px-4 py-3 text-sm"
                        min="{{ now()->addDay()->format('Y-m-d') }}">
                    @error('deadline') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">Initial Progress (%)</label>
                    <input type="number" name="progress" value="{{ old('progress', 0) }}"
                        min="0" max="100" class="glass-input w-full px-4 py-3 text-sm">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 justify-center py-3.5">
                    Create Goal
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
                <a href="{{ route('goals.index') }}" class="btn-glass px-6 py-3.5">Cancel</a>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>
