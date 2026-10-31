<?php
require_once 'config.php';
require_login();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Fetch note – only the owner may edit
$stmt = $conn->prepare("SELECT * FROM notes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$note = $stmt->get_result()->fetch_assoc();
if (!$note) { die("Note not found or you don't have permission."); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']);
    $subject = trim($_POST['subject']);
    $desc    = trim($_POST['description']);
    $fileName = $note['file_name'];

    if ($title === '' || $subject === '') {
        $error = "Title and subject are required.";
    } else {
        if (!empty($_FILES['file']['name'])) {
            $new = save_upload($_FILES['file'], $error);
            if ($new) {
                if ($fileName && file_exists("uploads/$fileName")) unlink("uploads/$fileName");
                $fileName = $new;
            }
        }
        if ($error === '') {
            // UPDATE note
            $stmt = $conn->prepare("UPDATE notes SET title=?, subject=?, description=?, file_name=? WHERE id=? AND user_id=?");
            $stmt->bind_param("ssssii", $title, $subject, $desc, $fileName, $id, $_SESSION['user_id']);
            $stmt->execute();
            header("Location: index.php?msg=Note updated successfully");
            exit;
        }
    }
}
include 'header.php';
?>
<h2>Edit Note</h2>
<?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form class="box" method="post" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= $note['id'] ?>">
  <label>Title</label><input type="text" name="title" value="<?= e($note['title']) ?>" required>
  <label>Subject</label><input type="text" name="subject" value="<?= e($note['subject']) ?>" required>
  <label>Description</label><textarea name="description" rows="4"><?= e($note['description']) ?></textarea>
  <label>Replace file (optional)</label><input type="file" name="file">
  <?php if ($note['file_name']): ?><div class="meta">Current file: <?= e($note['file_name']) ?></div><?php endif; ?>
  <br><button type="submit">Update</button>
</form>
<?php include 'footer.php'; ?>
