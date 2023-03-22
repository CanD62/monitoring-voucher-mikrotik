<?php
include_once('config.php');
if( isset($_POST['password'])) {
    if($_POST['password'] == $token) {
        session_start();
        $_SESSION['password'] = $_POST['password'];
        echo "<center><h1 style='color:green'>Berhasil, dalam 2 detik kamu akan dialihkan ke halaman utama</h1></center>";
        echo '<meta http-equiv="refresh" content="2; url=./index.php"/>';
    } elseif($_POST['password'] != $password) {
        echo "<center><h1 style='color:red'>Gagal!, Akses Token Salah</h1></center>";
        echo '<meta http-equiv="refresh" content="2; url=./login.php"/>';
    } else{
        echo "<center><h1 style='color:red'>Gagal!, Akses Token Salah....</h1></center>";
        echo '<meta http-equiv="refresh" content="2; url=./login.php"/>';
    }
} else {
    echo "<center><h1 style='color:red'>Gagal!, Akses token kosong</h1></center>";
}