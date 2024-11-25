<?php 
session_start();
if(!isset($_SESSION["login"])){
    header("Location:../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'admin'){
    header("Location:../../auth/login.php?pesan=tolak_akses");
}

$judul = "Data Kelas";
include ('../layout/header.php'); 
require_once('../../siswa/config.php');
$result = mysqli_query($connection,"SELECT * FROM kelas ORDER BY id DESC");
?>




<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <a href="<?=base_url('admin/data_kelas/tambah.php')?>" class="btn btn-primary"><svg
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
                class="icon icon-tabler icons-tabler-filled icon-tabler-circle-plus">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path
                    d="M4.929 4.929a10 10 0 1 1 14.141 14.141a10 10 0 0 1 -14.14 -14.14zm8.071 4.071a1 1 0 1 0 -2 0v2h-2a1 1 0 1 0 0 2h2v2a1 1 0 1 0 2 0v-2h2a1 1 0 1 0 0 -2h-2v-2z" />
            </svg>Tambah Data</a>
        <div class="row row-deck row-cards mt-2">
            <table class="table table-bordered">
                <tr class="text-center">
                    <th>No.</th>
                    <th>Nama Kelas</th>
                    <th>Aksi</th>
                </tr>
                <?php if(mysqli_num_rows($result) === 0) : ?>
                <tr>
                    <td colspan="3">Data Masih Kosong, Silahkan tambahkan data baru</td>
                </tr>
                <?php else : ?>
                <?php $no= 1;
                while($jabatan = mysqli_fetch_array($result)):?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $jabatan['kelas'] ?></td>
                    <td class="text-center">
                        <a href="<?=base_url('admin/data_kelas/edit.php?id='.$jabatan['id'])?>"
                            class="badge bg-primary">Edit</a>
                        <a href="<?=base_url('admin/data_kelas/Hapus.php?id='.$jabatan['id'])?>"
                            class="badge bg-danger tombol-hapus">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>

                <?php endif; ?>

            </table>

        </div>
    </div>
</div>

<?php include('../layout/footer.php') ?>