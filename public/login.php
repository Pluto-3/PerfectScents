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
        $message = "Please enter both username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= SITE_NAME ?> - Login</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --primary: #009B77;
        --secondary: #F5F5F5;
        --text-main: #222;
        --text-muted: #666;
        --border: #E0E0E0;
        --bg: #FFFFFF;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--secondary);
        color: var(--text-main);
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
    }

    .login-container {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 40px;
        width: 100%;
        max-width: 380px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .login-container h2 {
        font-weight: 600;
        margin-bottom: 24px;
        color: var(--text-main);
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    label {
        text-align: left;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-muted);
    }

    input {
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 1rem;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(0, 155, 119, 0.15);
    }

    button {
        background-color: var(--primary);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
    }

    button:hover {
        background-color: #00866A;
        transform: translateY(-1px);
    }

    .message {
        color: #DC3545;
        font-size: 0.9rem;
        margin-bottom: 12px;
    }

    .footer-text {
        margin-top: 20px;
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .footer-text strong {
        color: var(--primary);
    }
</style>
</head>
<body>

<div class="login-container">
    <h2><?= SITE_NAME ?></h2>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" autocomplete="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>

        <button type="submit">Login</button>
    </form>

    <div class="footer-text">
        <p>© <?= date('Y') ?> <strong><?= SITE_NAME ?></strong>. All rights reserved.</p>
        <p>Powered by <strong>WzrdPluto</strong></p>
    </div>
</div>

</body>
</html>
