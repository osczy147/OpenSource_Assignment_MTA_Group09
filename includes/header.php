<?php
// includes/header.php
// Usage: require_once 'includes/header.php';
// Set $pageTitle before including.
$pageTitle = $pageTitle ?? 'ArtVault';
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?> — ArtVault</title>
  <link rel="stylesheet" href="<?= $cssPath ?? '' ?>css/style.css">
</head>
<body>
<nav>
  <a href="<?= $cssPath ?? '' ?>index.php" class="nav-brand">Art<span>Vault</span></a>
  <div class="nav-links">
    <a href="<?= $cssPath ?? '' ?>index.php">Gallery</a>
    <a href="<?= $cssPath ?? '' ?>search.php">Search</a>
    <?php if ($user): ?>
      <a href="<?= $cssPath ?? '' ?>add_portfolio.php">+ Add Work</a>
      <?php if ($user['role'] === 'admin'): ?>
        <a href="<?= $cssPath ?? '' ?>admin/users.php">Users</a>
      <?php endif; ?>
      <a href="<?= $cssPath ?? '' ?>logout.php" class="btn-nav">Logout (<?= htmlspecialchars($user['username']) ?>)</a>
    <?php else: ?>
      <a href="<?= $cssPath ?? '' ?>login.php">Login</a>
      <a href="<?= $cssPath ?? '' ?>register.php" class="btn-nav">Join</a>
    <?php endif; ?>
  </div>
</nav>
