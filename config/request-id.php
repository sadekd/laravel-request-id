<?php

return [
    // enabled
    'enabled' => env('REQUEST_ID_ENABLED', true),

    // XRID key
    'key' => 'X-Request-ID',

    // set XRID from incoming request headers
    'accept_request_headers' => false,

    // set XRID to incoming request headers
    'request_headers' => true,

    // set XRID to config
    'config' => false,

    // set XRID to log context
    'log_context' => true,

    // set XRID to response headers
    'response_headers' => true,

    // XRID generator
    'generator' => 'ulid',
];
