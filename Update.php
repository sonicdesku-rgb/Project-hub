<?php
require 'Auth.php';
require 'dblogin.php';

$errors  = [];
$success = '';
$student = null;

// Step 1 — Load student by ID (from list.php link or form)
$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location: list.php");
    exit();
}

// Fetch the student record
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: list.php"); // ID not found — go back to list
    exit();
}

// Step 2 — Handle form submission (update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name']  ?? '');
    $email      = trim($_POST['email']      ?? '');
    $major      = trim($_POST['major']      ?? '');
    $gpa        = trim($_POST['gpa']        ?? '');
    $year       = trim($_POST['year']       ?? '');

    // PHP validation — server side safety net
    if (empty($first_name))  $errors[] = "First name is required";
    if (empty($last_name))   $errors[] = "Last name is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email";
    if (empty($major))       $errors[] = "Major is required";
    if (!is_numeric($gpa) || $gpa < 0 || $gpa > 4.0) $errors[] = "GPA must be between 0.0 and 4.0";
    if (!is_numeric($year) || strlen($year) !== 4)    $errors[] = "Enter a valid 4-digit enrollment year";

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE students SET
            first_name      = ?,
            last_name       = ?,
            email           = ?,
            major           = ?,
            gpa             = ?,
            enrollment_year = ?
            WHERE id = ?");

        $stmt->execute([$first_name, $last_name, $email, $major, $gpa, $year, $id]);
        $success = "Student record updated successfully!";

        // Refresh student data to show updated values
        $stmt    = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $student = $stmt->fetch();
    } else {
        // Keep posted values in form on error instead of reverting to DB values
        $student['first_name']      = $_POST['first_name'];
        $student['last_name']       = $_POST['last_name'];
        $student['email']           = $_POST['email'];
        $student['major']           = $_POST['major'];
        $student['gpa']             = $_POST['gpa'];
        $student['enrollment_year'] = $_POST['year'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Portal — Edit Student</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .field-error { color: #b91c1c; font-size: 12px; margin-top: 4px; display: none; }
        input.invalid { border-color: #f44336; }
        input.valid   { border-color: #04AA6D; }

        /* Student ID badge at top of form */
        .id-badge {
            display: inline-block;
            background: #ede9fe;
            color: #7c3aed;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
<div class="container-sm">

    <div class="page-header">
        <span class="logo">Student Portal</span>
        <a href="list.php" class="header-link">&larr; Back to List</a>
    </div>

    <h1 class="page-title">Edit Student</h1>
    <p class="page-sub">Update the student's details below</p>

    <span class="id-badge">Student ID: <?= htmlspecialchars($id) ?></span>

    <?php foreach ($errors as $e): ?>
        <div class="alert alert-error"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="" id="UpdateForm" novalidate>
        <!-- Hidden field carries the student ID through the POST -->
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <div class="field">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name"
                   placeholder="e.g. Emory"
                   value="<?= htmlspecialchars($student['first_name']) ?>">
            <span class="field-error" id="err-first_name">First name is required</span>
        </div>

        <div class="field">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name"
                   placeholder="e.g. Smith"
                   value="<?= htmlspecialchars($student['last_name']) ?>">
            <span class="field-error" id="err-last_name">Last name is required</span>
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   placeholder="e.g. emory@fsc.edu"
                   value="<?= htmlspecialchars($student['email']) ?>">
            <span class="field-error" id="err-email">Please enter a valid email address</span>
        </div>

        <div class="field">
            <label for="major">Major</label>
            <input type="text" id="major" name="major"
                   placeholder="e.g. Computer Science"
                   value="<?= htmlspecialchars($student['major']) ?>">
            <span class="field-error" id="err-major">Major is required</span>
        </div>

        <div class="field">
            <label for="gpa">GPA</label>
            <input type="number" id="gpa" name="gpa"
                   step="0.01" min="0" max="4"
                   placeholder="0.00 – 4.00"
                   value="<?= htmlspecialchars($student['gpa']) ?>">
            <span class="field-error" id="err-gpa">GPA must be between 0.0 and 4.0</span>
        </div>

        <div class="field">
            <label for="year">Enrollment Year</label>
            <input type="number" id="year" name="year"
                   placeholder="e.g. 2023"
                   value="<?= htmlspecialchars($student['enrollment_year']) ?>">
            <span class="field-error" id="err-year">Enter a valid 4-digit year</span>
        </div>

        <button type="submit" class="btn">Save Changes</button>
    </form>

</div>
<script>
    const form = document.getElementById('editForm');

    function showError(id, hasError) {
        const err   = document.getElementById('err-' + id);
        const input = document.getElementById(id);
        err.style.display = hasError ? 'block' : 'none';
        input.classList.toggle('invalid', hasError);
        input.classList.toggle('valid',  !hasError);
        return !hasError;
    }

    function validateRequired(id) {
        return showError(id, document.getElementById(id).value.trim() === '');
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

    // Live validation as user types
    document.getElementById('first_name').addEventListener('input', () => validateRequired('first_name'));
    document.getElementById('last_name').addEventListener('input',  () => validateRequired('last_name'));
    document.getElementById('email').addEventListener('input', validateEmail);
    document.getElementById('major').addEventListener('input',      () => validateRequired('major'));
    document.getElementById('gpa').addEventListener('input', validateGPA);
    document.getElementById('year').addEventListener('input', validateYear);

    // Block submit if anything invalid
    form.addEventListener('submit', function(e) {
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
