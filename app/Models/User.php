<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'bio', 'theme',
        'streak_days', 'last_active_at',
        'onboarding_completed', 'goal_type', 'productivity_style',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'     => 'datetime',
        'last_active_at'        => 'datetime',
        'password'              => 'hashed',
        'onboarding_completed'  => 'boolean',
    ];

    public function goals()       { return $this->hasMany(Goal::class); }
    public function categories()  { return $this->hasMany(Category::class); }
    public function tasks()       { return $this->hasMany(Task::class); }
    public function milestones()  { return $this->hasMany(Milestone::class); }
    public function notifications() { return $this->hasMany(AppNotification::class); }

    public function activeGoals()    { return $this->goals()->where('status', 'active'); }
    public function completedGoals() { return $this->goals()->where('status', 'completed'); }
}
