<x-layouts.app>
<x-slot:title>Edit Goal</x-slot:title>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6 animate-fade-in-down">
        <a href="{{ route('goals.show', $goal) }}" class="text-white/50 hover:text-white/80 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-white">Edit Goal</h2>
    </div>

    <div class="glass-card-static rounded-2xl p-8 animate-fade-in-up">
        <form method="POST" action="{{ route('goals.update', $goal) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">Goal Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" value="{{ old('title', $goal->title) }}" required
                    class="glass-input w-full px-4 py-3 text-sm">
                @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">Description</label>
                <textarea name="description" rows="3" class="glass-input w-full px-4 py-3 text-sm resize-none">{{ old('description', $goal->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">Category</label>
                    <select name="category_id" class="glass-input w-full px-4 py-3 text-sm">
                        <option value="">No category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $goal->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">Priority</label>
                    <select name="priority" class="glass-input w-full px-4 py-3 text-sm">
                        @foreach(['low','medium','high','critical'] as $p)
                        <option value="{{ $p }}" {{ old('priority', $goal->priority) === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">Status</label>
                    <select name="status" class="glass-input w-full px-4 py-3 text-sm">
                        @foreach(['active','completed','paused','archived'] as $s)
                        <option value="{{ $s }}" {{ old('status', $goal->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-white/60 mb-1.5 font-medium">Progress (%)</label>
                    <input type="number" name="progress" value="{{ old('progress', $goal->progress) }}"
                        min="0" max="100" class="glass-input w-full px-4 py-3 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm text-white/60 mb-1.5 font-medium">Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline', $goal->deadline?->format('Y-m-d')) }}"
                    class="glass-input w-full px-4 py-3 text-sm">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 justify-center py-3.5">Save Changes</button>
                <a href="{{ route('goals.show', $goal) }}" class="btn-glass px-6 py-3.5">Cancel</a>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>
