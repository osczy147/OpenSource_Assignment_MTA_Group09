<?php
$reason = $_GET['reason'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setup Required — ArtVault</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      min-height: 100vh;
      display: grid;
      place-items: center;
      background: radial-gradient(circle at top, rgba(192, 132, 252, 0.18), transparent 38%), #0b0b10;
      color: #f3f4f6;
      font-family: system-ui, sans-serif;
      margin: 0;
      padding: 24px;
    }
    .setup-card {
      width: min(640px, 100%);
      background: rgba(17, 17, 24, 0.92);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 24px;
      padding: 32px;
      box-shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
    }
    .setup-card h1 {
      margin-top: 0;
      font-family: 'Syne', system-ui, sans-serif;
      font-size: clamp(2rem, 4vw, 3rem);
    }
    .setup-card p {
      color: #cbd5e1;
      line-height: 1.6;
    }
    .setup-actions {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 24px;
    }
    .setup-note {
      margin-top: 20px;
      padding: 14px 16px;
      border-radius: 16px;
      background: rgba(192, 132, 252, 0.12);
      color: #e9d5ff;
      font-size: 0.95rem;
    }
  </style>
</head>
<body>
  <main class="setup-card">
    <div class="logo" style="margin-bottom:16px">Art<span>Vault</span></div>
    <h1>Database setup required</h1>
    <p>
      The application can connect to MySQL, but the expected schema is not ready yet.
      Run the installer once to create or repair the <strong>portfolio_db</strong> tables.
    </p>
    <?php if ($reason === 'missing_schema'): ?>
      <div class="setup-note">A page tried to load before the database tables were ready.</div>
    <?php endif; ?>
    <div class="setup-actions">
      <a href="install.php" class="btn btn-primary">Run Installer</a>
      <a href="index.php" class="btn btn-secondary">Try Homepage Again</a>
    </div>
    <div class="setup-note">
      Default admin credentials are created by the installer: username <strong>admin</strong>, password <strong>admin123</strong>.
    </div>
  </main>
</body>
</html>