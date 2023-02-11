<?php

namespace SmashedEgg\LaravelInMemoryAuth;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
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
        $this->app->extend('memory', function(Application $app) {
            /** @var Repository $config */
            $config = $app->make('config');

            return new InMemoryUserProvider(
                $app->make('hash'),
                $config->get('auth.memory.users', []),
                $config->get('auth.memory.model', 'Illuminate\Auth\GenericUser')
            );
        });

        $this->app->bind(HashPasswordCommand::class, function(Application $app) {
            return new HashPasswordCommand($app->make('hash'));
        });

        $this->commands([
            HashPasswordCommand::class
        ]);
    }
}
