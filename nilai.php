<?php
require_once 'config.php';
session_start();
$nis = htmlspecialchars($_GET['nis'] ?? null);
if (!$nis) {
    die("Parameter NIS tidak valid!");
}

$query_siswa = "SELECT nama FROM tb_siswa WHERE nis = :nis";
$stmt_siswa = $pdo->prepare($query_siswa);
$stmt_siswa->execute([':nis' => $nis]);
$siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    die("Siswa dengan NIS tersebut tidak ditemukan!");
}

$query_mapel = "SELECT id_mapel, nama_mapel FROM tb_mapel";
$stmt_mapel = $pdo->query($query_mapel);
$mapel_list = $stmt_mapel->fetchAll(PDO::FETCH_ASSOC);

$id_user = $_SESSION['id_user'] ?? 1; 
$query_user = "SELECT COUNT(*) FROM tb_user WHERE id_user = :id_user";
$stmt_user = $pdo->prepare($query_user);
$stmt_user->execute([':id_user' => $id_user]);
$user_exists = $stmt_user->fetchColumn();

if (!$user_exists) {
    die("ID User $id_user tidak ditemukan di tabel tb_user!");
}

if (isset($_POST['simpan_nilai'])) {
    foreach ($_POST['nilai'] as $id_mapel => $nilai) {
        if ($nilai !== "") {
          
            if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
                die("Nilai untuk mapel ID $id_mapel tidak valid!");
            }

           
            $query = "
                INSERT INTO tb_nilai (nis, id_mapel, nilai, id_user)
                VALUES (:nis, :id_mapel, :nilai, :id_user)
                ON DUPLICATE KEY UPDATE nilai = :nilai
            ";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':nis' => $nis,
                ':id_mapel' => $id_mapel,
                ':nilai' => $nilai,
                ':id_user' => $id_user
            ]);
        }
    }

    header('Location: index.php?page=transaksi');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Nilai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Input Nilai</h3>
        <p><strong>NIS:</strong> <?= htmlspecialchars($nis); ?></p>
        <p><strong>Nama Siswa:</strong> <?= htmlspecialchars($siswa['nama']); ?></p>
        <form method="POST">
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
                        $query_nilai = "
                            SELECT nilai 
                            FROM tb_nilai 
                            WHERE nis = :nis AND id_mapel = :id_mapel
                        ";
                        $stmt_nilai = $pdo->prepare($query_nilai);
                        $stmt_nilai->execute([
                            ':nis' => $nis,
                            ':id_mapel' => $mapel['id_mapel']
                        ]);
                        $nilai = $stmt_nilai->fetchColumn() ?? null;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($mapel['nama_mapel']); ?></td>
                            <td>
                                <input type="number" name="nilai[<?= $mapel['id_mapel']; ?>]" 
                                    class="form-control" 
                                    placeholder="<?= $nilai === null ? "Belum diinput" : ""; ?>"
                                    value="<?= $nilai; ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary" name="simpan_nilai">Simpan</button>
            <a href="index.php?page=transaksi" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
