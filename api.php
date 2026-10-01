<?php
include 'koneksi.php';
$a = $_GET['action'] ?? '';
$d = json_decode(file_get_contents("php://input"), true) ?? [];
$F = ['name','nisn','ttl','gender','email','phone','address'];

function val($d, $F) { $r = []; foreach ($F as $f) $r[$f] = trim($d[$f] ?? ''); $r['gender'] = strtoupper($r['gender']); return $r; }
function emailTaken($conn, $e, $except = 0) {
    $s = $conn->prepare("SELECT id FROM users WHERE email=? AND id<>?");
    $s->bind_param("si", $e, $except); $s->execute();
    return $s->get_result()->num_rows > 0;
}
function akunInfo($v) {
    $r = [];
    foreach (['name' => 'Nama', 'nisn' => 'NISN', 'ttl' => 'TTL', 'gender' => 'Gender', 'email' => 'Email', 'phone' => 'No. HP', 'address' => 'Alamat'] as $k => $l)
        $r[] = "• $l: " . ($v[$k] !== '' ? $v[$k] : '-');
    return implode("\n", $r);
}
function check($conn, $v, $except = 0) {
    if ($v['name'] === '' || !filter_var($v['email'], FILTER_VALIDATE_EMAIL)) out(["error" => "Isi nama dan email yang valid."], 422);
    if ($v['phone'] !== '' && !preg_match('/^[0-9+\- ]{8,20}$/', $v['phone'])) out(["error" => "Nomor HP tidak valid (8-20 digit)."], 422);
    if (emailTaken($conn, $v['email'], $except)) out(["error" => "Email sudah dipakai akun lain."], 422);
}

// ---- Login & daftar (tanpa sesi) ----
if ($a == 'login') {
    $e = trim($d['email'] ?? '');
    $s = $conn->prepare("SELECT id,name,email,password,role FROM users WHERE email=?");
    $s->bind_param("s", $e); $s->execute();
    $u = $s->get_result()->fetch_assoc();
    if (!$u) {
        $s = $conn->prepare("SELECT id FROM notifications WHERE user_id=-1 AND ref=?");
        $s->bind_param("s", $e); $s->execute();
        out(["error" => $s->get_result()->num_rows ? "Akun ini telah dihapus oleh admin." : "Email belum terdaftar."], 401);
    }
    if (!password_verify($d['password'] ?? '', $u['password'] ?? '')) out(["error" => "Password salah."], 401);
    session_regenerate_id(true);
    $_SESSION['uid'] = $u['id'];
    // Notifikasi WA ke admin: siapa yang login (admin maupun siswa)
    $peran = $u['role'] == 'admin' ? 'Admin' : 'Siswa';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '-';
    $pesan = "🔐 Login $peran\n• Nama: {$u['name']}\n• Email: {$u['email']}\n• Waktu: " . date('d/m/Y H:i:s') . " WIB\n• IP: $ip";
    out_then(["ok" => 1, "role" => $u['role']], function () use ($pesan) { wa(ADMIN_WA, $pesan); });
}
if ($a == 'register') {
    $v = val($d, $F); $p = $d['password'] ?? '';
    check($conn, $v);
    if (strlen($p) < 6) out(["error" => "Password minimal 6 karakter."], 422);
    $vals = array_merge(array_values($v), [password_hash($p, PASSWORD_DEFAULT)]);
    $s = $conn->prepare("INSERT INTO users(name,nisn,ttl,gender,email,phone,address,password,role) VALUES(?,?,?,?,?,?,?,?,'user')");
    $s->bind_param(str_repeat("s", 8), ...$vals); $s->execute();
    $nid = $conn->insert_id;   // ambil id SEGERA, sebelum query lain
    $conn->query("DELETE FROM notifications WHERE user_id=-1 AND ref='" . $conn->real_escape_string($v['email']) . "'");
    session_regenerate_id(true);
    $_SESSION['uid'] = $nid;
    $info = akunInfo($v);
    notif($conn, $nid, "Pendaftaran akunmu berhasil. Datamu:\n$info");
    notif($conn, 0, "Pengguna baru mendaftar:\n$info");
    out(["ok" => 1, "role" => "user"]);
}
if ($a == 'logout') { session_destroy(); out(["ok" => 1]); }

