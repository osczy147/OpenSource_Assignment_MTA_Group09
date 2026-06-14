<?php
require_once 'includes/auth.php';
$pageTitle = 'Search Portfolio';
require_once 'includes/header.php';

$conn = getConnection();

$query    = trim($_GET['q'] ?? '');
$category = $_GET['category'] ?? '';
$year     = intval($_GET['year'] ?? 0);

$categories = ['2D Animation','3D Animation','Illustration','Motion Graphics','Photography','Video','Graphic Design','Other'];

$sql    = "SELECT p.*, u.full_name AS artist_name FROM portfolios p JOIN users u ON p.user_id = u.id WHERE 1=1";
$params = [];
$types  = '';

if ($query) {
    $sql    .= " AND (p.title LIKE ? OR p.description LIKE ? OR p.tools_used LIKE ? OR u.full_name LIKE ?)";
    $like    = '%' . $query . '%';
    $params  = array_merge($params, [$like, $like, $like, $like]);
    $types  .= 'ssss';
}

if ($category && in_array($category, $categories)) {
    $sql    .= " AND p.category = ?";
    $params[] = $category;
    $types   .= 's';
}

if ($year > 1999 && $year <= intval(date('Y'))) {
    $sql    .= " AND p.project_year = ?";
    $params[] = $year;
    $types   .= 'i';
}

$sql .= " ORDER BY p.is_featured DESC, p.created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$results = $stmt->get_result();
$count   = $results->num_rows;
?>

<div class="container">
  <div class="page-header">
    <h1>Search Portfolio</h1>
    <p>Find artwork, animations, and creative projects</p>
  </div>

  <!-- Search Form -->
  <div class="form-card" style="max-width:100%;margin-bottom:2rem">
    <form method="GET" style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:1rem;align-items:end;flex-wrap:wrap">
      <div class="form-group" style="margin:0">
        <label>Keyword</label>
        <input type="text" name="q" class="form-control" placeholder="title, artist, tools…"
               value="<?= htmlspecialchars($query) ?>">
      </div>
      <div class="form-group" style="margin:0">
        <label>Category</label>
        <select name="category" class="form-control">
          <option value="">All categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat ?>" <?= $category === $cat ? 'selected' : '' ?>><?= $cat ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="margin:0">
        <label>Year</label>
        <input type="number" name="year" class="form-control" placeholder="e.g. 2024"
               min="2000" max="<?= date('Y') ?>" value="<?= $year ?: '' ?>">
      </div>
      <button type="submit" class="btn btn-primary" style="height:42px">Search</button>
    </form>
  </div>

  <!-- Results -->
  <?php if ($query || $category || $year): ?>
    <p style="color:var(--text-secondary);margin-bottom:1.5rem;font-size:0.9rem">
      Found <strong><?= $count ?></strong> result<?= $count !== 1 ? 's' : '' ?>
      <?= $query ? ' for <strong>"' . htmlspecialchars($query) . '"</strong>' : '' ?>
    </p>

    <?php if ($count === 0): ?>
      <div class="empty-state">
        <div class="icon">🔍</div>
        <h3>No results found</h3>
        <p>Try a different keyword or broaden your filters.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Title</th>
              <th>Category</th>
              <th>Artist</th>
              <th>Tools</th>
              <th>Year</th>
              <?php if ($user && $user['role'] === 'admin'): ?><th>Actions</th><?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $results->fetch_assoc()): ?>
            <tr>
              <td>
                <?php if ($row['is_featured']): ?><span class="star">★ </span><?php endif; ?>
                <strong><?= htmlspecialchars($row['title']) ?></strong>
                <?php if ($row['description']): ?>
                  <br><span style="color:var(--text-muted);font-size:0.8rem"><?= htmlspecialchars(substr($row['description'], 0, 70)) ?>…</span>
                <?php endif; ?>
              </td>
              <td><span class="badge badge-purple"><?= htmlspecialchars($row['category']) ?></span></td>
              <td><?= htmlspecialchars($row['artist_name']) ?></td>
              <td style="color:var(--text-secondary);font-size:0.82rem"><?= htmlspecialchars($row['tools_used'] ?? '—') ?></td>
              <td><?= $row['project_year'] ?: '—' ?></td>
              <?php if ($user && $user['role'] === 'admin'): ?>
              <td>
                <a href="edit_portfolio.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                <a href="delete_portfolio.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Delete this item?')">Del</a>
              </td>
              <?php endif; ?>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  <?php else: ?>
    <div class="empty-state">
      <div class="icon">🎨</div>
      <h3>Start searching</h3>
      <p>Enter a keyword, pick a category, or filter by year above.</p>
    </div>
  <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; $conn->close(); ?>
