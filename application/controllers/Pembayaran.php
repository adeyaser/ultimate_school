<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru', 'wali_kelas', 'murid', 'orang_tua'));
        $this->load->model(array('M_Pembayaran', 'M_Murid'));
    }

    public function index()
    {
        $data['title'] = 'Administrasi Keuangan & Pembayaran SPP';
        $user_role = $this->user_data['role'];
        $murid_id = null;

        if (in_array($user_role, array('murid', 'orang_tua'))) {
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            $murid_id = isset($murid['id']) ? $murid['id'] : null;
            $data['murid_list']   = array();
        } else {
            $data['murid_list']   = $this->M_Murid->get_all();
        }

        $data['tagihan_list'] = $this->M_Pembayaran->get_all_tagihan($murid_id);

        $this->render_page('pembayaran/index', $data);
    }

    public function buat_tagihan()
    {
        $this->check_role(array('super_admin', 'admin'));
        $data = array(
            'murid_id'            => $this->input->post('murid_id', true),
            'jenis'               => $this->input->post('jenis', true),
            'bulan'               => $this->input->post('bulan', true),
            'nominal'             => $this->input->post('nominal', true),
            'tanggal_jatuh_tempo' => $this->input->post('tanggal_jatuh_tempo', true),
            'keterangan'          => $this->input->post('keterangan', true)
        );

        $this->M_Pembayaran->create_tagihan($data);
        $this->session->set_flashdata('success', 'Tagihan pembayaran berhasil dibuat.');
        redirect('pembayaran');
    }

    public function bayar()
    {
        $this->check_role(array('super_admin', 'admin'));
        $pembayaran_id = $this->input->post('pembayaran_id', true);
        $jumlah_bayar  = $this->input->post('jumlah_bayar', true);
        $metode        = $this->input->post('metode', true);
        $keterangan    = $this->input->post('keterangan', true);

        $this->M_Pembayaran->bayar_tagihan($pembayaran_id, $jumlah_bayar, $metode, $keterangan, $this->user_data['id']);
        $this->session->set_flashdata('success', 'Pembayaran berhasil dicatat.');
        redirect('pembayaran');
    }

    public function kuitansi($pembayaran_id)
    {
        $data['school_info'] = $this->school_info;
        $tagihan = $this->M_Pembayaran->get_by_id($pembayaran_id);

        if (!$tagihan) {
            show_404();
        }

        if (in_array($this->user_data['role'], array('murid', 'orang_tua'))) {
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            if (isset($murid['id']) && $tagihan['murid_id'] != $murid['id']) {
                show_404();
            }
        }

        $data['tagihan']     = $tagihan;
        $data['detail_list'] = $this->M_Pembayaran->get_detail_pembayaran($pembayaran_id);

        $this->load->view('pembayaran/kuitansi', $data);
    }
}
