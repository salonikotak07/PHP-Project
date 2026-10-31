<?php
require_once 'config.php';
require_login();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']);
    $subject = trim($_POST['subject']);
    $desc    = trim($_POST['description']);
    $fileName = null;

    if ($title === '' || $subject === '') {
        $error = "Title and subject are required.";
    } else {
        if (!empty($_FILES['file']['name'])) {
            $fileName = save_upload($_FILES['file'], $error);
        }
        if ($error === '') {
            // CREATE note
            $stmt = $conn->prepare("INSERT INTO notes (user_id, title, subject, description, file_name) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $_SESSION['user_id'], $title, $subject, $desc, $fileName);
            $stmt->execute();
            header("Location: index.php?msg=Note uploaded successfully");
            exit;
        }
    }
}
include 'header.php';
?>
<h2>Upload Note</h2>
<?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form class="box" method="post" enctype="multipart/form-data">
  <label>Title</label><input type="text" name="title" required>
  <label>Subject</label><input type="text" name="subject" required>
  <label>Description</label><textarea name="description" rows="4"></textarea>
  <label>File (PDF, DOC, PPT, TXT - max 5MB)</label><input type="file" name="file">
  <br><br><button type="submit">Upload</button>
</form>
<?php include 'footer.php'; ?>
