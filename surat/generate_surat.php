<?php
/**
 * surat/generate_surat.php
 *
 * File ini disediakan sesuai struktur folder yang diminta.
 * Pada implementasi saat ini, Surat Kelulusan (Tahap 3) dirender
 * sepenuhnya di sisi client (index.php + JavaScript) dengan menumpuk
 * teks nama & alamat di atas gambar surat menggunakan CSS position:absolute,
 * karena data (nama, alamat) sudah tersedia langsung dari response AJAX
 * proses_daftar.php tanpa perlu reload / request tambahan ke server.
 *
 * File ini disiapkan sebagai endpoint cadangan jika suatu saat Anda ingin
 * men-generate surat sebagai gambar/PDF di sisi server (misalnya memakai
 * GD Library atau library PDF). Contoh kerangka dasarnya:
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../koneksi.php';

$no = isset($_GET['no']) ? (int)$_GET['no'] : 0;

if ($no <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Parameter "no" tidak valid.']);
    exit;
}

try {
    $stmt = $koneksi->prepare("SELECT * FROM pendaftaran_gen_1 WHERE no = ?");
    $stmt->bind_param('i', $no);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Data pendaftar tidak ditemukan.']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'nama_lengkap' => $row['nama_depan'] . ' ' . $row['nama_belakang'],
            'alamat'       => $row['alamat'],
            'idha'         => $row['idha'],
            'generasi'     => $row['generasi'],
        ]
    ]);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil data: ' . $e->getMessage()]);
}