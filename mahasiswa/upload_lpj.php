<?php
session_start();
include '../koneksi.php'; // Pastikan koneksi berada di luar folder mahasiswa

if (!isset($_SESSION['email'])) {
    die("Anda harus login terlebih dahulu.");
}

$email = $_SESSION['email'];
$target_dir = '../uploads/lpj/'; // Folder uploads di luar folder mahasiswa

// Pastikan folder tujuan ada & bisa ditulis
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}
if (!is_writable($target_dir)) {
    die("<script>alert('Error: Folder uploads/lpj/ tidak memiliki izin write!'); window.location.href='home_mahasiswa.php';</script>");
}

// Fungsi untuk membersihkan nama file
function sanitizeFileName($filename) {
    $filename = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $filename); // Hanya huruf, angka, -, _, .
    return $filename;
}

// Fungsi untuk upload file dengan validasi tambahan
function uploadFile($file, $dir, $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'], $max_size = 10 * 1024 * 1024) { // Maks 10MB
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ["error" => "File tidak diupload!"];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ["error" => "Error saat mengupload file. Kode error: " . $file['error']];
    }

    $file_ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        return ["error" => "Format file tidak didukung! Hanya PDF, JPG, JPEG, PNG."];
    }
    if ($file["size"] > $max_size) {
        return ["error" => "File terlalu besar! Maksimal 10MB."];
    }

    // Bersihkan nama file dan tambahkan uniqid agar tidak bentrok
    $clean_name = sanitizeFileName(pathinfo($file["name"], PATHINFO_FILENAME));
    $new_file_name = uniqid() . "_" . $clean_name . "." . $file_ext;
    $file_path = $dir . $new_file_name; // Gunakan path relatif

    if (move_uploaded_file($file["tmp_name"], $file_path)) {
        return ["success" => $new_file_name]; // Simpan nama file saja
    } else {
        return ["error" => "Gagal memindahkan file ke $file_path. Cek apakah folder bisa diakses!"];
    }
}

// Upload masing-masing file
$surat_pernyataan = uploadFile($_FILES["surat_pernyataan"], $target_dir);
$laporan_dana = uploadFile($_FILES["laporan_dana"], $target_dir);
$bukti_ukt = uploadFile($_FILES["bukti_ukt"], $target_dir);
$fakta_integritas = uploadFile($_FILES["fakta_integritas"], $target_dir);

// Cek apakah semua berhasil diupload
$errors = [];
if (isset($surat_pernyataan["error"])) $errors[] = "Surat Pernyataan: " . $surat_pernyataan["error"];
if (isset($laporan_dana["error"])) $errors[] = "Laporan Dana: " . $laporan_dana["error"];
if (isset($bukti_ukt["error"])) $errors[] = "Bukti UKT: " . $bukti_ukt["error"];
if (isset($fakta_integritas["error"])) $errors[] = "Fakta Integritas: " . $fakta_integritas["error"];

if (!empty($errors)) {
    $error_message = implode("\\n", $errors);
    echo "<script>alert('Gagal mengupload file:\n$error_message'); window.location.href='home_mahasiswa.php';</script>";
    exit;
}

// Simpan ke database menggunakan prepared statement
$query = "INSERT INTO laporan_lpj (email, surat_pernyataan, laporan_dana, bukti_ukt, fakta_integritas) 
          VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($koneksi, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "sssss", $email, 
        $surat_pernyataan["success"], 
        $laporan_dana["success"], 
        $bukti_ukt["success"], 
        $fakta_integritas["success"]
    );

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('LPJ berhasil dikirim!'); window.location.href='home_mahasiswa.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menyimpan ke database: " . mysqli_error($koneksi) . "'); window.location.href='laporan_pertanggungjawaban.php';</script>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Terjadi kesalahan pada query database: " . mysqli_error($koneksi) . "'); window.location.href='laporan_pertanggungjawaban.php';</script>";
}

mysqli_close($koneksi);
?>
