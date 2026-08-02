<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_auth();
        $this->load->model(array('M_Murid', 'M_Guru', 'M_Kelas', 'M_Absensi'));
    }

    public function index()
    {
        $data['title'] = 'Dashboard Utama';
        $user_id   = $this->user_data['id'];
        $user_role = $this->user_data['role'];

        if ($user_role === 'murid') {
            // 1. DATA DASHBOARD MURID
            $murid = $this->M_Murid->get_by_user_id($user_id);
            $data['murid']          = $murid;
            $data['saldo_tabungan'] = 0;
            $data['status_spp']     = 'Lunas';
            $data['jadwal_today']   = array();
            $data['ujian_aktif']    = array();
            $data['tugas_list']     = array();

            if ($murid && isset($murid['id'])) {
                // Tabungan
                $tabungan = $this->db->get_where('tabungan', array('murid_id' => $murid['id']))->row_array();
                $data['saldo_tabungan'] = ($tabungan && isset($tabungan['saldo'])) ? $tabungan['saldo'] : 0;

                // Status SPP Bulan Ini
                $spp = $this->db->get_where('pembayaran', array('murid_id' => $murid['id'], 'jenis' => 'SPP'))->row_array();
                $data['status_spp'] = ($spp && isset($spp['status'])) ? $spp['status'] : 'Lunas';

                // Today's Class Schedule
                $hari_ini = date('N'); // 1 = Senin, 7 = Minggu
                $hari_map = array(1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu');
                $hari_nama = isset($hari_map[$hari_ini]) ? $hari_map[$hari_ini] : 'Senin';

                if (!empty($murid['kelas_id'])) {
                    $this->db->select('jadwal.*, mata_pelajaran.nama_mapel, users.full_name as nama_guru');
                    $this->db->from('jadwal');
                    $this->db->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mata_pelajaran_id');
                    $this->db->join('guru', 'guru.id = jadwal.guru_id', 'left');
                    $this->db->join('users', 'users.id = guru.user_id', 'left');
                    $this->db->where('jadwal.kelas_id', $murid['kelas_id']);
                    $this->db->where('jadwal.hari', $hari_nama);
                    $data['jadwal_today'] = $this->db->get()->result_array();

                    // Active CBT Exams
                    $this->db->select('ujian.*, mata_pelajaran.nama_mapel');
                    $this->db->from('ujian');
                    $this->db->join('mata_pelajaran', 'mata_pelajaran.id = ujian.mata_pelajaran_id');
                    $this->db->where('ujian.kelas_id', $murid['kelas_id']);
                    $this->db->where('ujian.is_active', 1);
                    $data['ujian_aktif'] = $this->db->get()->result_array();

                    // Homework Assignments
                    $this->db->select('tugas.*, mata_pelajaran.nama_mapel');
                    $this->db->from('tugas');
                    $this->db->join('mata_pelajaran', 'mata_pelajaran.id = tugas.mata_pelajaran_id');
                    $this->db->where('tugas.kelas_id', $murid['kelas_id']);
                    $this->db->order_by('tugas.deadline', 'ASC');
                    $this->db->limit(5);
                    $data['tugas_list'] = $this->db->get()->result_array();
                }
            }
        } elseif ($user_role === 'guru' || $user_role === 'wali_kelas') {
            // 2. DATA DASHBOARD GURU
            $guru = $this->M_Guru->get_by_user_id($user_id);
            $data['guru'] = $guru;

            if ($guru) {
                // Classes taught
                $data['my_classes'] = $this->db->get_where('kelas', array('wali_kelas_id' => $user_id))->result_array();
                $data['total_tugas'] = $this->db->get_where('tugas', array('guru_id' => $guru['id']))->num_rows();
                $data['total_ujian'] = $this->db->get_where('ujian', array('guru_id' => $guru['id']))->num_rows();

                // Today's Teaching Schedule
                $hari_ini = date('N');
                $hari_map = array(1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu');
                $hari_nama = isset($hari_map[$hari_ini]) ? $hari_map[$hari_ini] : 'Senin';

                $this->db->select('jadwal.*, mata_pelajaran.nama_mapel, kelas.nama_kelas');
                $this->db->from('jadwal');
                $this->db->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mata_pelajaran_id');
                $this->db->join('kelas', 'kelas.id = jadwal.kelas_id');
                $this->db->where('jadwal.guru_id', $guru['id']);
                $this->db->where('jadwal.hari', $hari_nama);
                $this->db->order_by('jadwal.jam_mulai', 'ASC');
                $jadwal_today = $this->db->get()->result_array();

                if (!empty($jadwal_today)) {
                    $data['jadwal_mengajar'] = $jadwal_today;
                    $data['is_fallback_schedule'] = false;
                } else {
                    // Fallback to all weekly teaching schedules for this teacher
                    $this->db->select('jadwal.*, mata_pelajaran.nama_mapel, kelas.nama_kelas');
                    $this->db->from('jadwal');
                    $this->db->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mata_pelajaran_id');
                    $this->db->join('kelas', 'kelas.id = jadwal.kelas_id');
                    $this->db->where('jadwal.guru_id', $guru['id']);
                    $this->db->order_by('FIELD(jadwal.hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu")');
                    $this->db->order_by('jadwal.jam_mulai', 'ASC');
                    $data['jadwal_mengajar'] = $this->db->get()->result_array();
                    $data['is_fallback_schedule'] = true;
                }
            }
        } else {
            // 3. DATA DASHBOARD ADMIN & SUPER ADMIN
            $data['total_siswa'] = $this->db->where('status_murid', 'Aktif')->count_all_results('murid');
            $data['total_guru']  = $this->db->count_all_results('guru');
            $data['total_kelas'] = $this->db->count_all_results('kelas');
            $data['total_ppdb']  = $this->db->count_all_results('pendaftaran_ppdb');

            // Recent students & events
            $this->db->select('murid.*, users.full_name, kelas.nama_kelas');
            $this->db->from('murid');
            $this->db->join('users', 'users.id = murid.user_id');
            $this->db->join('kelas', 'kelas.id = murid.kelas_id', 'left');
            $this->db->order_by('murid.id', 'DESC');
            $this->db->limit(5);
            $data['recent_students'] = $this->db->get()->result_array();
        }

        $this->render_page('dashboard', $data);
    }
}
