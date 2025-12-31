<?php

use Sockeon\Sockeon\Config\ServerConfig;

/*
|--------------------------------------------------------------------------
| Sockeon WebSocket Server Configuration
|--------------------------------------------------------------------------
|
| This file contains the ServerConfig object that configures your Sockeon
| WebSocket server. You can publish this file to customize all available
| server options including CORS, rate limiting, authentication, and more.
|
| Publish this file using:
| php artisan vendor:publish --tag=sockeon-server
|
*/

return new ServerConfig([
    /*
    |--------------------------------------------------------------------------
    | Basic Server Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the fundamental server settings including host, port, and
    | debug mode. These can be controlled via environment variables.
    |
    */

    'host' => env('SOCKEON_HOST', '0.0.0.0'),
    'port' => env('SOCKEON_PORT', 8080),
    'debug' => env('SOCKEON_DEBUG', env('APP_DEBUG', false)),

    /*
    |--------------------------------------------------------------------------
    | Advanced Server Options
    |--------------------------------------------------------------------------
    |
    | queue_file: Path to queue file for message persistence (optional)
    | auth_key: Authentication key for secure connections (optional)
    | trust_proxy: Array of trusted proxy IP addresses or CIDR ranges
    | health_check_path: Endpoint for health checks (default: /health)
    |
    */

    'queue_file' => env('SOCKEON_QUEUE_FILE', null),
    'auth_key' => env('SOCKEON_AUTH_KEY', null),
    'trust_proxy' => [],
    'health_check_path' => env('SOCKEON_HEALTH_CHECK_PATH', '/health'),

    /*
    |--------------------------------------------------------------------------
    | CORS (Cross-Origin Resource Sharing) Configuration
    |--------------------------------------------------------------------------
    |
    | Configure CORS settings to control which origins can access your
    | WebSocket server from browsers.
    |
    | allowed_origins: Array of allowed origins or ['*'] for all
    | allowed_methods: HTTP methods allowed for cross-origin requests
    | allowed_headers: HTTP headers allowed in actual requests
    | allow_credentials: Whether credentials are allowed
    | max_age: How long browsers cache preflight responses (seconds)
    |
    */

    'allowed_origins' => ['*'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'allowed_headers' => ['*'],
    'allow_credentials' => true,
    'max_age' => 86400, // 24 hours

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting to protect your server from abuse. You can
    | set limits for HTTP requests, WebSocket messages, and connections.
    |
    | enabled: Global enable/disable for rate limiting
    | maxHttpRequestsPerIp: Max HTTP requests per IP in time window
    | httpTimeWindow: Time window for HTTP rate limiting (seconds)
    | maxWebSocketMessagesPerClient: Max WebSocket messages per client
    | webSocketTimeWindow: Time window for WebSocket rate limiting (seconds)
    | maxConnectionsPerIp: Max concurrent connections from single IP
    | connectionTimeWindow: Time window for connection rate limiting
    | maxGlobalConnections: Global limit on total connections
    | burstAllowance: Temporary burst allowance beyond normal limit
    | cleanupInterval: How often to clean up expired rate limit data
    | whitelist: Array of IP addresses exempt from rate limiting
    |
    */

    'rate_limiting' => [
        'enabled' => env('SOCKEON_RATE_LIMIT_ENABLED', false),
        'maxHttpRequestsPerIp' => 60,
        'httpTimeWindow' => 60,
        'maxWebSocketMessagesPerClient' => 100,
        'webSocketTimeWindow' => 60,
        'maxConnectionsPerIp' => 10,
        'connectionTimeWindow' => 60,
        'maxGlobalConnections' => 1000,
        'burstAllowance' => 10,
        'cleanupInterval' => 300,
        'whitelist' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Logger (Optional)
    |--------------------------------------------------------------------------
    |
    | You can provide a custom logger instance that implements
    | Psr\Log\LoggerInterface. Leave commented to use default logger.
    |
    | Example:
    | 'logger' => app(\Psr\Log\LoggerInterface::class),
    |
    */

    // 'logger' => null,
]);
