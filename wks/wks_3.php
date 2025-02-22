<!-- HALAMAN LOGIN PETUGAS -->

<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once './config.php';

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

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Siswa</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
</head>

<body class="bg-gray-100">
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Dashboard WKS 3 - Daftar Nilai Siswa</h1>
      <a href="./logout.php" class="bg-red-400 rounded-lg px-3 py-2 text-white font-medium">Logout</a>
    </div>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <div class="p-4">
        <table id="datatable" class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php $no = 1; ?>
            <?php foreach ($siswa_list as $siswa): ?>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap"><?= $no++; ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($siswa['nis']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($siswa['nama']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($siswa['nama_kelas']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                  
                  <button class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 ml-2" data-bs-toggle="modal" data-bs-target="#nilaiModal<?= $siswa['nis']; ?>">
                    Nilai
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  
  <!-- Modal Nilai -->
  <?php foreach ($siswa_list as $siswa): ?>
    <div class="modal fade fixed inset-0 bg-gray-600 bg-opacity-50 hidden" id="nilaiModal<?= $siswa['nis']; ?>" tabindex="-1" aria-labelledby="nilaiModalLabel<?= $siswa['nis']; ?>" aria-hidden="true">
      <div class="modal-dialog relative w-auto mx-auto mt-20 max-w-2xl">
        <div class="modal-content bg-white rounded-lg shadow-lg">
          <div class="modal-header p-4 border-b">
            <h5 class="text-xl font-semibold">Input Nilai - <?= htmlspecialchars($siswa['nama']); ?></h5>
            <button type="button" class="close absolute top-4 right-4 text-gray-600 hover:text-gray-900" data-bs-dismiss="modal" aria-label="Close">
              &times;
            </button>
          </div>
          <div class="modal-body p-4">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($mapel_list as $mapel): ?>
                  <?php
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
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($mapel['nama_mapel']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($nilai); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="modal-footer p-4 border-t">
            <a href="nilai.php?nis=<?= $siswa['nis']; ?>" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Input Nilai</a>
            <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 ml-2" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#datatable').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "responsive": true
      });

      // Handle modal toggle
      document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
        button.addEventListener('click', () => {
          const target = button.getAttribute('data-bs-target');
          document.querySelector(target).classList.remove('hidden');
        });
      });

      // Handle modal close
      document.querySelectorAll('.close').forEach(button => {
        button.addEventListener('click', () => {
          button.closest('.modal').classList.add('hidden');
        });
      });
    });
  </script>
</body>

</html>