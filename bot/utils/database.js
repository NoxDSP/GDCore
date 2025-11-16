/**
 * Database Connection Utility
 */

const mysql = require('mysql2/promise');

class Database {
    constructor() {
        this.pool = mysql.createPool({
            host: process.env.DB_HOST || 'localhost',
            port: process.env.DB_PORT || 3306,
            user: process.env.DB_USER,
            password: process.env.DB_PASS,
            database: process.env.DB_NAME,
            waitForConnections: true,
            connectionLimit: 10,
            queueLimit: 0
        });
        
        console.log('✓ Database pool created');
    }

    async query(sql, params = []) {
        try {
            const [rows] = await this.pool.execute(sql, params);
            return rows;
        } catch (error) {
            console.error('Database query error:', error);
            throw error;
        }
    }

    async getServerStats() {
        const stats = {};
        
        const users = await this.query('SELECT COUNT(*) as count FROM users WHERE isRegistered = 1');
        stats.users = users[0].count;
        
        const levels = await this.query('SELECT COUNT(*) as count FROM levels');
        stats.levels = levels[0].count;
        
        const downloads = await this.query('SELECT SUM(downloads) as total FROM levels');
        stats.downloads = downloads[0].total || 0;
        
        const stars = await this.query('SELECT SUM(stars) as total FROM users');
        stats.totalStars = stars[0].total || 0;
        
        return stats;
    }

    async getUserByName(username) {
        const users = await this.query(
            'SELECT u.*, a.registerDate FROM users u INNER JOIN accounts a ON u.accountID = a.accountID WHERE u.userName = ? LIMIT 1',
            [username]
        );
        return users[0] || null;
    }

    async getTopPlayers(limit = 10) {
        return await this.query(
            'SELECT userName, stars, demons, creatorPoints, userCoins FROM users WHERE isRegistered = 1 ORDER BY stars DESC LIMIT ?',
            [limit]
        );
    }

    async getLevelByName(levelName) {
        const levels = await this.query(
            'SELECT * FROM levels WHERE levelName LIKE ? ORDER BY downloads DESC LIMIT 1',
            [`%${levelName}%`]
        );
        return levels[0] || null;
    }

    async getRecentLevels(limit = 10) {
        return await this.query(
            'SELECT levelID, levelName, userName, stars, downloads, uploadDate FROM levels ORDER BY uploadDate DESC LIMIT ?',
            [limit]
        );
    }

    async close() {
        await this.pool.end();
        console.log('✓ Database pool closed');
    }
}

module.exports = Database;
