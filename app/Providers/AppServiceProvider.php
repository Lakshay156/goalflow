<?php

namespace App\Providers;

use App\Models\Goal;
use App\Policies\GoalPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Goal::class, GoalPolicy::class);
    }
}
