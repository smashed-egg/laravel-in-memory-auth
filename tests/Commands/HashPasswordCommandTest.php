<?php

namespace SmashedEgg\LaravelInMemoryAuth\Tests\Commands;

use Illuminate\Contracts\Foundation\Application;
use SmashedEgg\LaravelInMemoryAuth\AuthServiceProvider;
use SmashedEgg\LaravelInMemoryAuth\Tests\TestCase;

class HashPasswordCommandTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            AuthServiceProvider::class,
        ];
    }

    public function testCommand()
    {
        $this->artisan('smashed-egg:hash:password', [
            'password' => 'mypassword'
        ])->assertOk();
    }
}
