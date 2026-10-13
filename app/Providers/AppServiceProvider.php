<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Task;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

     
    public function boot(): void
    {
        Task::observe(\App\Observers\TaskObserver::class);
    }
}
