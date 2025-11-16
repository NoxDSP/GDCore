# Changelog

All notable changes to GDCore will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- GitHub-ready project structure
- Issue and PR templates
- GitHub Actions workflows
- Security policy
- Comprehensive contributing guidelines

## [1.0.0] - 2025-11-16

### Added
- 🎮 **40+ Geometry Dash endpoint files** (all GD versions 1.0-2.2)
  - Account management (register, login, settings, cloud saves)
  - Level management (upload, download, search, rate, delete)
  - User profiles and statistics
  - Comments (level and profile)
  - Social features (friends, messages)
  - Leaderboards (top, creators, friends)
  - Daily/weekly levels
  - Map packs and gauntlets
  - Rewards system
  - Moderator tools

- 🌐 **Web Admin Panel**
  - Modern, responsive dashboard
  - User management (ban, promote, search)
  - Level management (rate, feature, delete)
  - Statistics and analytics
  - Role-based access control
  - Session management with auto-timeout

- 🤖 **Discord Bot**
  - Server statistics command
  - User lookup with rankings
  - Level search
  - Leaderboard display
  - Recent levels feed
  - Auto-updating status
  - Both slash commands and prefix commands

- 🔐 **Security Features**
  - Prepared statements for all database queries
  - Bcrypt password hashing
  - XOR encryption for GD protocol compatibility
  - Security headers (XSS, CSRF, clickjacking protection)
  - Rate limiting infrastructure
  - IP tracking and ban system
  - Secure session management

- 🛠️ **Modern Architecture**
  - PSR-4 autoloading
  - Composer dependency management
  - Environment-based configuration (.env)
  - Shared utility classes (Crypto, Hash, Response)
  - Clean separation of concerns
  - Direct endpoint access (Cvolton-compatible)

- 📚 **Documentation**
  - Comprehensive README
  - Installation guide
  - Admin panel documentation
  - Discord bot setup guide
  - Security best practices
  - Code examples

- 🔧 **Developer Tools**
  - Key generation script
  - Database connection tester
  - Update system structure
  - Comprehensive .gitignore
  - Development environment configuration

### Changed
- Improved code organization from flat structure to hybrid approach
- Enhanced error handling with secure logging
- Modernized database connection (mysqli → PDO)
- Updated configuration system (PHP files → .env)

### Security
- All SQL queries use prepared statements
- Passwords hashed with bcrypt (GDPS compatible)
- Admin panel with role-based access
- Security headers on all responses
- Input validation on all endpoints
- Rate limiting ready for implementation

## [0.1.0] - Initial Development

### Added
- Basic endpoint structure
- Database schema
- Initial utility classes

---

## Release Types

- **Major (x.0.0)**: Breaking changes, major new features
- **Minor (0.x.0)**: New features, backwards compatible
- **Patch (0.0.x)**: Bug fixes, small improvements

## Links

- [GitHub Repository](https://github.com/NoxDSP/GDCore)
- [Issue Tracker](https://github.com/NoxDSP/GDCore/issues)
- [Pull Requests](https://github.com/NoxDSP/GDCore/pulls)

[Unreleased]: https://github.com/NoxDSP/GDCore/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/NoxDSP/GDCore/releases/tag/v1.0.0
