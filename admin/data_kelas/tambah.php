<?php 
session_start();
ob_start();
if(!isset($_SESSION["login"])){
    header("Location:../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'admin'){
    header("Location:../../auth/login.php?pesan=tolak_akses");
}

$judul = "Tambah Data Kelas";
include ('../layout/header.php'); 
require_once('../../siswa/config.php');
if(isset($_POST['submit'])) {
    $kelas = htmlspecialchars( $_POST['kelas']);
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty($kelas)){
            $pesan_kesalahan = "Nama kelas wajib di isi";
        }
        
        if(!empty($pesan_kesalahan)){
            $_SESSION['validasi'] = $pesan_kesalahan;
        }else{
            $result = mysqli_query($connection, "INSERT INTO kelas(kelas) VALUES('$kelas')");
            $_SESSION['berhasil'] = 'Data berhasil di simpan';
            header("Location: kelas.php");
            exit;
        }
    }
    

}
?>


<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <div class="card col-md-6">
            <div class="card-body">
                <form action="<?=base_url('admin/data_kelas/tambah.php')?>" method="POST">
                    <div class="mb-3">
                        <label for="">Nama Kelas</label>
                        <input type="text" class="form-control" name="kelas">
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php') ?>