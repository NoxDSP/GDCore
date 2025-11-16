/**
 * Server Stats Command
 */

const { SlashCommandBuilder, EmbedBuilder } = require('discord.js');

module.exports = {
    data: new SlashCommandBuilder()
        .setName('stats')
        .setDescription('Show server statistics'),

    async execute(interaction, db) {
        await interaction.deferReply();

        try {
            const stats = await db.getServerStats();
            const topPlayers = await db.getTopPlayers(5);

            const embed = new EmbedBuilder()
                .setColor(process.env.BOT_COLOR || '#667EEA')
                .setTitle(`📊 ${process.env.SERVER_NAME || 'GDCore'} Statistics`)
                .setDescription('Current server statistics')
                .addFields(
                    { name: '👥 Total Users', value: stats.users.toLocaleString(), inline: true },
                    { name: '🎮 Total Levels', value: stats.levels.toLocaleString(), inline: true },
                    { name: '📥 Total Downloads', value: stats.downloads.toLocaleString(), inline: true },
                    { name: '⭐ Total Stars', value: stats.totalStars.toLocaleString(), inline: true },
                    { name: '📊 Avg Stars/User', value: Math.round(stats.totalStars / stats.users).toString(), inline: true },
                    { name: '📊 Avg Levels/User', value: (stats.levels / stats.users).toFixed(2), inline: true }
                )
                .addFields({
                    name: '🏆 Top Players',
                    value: topPlayers.map((p, i) => 
                        `${i + 1}. **${p.userName}** - ${p.stars}⭐ ${p.demons}👹`
                    ).join('\n') || 'No players yet'
                })
                .setTimestamp()
                .setFooter({ text: 'GDCore Stats' });

            if (process.env.SERVER_URL) {
                embed.setURL(process.env.SERVER_URL);
            }

            await interaction.editReply({ embeds: [embed] });
        } catch (error) {
            console.error('Stats command error:', error);
            await interaction.editReply('❌ Failed to fetch server statistics.');
        }
    },

    async executePrefix(message, args, db) {
        const stats = await db.getServerStats();
        const topPlayers = await db.getTopPlayers(5);

        const embed = new EmbedBuilder()
            .setColor(process.env.BOT_COLOR || '#667EEA')
            .setTitle(`📊 ${process.env.SERVER_NAME || 'GDCore'} Statistics`)
            .addFields(
                { name: '👥 Users', value: stats.users.toString(), inline: true },
                { name: '🎮 Levels', value: stats.levels.toString(), inline: true },
                { name: '📥 Downloads', value: stats.downloads.toString(), inline: true }
            )
            .addFields({
                name: '🏆 Top Players',
                value: topPlayers.map((p, i) => `${i + 1}. ${p.userName} - ${p.stars}⭐`).join('\n')
            });

        await message.reply({ embeds: [embed] });
    }
};
