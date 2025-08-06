<?php

namespace SmashedEgg\LaravelInMemoryAuth;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class InMemoryUserProvider implements UserProvider
{
    public function __construct(
        protected Hasher $hasher,
        protected string $usernameField,
        protected array $users,
        protected string $model = User::class
    ) {}

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     * @return Authenticatable|null
     */
    public function retrieveById($identifier): ?Authenticatable
    {
        foreach ($this->users as $username => $fields) {
            if ($fields['id'] === $identifier) {
                return $this->getUser($username, $this->users[$username]);
            }
        }

        return null;
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed $identifier
     * @param string $token
     * @return Authenticatable|null
     */
    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        $user = $this->retrieveById($identifier);

        return $user && $user->getRememberToken() && hash_equals($user->getRememberToken(), $token)
            ? $user : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param Authenticatable $user
     * @param string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token): void
    {
        $this->users[$user->{$this->usernameField}][$user->getRememberTokenName()] = $token;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $username = $credentials[$this->usernameField];

        if ( ! isset($this->users[$username])) {
            return null;
        }

        return $this->getUser($username, $this->users[$username]);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return $this->hasher->check(
            $credentials['password'], $user->getAuthPassword()
        );
    }

    /**
     * Get the generic user.
     *
     * @param string $username
     * @param array $fields
     * @return User
     */
    protected function getUser(string $username, array $fields = []): User
    {
        $fields['username'] = $username;
        $model = $this->getUserClass();
        return new $model($fields);
    }

    /**
     * Rehash the user's password if required and supported.
     *
     * Required for Laravel 12+
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @param  bool  $force
     * @return void
     */
    public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false)
    {
        // NOOP
    }

    /**
     * @return string
     */
    protected function getUserClass(): string
    {
        return $this->model;
    }
}
