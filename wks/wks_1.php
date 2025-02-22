
<!-- HALAMAN LOGIN ADMIN -->

<br>
<h3 style="color: green;">Dashboard WKS 1</h3>
<?php
require_once './config.php';

$query_kelas = "SELECT id_kelas, nama_kelas FROM tb_kelas";
$stmt_kelas = $pdo->prepare($query_kelas);
$stmt_kelas->execute();
$kelas_list = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['add'])) {
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $id_kelas = $_POST['id_kelas'];
    $jk = $_POST['jk'];
    $user = $_POST['user'];
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
    $foto = $_FILES['foto'];

    if ($foto['error'] == 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png'];
        $file_ext = pathinfo($foto['name'], PATHINFO_EXTENSION);

        if (!in_array(strtolower($file_ext), $allowed_ext)) {
            echo "<script>alert('Format file tidak valid!');</script>";
            exit;
        }
        $foto_name = time() . '_' . basename($foto['name']);
        $target_dir = 'uploads/';
        $target_file = $target_dir . $foto_name;

        if (!move_uploaded_file($foto['tmp_name'], $target_file)) {
            echo "<script>alert('Gagal mengunggah foto!');</script>";
            exit;
        }
    }

    try {
        $query = "INSERT INTO tb_siswa (nis, nama, id_kelas, jk, user, pass, foto)
                  VALUES (:nis, :nama, :id_kelas, :jk, :user, :pass, :foto)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nis', $nis);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':id_kelas', $id_kelas);
        $stmt->bindParam(':jk', $jk);
        $stmt->bindParam(':user', $user);
        $stmt->bindParam(':pass', $pass);
        $stmt->bindParam(':foto', $foto_name);

        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: 'Data siswa berhasil ditambahkan!',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        window.location.href = 'index.php?page=siswa'; // Redirect ke halaman baru setelah berhasil
                    });
                </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data.',
                        confirmButtonText: 'Ok'
                    });
                </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

// BUAT EDIT SISWA
if (isset($_POST['edit'])) {
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $id_kelas = $_POST['id_kelas'];

    try {
        $query = "UPDATE tb_siswa SET nama = :nama, id_kelas = :id_kelas WHERE nis = :nis";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nis', $nis);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':id_kelas', $id_kelas);

        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data siswa berhasil diupdate!',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        window.location.href = 'index.php?page=siswa'; // Redirect ke halaman baru setelah berhasil
                    });
                </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memperbarui data.',
                        confirmButtonText: 'Ok'
                    });
                </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

// BUAT HAPUS SISWA
if (isset($_GET['delete_nis'])) {
    $nis = $_GET['delete_nis'];

    $sqlFoto = "SELECT foto, id_kelas FROM tb_siswa WHERE nis = :nis";
    $stmtFoto = $pdo->prepare($sqlFoto);
    $stmtFoto->bindParam(':nis', $nis);
    $stmtFoto->execute();
    $fotoSiswa = $stmtFoto->fetch(PDO::FETCH_ASSOC);

    if ($fotoSiswa) {
        if (file_exists('uploads/' . $fotoSiswa['foto'])) {
            unlink('uploads/' . $fotoSiswa['foto']);
        }

        $sqlDelete = "DELETE FROM tb_siswa WHERE nis = :nis";
        $stmtDelete = $pdo->prepare($sqlDelete);
        $stmtDelete->bindParam(':nis', $nis);
        if ($stmtDelete->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Siswa Dihapus',
                    text: 'Data siswa berhasil dihapus!',
                    confirmButtonText: 'Ok'
                }).then(() => {
                    window.location.href = 'index.php?page=siswa'; // Redirect setelah penghapusan
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menghapus',
                    text: 'Terjadi kesalahan saat menghapus data.',
                    confirmButtonText: 'Ok'
                });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Siswa Tidak Ditemukan',
                text: 'Data siswa tidak ditemukan.',
                confirmButtonText: 'Ok'
            });
        </script>";
    }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>

<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahSiswaModal" style="margin-left: 1145px;">Tambah Siswa</button>
        </div>
        <a href="export_pdf.php" class="btn btn-danger">
    <i class="fa-solid fa-file-pdf"></i> 
