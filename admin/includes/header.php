<?php
use GDCore\Config\Config;
$config = Config::getInstance();
$serverName = $config->get('SERVER_NAME', 'GDCore');
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($serverName) ?> - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>🎮 <?= htmlspecialchars($serverName) ?></h2>
                <p>Admin Panel</p>
            </div>
            
            <ul class="nav-menu">
                <li>
                    <a href="dashboard.php" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                        📊 Dashboard
                    </a>
                </li>
                <li>
                    <a href="users.php" class="<?= $currentPage === 'users' ? 'active' : '' ?>">
                        👥 Users
                    </a>
                </li>
                <li>
                    <a href="levels.php" class="<?= $currentPage === 'levels' ? 'active' : '' ?>">
                        🎮 Levels
                    </a>
                </li>
                <li>
                    <a href="comments.php" class="<?= $currentPage === 'comments' ? 'active' : '' ?>">
                        💬 Comments
                    </a>
                </li>
                <li>
                    <a href="moderation.php" class="<?= $currentPage === 'moderation' ? 'active' : '' ?>">
                        🛡️ Moderation
                    </a>
                </li>
                <li>
                    <a href="daily.php" class="<?= $currentPage === 'daily' ? 'active' : '' ?>">
                        📅 Daily/Weekly
                    </a>
                </li>
                <li>
                    <a href="settings.php" class="<?= $currentPage === 'settings' ? 'active' : '' ?>">
                        ⚙️ Settings
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <div class="admin-info">
                    <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>
                    <span class="role-badge">Admin</span>
                </div>
                <a href="logout.php" class="btn-logout">🚪 Logout</a>
            </div>
        </nav>
        
        <main class="main-content">
            <div class="container">
