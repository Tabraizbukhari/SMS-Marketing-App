<?php

namespace App\Providers;

use App\Channels\DatabaseChannel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        $this->app->instance(IlluminateDatabaseChannel::class, new DatabaseChannel());
    }
}
