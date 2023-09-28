<?php

namespace SmashedEgg\LaravelInMemoryAuth;

use Illuminate\Support\Arr;
use Illuminate\Auth\GenericUser;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use SmashedEgg\LaravelInMemoryAuth\Commands\HashPasswordCommand;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                HashPasswordCommand::class
            ]);
        }
    }

    public function register()
    {
        Auth::provider('memory', function(Application $app) {
            $memoryConfig = config('auth.providers.memory', []);

            return new InMemoryUserProvider(
                $app->make('hash'),
                Arr::get($memoryConfig, 'username_field', 'email'),
                Arr::get($memoryConfig, 'users', []),
                Arr::get($memoryConfig, 'model', GenericUser::class)
            );
        });

        $this->app->bind(HashPasswordCommand::class, fn(Application $app) => new HashPasswordCommand($app->make('hash')));
    }
}
