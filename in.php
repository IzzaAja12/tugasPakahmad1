<div class="container mt-4">
    <?php
    //tampilkan konten sesuai halaman yang diminta
    if (isset($_GET['page'])) {
        $page = $_GET['page'];

        switch ($page) {
            case 'siswa':
                include 'siswa.php';
                break;

            case 'transaksi':
                include 'transaksi.php';
                break;

            case 'dsiswa':
                include 'dsiswa.php';
                break;

            case 'nilai':
                if ($_SESSION['level'] === 'petugas') {
                    if (isset($_GET['nis']) && !empty($_GET['nis'])) {
                        $nis = $_GET['nis'];
                        include 'nilai.php';
                    } else {
                        echo "<center><h3>Parameter NIS tidak valid!</h3></center>";
                    }
                } else {
                    echo "<center><h3>Akses ditolak! Halaman ini hanya untuk petugas.</h3></center>";
                }
                break;

            case 'bayar':
                include 'bayar.php';
                break;

            default:
            echo "<center><h3>Maaf, halaman tidak ditemukan!</h3></center>";
            break;
        }
    } else {
        //tampilkan konten berdasarkan level pengguna
        if (isset($_SESSION['level'])) {
            switch ($_SESSION['level']) {
                case 'admin':
                    include 'admin.php';
                    break;

                case 'petugas':
                    include 'petugas.php';
                    break;

                case 'wakasek':
                    include 'wakasek.php';
                    break;

                case 'walikelas':
                    include 'walikelas.php';
                    break;
            }
        } elseif (isset($_SESSION['nis'])) {
            include 'siswa.php';
        } else {
            include 'home.php';
        }
    }
    ?>
</div>