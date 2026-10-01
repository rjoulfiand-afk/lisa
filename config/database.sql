CREATE DATABASE IF NOT EXISTS `sasparkir`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `sasparkir`;

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
    `username`   VARCHAR(50)  NOT NULL,
    `password`   VARCHAR(255) NOT NULL,
    `nama`       VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admin` (`username`, `password`, `nama`) VALUES
('admin', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGNTyOmCrJw2HQcAS', 'Petugas Parkir');

DROP TABLE IF EXISTS `parkir`;
CREATE TABLE `parkir` (
    `id`              INT(11)                  NOT NULL AUTO_INCREMENT,
    `nomor_id`        VARCHAR(20)              NOT NULL COMMENT 'Kode unik transaksi parkir',
    `nomor_plat`      VARCHAR(15)              NOT NULL COMMENT 'Nomor plat kendaraan (Contoh: L 1234 AB)',
    `jenis_kendaraan` ENUM('roda2','roda4')    NOT NULL COMMENT 'Tipe kendaraan: roda 2 atau roda 4',
    `waktu_masuk`     DATETIME                 NOT NULL COMMENT 'Jam dan tanggal kendaraan masuk',
    `waktu_keluar`    DATETIME                 NULL DEFAULT NULL COMMENT 'Jam dan tanggal kendaraan keluar',
    `durasi_jam`      INT(11)                  NULL DEFAULT NULL COMMENT 'Durasi parkir dalam jam (dibulatkan ke atas)',
    `status`          ENUM('parkir','selesai') NOT NULL DEFAULT 'parkir' COMMENT 'Status kendaraan',
    `total_bayar`     DECIMAL(12,2)            NOT NULL DEFAULT 0.00 COMMENT 'Total biaya parkir yang harus dibayar',
    `created_at`      TIMESTAMP                NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP                NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_nomor_id` (`nomor_id`),
    INDEX `idx_nomor_plat` (`nomor_plat`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `parkir` 
    (`nomor_id`, `nomor_plat`, `jenis_kendaraan`, `waktu_masuk`, `waktu_keluar`, `durasi_jam`, `status`, `total_bayar`) 
VALUES
    -- 1. Selesai: Roda 2, Masuk 07:00 Keluar 08:30 (Durasi 2 jam) -> Rp 2.000
    ('PRK-20260001', 'L 1234 AB', 'roda2', '2026-09-30 07:00:00', '2026-09-30 08:30:00', 2, 'selesai', 2000.00),
    
    -- 2. Selesai: Roda 4, Masuk 07:30 Keluar 10:30 (Durasi 3 jam) -> Rp 5.000 + 1 jam (Rp 1.000) = Rp 6.000
    ('PRK-20260002', 'N 5678 CD', 'roda4', '2026-09-30 07:30:00', '2026-09-30 10:30:00', 3, 'selesai', 6000.00),
    
    -- 3. Selesai: Roda 2, Masuk 08:00 Keluar 11:00 (Durasi 3 jam) -> Rp 2.000 + 1 jam (Rp 1.000) = Rp 3.000
    ('PRK-20260003', 'S 9999 XY', 'roda2', '2026-09-30 08:00:00', '2026-09-30 11:00:00', 3, 'selesai', 3000.00),
    
    -- 4. Masih Parkir: Roda 4, Masuk 09:00
    ('PRK-20260004', 'W 4321 ZZ', 'roda4', '2026-09-30 09:00:00', NULL, NULL, 'parkir', 0.00),
    
    -- 5. Masih Parkir: Roda 2, Masuk 09:30
    ('PRK-20260005', 'AE 1111 AA', 'roda2', '2026-09-30 09:30:00', NULL, NULL, 'parkir', 0.00);


UPDATE admin SET password = 'admin123' WHERE username = 'admin';


UPDATE parkir 
SET waktu_masuk = CONCAT(CURDATE(), ' ', TIME(waktu_masuk)),
    waktu_keluar = CASE 
        WHEN waktu_keluar IS NOT NULL THEN CONCAT(CURDATE(), ' ', TIME(waktu_keluar))
        ELSE NULL 
    END;