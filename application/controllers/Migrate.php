<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller {

    public function index()
    {
        $this->load->database();

        $queries = [
            // 27. bank_soal
            "CREATE TABLE IF NOT EXISTS `bank_soal` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `kode_soal` varchar(20) NOT NULL UNIQUE,
              `mata_pelajaran_id` int(11) NOT NULL,
              `kelas_id` int(11) NOT NULL,
              `guru_id` int(11) NOT NULL,
              `judul` varchar(255) NOT NULL,
              `jenis_soal` enum('Pilihan Ganda','Essay','Campuran') NOT NULL,
              `tingkat_kesulitan` enum('Mudah','Sedang','Sulit') NOT NULL,
              `bobot_nilai` int(11) DEFAULT 10,
              `durasi` int(11) DEFAULT 60 COMMENT 'Durasi dalam menit',
              `jumlah_soal` int(11) DEFAULT 0,
              `kkm` int(11) DEFAULT 75,
              `status` enum('Draft','Published','Archived') DEFAULT 'Draft',
              `is_random` tinyint(1) DEFAULT 0,
              `created_by` int(11) NOT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `mata_pelajaran_id` (`mata_pelajaran_id`),
              KEY `kelas_id` (`kelas_id`),
              KEY `guru_id` (`guru_id`),
              KEY `created_by` (`created_by`),
              CONSTRAINT `bank_soal_ibfk_1` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE,
              CONSTRAINT `bank_soal_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
              CONSTRAINT `bank_soal_ibfk_3` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE,
              CONSTRAINT `bank_soal_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            // 28. soal
            "CREATE TABLE IF NOT EXISTS `soal` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `bank_soal_id` int(11) NOT NULL,
              `nomor_soal` int(11) NOT NULL,
              `pertanyaan` text NOT NULL,
              `jenis` enum('Pilihan Ganda','Essay') NOT NULL,
              `pilihan_a` text DEFAULT NULL,
              `pilihan_b` text DEFAULT NULL,
              `pilihan_c` text DEFAULT NULL,
              `pilihan_d` text DEFAULT NULL,
              `pilihan_e` text DEFAULT NULL,
              `kunci_jawaban` text NOT NULL,
              `pembahasan` text DEFAULT NULL,
              `bobot` int(11) DEFAULT 10,
              `tingkat_kesulitan` enum('Mudah','Sedang','Sulit') DEFAULT 'Sedang',
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `bank_soal_id` (`bank_soal_id`),
              CONSTRAINT `soal_ibfk_1` FOREIGN KEY (`bank_soal_id`) REFERENCES `bank_soal` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            // 29. ujian
            "CREATE TABLE IF NOT EXISTS `ujian` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `bank_soal_id` int(11) NOT NULL,
              `kelas_id` int(11) NOT NULL,
              `mata_pelajaran_id` int(11) NOT NULL,
              `guru_id` int(11) NOT NULL,
              `judul_ujian` varchar(255) NOT NULL,
              `jenis_ujian` enum('Harian','PTS','PAS','Try Out','Remidi') NOT NULL,
              `tanggal_mulai` datetime NOT NULL,
              `tanggal_selesai` datetime NOT NULL,
              `durasi` int(11) NOT NULL COMMENT 'Durasi dalam menit',
              `jumlah_soal` int(11) NOT NULL,
              `kkm` int(11) DEFAULT 75,
              `is_active` tinyint(1) DEFAULT 1,
              `is_shuffle` tinyint(1) DEFAULT 0,
              `token` varchar(50) DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `bank_soal_id` (`bank_soal_id`),
              KEY `kelas_id` (`kelas_id`),
              KEY `mata_pelajaran_id` (`mata_pelajaran_id`),
              KEY `guru_id` (`guru_id`),
              CONSTRAINT `ujian_ibfk_1` FOREIGN KEY (`bank_soal_id`) REFERENCES `bank_soal` (`id`) ON DELETE CASCADE,
              CONSTRAINT `ujian_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
              CONSTRAINT `ujian_ibfk_3` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE,
              CONSTRAINT `ujian_ibfk_4` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            // 30. ujian_peserta
            "CREATE TABLE IF NOT EXISTS `ujian_peserta` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `ujian_id` int(11) NOT NULL,
              `murid_id` int(11) NOT NULL,
              `token_akses` varchar(50) DEFAULT NULL,
              `tanggal_mulai` datetime DEFAULT NULL,
              `tanggal_selesai` datetime DEFAULT NULL,
              `durasi_pengerjaan` int(11) DEFAULT NULL COMMENT 'Durasi dalam detik',
              `status` enum('Belum','Sedang','Selesai','Timeout') DEFAULT 'Belum',
              `nilai_total` decimal(5,2) DEFAULT NULL,
              `is_lulus` tinyint(1) DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `unique_ujian_peserta` (`ujian_id`,`murid_id`),
              KEY `murid_id` (`murid_id`),
              CONSTRAINT `ujian_peserta_ibfk_1` FOREIGN KEY (`ujian_id`) REFERENCES `ujian` (`id`) ON DELETE CASCADE,
              CONSTRAINT `ujian_peserta_ibfk_2` FOREIGN KEY (`murid_id`) REFERENCES `murid` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            // 31. ujian_jawaban
            "CREATE TABLE IF NOT EXISTS `ujian_jawaban` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `ujian_peserta_id` int(11) NOT NULL,
              `soal_id` int(11) NOT NULL,
              `jawaban` text DEFAULT NULL,
              `is_benar` tinyint(1) DEFAULT NULL,
              `nilai` decimal(5,2) DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `unique_jawaban` (`ujian_peserta_id`,`soal_id`),
              KEY `soal_id` (`soal_id`),
              CONSTRAINT `ujian_jawaban_ibfk_1` FOREIGN KEY (`ujian_peserta_id`) REFERENCES `ujian_peserta` (`id`) ON DELETE CASCADE,
              CONSTRAINT `ujian_jawaban_ibfk_2` FOREIGN KEY (`soal_id`) REFERENCES `soal` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            // 32. kuis_rekomendasi
            "CREATE TABLE IF NOT EXISTS `kuis_rekomendasi` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `murid_id` int(11) NOT NULL,
              `mata_pelajaran_id` int(11) NOT NULL,
              `tingkat_kesulitan` enum('Mudah','Sedang','Sulit') NOT NULL,
              `nilai_terakhir` decimal(5,2) DEFAULT NULL,
              `jumlah_soal_dikerjakan` int(11) DEFAULT 0,
              `persentase_benar` decimal(5,2) DEFAULT NULL,
              `rekomendasi_tingkat` enum('Mudah','Sedang','Sulit') DEFAULT 'Sedang',
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `unique_rekomendasi` (`murid_id`,`mata_pelajaran_id`),
              KEY `mata_pelajaran_id` (`mata_pelajaran_id`),
              CONSTRAINT `kuis_rekomendasi_ibfk_1` FOREIGN KEY (`murid_id`) REFERENCES `murid` (`id`) ON DELETE CASCADE,
              CONSTRAINT `kuis_rekomendasi_ibfk_2` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            // 33. kuis_latihan
            "CREATE TABLE IF NOT EXISTS `kuis_latihan` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `murid_id` int(11) NOT NULL,
              `mata_pelajaran_id` int(11) NOT NULL,
              `soal_ids` text NOT NULL COMMENT 'JSON array of soal ids',
              `jumlah_soal` int(11) NOT NULL,
              `jawaban_benar` int(11) DEFAULT 0,
              `jawaban_salah` int(11) DEFAULT 0,
              `nilai` decimal(5,2) DEFAULT NULL,
              `durasi_pengerjaan` int(11) DEFAULT NULL,
              `tanggal_mulai` datetime NOT NULL,
              `tanggal_selesai` datetime DEFAULT NULL,
              `status` enum('Belum','Sedang','Selesai') DEFAULT 'Belum',
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `murid_id` (`murid_id`),
              KEY `mata_pelajaran_id` (`mata_pelajaran_id`),
              CONSTRAINT `kuis_latihan_ibfk_1` FOREIGN KEY (`murid_id`) REFERENCES `murid` (`id`) ON DELETE CASCADE,
              CONSTRAINT `kuis_latihan_ibfk_2` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            // 34. kuis_latihan_jawaban
            "CREATE TABLE IF NOT EXISTS `kuis_latihan_jawaban` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `kuis_latihan_id` int(11) NOT NULL,
              `soal_id` int(11) NOT NULL,
              `jawaban` text DEFAULT NULL,
              `is_benar` tinyint(1) DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              PRIMARY KEY (`id`),
              KEY `kuis_latihan_id` (`kuis_latihan_id`),
              KEY `soal_id` (`soal_id`),
              CONSTRAINT `kuis_latihan_jawaban_ibfk_1` FOREIGN KEY (`kuis_latihan_id`) REFERENCES `kuis_latihan` (`id`) ON DELETE CASCADE,
              CONSTRAINT `kuis_latihan_jawaban_ibfk_2` FOREIGN KEY (`soal_id`) REFERENCES `soal` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            // 35. materi_ocr_soal
            "CREATE TABLE IF NOT EXISTS `materi_ocr_soal` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `bank_soal_id` int(11) DEFAULT NULL,
              `user_id` int(11) NOT NULL,
              `judul_materi` varchar(255) DEFAULT NULL,
              `image_path` text DEFAULT NULL,
              `ocr_text` longtext NOT NULL,
              `ringkasan_materi` longtext DEFAULT NULL,
              `jumlah_soal` int(11) DEFAULT 5,
              `jenis_soal` varchar(50) DEFAULT 'Pilihan Ganda',
              `tingkat_kesulitan` varchar(50) DEFAULT 'Sedang',
              `generated_json` longtext DEFAULT NULL,
              `status` enum('draft', 'summarized', 'completed') DEFAULT 'draft',
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `bank_soal_id` (`bank_soal_id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `materi_ocr_soal_ibfk_1` FOREIGN KEY (`bank_soal_id`) REFERENCES `bank_soal` (`id`) ON DELETE SET NULL,
              CONSTRAINT `materi_ocr_soal_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
        ];

        foreach ($queries as $idx => $sql) {
            $this->db->query($sql);
        }

        echo "Migration completed successfully. All 35 tables are present.";
    }
}
