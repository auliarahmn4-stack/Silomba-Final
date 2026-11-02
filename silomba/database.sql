
-- =========================================================
-- DATABASE SILOMBA
-- Sistem Informasi Lomba
-- PHP Native + MySQL / XAMPP
-- =========================================================

CREATE DATABASE IF NOT EXISTS silomba_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE silomba_db;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS notifikasi;
DROP TABLE IF EXISTS sertifikat;
DROP TABLE IF EXISTS pendaftaran_lomba;
DROP TABLE IF EXISTS lomba;
DROP TABLE IF EXISTS pengaturan;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- 1. TABEL USERS
-- Menyimpan akun AKAMA, PIC, dan mahasiswa
-- =========================================================

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    nim VARCHAR(30) NULL,
    email VARCHAR(150) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,

    role ENUM('akama', 'pic', 'mahasiswa')
        NOT NULL DEFAULT 'mahasiswa',

    status ENUM('aktif', 'nonaktif')
        NOT NULL DEFAULT 'aktif',

    no_telpon VARCHAR(25) NULL,
    fakultas VARCHAR(150) NULL,
    program_studi VARCHAR(150) NULL,
    foto VARCHAR(255) NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_users_email (email),
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_nim (nim),
    KEY idx_users_role_status (role, status)
) ENGINE=InnoDB;

-- =========================================================
-- 2. TABEL LOMBA
-- Menyimpan pengajuan dan informasi lomba
-- =========================================================

CREATE TABLE lomba (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pic_id BIGINT UNSIGNED NOT NULL,

    judul VARCHAR(200) NOT NULL,
    kategori VARCHAR(100) NULL,
    deskripsi TEXT NOT NULL,
    penyelenggara VARCHAR(200) NOT NULL,
    tingkat VARCHAR(50) NULL,
    lokasi VARCHAR(255) NULL,

    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    deadline_pendaftaran DATE NOT NULL,

    poster VARCHAR(255) NULL,

    link_pendaftaran VARCHAR(255) NULL,
    link_panduan VARCHAR(255) NULL,
    kontak_person VARCHAR(255) NULL,

    status ENUM(
        'menunggu',
        'disetujui',
        'ditolak',
        'siap_publikasi',
        'dipublikasikan',
        'selesai'
    ) NOT NULL DEFAULT 'menunggu',

    catatan_verifikasi TEXT NULL,
    verified_by BIGINT UNSIGNED NULL,
    verified_at DATETIME NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    KEY idx_lomba_pic (pic_id),
    KEY idx_lomba_status (status),
    KEY idx_lomba_deadline (deadline_pendaftaran),

    CONSTRAINT fk_lomba_pic
        FOREIGN KEY (pic_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_lomba_verifikator
        FOREIGN KEY (verified_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================
-- 3. TABEL PENDAFTARAN LOMBA
-- Menghubungkan mahasiswa dengan lomba
-- =========================================================

CREATE TABLE pendaftaran_lomba (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lomba_id BIGINT UNSIGNED NOT NULL,
    mahasiswa_id BIGINT UNSIGNED NOT NULL,

    tanggal_daftar DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    status ENUM(
        'menunggu',
        'diterima',
        'ditolak',
        'selesai'
    ) NOT NULL DEFAULT 'menunggu',

    catatan TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_pendaftaran_mahasiswa_lomba
        (lomba_id, mahasiswa_id),

    KEY idx_pendaftaran_mahasiswa (mahasiswa_id),
    KEY idx_pendaftaran_status (status),

    CONSTRAINT fk_pendaftaran_lomba
        FOREIGN KEY (lomba_id) REFERENCES lomba(id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT fk_pendaftaran_mahasiswa
        FOREIGN KEY (mahasiswa_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- 4. TABEL SERTIFIKAT
-- Menyimpan sertifikat yang diunggah PIC
-- =========================================================

CREATE TABLE sertifikat (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pendaftaran_id BIGINT UNSIGNED NOT NULL,
    lomba_id BIGINT UNSIGNED NOT NULL,
    mahasiswa_id BIGINT UNSIGNED NOT NULL,
    pic_id BIGINT UNSIGNED NOT NULL,

    nomor_sertifikat VARCHAR(100) NOT NULL,
    file_sertifikat VARCHAR(255) NOT NULL,
    tanggal_unggah DATE NOT NULL DEFAULT (CURRENT_DATE),

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_sertifikat_pendaftaran (pendaftaran_id),
    UNIQUE KEY uq_sertifikat_nomor (nomor_sertifikat),

    KEY idx_sertifikat_mahasiswa (mahasiswa_id),
    KEY idx_sertifikat_pic (pic_id),

    CONSTRAINT fk_sertifikat_pendaftaran
        FOREIGN KEY (pendaftaran_id)
        REFERENCES pendaftaran_lomba(id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT fk_sertifikat_lomba
        FOREIGN KEY (lomba_id) REFERENCES lomba(id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT fk_sertifikat_mahasiswa
        FOREIGN KEY (mahasiswa_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT fk_sertifikat_pic
        FOREIGN KEY (pic_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =========================================================
-- 5. TABEL NOTIFIKASI
-- Menyimpan notifikasi setiap pengguna
-- =========================================================

CREATE TABLE notifikasi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,

    judul VARCHAR(200) NOT NULL,
    pesan TEXT NOT NULL,
    tautan VARCHAR(255) NULL,
    dibaca_at DATETIME NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    KEY idx_notifikasi_user (user_id),
    KEY idx_notifikasi_dibaca (user_id, dibaca_at),

    CONSTRAINT fk_notifikasi_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- 6. TABEL PENGATURAN
-- Menyimpan pengaturan umum aplikasi
-- =========================================================

CREATE TABLE pengaturan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_situs VARCHAR(150) NOT NULL DEFAULT 'SILOMBA',
    email_admin VARCHAR(150) NULL,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO pengaturan (nama_situs, email_admin)
VALUES ('SILOMBA - Sistem Informasi Lomba', NULL);

ALTER TABLE `lomba`
ADD COLUMN `link_pendaftaran` VARCHAR(255) NULL DEFAULT NULL AFTER `deskripsi`,
ADD COLUMN `link_panduan` VARCHAR(255) NULL DEFAULT NULL AFTER `link_pendaftaran`,
ADD COLUMN `kontak_person` VARCHAR(255) NULL DEFAULT NULL AFTER `link_panduan`;
-- =========================================================
-- SELESAI
-- Catatan:
-- Akun pengguna dibuat melalui halaman registrasi.
-- Akun AKAMA pertama perlu dibuat secara terkontrol.
-- =========================================================
