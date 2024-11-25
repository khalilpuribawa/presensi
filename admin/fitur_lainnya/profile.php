<?php

session_start();
if(!isset($_SESSION["login"])){
    header("Location:../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'admin'){
    header("Location:../../auth/login.php?pesan=tolak_akses");
}

$judul = "";
include ('../layout/header.php'); 
require_once('../../siswa/config.php');

$id = $_SESSION['id'];
$result = mysqli_query($connection,"SELECT user.id_pegawai,user.username,user.status,user.role, siswa. * FROM user JOIN siswa ON user.id_pegawai = siswa.id where siswa.id=$id");

?>

<?php while($siswa = mysqli_fetch_array($result)) : ?>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <center>
                            <img style="border-radius: 100%; width: 180px; "
                                src="<?= base_url('asset/img/foto_siswa/' .$siswa['foto']) ?>" alt="">
                        </center>
                        <table class="table mt-4">
                            <tr>
                                <td>Nama</td>
                                <td>: <?= $siswa['nama'] ?></td>
                            </tr>
                            <tr>
                                <td>Jenis Kelamin</td>
                                <td>: <?= $siswa['jenis_kelamin'] ?></td>
                            </tr>
                            <tr>
                                <td>No. Handphone</td>
                                <td>: <?= $siswa['no_handphone'] ?></td>
                            </tr>
                            <tr>
                                <td>kelas/jabatan</td>
                                <td>: <?= $siswa['kelas'] ?></td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td>: <?= $siswa['username'] ?></td>
                            </tr>
                            <tr>
                                <td>Role</td>
                                <td>: <?= $siswa['role'] ?></td>
                            </tr>
                            <tr>
                                <td>Lokasi Presensi</td>
                                <td>: <?= $siswa['lokasi_presensi'] ?></td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>: <?= $siswa['status'] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
</div>

<?php endwhile; ?>

<?php include('../layout/footer.php') ?>