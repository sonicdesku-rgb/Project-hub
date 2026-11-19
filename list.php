<h2>List of Students</h2>
<?php
require 'Auth.php'; // checks login — redirects to index.php if not logged in
require 'dblogin.php'; // loads database connection

// Fetches all student records from the database and displays them in a table
$stmt = $pdo->query("SELECT * FROM students");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Major</th><th>Action</th></tr>";

// Loops through each student record and creates a table row with the student's information and a delete button
foreach ($rows as $row) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['major']) . "</td>";
     echo "<td>" . htmlspecialchars($row['gpa']) . "</td>";

    echo "<td>
        <form method='POST' action='delete.php' onsubmit='return confirm(\"Are you sure?\")'>
            <input type='hidden' name='id' value='{$row['id']}'>
            <button>Delete</button>
        </form>
    </td>";

    echo "</tr>";
}

echo "</table>";
?>
<a href="Mainmenu.php">Back</a>
<a href="Update.php?id=<?= $row['id'] ?>">Edit</a>