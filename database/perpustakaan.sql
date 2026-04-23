-- =============================================
-- DATABASE: perpustakaan_sekolah
-- Aplikasi Peminjaman Buku Digital
-- UKK RPL 2025/2026
-- =============================================

CREATE DATABASE IF NOT EXISTS `perpustakaan_sekolah`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `perpustakaan_sekolah`;

-- ----------------------------
-- Table: users (admin & siswa)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','siswa') NOT NULL DEFAULT 'siswa',
  `nis` VARCHAR(20) NULL COMMENT 'Nomor Induk Siswa (khusus siswa)',
  `kelas` VARCHAR(20) NULL,
  `no_hp` VARCHAR(20) NULL,
  `alamat` TEXT NULL,
  `foto` VARCHAR(255) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_role` (`role`),
  INDEX `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table: kategoris
-- ----------------------------
CREATE TABLE IF NOT EXISTS `kategoris` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `keterangan` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table: bukus
-- ----------------------------
CREATE TABLE IF NOT EXISTS `bukus` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_buku` VARCHAR(30) NOT NULL UNIQUE,
  `judul` VARCHAR(200) NOT NULL,
  `pengarang` VARCHAR(100) NOT NULL,
  `penerbit` VARCHAR(100) NOT NULL,
  `tahun_terbit` YEAR NOT NULL,
  `kategori_id` BIGINT UNSIGNED NOT NULL,
  `stok` INT NOT NULL DEFAULT 1,
  `stok_tersedia` INT NOT NULL DEFAULT 1,
  `isbn` VARCHAR(30) NULL,
  `deskripsi` TEXT NULL,
  `sampul` VARCHAR(255) NULL,
  `rak` VARCHAR(20) NULL COMMENT 'Lokasi rak buku',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_kode_buku` (`kode_buku`),
  CONSTRAINT `fk_buku_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategoris` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table: pinjams (transaksi peminjaman)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `pinjams` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_pinjam` VARCHAR(30) NOT NULL UNIQUE,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `buku_id` BIGINT UNSIGNED NOT NULL,
  `tgl_pinjam` DATE NOT NULL,
  `tgl_kembali_rencana` DATE NOT NULL,
  `tgl_kembali_aktual` DATE NULL,
  `status` ENUM('dipinjam','dikembalikan','terlambat') NOT NULL DEFAULT 'dipinjam',
  `denda` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `keterangan` TEXT NULL,
  `admin_id` BIGINT UNSIGNED NULL COMMENT 'Admin yang memproses',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_buku` (`buku_id`),
  INDEX `idx_status` (`status`),
  CONSTRAINT `fk_pinjam_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_pinjam_buku` FOREIGN KEY (`buku_id`) REFERENCES `bukus` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_pinjam_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- SEED DATA
-- ----------------------------

-- Admin default
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role`) VALUES
('Administrator', 'admin', 'admin@perpustakaan.sch.id', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- password: password

-- Siswa contoh
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role`, `nis`, `kelas`, `no_hp`) VALUES
('Budi Santoso', 'budi.santoso', 'budi@siswa.sch.id', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'siswa', '2024001', 'XII RPL 1', '081234567890'),
('Siti Rahayu', 'siti.rahayu', 'siti@siswa.sch.id', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'siswa', '2024002', 'XII RPL 2', '081234567891');

-- Kategori buku
INSERT INTO `kategoris` (`nama_kategori`, `keterangan`) VALUES
('Pemrograman', 'Buku-buku tentang pemrograman dan coding'),
('Matematika', 'Buku pelajaran matematika'),
('Bahasa Indonesia', 'Buku bahasa dan sastra Indonesia'),
('IPA', 'Ilmu Pengetahuan Alam'),
('IPS', 'Ilmu Pengetahuan Sosial'),
('Fiksi', 'Novel dan karya fiksi'),
('Referensi', 'Kamus, ensiklopedia, dan referensi');

-- Data buku contoh
INSERT INTO `bukus` (`kode_buku`, `judul`, `pengarang`, `penerbit`, `tahun_terbit`, `kategori_id`, `stok`, `stok_tersedia`, `isbn`, `rak`) VALUES
('BK001', 'Pemrograman Web dengan PHP & MySQL', 'Agus Saputra', 'Informatika', 2023, 1, 3, 3, '978-602-0000-01-1', 'A1'),
('BK002', 'Laravel: Framework PHP Modern', 'Ahmad Fauzi', 'Andi Publisher', 2023, 1, 2, 2, '978-602-0000-02-2', 'A2'),
('BK003', 'Matematika Kelas XII', 'Sukino', 'Erlangga', 2022, 2, 5, 5, '978-602-0000-03-3', 'B1'),
('BK004', 'Bahasa Indonesia untuk SMA/SMK', 'Kemdikbud', 'Kemendikbud', 2023, 3, 4, 4, '978-602-0000-04-4', 'C1'),
('BK005', 'Fisika Terapan SMK', 'Haris Suprapto', 'Yudhistira', 2022, 4, 3, 3, '978-602-0000-05-5', 'D1'),
('BK006', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 6, 2, 2, '978-602-0000-06-6', 'F1'),
('BK007', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Lentera Dipantara', 2005, 6, 2, 2, '978-602-0000-07-7', 'F2'),
('BK008', 'Kamus Besar Bahasa Indonesia', 'Tim Redaksi KBBI', 'Balai Pustaka', 2022, 7, 1, 1, '978-602-0000-08-8', 'G1');

-- ----------------------------
-- Table: sessions
-- ----------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table: cache
-- ----------------------------
CREATE TABLE IF NOT EXISTS `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
