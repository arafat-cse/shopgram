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

    'internal_key' => env('CHAT_INTERNAL_KEY', 'changeme'),
    'node_url' => env('CHAT_NODE_URL', 'http://localhost:3001'),
];
