<?php
session_start();

$conn = new mysqli("localhost", "root", "", "notenest");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Escape output (prevents XSS)
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

// Redirect to login if not logged in
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Validate and save an uploaded file. Returns file name or null (and sets $error)
function save_upload($file, &$error) {
    $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file['error'] !== UPLOAD_ERR_OK) { $error = "File upload failed."; return null; }
    if (!in_array($ext, $allowed))        { $error = "Only PDF, DOC, DOCX, PPT, PPTX, TXT allowed."; return null; }
    if ($file['size'] > 5 * 1024 * 1024)  { $error = "File must be under 5 MB."; return null; }

    $newName = uniqid('note_', true) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . "/uploads/" . $newName)) {
        $error = "Could not save the file."; return null;
    }
    return $newName;
}
