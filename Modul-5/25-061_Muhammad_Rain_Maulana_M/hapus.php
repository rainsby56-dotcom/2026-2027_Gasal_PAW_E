<?php
// Proses hapus data supplier (soal no. 6)
// Popup konfirmasi "Anda yakin akan menghapus supplier ini?" ada di index.php (confirm JavaScript)
require 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Supplier yang masih dipakai tabel barang tidak boleh dihapus (foreign key)
    $cek = mysqli_prepare($koneksi, 'SELECT COUNT(*) FROM barang WHERE supplier_id = ?');
    mysqli_stmt_bind_param($cek, 'i', $id);
    mysqli_stmt_execute($cek);
    mysqli_stmt_bind_result($cek, $jumlah);
    mysqli_stmt_fetch($cek);
    mysqli_stmt_close($cek);

    if ($jumlah > 0) {
        header('Location: index.php?pesan=dipakai');
        exit;
    }

    $stmt = mysqli_prepare($koneksi, 'DELETE FROM supplier WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
}

header('Location: index.php?pesan=hapus');
exit;
