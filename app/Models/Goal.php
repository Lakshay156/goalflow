<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'description',
        'priority', 'status', 'progress', 'deadline', 'completed_at',
    ];

    protected $casts = [
        'deadline'     => 'date',
        'completed_at' => 'datetime',
    ];

    public function user()       { return $this->belongsTo(User::class); }
    public function category()   { return $this->belongsTo(Category::class); }
    public function tasks()      { return $this->hasMany(Task::class)->orderBy('sort_order'); }
    public function milestones() { return $this->hasMany(Milestone::class)->orderBy('target_date'); }

    public function completedTasks()      { return $this->tasks()->where('is_completed', true); }
    public function completedMilestones() { return $this->milestones()->where('is_completed', true); }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low'      => '#10b981',
            'medium'   => '#f59e0b',
            'high'     => '#f97316',
            'critical' => '#ef4444',
            default    => '#6366f1',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active'    => '#6366f1',
            'completed' => '#10b981',
            'paused'    => '#f59e0b',
            'archived'  => '#6b7280',
            default     => '#6366f1',
        };
    }

    public function getDaysLeftAttribute(): ?int
    {
        if (!$this->deadline) return null;
        return max(0, now()->diffInDays($this->deadline, false));
    }

    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast() && $this->status !== 'completed';
    }
}
