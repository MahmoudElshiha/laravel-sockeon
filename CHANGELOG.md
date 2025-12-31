# Changelog

All notable changes to `laravel-sockeon` will be documented in this file.

## [1.1.0] - 2025-12-31

### Added
- Publishable `sockeon-server.php` configuration file for advanced server features
- Full CORS configuration support (allowed_origins, methods, headers, credentials, max_age)
- Rate limiting configuration (HTTP requests, WebSocket messages, connections)
- Authentication key support (`auth_key`)
- Proxy support with trusted proxy configuration
- Health check endpoint configuration
- Queue file support for message persistence
- Comprehensive documentation for all ServerConfig options

### Changed
- ServerConfig now loaded from dedicated publishable file instead of inline creation
- Simplified command implementation by using published config object
- Updated README with comprehensive configuration documentation

## [1.0.0] - Initial Release

### Added
- Initial release
- `sockeon:serve` command to start WebSocket server
- `sockeon:logs` command to tail server logs
- `sockeon:make` command to generate WebSocket controllers
- Configuration file for server settings
- Controller stub template
