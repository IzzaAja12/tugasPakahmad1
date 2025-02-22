<?php
require_once 'config.php'; 

if (!isset($_SESSION['id_wali'])) {
    header('Location: login.php');
    exit;
}

$id_wali = $_SESSION['id_wali'];
$id_user = $_SESSION['user_id'];
?>

<style>
    .judul-walikelas {
        color: green;
        font-weight: bold;
        margin-top: 10px;
    }
</style>

<div class="container mt-4">
    <?php
    switch ($id_wali) {
        case 1:
            echo "<h3 class='judul-walikelas'>Selamat datang Wali Kelas PPLG</h3>";
            break;
        case 2:
            echo "<h3 class='judul-walikelas'>Selamat datang Wali Kelas AKL</h3>";
            break;
        case 3:
            echo "<h3 class='judul-walikelas'>Selamat datang Wali Kelas MPLB</h3>";
            break;
        case 4:
            echo "<h3 class='judul-walikelas'>Selamat datang Wali Kelas PM</h3>";
            break;
        default:
            echo "<h3 class='judul-walikelas'>Jurusan tidak dikenal</h3>";
            break;
    }

    $query = "
    SELECT s.nis, s.nama, k.nama_kelas 
    FROM tb_siswa s
    LEFT JOIN tb_kelas k ON s.id_kelas = k.id_kelas
    LEFT JOIN tb_walikelas w ON k.jurusan = w.jurusan
    WHERE w.id_user = :id_user
    ";
    

    $stmt = $pdo->prepare($query);
    $stmt->execute([':id_user' => $id_user]);
    $siswa_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="card mt-3">
        <div class="table-responsive">
            <table id="datatable" class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($siswa_list as $siswa): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($siswa['nis']); ?></td>
                            <td><?= htmlspecialchars($siswa['nama']); ?></td>
                            <td><?= htmlspecialchars($siswa['nama_kelas']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#datatable').DataTable();
    });
</script>

<?php include 'footer.php'; ?>
