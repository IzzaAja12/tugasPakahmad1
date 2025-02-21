<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Data_Siswa.xls");
header("Pragma: no-cache");
header("Expires: 0");

require_once 'config.php';

echo "<table border='1'>
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
        </tr>";

$sqlSiswa = "SELECT s.nis, s.nama, k.nama_kelas FROM tb_siswa s LEFT JOIN tb_kelas k ON s.id_kelas = k.id_kelas";
$stmt = $pdo->query($sqlSiswa);
$no = 1;

foreach ($stmt as $row) {
    echo "<tr>
            <td>$no</td>
            <td>{$row['nis']}</td>
            <td>{$row['nama']}</td>
            <td>{$row['nama_kelas']}</td>
          </tr>";
    $no++;
}

echo "</table>";
?>
