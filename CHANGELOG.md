# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [1.1.4] - 2025-07-17

### Changed

- Made the `user` relation in `UserSession` nullable
- Added `onDelete: "SET NULL"` to avoid foreign key errors when a `User` is deleted

### Migration

- Run `php bin/console make:migration && php bin/console doctrine:migrations:migrate` to update the foreign key and make `user_id` nullable

### Notes

- If you want user sessions to be deleted automatically when a `User` is deleted, override the relation with `onDelete: "CASCADE"` in your project

## [1.1.3] - 2025-07-08

### Added

- `createSession()` now accepts a custom data array to generate the device fingerprint
- `generateDeviceFingerprint()` can directly receive values to hash

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
