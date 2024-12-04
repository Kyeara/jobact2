<?php
$host = "localhost";       
$user = "root";           
$password = "";            
$dbname = "dreamjob";      

$dsn = "mysql:host={$host};dbname={$dbname}";

try {
    
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
} catch (PDOException $e) {
    
    die("Database connection failed: " . $e->getMessage());
}
?>
