<?php
require_once 'includes/auth.php';
requireLogin();

$conn = getConnection();
$user = getCurrentUser();
$id   = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM portfolios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if ($item && ($item['user_id'] == $user['id'] || $user['role'] === 'admin')) {
    // Delete image file
    if ($item['image_filename'] && file_exists('uploads/' . $item['image_filename'])) {
        unlink('uploads/' . $item['image_filename']);
    }
    $del = $conn->prepare("DELETE FROM portfolios WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();
}

$conn->close();
header('Location: index.php?msg=deleted');
exit;
?>
