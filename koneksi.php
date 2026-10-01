<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
$conn = new mysqli("localhost", "root", "", "ujian_asts");
if ($conn->connect_error) die("Koneksi gagal");
$conn->set_charset("utf8mb4");

set_exception_handler(function ($e) { out(["error" => "Error server: " . $e->getMessage()], 500); });
function out($a, $c = 200) {
    http_response_code($c);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($a, JSON_UNESCAPED_UNICODE);
    exit;
}
// Kirim respons ke browser LEBIH DULU, lalu jalankan $after (mis. kirim WA)
// supaya login tidak terasa lambat menunggu API Fonnte.
function out_then($a, $after, $c = 200) {
    ignore_user_abort(true);
    $body = json_encode($a, JSON_UNESCAPED_UNICODE);
    http_response_code($c);
    header("Content-Type: application/json; charset=UTF-8");
    header("Content-Length: " . strlen($body));
    header("Connection: close");
    echo $body;
    session_write_close();
    if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
    else { while (ob_get_level()) ob_end_flush(); flush(); }
    $after();
    exit;
}
// ===== Fonnte (WhatsApp) =====
define('FONNTE_TOKEN', 'pvrvU839HCM8Bsw3oGTr');   // tempel token device dari fonnte.com di sini
define('ADMIN_WA', '088976546026');       // nomor WA admin, contoh: 081234567890

function wa($target, $msg) {
    $target = preg_replace('/[^0-9]/', '', (string)$target);
    if (!FONNTE_TOKEN || strlen($target) < 8) return;
    $c = curl_init('https://api.fonnte.com/send');
    curl_setopt_array($c, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5, CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => ['target' => $target, 'message' => $msg, 'countryCode' => '62'],
        CURLOPT_HTTPHEADER => ['Authorization: ' . FONNTE_TOKEN],
    ]);
    curl_exec($c); curl_close($c);
}

// user_id: 0 = notifikasi admin, -1 = catatan akun terhapus (ref = email)
function notif($conn, $uid, $msg, $ref = null) {
    $s = $conn->prepare("INSERT INTO notifications(user_id,message,ref) VALUES(?,?,?)");
    $short = mb_strimwidth($msg, 0, 250, "…");
    $s->bind_param("iss", $uid, $short, $ref);
    $s->execute();
    if ($uid == 0) wa(ADMIN_WA, "[Admin] $msg");
    elseif ($uid > 0) {
        $r = $conn->query("SELECT phone FROM users WHERE id=" . (int)$uid)->fetch_assoc();
        wa($r['phone'] ?? '', $msg);
    }
}