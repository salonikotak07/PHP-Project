<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NoteNest</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
  <a class="brand" href="index.php">NoteNest</a>
  <div>
    <a href="index.php">All Notes</a>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="index.php?mine=1">My Notes</a>
      <a href="add_note.php">Upload Note</a>
      <a href="logout.php">Logout (<?= e($_SESSION['user_name']) ?>)</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    <?php endif; ?>
  </div>
</nav>
<div class="container">
