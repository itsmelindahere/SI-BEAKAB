<?php
require_once '../vendor/tcpdf/tcpdf.php';

// Buat objek PDF baru
$pdf = new TCPDF();

// Atur informasi dokumen
$pdf->SetCreator('Test');
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Test TCPDF');

// Tambahkan halaman
$pdf->AddPage();

// Tambahkan teks ke halaman
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Hello, TCPDF bekerja dengan baik!', 0, 1, 'C');

// Output PDF
$pdf->Output('test_tcpdf.pdf', 'I');
?>
