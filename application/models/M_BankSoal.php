<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_BankSoal extends CI_Model {

    public function get_all($guru_id = null, $jenjang = null)
    {
        $this->db->select('bank_soal.*, mata_pelajaran.nama_mapel, kelas.nama_kelas, users.full_name as nama_pembuat');
        $this->db->from('bank_soal');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.id = bank_soal.mata_pelajaran_id');
        $this->db->join('kelas', 'kelas.id = bank_soal.kelas_id');
        $this->db->join('users', 'users.id = bank_soal.created_by');
        if ($guru_id) {
            $this->db->where('bank_soal.guru_id', $guru_id);
        }
        if ($jenjang === null) {
            $CI =& get_instance();
            $jenjang = $CI->session->userdata('active_jenjang');
        }
        if ($jenjang && $jenjang !== 'ALL') {
            $this->db->where('kelas.jenjang', $jenjang);
        }
        $this->db->order_by('bank_soal.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->select('bank_soal.*, mata_pelajaran.nama_mapel, kelas.nama_kelas, users.full_name as nama_pembuat');
        $this->db->from('bank_soal');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.id = bank_soal.mata_pelajaran_id');
        $this->db->join('kelas', 'kelas.id = bank_soal.kelas_id');
        $this->db->join('users', 'users.id = bank_soal.created_by');
        $this->db->where('bank_soal.id', $id);
        return $this->db->get()->row_array();
    }

    public function insert_bank_soal($data)
    {
        if (!isset($data['kode_soal']) || empty($data['kode_soal'])) {
            $data['kode_soal'] = 'SOAL-' . date('Ym') . '-' . rand(100, 999);
        }
        $this->db->insert('bank_soal', $data);
        return $this->db->insert_id();
    }

    public function update_bank_soal($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('bank_soal', $data);
    }

    public function delete_bank_soal($id)
    {
        return $this->db->delete('bank_soal', array('id' => $id));
    }

    // Detail Soal
    public function get_soal_by_bank($bank_soal_id)
    {
        $this->db->where('bank_soal_id', $bank_soal_id);
        $this->db->order_by('nomor_soal', 'ASC');
        return $this->db->get('soal')->result_array();
    }

    public function insert_soal($data)
    {
        $this->db->insert('soal', $data);
        $soal_id = $this->db->insert_id();

        // Update jumlah_soal in bank_soal
        $count = $this->db->where('bank_soal_id', $data['bank_soal_id'])->count_all_results('soal');
        $this->db->where('id', $data['bank_soal_id'])->update('bank_soal', array('jumlah_soal' => $count));

        return $soal_id;
    }

    public function update_soal($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('soal', $data);
    }

    public function delete_soal($id, $bank_soal_id)
    {
        $this->db->delete('soal', array('id' => $id));
        $count = $this->db->where('bank_soal_id', $bank_soal_id)->count_all_results('soal');
        return $this->db->where('id', $bank_soal_id)->update('bank_soal', array('jumlah_soal' => $count));
    }

    // Smart Suggestion Engine (Rekomendasi Soal)
    public function get_rekomendasi_soal($murid_id, $mata_pelajaran_id)
    {
        $rekom = $this->db->get_where('kuis_rekomendasi', array(
            'murid_id' => $murid_id,
            'mata_pelajaran_id' => $mata_pelajaran_id
        ))->row_array();

        $target_diff = isset($rekom['rekomendasi_tingkat']) ? $rekom['rekomendasi_tingkat'] : 'Sedang';

        // Retrieve repository questions matching suggested difficulty
        $this->db->select('soal.*, bank_soal.judul as nama_bank_soal');
        $this->db->from('soal');
        $this->db->join('bank_soal', 'bank_soal.id = soal.bank_soal_id');
        $this->db->where('bank_soal.mata_pelajaran_id', $mata_pelajaran_id);
        $this->db->where('soal.tingkat_kesulitan', $target_diff);
        $this->db->where('bank_soal.status', 'Published');
        $this->db->order_by('RAND()');
        $this->db->limit(10);

        return array(
            'target_difficulty' => $target_diff,
            'recommendation_meta' => $rekom,
            'suggested_questions' => $this->db->get()->result_array()
        );
    }

    public function update_rekomendasi_kinerja($murid_id, $mata_pelajaran_id, $nilai_baru, $total_soal, $total_benar)
    {
        $pct = ($total_soal > 0) ? ($total_benar / $total_soal) * 100 : 0;
        
        $next_level = 'Sedang';
        if ($pct >= 85) {
            $next_level = 'Sulit';
        } elseif ($pct < 65) {
            $next_level = 'Mudah';
        }

        $data = array(
            'murid_id' => $murid_id,
            'mata_pelajaran_id' => $mata_pelajaran_id,
            'tingkat_kesulitan' => $next_level,
            'nilai_terakhir' => $nilai_baru,
            'jumlah_soal_dikerjakan' => $total_soal,
            'persentase_benar' => round($pct, 2),
            'rekomendasi_tingkat' => $next_level
        );

        return $this->db->replace('kuis_rekomendasi', $data);
    }
}
