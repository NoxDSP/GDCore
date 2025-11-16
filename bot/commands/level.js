/**
 * Level Lookup Command
 */

const { SlashCommandBuilder, EmbedBuilder } = require('discord.js');

module.exports = {
    data: new SlashCommandBuilder()
        .setName('level')
        .setDescription('Look up a level')
        .addStringOption(option =>
            option.setName('name')
                .setDescription('Level name to search for')
                .setRequired(true)
        ),

    async execute(interaction, db) {
        await interaction.deferReply();

        const levelName = interaction.options.getString('name');

        try {
            const level = await db.getLevelByName(levelName);

            if (!level) {
                return await interaction.editReply(`❌ Level \`${levelName}\` not found.`);
            }

            const difficulty = getDifficultyEmoji(level.stars, level.starDemon);
            const description = level.levelDesc ? Buffer.from(level.levelDesc, 'base64').toString() : 'No description';

            const embed = new EmbedBuilder()
                .setColor(process.env.BOT_COLOR || '#667EEA')
                .setTitle(`${difficulty} ${level.levelName}`)
                .setDescription(description.substring(0, 200))
                .addFields(
                    { name: '👤 Creator', value: level.userName, inline: true },
                    { name: '⭐ Stars', value: level.stars?.toString() || 'Unrated', inline: true },
                    { name: '🆔 Level ID', value: level.levelID.toString(), inline: true },
                    { name: '📥 Downloads', value: level.downloads.toLocaleString(), inline: true },
                    { name: '👍 Likes', value: level.likes.toString(), inline: true },
                    { name: '🎮 Objects', value: level.objects.toLocaleString(), inline: true }
                )
                .setTimestamp(new Date(level.uploadDate))
                .setFooter({ text: `Version ${level.levelVersion} • ${level.gameVersion >= 21 ? 'GD 2.1+' : 'GD 2.0'}` });

            if (level.featured) embed.addFields({ name: '✨', value: 'Featured!', inline: true });
            if (level.epic) embed.addFields({ name: '💎', value: 'Epic!', inline: true });

            await interaction.editReply({ embeds: [embed] });
        } catch (error) {
            console.error('Level command error:', error);
            await interaction.editReply('❌ Failed to fetch level information.');
        }
    },

    async executePrefix(message, args, db) {
        if (!args[0]) {
            return message.reply('❌ Please provide a level name: `!level <name>`');
        }

        const levelName = args.join(' ');
        const level = await db.getLevelByName(levelName);

        if (!level) {
            return message.reply(`❌ Level \`${levelName}\` not found.`);
        }

        const embed = new EmbedBuilder()
            .setColor(process.env.BOT_COLOR || '#667EEA')
            .setTitle(level.levelName)
            .addFields(
                { name: '👤 Creator', value: level.userName, inline: true },
                { name: '⭐ Stars', value: level.stars?.toString() || 'Unrated', inline: true },
                { name: '📥 Downloads', value: level.downloads.toString(), inline: true }
            );

        await message.reply({ embeds: [embed] });
    }
};

function getDifficultyEmoji(stars, isDemon) {
    if (isDemon) return '👹';
    if (!stars || stars === 0) return '⚪';
    if (stars <= 2) return '⭐';
    if (stars <= 4) return '🟢';
    if (stars <= 6) return '🔵';
    if (stars <= 8) return '🟠';
    return '🔴';
}
