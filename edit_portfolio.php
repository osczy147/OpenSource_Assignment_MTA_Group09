<?php
require_once 'includes/auth.php';
requireLogin();
$pageTitle = 'Edit Portfolio Item';

$conn  = getConnection();
$user  = getCurrentUser();
$id    = intval($_GET['id'] ?? 0);

// Fetch item - owner or admin only
$stmt = $conn->prepare("SELECT * FROM portfolios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item || ($item['user_id'] != $user['id'] && $user['role'] !== 'admin')) {
    header('Location: index.php?error=not_found');
    exit;
}

$categories = ['2D Animation','3D Animation','Illustration','Motion Graphics','Photography','Video','Graphic Design','Other'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $category    = $_POST['category'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $tools       = trim($_POST['tools_used'] ?? '');
    $year        = intval($_POST['project_year'] ?? 0);
    $featured    = isset($_POST['is_featured']) ? 1 : 0;

    if (!$title || !$category) {
        $error = 'Title and category are required.';
    } elseif (!in_array($category, $categories)) {
        $error = 'Invalid category.';
    } else {
        $image_filename = $item['image_filename'];

        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg','jpeg','png','gif','webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $error = 'Only JPG, PNG, GIF, WEBP images allowed.';
            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $error = 'Image must be under 5MB.';
            } else {
                $new_name = uniqid('art_', true) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $new_name)) {
                    // Delete old file
                    if ($image_filename && file_exists('uploads/' . $image_filename)) {
                        unlink('uploads/' . $image_filename);
                    }
                    $image_filename = $new_name;
                } else {
                    $error = 'Image upload failed.';
                }
            }
        }

        if (!$error) {
            $upd = $conn->prepare("UPDATE portfolios SET title=?,category=?,description=?,tools_used=?,image_filename=?,project_year=?,is_featured=? WHERE id=?");
            $upd->bind_param("sssssiiii", $title, $category, $description, $tools, $image_filename, $year, $featured, $id);
            if ($upd->execute()) {
                $success = 'Portfolio item updated!';
                // Refresh item data
                $stmt2 = $conn->prepare("SELECT * FROM portfolios WHERE id=?");
                $stmt2->bind_param("i", $id);
                $stmt2->execute();
                $item = $stmt2->get_result()->fetch_assoc();
            } else {
                $error = 'Update failed. Please try again.';
            }
        }
    }
}

require_once 'includes/header.php';
?>
<div class="container">
  <div class="page-header">
    <h1>Edit Portfolio Item</h1>
    <p>Update the details of this artwork or project</p>
  </div>

  <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <div class="form-card wide">
    <form method="POST" enctype="multipart/form-data">
      <div class="form-row">
        <div class="form-group">
          <label>Title *</label>
          <input type="text" name="title" class="form-control"
                 value="<?= htmlspecialchars($item['title']) ?>" required>
        </div>
        <div class="form-group">
          <label>Category *</label>
          <select name="category" class="form-control" required>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat ?>" <?= $item['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Tools / Software Used</label>
          <input type="text" name="tools_used" class="form-control"
                 value="<?= htmlspecialchars($item['tools_used'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Year</label>
          <input type="number" name="project_year" class="form-control"
                 min="2000" max="<?= date('Y') ?>"
                 value="<?= $item['project_year'] ?>">
        </div>
      </div>

      <?php if ($item['image_filename'] && file_exists('uploads/' . $item['image_filename'])): ?>
        <div class="form-group">
          <label>Current Image</label>
          <img src="uploads/<?= htmlspecialchars($item['image_filename']) ?>" alt="current"
               style="max-height:160px;border-radius:8px;border:1px solid var(--border)">
        </div>
      <?php endif; ?>

      <div class="form-group">
        <label>Replace Image (optional, max 5MB)</label>
        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
      </div>

      <?php if ($user['role'] === 'admin'): ?>
      <div class="form-group" style="display:flex;align-items:center;gap:0.5rem">
        <input type="checkbox" name="is_featured" id="featured" <?= $item['is_featured'] ? 'checked' : '' ?>>
        <label for="featured" style="margin:0;cursor:pointer">★ Mark as Featured</label>
      </div>
      <?php endif; ?>

      <div style="display:flex;gap:0.8rem;margin-top:0.5rem">
        <button type="submit" class="btn btn-primary">Update Item</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php require_once 'includes/footer.php'; $conn->close(); ?>
