<?php
session_start();
require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek apakah username ada di tb_siswa atau tb_user
    try {
        $stmt = $pdo->prepare("SELECT * FROM tb_user WHERE user = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($password === $user['pass']) { // Bandingkan langsung password input dengan yang ada di database
                // Set session
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user'] = $user['user'];
                $_SESSION['level'] = $user['lvl'];
        
                // Cek jika user adalah walikelas, ambil id_kelas dari tabel tb_walikelas
                if ($user['lvl'] == 'walikelas') {
                    $_SESSION['id_wali'] = $user['id_wali']; // Simpan id_wali dari tb_user
                    
                    // Ambil id_kelas dari tb_walikelas berdasarkan id_user
                    $stmtKelas = $pdo->prepare("SELECT id_kelas FROM tb_walikelas WHERE id_user = :id_user");
                    $stmtKelas->bindParam(':id_user', $user['id_user']);
                    $stmtKelas->execute();
                    $kelas = $stmtKelas->fetch(PDO::FETCH_ASSOC);
                
                    if ($kelas) {
                        $_SESSION['id_kelas'] = $kelas['id_kelas'];
                    } else {
                        $_SESSION['id_kelas'] = null;
                    }
                
                    header('Location: index.php');
                    exit;
                                
                } elseif ($user['lvl'] == 'admin') {
                    header('Location: index.php');  // Redirect ke halaman admin
                } elseif ($user['lvl'] == 'petugas') {
                    $_SESSION['login'] = 'petugas';
                    header('Location: index.php');  // Redirect ke halaman petugas
                } elseif ($user['lvl'] == 'wakasek') {
                    header('Location: index.php');  // Redirect ke halaman wakasek
                }
                exit;
            } else {
                $error = "Invalid username or password";
            }
        } else {
            // Cek di tb_siswa jika user tidak ditemukan di tb_user
            $stmt = $pdo->prepare("SELECT * FROM tb_siswa WHERE user = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($siswa) {
                if ($password === $siswa['pass']) { // Bandingkan langsung password input dengan yang ada di database
                    // Set session untuk siswa
                    $_SESSION['nis'] = $siswa['nis'];
                    $_SESSION['nama'] = $siswa['nama'];
        
                    header('Location: index.php');  // Redirect ke halaman siswa
                    exit;
                } else {
                    $error = "Invalid username or password";
                }
            } else {
                $error = "Invalid username or password";
            }
        }
        
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
