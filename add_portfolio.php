<?php
require_once 'includes/auth.php';
requireLogin();
$pageTitle = 'Add Portfolio Item';

$error = '';
$success = '';

$categories = ['2D Animation','3D Animation','Illustration','Motion Graphics','Photography','Video','Graphic Design','Other'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $category    = $_POST['category'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $tools       = trim($_POST['tools_used'] ?? '');
    $year        = intval($_POST['project_year'] ?? 0);
    $featured    = isset($_POST['is_featured']) ? 1 : 0;
    $user        = getCurrentUser();

    if (!$title || !$category) {
        $error = 'Title and category are required.';
    } elseif (!in_array($category, $categories)) {
        $error = 'Invalid category.';
    } else {
        // Handle image upload
        $image_filename = null;
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg','jpeg','png','gif','webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $error = 'Only JPG, PNG, GIF, WEBP images allowed.';
            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $error = 'Image must be under 5MB.';
            } else {
                $image_filename = uniqid('art_', true) . '.' . $ext;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image_filename)) {
                    $error = 'Image upload failed.';
                    $image_filename = null;
                }
            }
        }

        if (!$error) {
            $conn = getConnection();
            $stmt = $conn->prepare("INSERT INTO portfolios (user_id,title,category,description,tools_used,image_filename,project_year,is_featured)
                                    VALUES (?,?,?,?,?,?,?,?)");
            $stmt->bind_param("isssssii", $user['id'], $title, $category, $description, $tools, $image_filename, $year, $featured);
            if ($stmt->execute()) {
                $success = 'Portfolio item added successfully!';
            } else {
                $error = 'Database error. Please try again.';
            }
            $conn->close();
        }
    }
}

require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-header">
    <h1>Add Portfolio Item</h1>
    <p>Showcase a new piece of your creative work</p>
  </div>

  <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="index.php" style="color:var(--success)">View gallery →</a></div><?php endif; ?>

  <div class="form-card wide">
    <form method="POST" enctype="multipart/form-data">
      <div class="form-row">
        <div class="form-group">
          <label>Title *</label>
          <input type="text" name="title" class="form-control" placeholder="Samson — Animated Short"
                 value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Category *</label>
          <select name="category" class="form-control" required>
            <option value="">Select category…</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat ?>" <?= ($_POST['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" placeholder="Describe this project — concept, process, inspiration…"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Tools / Software Used</label>
          <input type="text" name="tools_used" class="form-control" placeholder="Moho, After Effects, Photoshop…"
                 value="<?= htmlspecialchars($_POST['tools_used'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Year</label>
          <input type="number" name="project_year" class="form-control" placeholder="2025"
                 min="2000" max="<?= date('Y') ?>"
                 value="<?= htmlspecialchars($_POST['project_year'] ?? date('Y')) ?>">
        </div>
      </div>

      <div class="form-group">
        <label>Artwork / Preview Image (max 5MB)</label>
        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
      </div>

      <?php if (getCurrentUser()['role'] === 'admin'): ?>
      <div class="form-group" style="display:flex;align-items:center;gap:0.5rem">
        <input type="checkbox" name="is_featured" id="featured" <?= isset($_POST['is_featured']) ? 'checked' : '' ?>>
        <label for="featured" style="margin:0;cursor:pointer">★ Mark as Featured</label>
      </div>
      <?php endif; ?>

      <div style="display:flex;gap:0.8rem;margin-top:0.5rem">
        <button type="submit" class="btn btn-primary">Save Portfolio Item</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
