<?php
session_start();
if (isset($_SESSION['username'])) {

	// ############## INI ADALAH HALAMAN YANG DITAMPILKAN JIKA LOGIN BERHASIL ##########################

	$user = $_SESSION['username'];
	$nik = $_SESSION['nik']; //bisa juga diganti pake ID tergantung kondisi
	$nama = $_SESSION['nama'];

	
	
?>

selamat datang masyarakat  <br>
nama : <?=$nama?> <br>
email : <?=$user?> <br>
NIK/ID : <?=$nik?> <br>

<?php 
}
else {	
	// ############## INI ADALAH HALAMAN JIKA GAGAL DAN DIARAHKAN KE HALAMAN LOGIN ####################

	echo "<script> alert('HALAMAN INI HANYA UNTUK PENGGUNA');window.location.href='login.php';</script>";
}

?>