// ---- Wajib login ----
$uid = $_SESSION['uid'] ?? 0;
$s = $conn->prepare("SELECT id,name,nisn,ttl,gender,email,phone,address,role FROM users WHERE id=?");
$s->bind_param("i", $uid); $s->execute();
$me = $s->get_result()->fetch_assoc();
if (!$me) { session_destroy(); out(["error" => $uid ? "Akun kamu telah dihapus oleh admin." : "Silakan login.", "logout" => true], 401); }
$admin = $me['role'] == 'admin';
function adminOnly($ok) { if (!$ok) out(["error" => "Akses ditolak."], 403); }

if ($a == 'me') out($me);

if ($a == 'list') {
    adminOnly($admin);
    $pg = max(1, (int)($_GET['page'] ?? 1)); $lm = 10; $off = ($pg - 1) * $lm;
    $q = '%' . ($_GET['q'] ?? '') . '%';
    $w = "role='user' AND (name LIKE ? OR email LIKE ? OR nisn LIKE ?)";
    $s = $conn->prepare("SELECT COUNT(*) c FROM users WHERE $w");
    $s->bind_param("sss", $q, $q, $q); $s->execute();
    $tot = (int)$s->get_result()->fetch_assoc()['c'];
    $st = $conn->query("SELECT COUNT(*) total, COALESCE(SUM(UPPER(gender)='MALE'),0) m, COALESCE(SUM(UPPER(gender)='FEMALE'),0) f FROM users WHERE role='user'")->fetch_assoc();
    $s = $conn->prepare("SELECT id,name,nisn,ttl,gender,email,phone,address FROM users WHERE $w ORDER BY id LIMIT $lm OFFSET $off");
    $s->bind_param("sss", $q, $q, $q); $s->execute();
    $data = $s->get_result()->fetch_all(MYSQLI_ASSOC);
    out(["data" => $data, "total" => $tot, "page" => $pg, "pages" => max(1, ceil($tot / $lm)), "stats" => $st]);
}

if ($a == 'add') {
    adminOnly($admin);
    $v = val($d, $F); $p = $d['password'] ?? '';
    check($conn, $v);
    if (strlen($p) < 6) out(["error" => "Password minimal 6 karakter."], 422);
    $vals = array_merge(array_values($v), [password_hash($p, PASSWORD_DEFAULT)]);
    $s = $conn->prepare("INSERT INTO users(name,nisn,ttl,gender,email,phone,address,password,role) VALUES(?,?,?,?,?,?,?,?,'user')");
    $s->bind_param(str_repeat("s", 8), ...$vals); $s->execute();
    $nid = $conn->insert_id;
    $conn->query("DELETE FROM notifications WHERE user_id=-1 AND ref='" . $conn->real_escape_string($v['email']) . "'");
    // detail akun: lonceng tanpa password (tidak disimpan di database), WA dengan password
    $info = "Akunmu telah dibuat oleh admin:\n" . akunInfo($v);
    $short = mb_strimwidth($info, 0, 250, "…");
    $s = $conn->prepare("INSERT INTO notifications(user_id,message) VALUES(?,?)");
    $s->bind_param("is", $nid, $short); $s->execute();
    wa($v['phone'], $info . "\n• Password: $p\n\nSilakan login, lalu segera ganti password lewat menu Edit profil.");
    notif($conn, 0, "Akun baru telah dibuat oleh admin:\n" . akunInfo($v));
    out(["ok" => 1]);
}

