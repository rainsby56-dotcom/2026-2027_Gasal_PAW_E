<?php
// Halaman Data Master Supplier (soal no. 3): menampilkan data dari database
require 'koneksi.php';

$hasil = mysqli_query($koneksi, 'SELECT id, nama, telp, alamat FROM supplier ORDER BY id ASC');
$pesan = isset($_GET['pesan']) ? $_GET['pesan'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Master Supplier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

    <div class="header">
        <h1>Data Master Supplier</h1>
        <a href="tambah.php" class="btn btn-hijau">Tambah Data</a>
    </div>

    <?php if ($pesan == 'tambah') { ?>
        <div class="alert alert-sukses">Data supplier berhasil ditambahkan.</div>
    <?php } elseif ($pesan == 'update') { ?>
        <div class="alert alert-sukses">Data supplier berhasil diperbarui.</div>
    <?php } elseif ($pesan == 'hapus') { ?>
        <div class="alert alert-sukses">Data supplier berhasil dihapus.</div>
    <?php } elseif ($pesan == 'dipakai') { ?>
        <div class="alert alert-gagal">Supplier tidak bisa dihapus karena masih dipakai oleh data barang.</div>
    <?php } ?>

    <table>
        <thead>
            <tr>
                <th width="50">No</th>
                <th>Nama</th>
                <th>Telp</th>
                <th>Alamat</th>
                <th width="170">Tindakan</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        if (mysqli_num_rows($hasil) > 0) {
            while ($row = mysqli_fetch_assoc($hasil)) { ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['telp']) ?></td>
                <td><?= htmlspecialchars($row['alamat']) ?></td>
                <td>
                    <a href="edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-oranye">Edit</a>
                    <!-- Soal no. 6: popup konfirmasi sebelum data dihapus -->
                    <a href="hapus.php?id=<?= (int)$row['id'] ?>" class="btn btn-merah"
                       onclick="return confirm('Anda yakin akan menghapus supplier ini?')">Hapus</a>
                </td>
            </tr>
        <?php }
        } else { ?>
            <tr><td colspan="5" class="kosong">Belum ada data supplier.</td></tr>
        <?php } ?>
        </tbody>
    </table>

</div>
</body>
</html>
