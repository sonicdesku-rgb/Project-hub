<?php
require_once __DIR__ . '/dblogin.php';
require_once __DIR__ . '/functions.php';

//// Before rendering the form:
$_SESSION['csrf'] = bin2hex(random_bytes(32));

// Initialize an array to hold error messages
$errors = [];
// Initialize a variable to hold success message 
$success = '';

// Checks if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    // Validation checks
    if (strlen($username) < 6) $errors[] = "Username too short";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email";
    if (strlen($password) < 8) $errors[] = "Password too short";
    if ($password !== $confirm) $errors[] = "Passwords do not match";

    if (empty($errors)) {
        // Check duplicate
        $stmt = $pdo->prepare("SELECT username FROM users WHERE username=?");
        $stmt->execute([$username]);
        // If a record is found, add an error message; otherwise, hash the password and insert the new user into the database
        if ($stmt->fetch()) {
            $errors[] = "Account already exists";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:un, :em, :pw)");
            $stmt->execute([':un' => $username, ':em' => $email, ':pw' => $hashed]);

            echo "Registration successful! <a href='index.php'>Login</a>";
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Portal — Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container-sm">
 
    <div class="page-header">
        <span class="logo">Student Portal</span>
        <a href="index.php" class="header-link">&larr; Login</a>
    </div>
 
    <h1 class="page-title">Registration form</h1>
    <p class="page-sub">Fill in the form to register</p>
 
    <?php foreach ($errors as $e): ?>
        <div class="alert alert-error"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>
 
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?> — <a href="index.php">Login here</a>
        </div>
    <?php endif; ?>
 
    <form method="POST" action="">
        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
 
        <div class="field">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="At least 6 characters"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="you@example.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="At least 8 characters">
        </div>
        <div class="field">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat your password">
        </div>
 
        <button type="submit" class="btn">Register</button>
    </form>
 
</div>
<script>
    const form = document.getElementById('registerForm');
 
    // Shows or hides error under a field, marks it valid/invalid
    function showError(id, hasError) {
        const err   = document.getElementById('err-' + id);
        const input = document.getElementById(id);
        err.style.display = hasError ? 'block' : 'none';
        input.classList.toggle('invalid', hasError);
        input.classList.toggle('valid',  !hasError);
        return !hasError; // true = valid
    }
 
    function validateUsername() {
        const val = document.getElementById('username').value.trim();
        return showError('username', val.length < 6);
    }
 
    function validateEmail() {
        const val   = document.getElementById('email').value.trim();
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return showError('email', !regex.test(val));
    }
 
    function validatePassword() {
        const val = document.getElementById('password').value;
        return showError('password', val.length < 8);
    }
 
    function validateConfirm() {
        const pw  = document.getElementById('password').value;
        const cfm = document.getElementById('confirm_password').value;
        return showError('confirm', cfm === '' || pw !== cfm);
    }
 
    // Live validation — fires as the user types in each field
    document.getElementById('username').addEventListener('input', validateUsername);
    document.getElementById('email').addEventListener('input', validateEmail);
    document.getElementById('password').addEventListener('input', function () {
        validatePassword();
        validateConfirm(); // re-check confirm whenever password changes
    });
    document.getElementById('confirm_password').addEventListener('input', validateConfirm);
 
    // Block submit if any field is invalid
    form.addEventListener('submit', function (e) {
        // Use & not && so all fields are checked even if first fails
        const valid =
            validateUsername() &
            validateEmail()    &
            validatePassword() &
            validateConfirm();
 
        if (!valid) e.preventDefault();
    });
</script>
</body>
</html>