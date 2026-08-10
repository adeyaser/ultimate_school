<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_GudangSoal extends CI_Model {

    private $master_path;
    private $index_path;

    public function __construct()
    {
        parent::__construct();
        $this->master_path = FCPATH . 'application/data/gudang_soal/gudang_soal_master.json';
        $this->index_path  = FCPATH . 'application/data/gudang_soal/gudang_soal_index.json';
    }

    public function get_index_data()
    {
        if (!file_exists($this->index_path)) {
            return array('total_soal' => 0, 'categories' => array());
        }
        $raw = file_get_contents($this->index_path);
        return json_decode($raw, true);
    }

    public function get_master_data()
    {
        if (!file_exists($this->master_path)) {
            return array();
        }
        $raw = file_get_contents($this->master_path);
        $data = json_decode($raw, true);
        return is_array($data) ? $data : array();
    }

    public function get_soal_by_filter($mapel = null, $kelas = null, $jenjang = null, $limit = 10, $random = true)
    {
        $all = $this->get_master_data();
        if (empty($all)) return array();

        $filtered = array();

        $mapel_clean   = !empty($mapel) ? strtolower(trim($mapel)) : '';
        $kelas_val     = !empty($kelas) ? (int)$kelas : 0;
        $jenjang_clean = !empty($jenjang) ? strtoupper(trim($jenjang)) : '';

        foreach ($all as $item) {
            $match_mapel   = true;
            $match_jenjang = true;
            $match_kelas   = true;

            if (!empty($mapel_clean)) {
                $item_mapel = strtolower($item['mapel']);
                if (strpos($item_mapel, $mapel_clean) === false && strpos($mapel_clean, $item_mapel) === false) {
                    $match_mapel = false;
                }
            }

            if (!empty($jenjang_clean)) {
                if (strtoupper($item['jenjang']) !== $jenjang_clean) {
                    $match_jenjang = false;
                }
            }

            if ($kelas_val > 0) {
                if ((int)$item['kelas'] !== $kelas_val && (int)$item['kelas'] !== 0) {
                    $match_kelas = false;
                }
            }

            if ($match_mapel && $match_jenjang && $match_kelas) {
                $filtered[] = $item;
            }
        }

        // Fallback 1: match mapel & jenjang ignoring exact kelas
        if (empty($filtered) && (!empty($mapel_clean) || !empty($jenjang_clean))) {
            foreach ($all as $item) {
                $match_mapel   = empty($mapel_clean) || strpos(strtolower($item['mapel']), $mapel_clean) !== false || strpos($mapel_clean, strtolower($item['mapel'])) !== false;
                $match_jenjang = empty($jenjang_clean) || strtoupper($item['jenjang']) === $jenjang_clean;

                if ($match_mapel && $match_jenjang) {
                    $filtered[] = $item;
                }
            }
        }

        // Fallback 2: match jenjang only
        if (empty($filtered) && !empty($jenjang_clean)) {
            foreach ($all as $item) {
                if (strtoupper($item['jenjang']) === $jenjang_clean) {
                    $filtered[] = $item;
                }
            }
        }

        // Fallback 3: take any questions if repository matches nothing
        if (empty($filtered)) {
            $filtered = $all;
        }

        if ($random) {
            shuffle($filtered);
        }

        if ($limit > 0 && count($filtered) > $limit) {
            $filtered = array_slice($filtered, 0, $limit);
        }

        return $filtered;
    }

    public function import_to_bank_soal($bank_soal_id, $items)
    {
        if (empty($items) || !is_array($items)) return 0;

        $this->load->model('M_BankSoal');
        $existing = $this->M_BankSoal->get_soal_by_bank($bank_soal_id);
        $start_nomor = count($existing) + 1;
        $inserted = 0;

        foreach ($items as $item) {
            if (empty($item['pertanyaan'])) continue;

            $data = array(
                'bank_soal_id'      => $bank_soal_id,
                'nomor_soal'        => $start_nomor + $inserted,
                'pertanyaan'        => $item['pertanyaan'],
                'jenis'             => isset($item['jenis']) ? $item['jenis'] : 'Pilihan Ganda',
                'pilihan_a'         => isset($item['pilihan_a']) ? $item['pilihan_a'] : '',
                'pilihan_b'         => isset($item['pilihan_b']) ? $item['pilihan_b'] : '',
                'pilihan_c'         => isset($item['pilihan_c']) ? $item['pilihan_c'] : '',
                'pilihan_d'         => isset($item['pilihan_d']) ? $item['pilihan_d'] : '',
                'pilihan_e'         => isset($item['pilihan_e']) ? $item['pilihan_e'] : '',
                'kunci_jawaban'     => isset($item['kunci_jawaban']) ? $item['kunci_jawaban'] : '',
                'pembahasan'        => isset($item['pembahasan']) ? $item['pembahasan'] : '',
                'bobot'             => 10,
                'tingkat_kesulitan' => 'Sedang'
            );

            $this->M_BankSoal->insert_soal($data);
            $inserted++;
        }

        return $inserted;
    }
}
