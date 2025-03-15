<?php
require_once __DIR__ . '/../vendor/tcpdf/tcpdf.php';
include 'koneksi.php';

$query = "SELECT p.nama, p.nim FROM laporan_lpj l JOIN profil_mhs p ON l.email = p.email WHERE l.status='Diterima'";
$result = mysqli_query($koneksi, $query);

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Daftar Mahasiswa yang Mengirim LPJ', 0, 1, 'C');

$pdf->SetFont('Helvetica', '', 12);
$pdf->Ln(5);
$pdf->Cell(10, 7, "No", 1);
$pdf->Cell(70, 7, "Nama", 1);
$pdf->Cell(40, 7, "NIM", 1);
$pdf->Ln();

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(10, 7, $no++, 1);
    $pdf->Cell(70, 7, $row['nama'], 1);
    $pdf->Cell(40, 7, $row['nim'], 1);
    $pdf->Ln();
}

$pdf->Output("Daftar_Mahasiswa_LPJ.pdf", 'I');
?>
