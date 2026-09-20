# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Laravel package (`sadekd/laravel-request-id`) that adds X-Request-ID header support for HTTP request tracing. It generates a unique ID per request and can propagate it through request/response headers, log context, and Laravel config.

## Development

This is a Laravel package (not a standalone app). There are no tests, build steps, or linting configured. To install dependencies:

```bash
composer install
```

Requires PHP ^8.1 and depends on `spatie/laravel-package-tools` for package scaffolding.

## Architecture

The package uses Spatie's `PackageServiceProvider` pattern. Key flow:

- **`LaravelRequestIdServiceProvider`** — Registers `RequestIdConfig` as a singleton from the `request-id` config, aliases `RequestId` as `'request-id'` in the container, registers the middleware alias, and optionally tags Telescope entries with the XRID.
- **`RequestId`** (src/RequestId.php) — Core service class. Holds the generated ID and contains the middleware `handle()` logic directly. The middleware class (`Http/Middleware/RequestId`) is a thin wrapper that delegates to this class.
- **`RequestIdConfig`** — Value object wrapping the config array with typed accessors for each feature toggle.
- **`Facades\RequestId`** — Facade resolving `'request-id'` from the container.

The middleware pipeline in `RequestId::handle()`:
1. Short-circuits if disabled
2. Accepts incoming X-Request-ID header if configured (`accept_request_headers`)
3. Sets ID on request headers if configured (`request_headers`)
4. Stores ID in Laravel config if configured (`config`)
5. Adds ID + hostname + URL to log context (uses `Context::add()` on Laravel 11+, `Log::withContext()` on older)
6. Sets ID on response headers if configured (`response_headers`)

ID generation delegates to `SadekD\IdGenerator\IdGenerator::generate()` (from the `sadekd/id-generator` package) where generator defaults to `'ulid'`.

## Config

Published config lives at `config/request-id.php`. Key options: `enabled`, `key` (header name), `accept_request_headers`, `request_headers`, `response_headers`, `log_context`, `config`, `generator` (`ulid` or `uuid`, via `sadekd/id-generator`).
