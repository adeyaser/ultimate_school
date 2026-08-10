<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cbt extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('murid', 'orang_tua', 'super_admin', 'admin', 'guru', 'wali_kelas'));
        $this->load->model(array('M_Ujian', 'M_BankSoal', 'M_Murid'));
    }

    public function index()
    {
        $data['title'] = 'Daftar Ujian Online Saya (CBT)';
        
        $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
        $kelas_id = isset($murid['kelas_id']) ? $murid['kelas_id'] : null;
        $murid_id = isset($murid['id']) ? $murid['id'] : null;

        $active_jenjang = $this->session->userdata('active_jenjang');
        if (empty($active_jenjang)) {
            $active_jenjang = isset($this->school_info['jenjang']) ? $this->school_info['jenjang'] : 'SMP';
        }

        $data['active_jenjang'] = $active_jenjang;
        $data['ujian_list']     = $this->M_Ujian->get_ujian_siswa_list($kelas_id, $murid_id, $active_jenjang);

        $this->render_page('cbt/index', $data);
    }

    public function konfirmasi($encrypted_ujian_id)
    {
        $ujian_id = decrypt_id($encrypted_ujian_id);
        $data['title'] = 'Konfirmasi Ujian CBT';
        $data['ujian'] = $this->M_Ujian->get_by_id($ujian_id);

        $this->render_page('cbt/token', $data);
    }

    public function mulai_ujian()
    {
        $raw_ujian_id = $this->input->post('ujian_id', true);
        $ujian_id     = decrypt_id($raw_ujian_id);
        $token        = $this->input->post('token', true);

        $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
        $murid_id = isset($murid['id']) ? $murid['id'] : 1;

        $res = $this->M_Ujian->start_exam($ujian_id, $murid_id, $token);

        if ($res['status']) {
            redirect('cbt/lembar_ujian/' . encrypt_id($res['peserta_id']));
        } else {
            $this->session->set_flashdata('error', $res['message']);
            redirect('cbt/konfirmasi/' . encrypt_id($ujian_id));
        }
    }

    public function lembar_ujian($encrypted_peserta_id)
    {
        $peserta_id = decrypt_id($encrypted_peserta_id);
        $peserta = $this->db->get_where('ujian_peserta', array('id' => $peserta_id))->row_array();
        if (!$peserta || $peserta['status'] === 'Selesai') {
            redirect('cbt');
        }

        $ujian = $this->M_Ujian->get_by_id($peserta['ujian_id']);
        $soal_list = $this->M_BankSoal->get_soal_by_bank($ujian['bank_soal_id']);

        // Shuffle questions if is_shuffle enabled
        if ($ujian['is_shuffle']) {
            shuffle($soal_list);
        }

        // Fetch existing answers
        $existing_answers = $this->db->get_where('ujian_jawaban', array('ujian_peserta_id' => $peserta_id))->result_array();
        $jawaban_map = array();
        foreach ($existing_answers as $ans) {
            $jawaban_map[$ans['soal_id']] = $ans['jawaban'];
        }

        $data['title']       = 'Lembar Ujian Online - ' . $ujian['judul_ujian'];
        $data['peserta']     = $peserta;
        $data['ujian']       = $ujian;
        $data['soal_list']   = $soal_list;
        $data['jawaban_map'] = $jawaban_map;

        $this->load->view('cbt/lembar_ujian', $data);
    }

    public function simpan_jawaban_ajax()
    {
        $raw_peserta_id = $this->input->post('peserta_id', true);
        $peserta_id     = decrypt_id($raw_peserta_id);
        $soal_id        = $this->input->post('soal_id', true);
        $jawaban        = $this->input->post('jawaban', true);

        $res = $this->M_Ujian->save_jawaban_item($peserta_id, $soal_id, $jawaban);

        echo json_encode(array('status' => true, 'message' => 'Jawaban tersimpan otomatis.'));
    }

    public function selesai($encrypted_peserta_id)
    {
        $peserta_id = decrypt_id($encrypted_peserta_id);
        $res = $this->M_Ujian->finish_exam($peserta_id);
        
        $data['title'] = 'Ujian Selesai';
        $data['hasil'] = $res;
        
        $this->render_page('cbt/selesai', $data);
    }

    public function ulangi_ujian($encrypted_ujian_id)
    {
        $ujian_id = decrypt_id($encrypted_ujian_id);
        $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
        $murid_id = isset($murid['id']) ? $murid['id'] : null;

        if ($murid_id) {
            $peserta = $this->db->get_where('ujian_peserta', array(
                'ujian_id' => $ujian_id,
                'murid_id' => $murid_id
            ))->row_array();

            if ($peserta) {
                // Delete previous answers and participant attempt
                $this->db->delete('ujian_jawaban', array('ujian_peserta_id' => $peserta['id']));
                $this->db->delete('ujian_peserta', array('id' => $peserta['id']));
            }
        }

        $this->session->set_flashdata('success', 'Sesi pengerjaan Anda telah di-reset. Silakan masukkan token ujian kembali untuk mengulangi ujian.');
        redirect('cbt/konfirmasi/' . encrypt_id($ujian_id));
    }
}

