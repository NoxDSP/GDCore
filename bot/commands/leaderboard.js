/**
 * Leaderboard Command
 */

const { SlashCommandBuilder, EmbedBuilder } = require('discord.js');

module.exports = {
    data: new SlashCommandBuilder()
        .setName('leaderboard')
        .setDescription('Show top players')
        .addIntegerOption(option =>
            option.setName('limit')
                .setDescription('Number of players to show (1-25)')
                .setMinValue(1)
                .setMaxValue(25)
        ),

    async execute(interaction, db) {
        await interaction.deferReply();

        const limit = interaction.options.getInteger('limit') || 10;

        try {
            const players = await db.getTopPlayers(limit);

            if (players.length === 0) {
                return await interaction.editReply('❌ No players found.');
            }

            const embed = new EmbedBuilder()
                .setColor(process.env.BOT_COLOR || '#667EEA')
                .setTitle(`🏆 Top ${limit} Players`)
                .setDescription(
                    players.map((player, index) => {
                        const medal = index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : `**${index + 1}.**`;
                        return `${medal} **${player.userName}**\n` +
                               `⭐ ${player.stars.toLocaleString()} | ` +
                               `👹 ${player.demons} | ` +
                               `🏅 ${player.creatorPoints} CP`;
                    }).join('\n\n')
                )
                .setTimestamp()
                .setFooter({ text: `${process.env.SERVER_NAME || 'GDCore'} Leaderboard` });

            await interaction.editReply({ embeds: [embed] });
        } catch (error) {
            console.error('Leaderboard command error:', error);
            await interaction.editReply('❌ Failed to fetch leaderboard.');
        }
    },

    async executePrefix(message, args, db) {
        const limit = parseInt(args[0]) || 10;
        const players = await db.getTopPlayers(Math.min(limit, 25));

        const embed = new EmbedBuilder()
            .setColor(process.env.BOT_COLOR || '#667EEA')
            .setTitle(`🏆 Top ${limit} Players`)
            .setDescription(
                players.map((p, i) => 
                    `${i + 1}. **${p.userName}** - ⭐${p.stars} 👹${p.demons}`
                ).join('\n')
            );

        await message.reply({ embeds: [embed] });
    }
};
