<?php
include 'koneksi.php';
$c = curl_init('https://api.fonnte.com/send');
curl_setopt_array($c, [
    CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => ['target' => '088976546026', 'message' => 'Tes dari aplikasi', 'countryCode' => '62'],
    CURLOPT_HTTPHEADER => ['Authorization: ' . trim(FONNTE_TOKEN)],
]);
echo curl_exec($c) ?: curl_error($c);