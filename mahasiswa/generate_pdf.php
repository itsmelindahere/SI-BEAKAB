<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['email'])) {
    die("Anda harus login terlebih dahulu.");
}

require_once __DIR__ . '/../vendor/tcpdf/tcpdf.php';
require_once __DIR__ . '/koneksi.php';

$email = $_SESSION['email'];

// Query berdasarkan email
$query = "SELECT p.nama, p.tempat_lahir, p.tanggal_lahir, p.nim, p.perguruan_tinggi, 
                 p.alamat, p.nomor_wa, m.ipk 
          FROM profil_mhs p 
          JOIN mahasiswa m ON p.email = m.email 
          WHERE p.email = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan untuk email $email!");
}

$nama = $data['nama'];
$tempat_lahir = $data['tempat_lahir'];
$tanggal_lahir = date("d-m-Y", strtotime($data['tanggal_lahir']));
$nim = $data['nim'];
$perguruan_tinggi = $data['perguruan_tinggi'];
$alamat = $data['alamat'];
$nomor_wa = $data['nomor_wa'];
$ipk = $data['ipk'];
$tahun = date("Y");

ob_clean();

$pdf = new TCPDF();
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage();

// Judul
$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Cell(0, 10, 'FAKTA INTEGRITAS', 0, 1, 'C');

// Data Mahasiswa
$pdf->SetFont('Helvetica', '', 11);
$pdf->Ln(5);
$pdf->Cell(50, 7, "Nama", 0, 0);
$pdf->Cell(50, 7, ": $nama", 0, 1);
$pdf->Cell(50, 7, "Tempat/Tanggal Lahir", 0, 0);
$pdf->Cell(50, 7, ": $tempat_lahir, $tanggal_lahir", 0, 1);
$pdf->Cell(50, 7, "NIM", 0, 0);
$pdf->Cell(50, 7, ": $nim", 0, 1);
$pdf->Cell(50, 7, "Perguruan Tinggi", 0, 0);
$pdf->Cell(50, 7, ": $perguruan_tinggi", 0, 1);
$pdf->Cell(50, 7, "Alamat", 0, 0);
$pdf->MultiCell(0, 7, ": $alamat", 0, 1);
$pdf->Cell(50, 7, "Nomor HP/WA", 0, 0);
$pdf->Cell(50, 7, ": $nomor_wa", 0, 1);
$pdf->Cell(50, 7, "IPK", 0, 0);
$pdf->Cell(50, 7, ": $ipk", 0, 1);

// Pernyataan Fakta Integritas
$pdf->Ln(5);
$teks_integritas = "Dengan ini menyatakan janji dan komitmen akan mengikuti dan menaati aturan apabila ditetapkan sebagai penerima Bantuan Beasiswa dari Keluarga Tidak Mampu dari Pemerintah Kabupaten Asahan Tahun $tahun.\n\nSebagai penerima saya menyatakan bahwa:\n
1. Dokumen / berkas yang disampaikan / upload adalah benar dan asli.\n
2. Bersedia menerima bantuan dan bertanggung jawab penuh dalam penggunaan bantuan sesuai ketentuan.\n
3. Tidak menyalahgunakan bantuan untuk kepentingan lain di luar fungsi yang sudah ditentukan.\n
4. Bersedia mengembalikan dana bantuan apabila melanggar dari fungsi yang sudah ditentukan.\n
5. Bersedia menyampaikan laporan penggunaan dana bantuan yang saya terima.\n\n
Demikian Fakta Integritas ini dibuat, apabila saya melanggar hal-hal yang telah saya nyatakan, saya bersedia dikenakan sanksi sesuai dengan ketentuan peraturan perundang-undangan yang berlaku.";

$pdf->MultiCell(0, 7, $teks_integritas, 0, 1);

// Tambahkan spasi sebelum bagian tanda tangan
$pdf->Ln(10);

// Format tanda tangan dan bingkai foto
$pdf->SetFont('Helvetica', '', 11);
$pdf->Cell(90, 40, '', 0, 0); // Ruang kosong kiri
$pdf->Cell(90, 40, "Kisaran, " . date("d-m-Y"), 0, 1, 'R');

// Buat bingkai kosong untuk foto ukuran **38,1 mm × 55,9 mm**
$foto_x = 110; // Posisi X lebih ke kanan
$foto_y = $pdf->GetY() - 15; // Posisi Y
$foto_width = 38.1; // Lebar dalam mm
$foto_height = 55.9; // Tinggi dalam mm

$pdf->Rect($foto_x, $foto_y, $foto_width, $foto_height); // Kotak kosong untuk foto
$pdf->SetXY($foto_x, $foto_y + 25);
$pdf->Cell($foto_width, 7, "Pas Foto 4 x 6", 0, 1, 'C'); // Teks dalam bingkai

// Tanda tangan lebih rapi
$pdf->Ln(5);
$pdf->SetX(145);
$pdf->Cell(50, 7, "Hormat saya,", 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetX(155);
$pdf->Cell(50, 7, "$nama", 0, 1, 'C');

// Set header sebelum output PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="Fakta_Integritas_' . $nama . '.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

$pdf->Output("Fakta_Integritas_$nama.pdf", 'I');
exit;
?>
