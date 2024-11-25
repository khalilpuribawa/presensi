<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"
    integrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<!-- Make sure you put this AFTER Leaflet's CSS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
#map {
    height: 300px;
}
</style>
<?php 
ob_start();
session_start();
if(!isset($_SESSION["login"])){
    header("Location:../../auth/login.php?pesan=belum_login");
    exit;
}else if($_SESSION["role"] != 'siswa'){
    header("Location:../../auth/login.php?pesan=tolak_akses");
    exit;
}

$judul = 'Presensi Masuk';
include('../layout/header.php');
include_once("../../siswa/config.php");



if(isset($_POST["tombol_masuk"])){
    $latitude_siswa = $_POST['latitude_siswa'];
    $longtitude_siswa = $_POST['longtitude_siswa'];
    $latitude_sekolah = $_POST['latitude'];
    $longtitude_sekolah = $_POST['longtitude'];
    $radius = $_POST['radius'];
    $zona_waktu = $_POST['zona_waktu'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $jam_masuk = $_POST['jam_masuk'];    
}
if(empty($latitude_siswa) || empty($longtitude_siswa)){
    $_SESSION['gagal'] = 'Presensi Gagal, Lokasi anda belum di izinkan/aktif';
    header("Location: ../home/home.php");
    exit;
}

if(empty($latitude_sekolah) || empty($longtitude_sekolah)){
    $_SESSION['gagal'] = 'Presensi Gagal, Koordinat kantor belum di setting';
    header("Location: ../home/home.php");
    exit;
}

$perbedaan_koordinat = $longtitude_sekolah - $longtitude_sekolah;
$jarak = sin(deg2rad($latitude_siswa)) * sin(deg2rad($latitude_sekolah)) + cos(deg2rad($latitude_siswa)) * cos(deg2rad($latitude_sekolah)) * cos(deg2rad($perbedaan_koordinat));
$jarak = acos($jarak);
$jarak = rad2deg($jarak);
$mil = $jarak * 60 * 1.1515;
$jarak_km = $mil * 1.60934;
$jarak_meter = $jarak_km * 1000;

?>

<?php if($jarak_meter > $radius){ ?>
<?= $_SESSION['gagal'] = 'Anda berada diluar area sekolah';
    header("Location: ../home/home.php");
    exit;
    ?>
<?php } else { ?>
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div id="map">

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body" style="margin:auto;">
                        <input type="hidden" id="id" value="<?= $_SESSION['id'] ?>">
                        <input type="hidden" id="tanggal_masuk" value="<?= $tanggal_masuk ?>">
                        <input type="hidden" id="jam_masuk" value="<?= $jam_masuk ?>">

                        <div id="my_camera"></div>
                        <div id="my_result"></div>
                        <div><?= date('d F Y',strtotime($tanggal_masuk)) . ' - ' . $jam_masuk ?></div>
                        <button class="btn btn-primary mt-3" id="ambil-foto">Masuk</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script language="JavaScript">
Webcam.set({
    width: 320,
    height: 240,
    dest_width: 320,
    dest_height: 240,
    image_format: 'jpeg',
    jpeg_quality: 90,
    force_flash: false
});

// Attach camera here
Webcam.attach('#my_camera');

document.getElementById('ambil-foto').addEventListener('click', function() {
    let id = document.getElementById('id').value;
    let tanggal_masuk = document.getElementById('tanggal_masuk').value;
    let jam_masuk = document.getElementById('jam_masuk').value;
    Webcam.snap(function(data_uri) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            document.getElementById('my_result').innerHTML = '<img src="' + data_uri + '"/>';
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                window.location.href = '../home/home.php';
            }
        };
        xhttp.open("POST", "presensi_masuk_aksi.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(
            'photo=' + encodeURIComponent(data_uri) +
            '&id=' + id +
            '&tanggal_masuk=' + tanggal_masuk +
            '&jam_masuk=' + jam_masuk
        );
    });
});
// map leaftlet js
let latitude_school = <?= $latitude_sekolah  ?>;
let longtitude_school = <?= $longtitude_sekolah  ?>;

let latitude_pelajar = <?= $latitude_siswa  ?>;
let longtitude_pelajar = <?= $longtitude_siswa  ?>;


let map = L.map('map').setView([latitude_school, longtitude_school], 13);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);
var marker = L.marker([latitude_school, longtitude_school]).addTo(map);

var circle = L.circle([latitude_pelajar, longtitude_pelajar], {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 0.5,
    radius: 500
}).addTo(map).bindPopup("Lokasi anda saat ini").openPopup();
</script>

<?php } ?>

<?php include('../layout/footer.php') ?>