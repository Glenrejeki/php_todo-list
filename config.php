<?php
$host = 'localhost';
$port = '5432';
$db   = 'glenreje_todolist';
$user = 'glenreje_todolistuser';
$pass = 'Todo@2025!';

$pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
