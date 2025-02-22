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
      <h1 class="text-2xl font-bold">Dashboard WKS 2 - Daftar Pembayaran Siswa</h1>
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
                  <button class="bg-green-500 text-white px-3 py-1 rounded-md hover:bg-green-600" data-bs-toggle="modal" data-bs-target="#bayarModal<?= $siswa['nis']; ?>">
                    Pembayaran
                  </button>
                 
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Pembayaran -->
  <?php foreach ($siswa_list as $siswa): ?>
    <div class="modal fade fixed inset-0 bg-gray-600 bg-opacity-50 hidden" id="bayarModal<?= $siswa['nis']; ?>" tabindex="-1" aria-labelledby="bayarModalLabel<?= $siswa['nis']; ?>" aria-hidden="true">
      <div class="modal-dialog relative w-auto mx-auto mt-20 max-w-4xl">
        <div class="modal-content bg-white rounded-lg shadow-lg">
          <div class="modal-header p-4 border-b">
            <h5 class="text-xl font-semibold">Detail Pembayaran - <?= htmlspecialchars($siswa['nama']); ?></h5>
            <button type="button" class="close absolute top-4 right-4 text-gray-600 hover:text-gray-900" data-bs-dismiss="modal" aria-label="Close">
              &times;
            </button>
          </div>
          <div class="modal-body p-4">
            <?php
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
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SPP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tabungan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extra</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <?php
                  $bulanArray = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                  ];
                  foreach ($bulanArray as $index => $bulan):
                    $data = isset($pembayaran[$index + 1]) ? $pembayaran[$index + 1] : null;
                  ?>
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap"><?= $bulan; ?></td>
                      <td class="px-6 py-4 whitespace-nowrap"><?= $data ? "Rp " . number_format($data['spp'], 0, ',', '.') : '-'; ?></td>
                      <td class="px-6 py-4 whitespace-nowrap"><?= $data ? "Rp " . number_format($data['tabungan'], 0, ',', '.') : '-'; ?></td>
                      <td class="px-6 py-4 whitespace-nowrap"><?= $data ? "Rp " . number_format($data['extra'], 0, ',', '.') : '-'; ?></td>
                      <td class="px-6 py-4 whitespace-nowrap"><?= $data ? "Rp " . number_format($data['total'], 0, ',', '.') : '-'; ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer p-4 border-t">
            <a href="bayar.php?nis=<?= htmlspecialchars($siswa['nis']); ?>" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Input Pembayaran</a>
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