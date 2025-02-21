<?php
// Memulai session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Cek apakah session 'nama' ada
if (!isset($_SESSION['nama'])) {
    // Jika tidak ada session nama, redirect ke halaman login
    header('Location: login.php');
    exit;
}

// Koneksi ke database
require_once 'config.php';

// Query untuk menampilkan data siswa yang sesuai dengan NIS siswa yang login
$sqlSiswa = "
    SELECT s.nis, s.nama, k.nama_kelas
    FROM tb_siswa s
    LEFT JOIN tb_kelas k ON s.id_kelas = k.id_kelas
    WHERE s.nis = :nis
";
$stmt = $pdo->prepare($sqlSiswa);
$stmt->execute(['nis' => $_SESSION['nis']]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
</head>
<body>
    <br>
    <div class="h3" style="color:green">Halo <?= htmlspecialchars($_SESSION['nama']); ?>!</div>

    <!-- Tabel data siswa -->
    <div class="container mt-5">
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if ($siswa): ?>
                            <tr>
                                <td>1</td>
                                <td><?= htmlspecialchars($siswa['nis']); ?></td>
                                <td><?= htmlspecialchars($siswa['nama']); ?></td>
                                <td><?= htmlspecialchars($siswa['nama_kelas']); ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Data siswa tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
