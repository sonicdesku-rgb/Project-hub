<?php
require 'Auth.php';
require 'dblogin.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name']  ?? '');
    $email      = trim($_POST['email']      ?? '');
    $major      = trim($_POST['major']      ?? '');
    $gpa        = trim($_POST['gpa']        ?? '');
    $year       = trim($_POST['year']       ?? '');

    // Validation
    if (empty($first_name))  $errors[] = "First name is required";
    if (empty($last_name))   $errors[] = "Last name is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email";
    if (empty($major))       $errors[] = "Major is required";
    if (!is_numeric($gpa) || $gpa < 0 || $gpa > 4.0) $errors[] = "GPA must be between 0.0 and 4.0";
    if (!is_numeric($year))  $errors[] = "Invalid enrollment year";
    
    // If there are no validation errors, insert the new student record into the database
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO students 
            (first_name, last_name, email, major, gpa, enrollment_year)
            VALUES (?, ?, ?, ?, ?, ?)");

        $stmt->execute([$first_name, $last_name, $email, $major, $gpa, $year]);
        $success = "Student added successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Portal — Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container-sm">
 
    <div class="page-header">
        <span class="logo">Student Portal</span>
        <a href="Mainmenu.php" class="header-link">&larr; Menu</a>
    </div>
 
    <h1 class="page-title">Add Student</h1>
    <p class="page-sub">Fill in the details to register a new student</p>
 
    <?php foreach ($errors as $e): ?>
        <div class="alert alert-error"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>
 
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
 
    <form method="POST" action="">
        <div class="field">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" placeholder="e.g. Emory"
                   value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" placeholder="e.g. Smith"
                   value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="e.g. emory@fsc.edu"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="major">Major</label>
            <input type="text" id="major" name="major" placeholder="e.g. Computer Science"
                   value="<?= htmlspecialchars($_POST['major'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="gpa">GPA</label>
            <input type="number" id="gpa" name="gpa" step="0.01" min="0" max="4"
                   placeholder="0.00  4.00" value="<?= htmlspecialchars($_POST['gpa'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="year">Enrollment Year</label>
            <input type="number" id="year" name="year" placeholder="e.g. 2023"
                   value="<?= htmlspecialchars($_POST['year'] ?? '') ?>">
        </div>
 
        <button type="submit" class="btn">Add Student</button>
    </form>
 
</div>
<script>
    const form = document.getElementById('addForm');
 
    function showError(id, hasError) {
        const err   = document.getElementById('err-' + id);
        const input = document.getElementById(id);
        err.style.display = hasError ? 'block' : 'none';
        input.classList.toggle('invalid', hasError);
        input.classList.toggle('valid',  !hasError);
        return !hasError;
    }
 
    function validateRequired(id) {
        const val = document.getElementById(id).value.trim();
        return showError(id, val === '');
    }
 
    function validateEmail() {
        const val   = document.getElementById('email').value.trim();
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return showError('email', !regex.test(val));
    }
 
    function validateGPA() {
        const val = parseFloat(document.getElementById('gpa').value);
        return showError('gpa', isNaN(val) || val < 0 || val > 4.0);
    }
 
    function validateYear() {
        const val = document.getElementById('year').value.trim();
        return showError('year', !/^\d{4}$/.test(val));
    }
 
    // Live validation on each field
    document.getElementById('first_name').addEventListener('input', () => validateRequired('first_name'));
    document.getElementById('last_name').addEventListener('input',  () => validateRequired('last_name'));
    document.getElementById('email').addEventListener('input', validateEmail);
    document.getElementById('major').addEventListener('input',      () => validateRequired('major'));
    document.getElementById('gpa').addEventListener('input', validateGPA);
    document.getElementById('year').addEventListener('input', validateYear);
 
    // Block submit if any field is invalid
    form.addEventListener('submit', function (e) {
        const valid =
            validateRequired('first_name') &
            validateRequired('last_name')  &
            validateEmail()                &
            validateRequired('major')      &
            validateGPA()                  &
            validateYear();
 
        if (!valid) e.preventDefault();
    });
</script>
</body>
</html>