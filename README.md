# 🎮 GDCore - Geometry Dash Private Server

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Discord.js](https://img.shields.io/badge/Discord.js-14.x-5865F2?logo=discord&logoColor=white)](https://discord.js.org/)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)

A **complete** and **production-ready** Geometry Dash Private Server with **actual GD endpoint files**, modern PHP utilities, web admin panel, and Discord bot integration.

Based on [Cvolton's GMDprivateServer](https://github.com/Cvolton/GMDprivateServer) architecture with enhanced security, maintainability, and features.

## Features

- **Full GD Support**: Compatible with Geometry Dash versions 1.0 - 2.2
- **Real GD Endpoints**: Individual PHP files matching official GD server structure
- **Modern Utilities**: Encryption, hashing, and database management with PSR-4 autoloading
- **Secure**: XOR encryption, cloud save encryption, bcrypt password hashing
- **Complete Feature Set**:
  - Level upload/download/search (all GD versions)
  - Account system with registration and login
  - Profile customization and statistics
  - Comments and ratings
  - Private messaging and friend system
  - Daily/weekly levels
  - Map packs and gauntlets
  - Leaderboards (top, creators, friends)
  - Rewards system
  - Moderator tools

## Requirements

- PHP 7.4 or higher
- MySQL/MariaDB 5.7+
- Apache/Nginx web server
- Composer

## Installation

1. Clone this repository
2. Install dependencies:
   ```bash
   composer install
   ```

3. Create database and import schema:
   ```bash
   mysql -u root -p < database/schema.sql
   ```

4. Copy `.env.example` to `.env` and configure:
   ```bash
   cp .env.example .env
   ```

5. Edit `.env` with your database credentials and settings

6. Point your web server to the project root

7. Edit Geometry Dash executable to point to your server URL:
   - Replace `http://www.boomlings.com/database/` with `http://yourdomain.com/database/`
   - For GD 2.1+, some URLs are base64 encoded

## Quick Test

Test your installation:

```bash
# Test login endpoint
curl -X POST http://localhost/database/accounts/loginGJAccount.php \
  -d "userName=admin&password=admin123"

# Should return: accountID,userID (e.g., "1,1")
```

## Project Structure

```
GDCore/
├── database/              # All GD endpoint files (actual PHP endpoints)
│   ├── accounts/         # Account management endpoints
│   ├── uploadGJLevel21.php
│   ├── downloadGJLevel22.php
│   ├── getGJLevels21.php
│   └── ... (40+ endpoint files)
├── config/               # Shared configuration and security
├── src/                  # Utility classes (Crypto, Hash, etc.)
├── database/schema.sql   # Database structure
└── tools/                # Helper scripts
```

## Endpoints

See [ENDPOINTS.md](ENDPOINTS.md) for complete endpoint documentation.

## Updating

See `_updates/README.md` for update instructions.

## 📸 Screenshots

### Web Admin Panel
![Dashboard](https://via.placeholder.com/800x400?text=Admin+Dashboard+Preview)

### Discord Bot
![Bot Commands](https://via.placeholder.com/800x400?text=Discord+Bot+Preview)

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

## 🐛 Bug Reports & Feature Requests

- **Bug reports:** [Open an issue](../../issues/new?template=bug_report.md)
- **Feature requests:** [Open an issue](../../issues/new?template=feature_request.md)

## 💬 Community & Support

- **Discord:** [Join our server](#) (Add your Discord invite)
- **Issues:** [GitHub Issues](../../issues)
- **Discussions:** [GitHub Discussions](../../discussions)

## 📄 License

This project is licensed under the **GNU General Public License v3.0** - see the [LICENSE](LICENSE) file for details.

## 🙏 Credits & Acknowledgments

- **Based on:** [Cvolton's GMDprivateServer](https://github.com/Cvolton/GMDprivateServer)
- **Account settings base:** someguy28
- **XOR encryption:** Community implementation
- **Cloud save encryption:** [Defuse PHP-Encryption](https://github.com/defuse/php-encryption)
- **Hash generation:** pavlukivan, Italian APK Downloader
- **Contributors:** [All contributors](../../graphs/contributors)

## ⭐ Star History

If you find GDCore useful, please consider giving it a star!

[![Star History Chart](https://api.star-history.com/svg?repos=NoxDSP/GDCore&type=Date)](https://star-history.com/#NoxDSP/GDCore&Date)

---

<p align="center">Made with ❤️ by the NoxDSP</p>
<p align="center">
  <a href="../../">Home</a> •
  <a href="../../issues">Issues</a> •
  <a href="../../wiki">Wiki</a> •
  <a href="CONTRIBUTING.md">Contributing</a>
</p>
