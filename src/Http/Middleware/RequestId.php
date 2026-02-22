<?php

namespace SadekD\LaravelRequestId\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequestId
{
    public function __construct(
        private readonly \SadekD\LaravelRequestId\RequestId $requestId
    )
    {
    }

    public function handle(Request $request, Closure $next): mixed
    {
        return $this->requestId->handle($request, $next);
    }
}
