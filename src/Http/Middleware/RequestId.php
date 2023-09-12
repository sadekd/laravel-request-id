<?php

namespace SadekD\LaravelRequestId\Http\Middleware;

use Closure;
use SadekD\LaravelRequestId\RequestId as RequestIdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestId
{
    private string $key;

    public function __construct(
        private readonly RequestIdService $requestIdService
    )
    {
        $this->key = $this->requestIdService->getKey();
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$this->requestIdService->isEnabled()) {
            return $next($request);
        };

        $requestId = $this->requestIdService->isAcceptRequestHeadersEnabled()
            ? $request->headers->get($this->key, $this->requestIdService->generate())
            : $this->requestIdService->generate();

        if ($this->requestIdService->isRequestHeadersEnabled()) {
            $request->headers->set($this->key, $requestId);
        }

        if ($this->requestIdService->isConfigEnabled()) {
            config([$this->key => $requestId]);
        }

        if ($this->requestIdService->isLogContextEnabled()) {
            Log::withContext([$this->key => $requestId]);
        }

        if ($this->requestIdService->isResponseHeadersEnabled()) {
            $response = $next($request);

            $response->headers->set($this->key, $requestId);

            return $response;
        }

        return $next($request);
    }
}
