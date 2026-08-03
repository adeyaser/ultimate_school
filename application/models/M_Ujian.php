<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Ujian extends CI_Model {

    public function get_all($guru_id = null, $kelas_id = null, $jenjang = null)
    {
        $this->db->select('ujian.*, bank_soal.kode_soal, mata_pelajaran.nama_mapel, kelas.nama_kelas, users.full_name as nama_guru');
        $this->db->from('ujian');
        $this->db->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.id = ujian.mata_pelajaran_id');
        $this->db->join('kelas', 'kelas.id = ujian.kelas_id');
        $this->db->join('guru', 'guru.id = ujian.guru_id');
        $this->db->join('users', 'users.id = guru.user_id');
        if ($guru_id) {
            $this->db->where('ujian.guru_id', $guru_id);
        }
        if ($kelas_id) {
            $this->db->where('ujian.kelas_id', $kelas_id);
        }
        if ($jenjang === null) {
            $CI =& get_instance();
            $jenjang = $CI->session->userdata('active_jenjang');
        }
        if ($jenjang && $jenjang !== 'ALL') {
            $this->db->where('ujian.jenjang', $jenjang);
        }
        $this->db->order_by('ujian.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->select('ujian.*, bank_soal.kode_soal, bank_soal.is_random, mata_pelajaran.nama_mapel, kelas.nama_kelas, users.full_name as nama_guru');
        $this->db->from('ujian');
        $this->db->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.id = ujian.mata_pelajaran_id');
        $this->db->join('kelas', 'kelas.id = ujian.kelas_id');
        $this->db->join('guru', 'guru.id = ujian.guru_id');
        $this->db->join('users', 'users.id = guru.user_id');
        $this->db->where('ujian.id', $id);
        return $this->db->get()->row_array();
    }

    public function insert($data)
    {
        if (!isset($data['token']) || empty($data['token'])) {
            $data['token'] = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        }
        $this->db->insert('ujian', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('ujian', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('ujian', array('id' => $id));
    }

    // Ujian Peserta
    public function get_ujian_siswa_list($kelas_id, $murid_id, $jenjang = null)
    {
        $safe_murid_id = !empty($murid_id) ? (int)$murid_id : 0;
        $this->db->select('ujian.*, mata_pelajaran.nama_mapel, users.full_name as nama_guru, ujian_peserta.status as status_peserta, ujian_peserta.nilai_total, ujian_peserta.is_lulus, ujian_peserta.id as peserta_id');
        $this->db->from('ujian');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.id = ujian.mata_pelajaran_id');
        $this->db->join('guru', 'guru.id = ujian.guru_id');
        $this->db->join('users', 'users.id = guru.user_id');
        $this->db->join('ujian_peserta', 'ujian_peserta.ujian_id = ujian.id AND ujian_peserta.murid_id = '.$safe_murid_id, 'left');
        if ($kelas_id) {
            $this->db->where('ujian.kelas_id', $kelas_id);
        }
        if ($jenjang === null) {
            $CI =& get_instance();
            $jenjang = $CI->session->userdata('active_jenjang');
        }
        if ($jenjang && $jenjang !== 'ALL') {
            $this->db->where('ujian.jenjang', $jenjang);
        }
        $this->db->where('ujian.is_active', 1);
        $this->db->order_by('ujian.tanggal_mulai', 'DESC');
        return $this->db->get()->result_array();
    }

    public function start_exam($ujian_id, $murid_id, $token_input)
    {
        $ujian = $this->get_by_id($ujian_id);
        if (!$ujian || $ujian['token'] !== trim($token_input)) {
            return array('status' => false, 'message' => 'Token ujian tidak valid.');
        }

        $peserta = $this->db->get_where('ujian_peserta', array(
            'ujian_id' => $ujian_id,
            'murid_id' => $murid_id
        ))->row_array();

        if ($peserta) {
            if ($peserta['status'] === 'Selesai') {
                return array('status' => false, 'message' => 'Anda sudah menyelesaikan ujian ini.');
            }
            return array('status' => true, 'peserta_id' => $peserta['id']);
        }

        // Create new participant record
        $data_peserta = array(
            'ujian_id' => $ujian_id,
            'murid_id' => $murid_id,
            'token_akses' => $token_input,
            'tanggal_mulai' => date('Y-m-d H:i:s'),
            'status' => 'Sedang'
        );

        $this->db->insert('ujian_peserta', $data_peserta);
        return array('status' => true, 'peserta_id' => $this->db->insert_id());
    }

    public function save_jawaban_item($peserta_id, $soal_id, $jawaban)
    {
        // Check correct answer for multiple choice
        $soal = $this->db->get_where('soal', array('id' => $soal_id))->row_array();
        $is_benar = 0;
        $nilai = 0;

        if ($soal && $soal['jenis'] === 'Pilihan Ganda') {
            if (strtoupper(trim($jawaban)) === strtoupper(trim($soal['kunci_jawaban']))) {
                $is_benar = 1;
                $nilai = (float)$soal['bobot'];
            }
        }

        $data = array(
            'ujian_peserta_id' => $peserta_id,
            'soal_id' => $soal_id,
            'jawaban' => $jawaban,
            'is_benar' => $is_benar,
            'nilai' => $nilai
        );

        return $this->db->replace('ujian_jawaban', $data);
    }

    public function finish_exam($peserta_id)
    {
        $peserta = $this->db->get_where('ujian_peserta', array('id' => $peserta_id))->row_array();
        if (!$peserta) return false;

        $ujian = $this->get_by_id($peserta['ujian_id']);

        // Calculate total score from answers
        $this->db->select_sum('nilai', 'total_score');
        $this->db->where('ujian_peserta_id', $peserta_id);
        $score_row = $this->db->get('ujian_jawaban')->row_array();

        $total_score = isset($score_row['total_score']) ? (float)$score_row['total_score'] : 0;
        $is_lulus = ($total_score >= $ujian['kkm']) ? 1 : 0;

        $start_time = strtotime($peserta['tanggal_mulai']);
        $end_time = time();
        $durasi_sec = $end_time - $start_time;

        $update_data = array(
            'tanggal_selesai' => date('Y-m-d H:i:s'),
            'durasi_pengerjaan' => $durasi_sec,
            'status' => 'Selesai',
            'nilai_total' => $total_score,
            'is_lulus' => $is_lulus
        );

        $this->db->where('id', $peserta_id);
        $this->db->update('ujian_peserta', $update_data);

        // Update smart recommendation
        $this->load->model('M_BankSoal');
        $total_soal = $ujian['jumlah_soal'];
        $correct_count = $this->db->where(array('ujian_peserta_id' => $peserta_id, 'is_benar' => 1))->count_all_results('ujian_jawaban');
        $this->M_BankSoal->update_rekomendasi_kinerja($peserta['murid_id'], $ujian['mata_pelajaran_id'], $total_score, $total_soal, $correct_count);

        return $update_data;
    }
}
