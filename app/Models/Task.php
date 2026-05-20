<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id', 'user_id', 'title', 'description',
        'is_completed', 'sort_order', 'due_date', 'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    public function goal() { return $this->belongsTo(Goal::class); }
    public function user() { return $this->belongsTo(User::class); }
}
