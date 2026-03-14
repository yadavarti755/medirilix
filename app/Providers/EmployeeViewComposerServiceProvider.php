<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EmployeeViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Bind the 'frontend.menu' data to the frontend layout
        view()->composer('layouts.employee_layout', function ($view) {
            $view->with('employeeDetails', auth()->user()->employee);
        });
    }
}
