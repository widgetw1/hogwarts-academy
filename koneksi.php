<?php
// Ambil variabel dari sistem Clever Cloud
$host = getenv('MYSQL_ADDON_HOST');
$user = getenv('MYSQL_ADDON_USER');
$pass = getenv('MYSQL_ADDON_PASSWORD');
$db   = getenv('MYSQL_ADDON_DB');
$port = getenv('MYSQL_ADDON_PORT') ?;// Jika kosong, gunakan port default 3306

// Menggunakan socket jika host tidak terdeteksi, tapi ini jarang terjadi di cloud
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    // Eror ini yang muncul di websitemu, kita ubah jadi lebih spesifik
    die("Koneksi gagal: " . $conn->connect_error);
}
?>