<?php

namespace App\Providers;

use App\Repositories\Leave\LeaveRepository;
use App\Repositories\Leave\LeaveRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            LeaveRepositoryInterface::class,
            LeaveRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
