<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seed extends CI_Controller {

    public function index()
    {
        $m = new mysqli('localhost', 'root', '', 'ultimate_school');
        if ($m->connect_error) {
            die("Connection failed: " . $m->connect_error);
        }

        echo "Seeding comprehensive master data across all 34 tables...\n";

        // 1. Identitas & Profil Sekolah
        $sekolah_check = $m->query("SELECT COUNT(*) as c FROM sekolah WHERE id=1")->fetch_assoc();
        if ($sekolah_check['c'] == 0) {
            $m->query("INSERT INTO sekolah (id, nama_sekolah, npsn, kepala_sekolah, telepon, email, website, alamat, kota, provinsi, kode_pos, running_text, hero_title, hero_subtitle, hero_type, hero_media, logo, sambutan_kepsek, visi, misi, facebook_url, instagram_url, youtube_url) VALUES 
                (1, 'Ultimate School', '12345678', 'Dr. H. Ahmad Dahlan, M.Pd.', '(021) 555-0199', 'info@ultimateschool.sch.id', 'https://ultimateschool.sch.id', 'Jl. Pendidikan No. 123, Komplek Edukasi Terpadu', 'Jakarta Selatan', 'DKI Jakarta', '12345', ' Selamat Datang di Ultimate School! Penerimaan Peserta Didik Baru (PPDB) T.A. 2026/2027 Resmi Dibuka secara Online.', 'Mewujudkan Generasi Unggul, Berkarakter & Berdaya Saing Global', 'Pendidikan berkualitas tinggi berbasis teknologi modern, kurikulum unggulan terintegrasi, dan pembentukan karakter akhlak mulia.', 'image', 'dist/assets/img/photo1.png', 'dist/assets/img/AdminLTELogo.png', 'Assalamu\'alaikum Wr. Wb. Selamat datang di portal resmi Ultimate School. Kami berkomitmen menyelenggarakan pendidikan holistik berskala internasional.', 'Menjadi lembaga pendidikan terdepan dalam mencetak lulusan berprestasi, berkarakter, dan berdaya saing di era digital.', '1. Menyelenggarakan pembelajaran inovatif berbasis teknologi modern.\n2. Membina akhlak mulia dan kepemimpinan berwawasan lingkungan.', 'https://facebook.com/ultimateschool', 'https://instagram.com/ultimateschool', 'https://youtube.com/ultimateschool');
            ");
        }

        // 2. Tahun Ajaran
        $m->query("INSERT IGNORE INTO tahun_ajaran (id, nama, semester, tanggal_mulai, tanggal_selesai, is_active) VALUES 
            (1, '2026/2027', 'Ganjil', '2026-07-01', '2026-12-31', 1),
            (2, '2025/2026', 'Genap', '2026-01-01', '2026-06-30', 0);
        ");

        // 3. User Master (Superadmin, Admin, Guru, Murid)
        $pass = password_hash('password', PASSWORD_BCRYPT);
        $m->query("INSERT IGNORE INTO users (id, username, email, password, full_name, gender, role, status) VALUES 
            (1, 'superadmin', 'superadmin@ultimateschool.com', '$pass', 'Super Administrator', 'L', 'super_admin', 'active'),
            (2, 'budiguru', 'budi@ultimateschool.com', '$pass', 'Budi Santoso, S.Pd.', 'L', 'guru', 'active'),
            (10, 'hendraguru', 'hendra@ultimateschool.com', '$pass', 'Drs. Hendra Wijaya, M.Si.', 'L', 'guru', 'active'),
            (11, 'rinaguru', 'rina@ultimateschool.com', '$pass', 'Rina Melati, S.Pd.', 'P', 'guru', 'active'),
            (3, 'ahmadfauzi', 'ahmad@ultimateschool.com', '$pass', 'Ahmad Fauzi', 'L', 'murid', 'active'),
            (20, 'dewapratama', 'dewa@ultimateschool.com', '$pass', 'Dewa Pratama', 'L', 'murid', 'active'),
            (21, 'sitirahma', 'rahma@ultimateschool.com', '$pass', 'Siti Rahmawati', 'P', 'murid', 'active');
        ");

        // Guru Profiles
        $m->query("INSERT IGNORE INTO guru (id, nip, user_id, pendidikan_terakhir, jurusan_pendidikan, tahun_masuk, status_kepegawaian) VALUES 
            (1, '198501012010011001', 2, 'S1 Pendidikan Matematika', 'Pendidikan Matematika', 2015, 'PNS'),
            (2, '198703032011031003', 10, 'S2 Fisika Terapan', 'Fisika', 2018, 'PNS'),
            (3, '199004042015042004', 11, 'S1 Pendidikan Bahasa Inggris', 'Bahasa Inggris', 2020, 'GTT');
        ");

        // 4. Kelas
        $m->query("INSERT IGNORE INTO kelas (id, nama_kelas, tingkat, jurusan, ruangan, kapasitas, wali_kelas_id, tahun_ajaran_id, is_active) VALUES 
            (1, 'X MIPA 1', 'X', 'MIPA', 'R.101', 36, 2, 1, 1),
            (2, 'X MIPA 2', 'X', 'MIPA', 'R.102', 36, 10, 1, 1),
            (3, 'XI MIPA 1', 'XI', 'MIPA', 'R.201', 36, 11, 1, 1),
            (4, 'XII IPS 1', 'XII', 'IPS', 'R.301', 36, 2, 1, 1);
        ");

        // 5. Murid Profiles & Parents
        $m->query("INSERT IGNORE INTO murid (id, nisn, nis, user_id, kelas_id, tahun_ajaran_id, tempat_lahir, tanggal_lahir, agama, alamat_tinggal, status_murid, tanggal_masuk) VALUES 
            (1, '0081239011', '2026001', 3, 1, 1, 'Jakarta', '2010-05-15', 'Islam', 'Jl. Merdeka No. 10', 'Aktif', '2026-07-01'),
            (2, '0081239012', '2026002', 20, 1, 1, 'Bandung', '2010-08-20', 'Islam', 'Jl. Asia Afrika No. 45', 'Aktif', '2026-07-01'),
            (3, '0081239013', '2026003', 21, 2, 1, 'Surabaya', '2010-11-12', 'Islam', 'Jl. Pemuda No. 88', 'Aktif', '2026-07-01');
        ");

        $m->query("INSERT IGNORE INTO orang_tua (id, murid_id, ayah_nama, ayah_pekerjaan, ibu_nama, ibu_pekerjaan) VALUES 
            (1, 1, 'Bapak Fauzi', 'Wiraswasta', 'Ibu Siti', 'Ibu Rumah Tangga'),
            (2, 2, 'Bapak Pratama', 'PNS', 'Ibu Ratna', 'Guru'),
            (3, 3, 'Bapak Rahmawati', 'Karyawan Swasta', 'Ibu Dewi', 'Pedagang');
        ");

        // 6. Mata Pelajaran
        $m->query("INSERT IGNORE INTO mata_pelajaran (id, kode_mapel, nama_mapel, kelompok, jam_per_minggu, kkm, deskripsi) VALUES 
            (1, 'MAT-X', 'Matematika Wajib', 'Wajib', 4, 75, 'Mata pelajaran matematika dasar SMA'),
            (2, 'IND-X', 'Bahasa Indonesia', 'Wajib', 4, 75, 'Mata pelajaran Bahasa & Sastra Indonesia'),
            (3, 'FIS-X', 'Fisika Peminatan', 'Peminatan', 3, 75, 'Pembelajaran konsep dasar fisika dan laboratorium');
        ");

        // 7. Bank Soal & Soal CBT
        $m->query("INSERT IGNORE INTO bank_soal (id, kode_soal, mata_pelajaran_id, kelas_id, guru_id, judul, jenis_soal, tingkat_kesulitan, durasi, jumlah_soal, kkm, status, created_by) VALUES 
            (1, 'BS-MAT-01', 1, 1, 1, 'Bank Soal Aljabar & Persamaan Kuadrat', 'Pilihan Ganda', 'Sedang', 60, 3, 75, 'Published', 2);
        ");

        $m->query("INSERT IGNORE INTO soal (id, bank_soal_id, nomor_soal, pertanyaan, jenis, pilihan_a, pilihan_b, pilihan_c, pilihan_d, pilihan_e, kunci_jawaban, pembahasan, bobot, tingkat_kesulitan) VALUES 
            (1, 1, 1, 'Berapakah nilai x dari persamaan 2x + 6 = 14?', 'Pilihan Ganda', '2', '4', '6', '8', '10', 'B', '2x = 14 - 6 => 2x = 8 => x = 4.', 10, 'Mudah'),
            (2, 1, 2, 'Akar-akar dari persamaan kuadrat x² - 5x + 6 = 0 adalah...', 'Pilihan Ganda', 'x = 1 dan x = 5', 'x = 2 dan x = 3', 'x = -2 dan x = -3', 'x = 0 dan x = 6', 'x = 3 dan x = 5', 'B', '(x - 2)(x - 3) = 0 => x = 2 atau x = 3.', 10, 'Sedang'),
            (3, 1, 3, 'Jika f(x) = 3x² + 2x - 5, berapakah nilai f(2)?', 'Pilihan Ganda', '11', '13', '15', '17', '19', 'A', 'f(2) = 3(2)² + 2(2) - 5 = 12 + 4 - 5 = 11.', 10, 'Sedang');
        ");

        // 8. Sesi Ujian CBT
        $m->query("INSERT IGNORE INTO ujian (id, bank_soal_id, kelas_id, mata_pelajaran_id, guru_id, judul_ujian, jenis_ujian, tanggal_mulai, tanggal_selesai, durasi, jumlah_soal, kkm, is_active, is_shuffle, token) VALUES 
            (1, 1, 1, 1, 1, 'Ulangan Harian Bab Aljabar', 'Harian', NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 60, 3, 75, 1, 1, 'ULT123');
        ");

        // 9. Tabungan Siswa
        $m->query("INSERT IGNORE INTO tabungan (id, nomor_rekening, murid_id, saldo, status) VALUES 
            (1, 'TAB-2026-0001', 1, 500000.00, 'Aktif'),
            (2, 'TAB-2026-0002', 2, 750000.00, 'Aktif');
        ");

        $m->query("INSERT IGNORE INTO transaksi_tabungan (id, tabungan_id, jenis_transaksi, jumlah, saldo_awal, saldo_akhir, keterangan, petugas_id, tanggal_transaksi) VALUES 
            (1, 1, 'Setor', 500000.00, 0, 500000.00, 'Setoran awal buka tabungan siswa', 1, NOW()),
            (2, 2, 'Setor', 750000.00, 0, 750000.00, 'Setoran awal buka tabungan siswa', 1, NOW());
        ");

        // 10. Tagihan Pembayaran SPP (`pembayaran` table)
        $m->query("INSERT IGNORE INTO pembayaran (id, murid_id, jenis, bulan, nominal, terbayar, sisa, tanggal_jatuh_tempo, status, keterangan, created_at) VALUES 
            (1, 1, 'SPP', 'Agustus', 1000000.00, 1000000.00, 0.00, CURDATE(), 'Lunas', 'Pembayaran SPP Bulan Agustus 2026', NOW()),
            (2, 2, 'SPP', 'Agustus', 1000000.00, 0.00, 1000000.00, CURDATE(), 'Belum Lunas', 'Tagihan SPP Bulan Agustus 2026', NOW());
        ");

        // 11. Sertifikat & Raport
        $m->query("INSERT IGNORE INTO sertifikat (id, murid_id, nomor_seri, jenis, deskripsi, file_path, qr_code, tanggal_terbit, is_verified) VALUES 
            (1, 1, 'CERT-2026-0001', 'Sertifikat Siswa Berprestasi', 'Diberikan atas pencapaian Juara 1 Olimpiade Matematika Sains Tingkat Provinsi T.A. 2026/2027.', 'dist/assets/img/boxed-bg.jpg', 'QR-99881', CURDATE(), 1);
        ");

        // 12. Eskul
        $m->query("INSERT IGNORE INTO eskul (id, kode_eskul, nama_eskul, deskripsi, pembina_id, hari, jam_mulai, jam_selesai, tempat, kuota) VALUES 
            (1, 'PRAMUKA', 'Pramuka Wajib', 'Kegiatan kepramukaan wawasan kebangsaan dan pembentukan karakter kepemimpinan siswa.', 1, 'Jumat', '15:00:00', '17:00:00', 'Lapangan Utama Sekolah', 50),
            (2, 'PASKIBRA', 'Pasukan Pengibar Bendera', 'Pelatihan kedisiplinan dan pengibaran bendera pusaka.', 2, 'Sabtu', '08:00:00', '11:00:00', 'Lapangan Utama Sekolah', 40);
        ");

        $m->query("INSERT IGNORE INTO eskul_peserta (id, eskul_id, murid_id, tanggal_daftar) VALUES 
            (1, 1, 1, CURDATE()),
            (2, 2, 2, CURDATE());
        ");

        // 13. FAQ & Fasilitas Compro
        $check_faq = $m->query("SELECT COUNT(*) as c FROM faq")->fetch_assoc();
        if ($check_faq['c'] == 0) {
            $m->query("INSERT INTO faq (pertanyaan, jawaban, urutan) VALUES 
                ('Bagaimana cara mendaftar PPDB Online?', 'Isi formulir pendaftaran PPDB di beranda website ini, lalu lakukan verifikasi data.', 1),
                ('Apakah tersedia beasiswa prestasi?', 'Ya, kami menyediakan beasiswa penuh untuk siswa berprestasi di bidang akademik dan olahraga.', 2);
            ");
        }

        $check_fas = $m->query("SELECT COUNT(*) as c FROM fasilitas")->fetch_assoc();
        if ($check_fas['c'] == 0) {
            $m->query("INSERT INTO fasilitas (nama_fasilitas, deskripsi, foto, urutan) VALUES 
                ('Ruang Kelas Ber-AC & Smart Board', 'Ruang belajar Multimedia dengan Interactive Smart TV dan AC.', 'photo1.png', 1),
                ('Laboratorium Komputer & CBT Center', 'Perangkat komputer spek tinggi berkecepatan internet tinggi.', 'photo2.png', 2),
                ('Perpustakaan Digital', 'Koleksi buku cetak dan e-book digital terpadu.', 'photo3.jpg', 3);
            ");
        }

        // 14. Acara & Pengumuman Sekolah
        $check_acara = $m->query("SELECT COUNT(*) as c FROM acara_sekolah")->fetch_assoc();
        if ($check_acara['c'] == 0) {
            $m->query("INSERT INTO acara_sekolah (judul, deskripsi, tanggal_mulai, tanggal_selesai, waktu_mulai, waktu_selesai, tempat, jenis, penanggung_jawab, is_published) VALUES 
                ('Masa Pengenalan Lingkungan Sekolah (MPLS 2026)', 'Kegiatan orientasi pengenalan lingkungan sekolah bagi siswa baru T.A. 2026/2027.', '2026-07-15', '2026-07-17', '07:30:00', '14:00:00', 'Aula Utama Sekolah', 'Akademik', 'Wakasek Kesiswaan', 1),
                ('Olimpiade Sains & Komputer Antar Kelas', 'Kompetisi seru menguji penalaran sains, matematika, dan coding antar kelas.', '2026-08-20', '2026-08-21', '08:00:00', '16:00:00', 'Lab Komputer & Lab Sains', 'Lomba', 'Tim Pembina OSN', 1);
            ");
        }

        echo "SUCCESS: Master Database Seed Data populated across all 34 tables successfully.";
    }
}
