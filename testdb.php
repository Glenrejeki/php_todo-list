<?php
$host = 'localhost';
$port = '5432';
$db   = 'glenreje_todolist';
$user = 'glenreje_todolistuser';
$pass = 'Todo@2025!';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<h3 style='color:green'>✅ Koneksi ke PostgreSQL berhasil!</h3>";
} catch (PDOException $e) {
    echo "<h3 style='color:red'>❌ Gagal koneksi:</h3> " . $e->getMessage();
}
