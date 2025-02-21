<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<?php
require_once 'config.php'; 

if (isset($_GET['nis']) && is_numeric($_GET['nis'])) {
    $nis = $_GET['nis'];
} else {
    die('NIS tidak ditemukan atau tidak valid!');
}

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
$stmtBayar->execute(['nis' => $nis]);

$pembayaran = [];
while ($rowBayar = $stmtBayar->fetch(PDO::FETCH_ASSOC)) {
    $pembayaran[$rowBayar['bulan']] = $rowBayar;
}

if (isset($_POST['bayar'])) {
    $nis = $_POST['nis'];
    $bulan = $_POST['bulan'];
    $spp = $_POST['spp'];
    $tabungan = $_POST['tabungan'];
    $extra = $_POST['extra'];
    $iduser = 1; 

    if (empty($nis) || empty($bulan) || empty($spp) || empty($tabungan) || empty($extra)) {
        die('Semua kolom harus diisi!');
    }

    try {
        $sql = "
        INSERT INTO tb_bayar (nis, bulan, jenis, jumlah, id_user)
        VALUES (:nis, :bulan, 'SPP', :spp, :id_user),
               (:nis, :bulan, 'Tabungan', :tabungan, :id_user),
               (:nis, :bulan, 'Extra', :extra, :id_user)
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nis', $nis);
        $stmt->bindParam(':bulan', $bulan);
        $stmt->bindParam(':spp', $spp);
        $stmt->bindParam(':tabungan', $tabungan);
        $stmt->bindParam(':extra', $extra);
        $stmt->bindParam(':id_user', $iduser);

        $stmt->execute();

        header('Location: index.php?page=transaksi');


    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h4>Input Pembayaran</h4>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <input type="hidden" name="nis" value="<?= htmlspecialchars($nis); ?>">
                <div class="form-group">
                    <label for="bulan">Bulan</label>
                    <select class="form-control" id="bulan" name="bulan" required>
                        <?php
                        $bulanArray = [
                            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ];
                        foreach ($bulanArray as $index => $bulan) {
                            $selected = (isset($pembayaran[$index + 1])) ? 'disabled' : '';
                        ?>
                            <option value="<?= $index + 1; ?>" <?= $selected; ?>><?= $bulan; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group mt-3">
                    <label for="spp">SPP</label>
                    <input type="number" class="form-control" id="spp" name="spp" min="0" placeholder="Masukkan jumlah SPP" required>
                </div>
                <div class="form-group mt-3">
                    <label for="tabungan">Tabungan</label>
                    <input type="number" class="form-control" id="tabungan" name="tabungan" min="0" placeholder="Masukkan jumlah tabungan" required>
                </div>
                <div class="form-group mt-3">
                    <label for="extra">Extra</label>
                    <input type="number" class="form-control" id="extra" name="extra" min="0" placeholder="Masukkan jumlah extra" required>
                </div>
                <div class="form-group mt-4">
                    <button type="submit" name="bayar" class="btn btn-primary">Submit Pembayaran</button>
                    <a href="index.php?page=transaksi" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

