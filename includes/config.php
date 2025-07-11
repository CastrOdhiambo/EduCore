<?php
// config.php
$host = 'localhost';
$db = 'educore';
$user = 'root'; // or your MySQL user
$pass = '';     // or your MySQL password

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("<div style='
        max-width: 400px;
        margin: 2rem auto;
        padding: 1.5rem;
        background: #ffebee;
        color: #c62828;
        border-radius: 8px;
        border: 1px solid #d32f2f;
        text-align: center;'>
        Database connection failed: " . $e->getMessage() . "</div>");
}
?>
