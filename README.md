<p align="center">
  <img src="https://raw.githubusercontent.com/smashed-egg/.github/05d922c99f1a3bddea88339064534566b941eca9/profile/main.jpg" width="300">
</p>

# Laravel In Memory Auth Provider

*Still in development*

An In Memory User Auth Provider for Laravel 9+.

Allows you to Authenticate an admin area without the need for a database.
Great as a quick and temporary solution during development,
particularly if your site is mocked out and not let using a database.


## Requirements

* PHP 8.0.2+
* Laravel 9.0+

## Installation

To install this package please run:

```
composer require smashed-egg/laravel-in-memory-auth
```
## Configuration

### Setup config

In the `auth.php` config file you will need to set the driver:

```php
    'driver' => 'memory',
```

Add also setup your in memory users:

```php
    'memory' => [
        'model' => \Illuminate\Auth\GenericUser::class,
        'users' => [
    
            /*
             'username' => [
                'id' => 1,
                // Hashed password using the hasher service
                'password' => 'hashed_password',
            ],
             */
    
            'admin' => [
                'id' => 1,
                // Hashed password using the hasher service
                'password' => '$2y$10$Mfusxb1546MFxQ4A1s4GE.OF/gFuI8Y6Hw9xnlZeiHtjDl0/pnXPK',
            ],
        ],
    ],
```

You can add any properties you want making it easy to switch out the Auth drivers.

The package comes with a command for hashing passwords, making it easier to setup passwords, just run the following command to hash your password:

```shell
php artisan smashed-egg:hash:password mypassword

