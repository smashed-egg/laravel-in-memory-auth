<?php

namespace SmashedEgg\LaravelInMemoryAuth\Tests;

use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Hashing\Hasher;
use SmashedEgg\LaravelInMemoryAuth\InMemoryUserProvider;

/**
 * Class InMemoryUserProviderTest
 *
 * @package SmashedEgg\LaravelInMemoryAuth\Tests
 */
class InMemoryUserProviderTest extends TestCase
{
    public function testProviderRetrieveById()
    {
        $provider = $this->getProvider($this->app->make('hash'));

        $this->assertInstanceOf(GenericUser::class, $provider->retrieveById(1));
        $this->assertNull($provider->retrieveById(2));
    }

    public function testProviderRetrieveByToken()
    {
        $provider = $this->getProvider($this->app->make('hash'));

        $this->assertInstanceOf(GenericUser::class, $provider->retrieveByToken(1, 'token'));
        $this->assertNull($provider->retrieveByToken(1, 'non_existent_token'));
        $this->assertNull($provider->retrieveByToken(2, 'non_existent_token'));
    }

    public function testProviderUpdateRememberToken()
    {
        $provider = $this->getProvider($this->app->make('hash'));

        $user = $provider->retrieveById(1);

        $provider->updateRememberToken($user, 'updated_token');
        $this->assertInstanceOf(GenericUser::class, $provider->retrieveByToken(1, 'updated_token'));
    }

    public function testProviderRetrieveByCredentials()
    {
        $provider = $this->getProvider($this->app->make('hash'));

        $this->assertInstanceOf(GenericUser::class, $provider->retrieveByCredentials([
            'username' => 'admin'
        ]));

        $this->assertNull($provider->retrieveByCredentials([
            'username' => 'foo'
        ]));
    }

    public function testProviderValidateCredentials()
    {
        $user = $this->createMock(GenericUser::class);
        $user->expects($this->once())
            ->method('getAuthPassword')
            ->willReturn('$2y$10$Mfusxb1546MFxQ4A1s4GE.OF/gFuI8Y6Hw9xnlZeiHtjDl0/pnXPK')
        ;

        $provider = $this->getProvider($this->app->make('hash'));

        $this->assertTrue($provider->validateCredentials($user, [
            'username' => 'admin',
            'password' => 'password',
        ]));
    }

    /**
     * @param Hasher $hasher
     * @return InMemoryUserProvider
     */
    protected function getProvider(Hasher $hasher)
    {
        return new InMemoryUserProvider($hasher, [
            'admin' => [
                'id' => 1,
                'password' => '$2y$10$Mfusxb1546MFxQ4A1s4GE.OF/gFuI8Y6Hw9xnlZeiHtjDl0/pnXPK',
                'remember_token' => 'token',
            ]
        ]);
    }

}
