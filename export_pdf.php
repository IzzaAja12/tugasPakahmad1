<?php
// Nonaktifkan error sementara untuk debugging
error_reporting(0);

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=Data_Siswa.pdf");
header("Pragma: no-cache");
header("Expires: 0");

// Pastikan file FPDF ada
require_once 'fpdf/fpdf.php';

// Pastikan file config ada
require_once 'config.php';

// Buat objek PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Judul
$pdf->Cell(190, 10, 'Data Siswa', 0, 1, 'C');
$pdf->Ln(5); // Spasi

// Header tabel
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(10, 10, 'No', 1);
$pdf->Cell(40, 10, 'NIS', 1);
$pdf->Cell(80, 10, 'Nama Siswa', 1);
$pdf->Cell(60, 10, 'Kelas', 1);
$pdf->Ln();

// Ambil data dari database
$sqlSiswa = "SELECT s.nis, s.nama, k.nama_kelas FROM tb_siswa s 
             LEFT JOIN tb_kelas k ON s.id_kelas = k.id_kelas";
$stmt = $pdo->query($sqlSiswa);
$no = 1;

$pdf->SetFont('Arial', '', 12);
foreach ($stmt as $row) {
    $pdf->Cell(10, 10, $no, 1);
    $pdf->Cell(40, 10, $row['nis'], 1);
    $pdf->Cell(80, 10, $row['nama'], 1);
    $pdf->Cell(60, 10, $row['nama_kelas'], 1);
    $pdf->Ln();
    $no++;
}

// Output PDF ke browser
$pdf->Output('D', 'Data_Siswa.pdf');
exit;
?>
