<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chat Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the real-time order chat feature.
    |
    */

    'internal_key'        => env('CHAT_INTERNAL_KEY', 'changeme'),
    'node_url'            => env('CHAT_NODE_URL', 'http://localhost:3001'),
    // Used by Laravel server → Node.js (always loopback, never mobile IP)
    'node_internal_url'   => env('CHAT_NODE_INTERNAL_URL', 'http://127.0.0.1:3001'),
];
