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

$judul = 'Presensi Keluar';
include('../layout/header.php');
include_once("../../siswa/config.php");

if(isset($_POST["tombol-keluar"])){
    $id = $_POST['id'];
    $latitude_siswa = $_POST['latitude_siswa'];
    $longtitude_siswa = $_POST['longtitude_siswa'];
    $latitude_sekolah = $_POST['latitude'];
    $longtitude_sekolah = $_POST['longtitude'];
    $radius = $_POST['radius'];
    $zona_waktu = $_POST['zona_waktu'];
    $tanggal_keluar = $_POST['tanggal_keluar'];
    $jam_keluar = $_POST['jam_keluar'];    
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
                        <input type="hidden" id="id" value="<?= $id ?>">
                        <input type="hidden" id="tanggal_keluar" value="<?= $tanggal_keluar ?>">
                        <input type="hidden" id="jam_keluar" value="<?= $jam_keluar ?>">

                        <div id="my_camera"></div>
                        <div id="my_result"></div>
                        <div><?= date('d F Y',strtotime($tanggal_keluar)) . ' - ' . $jam_keluar ?></div>
                        <button class="btn btn-danger  mt-3" id="ambil-foto">Keluar</button>
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
    let tanggal_keluar = document.getElementById('tanggal_keluar').value;
    let jam_keluar = document.getElementById('jam_keluar').value;
    Webcam.snap(function(data_uri) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            document.getElementById('my_result').innerHTML = '<img src="' + data_uri + '"/>';
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                window.location.href = '../home/home.php';
            }
        };
        xhttp.open("POST", "presensi_keluar_aksi.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(
            'photo=' + encodeURIComponent(data_uri) +
            '&id=' + id +
            '&tanggal_keluar=' + tanggal_keluar +
            '&jam_keluar=' + jam_keluar
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