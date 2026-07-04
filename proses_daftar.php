<?php
/**
 * proses_daftar.php
 * Menerima data pendaftaran via AJAX (fetch API), menyimpan ke MySQL
 * dengan Prepared Statements, menghitung IDHA otomatis, lalu
 * mengembalikan response JSON berisi data lengkap termasuk IDHA.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/koneksi.php';

// Hanya izinkan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan.']);
    exit;
}

// Ambil raw JSON body (karena dikirim via fetch dengan Content-Type application/json)
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

// Jika bukan JSON, coba ambil dari $_POST (fallback form-data)
if (!is_array($data)) {
    $data = $_POST;
}

// Validasi & sanitasi input
$nama_depan     = trim($data['nama_depan'] ?? '');
$nama_belakang  = trim($data['nama_belakang'] ?? '');
$alamat         = trim($data['alamat'] ?? '');
$jenis_kelamin  = trim($data['jenis_kelamin'] ?? '');
$tanggal_lahir  = trim($data['tanggal_lahir'] ?? '');
$status_darah   = trim($data['status_darah'] ?? '');
$generasi       = trim($data['generasi'] ?? '1');

$errors = [];

if ($nama_depan === '') $errors[] = 'Nama depan wajib diisi.';
if ($nama_belakang === '') $errors[] = 'Nama belakang wajib diisi.';
if ($alamat === '') $errors[] = 'Alamat wajib diisi.';
if (!in_array($jenis_kelamin, ['Laki-laki', 'Perempuan', 'Male', 'Female'], true)) {
    $errors[] = 'Jenis kelamin tidak valid.';
}
if ($tanggal_lahir === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_lahir)) {
    $errors[] = 'Tanggal lahir tidak valid.';
}
if (!in_array($status_darah, ['Pure-blood', 'Half-blood', 'Muggle-born'], true)) {
    $errors[] = 'Status darah tidak valid.';
}
if (!is_numeric($generasi)) {
    $errors[] = 'Generasi tidak valid.';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

$generasi = (int)$generasi;

// Normalisasi jenis kelamin ke format konsisten untuk disimpan
$genderMap = [
    'Laki-laki' => 'Laki-laki',
    'Male'      => 'Laki-laki',
    'Perempuan' => 'Perempuan',
    'Female'    => 'Perempuan',
];
$jenis_kelamin_simpan = $genderMap[$jenis_kelamin] ?? $jenis_kelamin;

try {
    // Hitung nomor registrasi berikutnya untuk generasi ini
    // (berdasarkan jumlah pendaftar yang sudah ada di generasi yang sama)
    $stmtCount = $koneksi->prepare("SELECT COUNT(*) AS total FROM pendaftaran_gen_1 WHERE generasi = ?");
    $stmtCount->bind_param('i', $generasi);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    $rowCount = $resultCount->fetch_assoc();
    $noRegistrasi = (int)$rowCount['total'] + 1;
    $stmtCount->close();

    // Format nomor registrasi: 01-09 pakai leading zero, 10+ tanpa leading zero
    $noRegistrasiFormatted = $noRegistrasi < 10 ? '0' . $noRegistrasi : (string)$noRegistrasi;

    // Tahun sekarang 2 digit
    $tahun = date('y'); // contoh: '26' untuk 2026

    // ID Asrama default sementara '00' (sebelum sorting)
    $idAsrama = '00';

    // Susun IDHA: HW + tahun(2) + generasi(1) + idAsrama(2) + noRegistrasi
    $idha = 'HW' . $tahun . $generasi . $idAsrama . $noRegistrasiFormatted;

    // Insert ke database menggunakan Prepared Statement
    $stmt = $koneksi->prepare(
        "INSERT INTO pendaftaran_gen_1
        (nama_depan, nama_belakang, idha, tanggal_lahir, alamat, jenis_kelamin, status_darah, generasi)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        'sssssssi',
        $nama_depan,
        $nama_belakang,
        $idha,
        $tanggal_lahir,
        $alamat,
        $jenis_kelamin_simpan,
        $status_darah,
        $generasi
    );
    $stmt->execute();
    $insertedId = $stmt->insert_id;
    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => 'Pendaftaran berhasil!',
        'data' => [
            'no'             => $insertedId,
            'nama_depan'     => $nama_depan,
            'nama_belakang'  => $nama_belakang,
            'nama_lengkap'   => $nama_depan . ' ' . $nama_belakang,
            'alamat'         => $alamat,
            'jenis_kelamin'  => $jenis_kelamin_simpan,
            'tanggal_lahir'  => $tanggal_lahir,
            'status_darah'   => $status_darah,
            'generasi'       => $generasi,
            'idha'           => $idha,
        ]
    ]);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menyimpan data: ' . $e->getMessage()
    ]);
}