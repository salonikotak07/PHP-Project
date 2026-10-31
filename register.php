<?php
require_once 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name === '' || $email === '' || strlen($password) < 6) {
        $error = "Fill all fields. Password must be at least 6 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        // Check duplicate email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // CREATE user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hash);
            $stmt->execute();
            header("Location: login.php?registered=1");
            exit;
        }
    }
}
include 'header.php';
?>
<h2>Register</h2>
<?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form class="box" method="post">
  <label>Name</label><input type="text" name="name" required>
  <label>Email</label><input type="email" name="email" required>
  <label>Password</label><input type="password" name="password" required>
  <br><br><button type="submit">Create Account</button>
</form>
<?php include 'footer.php'; ?>
