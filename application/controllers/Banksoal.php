<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Banksoal extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru'));
        $this->load->model(array('M_BankSoal', 'M_Mapel', 'M_Kelas', 'M_Guru'));
    }

    public function index()
    {
        $data['title'] = 'Repositori Bank Soal';
        $guru_id = null;
        if ($this->role === 'guru') {
            $guru = $this->M_Guru->get_by_user_id($this->user_data['id']);
            $guru_id = isset($guru['id']) ? $guru['id'] : null;
        }

        $active_jenjang = $this->session->userdata('active_jenjang');
        if (empty($active_jenjang)) {
            $active_jenjang = isset($this->school_info['jenjang']) ? $this->school_info['jenjang'] : 'SMP';
        }

        $data['active_jenjang'] = $active_jenjang;
        $data['bank_list']      = $this->M_BankSoal->get_all($guru_id, $active_jenjang);
        $data['mapel_list']     = $this->M_Mapel->get_all($active_jenjang);
        $data['kelas_list']     = $this->M_Kelas->get_all(null, $active_jenjang);

        $this->render_page('bank_soal/index', $data);
    }

    public function simpan_bank()
    {
        $guru = $this->M_Guru->get_by_user_id($this->user_data['id']);
        $guru_id = isset($guru['id']) ? $guru['id'] : 1;

        $data = array(
            'kode_soal' => 'BS-' . date('Ym') . '-' . rand(100, 999),
            'mata_pelajaran_id' => $this->input->post('mata_pelajaran_id', true),
            'kelas_id' => $this->input->post('kelas_id', true),
            'guru_id' => $guru_id,
            'judul' => $this->input->post('judul', true),
            'jenis_soal' => $this->input->post('jenis_soal', true),
            'tingkat_kesulitan' => $this->input->post('tingkat_kesulitan', true),
            'durasi' => $this->input->post('durasi', true),
            'kkm' => $this->input->post('kkm', true),
            'status' => 'Published',
            'created_by' => $this->user_data['id']
        );

        $this->M_BankSoal->insert_bank_soal($data);
        $this->session->set_flashdata('success', 'Paket Bank Soal berhasil dibuat.');
        redirect('banksoal');
    }

    public function detail($bank_soal_id)
    {
        $data['title']     = 'Kelola Detail Soal Ujian';
        $data['bank_soal'] = $this->M_BankSoal->get_by_id($bank_soal_id);
        $data['soal_list'] = $this->M_BankSoal->get_soal_by_bank($bank_soal_id);

        $this->render_page('bank_soal/detail', $data);
    }

    public function simpan_soal_item()
    {
        $bank_soal_id = $this->input->post('bank_soal_id', true);
        
        // Auto-increment question number
        $existing = $this->M_BankSoal->get_soal_by_bank($bank_soal_id);
        $nomor = count($existing) + 1;

        $data = array(
            'bank_soal_id' => $bank_soal_id,
            'nomor_soal'   => $nomor,
            'pertanyaan'   => $this->input->post('pertanyaan', true),
            'jenis'        => $this->input->post('jenis', true),
            'pilihan_a'    => $this->input->post('pilihan_a', true),
            'pilihan_b'    => $this->input->post('pilihan_b', true),
            'pilihan_c'    => $this->input->post('pilihan_c', true),
            'pilihan_d'    => $this->input->post('pilihan_d', true),
            'pilihan_e'    => $this->input->post('pilihan_e', true),
            'kunci_jawaban'=> $this->input->post('kunci_jawaban', true),
            'pembahasan'   => $this->input->post('pembahasan', true),
            'bobot'        => $this->input->post('bobot', true) ? $this->input->post('bobot', true) : 10,
            'tingkat_kesulitan' => $this->input->post('tingkat_kesulitan', true) ? $this->input->post('tingkat_kesulitan', true) : 'Sedang'
        );

        $this->M_BankSoal->insert_soal($data);
        $this->session->set_flashdata('success', 'Soal berhasil ditambahkan ke repositori.');
        redirect('banksoal/detail/' . $bank_soal_id);
    }

    public function hapus_soal($soal_id, $bank_soal_id)
    {
        $this->M_BankSoal->delete_soal($soal_id, $bank_soal_id);
        $this->session->set_flashdata('success', 'Soal berhasil dihapus.');
        redirect('banksoal/detail/' . $bank_soal_id);
    }

    public function import_massal()
    {
        $bank_soal_id = $this->input->post('bank_soal_id', true);
        $raw_text     = $this->input->post('raw_text');

        if (empty($raw_text)) {
            $this->session->set_flashdata('error', 'Teks soal massal tidak boleh kosong.');
            redirect('banksoal/detail/' . $bank_soal_id);
            return;
        }

        $total_imported = $this->M_BankSoal->parse_and_import_bulk_soal($bank_soal_id, $raw_text);

        if ($total_imported > 0) {
            $this->session->set_flashdata('success', "Berhasil mengimpor $total_imported soal & kunci jawaban otomatis ke bank soal!");
        } else {
            $this->session->set_flashdata('error', 'Gagal memproses teks soal. Pastikan format penulisan nomor soal (1. 2. 3.) dan opsi (a. b. c. d.) sesuai.');
        }

        redirect('banksoal/detail/' . $bank_soal_id);
    }

    public function download_template()
    {
        $this->load->helper('download');

        $template_content = "TEMPLAT PENULISAN SOAL MASSAL (ULTIMATE SCHOOL SYSTEM)
====================================================================
PETUNJUK STANDAR PENULISAN:
1. Teks bacaan/wacana/puisi (jika ada) ditulis sebelum nomor soal.
2. Setiap butir pertanyaan diawali nomor angka & titik (contoh: 1.  2.  3.).
3. Pilihan opsi jawaban diawali huruf kecil & titik (a.  b.  c.  d.  e.).
4. Di bagian paling bawah, sertakan tabel/blok 'KUNCI JAWABAN + PEMBAHASAN'.
====================================================================

Teks Bacaan untuk soal nomor 1 dan 2:
Liburan sekolah kali ini, Rina dan keluarga pergi ke pantai. Mereka berangkat pagi-pagi sekali menggunakan mobil. Di perjalanan, mereka melihat pemandangan sawah yang hijau. Sesampainya di pantai, Rina sangat senang. Ia bermain pasir dan membuat istana pasir yang besar. Ayahnya berenang di laut, sedangkan ibunya duduk di bawah payung sambil membaca buku.

1. Apa yang dilakukan Rina di pantai?
a. Berenang di laut
b. Membaca buku
c. Membuat istana pasir
d. Menyiram sawah

2. Kapan Rina dan keluarganya berangkat ke pantai?
a. Siang hari
b. Pagi-pagi sekali
c. Sore hari
d. Malam hari

3. Perhatikan kalimat berikut!
\"Adik menangis karena jatuh dari sepeda.\"
Kata penghubung yang dicetak tebal (karena) menyatakan hubungan....
a. Waktu
b. Tujuan
c. Sebab-akibat
d. Cara

4. Bacalah puisi pendek di bawah ini!
Pagi ini mentari tersenyum
Burung-burung bernyanyi riang
Udara segar menerpa wajah
Semangat baru datang menyapa

Puisi di atas menggambarkan suasana....
a. Menyedihkan
b. Menakutkan
c. Meriah
d. Gembira

KUNCI JAWABAN + PEMBAHASAN
No\tJawaban\tPembahasan
1\tC\tMembuat istana pasir
2\tB\tPagi-pagi sekali
3\tC\tSebab-akibat
4\tD\tSuasana gembira";

        force_download('template_impor_soal_massal.txt', $template_content);
    }
}
