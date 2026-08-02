<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Struktur extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru', 'wali_kelas'));
        $this->load->model(array('M_Organisasi', 'M_Users', 'M_Kelas'));
    }

    public function index()
    {
        $data['title'] = 'Struktur Organisasi Sekolah & Kelas';
        $level    = $this->input->get('level', true) ? $this->input->get('level', true) : 'Sekolah';
        $kelas_id = $this->input->get('kelas_id', true);

        $data['level']        = $level;
        $data['selected_kelas'] = $kelas_id;
        $data['kelas_list']   = $this->M_Kelas->get_all();
        $data['users_list']   = $this->M_Users->get_all();
        $data['struktur']     = $this->M_Organisasi->get_by_level($level, $kelas_id);

        $this->render_page('struktur/index', $data);
    }

    public function simpan()
    {
        $data = array(
            'jabatan'   => $this->input->post('jabatan', true),
            'level'     => $this->input->post('level', true),
            'user_id'   => $this->input->post('user_id', true),
            'kelas_id'  => $this->input->post('kelas_id', true) ? $this->input->post('kelas_id', true) : null,
            'urutan'    => $this->input->post('urutan', true) ? $this->input->post('urutan', true) : 1,
            'is_active' => 1
        );

        $this->M_Organisasi->save($data);
        $this->session->set_flashdata('success', 'Struktur organisasi berhasil disimpan.');
        redirect('struktur?level=' . $data['level']);
    }
}
