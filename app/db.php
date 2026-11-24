<?php
declare(strict_types=1);

/**
 * MySQL connection helper. Copy config/database.example.php to
 * config/database.php and set local credentials before including this file.
 */
$configFile = dirname(__DIR__) . '/config/database.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('Konfigurasi database belum dibuat. Salin config/database.example.php menjadi config/database.php.');
}
$config = require $configFile;

$host = (string)($config['host'] ?? '127.0.0.1');
$port = (int)($config['port'] ?? 3306);
$name = (string)($config['database'] ?? 'school_cbt');
$user = (string)($config['username'] ?? 'root');
$pass = (string)($config['password'] ?? '');
$charset = (string)($config['charset'] ?? 'utf8mb4');

$dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $exception) {
    error_log('School CBT database connection failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Koneksi database gagal. Periksa konfigurasi MySQL dan pastikan layanan MySQL XAMPP aktif.');
}
