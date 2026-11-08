<?php
require "koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$cek = $conn->prepare("SELECT COUNT(*) AS jumlah FROM transaksi_detail WHERE barang_id=?");
$cek->bind_param("i", $id);
$cek->execute();
$hasil = $cek->get_result()->fetch_assoc();

if ((int)$hasil['jumlah'] > 0) {
    echo "<script>alert('Barang tidak dapat dihapus karena masih digunakan pada transaksi.');window.location='index.php';</script>";
    exit;
}

$hapus = $conn->prepare("DELETE FROM barang WHERE id=?");
$hapus->bind_param("i", $id);

if ($hapus->execute()) {
    echo "<script>alert('Data barang berhasil dihapus.');window.location='index.php';</script>";
} else {
    echo "<script>alert('Data barang gagal dihapus.');window.location='index.php';</script>";
}
?>