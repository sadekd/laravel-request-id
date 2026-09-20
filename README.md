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

Register the middleware as high in the stack as possible (higher is better — on a
later failure you still have the XRID in logs, the response, etc.). The package
registers a `request-id` middleware alias automatically.

### Laravel 11+ (`bootstrap/app.php`)

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->prepend(\SadekD\LaravelRequestId\Http\Middleware\RequestId::class);
    // or: $middleware->prepend('request-id');
})
```

### Laravel 10 (`app/Http/Kernel.php`)

```php
protected $middleware = [
    \SadekD\LaravelRequestId\Http\Middleware\RequestId::class,
    // or just 'request-id',
];
```

### Reading the ID

```php
use SadekD\LaravelRequestId\Facades\RequestId;

RequestId::getId();   // current request's ID
app('request-id')->getId();
```

### Config options

Published to `config/request-id.php`:

| Key | Default | Description |
| --- | --- | --- |
| `enabled` | `true` | Master switch. |
| `key` | `X-Request-ID` | Header name (also used as the log-context / config key). |
| `accept_request_headers` | `false` | Trust an inbound `X-Request-ID` header. Validated: non-empty, ≤128 chars, `[A-Za-z0-9-_.]` only. |
| `request_headers` | `true` | Write the ID onto the incoming request headers. |
| `config` | `false` | Store the ID in Laravel config under `key`. |
| `log_context` | `true` | Add ID + hostname + URL to log context (`Context::add` on L11+, `Log::withContext` otherwise). |
| `response_headers` | `true` | Echo the ID on the response. |
| `generator` | `ulid` | `ulid` or `uuid` (via `sadekd/request-id`'s `RequestIdGenerator`). |
| `enable_telescope_tags` | `true` | Tag Telescope entries with the ID when Telescope is installed. |

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
