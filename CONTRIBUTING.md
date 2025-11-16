# Contributing to GDCore

First off, thank you for considering contributing to GDCore! 🎉

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Guidelines](#coding-guidelines)
- [Commit Guidelines](#commit-guidelines)
- [Pull Request Process](#pull-request-process)

## 📜 Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

### Our Standards

- **Be respectful** and inclusive
- **Be collaborative** and constructive
- **Focus on what is best** for the community
- **Show empathy** towards other community members

## 🤝 How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues. When creating a bug report, include:

- **Clear title** and description
- **Steps to reproduce**
- **Expected vs actual behavior**
- **Screenshots** (if applicable)
- **Environment details** (PHP version, MySQL version, OS)

### Suggesting Features

Feature requests are welcome! Please:

- **Check existing requests** first
- **Provide clear use cases**
- **Explain why** it would be useful
- **Consider implementation complexity**

### Code Contributions

1. **Fork** the repository
2. **Create a branch** (`git checkout -b feature/AmazingFeature`)
3. **Make your changes**
4. **Test thoroughly**
5. **Commit** with clear messages
6. **Push** to your fork
7. **Open a Pull Request**

## 🛠️ Development Setup

### Prerequisites

```bash
# PHP 7.4+
php -v

# Composer
composer --version

# MySQL/MariaDB
mysql --version

# Node.js 16+ (for Discord bot)
node --version
```

### Initial Setup

```bash
# Clone your fork
git clone https://github.com/NoxDSP/GDCore.git
cd GDCore

# Install PHP dependencies
composer install

# Install Node.js dependencies (bot)
cd bot && npm install && cd ..

# Copy environment file
cp .env.example .env
# Edit .env with your configuration

# Import database
mysql -u root -p gdcore < database/schema.sql
```

### Running Locally

```bash
# PHP development server
php -S localhost:8000

# Discord bot (development mode)
cd bot && npm run dev
```

## 💻 Coding Guidelines

### PHP Code Style

We follow **PSR-12** coding standards:

```php
<?php
namespace GDCore\Example;

class ExampleClass
{
    private $property;

    public function exampleMethod(string $param): int
    {
        if ($param === 'value') {
            return 1;
        }
        
        return 0;
    }
}
```

### Key Principles

- **Use prepared statements** for all database queries
- **Validate input** before processing
- **Use type hints** where possible
- **Comment complex logic**
- **Keep functions small** and focused
- **Follow DRY** (Don't Repeat Yourself)

### Security Requirements

- ✅ Always use prepared statements
- ✅ Sanitize output with `htmlspecialchars()`
- ✅ Validate and sanitize user input
- ✅ Never expose sensitive information in errors
- ✅ Use HTTPS in production
- ✅ Implement rate limiting for sensitive endpoints

### File Structure

```
src/
├── Config/         # Configuration classes
├── Utils/          # Utility classes
└── Controllers/    # Controller classes (if used)

database/
├── accounts/       # Account endpoints
└── *.php          # Other endpoints

admin/
├── *.php          # Admin panel pages
└── includes/      # Shared admin files

bot/
├── commands/      # Discord bot commands
└── utils/         # Bot utilities
```

## 📝 Commit Guidelines

### Commit Message Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation only
- `style`: Code style (formatting, missing semicolons, etc.)
- `refactor`: Code refactoring
- `perf`: Performance improvement
- `test`: Adding tests
- `chore`: Maintenance tasks

### Examples

```bash
feat(endpoints): add level search pagination

Add pagination support to getGJLevels21.php with configurable
page size and offset parameters.

Closes #123

---

fix(security): prevent SQL injection in user lookup

Use prepared statements instead of string concatenation
in getGJUserInfo20.php

Fixes #456

---

docs(readme): update installation instructions

Add detailed steps for Windows installation and common
troubleshooting tips.
```

## 🔄 Pull Request Process

### Before Submitting

1. **Update documentation** if needed
2. **Add tests** for new features
3. **Ensure all tests pass**
4. **Check code style** (`composer lint` if available)
5. **Update CHANGELOG.md**

### PR Checklist

- [ ] Code follows project style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] No new warnings or errors
- [ ] Tests added/updated
- [ ] All tests passing

### Review Process

1. **Automated checks** must pass (GitHub Actions)
2. **Code review** by maintainers
3. **Changes requested** (if needed)
4. **Approval** and merge

### After Merge

- Your contribution will be included in the next release
- You'll be added to contributors list
- Thank you! 🎉

## 🧪 Testing

### Manual Testing

```bash
# Test database connection
php tools/test_connection.php

# Test specific endpoint
curl -X POST http://localhost:8000/database/accounts/loginGJAccount.php \
  -d "userName=test&password=test"
```

### Future: Automated Tests

We plan to add:
- Unit tests (PHPUnit)
- Integration tests
- E2E tests

## 📚 Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Discord.js Guide](https://discordjs.guide/)
- [Cvolton's GMDprivateServer](https://github.com/Cvolton/GMDprivateServer)

## ❓ Questions?

- **General questions:** [Open a discussion](../../discussions)
- **Bug reports:** [Open an issue](../../issues/new/choose)
- **Security issues:** See [SECURITY.md](SECURITY.md)

## 🙏 Thank You!

Your contributions make GDCore better for everyone. Whether it's code, documentation, bug reports, or feature suggestions—every contribution is valuable!

---

Happy coding! 🚀
