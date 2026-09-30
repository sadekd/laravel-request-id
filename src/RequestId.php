<?php

namespace SadekD\LaravelRequestId;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use SadekD\IdGenerator\IdGenerator;
use SadekD\RequestId\RequestIdConfig;
use SadekD\RequestId\RequestIdValidator;

class RequestId
{
    private readonly string $key;

    private string $id;

    public function __construct(
        private readonly RequestIdConfig $config,
        private readonly IdGenerator $generator = new IdGenerator,
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

            if (RequestIdValidator::isValid($incoming)) {
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
        return $this->generator->generate($this->config->getGenerator());
    }
}
