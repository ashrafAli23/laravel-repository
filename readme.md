
# Laravel Repository

This is a package that help to implement repository pattern in laravel

## Installation

The first step is using composer to install the package and automatically update your composer.json file, you can do this by running:

```bash
  composer require 0x17/laravel-repository
```
    
## Usage/Examples

create dto
```php
  php artisan make:dto User
```

create service class
```php
  php artisan make:service User
```

create repository class
```php
  php artisan make:repository User
```

Register UserRepository:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\IUserRepository;
use App\Repositories\UserRepository;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IUserRepository::class, function () {
            return new UserRepository(new User());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
```


