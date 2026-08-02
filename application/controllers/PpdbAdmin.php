<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PpdbAdmin extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah'));
        $this->load->model(array('M_Ppdb', 'M_Murid', 'M_Kelas', 'M_TahunAjaran'));
    }

    public function index()
    {
        $data['title'] = 'Kelola Penerimaan Murid Baru (PPDB)';
        
        $jenjang_filter = $this->input->get('jenjang', true);
        if (empty($jenjang_filter)) {
            $jenjang_filter = $this->session->userdata('active_jenjang');
        }
        if (empty($jenjang_filter)) {
            $jenjang_filter = isset($this->school_info['jenjang']) ? $this->school_info['jenjang'] : 'SMP';
        }

        $data['selected_jenjang'] = $jenjang_filter;
        $data['ppdb_list']        = $this->M_Ppdb->get_all(null, $jenjang_filter);
        $data['kelas_list']       = $this->M_Kelas->get_all(null, $jenjang_filter);

        $this->render_page('ppdb/index', $data);
    }

    public function update_status($id)
    {
        $status = $this->input->post('status', true);
        $catatan = $this->input->post('catatan', true);

        $this->M_Ppdb->update($id, array('status' => $status, 'catatan' => $catatan));

        // If status converted to 'Daftar Ulang' or 'Lulus', create student account automatically
        if ($status === 'Lulus' || $status === 'Daftar Ulang') {
            $pendaftar = $this->M_Ppdb->get_by_id($id);
            $kelas_id = $this->input->post('kelas_id', true) ? $this->input->post('kelas_id', true) : 1;

            $user_data = array(
                'username'  => strtolower(str_replace(' ', '', $pendaftar['nama_lengkap'])) . rand(10, 99),
                'email'     => $pendaftar['email'] ? $pendaftar['email'] : 'siswa' . rand(1000, 9999) . '@ultimateschool.com',
                'password'  => '12345678',
                'full_name' => $pendaftar['nama_lengkap'],
                'gender'    => $pendaftar['jenis_kelamin'],
                'phone'     => $pendaftar['telepon'],
                'address'   => $pendaftar['alamat'],
                'status'    => 'active'
            );

            $murid_data = array(
                'nisn' => $pendaftar['nisn'] ? $pendaftar['nisn'] : '00' . rand(10000000, 99999999),
                'nis'  => 'NIS-' . date('Y') . '-' . rand(100, 999),
                'kelas_id' => $kelas_id,
                'tahun_ajaran_id' => $pendaftar['tahun_ajaran_id'],
                'tempat_lahir' => $pendaftar['tempat_lahir'],
                'tanggal_lahir' => $pendaftar['tanggal_lahir'],
                'agama' => 'Islam',
                'alamat_tinggal' => $pendaftar['alamat'],
                'status_murid' => 'Aktif',
                'tanggal_masuk' => date('Y-m-d')
            );

            $this->M_Murid->insert($user_data, $murid_data);
        }

        $this->session->set_flashdata('success', 'Status pendaftaran PPDB diperbarui.');
        redirect('ppdbadmin');
    }
}
