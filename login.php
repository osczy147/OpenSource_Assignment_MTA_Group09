<?php
require_once 'includes/auth.php';
if (isLoggedIn()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    if ($u && $p) {
        if (loginUser($u, $p)) {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login — ArtVault</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="logo">Art<span>Vault</span></div>
    <h2 style="font-family:'Syne',sans-serif;font-size:1.2rem;margin-bottom:0.3rem">Welcome back</h2>
    <p style="color:var(--text-secondary);font-size:0.85rem;margin-bottom:1.5rem">Sign in to manage your portfolio</p>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Username or Email</label>
        <input type="text" name="username" class="form-control" placeholder="your_username"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Sign In</button>
    </form>

    <p style="text-align:center;margin-top:1.2rem;font-size:0.85rem;color:var(--text-secondary)">
      No account? <a href="register.php" style="color:var(--accent)">Create one</a>
    </p>
  </div>
</div>
</body>
</html>
