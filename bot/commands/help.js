/**
 * Help Command
 */

const { SlashCommandBuilder, EmbedBuilder } = require('discord.js');

module.exports = {
    data: new SlashCommandBuilder()
        .setName('help')
        .setDescription('Show all available commands'),

    async execute(interaction, db) {
        const embed = new EmbedBuilder()
            .setColor(process.env.BOT_COLOR || '#667EEA')
            .setTitle(`🎮 ${process.env.SERVER_NAME || 'GDCore'} Bot Commands`)
            .setDescription('All available commands for the GDCore Discord bot')
            .addFields(
                {
                    name: '📊 Server Commands',
                    value: '`/stats` - Show server statistics\n' +
                           '`/leaderboard [limit]` - Show top players\n' +
                           '`/recent [limit]` - Show recent levels'
                },
                {
                    name: '👤 User Commands',
                    value: '`/user <username>` - Look up a user profile'
                },
                {
                    name: '🎮 Level Commands',
                    value: '`/level <name>` - Look up a level'
                },
                {
                    name: 'ℹ️ Info',
                    value: '`/help` - Show this help message'
                }
            )
            .setFooter({ text: 'Use /command for slash commands or !command for prefix commands' });

        if (process.env.SERVER_URL) {
            embed.addFields({
                name: '🌐 Server URL',
                value: process.env.SERVER_URL
            });
        }

        await interaction.reply({ embeds: [embed] });
    },

    async executePrefix(message, args, db) {
        const embed = new EmbedBuilder()
            .setColor(process.env.BOT_COLOR || '#667EEA')
            .setTitle('🎮 Bot Commands')
            .addFields(
                { name: '!stats', value: 'Server statistics' },
                { name: '!user <name>', value: 'User lookup' },
                { name: '!level <name>', value: 'Level lookup' },
                { name: '!leaderboard [limit]', value: 'Top players' }
            );

        await message.reply({ embeds: [embed] });
    }
};
