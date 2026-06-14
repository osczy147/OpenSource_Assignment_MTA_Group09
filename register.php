<?php
require_once 'includes/auth.php';
if (isLoggedIn()) { header('Location: index.php'); exit; }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if (!$full_name || !$username || !$email || !$password) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $conn = getConnection();
        // Check uniqueness
        $chk = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $chk->bind_param("ss", $username, $email);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $error = 'Username or email already in use.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name,username,email,password) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $full_name, $username, $email, $hash);
            if ($stmt->execute()) {
                $success = 'Account created! You can now log in.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register — ArtVault</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card" style="max-width:460px">
    <div class="logo">Art<span>Vault</span></div>
    <h2 style="font-family:'Syne',sans-serif;font-size:1.2rem;margin-bottom:0.3rem">Create your portfolio</h2>
    <p style="color:var(--text-secondary);font-size:0.85rem;margin-bottom:1.5rem">Join ArtVault to showcase your creative work</p>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="login.php" style="color:var(--success)">Sign in →</a></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name" class="form-control" placeholder="Amina Rashidi"
               value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" class="form-control" placeholder="amina_art"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" class="form-control" placeholder="amina@email.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" class="form-control" placeholder="min. 6 characters" required>
        </div>
        <div class="form-group">
          <label>Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control" placeholder="repeat password" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Create Account</button>
    </form>

    <p style="text-align:center;margin-top:1.2rem;font-size:0.85rem;color:var(--text-secondary)">
      Already a member? <a href="login.php" style="color:var(--accent)">Sign in</a>
    </p>
  </div>
</div>
</body>
</html>
