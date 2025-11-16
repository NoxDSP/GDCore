<?php
/**
 * GDCore Admin Panel - Users Management
 */

session_start();
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/connection.php';

// Handle actions
$message = '';
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'ban' && isset($_POST['accountID'])) {
        $accountID = $_POST['accountID'];
        $reason = $_POST['reason'] ?? 'No reason provided';
        $duration = $_POST['duration'] ?? null;
        
        $expireDate = $duration ? date('Y-m-d H:i:s', strtotime("+{$duration} days")) : null;
        
        $stmt = $db->prepare("INSERT INTO bans (accountID, reason, expireDate) VALUES (?, ?, ?)");
        $stmt->execute([$accountID, $reason, $expireDate]);
        
        $message = 'User banned successfully';
    } elseif ($action === 'unban' && isset($_POST['accountID'])) {
        $accountID = $_POST['accountID'];
        $stmt = $db->prepare("DELETE FROM bans WHERE accountID = ?");
        $stmt->execute([$accountID]);
        
        $message = 'User unbanned successfully';
    } elseif ($action === 'promote' && isset($_POST['accountID'])) {
        $accountID = $_POST['accountID'];
        $roleType = $_POST['roleType'];
        
        $stmt = $db->prepare("SELECT * FROM roles WHERE accountID = ?");
        $stmt->execute([$accountID]);
        
        if ($stmt->fetch()) {
            $stmt = $db->prepare("UPDATE roles SET roleType = ? WHERE accountID = ?");
            $stmt->execute([$roleType, $accountID]);
        } else {
            $stmt = $db->prepare("INSERT INTO roles (accountID, roleType) VALUES (?, ?)");
            $stmt->execute([$accountID, $roleType]);
        }
        
        $message = 'User role updated successfully';
    }
}

// Pagination
$page = $_GET['page'] ?? 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Search
$search = $_GET['search'] ?? '';
$whereClause = $search ? "WHERE u.userName LIKE ?" : "";
$searchParam = $search ? "%{$search}%" : null;

// Get total count
$countQuery = "SELECT COUNT(*) as count FROM users u INNER JOIN accounts a ON u.accountID = a.accountID $whereClause";
$stmt = $searchParam ? $db->prepare($countQuery) : $db->query($countQuery);
if ($searchParam) $stmt->execute([$searchParam]);
$totalUsers = $stmt->fetch()['count'];
$totalPages = ceil($totalUsers / $perPage);

// Get users
$query = "
    SELECT u.*, a.email, a.registerDate,
           (SELECT roleType FROM roles WHERE accountID = u.accountID LIMIT 1) as roleType,
           (SELECT COUNT(*) FROM bans WHERE accountID = u.accountID AND (expireDate IS NULL OR expireDate > NOW())) as isBanned
    FROM users u
    INNER JOIN accounts a ON u.accountID = a.accountID
    $whereClause
    ORDER BY u.stars DESC
    LIMIT ? OFFSET ?
";

$stmt = $db->prepare($query);
$params = $searchParam ? [$searchParam, $perPage, $offset] : [$perPage, $offset];
$stmt->execute($params);
$users = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="users-page">
    <div class="page-header">
        <h1>👥 User Management</h1>
        
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search users..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if ($search): ?>
                <a href="users.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <div class="content-section">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Stars</th>
                    <th>Demons</th>
                    <th>CP</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['accountID'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($user['userName']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= number_format($user['stars']) ?></td>
                        <td><?= number_format($user['demons']) ?></td>
                        <td><?= number_format($user['creatorPoints']) ?></td>
                        <td>
                            <?php if ($user['roleType'] >= 2): ?>
                                <span class="badge badge-danger">Admin</span>
                            <?php elseif ($user['roleType'] == 1): ?>
                                <span class="badge badge-warning">Mod</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['isBanned']): ?>
                                <span class="badge badge-error">Banned</span>
                            <?php else: ?>
                                <span class="badge badge-success">Active</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <?php if (!$user['isBanned']): ?>
                                    <button class="btn btn-sm btn-danger" onclick="banUser(<?= $user['accountID'] ?>, '<?= htmlspecialchars($user['userName']) ?>')">
                                        Ban
                                    </button>
                                <?php else: ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="accountID" value="<?= $user['accountID'] ?>">
                                        <button type="submit" name="action" value="unban" class="btn btn-sm btn-success">
                                            Unban
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <button class="btn btn-sm btn-secondary" onclick="promoteUser(<?= $user['accountID'] ?>, '<?= htmlspecialchars($user['userName']) ?>', <?= $user['roleType'] ?? 0 ?>)">
                                    Role
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                       class="<?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Ban Modal -->
<div id="banModal" class="modal" style="display:none;">
    <div class="modal-content">
        <h2>Ban User</h2>
        <form method="POST" action="?action=ban">
            <input type="hidden" name="accountID" id="banAccountID">
            
            <div class="form-group">
                <label>User: <strong id="banUsername"></strong></label>
            </div>
            
            <div class="form-group">
                <label for="reason">Reason</label>
                <textarea name="reason" id="reason" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="duration">Duration (days)</label>
                <select name="duration" id="duration">
                    <option value="">Permanent</option>
                    <option value="1">1 day</option>
                    <option value="7">7 days</option>
                    <option value="30">30 days</option>
                    <option value="365">1 year</option>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="submit" class="btn btn-danger">Ban User</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('banModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Promote Modal -->
<div id="promoteModal" class="modal" style="display:none;">
    <div class="modal-content">
        <h2>Change User Role</h2>
        <form method="POST" action="?action=promote">
            <input type="hidden" name="accountID" id="promoteAccountID">
            
            <div class="form-group">
                <label>User: <strong id="promoteUsername"></strong></label>
            </div>
            
            <div class="form-group">
                <label for="roleType">Role</label>
                <select name="roleType" id="promoteRoleType" required>
                    <option value="0">User (No permissions)</option>
                    <option value="1">Moderator</option>
                    <option value="2">Admin</option>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">Update Role</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('promoteModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function banUser(accountID, username) {
    document.getElementById('banAccountID').value = accountID;
    document.getElementById('banUsername').textContent = username;
    document.getElementById('banModal').style.display = 'flex';
}

function promoteUser(accountID, username, currentRole) {
    document.getElementById('promoteAccountID').value = accountID;
    document.getElementById('promoteUsername').textContent = username;
    document.getElementById('promoteRoleType').value = currentRole;
    document.getElementById('promoteModal').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
