# GDCore Installation Guide

## Prerequisites

Before installing GDCore, ensure you have:

- **PHP 7.4 or higher** (PHP 8.0+ recommended)
  - Extensions: PDO, pdo_mysql, json, mbstring, openssl
- **MySQL 5.7+ or MariaDB 10.2+**
- **Apache 2.4+ or Nginx**
- **Composer** (PHP dependency manager)
- **Git** (optional, for updates)

## Step 1: Download GDCore

Clone the repository or download the source code:

```bash
git clone https://github.com/NoxDSP/GDCore.git
cd GDCore
```

## Step 2: Install Dependencies

Install PHP dependencies using Composer:

```bash
composer install
```

If you don't have Composer, install it from https://getcomposer.org/

## Step 3: Configure Database

### Create Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE gdcore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'gdcore_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON gdcore.* TO 'gdcore_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Import Database Schema

```bash
mysql -u gdcore_user -p gdcore < database/schema.sql
```

## Step 4: Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` and configure your settings:

```ini
# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_NAME=gdcore
DB_USER=gdcore_user
DB_PASS=your_secure_password

# Server Configuration
SERVER_NAME="Your GDPS Name"
SERVER_URL=http://yourdomain.com

# Security - CHANGE THESE!
SECRET_KEY=generate_a_long_random_string_here
```

### Generate Secret Key

You can generate a random secret key with:

```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

## Step 5: Configure Web Server

### Apache

Ensure `.htaccess` is present (already included) and enable mod_rewrite:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Configure your VirtualHost:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /path/to/gdcore
    
    <Directory /path/to/gdcore>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/gdcore_error.log
    CustomLog ${APACHE_LOG_DIR}/gdcore_access.log combined
</VirtualHost>
```

### Nginx

Create a server block:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/gdcore;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Step 6: Set Permissions

```bash
chmod -R 755 /path/to/gdcore
chown -R www-data:www-data /path/to/gdcore
```

## Step 7: Test Installation

Visit your server URL in a browser. You should see no errors.

Test with a simple curl command:

```bash
curl -X POST http://yourdomain.com/database/accounts/loginGJAccount.php \
  -d "userName=admin&password=admin123"
```

You should receive a response (even if it's an error, it means the server is working).

## Step 8: Configure Geometry Dash Client

You need to modify the Geometry Dash executable to point to your server instead of RobTop's servers.

### URLs to Replace

Replace these Boomlings URLs with your server URL:

- `http://www.boomlings.com/database/` → `http://yourdomain.com/database/`

### Tools for Editing

- **For Windows**: Use a hex editor like HxD
- **For mobile**: Use APK editing tools or proxy methods

### Important Notes

1. Some URLs are base64 encoded in GD 2.1+
2. Make sure to replace ALL occurrences
3. Keep backup of original executable

## Step 9: Change Default Admin Password

The default admin credentials are:
- **Username**: admin
- **Password**: admin123

**IMPORTANT**: Change this immediately!

```bash
mysql -u gdcore_user -p gdcore
```

```sql
-- Generate new password hash (replace 'your_new_password' with actual password)
-- You can use online bcrypt generators or PHP
UPDATE accounts SET password = '$2y$10$YOUR_BCRYPT_HASH_HERE' WHERE userName = 'admin';
```

Or use PHP:

```bash
php -r "echo password_hash('your_new_password', PASSWORD_BCRYPT) . PHP_EOL;"
```

## Step 10: Enable HTTPS (Recommended)

For production, use HTTPS with Let's Encrypt:

```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com
```

Update your `.env`:
```ini
SERVER_URL=https://yourdomain.com
```

## Troubleshooting

### Can't connect to database
- Check credentials in `.env`
- Verify MySQL is running: `sudo systemctl status mysql`
- Check firewall rules

### 500 Internal Server Error
- Check PHP error logs: `/var/log/apache2/error.log` or `/var/log/nginx/error.log`
- Verify file permissions
- Enable debug mode in `.env`: `DEBUG_MODE=true`

### 404 errors on all endpoints
- Ensure mod_rewrite is enabled (Apache)
- Check `.htaccess` is present
- Verify server configuration

### GD client can't connect
- Ensure URLs are correctly replaced in client
- Check server is accessible from internet
- Verify firewall allows connections
- Test endpoints with curl

## Next Steps

1. Read the [README.md](README.md) for features and usage
2. Check [_updates/README.md](_updates/README.md) for update procedures
3. Customize your server settings
4. Invite users to register!

## Security Recommendations

1. **Change default admin password immediately**
2. **Use HTTPS in production**
3. **Set DEBUG_MODE=false in production**
4. **Regularly backup your database**
5. **Keep dependencies updated**: `composer update`
6. **Use strong passwords**
7. **Implement rate limiting** (built-in but configurable)
8. **Monitor server logs**

## Support

If you encounter issues:
1. Check error logs
2. Verify configuration
3. Consult documentation
4. Open an issue on GitHub

---

Enjoy your Geometry Dash Private Server!
