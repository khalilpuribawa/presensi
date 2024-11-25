<?php

session_start();
ob_start();
if(!isset($_SESSION["login"])){
    header("Location:../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'admin'){
    header("Location:../../auth/login.php?pesan=tolak_akses");
}

$judul = "Edit Data Siswa";
include ('../layout/header.php'); 
require_once('../../siswa/config.php');

if( isset($_POST['edit'])){
    $id = $_POST['id'];
    $nama = htmlspecialchars($_POST['nama']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $no_handphone = htmlspecialchars($_POST['no_handphone']);
    $kelas = htmlspecialchars($_POST['kelas']);
    $username = htmlspecialchars($_POST['username']);
    $role = htmlspecialchars($_POST['role']);
    $status = htmlspecialchars($_POST['status']);
    $lokasi_presensi = htmlspecialchars($_POST['lokasi_presensi']);

    if(empty($_POST['password'])){
        $password = $_POST['password_lama'];
    }else{
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    if($_FILES['foto']['error'] === 4){
        $nama_file = $_POST['foto_lama'];
    }else{
        if (isset($_FILES['foto'])) {
            $file = $_FILES['foto'];
            $nama_file = $file['name'];
            $file_tmp = $file['tmp_name'];
            $ukuran_file = $file['size'];
            $file_direktori = "../../asset/img/foto_siswa/" . $nama_file;
        
            $ambil_ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
            $ekstensi_diizinkan = ["jpg", "png", "jpeg"];
            $max_ukuran_file = 15 * 1024 * 1024;
        
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

        if($_FILES['foto']['error'] != 4){
            if (!in_array(strtolower($ambil_ekstensi), $ekstensi_diizinkan)) {
                $pesan_kesalahan[] = "Hanya file JPG,JPEG,PNG yang di perbolehkan";
            }
            
            if ($ukuran_file > $max_ukuran_file) {
                $pesan_kesalahan[] = "Ukuran file melebihi 15 MB";
            }
        }
        
    
    
        
        
        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            // Pastikan `$foto` terisi sebelum disimpan ke database
            $siswa = mysqli_query($connection, "UPDATE siswa SET
                nama = '$nama',
                jenis_kelamin = '$jenis_kelamin',
                alamat = '$alamat',
                no_handphone = '$no_handphone',
                kelas = '$kelas',
                lokasi_presensi = '$lokasi_presensi',
                foto = '$nama_file'

            WHERE id = '$id';");
            
            if ($siswa) {
                $user = mysqli_query($connection, "UPDATE user SET
                    username = '$username',
                    password = '$password',
                    status = '$status',
                    role = '$role'
                WHERE id = '$id'");
        
                if ($user) {
                    $_SESSION['berhasil'] = 'Data berhasil di update';
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


$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
$result = mysqli_query($connection,"SELECT user.id_pegawai,user.username,user.password,user.status,user.role, siswa. 
* FROM user JOIN siswa ON user.id_pegawai = siswa.id WHERE siswa.id = $id");
while($siswa = mysqli_fetch_array($result)){
    $nama = $siswa['nama'];
    $jenis_kelamin = $siswa['jenis_kelamin'];
    $alamat = $siswa['alamat'];
    $no_handphone = $siswa['no_handphone'];
    $kelas = $siswa['kelas'];
    $username = $siswa['username'];
    $password = $siswa['password'];
    $status = $siswa['status'];
    $lokasi_presensi = $siswa['lokasi_presensi'];
    $role = $siswa['role'];
    $foto = $siswa['foto'];
}


?>

<div class="page-body">
    <div class="container-xl">
        <form action="<?=base_url('admin/data_siswa/edit.php')?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="card ">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="">Nama</label>
                                <input type="text" class="form-control" name="nama" value="<?= $nama ?>">
                            </div>
                            <div class="mb-3">
                                <label for="">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control">
                                    <option value="">---pilih jenis kelamin---</option>
                                    <option <?php if($jenis_kelamin == 'laki-laki'){
                                echo 'selected';
                                    }?> value="laki-laki">
                                        Laki-Laki
                                    </option>
                                    <option <?php if ($jenis_kelamin == 'perempuan'){
                                        echo 'selected';
                                    } ?> value="perempuan">
                                        Perempuan
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="">Alamat</label>
                                <input type="text" class="form-control" name="alamat" value="<?= $alamat?>">
                            </div>
                            <div class="mb-3">
                                <label for="">No Handphone</label>
                                <input type="text" class="form-control" name="no_handphone"
                                    value="<?= $no_handphone ?>">
                            </div>


                            <div class="mb-3">
                                <label for="">Kelas</label>
                                <select name="kelas" class="form-control">
                                    <option value="">---pilih kelas---</option>
                                    <?php 
                                    $ambil_kelas = mysqli_query($connection, "SELECT * FROM kelas ORDER BY kelas ASC");
                                    while($row = mysqli_fetch_assoc($ambil_kelas)) :?>
                                    <option value="<?=$row['kelas']?>"
                                        <?php if($kelas == $row['kelas']) {echo 'selected';}  ?>>
                                        <?=$row['kelas']?>
                                    </option>
                                    <?php endwhile?>
                                </select>
                            </div>
                            <div class=" mb-3">
                                <label for="">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">---pilih status---</option>
                                    <option <?php if($status == 'aktif'){
                                echo 'selected';
                            }?> value="aktif">
                                        Aktif
                                    </option>
                                    <option <?php if($status == 'tidak_aktif'){
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
                                <input type="text" class="form-control" name="username" value="<?= $username ?>">
                            </div>

                            <div class="mb-3">
                                <label for="">Password</label>
                                <input type="hidden" value="<?= $password ?>" name="password_lama">
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
                                    <option <?php if($role == 'admin'){
                                echo 'selected';
                            }?> value="admin">
                                        Admin
                                    </option>
                                    <option <?php if($role == 'siswa'){
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
                                    
                                    $ambil_lokasi = mysqli_query($connection, "SELECT * FROM lokasi_presensi ORDER BY nama_lokasi ASC");
                                    while($row = mysqli_fetch_assoc($ambil_lokasi)) :?>
                                    <option value="<?=$row['nama_lokasi']?>"
                                        <?php if($lokasi_presensi == $row['nama_lokasi']) {echo 'selected';}  ?>>
                                        <?=$row['nama_lokasi']?>
                                    </option>
                                    <?php endwhile?>


                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="">Foto</label>
                                <input type="hidden" value="<?= $foto ?>" name="foto_lama">
                                <input type="file" class="form-control" name="foto">
                            </div>

                            <input type="hidden" value="<?= $id ?>" name="id">

                            <button type="submit" class="btn btn-primary" name="edit">Update</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
</div>



<?php include('../layout/footer.php') ?>