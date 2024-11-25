<?php 
session_start();
ob_start();
if(!isset($_SESSION["login"])){
    header("Location:../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'admin'){
    header("Location:../../auth/login.php?pesan=tolak_akses");
}

$judul = "Edit Data Kelas";
include ('../layout/header.php'); 
require_once('../../siswa/config.php');
if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $jabatan  = htmlspecialchars( $_POST['kelas']);
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty($jabatan)){
            $pesan_kesalahan = "Nama kelas wajib di isi";
        }
        
        if(!empty($pesan_kesalahan)){
            $_SESSION['validasi'] = $pesan_kesalahan;
        }else{
            $result = mysqli_query($connection, "UPDATE kelas SET kelas ='$jabatan' WHERE id=$id");
            $_SESSION['berhasil'] = 'Data berhasil di update';
            header("Location: kelas.php");
            exit;
        }
    }
    

}

// $id = $_GET['id'];
$id = isset($_GET["id"]) ? $_GET["id"] : $_POST['id'];
$result = mysqli_query($connection, "SELECT * FROM kelas WHERE id=$id");

while($kelas = mysqli_fetch_array($result)){
    $nama_kelas = $kelas['kelas'];
}

?>



<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <div class="card col-md-6">
            <div class="card-body">
                <form action="<?=base_url('admin/data_kelas/edit.php')?>" method="POST">
                    <div class="mb-3">
                        <label for="">Nama Kelas</label>
                        <input type="text" class="form-control" name="kelas" value="<?=$nama_kelas?>">
                    </div>
                    <input type="hidden" value="<?= $id ?>" name="id">
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php') ?>