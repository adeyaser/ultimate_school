<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ujian extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru'));
        $this->load->model(array('M_Ujian', 'M_BankSoal', 'M_Kelas', 'M_Mapel', 'M_Guru'));
    }

    public function index()
    {
        $data['title'] = 'Jadwal & Sesi Ujian Online (CBT)';
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
        $data['ujian_list']     = $this->M_Ujian->get_all($guru_id, null, $active_jenjang);
        $data['bank_list']      = $this->M_BankSoal->get_all($guru_id, $active_jenjang);
        $data['kelas_list']     = $this->M_Kelas->get_all(null, $active_jenjang);
        $data['mapel_list']     = $this->M_Mapel->get_all($active_jenjang);

        $this->render_page('ujian/index', $data);
    }

    public function simpan_ujian()
    {
        $guru = $this->M_Guru->get_by_user_id($this->user_data['id']);
        $guru_id = isset($guru['id']) ? $guru['id'] : 1;

        $active_jenjang = $this->session->userdata('active_jenjang');
        if (empty($active_jenjang) || $active_jenjang === 'ALL') {
            $active_jenjang = isset($this->school_info['jenjang']) ? $this->school_info['jenjang'] : 'SMP';
        }

        $kelas_id = $this->input->post('kelas_id', true);
        $kelas = $this->M_Kelas->get_by_id($kelas_id);
        $jenjang = isset($kelas['jenjang']) ? $kelas['jenjang'] : $active_jenjang;

        $bank_id = $this->input->post('bank_soal_id', true);
        $bank = $this->M_BankSoal->get_by_id($bank_id);

        $token = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

        $data = array(
            'bank_soal_id' => $bank_id,
            'kelas_id' => $kelas_id,
            'jenjang' => $jenjang,
            'mata_pelajaran_id' => $bank['mata_pelajaran_id'],
            'guru_id' => $guru_id,
            'judul_ujian' => $this->input->post('judul_ujian', true),
            'jenis_ujian' => $this->input->post('jenis_ujian', true),
            'tanggal_mulai' => $this->input->post('tanggal_mulai', true),
            'tanggal_selesai' => $this->input->post('tanggal_selesai', true),
            'durasi' => $this->input->post('durasi', true),
            'jumlah_soal' => $bank['jumlah_soal'],
            'kkm' => $bank['kkm'],
            'is_active' => 1,
            'is_shuffle' => $this->input->post('is_shuffle', true) ? 1 : 0,
            'token' => $token
        );

        $this->M_Ujian->insert($data);
        $this->session->set_flashdata('success', 'Sesi ujian berhasil dijadwalkan dengan Token: <strong>' . $token . '</strong>');
        redirect('ujian');
    }

    public function toggle_status($encrypted_ujian_id, $status)
    {
        $ujian_id = decrypt_id($encrypted_ujian_id);
        $this->M_Ujian->update($ujian_id, array('is_active' => $status));
        $this->session->set_flashdata('success', 'Status ujian berhasil diperbarui.');
        redirect('ujian');
    }

    public function reset_peserta($encrypted_ujian_id)
    {
        $ujian_id = decrypt_id($encrypted_ujian_id);
        $peserta_list = $this->db->get_where('ujian_peserta', array('ujian_id' => $ujian_id))->result_array();
        foreach ($peserta_list as $p) {
            $this->db->delete('ujian_jawaban', array('ujian_peserta_id' => $p['id']));
        }
        $this->db->delete('ujian_peserta', array('ujian_id' => $ujian_id));

        $this->session->set_flashdata('success', 'Seluruh data pengerjaan ujian untuk sesi ini berhasil di-reset.');
        redirect('ujian');
    }
}

