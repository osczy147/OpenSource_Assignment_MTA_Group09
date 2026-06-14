<?php
require_once 'includes/auth.php';
$pageTitle = 'Gallery';
require_once 'includes/header.php';

$conn = getConnection();

// Stats
$totalPortfolios = $conn->query("SELECT COUNT(*) as c FROM portfolios")->fetch_assoc()['c'];
$totalArtists    = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='artist'")->fetch_assoc()['c'];
$totalCategories = $conn->query("SELECT COUNT(DISTINCT category) as c FROM portfolios")->fetch_assoc()['c'];

// Filter by category
$filterCat = $_GET['cat'] ?? '';
$where = $filterCat ? "WHERE p.category = '" . $conn->real_escape_string($filterCat) . "'" : '';

$portfolios = $conn->query("
    SELECT p.*, u.full_name AS artist_name
    FROM portfolios p
    JOIN users u ON p.user_id = u.id
    $where
    ORDER BY p.is_featured DESC, p.created_at DESC
    LIMIT 50
");

$categories = $conn->query("SELECT DISTINCT category FROM portfolios ORDER BY category");
?>

<?php if (!$user): ?>
<section class="hero">
  <div class="container">
    <p class="hero-eyebrow">Multimedia Technology & Animation — ArtVault</p>
    <h1>Where Creative Work<br><em>Lives & Breathes</em></h1>
    <p>A professional digital portfolio system for animators, illustrators, and multimedia artists.</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
      <a href="register.php" class="btn btn-primary">Create Portfolio</a>
      <a href="search.php" class="btn btn-secondary">Explore Work</a>
    </div>
  </div>
</section>
<?php endif; ?>

<div class="container" style="padding-top:2rem">

  <!-- Stats -->
  <div class="stats-row">
    <div class="stat-card"><div class="num"><?= $totalPortfolios ?></div><div class="lbl">Portfolio Items</div></div>
    <div class="stat-card"><div class="num"><?= $totalArtists ?></div><div class="lbl">Artists</div></div>
    <div class="stat-card"><div class="num"><?= $totalCategories ?></div><div class="lbl">Categories</div></div>
  </div>

  <!-- Category filter tabs -->
  <div class="tabs">
    <a href="index.php" class="tab <?= !$filterCat ? 'active' : '' ?>">All</a>
    <?php while ($cat = $categories->fetch_assoc()): ?>
      <a href="index.php?cat=<?= urlencode($cat['category']) ?>"
         class="tab <?= $filterCat === $cat['category'] ? 'active' : '' ?>">
        <?= htmlspecialchars($cat['category']) ?>
      </a>
    <?php endwhile; ?>
  </div>

  <!-- Portfolio grid -->
  <?php if ($portfolios->num_rows === 0): ?>
    <div class="empty-state">
      <div class="icon">🎨</div>
      <h3>No portfolio items yet</h3>
      <p>Be the first to showcase your work.</p>
      <br>
      <?php if ($user): ?>
        <a href="add_portfolio.php" class="btn btn-primary">Add Your First Item</a>
      <?php else: ?>
        <a href="register.php" class="btn btn-primary">Join ArtVault</a>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="grid grid-3">
      <?php while ($item = $portfolios->fetch_assoc()): ?>
        <div class="card">
          <div class="card-img">
            <?php if ($item['image_filename'] && file_exists('uploads/' . $item['image_filename'])): ?>
              <img src="uploads/<?= htmlspecialchars($item['image_filename']) ?>"
                   alt="<?= htmlspecialchars($item['title']) ?>">
            <?php else: ?>
              🖼️
            <?php endif; ?>
          </div>
          <div class="card-body">
            <h3>
              <?php if ($item['is_featured']): ?><span class="star">★ </span><?php endif; ?>
              <?= htmlspecialchars($item['title']) ?>
            </h3>
            <p><?= htmlspecialchars(substr($item['description'] ?? '', 0, 90)) ?><?= strlen($item['description'] ?? '') > 90 ? '…' : '' ?></p>
          </div>
          <div class="card-meta">
            <span class="badge badge-purple"><?= htmlspecialchars($item['category']) ?></span>
            <span style="font-size:0.78rem;color:var(--text-muted)"><?= htmlspecialchars($item['artist_name']) ?><?= $item['project_year'] ? ' · ' . $item['project_year'] : '' ?></span>
          </div>
          <?php if ($user && ($user['id'] == $item['user_id'] || $user['role'] === 'admin')): ?>
          <div style="padding:0.6rem 1.4rem;border-top:1px solid var(--border);display:flex;gap:0.5rem">
            <a href="edit_portfolio.php?id=<?= $item['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
            <a href="delete_portfolio.php?id=<?= $item['id'] ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('Delete this item?')">Delete</a>
          </div>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; $conn->close(); ?>
