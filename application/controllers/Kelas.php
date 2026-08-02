<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru'));
        $this->load->model(array('M_Kelas', 'M_Guru', 'M_TahunAjaran', 'M_Sekolah'));
    }

    public function index()
    {
        $data['title']      = 'Data Rombongan Belajar / Kelas';
        $data['kelas_list'] = $this->M_Kelas->get_all();
        $data['guru_list']  = $this->M_Guru->get_all();
        $data['ta_active']  = $this->M_TahunAjaran->get_active();
        $data['sekolah']    = $this->M_Sekolah->get_school_profile();

        $this->render_page('kelas/index', $data);
    }

    public function simpan()
    {
        $ta = $this->M_TahunAjaran->get_active();

        $sek = $this->M_Sekolah->get_school_profile();
        $jenjang = isset($sek['jenjang']) ? $sek['jenjang'] : 'SMP';

        $data = array(
            'nama_kelas' => $this->input->post('nama_kelas', true),
            'jenjang'    => $jenjang,
            'tingkat'    => $this->input->post('tingkat', true),
            'jurusan'    => $this->input->post('jurusan', true),
            'ruangan'    => $this->input->post('ruangan', true),
            'kapasitas'  => $this->input->post('kapasitas', true) ? $this->input->post('kapasitas', true) : 36,
            'wali_kelas_id' => $this->input->post('wali_kelas_id', true),
            'tahun_ajaran_id' => isset($ta['id']) ? $ta['id'] : 1,
            'is_active'  => 1
        );

        $this->M_Kelas->insert($data);
        $this->session->set_flashdata('success', 'Data kelas berhasil ditambahkan.');
        redirect('kelas');
    }
}
