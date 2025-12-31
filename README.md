# Laravel Sockeon

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elshiha/laravel-sockeon.svg?style=flat-square)](https://packagist.org/packages/elshiha/laravel-sockeon)
[![Total Downloads](https://img.shields.io/packagist/dt/elshiha/laravel-sockeon.svg?style=flat-square)](https://packagist.org/packages/elshiha/laravel-sockeon)
[![License](https://img.shields.io/packagist/l/elshiha/laravel-sockeon.svg?style=flat-square)](https://packagist.org/packages/elshiha/laravel-sockeon)

Laravel integration for [Sockeon](https://sockeon.com) WebSocket server with artisan commands for easy development and deployment.

## Features

- 🚀 **Easy Setup** - Get WebSocket server running in minutes
- 🛠️ **Artisan Commands** - Laravel-style commands for server management
- 📝 **Code Generation** - Generate WebSocket controllers with `sockeon:make`
- 📊 **Log Tailing** - Built-in log viewer with `sockeon:logs`
- ⚙️ **Configurable** - Environment-based configuration
- 🔧 **Auto-Discovery** - Zero configuration installation

## Requirements

- PHP 8.1 or higher
- Laravel 11.x or 12.x
- Sockeon 2.0 or higher

## Installation

Install the package via Composer:

```bash
composer require elshiha/laravel-sockeon
```

The package will automatically register itself via Laravel's package auto-discovery.

### Publish Configuration (Optional)

Publish the configuration files:
```bash
php artisan vendor:publish --tag=sockeon-config
```

This creates:
- `config/sockeon.php` - Controllers and logging settings
- `config/sockeon-server.php` - Full server configuration with CORS, rate limiting, and more

### Publish Stubs (Optional)

```bash
php artisan vendor:publish --tag=sockeon-stubs
```

This allows you to customize the controller template used by `sockeon:make`.

## Configuration

### Basic Configuration

Add these variables to your `.env` file:

```env
SOCKEON_HOST=0.0.0.0
SOCKEON_PORT=8080
SOCKEON_DEBUG=false
SOCKEON_LOG_FILE=
```

### Advanced Server Configuration

For advanced features like **CORS**, **rate limiting**, **authentication**, and **proxy support**, publish the configuration and edit `config/sockeon-server.php`:

```bash
php artisan vendor:publish --tag=sockeon-config
```

The `config/sockeon-server.php` file allows you to configure:

#### Available Options

**Basic Settings:**
- `host`, `port`, `debug` - Server fundamentals
- `queue_file` - Message persistence
- `auth_key` - Authentication key
- `health_check_path` - Health check endpoint

**CORS Configuration:**
- `allowed_origins` - Allowed origins (default: `['*']`)
- `allowed_methods` - HTTP methods
- `allowed_headers` - Allowed headers
- `allow_credentials` - Allow credentials
- `max_age` - Preflight cache duration

**Rate Limiting:**
- `enabled` - Enable/disable rate limiting
- `maxHttpRequestsPerIp` - HTTP request limit per IP
- `maxWebSocketMessagesPerClient` - WebSocket message limit
- `maxConnectionsPerIp` - Connection limit per IP
- `maxGlobalConnections` - Global connection limit
- `whitelist` - IP whitelist

**Security:**
- `trust_proxy` - Trusted proxy IPs/CIDR ranges

### Environment Variables

| Option | Description | Default |
|--------|-------------|---------|
| `SOCKEON_HOST` | WebSocket server host | `0.0.0.0` |
| `SOCKEON_PORT` | WebSocket server port | `8080` |
| `SOCKEON_DEBUG` | Enable debug mode | `false` |
| `SOCKEON_LOG_FILE` | Custom log file path | `storage/logs/sockeon-websocket.log` |
| `SOCKEON_AUTH_KEY` | Authentication key | `null` |
| `SOCKEON_RATE_LIMIT_ENABLED` | Enable rate limiting | `false` |

## Usage

### 1. Create a WebSocket Controller

Generate a new WebSocket controller:

```bash
php artisan sockeon:make ChatController
```

This creates a controller at `app/WebSocket/ChatController.php`:

```php
<?php

namespace App\WebSocket;

use Sockeon\Sockeon\Controllers\SocketController;
use Sockeon\Sockeon\WebSocket\Attributes\OnConnect;
use Sockeon\Sockeon\WebSocket\Attributes\OnDisconnect;
use Sockeon\Sockeon\WebSocket\Attributes\SocketOn;

class ChatController extends SocketController
{
    #[OnConnect]
    public function onConnect(int $clientId): void
    {
        echo "Client {$clientId} connected\n";
        
        $this->emit($clientId, 'welcome', [
            'message' => 'Welcome to the server!',
            'clientId' => $clientId
        ]);
    }

    #[OnDisconnect]
    public function onDisconnect(int $clientId): void
    {
        echo "Client {$clientId} disconnected\n";
    }

    #[SocketOn('chat.message')]
    public function handleMessage(int $clientId, array $data): void
    {
        $this->broadcast('chat.message', [
            'from' => $clientId,
            'message' => $data['message'],
            'timestamp' => time()
        ]);
    }
}
```

### 2. Register Your Controller

Add your controller to `config/sockeon.php`:

```php
'controllers' => [
    App\WebSocket\ChatController::class,
],
```

### 3. Start the WebSocket Server

```bash
php artisan sockeon:serve
```

Output:
```
Registered controller: App\WebSocket\ChatController
Starting WebSocket server on ws://0.0.0.0:8080
Press Ctrl+C to stop
```

### 4. View Server Logs

Show last 50 lines:
```bash
php artisan sockeon:logs
```

Follow logs in real-time:
```bash
php artisan sockeon:logs --follow
```

Show custom number of lines:
```bash
php artisan sockeon:logs --lines=100
```

## Available Commands

| Command | Description |
|---------|-------------|
| `sockeon:serve` | Start the WebSocket server |
| `sockeon:logs` | Display and tail server logs |
| `sockeon:make` | Generate a WebSocket controller |

## Client-Side Connection

Connect to your WebSocket server from JavaScript:

```javascript
// Development
const ws = new WebSocket('ws://localhost:8080');

// Production (behind Nginx proxy)
const ws = new WebSocket('wss://yourdomain.com/sockeon');

ws.onopen = () => {
    console.log('Connected to WebSocket server');
};

ws.onmessage = (event) => {
    const data = JSON.parse(event.data);
    console.log('Received:', data);
};

// Send a message
ws.send(JSON.stringify({
    event: 'chat.message',
    data: { message: 'Hello, World!' }
}));
```

## Production Deployment

For production deployment, you should:

1. **Use a process manager** like Supervisor to keep the WebSocket server running
2. **Configure Nginx** to proxy WebSocket connections with SSL/WSS
3. **Set proper environment variables** in your `.env` file

### Example Supervisor Configuration

```ini
[program:sockeon-websocket]
process_name=%(program_name)s
command=php /var/www/your-app/artisan sockeon:serve
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/your-app/storage/logs/sockeon-websocket.log
stopwaitsecs=3600
```

### Example Nginx Configuration

```nginx
location /sockeon {
    proxy_pass http://127.0.0.1:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_connect_timeout 7d;
    proxy_send_timeout 7d;
    proxy_read_timeout 7d;
    proxy_buffering off;
}
```

## Documentation

For more information about Sockeon:
- [Sockeon Documentation](https://sockeon.com/v2.0/)
- [Sockeon GitHub](https://github.com/sockeon/sockeon)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Credits

- [Mahmoud Elshiha](https://github.com/MahmoudElshiha)
- [All Contributors](../../contributors)
