<?php
session_start();
require_once('../../siswa/config.php');

$id = $_GET['id'];

$result = mysqli_query($connection, "DELETE FROM ketidakhadiran WHERE id=$id");

$_SESSION['berhasil'] = 'Data berhasil di hapus';
header("Location: ketidakhadiran.php");
exit;



?>