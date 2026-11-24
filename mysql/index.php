<?php
declare(strict_types=1);
session_start();
require_once dirname(__DIR__) . '/app/db.php';

function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(24)); return $_SESSION['csrf']; }
function redirect(string $url = 'index.php'): never { header('Location: ' . $url); exit; }
function current_user(): ?array { return $_SESSION['cbt_user'] ?? null; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string)($_POST['csrf'] ?? ''))) {
        http_response_code(403); exit('Token keamanan tidak valid. Muat ulang halaman.');
    }
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'login') {
        $stmt = $pdo->prepare('SELECT id,username,password_hash,full_name,role,class_id,is_active FROM users WHERE username=? LIMIT 1');
        $stmt->execute([trim((string)($_POST['username'] ?? ''))]);
        $account = $stmt->fetch();
        if ($account && (int)$account['is_active'] === 1 && password_verify((string)($_POST['password'] ?? ''), $account['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['cbt_user'] = [
                'id'=>(int)$account['id'], 'username'=>$account['username'],
                'name'=>$account['full_name'], 'role'=>$account['role'],
                'class_id'=>$account['class_id'] === null ? null : (int)$account['class_id']
            ];
            $pdo->prepare('UPDATE users SET last_login_at=NOW() WHERE id=?')->execute([$account['id']]);
            redirect();
        }
        $_SESSION['flash'] = ['Username atau password salah, atau akun dinonaktifkan.', 'error'];
        redirect();
    }
    if ($action === 'logout') {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy(); redirect();
    }
    if ($action === 'create_user' && current_user() && current_user()['role'] === 'admin') {
        $name = trim((string)($_POST['full_name'] ?? ''));
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $role = (string)($_POST['role'] ?? '');
        $grade = (int)($_POST['grade'] ?? 0);
        $classId = null;
        if ($role === 'student') {
            $stmt = $pdo->prepare('SELECT id FROM classes WHERE grade=? ORDER BY id LIMIT 1');
            $stmt->execute([$grade]); $classId = $stmt->fetchColumn() ?: null;
        }
        if ($name === '' || !preg_match('/^[a-zA-Z0-9._-]{4,40}$/', $username) || strlen($password) < 8 || !in_array($role,['teacher','student'],true) || ($role==='student' && !$classId)) {
            $_SESSION['flash'] = ['Data tidak valid. Password minimal 8 karakter; pilih kelas untuk siswa.', 'error'];
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO users(username,password_hash,full_name,role,class_id,nisn,must_change_password) VALUES(?,?,?,?,?,?,1)');
                $stmt->execute([$username,password_hash($password,PASSWORD_DEFAULT),$name,$role,$classId,$role==='student'?$username:null]);
                $_SESSION['flash'] = ['Akun '.($role==='teacher'?'guru':'siswa').' berhasil dibuat. Berikan username dan password awal secara pribadi.', 'success'];
            } catch (PDOException $ex) {
                $_SESSION['flash'] = ['Username/NISN sudah terpakai atau data tidak dapat disimpan.', 'error'];
            }
        }
        redirect();
    }
    if ($action === 'toggle_user' && current_user() && current_user()['role'] === 'admin') {
        $target=(int)($_POST['user_id']??0);
        if ($target !== current_user()['id']) {
            $pdo->prepare("UPDATE users SET is_active=1-is_active WHERE id=? AND role IN ('teacher','student')")->execute([$target]);
        }
        redirect();
    }
    redirect();
}

