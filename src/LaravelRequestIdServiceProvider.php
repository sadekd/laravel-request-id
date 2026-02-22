<?php

namespace SadekD\LaravelRequestId;

use SadekD\LaravelRequestId\Http\Middleware\RequestId as RequestIdMiddleware;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelRequestIdServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-request-id')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(RequestIdConfig::class, function ($app) {
            return new RequestIdConfig(
                $app['config']['request-id'],
            );
        });

        $this->app->singleton(RequestId::class);

        $this->app->alias(RequestId::class, 'request-id');
    }

    public function packageBooted(): void
    {
        $this->app['router']->aliasMiddleware('request-id', RequestIdMiddleware::class);

        if (class_exists(\Laravel\Telescope\Telescope::class) && $this->app->make(RequestIdConfig::class)->isTelescopeTagsEnabled()) {
            \Laravel\Telescope\Telescope::tag(function (\Laravel\Telescope\IncomingEntry $entry) {
                return request()->hasHeader('X-Request-ID')
                    ? ['XRID:'.request()->header('X-Request-ID')]
                    : [];
            });
        }
    }

    public function provides(): array
    {
        return ['request-id'];
    }
}
