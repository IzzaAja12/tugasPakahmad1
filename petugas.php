<!-- HALAMAN LOGIN PETUGAS -->

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// Periksa apakah user memiliki akses level petugas
if (isset($_SESSION['login']) && $_SESSION['login'] == "petugas") {  
    $id = $_SESSION['user_id'];
    $nama = $_SESSION['user']; 
    $level = $_SESSION['login'];
?>
    <br>
    <h3 style="color: green;">Halo Petugas</h3> <br>
    <p>Level : <?=$level?></p>
    <p>ID Petugas : <?=$id?></p>
    <p>Nama Petugas : <?=$nama?></p>

<?php

// Ambil data kelas
$query_kelas = "SELECT id_kelas, nama_kelas FROM tb_kelas";
$stmt_kelas = $pdo->prepare($query_kelas);
$stmt_kelas->execute();
$kelas_list = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);

// Ambil data siswa
$query_siswa = "
    SELECT s.nis, s.nama, k.nama_kelas 
    FROM tb_siswa s
    LEFT JOIN tb_kelas k ON s.id_kelas = k.id_kelas
";
$stmt_siswa = $pdo->query($query_siswa);
$siswa_list = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);

// Ambil data mata pelajaran
$query_mapel = "SELECT id_mapel, nama_mapel FROM tb_mapel";
$stmt_mapel = $pdo->query($query_mapel);
$mapel_list = $stmt_mapel->fetchAll(PDO::FETCH_ASSOC);

} else {
    echo "<script> alert('HALAMAN INI HANYA UNTUK PETUGAS'); window.location.href='login.php'; </script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-3">
        </div>
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table id="datatable" class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php $no = 1; ?>
                        <?php foreach ($siswa_list as $siswa): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($siswa['nis']); ?></td>
                                <td><?= htmlspecialchars($siswa['nama']); ?></td>
                                <td><?= htmlspecialchars($siswa['nama_kelas']); ?></td>
                                <td>
                                    <!-- Button Nilai -->
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#nilaiModal<?= $siswa['nis']; ?>">
                                        Nilai
                                    </button>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#bayarModal<?= $siswa['nis']; ?>">
                                       Pembayaran
                                    </button>
                                    <!-- Modal Nilai -->
                                    <div class="modal fade" id="nilaiModal<?= $siswa['nis']; ?>" tabindex="-1" aria-labelledby="nilaiModalLabel<?= $siswa['nis']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="nilaiModalLabel<?= $siswa['nis']; ?>">Input Nilai - <?= htmlspecialchars($siswa['nama']); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Mata Pelajaran</th>
                                                                <th>Nilai</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                   

                                                        <?php foreach ($mapel_list as $mapel): ?>
                                                                <?php
                                                                // Query untuk mendapatkan nilai siswa
                                                                $query_nilai = "
                                                                    SELECT nilai 
                                                                    FROM tb_nilai 
                                                                    WHERE nis = :nis AND id_mapel = :id_mapel
                                                                ";
                                                                $stmt_nilai = $pdo->prepare($query_nilai);
                                                                $stmt_nilai->execute([
                                                                    ':nis' => $siswa['nis'],
                                                                    ':id_mapel' => $mapel['id_mapel']
                                                                ]);
                                                                $nilai = $stmt_nilai->fetchColumn() ?? "-";
                                                                ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($mapel['nama_mapel']); ?></td>
                                                                    <td><?= htmlspecialchars($nilai); ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <a href="nilai.php?nis=<?= $siswa['nis']; ?>" class="btn btn-primary btn-sm">Input Nilai</a>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="modal fade" id="bayarModal<?= $siswa['nis']; ?>" tabindex="-1" aria-labelledby="bayarModalLabel<?= $siswa['nis']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bayarModalLabel<?= $siswa['nis']; ?>">Detail Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                // SQL query untuk mengambil data pembayaran siswa berdasarkan NIS
                $sqlBayar = "
                SELECT
                b.bulan,
                SUM(CASE WHEN b.jenis = 'SPP' THEN b.jumlah ELSE 0 END) AS spp,
                SUM(CASE WHEN b.jenis = 'Tabungan' THEN b.jumlah ELSE 0 END) AS tabungan,
                SUM(CASE WHEN b.jenis = 'Extra' THEN b.jumlah ELSE 0 END) AS extra,
                SUM(b.jumlah) AS total
                FROM tb_bayar b
                WHERE b.nis = :nis AND b.bulan IS NOT NULL
                GROUP BY b.bulan
                ORDER BY b.bulan
                ";

                $stmtBayar = $pdo->prepare($sqlBayar);
                $stmtBayar->execute(['nis' => $siswa['nis']]);

                $pembayaran = [];
                while ($rowBayar = $stmtBayar->fetch(PDO::FETCH_ASSOC)) {
                    $pembayaran[$rowBayar['bulan']] = $rowBayar;
                }
                ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Bulan</th>
                                <th>SPP</th>
                                <th>Tabungan</th>
                                <th>Extra</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $bulanArray = [
                                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            ];

                            foreach ($bulanArray as $index => $bulan):
                                $data = isset($pembayaran[$index + 1]) ? $pembayaran[$index + 1] : null;
                            ?>
                            <tr>
                                <td><?= $bulan; ?></td>
                                <td><?= $data ? "Rp " . number_format($data['spp'], 0, ',', '.') : '-'; ?></td>
                                <td><?= $data ? "Rp " . number_format($data['tabungan'], 0, ',', '.') : '-'; ?></td>
                                <td><?= $data ? "Rp " . number_format($data['extra'], 0, ',', '.') : '-'; ?></td>
                                <td><?= $data ? "Rp " . number_format($data['total'], 0, ',', '.') : '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
    <a href="bayar.php?nis=<?= htmlspecialchars($siswa['nis']); ?>" class="btn btn-primary">Input Pembayaran</a>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

            

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#datatable').DataTable();
        });
    </script>
    <br>
    <br>
</body>
</html>



