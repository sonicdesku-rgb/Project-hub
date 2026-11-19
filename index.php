<?php
session_start(); // starts the session so $_SESSION is available
require_once __DIR__ . '/dblogin.php'; 

$error = '';  // initializes error as empty string

// Checks if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Validation: checks if username or password is empty
    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields"; // sets error message if username or password is empty
    } else {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE username = :un");
        $stmt->execute([':un' => $username]);
        $row = $stmt->fetch();
        $hashed = $row ? $row['password'] : null;

        if ($hashed && password_verify($password, $hashed)) {
            // If password is correct, set session and redirect to main menu
            $_SESSION['username'] = $username;
            header("Location: Mainmenu.php");
            exit();
        } else {
            $error = "Invalid username or password"; // sets error message if username doesn't exist or password is incorrect
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Portal — Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container-sm">
 
    <div class="page-header">
        <span class="logo">Student Portal</span>
        <a href="register.php" class="header-link">Register &rarr;</a>
    </div>
 
    <h1 class="page-title">Login</h1>
    <p class="page-sub">Enter your credentials to continue</p>
 
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
 
    <form method="POST" action="">
        <div class="field">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="e.g. bsmith">
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="••••••••">
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
 
</div>
</body>
</html>