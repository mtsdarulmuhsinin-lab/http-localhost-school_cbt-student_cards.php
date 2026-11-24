<?php
declare(strict_types=1);
session_start();
require_once dirname(__DIR__) . '/app/db.php';

$count = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['install_csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
        $error = 'Permintaan tidak valid. Muat ulang halaman.';
    } elseif ($count > 0) {
        $error = 'Admin sudah dibuat. Installer tidak dapat digunakan kembali.';
    } else {
        $name = trim((string)($_POST['name'] ?? ''));
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['confirm'] ?? '');
        if ($name === '' || !preg_match('/^[a-zA-Z0-9._-]{4,40}$/', $username) || strlen($password) < 10 || $password !== $confirm) {
            $error = 'Isi nama, username 4–40 karakter, dan password minimal 10 karakter yang sama.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO users(username,password_hash,full_name,role,must_change_password) VALUES(?,?,?,'admin',0)");
            try {
                $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $name]);
                $message = 'Akun admin berhasil dibuat. Hapus atau batasi akses file installer, lalu masuk melalui halaman login.';
                $count = 1;
            } catch (PDOException $e) {
                $error = 'Username sudah digunakan atau data tidak dapat disimpan.';
            }
        }
    }
}
$_SESSION['install_csrf'] = bin2hex(random_bytes(24));
?><!doctype html><html lang="id"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Instalasi CBT MySQL</title>
<style>body{font:16px system-ui;background:#f3f6f3;color:#18352b;margin:0;padding:24px}.box{max-width:520px;margin:5vh auto;background:white;padding:28px;border-radius:16px;border:1px solid #dfe8e1}label{display:block;font-weight:650;margin:14px 0 5px}input{box-sizing:border-box;width:100%;padding:12px;border:1px solid #ccd9d0;border-radius:9px;font:inherit}button{margin-top:20px;padding:12px 18px;background:#176b51;color:white;border:0;border-radius:9px;font-weight:700}.msg{padding:12px;background:#e7f4eb;border-radius:8px}.err{padding:12px;background:#fff0ed;color:#a33d35;border-radius:8px}</style>
<div class="box"><h1>Instalasi School CBT</h1><p>Database: <b>school_cbt</b> · MTs Darul Muhsinin NWDI Surabaya Utara</p>
<?php if($message):?><p class="msg"><?=htmlspecialchars($message,ENT_QUOTES,'UTF-8')?></p><p><a href="index.php">Buka halaman login</a></p>
<?php elseif($count>0):?><p class="msg">Akun admin sudah tersedia. Installer dinonaktifkan.</p><p><a href="index.php">Buka halaman login</a></p>
<?php else:?><?php if($error):?><p class="err"><?=htmlspecialchars($error,ENT_QUOTES,'UTF-8')?></p><?php endif?>
<form method="post"><input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['install_csrf'],ENT_QUOTES,'UTF-8')?>"><label>Nama lengkap admin</label><input name="name" required><label>Username admin</label><input name="username" minlength="4" maxlength="40" required><label>Password (minimal 10 karakter)</label><input type="password" name="password" minlength="10" required><label>Ulangi password</label><input type="password" name="confirm" minlength="10" required><button type="submit">Buat akun admin</button></form><?php endif?></div></html>
