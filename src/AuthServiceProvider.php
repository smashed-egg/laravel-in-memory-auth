<?php

namespace SmashedEgg\LaravelInMemoryAuth;

use Illuminate\Auth\GenericUser;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use SmashedEgg\LaravelInMemoryAuth\Commands\HashPasswordCommand;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Auth::provider('memory', function(Application $app) {
            return new InMemoryUserProvider(
                $app->make('hash'),
                config('auth.providers.memory.username_field', 'email'),
                config('auth.providers.memory.users', []),
                config('auth.providers.memory.model', GenericUser::class)
            );
        });

        $this->app->bind(HashPasswordCommand::class, fn(Application $app) => new HashPasswordCommand($app->make('hash')));

        $this->commands([
            HashPasswordCommand::class
        ]);
    }
}
