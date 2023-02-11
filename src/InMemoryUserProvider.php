<?php

namespace SmashedEgg\LaravelInMemoryAuth;

use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class InMemoryUserProvider implements UserProvider
{
    public function __construct(
        protected Hasher $hasher,
        protected array $users,
        protected string $model = GenericUser::class
    ) {}

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     * @return Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        foreach ($this->users as $username => $fields) {
            if ($fields['id'] === $identifier) {
                return $this->getGenericUser($username, $this->users[$username]);
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
    public function retrieveByToken($identifier, $token)
    {
        if ( ! $user = $this->retrieveById($identifier)) {
            return null;
        }

        if ($token === $user->getRememberToken()) {
            return $user;
        }

        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $this->users[$user->username][$user->getRememberTokenName()] = $token;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $username = $credentials['username'];

        if ( ! isset($this->users[$username])) {
            return null;
        }

        return $this->getGenericUser($username, $this->users[$username]);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    /**
     * Get the generic user.
     *
     * @param string $username
     * @param array $fields
     * @return GenericUser
     */
    protected function getGenericUser($username, array $fields = [])
    {
        $fields['username'] = $username;
        $model = $this->getUserClass();
        return new $model($fields);
    }

    /**
     * @return string
     */
    protected function getUserClass()
    {
        return $this->model;
    }
}
