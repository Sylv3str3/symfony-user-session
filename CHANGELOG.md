# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.2] - 2025-06-16

### Fixed

- Changed UUID column name from 'id' to 'session_id' to prevent conflicts
- Fixed database schema compatibility issues

## [1.1.0] - 2025-06-16

### Added

- Abstract entity class for extensibility
- Custom entity support via configuration
- Documentation for entity extension
- Type-safe entity handling in services

### Changed

- UserSession entity now extends AbstractUserSession
- Updated service layer to support custom entities
- Improved configuration validation
- Enhanced documentation with extension examples

## [1.0.0] - 2025-06-11

### Added

- Initial release
- Multi-device session management with fingerprinting
- JWT session tracking and revocation
- Configurable maximum sessions per user
- Session activity monitoring
- Automatic session cleanup
- Event system for session lifecycle
- Role-based access control for routes
- Pagination for session listings
- Device fingerprinting with configurable parameters

### Security

- Device fingerprinting for session identification
- Session revocation capabilities
- Protection against token reuse
- Role-based access control

[1.1.1]: https://github.com/username/symfony-user-session-bundle/compare/v1.1.0...v1.1.2
[1.1.0]: https://github.com/username/symfony-user-session-bundle/releases/tag/v1.0.0
