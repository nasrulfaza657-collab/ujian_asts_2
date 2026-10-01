<?php
include 'koneksi.php';
$id = intval($_GET['id']);
$conn->query("DELETE FROM users WHERE id=$id");
echo json_encode(["status" => "ok"]);
?>