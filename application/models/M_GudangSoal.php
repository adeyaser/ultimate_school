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

    public function normalize_mapel_name($mapel)
    {
        $m = strtolower(trim($mapel));
        if (empty($m)) return '';

        // Strip grade/curriculum suffixes
        $m = preg_replace('/\b(sd|smp|sma|smk|wajib|peminatan|terpadu|kesenian|utama|kelompok\s+[a-c])\b/i', '', $m);
        $m = trim($m);

        if (preg_match('/\b(inggris|english)\b/i', $m)) {
            return 'bahasa inggris';
        }
        if (preg_match('/\b(indonesia)\b/i', $m)) {
            return 'bahasa indonesia';
        }
        if (preg_match('/\b(sunda)\b/i', $m)) {
            return 'bahasa sunda';
        }
        if (preg_match('/\b(jawa|jawa\s+kuno)\b/i', $m)) {
            return 'bahasa jawa';
        }
        if (preg_match('/\b(bali)\b/i', $m)) {
            return 'bahasa bali';
        }
        if (preg_match('/\b(lampung)\b/i', $m)) {
            return 'bahasa lampung';
        }
        if (preg_match('/\b(madura)\b/i', $m)) {
            return 'bahasa madura';
        }
        if (preg_match('/\b(makassar|bugis)\b/i', $m)) {
            return 'bahasa makassar';
        }
        if (preg_match('/\b(banjar)\b/i', $m)) {
            return 'bahasa banjar';
        }
        if (preg_match('/\b(dayak|ngaju)\b/i', $m)) {
            return 'bahasa dayak ngaju';
        }
        if (preg_match('/\b(minangkabau|bam)\b/i', $m)) {
            return 'budaya alam minangkabau';
        }

        if ($m === 'bahasa') {
            return 'bahasa indonesia';
        }

        return $m;
    }

    private function match_mapel($search_mapel, $item_mapel)
    {
        $norm_search = $this->normalize_mapel_name($search_mapel);
        $norm_item   = $this->normalize_mapel_name($item_mapel);

        if (empty($norm_search)) return true;

        $language_subjects = array(
            'bahasa inggris', 'bahasa indonesia', 'bahasa sunda', 'bahasa jawa',
            'bahasa bali', 'bahasa lampung', 'bahasa madura', 'bahasa makassar',
            'bahasa banjar', 'bahasa dayak ngaju', 'budaya alam minangkabau'
        );

        $is_search_lang = in_array($norm_search, $language_subjects);
        $is_item_lang   = in_array($norm_item, $language_subjects);

        if ($is_search_lang || $is_item_lang) {
            return $norm_search === $norm_item;
        }

        return ($norm_search === $norm_item) ||
               (strpos($norm_item, $norm_search) !== false) ||
               (strpos($norm_search, $norm_item) !== false);
    }

    public function get_soal_by_filter($mapel = null, $kelas = null, $jenjang = null, $limit = 10, $random = true)
    {
        $all = $this->get_master_data();
        if (empty($all)) return array();

        $kelas_val     = is_numeric($kelas) ? (int)$kelas : (function_exists('parse_kelas_number') ? parse_kelas_number($kelas) : 0);
        $jenjang_clean = !empty($jenjang) ? strtoupper(trim($jenjang)) : '';

        $result   = array();
        $used_ids = array();

        $add_items = function($list) use (&$result, &$used_ids, $limit, $random) {
            if ($random) shuffle($list);
            foreach ($list as $item) {
                if ($limit > 0 && count($result) >= $limit) break;
                if (!isset($used_ids[$item['id']])) {
                    $used_ids[$item['id']] = true;
                    $result[] = $item;
                }
            }
        };

        // Pass 1: Strict Match (Mapel + Exact Kelas + Jenjang)
        $p1 = array();
        foreach ($all as $item) {
            $match_mapel   = $this->match_mapel($mapel, $item['mapel']);
            $match_jenjang = empty($jenjang_clean) || (strtoupper($item['jenjang']) === $jenjang_clean);
            $match_kelas   = ($kelas_val <= 0) || ((int)$item['kelas'] === $kelas_val);

            if ($match_mapel && $match_jenjang && $match_kelas) {
                $p1[] = $item;
            }
        }
        $add_items($p1);

        // Pass 2: Match Mapel + Jenjang (other classes in same school level)
        if ($limit <= 0 || count($result) < $limit) {
            $p2 = array();
            foreach ($all as $item) {
                $match_mapel   = $this->match_mapel($mapel, $item['mapel']);
                $match_jenjang = empty($jenjang_clean) || (strtoupper($item['jenjang']) === $jenjang_clean);

                if ($match_mapel && $match_jenjang) {
                    $p2[] = $item;
                }
            }
            $add_items($p2);
        }

        // Pass 3: Match Mapel (any level/class for same subject)
        if ($limit <= 0 || count($result) < $limit) {
            $p3 = array();
            foreach ($all as $item) {
                $match_mapel = $this->match_mapel($mapel, $item['mapel']);
                if ($match_mapel) {
                    $p3[] = $item;
                }
            }
            $add_items($p3);
        }

        return $result;
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

    public function save_to_gudang_soal($items, $mapel_name, $kelas = 0, $jenjang = 'SMA', $sumber = 'Kontributor AI & Manual')
    {
        if (empty($items) || !is_array($items)) return 0;

        $master_path = FCPATH . 'application/data/gudang_soal/gudang_soal_master.json';
        $index_path  = FCPATH . 'application/data/gudang_soal/gudang_soal_index.json';

        if (!file_exists($master_path)) return 0;

        $raw_master = file_get_contents($master_path);
        $master = json_decode($raw_master, true);
        if (!is_array($master)) $master = array();

        $start_id = count($master);
        $added_count = 0;

        $clean_mapel = preg_replace('/\b(SD|SMP|SMA|SMK|Wajib|Peminatan|Terpadu|Kesenian|Utama)\b/i', '', $mapel_name);
        $clean_mapel = trim($clean_mapel);
        if (empty($clean_mapel)) $clean_mapel = $mapel_name;

        $kelas_val = is_numeric($kelas) ? (int)$kelas : (function_exists('parse_kelas_number') ? parse_kelas_number($kelas) : 0);
        $jenjang_val = !empty($jenjang) ? strtoupper(trim($jenjang)) : 'SMA';

        foreach ($items as $item) {
            $pertanyaan = isset($item['pertanyaan']) ? trim($item['pertanyaan']) : '';
            if (empty($pertanyaan)) continue;

            $is_dup = false;
            foreach ($master as $existing) {
                if (mb_strtolower(trim($existing['pertanyaan'])) === mb_strtolower($pertanyaan)) {
                    $is_dup = true;
                    break;
                }
            }
            if ($is_dup) continue;

            $new_entry = array(
                'id' => $start_id++,
                'sumber' => $sumber,
                'jenjang' => $jenjang_val,
                'kelas' => $kelas_val,
                'mapel' => $clean_mapel,
                'pertanyaan' => $pertanyaan,
                'jenis' => isset($item['jenis']) ? $item['jenis'] : 'Pilihan Ganda',
                'pilihan_a' => isset($item['pilihan_a']) ? $item['pilihan_a'] : '',
                'pilihan_b' => isset($item['pilihan_b']) ? $item['pilihan_b'] : '',
                'pilihan_c' => isset($item['pilihan_c']) ? $item['pilihan_c'] : '',
                'pilihan_d' => isset($item['pilihan_d']) ? $item['pilihan_d'] : '',
                'pilihan_e' => isset($item['pilihan_e']) ? $item['pilihan_e'] : '',
                'kunci_jawaban' => isset($item['kunci_jawaban']) ? strtoupper(trim($item['kunci_jawaban'])) : 'A',
                'pembahasan' => isset($item['pembahasan']) ? $item['pembahasan'] : ''
            );

            $master[] = $new_entry;
            $added_count++;
        }

        if ($added_count > 0) {
            file_put_contents($master_path, json_encode($master, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $categories = array();
            foreach ($master as $m) {
                $j = isset($m['jenjang']) ? $m['jenjang'] : 'SMA';
                $map = isset($m['mapel']) ? $m['mapel'] : 'Umum';
                $k = isset($m['kelas']) ? (int)$m['kelas'] : 0;
                $key = "$j|$map|$k";
                if (!isset($categories[$key])) {
                    $categories[$key] = array('jenjang' => $j, 'mapel' => $map, 'kelas' => $k, 'jumlah' => 0);
                }
                $categories[$key]['jumlah']++;
            }

            $index_data = array(
                'title' => 'Gudang Soal Repositori IndoMMLU & Kontributor AI/Manual',
                'total_soal' => count($master),
                'generated_at' => date('Y-m-d H:i:s'),
                'categories' => array_values($categories)
            );

            file_put_contents($index_path, json_encode($index_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return $added_count;
    }
}
