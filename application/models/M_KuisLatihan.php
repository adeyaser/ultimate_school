<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_KuisLatihan extends CI_Model {

    public function generate_kuis($murid_id, $mata_pelajaran_id, $jumlah_soal = 10)
    {
        // 1. Try questions matching the selected subject
        $this->db->select('soal.id');
        $this->db->from('soal');
        $this->db->join('bank_soal', 'bank_soal.id = soal.bank_soal_id');
        $this->db->where('bank_soal.mata_pelajaran_id', $mata_pelajaran_id);
        $this->db->order_by('RAND()');
        $this->db->limit($jumlah_soal);
        $res = $this->db->get()->result_array();

        // 2. Fallback: if no questions for that subject, pick any available questions in repository
        if (empty($res)) {
            $this->db->select('soal.id');
            $this->db->from('soal');
            $this->db->order_by('RAND()');
            $this->db->limit($jumlah_soal);
            $res = $this->db->get()->result_array();
        }

        if (empty($res)) {
            return false;
        }

        $soal_ids = array_column($res, 'id');

        $data = array(
            'murid_id' => $murid_id,
            'mata_pelajaran_id' => $mata_pelajaran_id,
            'soal_ids' => json_encode($soal_ids),
            'jumlah_soal' => count($soal_ids),
            'tanggal_mulai' => date('Y-m-d H:i:s'),
            'status' => 'Sedang'
        );

        $this->db->insert('kuis_latihan', $data);
        return $this->db->insert_id();
    }

    public function get_history($murid_id)
    {
        $this->db->select('kuis_latihan.*, mata_pelajaran.nama_mapel, mata_pelajaran.kode_mapel');
        $this->db->from('kuis_latihan');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.id = kuis_latihan.mata_pelajaran_id', 'left');
        $this->db->where('kuis_latihan.murid_id', $murid_id);
        $this->db->order_by('kuis_latihan.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_kuis($kuis_id)
    {
        $this->db->select('kuis_latihan.*, mata_pelajaran.nama_mapel');
        $this->db->from('kuis_latihan');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.id = kuis_latihan.mata_pelajaran_id');
        $this->db->where('kuis_latihan.id', $kuis_id);
        return $this->db->get()->row_array();
    }

    public function get_soal_kuis($soal_ids_json)
    {
        $soal_ids = json_decode($soal_ids_json, true);
        if (empty($soal_ids)) return array();

        $this->db->where_in('id', $soal_ids);
        return $this->db->get('soal')->result_array();
    }

    public function submit_kuis($kuis_id, $jawaban_map)
    {
        $kuis = $this->get_kuis($kuis_id);
        if (!$kuis) return false;

        $soal_list = $this->get_soal_kuis($kuis['soal_ids']);
        $benar = 0;
        $salah = 0;

        foreach ($soal_list as $s) {
            $user_ans = isset($jawaban_map[$s['id']]) ? trim($jawaban_map[$s['id']]) : '';
            $is_correct = 0;

            if (strtoupper($user_ans) === strtoupper(trim($s['kunci_jawaban']))) {
                $is_correct = 1;
                $benar++;
            } else {
                $salah++;
            }

            $this->db->insert('kuis_latihan_jawaban', array(
                'kuis_latihan_id' => $kuis_id,
                'soal_id' => $s['id'],
                'jawaban' => $user_ans,
                'is_benar' => $is_correct
            ));
        }

        $total_soal = count($soal_list);
        $score = ($total_soal > 0) ? round(($benar / $total_soal) * 100, 2) : 0;

        $update_data = array(
            'jawaban_benar' => $benar,
            'jawaban_salah' => $salah,
            'nilai' => $score,
            'tanggal_selesai' => date('Y-m-d H:i:s'),
            'status' => 'Selesai'
        );

        $this->db->where('id', $kuis_id);
        $this->db->update('kuis_latihan', $update_data);

        return $score;
    }
}