if ($a == 'update') {
    $id = $admin ? (int)($d['id'] ?? 0) : $uid;
    $v = val($d, $F); $p = $d['password'] ?? '';
    check($conn, $v, $id);
    if ($p !== '' && strlen($p) < 6) out(["error" => "Password baru minimal 6 karakter."], 422);
    // bandingkan data lama dengan data baru
    $s = $conn->prepare("SELECT name,nisn,ttl,gender,email,phone,address FROM users WHERE id=?");
    $s->bind_param("i", $id); $s->execute();
    $old = $s->get_result()->fetch_assoc();
    if (!$old) out(["error" => "Akun tidak ditemukan."], 404);
    $lb = ['name' => 'Nama', 'nisn' => 'NISN', 'ttl' => 'TTL', 'gender' => 'Gender', 'email' => 'Email', 'phone' => 'No. HP', 'address' => 'Alamat'];
    $ch = [];
    foreach ($lb as $k => $l) {
        $o = trim((string)$old[$k]); $n = $v[$k];
        if ($k == 'gender') $o = strtoupper($o);
        if ($o !== $n) $ch[] = "• $l: " . ($o === '' ? '(kosong)' : $o) . " → " . ($n === '' ? '(kosong)' : $n);
    }
    if ($p !== '') $ch[] = "• Password diubah";
    if (!$ch) out(["ok" => 1]);   // tidak ada yang berubah
    $det = implode("\n", $ch);
    $sql = "UPDATE users SET name=?,nisn=?,ttl=?,gender=?,email=?,phone=?,address=?" . ($p !== '' ? ",password=?" : "") . " WHERE id=?";
    $vals = array_values($v);
    if ($p !== '') $vals[] = password_hash($p, PASSWORD_DEFAULT);
    $vals[] = $id;
    $s = $conn->prepare($sql);
    $s->bind_param(str_repeat("s", count($vals) - 1) . "i", ...$vals); $s->execute();
    if ($admin) notif($conn, $id, "Admin mengubah datamu:\n$det");
    else notif($conn, $id, "Profilmu berhasil diperbarui:\n$det");
    notif($conn, 0, "Data {$v['name']} diubah " . ($admin ? "oleh admin" : "oleh pengguna sendiri") . ":\n$det");
    out(["ok" => 1]);
}

if ($a == 'delete_self') {
    adminOnly(!$admin);   // admin tidak boleh menghapus akunnya sendiri
    $s = $conn->prepare("SELECT password FROM users WHERE id=?");
    $s->bind_param("i", $uid); $s->execute();
    $row = $s->get_result()->fetch_assoc();
    if (!password_verify($d['password'] ?? '', $row['password'] ?? '')) out(["error" => "Password salah."], 422);
    $nama = $me['name']; $email = $me['email']; $hp = $me['phone'] ?? '';
    $conn->query("DELETE FROM notifications WHERE user_id=" . (int)$uid);
    $s = $conn->prepare("DELETE FROM users WHERE id=?");
    $s->bind_param("i", $uid); $s->execute();
    $_SESSION = []; session_destroy();
    out_then(["ok" => 1], function () use ($conn, $nama, $email, $hp) {
        wa($hp, "Akunmu ($email) telah berhasil kamu hapus. Terima kasih sudah menggunakan aplikasi ini.");
        notif($conn, 0, "Akun $nama ($email) dihapus oleh pemilik akun sendiri.");
    });
}

if ($a == 'delete') {
    adminOnly($admin);
    $id = (int)($d['id'] ?? 0);
    $s = $conn->prepare("SELECT name,email,phone FROM users WHERE id=? AND role='user'");
    $s->bind_param("i", $id); $s->execute();
    $u = $s->get_result()->fetch_assoc();
    if (!$u) out(["error" => "Akun tidak ditemukan."], 404);
    wa($u['phone'], "Akunmu ({$u['email']}) telah dihapus oleh admin.");
    $s = $conn->prepare("DELETE FROM users WHERE id=?");
    $s->bind_param("i", $id); $s->execute();
    $conn->query("DELETE FROM notifications WHERE user_id=$id");
    notif($conn, 0, "Akun {$u['name']} telah dihapus.");
    notif($conn, -1, "deleted", $u['email']);
    out(["ok" => 1]);
}

if ($a == 'notifs' || $a == 'read') {
    $t = $admin ? 0 : $uid;
    if ($a == 'read') { $conn->query("UPDATE notifications SET is_read=1 WHERE user_id=$t"); out(["ok" => 1]); }
    $list = $conn->query("SELECT id,message,is_read,created_at FROM notifications WHERE user_id=$t ORDER BY id DESC LIMIT 20")->fetch_all(MYSQLI_ASSOC);
    out(["list" => $list]);
}
out(["error" => "Aksi tidak dikenal."], 400);