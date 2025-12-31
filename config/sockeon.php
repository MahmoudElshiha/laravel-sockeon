<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sockeon Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file contains settings for Sockeon WebSocket features.
    | For server configuration (host, port, CORS, rate limiting, etc.),
    | publish and edit the sockeon-server.php file:
    |
    | php artisan vendor:publish --tag=sockeon-server
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Log File
    |--------------------------------------------------------------------------
    |
    | Specify the log file path for Sockeon WebSocket server logs.
    | Leave empty to use the default Laravel log file.
    |
    */

    'log_file' => env('SOCKEON_LOG_FILE', storage_path('logs/sockeon-websocket.log')),

    /*
    |--------------------------------------------------------------------------
    | WebSocket Controllers
    |--------------------------------------------------------------------------
    |
    | Register your WebSocket controllers here. Each controller should extend
    | Sockeon\Sockeon\Controllers\SocketController and use attributes to
    | define event handlers.
    |
    | Example:
    |   App\WebSocket\ChatController::class,
    |
    */

    'controllers' => [
        // Register your controllers here
    ],
];
