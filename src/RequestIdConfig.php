<?php

namespace SadekD\LaravelRequestId;

class RequestIdConfig
{
    public function __construct(
        private readonly array $config,
    )
    {
    }

    public function isEnabled(): bool
    {
        return $this->config['enabled'];
    }

    public function isAcceptRequestHeadersEnabled(): bool
    {
        return $this->config['accept_request_headers'];
    }

    public function isRequestHeadersEnabled(): bool
    {
        return $this->config['request_headers'];
    }

    public function isResponseHeadersEnabled(): bool
    {
        return $this->config['response_headers'];
    }

    public function isLogContextEnabled(): bool
    {
        return $this->config['log_context'];
    }

    public function isConfigEnabled(): bool
    {
        return $this->config['config'];
    }

    public function getKey(): string
    {
        return $this->config['key'];
    }

    public function getGenerator(): string
    {
        return $this->config['generator'];
    }

    public function isTelescopeTagsEnabled(): bool
    {
        return $this->config['enable_telescope_tags'];
    }
}
