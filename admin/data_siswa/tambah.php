<?php

session_start();
ob_start();
if(!isset($_SESSION["login"])){
    header("Location:../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'admin'){
    header("Location:../../auth/login.php?pesan=tolak_akses");
}

$judul = "Tambah Pegawai";
include ('../layout/header.php'); 
require_once('../../siswa/config.php');

if( isset($_POST['submit'])){
    $ambil_nisn = mysqli_query($connection, "SELECT nisn FROM siswa ORDER BY nisn DESC LIMIT 1");

    if (mysqli_num_rows($ambil_nisn) > 0) {
        $row = mysqli_fetch_assoc($ambil_nisn);
        $nisn_db = $row['nisn'];
        
        // Pisahkan bagian awal dan 5 digit terakhir dari nisn
        $prefix = substr($nisn_db, 0, 5);  // Ambil 5 digit pertama
        $suffix = substr($nisn_db, -5);    // Ambil 5 digit terakhir
        
        // Tambahkan 1 pada 5 digit terakhir secara keseluruhan
        $no_baru = (int)$suffix + 11111;   // Menambahkan 11111 ke suffix
        
        // Format agar hasilnya tetap 5 digit dengan padding nol
        $suffix_baru = str_pad($no_baru, 5, "0", STR_PAD_LEFT);
        
        // Gabungkan kembali prefix dan suffix yang sudah ditambah
        $nisn_baru = $prefix . $suffix_baru;
    } else {
        // Jika tidak ada nisn sebelumnya, mulai dari angka default
        $nisn_baru = "0000033333";
    }
    $nisn = $nisn_baru;
    $nama = htmlspecialchars($_POST['nama']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $no_handphone = htmlspecialchars($_POST['no_handphone']);
    $kelas = htmlspecialchars($_POST['kelas']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = htmlspecialchars($_POST['role']);
    $status = htmlspecialchars($_POST['status']);
    $lokasi_presensi = htmlspecialchars($_POST['lokasi_presensi']);

    if (isset($_FILES['foto'])) {
        $file = $_FILES['foto'];
        $nama_file = $file['name'];
        $file_tmp = $file['tmp_name'];
        $ukuran_file = $file['size'];
        $file_direktori = "../../asset/img/foto_siswa/" . $nama_file;
    
        $ambil_ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
        $ekstensi_diizinkan = ["jpg", "png", "jpeg"];
        $max_ukuran_file = 10 * 1024 * 1024;
    
        // Validasi ekstensi dan ukuran file
        if (in_array(strtolower($ambil_ekstensi), $ekstensi_diizinkan) && $ukuran_file <= $max_ukuran_file) {
            // Memindahkan file dan menyimpan nama file jika berhasil
            if (move_uploaded_file($file_tmp, $file_direktori)) {
                $foto = $nama_file; // Simpan nama file yang dipindahkan
            } else {
                $pesan_kesalahan[] = "Gagal memindahkan file.";
            }
        } else {
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($nama)) {
            $pesan_kesalahan[] = "Nama wajib di isi";
        }
        if (empty($jenis_kelamin)) {
            $pesan_kesalahan[] = "Jenis kelamin wajib di isi";
        }
        if (empty($alamat)) {
            $pesan_kesalahan[] = "Alamat wajib di isi";
        }
        if (empty($no_handphone)) {
            $pesan_kesalahan[] = "No Handphone wajib di isi";
        }
        if (empty($kelas)) {
            $pesan_kesalahan[] = "Kelas wajib di isi";
        }
        if (empty($username)) {
            $pesan_kesalahan[] = "Username wajib di isi";
        }
        if (empty($role)) {
            $pesan_kesalahan[] = "Role wajib di isi";
        }
        if (empty($status)) {
            $pesan_kesalahan[] = "Status wajib di isi";
        }
        if (empty($lokasi_presensi)) {
            $pesan_kesalahan[] = "Lokasi presensi wajib di isi";
        }
        if ($_POST['password'] != $_POST['konfirmasi_password']) {
            $pesan_kesalahan[] = "Password tidak cocok";
        }
        if (!in_array(strtolower($ambil_ekstensi), $ekstensi_diizinkan)) {
            $pesan_kesalahan[] = "Hanya file JPG,JPEG,PNG yang di perbolehkan";
        }
        
        if ($ukuran_file > $max_ukuran_file) {
            $pesan_kesalahan[] = "Ukuran file melebihi 10 MB";
        }
        
    
    
        $nisn = mysqli_real_escape_string($connection, $nisn);
        
        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            // Pastikan `$foto` terisi sebelum disimpan ke database
            $siswa = mysqli_query($connection, "INSERT INTO siswa(nisn, nama, jenis_kelamin, alamat, no_handphone, kelas, lokasi_presensi, foto) VALUES ('$nisn','$nama','$jenis_kelamin','$alamat','$no_handphone','$kelas','$lokasi_presensi','$foto')");
            
            if ($siswa) {
                $id_pegawai = mysqli_insert_id($connection);
                $user = mysqli_query($connection, "INSERT INTO user(id_pegawai, username, password, status, role) VALUES ('$id_pegawai', '$username', '$password', '$status', '$role')");
        
                if ($user) {
                    $_SESSION['berhasil'] = 'Data berhasil disimpan';
                    header("Location: siswa.php");
                    exit;
                } else {
                    $_SESSION['validasi'] = "Gagal menyimpan data user: " . mysqli_error($connection);
                }
            } else {
                $_SESSION['validasi'] = "Gagal menyimpan data siswa: " . mysqli_error($connection);
            }
        }
    } 
    
}

?>

<div class="page-body">
    <div class="container-xl">
        <form action="<?=base_url('admin/data_siswa/tambah.php')?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="card ">
                        <div class="card-body">

                            <!-- <div class="mb-3">
                                <label for="">NISN</label>
                                <input type="text" class="form-control" name="nisn" value="<?= $nisn_baru ?>" readonly>
                            </div> -->
                            <div class="mb-3">
                                <label for="">Nama</label>
                                <input type="text" class="form-control" name="nama"
                                    value="<?php if(isset($_POST['nama'])) echo $_POST['nama'] ?>">
                            </div>

                            <div class="mb-3">
                                <label for="">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control">
                                    <option value="">---pilih jenis kelamin---</option>
                                    <option <?php if(isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'laki-laki'){
                                echo 'selected';
                            }?> value="laki-laki">
                                        Laki-Laki
                                    </option>
                                    <option <?php if(isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'perempuan'){
                                echo 'selected';
                            }?> value="perempuan">
                                        Perempuan
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="">Alamat</label>
                                <input type="text" class="form-control" name="alamat"
                                    value="<?php if(isset($_POST['alamat'])) echo $_POST['alamat'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="">No Handphone</label>
                                <input type="text" class="form-control" name="no_handphone"
                                    value="<?php if(isset($_POST['alamat'])) echo $_POST['alamat'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="">Kelas</label>
                                <select name="kelas" class="form-control">
                                    <option value="">---pilih kelas---</option>
                                    <?php 
                            $ambil_kelas = mysqli_query($connection, "SELECT * FROM kelas ORDER BY kelas ASC");
                            while($kelas = mysqli_fetch_assoc($ambil_kelas)){
                                $nama_kelas = $kelas['kelas'];

                                if(isset($_POST['jabatan']) && $_POST['kelas'] == $nama_kelas) {
                                     echo '<option value="' .$nama_kelas . '"
                                     selected="selected">' . $nama_kelas . '</option';
                                }else{
                                    echo '<option value="' .$nama_kelas . '">' . $nama_kelas .  '</option>';
                                }
                            }
                            
                            ?>

                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">---pilih status---</option>
                                    <option <?php if(isset($_POST['status']) && $_POST['status'] == 'aktif'){
                                echo 'selected';
                            }?> value="aktif">
                                        Aktif
                                    </option>
                                    <option <?php if(isset($_POST['status']) && $_POST['status'] == 'tidak_aktif'){
                                echo 'selected';
                            }?> value="tidak_aktif">
                                        Tidak Aktif
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="">Username</label>
                                <input type="text" class="form-control" name="username"
                                    value="<?php if(isset($_POST['username'])) echo $_POST['username'] ?>">
                            </div>

                            <div class="mb-3">
                                <label for="">Password</label>
                                <input type="password" class="form-control" name="password">
                            </div>

                            <div class="mb-3">
                                <label for="">Konfirmasi Password</label>
                                <input type="password" class="form-control" name="konfirmasi_password">
                            </div>

                            <div class="mb-3">
                                <label for="">Role</label>
                                <select name="role" class="form-control">
                                    <option value="">---pilih role---</option>
                                    <option <?php if(isset($_POST['role']) && $_POST['role'] == 'admin'){
                                echo 'selected';
                            }?> value="admin">
                                        Admin
                                    </option>
                                    <option <?php if(isset($_POST['role']) && $_POST['role'] == 'siswa'){
                                echo 'selected';
                            }?> value="siswa">
                                        Siswa
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="">Lokasi_Presensi</label>
                                <select name="lokasi_presensi" class="form-control">
                                    <option value="">---pilih lokasi presensi---</option>
                                    <?php 
                            $ambil_lok_presensi = mysqli_query($connection, "SELECT * FROM lokasi_presensi ORDER BY nama_lokasi ASC");
                            while($lokasi = mysqli_fetch_assoc($ambil_lok_presensi)){
                                $nama_lokasi = $lokasi['nama_lokasi'];

                                if(isset($_POST['lokasi_presensi']) && $_POST['lokasi_presensi'] == $nama_lokasi) {
                                     echo '<option value="' .$nama_lokasi . '"
                                     selected="selected">' . $nama_lokasi . '</option';
                                }else{
                                    echo '<option value="' .$nama_lokasi . '">' . $nama_lokasi .  '</option>';
                                }
                            }
                            
                            ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="">Foto</label>
                                <input type="file" class="form-control" name="foto">
                            </div>
                            <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include('../layout/footer.php') ?>