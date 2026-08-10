<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kuislatihan extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('murid', 'orang_tua', 'super_admin', 'admin', 'guru', 'wali_kelas'));
        $this->load->model(array('M_KuisLatihan', 'M_BankSoal', 'M_Mapel', 'M_Murid'));
    }

    public function index()
    {
        $data['title'] = 'Latihan Soal Mandiri & Smart Suggestion';
        $data['mapel_list'] = $this->M_Mapel->get_all();
        $data['bank_list']  = $this->M_BankSoal->get_all();

        $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
        $murid_id = isset($murid['id']) ? $murid['id'] : 1;

        // History of practice quizzes with Joined Mapel Name
        $data['history'] = $this->M_KuisLatihan->get_history($murid_id);

        $this->render_page('kuis/index', $data);
    }

    public function mulai()
    {
        $mode = $this->input->post('mode', true);
        $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
        $murid_id = isset($murid['id']) ? $murid['id'] : 1;

        if ($mode === 'ai') {
            $mapel_id          = $this->input->post('mata_pelajaran_id', true);
            $topik             = $this->input->post('topik', true);
            $jumlah_soal       = (int)$this->input->post('jumlah_soal', true);
            $tingkat_kesulitan = $this->input->post('tingkat_kesulitan', true);

            if (empty($topik)) {
                $this->session->set_flashdata('error', 'Topik/materi kuis wajib diisi untuk mode AI.');
                redirect('kuislatihan');
                return;
            }

            if ($jumlah_soal <= 0 || $jumlah_soal > 20) $jumlah_soal = 5;
            if (empty($tingkat_kesulitan)) $tingkat_kesulitan = 'Sedang';

            $kuis_id = $this->M_KuisLatihan->generate_ai_kuis($murid_id, $mapel_id, $topik, $jumlah_soal, $tingkat_kesulitan);

            if ($kuis_id) {
                redirect('kuislatihan/kerjakan/' . $kuis_id);
            } else {
                $this->session->set_flashdata('error', 'Gagal memproses pembuatan soal AI. Silakan periksa koneksi atau coba topik lain.');
                redirect('kuislatihan');
            }
        } elseif ($mode === 'kustom') {
            $bank_soal_id = $this->input->post('bank_soal_id', true);
            $jumlah_soal  = (int)$this->input->post('jumlah_soal', true);
            if ($jumlah_soal <= 0) $jumlah_soal = 10;

            $kuis_id = $this->M_KuisLatihan->generate_kuis_from_bank($murid_id, $bank_soal_id, $jumlah_soal);

            if ($kuis_id) {
                redirect('kuislatihan/kerjakan/' . $kuis_id);
            } else {
                $this->session->set_flashdata('error', 'Paket Bank Soal kustom ini belum memiliki butir soal yang siap dikerjakan.');
                redirect('kuislatihan');
            }
        } else {
            // Mode Gudang Soal (Default)
            $mapel_id = $this->input->post('mata_pelajaran_id', true);
            $jumlah_soal = $this->input->post('jumlah_soal', true) ? $this->input->post('jumlah_soal', true) : 10;

            $kuis_id = $this->M_KuisLatihan->generate_kuis($murid_id, $mapel_id, $jumlah_soal);

            if ($kuis_id) {
                redirect('kuislatihan/kerjakan/' . $kuis_id);
            } else {
                $this->session->set_flashdata('error', 'Belum ada soal terpublikasi untuk mata pelajaran ini di bank soal.');
                redirect('kuislatihan');
            }
        }
    }

    public function kerjakan($kuis_id)
    {
        $kuis = $this->M_KuisLatihan->get_kuis($kuis_id);
        if (!$kuis || $kuis['status'] === 'Selesai') {
            redirect('kuislatihan');
        }

        $soal_list = $this->M_KuisLatihan->get_soal_kuis($kuis['soal_ids'], $kuis_id);

        $data['title']     = 'Kuis Latihan - ' . $kuis['nama_mapel'];
        $data['kuis']      = $kuis;
        $data['soal_list'] = $soal_list;

        $this->load->view('kuis/latihan', $data);
    }

    public function submit()
    {
        $kuis_id = $this->input->post('kuis_id', true);
        $jawaban_map = $this->input->post('jawaban', true);

        $score = $this->M_KuisLatihan->submit_kuis($kuis_id, $jawaban_map);
        
        redirect('kuislatihan/hasil/' . $kuis_id);
    }

    public function hasil($kuis_id)
    {
        $data['title'] = 'Hasil & Pembahasan Kuis Latihan';
        $data['kuis']  = $this->M_KuisLatihan->get_kuis($kuis_id);
        
        $data['soal_list'] = $this->M_KuisLatihan->get_soal_kuis($data['kuis']['soal_ids']);
        
        $answers = $this->db->get_where('kuis_latihan_jawaban', array('kuis_latihan_id' => $kuis_id))->result_array();
        $user_ans = array();
        foreach ($answers as $a) {
            $user_ans[$a['soal_id']] = $a;
        }
        $data['user_ans'] = $user_ans;

        $this->render_page('kuis/hasil', $data);
    }
}
