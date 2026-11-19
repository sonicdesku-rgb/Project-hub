<?php
require 'Auth.php';
require 'dblogin.php';
 
$allowed = ['first_name', 'last_name', 'major'];
$results = [];
$searched = false;
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field = $_POST['field'] ?? '';
    $value = trim($_POST['value'] ?? '');
    $searched = true;
 
    if (in_array($field, $allowed) && !empty($value)) {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE $field LIKE ?");
        $stmt->execute(["%$value%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Portal — Search</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { align-items: flex-start; padding-top: 3rem; }
    </style>
</head>
<body>
<div class="container">
 
    <div class="page-header">
        <span class="logo">Student Portal</span>
        <a href="Mainmenu.php" class="header-link">&larr; Menu</a>
    </div>
 
    <h1 class="page-title">Search Students</h1>
    <p class="page-sub">Search by name or major</p>
 
    <form method="POST" action="">
        <div class="search-row">
            <div class="field">
                <label for="field">Search By</label>
                <select id="field" name="field">
                    <option value="first_name" <?= ($_POST['field'] ?? '') === 'first_name' ? 'selected' : '' ?>>First Name</option>
                    <option value="last_name"  <?= ($_POST['field'] ?? '') === 'last_name'  ? 'selected' : '' ?>>Last Name</option>
                    <option value="major"      <?= ($_POST['field'] ?? '') === 'major'      ? 'selected' : '' ?>>Major</option>
                </select>
            </div>
            <div class="field">
                <label for="value">Keyword</label>
                <input type="text" id="value" name="value"
                       placeholder="e.g. Smith"
                       value="<?= htmlspecialchars($_POST['value'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-sm">Search</button>
        </div>
    </form>
 
    <?php if ($searched): ?>
        <?php if (empty($results)): ?>
            <div class="alert alert-error">No students matched your search.</div>
        <?php else: ?>
            <p class="page-sub"><?= count($results) ?> result<?= count($results) !== 1 ? 's' : '' ?> found</p>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Major</th>
                            <th>GPA</th>
                            <th>Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['major']) ?></td>
                            <td><?= htmlspecialchars($row['gpa']) ?></td>
                            <td><?= htmlspecialchars($row['enrollment_year']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
 
</div>
</body>
<script>
    const form = document.getElementById('searchForm');
 
    function validateKeyword() {
        const val   = document.getElementById('value').value.trim();
        const err   = document.getElementById('err-value');
        const input = document.getElementById('value');
        const hasError = val === '';
        err.style.display = hasError ? 'block' : 'none';
        input.classList.toggle('invalid', hasError);
        return !hasError;
    }
 
    // Live validation — clears error as soon as user starts typing
    document.getElementById('value').addEventListener('input', validateKeyword);
 
    // Block submit if keyword is empty
    form.addEventListener('submit', function (e) {
        if (!validateKeyword()) e.preventDefault();
    });
</script>
</html>