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
    }

    public function getId(): string
    {
        // Lazily generate so callers outside the middleware pipeline (e.g. Telescope
        // tags) always get a valid ID, even before handle() has run this request.
        if (! isset($this->id)) {
            $this->id = $this->generate();
        }

        return $this->id;
    }

    public function handle(Request $request, Closure $next): mixed
    {
        if (! $this->config->isEnabled()) {
            return $next($request);
        }

        // Regenerate per request. The service is a container singleton, so under
        // Octane/Swoole the constructor runs once and would otherwise reuse one ID
        // across every request.
        $this->id = $this->generate();

        if ($this->config->isAcceptRequestHeadersEnabled() && $request->headers->has($this->key)) {
            $incoming = (string) $request->headers->get($this->key);

            // Only trust an inbound ID that is safe to echo into headers, logs and
            // config: non-empty, bounded length, restricted charset (no CR/LF).
            if ($incoming !== '' && strlen($incoming) <= 128 && preg_match('/^[A-Za-z0-9\-_.]+$/', $incoming)) {
                $this->id = $incoming;
            }
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
