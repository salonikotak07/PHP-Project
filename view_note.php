<?php
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT n.*, u.name AS author FROM notes n JOIN users u ON n.user_id = u.id WHERE n.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$n = $stmt->get_result()->fetch_assoc();

include 'header.php';
if (!$n) { echo "<p>Note not found.</p>"; include 'footer.php'; exit; }
?>
<div class="card">
  <h2><?= e($n['title']) ?></h2>
  <span class="tag"><?= e($n['subject']) ?></span>
  <div class="meta">By <?= e($n['author']) ?> &middot; <?= date('d M Y', strtotime($n['created_at'])) ?></div>
  <p><?= nl2br(e($n['description'])) ?></p>
  <?php if ($n['file_name']): ?>
    <a class="btn" href="uploads/<?= e($n['file_name']) ?>" download>Download File</a>
  <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
