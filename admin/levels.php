<?php
session_start();
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/connection.php';

// Handle rate/delete actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'rate') {
        $stmt = $db->prepare("UPDATE levels SET stars = ?, featured = ?, epic = ? WHERE levelID = ?");
        $stmt->execute([$_POST['stars'], $_POST['featured'] ?? 0, $_POST['epic'] ?? 0, $_POST['levelID']]);
        $message = 'Level rated';
    } elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM levels WHERE levelID = ?");
        $stmt->execute([$_POST['levelID']]);
        $message = 'Level deleted';
    }
}

// Get levels
$page = $_GET['page'] ?? 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;

$stmt = $db->prepare("SELECT * FROM levels ORDER BY uploadDate DESC LIMIT ? OFFSET ?");
$stmt->execute([$perPage, $offset]);
$levels = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<h1>🎮 Levels</h1>
<?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>

<table class="data-table">
    <thead>
        <tr><th>ID</th><th>Name</th><th>Creator</th><th>⭐</th><th>📥</th><th>Featured</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($levels as $level): ?>
        <tr>
            <td><?= $level['levelID'] ?></td>
            <td><?= htmlspecialchars($level['levelName']) ?></td>
            <td><?= htmlspecialchars($level['userName']) ?></td>
            <td><?= $level['stars'] ?: '-' ?></td>
            <td><?= $level['downloads'] ?></td>
            <td><?= $level['featured'] ? '⭐' : '-' ?></td>
            <td>
                <button class="btn btn-sm" onclick="rateLevel(<?= $level['levelID'] ?>)">Rate</button>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="levelID" value="<?= $level['levelID'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
