<?php
/**
 * GDCore Admin Panel - Login
 */

session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/connection.php';

use GDCore\Config\Config;

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        // Check admin credentials
        $stmt = $db->prepare(
            "SELECT a.*, r.roleType FROM accounts a
             INNER JOIN roles r ON a.accountID = r.accountID
             WHERE a.userName = ? AND r.roleType >= 2
             LIMIT 1"
        );
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['accountID'];
            $_SESSION['admin_username'] = $admin['userName'];
            $_SESSION['admin_role'] = $admin['roleType'];
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Please enter both username and password';
    }
}

$config = Config::getInstance();
$serverName = $config->get('SERVER_NAME', 'GDCore');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?= htmlspecialchars($serverName) ?></title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>🎮 <?= htmlspecialchars($serverName) ?></h1>
                <p>Admin Panel</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Login
                </button>
            </form>
            
            <div class="login-footer">
                <p>Authorized access only</p>
            </div>
        </div>
    </div>
</body>
</html>
