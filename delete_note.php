<?php
require_once 'config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];

    // Get file name (owner only)
    $stmt = $conn->prepare("SELECT file_name FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
    $note = $stmt->get_result()->fetch_assoc();

    if ($note) {
        // DELETE note
        $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $_SESSION['user_id']);
        $stmt->execute();

        if ($note['file_name'] && file_exists("uploads/" . $note['file_name'])) {
            unlink("uploads/" . $note['file_name']);
        }
        header("Location: index.php?msg=Note deleted");
        exit;
    }
}
header("Location: index.php");
exit;
