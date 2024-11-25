<?php 
ob_start(); 
session_status() == PHP_SESSION_NONE;
session_start();
if(!isset($_SESSION["login"])){
    header("Location:../../auth/login.php?pesan=belum_login");
    exit;
}else if($_SESSION["role"] != 'siswa'){
    header("Location:../../auth/login.php?pesan=tolak_akses");
    exit;
}

$judul = 'Edit Pengajuan Ketidak Hadiran';
include('../layout/header.php');
include_once("../../siswa/config.php");

if (isset($_POST["update"])) {
    $id = $_POST["id"];
    $keterangan = $_POST["keterangan"];
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];
    if ($_FILES['file_baru']['error'] === 4) {
        // Tidak ada file baru diupload, gunakan file lama
        $nama_file = $_POST['file_lama'];
    } else {
        // Ada file baru diupload
        $file = $_FILES['file_baru'];
        $nama_file = $file['name'];
        $file_tmp = $file['tmp_name'];
        $ukuran_file = $file['size'];
        $file_direktori = "../../asset/img/file_ketidakhadiran/" . $nama_file;
    
        // Validasi ukuran dan ekstensi file
        $ambil_ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $ekstensi_diizinkan = ["jpg", "png", "jpeg", "pdf"];
        $max_ukuran_file = 10 * 1024 * 1024; // Maksimal 10 MB
    
        if (!in_array($ambil_ekstensi, $ekstensi_diizinkan)) {
            $pesan_kesalahan[] = "Hanya file JPG, JPEG, PNG, dan PDF yang diperbolehkan.";
        }
    
        if ($ukuran_file > $max_ukuran_file) {
            $pesan_kesalahan[] = "Ukuran file melebihi 10 MB.";
        }
    
        if (empty($pesan_kesalahan)) {
            if (move_uploaded_file($file_tmp, $file_direktori)) {
                // File berhasil diupload
            } else {
                $pesan_kesalahan[] = "Gagal mengupload file baru.";
            }
        }
    }
    
    // Update data ke database
    if (empty($pesan_kesalahan)) {
        $siswa = mysqli_query($connection, "UPDATE ketidakhadiran 
            SET keterangan='$keterangan', deskripsi='$deskripsi', tanggal_pengajuan='$tanggal', file='$nama_file' 
            WHERE id = $id");
    
        $_SESSION['berhasil'] = 'Data berhasil diupdate';
        header("Location: ketidakhadiran.php");
        exit;
    } else {
        // Simpan pesan kesalahan jika ada
        $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
    }
    
        // Jika ada kesalahan, simpan pesan kesalahan di session
        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        }
    }



$id = $_GET['id'];
$result = mysqli_query($connection, "SELECT * FROM ketidakhadiran WHERE id=$id");
while($data = mysqli_fetch_array($result)){
    $keterangan = $data['keterangan'];
    $deskripsi = $data['deskripsi'];
    $file = $data['file'];
    $tanggal = $data['tanggal_pengajuan'];

}

?>
<div class="page-body">
    <div class="container-xl">
        <div class="card col-md-6">
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="<?= $_SESSION['id'] ?>" name="id_siswa">
                    <div class="mb-3">
                        <label for="">Keterangan</label>
                        <select name="keterangan" class="form-control">
                            <option value="">---pilih jenis keterangan---</option>
                            <option <?php if($keterangan == 'izin'){
                                echo 'selected';
                            }
                            ?> value="izin">
                                Izin
                            </option>
                            <option <?php if($keterangan == 'sakit'){
                                echo 'selected';
                            }
                            ?> value="sakit">
                                Sakit
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" cols="30" rows="5"><?= $deskripsi ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= $tanggal; ?>">
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="">Surat Keterangan</label>
                            <input type="file" class="form-control" name="file_baru">
                            <input type="hidden" name="file_lama" value="<?= $file ?>">
                        </div>

                        <input type="hidden" name="id" value="<?= $_GET['id']; ?>">

                        <button type="submit" class="btn btn-primary" name="update">Update</button>
                    </form>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php');?>