/**
 * GDCore Discord Bot
 * Main entry point
 */

require('dotenv').config();
const { Client, GatewayIntentBits, Collection, ActivityType } = require('discord.js');
const fs = require('fs');
const path = require('path');
const Database = require('./utils/database');

// Create Discord client
const client = new Client({
    intents: [
        GatewayIntentBits.Guilds,
        GatewayIntentBits.GuildMessages,
        GatewayIntentBits.MessageContent,
    ]
});

// Initialize database
const db = new Database();

// Load commands
client.commands = new Collection();
const commandsPath = path.join(__dirname, 'commands');
const commandFiles = fs.readdirSync(commandsPath).filter(file => file.endsWith('.js'));

for (const file of commandFiles) {
    const filePath = path.join(commandsPath, file);
    const command = require(filePath);
    if ('data' in command && 'execute' in command) {
        client.commands.set(command.data.name, command);
        console.log(`✓ Loaded command: ${command.data.name}`);
    }
}

// Bot ready event
client.once('ready', async () => {
    console.log(`✓ Bot logged in as ${client.user.tag}`);
    
    // Set initial status
    updateStatus();
    
    // Auto-update status
    if (process.env.ENABLE_AUTO_STATUS === 'true') {
        const interval = parseInt(process.env.STATUS_UPDATE_INTERVAL) || 300000;
        setInterval(updateStatus, interval);
    }
});

// Update bot status with server stats
async function updateStatus() {
    try {
        const stats = await db.query('SELECT COUNT(*) as count FROM users WHERE isRegistered = 1');
        const userCount = stats[0].count;
        
        client.user.setPresence({
            activities: [{
                name: `${userCount} players | /help`,
                type: ActivityType.Watching
            }],
            status: 'online'
        });
    } catch (error) {
        console.error('Error updating status:', error);
    }
}

// Handle slash commands
client.on('interactionCreate', async interaction => {
    if (!interaction.isChatInputCommand()) return;

    const command = client.commands.get(interaction.commandName);
    if (!command) return;

    try {
        await command.execute(interaction, db);
    } catch (error) {
        console.error(`Error executing ${interaction.commandName}:`, error);
        const errorMessage = {
            content: '❌ There was an error executing this command!',
            ephemeral: true
        };
        
        if (interaction.replied || interaction.deferred) {
            await interaction.followUp(errorMessage);
        } else {
            await interaction.reply(errorMessage);
        }
    }
});

// Handle prefix commands (legacy support)
client.on('messageCreate', async message => {
    if (message.author.bot) return;
    
    const prefix = process.env.PREFIX || '!';
    if (!message.content.startsWith(prefix)) return;

    const args = message.content.slice(prefix.length).trim().split(/ +/);
    const commandName = args.shift().toLowerCase();

    const command = client.commands.get(commandName);
    if (!command || !command.executePrefix) return;

    try {
        await command.executePrefix(message, args, db);
    } catch (error) {
        console.error(`Error executing ${commandName}:`, error);
        message.reply('❌ There was an error executing this command!');
    }
});

// Error handling
client.on('error', error => {
    console.error('Discord client error:', error);
});

process.on('unhandledRejection', error => {
    console.error('Unhandled promise rejection:', error);
});

// Login to Discord
client.login(process.env.DISCORD_TOKEN);
