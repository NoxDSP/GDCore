# GDCore Updates

This directory contains update scripts and documentation for updating your GDCore installation.

## How to Update

1. **Backup your database** before applying any updates:
   ```bash
   mysqldump -u root -p gdcore > backup_$(date +%Y%m%d).sql
   ```

2. **Pull the latest changes** from the repository:
   ```bash
   git pull origin master
   ```

3. **Update dependencies**:
   ```bash
   composer update
   ```

4. **Check for database migrations** in this directory and apply them in order:
   ```bash
   mysql -u root -p gdcore < _updates/update_YYYYMMDD.sql
   ```

5. **Clear any caches** if applicable

6. **Test the server** to ensure everything works correctly

## Version History

### v1.0.0 (Current)
- Initial release based on Cvolton's GMDprivateServer
- Full support for Geometry Dash 1.0 - 2.2
- Modern PHP architecture with PSR-4 autoloading
- Account system with registration and login
- Level upload/download/search
- Comments and social features
- Friend requests and messaging
- Encryption utilities (XOR, cloud saves)

## Update Notes

When creating update scripts, follow this naming convention:
- `update_YYYYMMDD_description.sql` for database changes
- Include comments explaining what each change does
- Test updates on a development server before production

## Backwards Compatibility

GDCore maintains backwards compatibility with:
- Geometry Dash versions 1.0 through 2.2
- Original Cvolton GMDprivateServer database structure
- Standard GD server API endpoints

## Breaking Changes

None yet.

## Troubleshooting Updates

If you encounter issues after updating:

1. Check PHP error logs
2. Verify database schema matches expected structure
3. Ensure all dependencies are up to date
4. Check file permissions
5. Verify .env configuration

For help, consult the main README or open an issue on GitHub.
