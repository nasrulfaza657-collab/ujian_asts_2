<?php
include 'koneksi.php';

// Ambil parameter halaman & limit dari URL
$page  = isset($_GET['page'])  ? intval($_GET['page'])  : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

if ($page < 1) $page = 1;
if ($limit < 1) $limit = 10;

$offset = ($page - 1) * $limit;

// Hitung total data
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM users");
$total = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

// Ambil data sesuai halaman
$result = $conn->query("SELECT * FROM users ORDER BY id ASC LIMIT $limit OFFSET $offset");
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Response berisi data + info pagination
$response = [
    "page"       => $page,
    "limit"      => $limit,
    "total"      => (int)$total,
    "totalPages" => $totalPages,
    "data"       => $data
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>