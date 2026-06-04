<?php
require "auth.php";
require "../koneksi.php";

$id = $_GET['id'];

$cekPesanan = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE idPelanggan='$id'");

if (mysqli_num_rows($cekPesanan) > 0) {
    echo "
    <script>
        alert('Pelanggan tidak bisa dihapus karena sudah memiliki pesanan.');
        window.location.href='pelanggan.php';
    </script>";
    exit;
}

$query = mysqli_query($koneksi, "SELECT idUser FROM pelanggan WHERE idPelanggan='$id'");
$data = mysqli_fetch_assoc($query);

if ($data) {
    $idUser = $data['idUser'];

    mysqli_query($koneksi, "DELETE FROM pelanggan WHERE idPelanggan='$id'");
    mysqli_query($koneksi, "DELETE FROM user WHERE idUser='$idUser'");
}

echo "
<script>
    alert('Data pelanggan berhasil dihapus.');
    window.location.href='pelanggan.php';
</script>";
exit;
?>