$me = current_user();
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
$classes = $pdo->query('SELECT id,name,grade FROM classes ORDER BY grade')->fetchAll();
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#102b25"><title>School CBT — MTs Darul Muhsinin NWDI</title><style>
*{box-sizing:border-box}body{margin:0;background:#f4f7f4;color:#19352b;font:15px/1.5 system-ui,-apple-system,"Segoe UI",sans-serif}header{background:#102b25;color:white;padding:15px max(18px,calc((100% - 1100px)/2));display:flex;justify-content:space-between;align-items:center;gap:12px}.brand{font-weight:800}.brand small{display:block;color:#b9d0c3;font-weight:500}.wrap{max-width:1100px;margin:30px auto;padding:0 18px}.card{background:white;border:1px solid #e0e9e2;border-radius:15px;padding:21px;margin-bottom:17px}.login{max-width:430px;margin:8vh auto}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px}.field{margin:12px 0}.field label{display:block;font-weight:700;font-size:13px;margin-bottom:5px}.field input,.field select{width:100%;padding:11px;border:1px solid #d5e1d8;border-radius:9px;font:inherit}.btn{border:0;border-radius:9px;background:#176b51;color:white;padding:10px 15px;font-weight:750;cursor:pointer}.btn.gray{background:#eaf1ec;color:#254c3b}.btn.red{background:#a63e36}.notice{padding:12px;border-radius:9px;background:#e6f4e9;margin-bottom:15px}.notice.error{background:#fff0ed;color:#9c3932}.muted{color:#73847a}.pill{background:#e8f4ed;color:#246447;border-radius:20px;padding:4px 9px;font-size:12px;font-weight:700}.table{overflow:auto}table{width:100%;border-collapse:collapse;min-width:540px}td,th{text-align:left;padding:10px;border-bottom:1px solid #e7ece8}th{font-size:12px;color:#718078;text-transform:uppercase}.inline{display:inline}h1{line-height:1.2;letter-spacing:-.03em}@media(max-width:600px){header{padding:13px 14px}.wrap{margin:20px auto}.card{padding:16px}}
</style></head><body>
<?php if(!$me):?><main class="wrap"><section class="card login"><div class="brand" style="color:#176b51">CBT MTs Darul Muhsinin NWDI<small style="color:#71847a">Surabaya Utara · Kelas VII, VIII, IX</small></div><h1>Masuk ke School CBT</h1><p class="muted">Gunakan username atau NIS siswa dan password yang diberikan Admin.</p><?php if($flash):?><div class="notice <?=e($flash[1])?>"><?=e($flash[0])?></div><?php endif?><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="login"><div class="field"><label>Username / NIS</label><input name="username" autocomplete="username" required autofocus></div><div class="field"><label>Password</label><input type="password" name="password" autocomplete="current-password" required></div><button class="btn" style="width:100%">Masuk</button></form></section></main>
<?php else:?><header><div class="brand">School CBT <small>MTs Darul Muhsinin NWDI Surabaya Utara</small></div><div><?=e($me['name'])?> · <?=e(strtoupper($me['role']))?> <form method="post" class="inline"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="logout"><button class="btn gray">Keluar</button></form></div></header><main class="wrap"><?php if($flash):?><div class="notice <?=e($flash[1])?>"><?=e($flash[0])?></div><?php endif?>
<?php if($me['role']==='admin'):$users=$pdo->query('SELECT u.id,u.username,u.full_name,u.role,u.is_active,c.name class_name FROM users u LEFT JOIN classes c ON c.id=u.class_id ORDER BY u.role,u.full_name')->fetchAll();$summary=$pdo->query("SELECT role,COUNT(*) total FROM users GROUP BY role")->fetchAll();?>
<h1>Dashboard Administrator</h1><p class="muted">Kelola akun guru, siswa, dan persiapan ujian.</p><div class="grid"><section class="card"><h2>Tambah akun</h2><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="create_user"><div class="field"><label>Nama lengkap</label><input name="full_name" required></div><div class="field"><label>Username / NIS</label><input name="username" minlength="4" maxlength="40" required></div><div class="field"><label>Password awal (min. 8 karakter)</label><input type="password" name="password" minlength="8" required></div><div class="field"><label>Jenis akun</label><select name="role" id="role" onchange="document.getElementById('gradewrap').hidden=this.value!=='student'"><option value="teacher">Guru</option><option value="student">Siswa</option></select></div><div class="field" id="gradewrap" hidden><label>Kelas</label><select name="grade"><?php foreach([7,8,9] as $g):?><option value="<?=$g?>">Kelas <?=$g?></option><?php endforeach?></select></div><button class="btn">Simpan akun</button></form></section><section class="card"><h2>Ringkasan pengguna</h2><?php foreach(['admin'=>'Administrator','teacher'=>'Guru','student'=>'Siswa'] as $role=>$label):$n=0;foreach($summary as $s)if($s['role']===$role)$n=(int)$s['total'];?><p><span class="pill"><?=$label?></span> <b style="float:right"><?=$n?></b></p><?php endforeach?><p class="muted">Akun siswa menggunakan NIS sebagai username. Sampaikan password awal secara pribadi.</p></section></div><section class="card"><h2>Daftar pengguna</h2><div class="table"><table><thead><tr><th>Nama</th><th>Username/NIS</th><th>Peran</th><th>Kelas</th><th>Status</th><th>Aksi</th></tr></thead><tbody><?php foreach($users as $u):?><tr><td><?=e($u['full_name'])?></td><td><?=e($u['username'])?></td><td><?=e($u['role'])?></td><td><?=e($u['class_name']??'-')?></td><td><?=$u['is_active']?'Aktif':'Nonaktif'?></td><td><?php if($u['role']!=='admin'):?><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="toggle_user"><input type="hidden" name="user_id" value="<?=$u['id']?>"><button class="btn <?= $u['is_active']?'red':'gray'?>"><?=$u['is_active']?'Nonaktifkan':'Aktifkan'?></button></form><?php endif?></td></tr><?php endforeach?></tbody></table></div></section>
<?php elseif($me['role']==='teacher'):?><h1>Dashboard Guru</h1><p>Assalamu'alaikum, <?=e($me['name'])?>. Modul pengelolaan ujian dan bank soal sedang disiapkan.</p><section class="card"><h2>Modul guru</h2><p class="muted">Tahap berikutnya: buat ujian, kelola bank soal bergambar, atur token, jadwal, durasi, dan lihat hasil siswa.</p></section>
<?php else:$q=$pdo->prepare('SELECT name FROM classes WHERE id=?');$q->execute([$me['class_id']]);$className=$q->fetchColumn()?:'Belum ditentukan';?><h1>Beranda Siswa</h1><p>Assalamu'alaikum, <?=e($me['name'])?>.</p><section class="card"><span class="pill"><?=e($className)?></span><h2>Ujian yang tersedia</h2><p class="muted">Belum ada ujian yang dipublikasikan untuk kelas Anda.</p></section><?php endif?></main><?php endif?></body></html>
