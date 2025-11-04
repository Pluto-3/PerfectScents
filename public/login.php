<?php

require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/session.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Valid login
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            header('Location: dashboard.php');
            exit();
        } else {
            $message = "Invalid username or password.";
        }
    } else {
        $message = "Please enter username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= SITE_NAME ?> - Login</title>
<link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
<div class="login-container">
    <h2>Login to <?= SITE_NAME ?></h2>
    <?php if ($message): ?>
        <p style="color:red;"><?= $message ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
