<?php
// Halaman Edit Data Master Supplier (soal no. 5)
require 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

// Ambil data supplier yang akan diedit
$stmt = mysqli_prepare($koneksi, 'SELECT id, nama, telp, alamat FROM supplier WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']);
    $telp   = trim($_POST['telp']);
    $alamat = trim($_POST['alamat']);

    // Pertahankan isian pengguna jika validasi gagal
    $data['nama']   = $nama;
    $data['telp']   = $telp;
    $data['alamat'] = $alamat;

    if ($nama === '' || $telp === '' || $alamat === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!preg_match('/^[0-9]{1,12}$/', $telp)) {
        $error = 'Telp hanya boleh berisi angka (maksimal 12 digit).';
    } else {
        $upd = mysqli_prepare($koneksi, 'UPDATE supplier SET nama = ?, telp = ?, alamat = ? WHERE id = ?');
        mysqli_stmt_bind_param($upd, 'sssi', $nama, $telp, $alamat, $id);

        if (mysqli_stmt_execute($upd)) {
            header('Location: index.php?pesan=update');
            exit;
        }
        $error = 'Gagal memperbarui data: ' . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Data Master Supplier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

    <div class="header">
        <h1 class="judul-form">Edit Data Master Supplier</h1>
    </div>

    <?php if ($error) { ?>
        <div class="alert alert-gagal"><?= htmlspecialchars($error) ?></div>
    <?php } ?>

    <form method="post" action="edit.php?id=<?= (int)$data['id'] ?>" class="form">
        <input type="hidden" name="id" value="<?= (int)$data['id'] ?>">
        <div class="baris">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" class="input-lebar"
                   maxlength="100" value="<?= htmlspecialchars($data['nama']) ?>" required>
        </div>
        <div class="baris">
            <label for="telp">Telp</label>
            <input type="text" id="telp" name="telp" class="input-sedang"
                   maxlength="12" value="<?= htmlspecialchars($data['telp']) ?>" required>
        </div>
        <div class="baris">
            <label for="alamat">Alamat</label>
            <input type="text" id="alamat" name="alamat" class="input-sedang"
                   value="<?= htmlspecialchars($data['alamat']) ?>" required>
        </div>
        <div class="baris tombol">
            <button type="submit" class="btn btn-hijau">Update</button>
            <a href="index.php" class="btn btn-merah">Batal</a>
        </div>
    </form>

</div>
</body>
</html>
