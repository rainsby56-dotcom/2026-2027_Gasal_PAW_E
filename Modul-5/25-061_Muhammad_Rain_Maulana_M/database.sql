-- ============================================================
-- MODULE 5 : DATABASE - Skema database "penjualan"
-- Import lewat phpMyAdmin (tab Import) atau: mysql -u root < database.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS penjualan
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE penjualan;

-- Hapus tabel lama (urutan dari tabel anak ke tabel induk)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS pembayaran;
DROP TABLE IF EXISTS transaksi_detail;
DROP TABLE IF EXISTS transaksi;
DROP TABLE IF EXISTS barang;
DROP TABLE IF EXISTS supplier;
DROP TABLE IF EXISTS pelanggan;
DROP TABLE IF EXISTS user;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- 1. Tabel pelanggan
-- ------------------------------------------------------------
CREATE TABLE pelanggan (
  id            VARCHAR(20)  NOT NULL,
  nama          VARCHAR(100) NOT NULL,
  jenis_kelamin ENUM('L','P') NOT NULL,
  telp          VARCHAR(12)  NOT NULL,
  alamat        TEXT         NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 2. Tabel supplier
-- ------------------------------------------------------------
CREATE TABLE supplier (
  id     INT(11)      NOT NULL AUTO_INCREMENT,
  nama   VARCHAR(100) NOT NULL,
  telp   VARCHAR(12)  NOT NULL,
  alamat TEXT         NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 3. Tabel barang
-- ------------------------------------------------------------
CREATE TABLE barang (
  id          INT(11)      NOT NULL AUTO_INCREMENT,
  kode_barang VARCHAR(10)  NOT NULL,
  nama_barang VARCHAR(100) NOT NULL,
  harga       INT(11)      NOT NULL,
  stok        INT(11)      NOT NULL,
  supplier_id INT(11)      NOT NULL,
  PRIMARY KEY (id),
  KEY fk_barang_supplier (supplier_id),
  CONSTRAINT fk_barang_supplier FOREIGN KEY (supplier_id)
    REFERENCES supplier (id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 4. Tabel transaksi
-- ------------------------------------------------------------
CREATE TABLE transaksi (
  id              INT(11)     NOT NULL AUTO_INCREMENT,
  waktu_transaksi DATE        NOT NULL,
  keterangan      TEXT,
  total           INT(11)     NOT NULL,
  pelanggan_id    VARCHAR(20) NOT NULL,
  PRIMARY KEY (id),
  KEY fk_transaksi_pelanggan (pelanggan_id),
  CONSTRAINT fk_transaksi_pelanggan FOREIGN KEY (pelanggan_id)
    REFERENCES pelanggan (id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 5. Tabel transaksi_detail (composite primary key)
-- ------------------------------------------------------------
CREATE TABLE transaksi_detail (
  transaksi_id INT(11) NOT NULL,
  barang_id    INT(11) NOT NULL,
  harga        INT(11) NOT NULL,
  qty          INT(11) NOT NULL,
  PRIMARY KEY (transaksi_id, barang_id),
  KEY fk_detail_barang (barang_id),
  CONSTRAINT fk_detail_transaksi FOREIGN KEY (transaksi_id)
    REFERENCES transaksi (id) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_detail_barang FOREIGN KEY (barang_id)
    REFERENCES barang (id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 6. Tabel pembayaran
-- ------------------------------------------------------------
CREATE TABLE pembayaran (
  id           INT(11)  NOT NULL AUTO_INCREMENT,
  waktu_bayar  DATETIME NOT NULL,
  total        INT(11)  NOT NULL,
  metode       ENUM('TUNAI','TRANSFER','EDC') NOT NULL,
  transaksi_id INT(11)  NOT NULL,
  PRIMARY KEY (id),
  KEY fk_pembayaran_transaksi (transaksi_id),
  CONSTRAINT fk_pembayaran_transaksi FOREIGN KEY (transaksi_id)
    REFERENCES transaksi (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 7. Tabel user
-- ------------------------------------------------------------
CREATE TABLE user (
  id_user  TINYINT(2)   NOT NULL AUTO_INCREMENT,
  username VARCHAR(30)  NOT NULL,
  password VARCHAR(35)  NOT NULL,
  nama     VARCHAR(50)  NOT NULL,
  alamat   VARCHAR(150) NOT NULL,
  hp       VARCHAR(20)  NOT NULL,
  level    TINYINT(1)   NOT NULL,
  PRIMARY KEY (id_user)
) ENGINE=InnoDB;

-- ============================================================
-- DATA (minimal 10 baris per tabel)
-- ============================================================

-- supplier (3 baris pertama sama dengan contoh di soal)
INSERT INTO supplier (id, nama, telp, alamat) VALUES
(1,  'PT. Maju Bersama',   '08123456',     'Surabaya'),
(2,  'PT. Senang Sekali',  '081515563',    'Bangkalan'),
(3,  'PT. Segar Segar',    '0845454663',   'Surabaya'),
(4,  'CV. Sumber Rejeki',  '081234567801', 'Sidoarjo'),
(5,  'PT. Jaya Abadi',     '082134567802', 'Gresik'),
(6,  'CV. Mitra Sejahtera','085234567803', 'Malang'),
(7,  'PT. Indo Pangan',    '081334567804', 'Mojokerto'),
(8,  'PT. Karya Mandiri',  '087834567805', 'Surabaya'),
(9,  'CV. Berkah Utama',   '081934567806', 'Lamongan'),
(10, 'PT. Sinar Makmur',   '085734567807', 'Pasuruan');

-- barang
INSERT INTO barang (id, kode_barang, nama_barang, harga, stok, supplier_id) VALUES
(1,  'BRG001', 'Beras Premium 5kg',  65000, 100, 1),
(2,  'BRG002', 'Minyak Goreng 2L',   38000,  80, 2),
(3,  'BRG003', 'Gula Pasir 1kg',     16000, 120, 3),
(4,  'BRG004', 'Telur Ayam 1kg',     28000,  60, 4),
(5,  'BRG005', 'Tepung Terigu 1kg',  12000,  90, 5),
(6,  'BRG006', 'Kopi Bubuk 200g',    22000,  70, 6),
(7,  'BRG007', 'Teh Celup 25s',       9000, 150, 7),
(8,  'BRG008', 'Susu UHT 1L',        18000,  85, 8),
(9,  'BRG009', 'Mie Instan 5pcs',    15000, 200, 9),
(10, 'BRG010', 'Sabun Mandi 3pcs',   12000, 110, 10);

-- pelanggan
INSERT INTO pelanggan (id, nama, jenis_kelamin, telp, alamat) VALUES
('P001', 'Budi Santoso',    'L', '081211110001', 'Jl. Mawar No. 1, Surabaya'),
('P002', 'Siti Aminah',     'P', '081211110002', 'Jl. Melati No. 2, Sidoarjo'),
('P003', 'Agus Prasetyo',   'L', '081211110003', 'Jl. Kenanga No. 3, Gresik'),
('P004', 'Dewi Lestari',    'P', '081211110004', 'Jl. Anggrek No. 4, Surabaya'),
('P005', 'Rudi Hartono',    'L', '081211110005', 'Jl. Dahlia No. 5, Malang'),
('P006', 'Rina Wulandari',  'P', '081211110006', 'Jl. Flamboyan No. 6, Mojokerto'),
('P007', 'Joko Widodo',     'L', '081211110007', 'Jl. Cempaka No. 7, Surabaya'),
('P008', 'Maya Sari',       'P', '081211110008', 'Jl. Teratai No. 8, Lamongan'),
('P009', 'Eko Nugroho',     'L', '081211110009', 'Jl. Seruni No. 9, Pasuruan'),
('P010', 'Lina Marlina',    'P', '081211110010', 'Jl. Kamboja No. 10, Bangkalan');

-- transaksi (total = jumlah harga x qty dari transaksi_detail)
INSERT INTO transaksi (id, waktu_transaksi, keterangan, total, pelanggan_id) VALUES
(1,  '2026-10-01', 'Belanja kebutuhan dapur',     97000,  'P001'),
(2,  '2026-10-03', 'Belanja bulanan',            121000,  'P002'),
(3,  '2026-10-05', 'Bahan kue',                   52000,  'P003'),
(4,  '2026-10-07', 'Stok warung',                 71000,  'P004'),
(5,  '2026-10-09', 'Pembelian susu',              72000,  'P005'),
(6,  '2026-10-12', 'Belanja bulanan',            168000,  'P006'),
(7,  '2026-10-15', 'Kebutuhan rumah tangga',      99000,  'P007'),
(8,  '2026-10-18', 'Belanja mingguan',            78000,  'P008'),
(9,  '2026-10-21', 'Telur dan kopi',              78000,  'P009'),
(10, '2026-10-25', 'Belanja campuran',           110000,  'P010');

-- transaksi_detail
INSERT INTO transaksi_detail (transaksi_id, barang_id, harga, qty) VALUES
(1,  1,  65000, 1),
(1,  3,  16000, 2),
(2,  2,  38000, 2),
(2,  9,  15000, 3),
(3,  4,  28000, 1),
(3,  5,  12000, 2),
(4,  6,  22000, 2),
(4,  7,   9000, 3),
(5,  8,  18000, 4),
(6,  1,  65000, 2),
(6,  2,  38000, 1),
(7,  9,  15000, 5),
(7,  10, 12000, 2),
(8,  3,  16000, 3),
(8,  5,  12000, 1),
(8,  7,   9000, 2),
(9,  4,  28000, 2),
(9,  6,  22000, 1),
(10, 10, 12000, 3),
(10, 8,  18000, 2),
(10, 2,  38000, 1);

-- pembayaran
INSERT INTO pembayaran (id, waktu_bayar, total, metode, transaksi_id) VALUES
(1,  '2026-10-01 09:15:00',  97000, 'TUNAI',    1),
(2,  '2026-10-03 10:30:00', 121000, 'TRANSFER', 2),
(3,  '2026-10-05 11:05:00',  52000, 'TUNAI',    3),
(4,  '2026-10-07 13:20:00',  71000, 'EDC',      4),
(5,  '2026-10-09 14:45:00',  72000, 'TUNAI',    5),
(6,  '2026-10-12 08:50:00', 168000, 'TRANSFER', 6),
(7,  '2026-10-15 16:10:00',  99000, 'EDC',      7),
(8,  '2026-10-18 12:00:00',  78000, 'TUNAI',    8),
(9,  '2026-10-21 15:35:00',  78000, 'TRANSFER', 9),
(10, '2026-10-25 17:25:00', 110000, 'EDC',     10);

-- user (password disimpan dalam bentuk hash MD5, 32 karakter muat di varchar(35))
-- level: 1 = admin, 2 = kasir, 3 = gudang
INSERT INTO user (id_user, username, password, nama, alamat, hp, level) VALUES
(1,  'admin',   MD5('admin123'),  'Administrator',  'Jl. Raya Darmo No. 1, Surabaya',     '081300000001', 1),
(2,  'kasir1',  MD5('kasir123'),  'Andi Kurniawan', 'Jl. Ngagel No. 12, Surabaya',        '081300000002', 2),
(3,  'kasir2',  MD5('kasir123'),  'Bella Anjani',   'Jl. Rungkut No. 8, Surabaya',        '081300000003', 2),
(4,  'gudang1', MD5('gudang123'), 'Candra Wijaya',  'Jl. Wonokromo No. 5, Surabaya',      '081300000004', 3),
(5,  'gudang2', MD5('gudang123'), 'Dina Fitriani',  'Jl. Gubeng No. 20, Surabaya',        '081300000005', 3),
(6,  'kasir3',  MD5('kasir123'),  'Eko Setiawan',   'Jl. Tenggilis No. 3, Surabaya',      '081300000006', 2),
(7,  'kasir4',  MD5('kasir123'),  'Fajar Ramadhan', 'Jl. Gayungan No. 7, Surabaya',       '081300000007', 2),
(8,  'gudang3', MD5('gudang123'), 'Gita Permata',   'Jl. Mulyorejo No. 15, Surabaya',     '081300000008', 3),
(9,  'admin2',  MD5('admin123'),  'Hendra Saputra', 'Jl. Kenjeran No. 9, Surabaya',       '081300000009', 1),
(10, 'kasir5',  MD5('kasir123'),  'Intan Permatasari','Jl. Sukolilo No. 11, Surabaya',    '081300000010', 2);
