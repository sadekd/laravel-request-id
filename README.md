# Laravel Request ID (XRID)

This Laravel package to help you add [X-Request-ID](https://http.dev/x-request-id) feature into your Laravel app

> The HTTP X-Request-ID request header is an optional and unofficial HTTP header, used to trace individual HTTP requests from the client to the server and back again. It allows the client and server to correlate each HTTP request.

## Installation

```bash
composer require sadekd/laravel-request-id
```

### Config

```bash
php artisan vendor:publish --provider="SadekD\LaravelRequestId\LaravelRequestIdServiceProvider"
```

Configuration is commented in the [config file](config/request-id.php)

## Usage

Edit `App\Http\Kernel.php` and add it as middleware into any stack or use it single
(overall higher is better - in case of fail, you have XRID in logs, response, etc.)

```php
protected $middleware = [
    \SadekD\LaravelRequestId\Http\Middleware\RequestId::class,
     // or just 'request-id',
];
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
