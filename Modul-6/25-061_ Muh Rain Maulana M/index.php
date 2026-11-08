<?php
require "koneksi.php";

function rupiah($angka) {
    return "Rp " . number_format((int)$angka, 0, ',', '.');
}

if (isset($_POST['tambah_detail'])) {
    $transaksi_id = (int)$_POST['transaksi_id'];
    $barang_id = (int)$_POST['barang_id'];
    $qty = (int)$_POST['qty'];

    if ($transaksi_id > 0 && $barang_id > 0 && $qty > 0) {
        // Harga disalin dari tabel barang agar harga transaksi tersimpan saat pembelian.
        $stmt = $conn->prepare("SELECT harga FROM barang WHERE id = ?");
        $stmt->bind_param("i", $barang_id);
        $stmt->execute();
        $barang = $stmt->get_result()->fetch_assoc();

        if ($barang) {
            $cek = $conn->prepare("SELECT barang_id FROM transaksi_detail WHERE transaksi_id=? AND barang_id=?");
            $cek->bind_param("ii", $transaksi_id, $barang_id);
            $cek->execute();

            if ($cek->get_result()->num_rows > 0) {
                $pesan = "Barang tersebut sudah ada pada transaksi ini.";
            } else {
                $harga = (int)$barang['harga'];
                $insert = $conn->prepare("INSERT INTO transaksi_detail (transaksi_id,barang_id,harga,qty) VALUES (?,?,?,?)");
                $insert->bind_param("iiii", $transaksi_id, $barang_id, $harga, $qty);

                if ($insert->execute()) {
                    $update = $conn->prepare("UPDATE transaksi SET total = (SELECT COALESCE(SUM(harga * qty),0) FROM transaksi_detail WHERE transaksi_id=?) WHERE id=?");
                    $update->bind_param("ii", $transaksi_id, $transaksi_id);
                    $update->execute();
                    $pesan = "Detail transaksi berhasil ditambahkan dan total diperbarui.";
                } else {
                    $pesan = "Gagal menambahkan detail: " . $conn->error;
                }
            }
        } else {
            $pesan = "Barang tidak ditemukan.";
        }
    } else {
        $pesan = "Pilih barang dan masukkan qty lebih dari 0.";
    }
}

if (isset($_POST['hapus_detail'])) {
    $transaksi_id = (int)$_POST['transaksi_id'];
    $barang_id = (int)$_POST['barang_id'];
    $stmt = $conn->prepare("DELETE FROM transaksi_detail WHERE transaksi_id=? AND barang_id=?");
    $stmt->bind_param("ii", $transaksi_id, $barang_id);
    $stmt->execute();

    $update = $conn->prepare("UPDATE transaksi SET total = (SELECT COALESCE(SUM(harga * qty),0) FROM transaksi_detail WHERE transaksi_id=?) WHERE id=?");
    $update->bind_param("ii", $transaksi_id, $transaksi_id);
    $update->execute();
    $pesan = "Detail dihapus dan total transaksi diperbarui.";
}

$pilih = isset($_GET['transaksi']) ? (int)$_GET['transaksi'] : 1;
$transaksi = $conn->query("SELECT t.*, p.nama AS nama_pelanggan FROM transaksi t LEFT JOIN pelanggan p ON t.pelanggan_id=p.id ORDER BY t.id");
$master = $conn->prepare("SELECT t.*, p.nama AS nama_pelanggan FROM transaksi t LEFT JOIN pelanggan p ON t.pelanggan_id=p.id WHERE t.id=?");
$master->bind_param("i", $pilih);
$master->execute();
$data_master = $master->get_result()->fetch_assoc();

$detail = $conn->prepare("SELECT td.*, b.kode_barang, b.nama_barang FROM transaksi_detail td JOIN barang b ON td.barang_id=b.id WHERE td.transaksi_id=? ORDER BY b.nama_barang");
$detail->bind_param("i", $pilih);
$detail->execute();
$data_detail = $detail->get_result();

