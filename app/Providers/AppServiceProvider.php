<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Contracts\UploadServiceContract;
use App\Services\UploadService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UploadServiceContract::class, UploadService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        Gate::define('is-admin', function (User $user) {
            return $user->hasRole('admin');
                });
    }

    
}
