<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru', 'wali_kelas', 'murid', 'orang_tua'));
        $this->load->model(array('M_Absensi', 'M_Kelas', 'M_Murid', 'M_Guru'));
    }

    public function index()
    {
        $data['title'] = 'Presensi Siswa Harian';
        $user_role = $this->user_data['role'];

        if (in_array($user_role, array('murid', 'orang_tua'))) {
            // Student / Parent Isolation View
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            $data['is_student_view'] = true;
            $data['my_student']      = $murid;
            $data['self_absensi']    = $murid ? $this->M_Absensi->get_absensi_self($murid['id']) : array();
        } else {
            // Admin / Teacher View
            $kelas_id = $this->input->get('kelas_id', true);
            $tanggal  = $this->input->get('tanggal', true) ? $this->input->get('tanggal', true) : date('Y-m-d');

            $data['is_student_view'] = false;
            $data['kelas_list']      = $this->M_Kelas->get_all();
            $data['selected_kelas']  = $kelas_id;
            $data['tanggal']         = $tanggal;
            $data['murid_list']      = array();

            if ($kelas_id) {
                $data['murid_list']  = $this->M_Absensi->get_absensi_murid($kelas_id, $tanggal);
            }
        }

        $this->render_page('absensi/index', $data);
    }

    public function simpan()
    {
        $this->check_role(array('super_admin', 'admin', 'guru', 'wali_kelas'));
        $kelas_id   = $this->input->post('kelas_id', true);
        $tanggal    = $this->input->post('tanggal', true);
        $status_arr = $this->input->post('status', true);
        $ket_arr    = $this->input->post('keterangan', true);

        if (!empty($status_arr)) {
            foreach ($status_arr as $murid_id => $status) {
                $data = array(
                    'murid_id'   => $murid_id,
                    'kelas_id'   => $kelas_id,
                    'tanggal'    => $tanggal,
                    'status'     => $status,
                    'jam_datang' => date('H:i:s'),
                    'keterangan' => isset($ket_arr[$murid_id]) ? $ket_arr[$murid_id] : '',
                    'input_by'   => $this->user_data['id']
                );
                $this->M_Absensi->save_absensi_murid($data);
            }
            $this->session->set_flashdata('success', 'Data presensi murid berhasil disimpan.');
        }

        redirect('absensi?kelas_id=' . $kelas_id . '&tanggal=' . $tanggal);
    }

    // Presensi Guru & Staf
    public function guru()
    {
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah'));
        $data['title']   = 'Presensi & Absensi Guru & Staf';
        $tanggal         = $this->input->get('tanggal', true) ? $this->input->get('tanggal', true) : date('Y-m-d');
        $data['tanggal'] = $tanggal;
        $data['guru_list'] = $this->M_Absensi->get_absensi_guru($tanggal);

        $this->render_page('absensi/guru', $data);
    }

    public function simpan_guru()
    {
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah'));
        $tanggal    = $this->input->post('tanggal', true);
        $status_arr = $this->input->post('status', true);
        $datang_arr = $this->input->post('jam_datang', true);
        $pulang_arr = $this->input->post('jam_pulang', true);
        $ket_arr    = $this->input->post('keterangan', true);

        if (!empty($status_arr)) {
            foreach ($status_arr as $guru_id => $status) {
                $data = array(
                    'guru_id'    => $guru_id,
                    'tanggal'    => $tanggal,
                    'status'     => $status,
                    'jam_datang' => isset($datang_arr[$guru_id]) && !empty($datang_arr[$guru_id]) ? $datang_arr[$guru_id] : '07:30:00',
                    'jam_pulang' => isset($pulang_arr[$guru_id]) && !empty($pulang_arr[$guru_id]) ? $pulang_arr[$guru_id] : '15:00:00',
                    'keterangan' => isset($ket_arr[$guru_id]) ? $ket_arr[$guru_id] : ''
                );
                $this->M_Absensi->save_absensi_guru($data);
            }
            $this->session->set_flashdata('success', 'Data presensi guru berhasil disimpan.');
        }

        redirect('absensi/guru?tanggal=' . $tanggal);
    }
}
