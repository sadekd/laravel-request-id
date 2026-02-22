<?php

namespace SadekD\LaravelRequestId\Facades;

use Illuminate\Support\Facades\Facade;

class RequestId extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'request-id';
    }
}
