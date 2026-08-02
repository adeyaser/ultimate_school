<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Akademik extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru', 'wali_kelas', 'murid', 'orang_tua'));
        $this->load->model(array('M_Akademik', 'M_Kelas', 'M_Mapel', 'M_Murid', 'M_TahunAjaran', 'M_Guru'));
    }

    public function tugas()
    {
        $data['title'] = 'Kelola Tugas Murid';
        $user_role = $this->user_data['role'];
        $kelas_id  = $this->input->get('kelas_id', true);
        $mapel_id  = $this->input->get('mata_pelajaran_id', true);
        $murid_id  = 0;

        $active_jenjang = $this->session->userdata('active_jenjang');
        if (empty($active_jenjang)) {
            $active_jenjang = isset($this->school_info['jenjang']) ? $this->school_info['jenjang'] : 'SMP';
        }

        if (in_array($user_role, array('murid', 'orang_tua'))) {
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            $kelas_id = isset($murid['kelas_id']) ? $murid['kelas_id'] : 0;
            $murid_id = isset($murid['id']) ? $murid['id'] : 0;
        }

        $data['active_jenjang']  = $active_jenjang;
        $data['kelas_list']      = $this->M_Kelas->get_all(null, $active_jenjang);
        $data['mapel_list']      = $this->M_Mapel->get_all($active_jenjang);
        $data['selected_kelas']  = $kelas_id;
        $data['selected_mapel']  = $mapel_id;
        $data['tugas_list']      = $this->M_Akademik->get_tugas($kelas_id, $mapel_id);
        $data['murid_id']        = $murid_id;

        // Get student's submissions for status tracking
        if ($murid_id) {
            $submissions = $this->M_Akademik->get_pengumpulan_by_murid($murid_id);
            $data['my_submissions'] = array();
            foreach ($submissions as $s) {
                $data['my_submissions'][$s['tugas_id']] = $s;
            }
        } else {
            $data['my_submissions'] = array();
        }

        $this->render_page('akademik/tugas', $data);
    }

    public function tugas_json()
    {
        $user_role = $this->user_data['role'];
        $is_admin_or_teacher = in_array($user_role, array('super_admin', 'admin', 'guru', 'wali_kelas', 'kepala_sekolah'));

        $kelas_id = $this->input->get('kelas_id', true);
        $mapel_id = $this->input->get('mata_pelajaran_id', true);

        $active_jenjang = $this->session->userdata('active_jenjang');
        if (empty($active_jenjang)) {
            $active_jenjang = isset($this->school_info['jenjang']) ? $this->school_info['jenjang'] : 'SMP';
        }

        if (in_array($user_role, array('murid', 'orang_tua'))) {
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            $kelas_id = isset($murid['kelas_id']) ? $murid['kelas_id'] : 0;
            $murid_id = isset($murid['id']) ? $murid['id'] : 0;
            $my_submissions_arr = array();
            if ($murid_id) {
                $submissions = $this->M_Akademik->get_pengumpulan_by_murid($murid_id);
                foreach ($submissions as $s) {
                    $my_submissions_arr[$s['tugas_id']] = $s;
                }
            }
        } else {
            $murid_id = 0;
            $my_submissions_arr = array();
        }

        $draw   = intval($this->input->get('draw'));
        $start  = intval($this->input->get('start'));
        $length = intval($this->input->get('length'));
        $search = $this->input->get('search')['value'] ?? null;

        $columns = array('tugas.id', 'tugas.judul', 'mata_pelajaran.nama_mapel', 'kelas.nama_kelas', 'users.full_name', 'tugas.deadline', 'tugas.bobot', 'tugas.id');
        $order_idx = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
        $order_col = $columns[$order_idx] ?? 'tugas.id';
        $order_dir = isset($_GET['order'][0]['dir']) && $_GET['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC';

        $recordsTotal = $this->M_Akademik->count_tugas_all($kelas_id, $mapel_id, $active_jenjang);
        $recordsFiltered = $this->M_Akademik->count_tugas_filtered($kelas_id, $mapel_id, $search, $active_jenjang);
        $list = $this->M_Akademik->get_tugas_datatables($start, $length, $kelas_id, $mapel_id, $search, $order_col, $order_dir, $active_jenjang);

        $data = array();
        $no = $start + 1;

        foreach ($list as $t) {
            $is_expired = (strtotime($t['deadline']) < strtotime(date('Y-m-d')));
            $dl_badge = $is_expired ? 'text-bg-danger' : 'text-bg-warning text-dark';

            $judul_html = '<strong class="text-dark fs-6 d-block">' . htmlspecialchars($t['judul']) . '</strong>' .
                           '<small class="text-muted">' . ($t['deskripsi'] ? htmlspecialchars(mb_substr(strip_tags($t['deskripsi']), 0, 70)) . '...' : 'Tidak ada instruksi khusus.') . '</small>';
            
            $mapel_html = '<span class="badge text-bg-primary fs-6">' . htmlspecialchars($t['nama_mapel']) . '</span>';
            $kelas_html = '<span class="badge text-bg-info text-white fs-6">' . htmlspecialchars($t['nama_kelas']) . '</span>';
            $guru_html  = '<i class="bi bi-person-circle text-success me-1"></i> ' . htmlspecialchars($t['nama_guru'] ? $t['nama_guru'] : 'Guru Pengampu');
            $dl_html    = '<span class="badge ' . $dl_badge . ' px-3 py-1 fs-6"><i class="bi bi-clock me-1"></i> ' . date('d M Y', strtotime($t['deadline'])) . '</span>';
            $bobot_html = '<span class="badge text-bg-secondary fs-6">' . $t['bobot'] . ' Poin</span>';

            if ($is_admin_or_teacher) {
                $aksi = '<div class="btn-group" role="group">
                    <a href="' . base_url('akademik/lihat_pengumpulan/' . $t['id']) . '" class="btn btn-sm btn-success" title="Lihat Pengumpulan"><i class="bi bi-people-fill me-1"></i> Pengumpulan</a>
                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalDetailTugas_' . $t['id'] . '" title="Lihat Detail"><i class="bi bi-eye-fill"></i></button>
                    <button type="button" class="btn btn-sm btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#modalEditTugas_' . $t['id'] . '" title="Edit Tugas"><i class="bi bi-pencil-square"></i></button>
                    <a href="' . base_url('akademik/hapus_tugas/' . $t['id']) . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus tugas ini?\')" title="Hapus Tugas"><i class="bi bi-trash-fill"></i></a>
                </div>';
            } else {
                $submitted = isset($my_submissions_arr[$t['id']]);
                if ($submitted) {
                    $sub = $my_submissions_arr[$t['id']];
                    if ($sub['status'] == 'Dinilai') {
                        $aksi = '<span class="badge text-bg-success px-3 py-2 fs-6"><i class="bi bi-check-circle-fill me-1"></i> Dinilai: ' . $sub['nilai'] . '</span>';
                    } else {
                        $aksi = '<span class="badge text-bg-info text-white px-3 py-2 fs-6"><i class="bi bi-hourglass-split me-1"></i> Menunggu Penilaian</span>';
                    }
                } else {
                    $aksi = '<button type="button" class="btn btn-sm btn-success fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSubmitTugas_' . $t['id'] . '"><i class="bi bi-upload me-1"></i> Kumpulkan Tugas</button>';
                }
            }

            $data[] = array(
                $no++,
                $judul_html,
                $mapel_html,
                $kelas_html,
                $guru_html,
                $dl_html,
                $bobot_html,
                '<div class="text-center">' . $aksi . '</div>'
            );
        }

        $output = array(
            "draw"            => $draw,
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data"            => $data
        );

        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }

    public function simpan_tugas()
    {
        $this->check_role(array('super_admin', 'admin', 'guru', 'wali_kelas'));
        $guru = $this->M_Guru->get_by_user_id($this->user_data['id']);
        $guru_id = isset($guru['id']) ? $guru['id'] : 1;
        $tugas_id = $this->input->post('id', true);

        $data = array(
            'mata_pelajaran_id' => $this->input->post('mata_pelajaran_id', true),
            'kelas_id'          => $this->input->post('kelas_id', true),
            'guru_id'           => $guru_id,
            'judul'             => $this->input->post('judul', true),
            'deskripsi'         => strip_tags($this->input->post('deskripsi', false), '<p><br><strong><em><u><ol><ul><li><h1><h2><h3><h4><h5><h6><table><thead><tbody><tr><td><th><blockquote><pre><code><span><div><hr><a><img><sub><sup>'),
            'tanggal_diberikan' => date('Y-m-d'),
            'deadline'          => $this->input->post('deadline', true),
            'bobot'             => $this->input->post('bobot', true) ? $this->input->post('bobot', true) : 10
        );

        if ($tugas_id) {
            $this->M_Akademik->update_tugas($tugas_id, $data);
            $this->session->set_flashdata('success', 'Tugas berhasil diperbarui.');
        } else {
            $this->M_Akademik->save_tugas($data);
            $this->session->set_flashdata('success', 'Tugas berhasil dipublikasikan.');
        }

        redirect('akademik/tugas');
    }

    public function hapus_tugas($id)
    {
        $this->check_role(array('super_admin', 'admin', 'guru', 'wali_kelas'));
        $this->M_Akademik->delete_tugas($id);
        $this->session->set_flashdata('success', 'Tugas berhasil dihapus.');
        redirect('akademik/tugas');
    }

    public function submit_tugas()
    {
        $this->check_role(array('murid', 'orang_tua'));
        $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
        if (!$murid) {
            $this->session->set_flashdata('error', 'Data murid tidak ditemukan.');
            redirect('akademik/tugas');
        }

        $tugas_id = $this->input->post('tugas_id', true);

        // Check if already submitted
        $existing = $this->M_Akademik->get_pengumpulan_by_murid($murid['id'], $tugas_id);
        if (!empty($existing)) {
            $this->session->set_flashdata('error', 'Anda sudah mengumpulkan tugas ini sebelumnya.');
            redirect('akademik/tugas');
        }

        $data = array(
            'tugas_id'         => $tugas_id,
            'murid_id'         => $murid['id'],
            'catatan_jawaban'  => $this->input->post('catatan_jawaban', true),
            'tanggal_kumpul'   => date('Y-m-d H:i:s')
        );

        // Handle file upload
        if (!empty($_FILES['file_jawaban']['name'])) {
            $config['upload_path']   = './uploads/tugas/';
            $config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png|zip|rar';
            $config['max_size']      = 10240; // 10MB
            $config['file_name']     = 'tugas_' . $tugas_id . '_' . $murid['id'] . '_' . time();

            // Create directory if not exists
            if (!is_dir('./uploads/tugas/')) {
                mkdir('./uploads/tugas/', 0777, true);
            }

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('file_jawaban')) {
                $data['file_jawaban'] = $this->upload->data('file_name');
            }
        }

        $this->M_Akademik->submit_tugas($data);
        $this->session->set_flashdata('success', 'Tugas berhasil dikumpulkan! Menunggu penilaian dari guru.');
        redirect('akademik/tugas');
    }

    public function lihat_pengumpulan($tugas_id)
    {
        $this->check_role(array('super_admin', 'admin', 'guru', 'wali_kelas', 'kepala_sekolah'));
        $data['title'] = 'Daftar Pengumpulan Tugas';
        $data['tugas'] = $this->M_Akademik->get_tugas_by_id($tugas_id);
        $data['pengumpulan_list'] = $this->M_Akademik->get_pengumpulan_by_tugas($tugas_id);
        $this->render_page('akademik/pengumpulan_tugas', $data);
    }

    public function nilai_pengumpulan()
    {
        $this->check_role(array('super_admin', 'admin', 'guru', 'wali_kelas'));
        $id = $this->input->post('pengumpulan_id', true);
        $data = array(
            'nilai'           => $this->input->post('nilai', true),
            'catatan_guru'    => $this->input->post('catatan_guru', true),
            'status'          => 'Dinilai',
            'tanggal_dinilai' => date('Y-m-d H:i:s')
        );
        $this->M_Akademik->nilai_pengumpulan($id, $data);

        $pengumpulan = $this->M_Akademik->get_pengumpulan_by_id($id);
        $this->session->set_flashdata('success', 'Nilai berhasil diberikan.');
        redirect('akademik/lihat_pengumpulan/' . $pengumpulan['tugas_id']);
    }

    public function nilai()
    {
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru', 'wali_kelas'));
        $data['title'] = 'Pengolahan Nilai Siswa';
        $kelas_id = $this->input->get('kelas_id', true);
        $mapel_id = $this->input->get('mata_pelajaran_id', true);
        $ta = $this->M_TahunAjaran->get_active();

        $data['kelas_list']     = $this->M_Kelas->get_all();
        $data['mapel_list']     = $this->M_Mapel->get_all();
        $data['selected_kelas'] = $kelas_id;
        $data['selected_mapel'] = $mapel_id;
        $data['nilai_list']      = array();

        if ($kelas_id && $mapel_id) {
            $data['nilai_list'] = $this->M_Akademik->get_nilai($kelas_id, $mapel_id, isset($ta['id']) ? $ta['id'] : 1);
        }

        $this->render_page('akademik/nilai', $data);
    }

    public function simpan_nilai()
    {
        $this->check_role(array('super_admin', 'admin', 'guru', 'wali_kelas'));
        $kelas_id = $this->input->post('kelas_id', true);
        $mapel_id = $this->input->post('mata_pelajaran_id', true);
        $ta = $this->M_TahunAjaran->get_active();

        $harian_arr = $this->input->post('nilai_harian', true);
        $tugas_arr  = $this->input->post('nilai_tugas', true);
        $pts_arr    = $this->input->post('nilai_pts', true);
        $pas_arr    = $this->input->post('nilai_pas', true);

        if (!empty($harian_arr)) {
            foreach ($harian_arr as $murid_id => $val) {
                $data = array(
                    'murid_id'          => $murid_id,
                    'mata_pelajaran_id' => $mapel_id,
                    'tahun_ajaran_id'   => isset($ta['id']) ? $ta['id'] : 1,
                    'kelas_id'          => $kelas_id,
                    'nilai_harian'      => $val,
                    'nilai_tugas'       => isset($tugas_arr[$murid_id]) ? $tugas_arr[$murid_id] : 0,
                    'nilai_pts'         => isset($pts_arr[$murid_id]) ? $pts_arr[$murid_id] : 0,
                    'nilai_pas'         => isset($pas_arr[$murid_id]) ? $pas_arr[$murid_id] : 0
                );
                $this->M_Akademik->save_nilai($data);
            }
            $this->session->set_flashdata('success', 'Nilai siswa berhasil dihitung dan disimpan.');
        }

        redirect('akademik/nilai?kelas_id=' . $kelas_id . '&mata_pelajaran_id=' . $mapel_id);
    }

    public function raport($murid_id = null)
    {
        $data['title'] = 'Raport Pembelajaran Murid';
        $user_role = $this->user_data['role'];
        $is_admin_or_teacher = in_array($user_role, array('super_admin', 'admin', 'guru', 'wali_kelas', 'kepala_sekolah'));
        
        $ta_active       = $this->M_TahunAjaran->get_active();
        $ta_list         = $this->M_TahunAjaran->get_all();
        $data['ta_list'] = $ta_list;

        $selected_ta_id = $this->input->get('tahun_ajaran_id', true) ? $this->input->get('tahun_ajaran_id', true) : (isset($ta_active['id']) ? $ta_active['id'] : 1);
        $data['selected_ta_id'] = $selected_ta_id;
        $data['selected_ta']    = $this->db->get_where('tahun_ajaran', array('id' => $selected_ta_id))->row_array();

        if ($is_admin_or_teacher) {
            // Admin/Guru: Show summary table of all students in selected class
            $kelas_id = $this->input->get('kelas_id', true);
            $data['kelas_list'] = $this->M_Kelas->get_all();
            $data['selected_kelas'] = $kelas_id;
            $data['view_mode'] = 'table'; // table or detail

            // If murid_id is provided via URL param, show detail
            $detail_murid_id = $this->input->get('murid_id', true);
            if (!empty($detail_murid_id) || !empty($murid_id)) {
                $target_id = !empty($detail_murid_id) ? $detail_murid_id : $murid_id;
                $data['view_mode'] = 'detail';
                $data['murid']      = $this->M_Murid->get_by_id($target_id);
                $data['raport']     = $this->M_Akademik->get_raport($target_id, $selected_ta_id);
                
                $this->db->select('nilai.*, mata_pelajaran.nama_mapel, mata_pelajaran.kelompok, mata_pelajaran.kkm');
                $this->db->from('nilai');
                $this->db->join('mata_pelajaran', 'mata_pelajaran.id = nilai.mata_pelajaran_id');
                $this->db->where('nilai.murid_id', $target_id);
                $this->db->where('nilai.tahun_ajaran_id', $selected_ta_id);
                $data['nilai_list'] = $this->db->get()->result_array();
            }

            // Always load student list for table view
            if ($kelas_id) {
                $this->db->select('murid.id, murid.nisn, murid.nis, murid.kelas_id, users.full_name, kelas.nama_kelas');
                $this->db->from('murid');
                $this->db->join('users', 'users.id = murid.user_id');
                $this->db->join('kelas', 'kelas.id = murid.kelas_id');
                $this->db->where('murid.kelas_id', $kelas_id);
                $this->db->where('murid.status_murid', 'Aktif');
                $this->db->order_by('users.full_name', 'ASC');
                $students = $this->db->get()->result_array();

                // For each student, get summary of their grades
                foreach ($students as &$s) {
                    $this->db->select('AVG(nilai_harian) as avg_uh, AVG(nilai_tugas) as avg_tugas, AVG(nilai_pts) as avg_uts, AVG(nilai_pas) as avg_ukk, AVG(nilai_akhir) as avg_akhir, COUNT(*) as jml_mapel');
                    $this->db->where('murid_id', $s['id']);
                    $this->db->where('tahun_ajaran_id', $selected_ta_id);
                    $row = $this->db->get('nilai')->row_array();
                    $s['avg_uh']     = isset($row['avg_uh']) ? round($row['avg_uh'], 1) : 0;
                    $s['avg_tugas']  = isset($row['avg_tugas']) ? round($row['avg_tugas'], 1) : 0;
                    $s['avg_uts']    = isset($row['avg_uts']) ? round($row['avg_uts'], 1) : 0;
                    $s['avg_ukk']    = isset($row['avg_ukk']) ? round($row['avg_ukk'], 1) : 0;
                    $s['avg_akhir']  = isset($row['avg_akhir']) ? round($row['avg_akhir'], 1) : 0;
                    $s['jml_mapel']  = isset($row['jml_mapel']) ? $row['jml_mapel'] : 0;

                    // Check if raport published
                    $rp = $this->db->get_where('raport', array('murid_id' => $s['id'], 'tahun_ajaran_id' => $selected_ta_id))->row_array();
                    $s['raport_published'] = !empty($rp) && $rp['is_published'];
                }
                unset($s);
                $data['students_summary'] = $students;
            } else {
                $data['students_summary'] = array();
            }

        } else {
            // Murid/Ortu: Show own detail raport directly
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            $murid_id = isset($murid['id']) ? $murid['id'] : 1;
            $data['view_mode']      = 'detail';
            $data['kelas_list']     = array();
            $data['selected_kelas'] = isset($murid['kelas_id']) ? $murid['kelas_id'] : 1;
            $data['murid']          = $this->M_Murid->get_by_id($murid_id);
            $data['raport']         = $this->M_Akademik->get_raport($murid_id, $selected_ta_id);
            
            $this->db->select('nilai.*, mata_pelajaran.nama_mapel, mata_pelajaran.kelompok, mata_pelajaran.kkm');
            $this->db->from('nilai');
            $this->db->join('mata_pelajaran', 'mata_pelajaran.id = nilai.mata_pelajaran_id');
            $this->db->where('nilai.murid_id', $murid_id);
            $this->db->where('nilai.tahun_ajaran_id', $selected_ta_id);
            $data['nilai_list'] = $this->db->get()->result_array();
        }

        $this->render_page('akademik/raport', $data);
    }

    public function cetak_raport($murid_id)
    {
        $selected_ta_id = $this->input->get('tahun_ajaran_id', true);
        if (empty($selected_ta_id)) {
            $ta_active = $this->M_TahunAjaran->get_active();
            $selected_ta_id = isset($ta_active['id']) ? $ta_active['id'] : 1;
        }

        if (in_array($this->user_data['role'], array('murid', 'orang_tua'))) {
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            $murid_id = isset($murid['id']) ? $murid['id'] : $murid_id;
        }

        $data['school_info']  = $this->school_info;
        $data['murid']        = $this->M_Murid->get_by_id($murid_id);
        $data['raport']       = $this->M_Akademik->get_raport($murid_id, $selected_ta_id);
        $data['tahun_ajaran'] = $this->db->get_where('tahun_ajaran', array('id' => $selected_ta_id))->row_array();
        
        $this->db->select('nilai.*, mata_pelajaran.nama_mapel, mata_pelajaran.kelompok, mata_pelajaran.kkm');
        $this->db->from('nilai');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.id = nilai.mata_pelajaran_id');
        $this->db->where('nilai.murid_id', $murid_id);
        $this->db->where('nilai.tahun_ajaran_id', $selected_ta_id);
        $data['nilai_list'] = $this->db->get()->result_array();

        $this->load->view('akademik/cetak_raport', $data);
    }

    public function sertifikat()
    {
        $data['title'] = 'Sertifikat & Ijazah Siswa';
        $user_role = $this->user_data['role'];

        if (in_array($user_role, array('murid', 'orang_tua'))) {
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            $murid_id = isset($murid['id']) ? $murid['id'] : 0;
            
            $this->db->select('sertifikat.*, users.full_name as nama_murid, murid.nisn');
            $this->db->from('sertifikat');
            $this->db->join('murid', 'murid.id = sertifikat.murid_id');
            $this->db->join('users', 'users.id = murid.user_id');
            $this->db->where('sertifikat.murid_id', $murid_id);
            $data['sertifikat_list'] = $this->db->get()->result_array();
            $data['murid_list']      = array();
        } else {
            $data['sertifikat_list'] = $this->M_Akademik->get_sertifikat();
            $data['murid_list']      = $this->M_Murid->get_all();
        }

        $this->render_page('akademik/sertifikat', $data);
    }

    public function simpan_sertifikat()
    {
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah'));
        $data = array(
            'murid_id'       => $this->input->post('murid_id', true),
            'nomor_seri'     => 'CERT-' . date('Y') . '-' . rand(1000, 9999),
            'jenis'          => $this->input->post('jenis', true),
            'deskripsi'      => $this->input->post('deskripsi', true),
            'file_path'      => 'dist/assets/img/boxed-bg.jpg',
            'qr_code'        => 'QR-' . rand(10000, 99999),
            'tanggal_terbit' => date('Y-m-d'),
            'is_verified'    => 1
        );

        $this->M_Akademik->save_sertifikat($data);
        $this->session->set_flashdata('success', 'Sertifikat berhasil diterbitkan.');
        redirect('akademik/sertifikat');
    }

    public function cetak_sertifikat($id)
    {
        $data['school_info'] = $this->school_info;
        
        $this->db->select('sertifikat.*, users.full_name as nama_murid, murid.nisn, murid.nis, kelas.nama_kelas');
        $this->db->from('sertifikat');
        $this->db->join('murid', 'murid.id = sertifikat.murid_id');
        $this->db->join('users', 'users.id = murid.user_id');
        $this->db->join('kelas', 'kelas.id = murid.kelas_id', 'left');
        $this->db->where('sertifikat.id', $id);
        $sertifikat = $this->db->get()->row_array();

        if (!$sertifikat) {
            show_404();
        }

        if (in_array($this->user_data['role'], array('murid', 'orang_tua'))) {
            $murid = $this->M_Murid->get_by_user_id($this->user_data['id']);
            if (isset($murid['id']) && $sertifikat['murid_id'] != $murid['id']) {
                show_404();
            }
        }

        $data['sertifikat'] = $sertifikat;
        $this->load->view('akademik/cetak_sertifikat', $data);
    }
}
