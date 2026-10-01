<?php
// Jalankan SEKALI: http://localhost/ujian_asts/setup.php
include 'koneksi.php';
header("Content-Type: text/plain; charset=utf-8");

foreach (["phone VARCHAR(20) DEFAULT NULL",
          "password VARCHAR(255) DEFAULT NULL",
          "role VARCHAR(10) NOT NULL DEFAULT 'user'",
          "kelas VARCHAR(30) DEFAULT NULL"] as $c)
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS $c");

$conn->query("CREATE TABLE IF NOT EXISTS notifications(
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, message VARCHAR(255) NOT NULL,
  ref VARCHAR(100) DEFAULT NULL, is_read TINYINT DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

$h = password_hash("user123", PASSWORD_DEFAULT);
$s = $conn->prepare("UPDATE users SET password=? WHERE role='user' AND (password IS NULL OR password='')");
$s->bind_param("s", $h); $s->execute();
echo "Password semua user lama diset: user123\n";

if (!$conn->query("SELECT id FROM users WHERE role='admin'")->num_rows) {
    $h = password_hash("admin123", PASSWORD_DEFAULT);
    $s = $conn->prepare("INSERT INTO users(name,email,password,role) VALUES('Administrator','admin@mail.com',?,'admin')");
    $s->bind_param("s", $h); $s->execute();
    echo "Admin dibuat: admin@mail.com / admin123\n";
}
echo "Setup selesai. Buka login.html";
