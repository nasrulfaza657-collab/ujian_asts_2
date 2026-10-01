<?php
include 'koneksi.php';
$d = json_decode(file_get_contents("php://input"), true);
$stmt = $conn->prepare("UPDATE users SET name=?,nisn=?,ttl=?,gender=?,email=?,address=? WHERE id=?");
$stmt->bind_param("ssssssi", $d['name'],$d['nisn'],$d['ttl'],$d['gender'],$d['email'],$d['address'],$d['id']);
echo json_encode(["status" => $stmt->execute() ? "ok" : "gagal"]);
?>