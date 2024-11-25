<?php 

session_start();
if(!isset($_SESSION["login"])){
    header("Location:../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'admin'){
    header("Location:../../auth/login.php?pesan=tolak_akses");
}

$judul = "Data Ketidakhadiran";
include ('../layout/header.php'); 
require_once('../../siswa/config.php');



if(empty($_GET['tanggal_dari'])) {
    $tanggal_hari_ini = date('Y-m-d');
    $result = mysqli_query($connection, "
        SELECT ketidakhadiran.*, siswa.nama 
        FROM ketidakhadiran 
        JOIN siswa ON ketidakhadiran.id_siswa = siswa.id  
        WHERE ketidakhadiran.tanggal_pengajuan = '$tanggal_hari_ini'  
        ORDER BY ketidakhadiran.id DESC
    ");
} else {
    $tanggal_dari = $_GET['tanggal_dari'];
    $tanggal_sampai = $_GET['tanggal_sampai'];
    $result = mysqli_query($connection, "
        SELECT ketidakhadiran.*, siswa.nama 
        FROM ketidakhadiran 
        JOIN siswa ON ketidakhadiran.id_siswa = siswa.id 
        WHERE ketidakhadiran.tanggal_pengajuan BETWEEN '$tanggal_dari' AND '$tanggal_sampai'  
        ORDER BY ketidakhadiran.id DESC
    ");
}
?>

<div class="page-body">
    <div class="container-xl">
        <div class="row mb-4">
            <div class="col-md-10">
                <form method="GET">
                    <div class="input-group">
                        <input type="date" class="form-control" name="tanggal_dari">
                        <input type="date" class="form-control" name="tanggal_sampai">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                    </div>
                </form>
            </div>
        </div>
        <?php if(empty($_GET['tanggal_dari'])) : ?>
        <span>Rekap Presensi Tanggal: <?= date('d F Y') ?></span>
        <?php else : ?>
        <span>Rekap Presensi Tanggal:
            <?= date('d F Y', strtotime($_GET['tanggal_dari'])) . ' sampai ' . date('d F Y',strtotime( $_GET['tanggal_sampai']))?></span>
        <?php endif; ?>
        <table class="table table-bordered mt-2">
            <tr class="text-center">
                <th>No.</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Deskripsi</th>
                <th>File</th>
                <th>Status Pengajuan</th>
            </tr>
            <?php if(mysqli_num_rows($result) === 0) { ?>
            <tr>
                <td colspan="7">Data Ketidak Hadiran Masih Kosong</td>
            </tr>
            <?php }else{ ?>

            <?php $no = 1;
                while($data = mysqli_fetch_array($result)) : ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $data['nama'] ?></td>
                <td><?= date('d F Y', strtotime($data['tanggal_pengajuan'])) ?></td>
                <td><?= $data['keterangan'] ?></td>
                <td><?= $data['deskripsi'] ?></td>
                <td class="text-center">
                    <a target="_blank" href="<?= base_url('asset/img/file_ketidakhadiran/' . $data['file']) ?>"
                        class="badge badge-pill bg-primary">Download</a>
                </td>
                <td class="text-center">
                    <?php if($data['status_pengajuan'] == 'PENDING') : ?>
                    <a class="badge badge-pill bg-warning"
                        href="<?= base_url('admin/data_ketidakhadiran/detail.php?id=' . $data['id']) ?>">PENDING</a>
                    <?php elseif($data['status_pengajuan'] == 'REJECTED') : ?>
                    <a class="badge badge-pill bg-danger"
                        href="<?= base_url('admin/data_ketidakhadiran/detail.php?id=' . $data['id']) ?>">REJECTED</a>
                    <?php else: ?>
                    <a class="badge badge-pill bg-success"
                        href="<?= base_url('admin/data_ketidakhadiran/detail.php?id=' . $data['id']) ?>">APPROVED</a>
                    <?php endif ?>

                </td>
            </tr>
            <?php endwhile ?>
            <?php } ?>
        </table>
    </div>
</div>



<?php include('../layout/footer.php') ?>