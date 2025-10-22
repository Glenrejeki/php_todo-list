<?php
require __DIR__ . '/config.php';
try {
    // kalau kamu pakai pg_connect di model, coba gunakan pg_connect
    $connStr = "host=".DB_HOST." port=".DB_PORT." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASSWORD;
    $res = @pg_connect($connStr);
    if (!$res) {
        throw new Exception('pg_connect failed');
    }
    $r = pg_query($res, 'SELECT 1 as ok');
    $row = pg_fetch_assoc($r);
    echo "DB OK: " . ($row['ok'] ?? 'no') . PHP_EOL;
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}




