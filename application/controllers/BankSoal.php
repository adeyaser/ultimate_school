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
}