</a>

<a href="export_excel.php" class="btn btn-success">
    <i class="fa-solid fa-file-excel"></i> 
</a>
<br><br>

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
                        <?php
                        $sqlSiswa = "
                        SELECT s.nis, s.nama, k.nama_kelas, s.foto
                        FROM tb_siswa s
                        LEFT JOIN tb_kelas k ON s.id_kelas = k.id_kelas
                        ";
                        $stmt = $pdo->query($sqlSiswa);
                        $no = 1;
                        foreach ($stmt as $row) :
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nis']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailSiswaModal<?= $row['nis']; ?>">
                                        Detail
                                    </button>
                                    <div class="modal fade" id="detailSiswaModal<?= $row['nis']; ?>" tabindex="-1" aria-labelledby="detailSiswaModalLabel<?= $row['nis']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="detailSiswaModalLabel<?= $row['nis']; ?>">Detail Siswa</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <center><p><strong></strong><img src="uploads/<?= $row['foto']; ?>" width="100" alt="Foto Siswa"></p></center>
                                                    <p><strong>NIS: </strong><?= htmlspecialchars($row['nis']); ?></p>
                                                    <p><strong>Nama: </strong><?= htmlspecialchars($row['nama']); ?></p>
                                                    <p><strong>Kelas: </strong><?= htmlspecialchars($row['nama_kelas']); ?></p>
                                                </div>
                                                <div class="modal-footer">
                                                    <a href="?page=siswa&delete_nis=<?php echo $row['nis']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus siswa ini?');">Hapus</a>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSiswaModal<?= $row['nis']; ?>">
                                        Edit
                                    </button>
                                    <!-- BUAT EDIT SISWA -->
                                    <div class="modal fade" id="editSiswaModal<?= $row['nis']; ?>" tabindex="-1" aria-labelledby="editSiswaModalLabel<?= $row['nis']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editSiswaModalLabel<?= $row['nis']; ?>">Edit Siswa</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="nis" value="<?= $row['nis']; ?>">
                                                        <center><p><strong></strong><img src="uploads/<?= $row['foto']; ?>" width="100" alt="Foto Siswa"></p></center>
                                                        <div class="mb-3">
                                                           <label for="nis" class="form-label">NIS</label>
                                                           <input type="text" class="form-control" id="nis" name="nis" value="<?= htmlspecialchars($row['nis']); ?>" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="nama" class="form-label">Nama</label>
                                                            <input type="text" class="form-control" id="nama" name="nama" value="<?= $row['nama']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="id_kelas" class="form-label">Kelas</label>
                                                            <select class="form-select" id="id_kelas" name="id_kelas" required>
                                                                <?php foreach ($kelas_list as $kelas) : ?>
                                                                    <option value="<?= $kelas['id_kelas'] ?>" <?= $row['nama_kelas'] == $kelas['nama_kelas'] ? 'selected' : '' ?>><?= htmlspecialchars($kelas['nama_kelas']) ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary" name="edit">Simpan</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- BUAT TAMBAH SISWA -->
    <div class="modal fade" id="tambahSiswaModal" tabindex="-1" aria-labelledby="tambahSiswaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahSiswaModalLabel">Tambah Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nis" class="form-label">NIS</label>
                            <input type="text" class="form-control" id="nis" name="nis" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_kelas" class="form-label">Kelas</label>
                            <select class="form-select" id="id_kelas" name="id_kelas" required>
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($kelas_list as $kelas) : ?>
                                    <option value="<?= $kelas['id_kelas'] ?>"><?= htmlspecialchars($kelas['nama_kelas']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jk" class="form-label">Jenis Kelamin</label>
                            <select class="form-select" id="jk" name="jk" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="user" class="form-label">Username</label>
                            <input type="text" class="form-control" id="user" name="user" required>
                        </div>
                        <div class="mb-3">
                            <label for="pass" class="form-label">Password</label>
                            <input type="password" class="form-control" id="pass" name="pass" required>
                        </div>
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto</label>
                            <input type="file" class="form-control" id="foto" name="foto">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="add">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
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



