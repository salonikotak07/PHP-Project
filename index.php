<?php
require_once 'config.php';

$search = trim($_GET['subject'] ?? '');
$mine   = isset($_GET['mine']) && isset($_SESSION['user_id']);

// READ notes (all / by subject / only mine)
$sql = "SELECT n.*, u.name AS author FROM notes n JOIN users u ON n.user_id = u.id WHERE 1=1";
$types = ""; $params = [];

if ($search !== '') {
    $sql .= " AND n.subject LIKE ?";
    $types .= "s"; $params[] = "%$search%";
}
if ($mine) {
    $sql .= " AND n.user_id = ?";
    $types .= "i"; $params[] = $_SESSION['user_id'];
}
$sql .= " ORDER BY n.created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$notes = $stmt->get_result();

include 'header.php';
?>
<h2><?= $mine ? 'My Notes' : 'All Notes' ?></h2>

<?php if (isset($_GET['msg'])): ?><div class="success"><?= e($_GET['msg']) ?></div><?php endif; ?>

<form class="search" method="get">
  <?php if ($mine): ?><input type="hidden" name="mine" value="1"><?php endif; ?>
  <input type="text" name="subject" placeholder="Search by subject (e.g. Maths)" value="<?= e($search) ?>">
  <button type="submit">Search</button>
</form>

<?php if ($notes->num_rows === 0): ?>
  <p>No notes found.</p>
<?php endif; ?>

<?php while ($n = $notes->fetch_assoc()): ?>
  <div class="card">
    <h3><a href="view_note.php?id=<?= $n['id'] ?>"><?= e($n['title']) ?></a></h3>
    <span class="tag"><?= e($n['subject']) ?></span>
    <div class="meta">By <?= e($n['author']) ?> &middot; <?= date('d M Y', strtotime($n['created_at'])) ?></div>
    <p><?= e(mb_strimwidth($n['description'], 0, 150, '...')) ?></p>

    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $n['user_id']): ?>
      <div class="actions">
        <a class="btn btn-edit" href="edit_note.php?id=<?= $n['id'] ?>">Edit</a>
        <form method="post" action="delete_note.php" onsubmit="return confirm('Delete this note?');">
          <input type="hidden" name="id" value="<?= $n['id'] ?>">
          <button class="btn-danger" type="submit">Delete</button>
        </form>
      </div>
    <?php endif; ?>
  </div>
<?php endwhile; ?>

<?php include 'footer.php'; ?>