$barang = $conn->prepare("SELECT id,kode_barang,nama_barang,harga,stok FROM barang WHERE id NOT IN (SELECT barang_id FROM transaksi_detail WHERE transaksi_id=?) ORDER BY nama_barang");
$barang->bind_param("i", $pilih);
$barang->execute();
$data_barang = $barang->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Master Detail Penjualan</title>
<style>
*{box-sizing:border-box} body{font-family:Arial,sans-serif;background:#f3f5f7;margin:0;color:#222}
.container{max-width:1100px;margin:30px auto;padding:0 16px}
h1{color:#1f7e3e} h2{margin-top:0;font-size:20px}
.card{background:white;padding:20px;border-radius:8px;margin-bottom:20px;box-shadow:0 2px 8px #00000012}
table{width:100%;border-collapse:collapse;margin-top:12px} th,td{border:1px solid #ddd;padding:10px;text-align:left}
th{background:#1f7e3e;color:white} tr:nth-child(even){background:#f8f8f8}
a,button{display:inline-block;background:#1f7e3e;color:white;padding:8px 12px;border:0;border-radius:4px;text-decoration:none;cursor:pointer}
a:hover,button:hover{background:#17612e}.active{background:#14532d;font-weight:bold}
input,select{padding:9px;border:1px solid #bbb;border-radius:4px;max-width:100%}
.form-row{display:flex;gap:10px;flex-wrap:wrap;align-items:end}.form-row label{display:block;margin-bottom:5px}
.danger{background:#c0392b}.danger:hover{background:#962d22}.notice{padding:12px;background:#e8f5e9;border-left:4px solid #1f7e3e;margin-bottom:16px}
.small{font-size:13px;color:#555}.total{font-size:22px;color:#1f7e3e;font-weight:bold}
</style>
</head>
<body>
<div class="container">
<h1>Master Detail Penjualan</h1>
<p class="small">Pilih transaksi pada tabel master untuk melihat barang pada tabel detail.</p>

<?php if (!empty($pesan)): ?><div class="notice"><?= htmlspecialchars($pesan) ?></div><?php endif; ?>

<div class="card">
<h2>Data Master - Transaksi</h2>
<table>
<tr><th>ID</th><th>Tanggal</th><th>Pelanggan</th><th>Keterangan</th><th>Total</th><th>Aksi</th></tr>
<?php while($t = $transaksi->fetch_assoc()): ?>
<tr>
<td><?= $t['id'] ?></td><td><?= htmlspecialchars($t['waktu_transaksi']) ?></td>
<td><?= htmlspecialchars($t['nama_pelanggan'] ?? '-') ?></td>
<td><?= htmlspecialchars($t['keterangan'] ?? '') ?></td><td><?= rupiah($t['total']) ?></td>
<td><a class="<?= $pilih===(int)$t['id']?'active':'' ?>" href="index.php?transaksi=<?= $t['id'] ?>">Pilih</a></td>
</tr>
<?php endwhile; ?>
</table>
</div>

<?php if ($data_master): ?>
<div class="card">
<h2>Detail Transaksi #<?= $data_master['id'] ?></h2>
<p><b>Pelanggan:</b> <?= htmlspecialchars($data_master['nama_pelanggan'] ?? '-') ?> |
<b>Tanggal:</b> <?= htmlspecialchars($data_master['waktu_transaksi']) ?></p>
<p class="total">Total: <?= rupiah($data_master['total']) ?></p>

<h3>Daftar Barang</h3>
<table>
<tr><th>Kode</th><th>Nama Barang</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th>Aksi</th></tr>
<?php while($d = $data_detail->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($d['kode_barang']) ?></td>
<td><?= htmlspecialchars($d['nama_barang']) ?></td>
<td><?= rupiah($d['harga']) ?></td><td><?= $d['qty'] ?></td>
<td><?= rupiah($d['harga']*$d['qty']) ?></td>
<td>
<form method="post" onsubmit="return confirm('Hapus detail ini?')">
<input type="hidden" name="transaksi_id" value="<?= $pilih ?>">
<input type="hidden" name="barang_id" value="<?= $d['barang_id'] ?>">
<button class="danger" name="hapus_detail">Hapus</button>
</form>
</td>
</tr>
<?php endwhile; ?>
</table>

<h3>Tambah Detail Transaksi</h3>
<form method="post" class="form-row">
<input type="hidden" name="transaksi_id" value="<?= $pilih ?>">
<div><label>Barang</label>
<select name="barang_id" required>
<option value="">-- Pilih Barang --</option>
<?php while($b = $data_barang->fetch_assoc()): ?>
<option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['kode_barang']." - ".$b['nama_barang']." (".rupiah($b['harga']).")") ?></option>
<?php endwhile; ?>
</select></div>
<div><label>Qty</label><input type="number" name="qty" min="1" value="1" required></div>
<div><button name="tambah_detail">Tambah Detail</button></div>
</form>
<p class="small">Barang yang sudah digunakan pada transaksi ini tidak ditampilkan lagi di dropdown.</p>
</div>
<?php else: ?>
<div class="card">Data transaksi tidak ditemukan.</div>
<?php endif; ?>

<div class="card">
<h2>Data Barang</h2>
<p class="small">Barang yang sudah dipakai pada transaksi tidak dapat dihapus.</p>
<table>
<tr><th>Kode</th><th>Nama Barang</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
<?php
$daftar_barang = $conn->query("SELECT * FROM barang ORDER BY id");
while($br = $daftar_barang->fetch_assoc()):
?>
<tr>
<td><?= htmlspecialchars($br['kode_barang']) ?></td><td><?= htmlspecialchars($br['nama_barang']) ?></td>
<td><?= rupiah($br['harga']) ?></td><td><?= $br['stok'] ?></td>
<td><a class="danger" href="hapus_barang.php?id=<?= $br['id'] ?>" onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</a></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</div>
</body>
</html>
