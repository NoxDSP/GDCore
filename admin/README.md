# GDCore Admin Panel

Web-based administration panel for managing your Geometry Dash Private Server.

## Features

- 📊 **Dashboard** - Server statistics and overview
- 👥 **User Management** - View, ban, promote users
- 🎮 **Level Management** - Rate, feature, delete levels
- 💬 **Comment Moderation** - Review and delete comments
- 🛡️ **Moderation Tools** - Ban management, action logs
- 📅 **Daily/Weekly** - Set daily and weekly levels
- ⚙️ **Settings** - Server configuration

## Access

Navigate to: `http://yourdomain.com/admin/`

## Default Login

**Username:** admin  
**Password:** admin123

⚠️ **CHANGE THIS IMMEDIATELY AFTER FIRST LOGIN!**

## Security

1. The admin panel requires admin role (roleType >= 2)
2. Sessions timeout after 30 minutes of inactivity
3. All actions are logged to the `modactions` table
4. Use HTTPS in production

## Setup

1. Ensure you have an admin account in the database:
```sql
-- Create admin role
INSERT INTO roles (accountID, roleType) VALUES (1, 2);
```

2. Access the admin panel at `/admin/`

3. Change default password immediately

## File Structure

```
admin/
├── index.php           # Login page
├── dashboard.php       # Main dashboard
├── users.php          # User management
├── levels.php         # Level management
├── comments.php       # Comment moderation
├── moderation.php     # Mod tools
├── daily.php          # Daily/weekly levels
├── settings.php       # Server settings
├── logout.php         # Logout
├── includes/
│   ├── auth.php       # Authentication check
│   ├── header.php     # Page header
│   └── footer.php     # Page footer
└── assets/
    ├── css/
    │   └── admin.css  # Admin styles
    └── js/
        └── admin.js   # Admin scripts
```

## Customization

Edit `assets/css/admin.css` to customize the appearance.

## Pages

### Dashboard
- Server statistics
- Recent activity
- Top players
- Latest levels

### Users
- Search users
- View user details
- Ban/unban users
- Change user roles (User/Mod/Admin)
- View user statistics

### Levels
- Search and filter levels
- Rate levels (set stars)
- Feature/unfeature levels
- Mark as epic
- Delete levels
- View level details

### Comments (To be implemented)
- View all comments
- Delete inappropriate comments
- Ban users from commenting

### Moderation
- View ban list
- Manage active bans
- View moderation logs
- Track admin actions

### Daily/Weekly
- Set daily level
- Set weekly level
- Schedule future dailies
- View history

### Settings
- Server configuration
- Feature toggles
- Rate limits
- Email settings

## Permissions

| Role | Level | Permissions |
|------|-------|-------------|
| User | 0 | No admin access |
| Mod | 1 | View stats, moderate content |
| Admin | 2 | Full access |

## Best Practices

1. **Use HTTPS** - Always use HTTPS for admin panel
2. **Strong Passwords** - Use strong, unique passwords
3. **Regular Backups** - Backup database before major changes
4. **Audit Logs** - Review moderation logs regularly
5. **Limit Access** - Only give admin to trusted users

## Troubleshooting

### Can't login
- Check database for admin role: `SELECT * FROM roles WHERE roleType >= 2`
- Verify password is correct
- Check PHP sessions are enabled

### Session timeout
- Increase timeout in `includes/auth.php`
- Check PHP session configuration

### Styles not loading
- Verify `assets/css/admin.css` exists
- Check file permissions
- Clear browser cache

## Security Notes

- Admin panel is NOT accessible via GD client
- Sessions are server-side only
- All SQL queries use prepared statements
- XSS protection on all outputs
- CSRF protection recommended for production

## Future Enhancements

- [ ] Two-factor authentication
- [ ] Activity dashboard with charts
- [ ] Bulk actions
- [ ] Advanced search filters
- [ ] Email notifications
- [ ] API access
- [ ] Mobile-responsive design improvements
