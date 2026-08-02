<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Eskul extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru', 'wali_kelas', 'murid', 'orang_tua'));
        $this->load->model(array('M_Eskul', 'M_Guru', 'M_Murid'));
    }

    public function index()
    {
        $data['title'] = 'Kegiatan Ekstrakurikuler (Eskul)';
        $user_role = $this->user_data['role'];

        $data['eskul_list'] = $this->M_Eskul->get_all();
        $data['guru_list']  = $this->M_Guru->get_all();

        if (in_array($user_role, array('murid', 'orang_tua'))) {
            $data['murid_list'] = array();
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            $data['my_student'] = $murid;
        } else {
            $data['murid_list'] = $this->M_Murid->get_all();
        }

        $this->render_page('eskul/index', $data);
    }

    public function simpan()
    {
        $this->check_role(array('super_admin', 'admin', 'guru'));
        $data = array(
            'kode_eskul'  => 'ESKUL-' . strtoupper(substr($this->input->post('nama_eskul', true), 0, 3)) . '-' . rand(10, 99),
            'nama_eskul'  => $this->input->post('nama_eskul', true),
            'pembina_id'  => $this->input->post('pembina_id', true),
            'hari'        => $this->input->post('hari', true),
            'jam_mulai'   => $this->input->post('jam_mulai', true),
            'jam_selesai' => $this->input->post('jam_selesai', true),
            'tempat'      => $this->input->post('tempat', true),
            'kuota'       => $this->input->post('kuota', true) ? $this->input->post('kuota', true) : 30,
            'deskripsi'   => $this->input->post('deskripsi', true)
        );

        $this->M_Eskul->insert($data);
        $this->session->set_flashdata('success', 'Kegiatan eskul berhasil ditambahkan.');
        redirect('eskul');
    }

    public function daftar_peserta()
    {
        $eskul_id = $this->input->post('eskul_id', true);
        $user_role = $this->user_data['role'];

        if (in_array($user_role, array('murid', 'orang_tua'))) {
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            $murid_id = isset($murid['id']) ? $murid['id'] : 0;
        } else {
            $murid_id = $this->input->post('murid_id', true);
        }

        if ($murid_id) {
            $this->M_Eskul->enroll_peserta($eskul_id, $murid_id);
            $this->session->set_flashdata('success', 'Pendaftaran eskul berhasil.');
        }

        redirect('eskul');
    }
}
