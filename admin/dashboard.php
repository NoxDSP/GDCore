<?php
/**
 * GDCore Admin Panel - Dashboard
 */

session_start();
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/connection.php';

// Get statistics
$stats = [];

// Total users
$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE isRegistered = 1");
$stats['users'] = $stmt->fetch()['count'];

// Total levels
$stmt = $db->query("SELECT COUNT(*) as count FROM levels");
$stats['levels'] = $stmt->fetch()['count'];

// Total comments
$stmt = $db->query("SELECT COUNT(*) as count FROM comments");
$stats['comments'] = $stmt->fetch()['count'];

// Active bans
$stmt = $db->query("SELECT COUNT(*) as count FROM bans WHERE expireDate IS NULL OR expireDate > NOW()");
$stats['bans'] = $stmt->fetch()['count'];

// Recent activity (last 24h)
$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE registerDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
$stats['new_users_24h'] = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM levels WHERE uploadDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
$stats['new_levels_24h'] = $stmt->fetch()['count'];

// Top players
$stmt = $db->query("SELECT userName, stars, demons, creatorPoints FROM users ORDER BY stars DESC LIMIT 5");
$topPlayers = $stmt->fetchAll();

// Recent levels
$stmt = $db->query("SELECT levelID, levelName, userName, uploadDate, stars, downloads FROM levels ORDER BY uploadDate DESC LIMIT 10");
$recentLevels = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="dashboard">
    <h1>📊 Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?= number_format($stats['users']) ?></h3>
                <p>Total Users</p>
                <span class="stat-change">+<?= $stats['new_users_24h'] ?> today</span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🎮</div>
            <div class="stat-info">
                <h3><?= number_format($stats['levels']) ?></h3>
                <p>Total Levels</p>
                <span class="stat-change">+<?= $stats['new_levels_24h'] ?> today</span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">💬</div>
            <div class="stat-info">
                <h3><?= number_format($stats['comments']) ?></h3>
                <p>Total Comments</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🚫</div>
            <div class="stat-info">
                <h3><?= number_format($stats['bans']) ?></h3>
                <p>Active Bans</p>
            </div>
        </div>
    </div>
    
    <div class="content-grid">
        <div class="content-section">
            <h2>🏆 Top Players</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Player</th>
                        <th>⭐ Stars</th>
                        <th>👹 Demons</th>
                        <th>🏅 CP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topPlayers as $player): ?>
                        <tr>
                            <td><?= htmlspecialchars($player['userName']) ?></td>
                            <td><?= number_format($player['stars']) ?></td>
                            <td><?= number_format($player['demons']) ?></td>
                            <td><?= number_format($player['creatorPoints']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="content-section">
            <h2>🆕 Recent Levels</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Level</th>
                        <th>Creator</th>
                        <th>⭐</th>
                        <th>📥</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentLevels as $level): ?>
                        <tr>
                            <td>
                                <a href="levels.php?action=view&id=<?= $level['levelID'] ?>">
                                    <?= htmlspecialchars($level['levelName']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($level['userName']) ?></td>
                            <td><?= $level['stars'] ?: '-' ?></td>
                            <td><?= number_format($level['downloads']) ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($level['uploadDate'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
