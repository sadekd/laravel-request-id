<?php

namespace SadekD\LaravelRequestId;

use Illuminate\Support\Facades\App;
use SadekD\LaravelRequestId\Http\Middleware\RequestId as RequestIdMiddleware;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelRequestIdServiceProvider extends PackageServiceProvider
{
    function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-request-id')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        $this->app['router']->aliasMiddleware('request-id', RequestIdMiddleware::class);

        $this->app->singleton(RequestId::class, function ($app) {
            return new RequestId(
                $app['config']['request-id'], // $app->make('config')->get('request-id')
            );
        });
    }
}
