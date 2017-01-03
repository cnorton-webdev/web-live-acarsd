<?php
require_once 'database.php';
if (!empty($_POST['id']) && isset($_POST['id'])) {
    $pdo = Database::connect();
    $stmt = $pdo->prepare('UPDATE acars SET flagged = 1 WHERE id = ?');
    $stmt->execute(array($_POST['id']));
}