/**
 * User Lookup Command
 */

const { SlashCommandBuilder, EmbedBuilder } = require('discord.js');

module.exports = {
    data: new SlashCommandBuilder()
        .setName('user')
        .setDescription('Look up a user profile')
        .addStringOption(option =>
            option.setName('username')
                .setDescription('Username to search for')
                .setRequired(true)
        ),

    async execute(interaction, db) {
        await interaction.deferReply();

        const username = interaction.options.getString('username');

        try {
            const user = await db.getUserByName(username);

            if (!user) {
                return await interaction.editReply(`❌ User \`${username}\` not found.`);
            }

            const rank = await db.query(
                'SELECT COUNT(*) + 1 as rank FROM users WHERE stars > ? AND isRegistered = 1',
                [user.stars]
            );

            const embed = new EmbedBuilder()
                .setColor(process.env.BOT_COLOR || '#667EEA')
                .setTitle(`👤 ${user.userName}`)
                .setDescription(`User ID: ${user.userID} | Account ID: ${user.accountID}`)
                .addFields(
                    { name: '⭐ Stars', value: user.stars.toLocaleString(), inline: true },
                    { name: '👹 Demons', value: user.demons.toString(), inline: true },
                    { name: '🏅 Creator Points', value: user.creatorPoints.toString(), inline: true },
                    { name: '💎 Secret Coins', value: user.coins.toString(), inline: true },
                    { name: '🪙 User Coins', value: user.userCoins.toString(), inline: true },
                    { name: '🏆 Global Rank', value: `#${rank[0].rank}`, inline: true }
                )
                .setTimestamp()
                .setFooter({ text: `Registered: ${new Date(user.registerDate).toLocaleDateString()}` });

            await interaction.editReply({ embeds: [embed] });
        } catch (error) {
            console.error('User command error:', error);
            await interaction.editReply('❌ Failed to fetch user information.');
        }
    },

    async executePrefix(message, args, db) {
        if (!args[0]) {
            return message.reply('❌ Please provide a username: `!user <username>`');
        }

        const username = args.join(' ');
        const user = await db.getUserByName(username);

        if (!user) {
            return message.reply(`❌ User \`${username}\` not found.`);
        }

        const embed = new EmbedBuilder()
            .setColor(process.env.BOT_COLOR || '#667EEA')
            .setTitle(`👤 ${user.userName}`)
            .addFields(
                { name: '⭐ Stars', value: user.stars.toString(), inline: true },
                { name: '👹 Demons', value: user.demons.toString(), inline: true },
                { name: '🏅 CP', value: user.creatorPoints.toString(), inline: true }
            );

        await message.reply({ embeds: [embed] });
    }
};
