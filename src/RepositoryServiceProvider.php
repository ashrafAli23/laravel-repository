<?php

declare(strict_types=1);

namespace Laravel\Repository;

use Illuminate\Support\ServiceProvider;
use Laravel\Repository\Commands\CreateDtoCommand;
use Laravel\Repository\Commands\CreateRepositoryCommand;
use Laravel\Repository\Commands\CreateServiceCommand;

class RepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateRepositoryCommand::class,
                CreateServiceCommand::class,
                CreateDtoCommand::class,
            ]);
        }
    }

    public function register()
    {
    }
}
