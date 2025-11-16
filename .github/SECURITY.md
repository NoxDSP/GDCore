# Security Policy

## 🔒 Supported Versions

We actively support the following versions with security updates:

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |
| < 1.0   | :x:                |

## 🚨 Reporting a Vulnerability

We take security vulnerabilities seriously. If you discover a security issue, please follow responsible disclosure practices.

### ⚠️ **DO NOT** create a public GitHub issue for security vulnerabilities.

Instead, please report security issues to:
- **Email:** security@noxdsp.com (or create a private security advisory)
- **Security Advisory:** Use [GitHub Security Advisories](../../security/advisories/new)

### What to Include

Please provide:
- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)
- Your contact information (optional, for credit)

### Response Timeline

- **Initial Response:** Within 48 hours
- **Status Update:** Within 7 days
- **Fix Timeline:** Depends on severity
  - **Critical:** 1-3 days
  - **High:** 7-14 days
  - **Medium:** 30 days
  - **Low:** Next release cycle

### Disclosure Policy

- We will acknowledge receipt of your vulnerability report
- We will confirm the vulnerability and determine its severity
- We will release a fix as soon as possible
- We will publicly disclose the vulnerability after a fix is available
- We will credit you in our security advisories (unless you prefer to remain anonymous)

## 🛡️ Security Best Practices

When deploying GDCore:

### Required
- [ ] Use HTTPS in production
- [ ] Change default admin credentials immediately
- [ ] Set `DEBUG_MODE=false` in production
- [ ] Use strong database passwords
- [ ] Keep PHP and MySQL updated
- [ ] Configure firewall rules
- [ ] Regular database backups

### Recommended
- [ ] Enable rate limiting
- [ ] Use fail2ban for brute force protection
- [ ] Set up monitoring and alerts
- [ ] Regular security audits
- [ ] Keep dependencies updated (`composer update`)
- [ ] Use environment variables for secrets
- [ ] Implement 2FA for admin accounts (future feature)

## 🔐 Known Security Considerations

### Authentication
- GJP (Geometry Dash Password) is XOR-encrypted, not a secure cryptographic method
- This is intentional for GD client compatibility
- Admin panel uses bcrypt hashing (secure)

### Rate Limiting
- Basic rate limiting is implemented
- For production, consider using external services (Cloudflare, etc.)

### Session Management
- Admin sessions expire after 30 minutes
- Sessions are server-side only
- Consider Redis for session storage in high-traffic environments

## 📚 Security Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
- [MySQL Security Best Practices](https://dev.mysql.com/doc/refman/8.0/en/security.html)

## 🏆 Security Hall of Fame

We recognize and thank security researchers who responsibly disclose vulnerabilities:

<!-- Add names here as they report issues -->
- *No reported vulnerabilities yet*

---

Thank you for helping keep GDCore and our community safe! 🙏
