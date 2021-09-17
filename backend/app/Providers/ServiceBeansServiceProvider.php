<?php

namespace App\Providers;

use App\Service\User\UserService;
use App\Service\User\UserServiceInterface;
use Illuminate\Support\ServiceProvider;

class ServiceBeansServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
