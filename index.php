<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
 
 <?php
session_start();
require_once 'config.php'; 

include 'navbar.php';

if (!isset($_SESSION['level']) && !isset($_SESSION['nis'])) {
    header('Location: login.php');
    exit;
}
?>

<div class="container mt-4">
    <h1>Selamat Datang di Web Sekolah</h1>

    <?php
    // Menampilkan konten sesuai level pengguna
    if (isset($_SESSION['level'])) {
        if ($_SESSION['level'] == 'admin') {
            include 'admin.php';  
        } elseif ($_SESSION['level'] == 'petugas') {
            include 'petugas.php';  
        } elseif ($_SESSION['level'] == 'walikelas') {
            include 'walikelas.php';  
        } else {
            include 'wakasek.php';
        }
    }
    if (isset($_SESSION['nis'])) {
        include 'siswa.php';  // Konten untuk siswa
    }
    ?>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  