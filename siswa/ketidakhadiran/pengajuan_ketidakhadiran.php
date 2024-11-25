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

$judul = 'Pengajuan Ketidak Hadiran';
include('../layout/header.php');
include_once("../../siswa/config.php");

if (isset($_POST["submit"])) {
    $id = $_POST["id_siswa"];
    $keterangan = $_POST["keterangan"];
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];
    $status_pengajuan = 'PENDING';

    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $nama_file = $file['name'];
        $file_tmp = $file['tmp_name'];
        $ukuran_file = $file['size'];
        $file_direktori = "../../asset/img/file_ketidakhadiran/" . $nama_file;

        // Mendapatkan ekstensi file yang benar
        $ambil_ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $ekstensi_diizinkan = ["jpg", "png", "jpeg", "pdf"];
        $max_ukuran_file = 10 * 1024 * 1024; // Maksimal 10MB
        
        // Pengecekan ekstensi file dan ukuran file
        if (!in_array($ambil_ekstensi, $ekstensi_diizinkan)) {
            $pesan_kesalahan[] = "Hanya file JPG, JPEG, PNG, dan PDF yang diperbolehkan.";
        }
        
        if ($ukuran_file > $max_ukuran_file) {
            $pesan_kesalahan[] = "Ukuran file melebihi 10 MB.";
        }

        // Jika tidak ada kesalahan, pindahkan file ke direktori tujuan
        if (empty($pesan_kesalahan)) {
            if (move_uploaded_file($file_tmp, $file_direktori)) {
                // Simpan data ke database jika file berhasil diupload
                $siswa = mysqli_query($connection, "INSERT INTO ketidakhadiran(id_siswa, keterangan, deskripsi, tanggal_pengajuan, status_pengajuan, file) 
                                                    VALUES ('$id', '$keterangan', '$deskripsi', '$tanggal', '$status_pengajuan', '$nama_file')");
                
                $_SESSION['berhasil'] = 'Data berhasil disimpan';
                error_log("Redirecting to ketidakhadiran.php");
                header ("Location: ketidakhadiran.php");
                exit;
            } 
        }
    }

    // Validasi input lainnya
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($keterangan)) {
            $pesan_kesalahan[] = "Keterangan wajib diisi.";
        }
        if (empty($tanggal)) {
            $pesan_kesalahan[] = "Tanggal wajib diisi.";
        }
        if (empty($deskripsi)) {
            $pesan_kesalahan[] = "Deskripsi wajib diisi.";
        }

        // Jika ada kesalahan, simpan pesan kesalahan di session
        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        }
    }
}



$id = $_SESSION['id'];
$result = mysqli_query($connection, "SELECT * FROM ketidakhadiran WHERE id_siswa = '$id' ORDER BY id DESC");

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
                            <option <?php if(isset($_POST['keterangan']) && $_POST['keterangan'] == 'izin'){
                                echo 'selected';
                            }?> value="izin">
                                Izin
                            </option>
                            <option <?php if(isset($_POST['keterangan']) && $_POST['keterangan'] == 'sakit'){
                                echo 'selected';
                            }?> value="sakit">
                                Sakit
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" cols="30" rows="5"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control">
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="">Surat Keterangan</label>
                            <input type="file" name="file" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Ajukan</button>
                    </form>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php');?>