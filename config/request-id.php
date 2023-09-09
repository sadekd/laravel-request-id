<?php

return [
    'enabled' => env('REQUEST_ID_ENABLED', true),

    'key' => 'x-request-id',

    'accept_request_headers' => false,
    'request_headers' => true,

    'config' => false,

    'log_context' => true,

    'response_headers' => true,

    'generator' => 'ulid',
];
