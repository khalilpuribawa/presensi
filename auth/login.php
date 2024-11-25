<?php 
session_start();

require_once('../siswa/config.php');

if(isset($_POST["login"])){
    $username = $_POST["username"];
    $password = $_POST["password"];

    $result = mysqli_query($connection, "SELECT * FROM user JOIN siswa ON user.id_pegawai = siswa.id WHERE username = '$username' ");
    
    if(mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if(password_verify($password, $row["password"])){
            if($row['status'] == 'aktif'){
                $_SESSION['login'] = true;
                $_SESSION['id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['nama'] = $row['nama'];
                $_SESSION['nisn'] = $row['nisn'];
                $_SESSION['kelas'] = $row['kelas'];
                $_SESSION['lokasi_presensi'] = $row['lokasi_presensi'];
                
                if($row['role'] === 'admin'){
                    header("Location: ../admin/home/home.php");
                    exit();
                }else{
                    header("Location: ../siswa/home/home.php");
                    exit();
                }
            }else{
                $_SESSION["gagal"] = "Akun anda belum aktif";
            }
        }else{
            $_SESSION["gagal"] = "Password salah, silahkan coba lagi";
        }
    }else{
        $_SESSION["gagal"] = "Username salah, silahkan coba lagi";
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>ABSENSIPINTAR || ITS ACADEMY</title>
    <!-- CSS files -->
    <link href="<?php echo base_url('asset/css/tabler.min.css?1692870487') ?>" rel="stylesheet" />
    <link href="<?php echo base_url('asset/css/tabler-vendors.min.css?1692870487')?>" rel="stylesheet" />
    <link href="<?php echo base_url('asset/css/demo.min.css?1692870487') ?>" rel="stylesheet" />
    <style>
    @import url('https://rsms.me/inter/inter.css');

    :root {
        --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }

    body {
        font-feature-settings: "cv03", "cv04", "cv11";
    }
    </style>
</head>

<body class=" d-flex flex-column">
    <script src="./dist/js/demo-theme.min.js?1692870487"></script>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href=".">
                    <img src="<?php echo base_url('asset/img/ITS.svg')?>" width="155" height="60" alt="Tabler"
                        class="navbar-brand-image">
                </a>
                <span class="w-110 h-30 fw-bold fs-2 mt-5 ms-2">ITS ACADEMY ABSENSI</span>

            </div>
            <?php 
            if(isset($_GET['pesan'])){
                if($_GET['pesan'] == "belum_login"){
                    $_SESSION['gagal'] = 'Anda belum login';
                }else if($_GET['pesan'] == "tolak_akses"){
                    $_SESSION['gagal'] = 'Akses menuju halaman ini di tolak';
                }
            }
            ?>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Login to your account</h2>
                    <form action="" method="POST" autocomplete="off" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" autofocus name="username" placeholder="Username"
                                autocomplete="off">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">
                                Password
                            </label>
                            <div class="input-group input-group-flat">
                                <input type="password" class="form-control" name="password" placeholder="Password"
                                    autocomplete="off">
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path
                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="submit" name="login" class="btn btn-primary w-100">Sign in</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Libs JS -->
    <script src="<?php echo base_url('asset/libs/apexcharts/dist/apexcharts.min.js?1692870487') ?>" defer></script>
    <script src="<?php echo base_url('asset/libs/jsvectormap/dist/js/jsvectormap.min.js?1692870487') ?>" defer>
    </script>
    <script src="<?php echo base_url('asset/libs/jsvectormap/dist/maps/world.js?1692870487') ?>" defer></script>
    <script src="<?php echo base_url('asset/libs/jsvectormap/dist/maps/world-merc.js?1692870487') ?>" defer>
    </script>
    <!-- Tabler Core -->
    <script src="<?php echo base_url('asset/js/tabler.min.js?1692870487') ?>" defer></script>
    <script src="<?php echo base_url('asset/js/demo.min.js?1692870487') ?>" defer></script>
    <!-- Sweet alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if(isset($_SESSION['gagal'])) {?>
    <script>
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "<?= $_SESSION['gagal']; ?>",
        footer: '<a href="#">Why do I have this issue?</a>'
    });
    </script>
    <?php unset($_SESSION['gagal']) ?>
    <?php } ?>

</body>

</html>