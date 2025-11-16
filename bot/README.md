# GDCore Discord Bot

Discord bot for GDCore Geometry Dash Private Server with stats, leaderboards, and user lookup.

## Features

- 📊 **Server Statistics** - View player count, levels, downloads
- 🏆 **Leaderboards** - Top players by stars
- 👤 **User Lookup** - Search player profiles
- 🎮 **Level Search** - Find and view level information
- 🆕 **Recent Levels** - Show latest uploads
- ⚡ **Slash Commands** - Modern Discord interactions
- 🔄 **Auto Status** - Live player count in bot status

## Commands

### Slash Commands (Recommended)
- `/stats` - Server statistics
- `/user <username>` - User profile
- `/level <name>` - Level information
- `/leaderboard [limit]` - Top players
- `/recent [limit]` - Recent levels
- `/help` - Command list

### Prefix Commands (Legacy)
- `!stats` - Server stats
- `!user <username>` - User lookup
- `!level <name>` - Level lookup
- `!leaderboard [limit]` - Leaderboard
- `!help` - Help

## Setup

### 1. Install Node.js
Download from https://nodejs.org/ (v16 or higher)

### 2. Install Dependencies
```bash
cd bot
npm install
```

### 3. Create Discord Bot
1. Go to https://discord.com/developers/applications
2. Click "New Application"
3. Go to "Bot" tab
4. Click "Add Bot"
5. Copy bot token
6. Enable "Message Content Intent"
7. Go to "OAuth2" > "URL Generator"
8. Select scopes: `bot`, `applications.commands`
9. Select permissions: `Send Messages`, `Embed Links`, `Read Message History`
10. Copy URL and invite bot to your server

### 4. Configure Environment
```bash
cp .env.example .env
```

Edit `.env`:
```ini
DISCORD_TOKEN=your_bot_token
CLIENT_ID=your_bot_client_id
GUILD_ID=your_server_id_for_testing
DB_HOST=localhost
DB_NAME=gdcore
DB_USER=gdcore_user
DB_PASS=your_password
SERVER_NAME=Your GDPS Name
SERVER_URL=https://yourdomain.com
```

### 5. Deploy Commands
```bash
node deploy-commands.js
```

### 6. Start Bot
```bash
npm start
```

Or with auto-restart:
```bash
npm run dev
```

## Bot Status

The bot automatically updates its status with live player count every 5 minutes.

## Database Connection

Bot connects to the same MySQL database as GDCore using read-only queries.

## File Structure

```
bot/
├── index.js              # Main bot file
├── deploy-commands.js    # Command registration
├── package.json          # Dependencies
├── .env                  # Configuration (create from .env.example)
├── commands/
│   ├── stats.js         # Server stats
│   ├── user.js          # User lookup
│   ├── level.js         # Level lookup
│   ├── leaderboard.js   # Top players
│   ├── recent.js        # Recent levels
│   └── help.js          # Help command
└── utils/
    └── database.js       # Database helper
```

## Troubleshooting

### Bot doesn't respond
- Check bot is online in Discord
- Verify bot has proper permissions
- Check console for errors
- Ensure commands are deployed

### Database connection fails
- Verify database credentials in `.env`
- Check MySQL is running
- Test connection with GDCore

### Commands not showing
- Run `node deploy-commands.js`
- Wait a few minutes (global) or seconds (guild)
- Check `CLIENT_ID` and `GUILD_ID` are correct

## Production Deployment

### Using PM2
```bash
npm install -g pm2
pm2 start index.js --name gdcore-bot
pm2 save
pm2 startup
```

### Using systemd
Create `/etc/systemd/system/gdcore-bot.service`:
```ini
[Unit]
Description=GDCore Discord Bot
After=network.target

[Service]
Type=simple
User=youruser
WorkingDirectory=/path/to/bot
ExecStart=/usr/bin/node index.js
Restart=on-failure

[Install]
WantedBy=multi-user.target
```

Enable:
```bash
sudo systemctl enable gdcore-bot
sudo systemctl start gdcore-bot
```

## License

GPL-3.0 - Same as GDCore
