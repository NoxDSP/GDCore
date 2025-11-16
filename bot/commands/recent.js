/**
 * Recent Levels Command
 */

const { SlashCommandBuilder, EmbedBuilder } = require('discord.js');

module.exports = {
    data: new SlashCommandBuilder()
        .setName('recent')
        .setDescription('Show recently uploaded levels')
        .addIntegerOption(option =>
            option.setName('limit')
                .setDescription('Number of levels to show (1-10)')
                .setMinValue(1)
                .setMaxValue(10)
        ),

    async execute(interaction, db) {
        await interaction.deferReply();

        const limit = interaction.options.getInteger('limit') || 5;

        try {
            const levels = await db.getRecentLevels(limit);

            if (levels.length === 0) {
                return await interaction.editReply('❌ No levels found.');
            }

            const embed = new EmbedBuilder()
                .setColor(process.env.BOT_COLOR || '#667EEA')
                .setTitle(`🆕 Recent Levels`)
                .setDescription(
                    levels.map(level => {
                        const stars = level.stars > 0 ? `⭐${level.stars}` : '⚪ Unrated';
                        const timeAgo = getTimeAgo(new Date(level.uploadDate));
                        return `**${level.levelName}** by ${level.userName}\n` +
                               `${stars} • 📥 ${level.downloads} • ${timeAgo}`;
                    }).join('\n\n')
                )
                .setTimestamp()
                .setFooter({ text: `${process.env.SERVER_NAME || 'GDCore'} Recent Levels` });

            await interaction.editReply({ embeds: [embed] });
        } catch (error) {
            console.error('Recent command error:', error);
            await interaction.editReply('❌ Failed to fetch recent levels.');
        }
    }
};

function getTimeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    
    if (seconds < 60) return 'just now';
    if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`;
    if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`;
    if (seconds < 604800) return `${Math.floor(seconds / 86400)}d ago`;
    return `${Math.floor(seconds / 604800)}w ago`;
}
