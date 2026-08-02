<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah'));
        $this->load->model('M_Guru');
    }

    public function index()
    {
        $data['title'] = 'Data Guru & Tenaga Kependidikan';
        $data['guru_list'] = $this->M_Guru->get_all();

        $this->render_page('guru/index', $data);
    }

    public function simpan()
    {
        $user_data = array(
            'username'  => $this->input->post('username', true),
            'email'     => $this->input->post('email', true),
            'password'  => $this->input->post('password', true) ? $this->input->post('password', true) : '12345678',
            'full_name' => $this->input->post('full_name', true),
            'gender'    => $this->input->post('gender', true),
            'phone'     => $this->input->post('phone', true),
            'status'    => 'active'
        );

        $guru_data = array(
            'nip'                 => $this->input->post('nip', true),
            'nuptk'               => $this->input->post('nuptk', true),
            'pendidikan_terakhir' => $this->input->post('pendidikan_terakhir', true),
            'jurusan_pendidikan'  => $this->input->post('jurusan_pendidikan', true),
            'tahun_masuk'         => $this->input->post('tahun_masuk', true) ? $this->input->post('tahun_masuk', true) : date('Y'),
            'status_kepegawaian'  => $this->input->post('status_kepegawaian', true)
        );

        $this->M_Guru->insert($user_data, $guru_data);
        $this->session->set_flashdata('success', 'Data guru berhasil ditambahkan.');
        redirect('guru');
    }
}
