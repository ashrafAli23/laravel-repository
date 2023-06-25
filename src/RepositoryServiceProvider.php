<?php

declare(strict_types=1);

namespace Laravel\Repository;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateRepository::class,
            ]);
        }
    }

    public function register()
    {
    }
}
