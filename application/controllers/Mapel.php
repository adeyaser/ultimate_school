<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapel extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru', 'wali_kelas', 'murid'));
        $this->load->model(array('M_Mapel', 'M_Guru', 'M_Kelas', 'M_TahunAjaran', 'M_Murid'));
    }

    public function index()
    {
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru'));
        $data['title'] = 'Mata Pelajaran & KKM';

        $active_jenjang = $this->session->userdata('active_jenjang');
        if (empty($active_jenjang)) {
            $active_jenjang = isset($this->school_info['jenjang']) ? $this->school_info['jenjang'] : 'SMP';
        }

        $data['active_jenjang'] = $active_jenjang;
        $data['mapel_list']     = $this->M_Mapel->get_all($active_jenjang);

        $this->render_page('mapel/index', $data);
    }

    public function simpan()
    {
        $this->check_role(array('super_admin', 'admin'));

        $active_jenjang = $this->session->userdata('active_jenjang');
        if (empty($active_jenjang) || $active_jenjang === 'ALL') {
            $active_jenjang = isset($this->school_info['jenjang']) ? $this->school_info['jenjang'] : 'SMP';
        }

        $data = array(
            'kode_mapel'     => $this->input->post('kode_mapel', true),
            'nama_mapel'     => $this->input->post('nama_mapel', true),
            'jenjang'        => $active_jenjang,
            'kelompok'       => $this->input->post('kelompok', true),
            'jam_per_minggu' => $this->input->post('jam_per_minggu', true),
            'kkm'            => $this->input->post('kkm', true) ? $this->input->post('kkm', true) : 75,
            'deskripsi'      => $this->input->post('deskripsi', true)
        );

        $this->M_Mapel->insert($data);
        $this->session->set_flashdata('success', 'Mata pelajaran berhasil ditambahkan.');
        redirect('mapel');
    }

    public function jadwal()
    {
        $data['title']      = 'Jadwal Pelajaran Kelas';
        $kelas_list         = $this->M_Kelas->get_all();
        $data['kelas_list'] = $kelas_list;

        $kelas_id = $this->input->get('kelas_id', true);

        // Strictly lock class for Murid & Orang Tua roles (Cannot switch class)
        if (in_array($this->user_data['role'], array('murid', 'orang_tua'))) {
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            if ($murid && !empty($murid['kelas_id'])) {
                $kelas_id = $murid['kelas_id']; // Strictly overrides any $_GET attempt!
            }
        }

        // Default to first class if not selected (for Admin/Teachers)
        if (empty($kelas_id) && !empty($kelas_list)) {
            $kelas_id = $kelas_list[0]['id'];
        }

        $ta = $this->M_TahunAjaran->get_active();

        $data['mapel_list']      = $this->M_Mapel->get_all();
        $data['guru_list']       = $this->M_Guru->get_all();
        $data['selected_kelas']  = $kelas_id;
        $data['selected_class']  = $this->M_Kelas->get_by_id($kelas_id);
        $data['jadwal_list']     = $this->M_Mapel->get_jadwal($kelas_id, isset($ta['id']) ? $ta['id'] : 1);

        $this->render_page('mapel/jadwal', $data);
    }

    public function simpan_jadwal()
    {
        $this->check_role(array('super_admin', 'admin', 'guru'));
        $ta = $this->M_TahunAjaran->get_active();
        $data = array(
            'kelas_id'          => $this->input->post('kelas_id', true),
            'mata_pelajaran_id' => $this->input->post('mata_pelajaran_id', true),
            'guru_id'           => $this->input->post('guru_id', true),
            'tahun_ajaran_id'   => isset($ta['id']) ? $ta['id'] : 1,
            'hari'              => $this->input->post('hari', true),
            'jam_mulai'         => $this->input->post('jam_mulai', true),
            'jam_selesai'       => $this->input->post('jam_selesai', true),
            'ruangan'           => $this->input->post('ruangan', true)
        );

        $res = $this->M_Mapel->save_jadwal($data);
        if ($res === 'updated') {
            $this->session->set_flashdata('success', 'Entri jadwal pelajaran hari ' . $data['hari'] . ' (' . $data['jam_mulai'] . ') berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('success', 'Jadwal pelajaran baru berhasil ditambahkan.');
        }

        redirect('mapel/jadwal?kelas_id=' . $data['kelas_id']);
    }

    public function hapus_jadwal($id)
    {
        $this->check_role(array('super_admin', 'admin', 'guru'));
        $kelas_id = $this->input->get('kelas_id', true);
        $this->M_Mapel->delete_jadwal($id);
        $this->session->set_flashdata('success', 'Entri jadwal pelajaran berhasil dihapus.');
        redirect('mapel/jadwal' . ($kelas_id ? '?kelas_id=' . $kelas_id : ''));
    }
}

