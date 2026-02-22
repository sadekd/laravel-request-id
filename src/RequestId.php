<?php

namespace SadekD\LaravelRequestId;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RequestId
{
    private readonly string $key;

    private string $id;

    public function __construct(
        private readonly RequestIdConfig $config,
    ) {
        $this->key = $this->config->getKey();
        $this->id = $this->generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function handle(Request $request, Closure $next): mixed
    {
        if (! $this->config->isEnabled()) {
            return $next($request);
        }

        if ($this->config->isAcceptRequestHeadersEnabled() && $request->headers->has($this->key)) {
            $this->id = $request->headers->get($this->key);
        }

        if ($this->config->isRequestHeadersEnabled()) {
            $request->headers->set($this->key, $this->id);
        }

        if ($this->config->isConfigEnabled()) {
            config([$this->key => $this->id]);
        }

        if ($this->config->isLogContextEnabled()) {
            $context = [
                $this->key => $this->id,
                'hostname' => $request->host(),
                'url' => $request->url(),
            ];

            if (version_compare(app()->version(), '11', '>=')) {
                Context::add($context);
            } else {
                Log::withContext($context);
            }
        }

        if ($this->config->isResponseHeadersEnabled()) {
            $response = $next($request);

            $response->headers->set($this->key, $this->id);

            return $response;
        }

        return $next($request);
    }

    private function generate(): string
    {
        return Str::{$this->config->getGenerator()}();
    }
}
