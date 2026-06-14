<?php
$cssPath = '../';
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'User Management';
require_once '../includes/header.php';

$conn = getConnection();
$error = '';
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Add user
    if ($action === 'add') {
        $fn   = trim($_POST['full_name'] ?? '');
        $un   = trim($_POST['username'] ?? '');
        $em   = trim($_POST['email'] ?? '');
        $pw   = $_POST['password'] ?? '';
        $role = in_array($_POST['role'] ?? '', ['admin','artist']) ? $_POST['role'] : 'artist';

        if (!$fn || !$un || !$em || !$pw) {
            $error = 'All fields required.';
        } elseif (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email.';
        } else {
            $hash = password_hash($pw, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name,username,email,password,role) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $fn, $un, $em, $hash, $role);
            if ($stmt->execute()) { $success = "User '$un' created."; }
            else { $error = "Username or email already exists."; }
        }
    }

    // Delete user
    if ($action === 'delete') {
        $uid = intval($_POST['uid'] ?? 0);
        $me  = getCurrentUser();
        if ($uid == $me['id']) {
            $error = "You cannot delete your own account.";
        } else {
            $conn->query("DELETE FROM users WHERE id = $uid");
            $success = "User deleted.";
        }
    }

    // Change role
    if ($action === 'role') {
        $uid  = intval($_POST['uid'] ?? 0);
        $role = in_array($_POST['role'] ?? '', ['admin','artist']) ? $_POST['role'] : 'artist';
        $conn->query("UPDATE users SET role='$role' WHERE id=$uid");
        $success = "Role updated.";
    }
}

$users = $conn->query("SELECT u.*, COUNT(p.id) AS portfolio_count
                        FROM users u
                        LEFT JOIN portfolios p ON p.user_id = u.id
                        GROUP BY u.id
                        ORDER BY u.created_at DESC");
?>

<div class="container">
  <div class="page-header">
    <h1>User Management</h1>
    <p>Manage all registered users and their roles</p>
  </div>

  <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <div style="display:grid;grid-template-columns:1fr 360px;gap:2rem;align-items:start">

    <!-- User Table -->
    <div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Username</th>
              <th>Email</th>
              <th>Role</th>
              <th>Items</th>
              <th>Joined</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $me = getCurrentUser(); while ($u = $users->fetch_assoc()): ?>
            <tr>
              <td style="color:var(--text-muted)"><?= $u['id'] ?></td>
              <td><?= htmlspecialchars($u['full_name']) ?></td>
              <td><code style="color:var(--accent)"><?= htmlspecialchars($u['username']) ?></code></td>
              <td style="color:var(--text-secondary);font-size:0.82rem"><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <span class="badge <?= $u['role'] === 'admin' ? 'badge-yellow' : 'badge-purple' ?>">
                  <?= $u['role'] ?>
                </span>
              </td>
              <td><?= $u['portfolio_count'] ?></td>
              <td style="color:var(--text-muted);font-size:0.78rem"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
              <td>
                <div style="display:flex;gap:0.4rem;flex-wrap:wrap">
                  <!-- Change role -->
                  <form method="POST" style="display:inline">
                    <input type="hidden" name="action" value="role">
                    <input type="hidden" name="uid" value="<?= $u['id'] ?>">
                    <select name="role" class="form-control" style="padding:4px 8px;font-size:0.75rem;height:auto"
                            onchange="this.form.submit()">
                      <option value="artist" <?= $u['role']==='artist'?'selected':'' ?>>artist</option>
                      <option value="admin"  <?= $u['role']==='admin' ?'selected':'' ?>>admin</option>
                    </select>
                  </form>
                  <?php if ($u['id'] != $me['id']): ?>
                  <form method="POST" onsubmit="return confirm('Delete user <?= htmlspecialchars($u['username']) ?>?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="uid" value="<?= $u['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Del</button>
                  </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add User Form -->
    <div class="form-card">
      <h3 style="font-family:'Syne',sans-serif;margin-bottom:1.2rem">Add New User</h3>
      <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="full_name" class="form-control" placeholder="John Makwela" required>
        </div>
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" class="form-control" placeholder="john_art" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" class="form-control" placeholder="john@email.com" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" class="form-control" placeholder="min. 6 characters" required>
        </div>
        <div class="form-group">
          <label>Role</label>
          <select name="role" class="form-control">
            <option value="artist">Artist</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Add User</button>
      </form>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; $conn->close(); ?>
