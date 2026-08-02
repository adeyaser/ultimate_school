<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tabungan extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru', 'wali_kelas', 'murid', 'orang_tua'));
        $this->load->model(array('M_Tabungan', 'M_Kelas', 'M_TahunAjaran', 'M_Murid'));
    }

    public function index()
    {
        $user_role = $this->user_data['role'];

        if (in_array($user_role, array('murid', 'orang_tua'))) {
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            $murid_id = isset($murid['id']) ? $murid['id'] : 1;
            redirect('tabungan/transaksi/' . $murid_id);
            return;
        }

        $data['title'] = 'Kelola Tabungan Murid Per Kelas';
        $kelas_id = $this->input->get('kelas_id', true);
        $ta = $this->M_TahunAjaran->get_active();

        $data['kelas_list']     = $this->M_Kelas->get_all();
        $data['selected_kelas'] = $kelas_id;
        $data['tabungan_list']  = array();

        if ($kelas_id) {
            $data['tabungan_list'] = $this->M_Tabungan->get_tabungan_kelas($kelas_id, isset($ta['id']) ? $ta['id'] : 1);
        }

        $this->render_page('tabungan/index', $data);
    }

    public function transaksi($murid_id)
    {
        $user_role = $this->user_data['role'];

        if (in_array($user_role, array('murid', 'orang_tua'))) {
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            $murid_id = isset($murid['id']) ? $murid['id'] : $murid_id;
        }

        $data['title'] = 'Transaksi Tabungan Siswa';
        $ta = $this->M_TahunAjaran->get_active();
        $murid = $this->M_Murid->get_by_id($murid_id);
        
        $tabungan = $this->M_Tabungan->get_or_create_tabungan($murid_id, isset($murid['kelas_id']) ? $murid['kelas_id'] : 1, isset($ta['id']) ? $ta['id'] : 1);

        $data['murid']    = $murid;
        $data['tabungan'] = $tabungan;
        $data['riwayat']  = $this->M_Tabungan->get_riwayat_transaksi($tabungan['id']);

        $this->render_page('tabungan/transaksi', $data);
    }

    public function simpan_transaksi()
    {
        $this->check_role(array('super_admin', 'admin', 'guru', 'wali_kelas'));
        $tabungan_id = $this->input->post('tabungan_id', true);
        $murid_id    = $this->input->post('murid_id', true);
        $jenis       = $this->input->post('jenis', true);
        $nominal     = $this->input->post('nominal', true);
        $keterangan  = $this->input->post('keterangan', true);

        $this->M_Tabungan->tambah_transaksi($tabungan_id, $jenis, $nominal, $keterangan, $this->user_data['id']);
        $this->session->set_flashdata('success', 'Transaksi tabungan berhasil dicatat.');
        redirect('tabungan/transaksi/' . $murid_id);
    }
}
