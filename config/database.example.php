<?php
declare(strict_types=1);

/*
 * Salin file ini menjadi config/database.php lalu isi kredensial MySQL XAMPP.
 * Jangan commit config/database.php karena berisi password database.
 */
return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'school_cbt',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];
