<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(array('url', 'html', 'form', 'turnstile'));
        $this->load->model(array('M_Sekolah', 'M_Ppdb', 'M_Acara', 'M_TahunAjaran', 'M_Eskul', 'M_Kelas', 'M_Mapel'));
    }

    public function index()
    {
        $data['sekolah']   = $this->M_Sekolah->get_school_profile();
        $data['acara']     = $this->M_Acara->get_all(true);
        $data['active_ta'] = $this->M_TahunAjaran->get_active();
        $data['eskul']     = $this->M_Eskul->get_all();
        $data['faqs']      = $this->M_Sekolah->get_faqs();
        $data['fasilitas'] = $this->M_Sekolah->get_fasilitas();

        // High-level statistics automatically calculated from database
        $data['stats'] = array(
            'total_siswa'   => $this->db->where('status_murid', 'Aktif')->count_all_results('murid'),
            'total_guru'    => $this->db->count_all_results('guru'),
            'total_kelas'   => $this->db->count_all_results('kelas'),
            'total_eskul'   => $this->db->count_all_results('eskul'),
            'kelulusan_pct' => 99.8
        );

        $this->load->view('public/home', $data);
    }

    public function daftar_ppdb()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'required');
        $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('telepon', 'Telepon', 'required');

        $turnstile_token = $this->input->post('cf-turnstile-response');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', 'Silakan lengkapi formulir pendaftaran dengan benar.');
            redirect('home#ppdb');
            return;
        }

        if (!verify_turnstile($turnstile_token)) {
            $this->session->set_flashdata('error', 'Verifikasi Keamanan CAPTCHA (Cloudflare Turnstile) gagal atau tidak valid. Silakan centang/verifikasi ulang.');
            redirect('home#ppdb');
            return;
        } else {
            $active_ta = $this->M_TahunAjaran->get_active();
            $no_reg = 'PPDB-' . date('Ymd') . '-' . rand(1000, 9999);
            
            $ppdb_data = array(
                'no_pendaftaran' => $no_reg,
                'nama_lengkap'   => $this->input->post('nama_lengkap', true),
                'nisn'           => $this->input->post('nisn', true),
                'tempat_lahir'   => $this->input->post('tempat_lahir', true),
                'tanggal_lahir'  => $this->input->post('tanggal_lahir', true),
                'jenis_kelamin'  => $this->input->post('jenis_kelamin', true),
                'alamat'         => $this->input->post('alamat', true),
                'telepon'        => $this->input->post('telepon', true),
                'email'          => $this->input->post('email', true),
                'asal_sekolah'   => $this->input->post('asal_sekolah', true),
                'jurusan_dipilih'=> $this->input->post('jurusan_dipilih', true),
                'status'         => 'Daftar',
                'tahun_ajaran_id'=> isset($active_ta['id']) ? $active_ta['id'] : 1
            );

            $this->M_Ppdb->insert($ppdb_data);
            $this->session->set_flashdata('success_ppdb', 'Pendaftaran PPDB Berhasil! Nomor Pendaftaran Anda: <strong>' . $no_reg . '</strong>. Harap simpan nomor ini untuk pengecekan status.');
            redirect('home#cek-status');
        }
    }

    public function cek_status()
    {
        $no_pendaftaran = $this->input->get('no_pendaftaran', true);
        $data['sekolah'] = $this->M_Sekolah->get_school_profile();
        $data['hasil']   = null;

        if ($no_pendaftaran) {
            $data['hasil'] = $this->M_Ppdb->get_by_no_pendaftaran(trim($no_pendaftaran));
        }

        $this->load->view('public/cek_status', $data);
    }
}
