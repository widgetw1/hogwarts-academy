<?php
/**
 * koneksi.php
 * Koneksi database — LOCALHOST ONLY (XAMPP/Laragon/dsb).
 * Sesuaikan nilai di bawah jika konfigurasi MySQL localhost Anda berbeda.
 */

$host = 'localhost';
$db   = 'hogwarts';
$user = 'root';
$pass = '';
$port = '3306';

// Aktifkan error reporting mysqli sebagai exception agar mudah di-debug
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $koneksi = new mysqli($host, $user, $pass, $db, (int)$port);
    $koneksi->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Koneksi database gagal: ' . $e->getMessage()
    ]));
}

/**
 * Struktur tabel yang dibutuhkan (jalankan sekali di phpMyAdmin / MySQL client):
 *
 * CREATE TABLE IF NOT EXISTS pendaftaran_gen_1 (
 *   no INT AUTO_INCREMENT PRIMARY KEY,
 *   nama_depan VARCHAR(100) NOT NULL,
 *   nama_belakang VARCHAR(100) NOT NULL,
 *   idha VARCHAR(20) NOT NULL,
 *   tanggal_lahir DATE NOT NULL,
 *   alamat VARCHAR(255) NOT NULL,
 *   jenis_kelamin VARCHAR(20) NOT NULL,
 *   status_darah VARCHAR(20) NOT NULL,
 *   generasi INT NOT NULL,
 *   tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 */