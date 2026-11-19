<?php
require 'Auth.php';
require 'dblogin.php';
// prepared statement 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare("DELETE FROM students WHERE id=?");
    $stmt->execute([$_POST['id']]);

    echo "Deleted!";
}
?>

<a href="list.php">Back</a>