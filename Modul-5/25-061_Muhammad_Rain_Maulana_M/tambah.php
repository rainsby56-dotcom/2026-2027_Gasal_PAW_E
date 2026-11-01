<?php
// Halaman Tambah Data Master Supplier Baru (soal no. 4)
require 'koneksi.php';

$error  = '';
$nama   = '';
$telp   = '';
$alamat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']);
    $telp   = trim($_POST['telp']);
    $alamat = trim($_POST['alamat']);

    if ($nama === '' || $telp === '' || $alamat === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!preg_match('/^[0-9]{1,12}$/', $telp)) {
        $error = 'Telp hanya boleh berisi angka (maksimal 12 digit).';
    } else {
        $stmt = mysqli_prepare($koneksi, 'INSERT INTO supplier (nama, telp, alamat) VALUES (?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sss', $nama, $telp, $alamat);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php?pesan=tambah');
            exit;
        }
        $error = 'Gagal menyimpan data: ' . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Data Master Supplier Baru</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

    <div class="header">
        <h1 class="judul-form">Tambah Data Master Supplier Baru</h1>
    </div>

    <?php if ($error) { ?>
        <div class="alert alert-gagal"><?= htmlspecialchars($error) ?></div>
    <?php } ?>

    <form method="post" action="tambah.php" class="form">
        <div class="baris">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" class="input-lebar" placeholder="Nama"
                   maxlength="100" value="<?= htmlspecialchars($nama) ?>" required>
        </div>
        <div class="baris">
            <label for="telp">Telp</label>
            <input type="text" id="telp" name="telp" class="input-sedang" placeholder="telp"
                   maxlength="12" value="<?= htmlspecialchars($telp) ?>" required>
        </div>
        <div class="baris">
            <label for="alamat">Alamat</label>
            <input type="text" id="alamat" name="alamat" class="input-sedang" placeholder="alamat"
                   value="<?= htmlspecialchars($alamat) ?>" required>
        </div>
        <div class="baris tombol">
            <button type="submit" class="btn btn-hijau">Simpan</button>
            <a href="index.php" class="btn btn-merah">Batal</a>
        </div>
    </form>

</div>
</body>
</html>
