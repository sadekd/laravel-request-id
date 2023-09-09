# Laravel Request ID

## Installation

Require this package with composer.

```bash
composer require sadekd/laravel-request-id
```

### Copy the package config to your local config with the publish command:

```bash
php artisan vendor:publish --provider="SadekD\LaravelRequestId\LaravelRequestIdServiceProvider"
```

## Usage

Edit `App\Http\Kernel.php` and add it into global middleware stack

```php
protected $middleware = [
    \SadekD\LaravelRequestId\Http\Middleware\RequestId::class,
     // or just 'request-id',
];
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
