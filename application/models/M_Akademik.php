<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Akademik extends CI_Model {

    // Tugas
    public function get_tugas($kelas_id = null, $mapel_id = null)
    {
        $this->db->select('tugas.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, users.full_name as nama_guru');
        $this->db->from('tugas');
        $this->db->join('kelas', 'kelas.id = tugas.kelas_id');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.id = tugas.mata_pelajaran_id');
        $this->db->join('guru', 'guru.id = tugas.guru_id');
        $this->db->join('users', 'users.id = guru.user_id');
        if ($kelas_id) {
            $this->db->where('tugas.kelas_id', $kelas_id);
        }
        if ($mapel_id) {
            $this->db->where('tugas.mata_pelajaran_id', $mapel_id);
        }
        $this->db->order_by('tugas.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function save_tugas($data)
    {
        return $this->db->insert('tugas', $data);
    }

    // Nilai
    public function get_nilai($kelas_id, $mapel_id, $tahun_ajaran_id)
    {
        $this->db->select('murid.id as murid_id, murid.nisn, murid.nis, users.full_name, nilai.id as nilai_id, nilai.nilai_harian, nilai.nilai_tugas, nilai.nilai_pts, nilai.nilai_pas, nilai.nilai_akhir, nilai.predikat, nilai.is_tuntas');
        $this->db->from('murid');
        $this->db->join('users', 'users.id = murid.user_id');
        $this->db->join('nilai', 'nilai.murid_id = murid.id AND nilai.mata_pelajaran_id = '.$mapel_id.' AND nilai.tahun_ajaran_id = '.$tahun_ajaran_id, 'left');
        $this->db->where('murid.kelas_id', $kelas_id);
        $this->db->where('murid.status_murid', 'Aktif');
        $this->db->order_by('users.full_name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function save_nilai($data)
    {
        // Calculate Nilai Akhir & Predikat
        $harian = isset($data['nilai_harian']) ? (float)$data['nilai_harian'] : 0;
        $tugas  = isset($data['nilai_tugas'])  ? (float)$data['nilai_tugas']  : 0;
        $pts    = isset($data['nilai_pts'])    ? (float)$data['nilai_pts']    : 0;
        $pas    = isset($data['nilai_pas'])    ? (float)$data['nilai_pas']    : 0;

        $akhir = ($harian * 0.2) + ($tugas * 0.2) + ($pts * 0.3) + ($pas * 0.3);
        $data['nilai_akhir'] = round($akhir, 2);

        if ($akhir >= 90) {
            $data['predikat'] = 'A';
        } elseif ($akhir >= 80) {
            $data['predikat'] = 'B';
        } elseif ($akhir >= 75) {
            $data['predikat'] = 'C';
        } else {
            $data['predikat'] = 'D';
        }

        $data['is_tuntas'] = ($akhir >= 75) ? 1 : 0;

        return $this->db->replace('nilai', $data);
    }

    // Raport
    public function get_raport($murid_id, $tahun_ajaran_id)
    {
        $this->db->select('raport.*, murid.nisn, murid.nis, users.full_name as nama_murid, kelas.nama_kelas, tahun_ajaran.nama as nama_tahun_ajaran, tahun_ajaran.semester');
        $this->db->from('raport');
        $this->db->join('murid', 'murid.id = raport.murid_id');
        $this->db->join('users', 'users.id = murid.user_id');
        $this->db->join('kelas', 'kelas.id = raport.kelas_id');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id = raport.tahun_ajaran_id');
        $this->db->where('raport.murid_id', $murid_id);
        $this->db->where('raport.tahun_ajaran_id', $tahun_ajaran_id);
        return $this->db->get()->row_array();
    }

    public function generate_raport($murid_id, $kelas_id, $tahun_ajaran_id, $semester, $catatan_wali)
    {
        // Get all grades for average
        $this->db->select('AVG(nilai_akhir) as rata_rata');
        $this->db->where('murid_id', $murid_id);
        $this->db->where('tahun_ajaran_id', $tahun_ajaran_id);
        $avg_row = $this->db->get('nilai')->row_array();

        $data = array(
            'murid_id' => $murid_id,
            'kelas_id' => $kelas_id,
            'tahun_ajaran_id' => $tahun_ajaran_id,
            'semester' => $semester,
            'rata_rata' => isset($avg_row['rata_rata']) ? round($avg_row['rata_rata'], 2) : 0,
            'catatan_wali_kelas' => $catatan_wali,
            'is_published' => 1,
            'published_at' => date('Y-m-d H:i:s')
        );

        return $this->db->replace('raport', $data);
    }

    // Sertifikat
    public function get_sertifikat($murid_id = null)
    {
        $this->db->select('sertifikat.*, murid.nisn, users.full_name as nama_murid');
        $this->db->from('sertifikat');
        $this->db->join('murid', 'murid.id = sertifikat.murid_id');
        $this->db->join('users', 'users.id = murid.user_id');
        if ($murid_id) {
            $this->db->where('sertifikat.murid_id', $murid_id);
        }
        return $this->db->get()->result_array();
    }

    public function save_sertifikat($data)
    {
        return $this->db->insert('sertifikat', $data);
    }

    public function update_tugas($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tugas', $data);
    }

    public function delete_tugas($id)
    {
        return $this->db->delete('tugas', array('id' => $id));
    }

    // === Pengumpulan Tugas (Student Submissions) ===

    public function submit_tugas($data)
    {
        return $this->db->insert('pengumpulan_tugas', $data);
    }

    public function get_pengumpulan_by_tugas($tugas_id)
    {
        $this->db->select('pengumpulan_tugas.*, users.full_name as nama_murid, murid.nisn, murid.nis');
        $this->db->from('pengumpulan_tugas');
        $this->db->join('murid', 'murid.id = pengumpulan_tugas.murid_id');
        $this->db->join('users', 'users.id = murid.user_id');
        $this->db->where('pengumpulan_tugas.tugas_id', $tugas_id);
        $this->db->order_by('pengumpulan_tugas.tanggal_kumpul', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_pengumpulan_by_murid($murid_id, $tugas_id = null)
    {
        $this->db->from('pengumpulan_tugas');
        $this->db->where('murid_id', $murid_id);
        if ($tugas_id) {
            $this->db->where('tugas_id', $tugas_id);
        }
        return $this->db->get()->result_array();
    }

    public function get_pengumpulan_by_id($id)
    {
        return $this->db->get_where('pengumpulan_tugas', array('id' => $id))->row_array();
    }

    public function nilai_pengumpulan($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('pengumpulan_tugas', $data);
    }

    public function count_pengumpulan($tugas_id)
    {
        return $this->db->where('tugas_id', $tugas_id)->count_all_results('pengumpulan_tugas');
    }

    public function get_tugas_by_id($id)
    {
        $this->db->select('tugas.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, users.full_name as nama_guru');
        $this->db->from('tugas');
        $this->db->join('kelas', 'kelas.id = tugas.kelas_id');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.id = tugas.mata_pelajaran_id');
        $this->db->join('guru', 'guru.id = tugas.guru_id');
        $this->db->join('users', 'users.id = guru.user_id');
        $this->db->where('tugas.id', $id);
        return $this->db->get()->row_array();
    }

    // === DataTables Server-Side Processing for Tugas ===

    private function _get_tugas_datatables_query($kelas_id = null, $mapel_id = null, $search = null, $jenjang = null)
    {
        $this->db->select('tugas.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, users.full_name as nama_guru');
        $this->db->from('tugas');
        $this->db->join('kelas', 'kelas.id = tugas.kelas_id');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.id = tugas.mata_pelajaran_id');
        $this->db->join('guru', 'guru.id = tugas.guru_id');
        $this->db->join('users', 'users.id = guru.user_id');

        if ($kelas_id) {
            $this->db->where('tugas.kelas_id', $kelas_id);
        }
        if ($mapel_id) {
            $this->db->where('tugas.mata_pelajaran_id', $mapel_id);
        }
        if ($jenjang === null) {
            $CI =& get_instance();
            $jenjang = $CI->session->userdata('active_jenjang');
        }
        if ($jenjang && $jenjang !== 'ALL') {
            $this->db->where('kelas.jenjang', $jenjang);
        }

        if ($search) {
            $this->db->group_start();
            $this->db->like('tugas.judul', $search);
            $this->db->or_like('tugas.deskripsi', $search);
            $this->db->or_like('mata_pelajaran.nama_mapel', $search);
            $this->db->or_like('kelas.nama_kelas', $search);
            $this->db->or_like('users.full_name', $search);
            $this->db->group_end();
        }
    }

    public function get_tugas_datatables($start = 0, $length = 10, $kelas_id = null, $mapel_id = null, $search = null, $order_col = 'tugas.id', $order_dir = 'DESC', $jenjang = null)
    {
        $this->_get_tugas_datatables_query($kelas_id, $mapel_id, $search, $jenjang);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        $this->db->order_by($order_col, $order_dir);
        return $this->db->get()->result_array();
    }

    public function count_tugas_filtered($kelas_id = null, $mapel_id = null, $search = null, $jenjang = null)
    {
        $this->_get_tugas_datatables_query($kelas_id, $mapel_id, $search, $jenjang);
        return $this->db->count_all_results();
    }

    public function count_tugas_all($kelas_id = null, $mapel_id = null, $jenjang = null)
    {
        $this->db->from('tugas');
        $this->db->join('kelas', 'kelas.id = tugas.kelas_id');
        if ($kelas_id) {
            $this->db->where('tugas.kelas_id', $kelas_id);
        }
        if ($mapel_id) {
            $this->db->where('tugas.mata_pelajaran_id', $mapel_id);
        }
        if ($jenjang === null) {
            $CI =& get_instance();
            $jenjang = $CI->session->userdata('active_jenjang');
        }
        if ($jenjang && $jenjang !== 'ALL') {
            $this->db->where('kelas.jenjang', $jenjang);
        }
        return $this->db->count_all_results();
    }
}
