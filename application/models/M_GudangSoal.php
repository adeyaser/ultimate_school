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
        $m = preg_replace('/\b(sd|smp|sma|smk|wajib|peminatan|terpadu|kesenian|utama|kelompok\s+[a-c]|\bkelas\s+\d+\b)\b/i', '', $m);
        $m = trim(preg_replace('/\s+/', ' ', $m));

        // Exact & specific subject aliases mapping
        if (preg_match('/\b(inggris|english)\b/i', $m)) {
            return 'bahasa inggris';
        }
        if (preg_match('/\b(indonesia)\b/i', $m)) {
            return 'bahasa indonesia';
        }
        if (preg_match('/\b(matematika|mtk|math)\b/i', $m)) {
            return 'matematika';
        }
        if ($m === 'ipa' || preg_match('/\b(ilmu pengetahuan alam|sains|science)\b/i', $m)) {
            return 'ipa';
        }
        if ($m === 'ips' || preg_match('/\b(ilmu pengetahuan sosial|social studies)\b/i', $m)) {
            return 'ips';
        }
        if (preg_match('/\b(fisika|physics)\b/i', $m)) {
            return 'fisika';
        }
        if (preg_match('/\b(kimia|chemistry)\b/i', $m)) {
            return 'kimia';
        }
        if (preg_match('/\b(biologi|biology)\b/i', $m)) {
            return 'biologi';
        }
        if (preg_match('/\b(sejarah|history)\b/i', $m)) {
            return 'sejarah';
        }
        if (preg_match('/\b(geografi|geography)\b/i', $m)) {
            return 'geografi';
        }
        if (preg_match('/\b(sosiologi|sociology)\b/i', $m)) {
            return 'sosiologi';
        }
        if (preg_match('/\b(ekonomi|economy|akuntansi)\b/i', $m)) {
            return 'ekonomi';
        }
        if (preg_match('/\b(pjok|penjaskes|pendidikan jasmani|olahraga)\b/i', $m)) {
            return 'pjok';
        }
        if (preg_match('/\b(ppkn|pkn|pendidikan pancasila|kewarganegaraan)\b/i', $m)) {
            return 'ppkn';
        }
        if (preg_match('/\b(pai|pendidikan agama islam|agama islam)\b/i', $m)) {
            return 'pendidikan agama islam';
        }
        if (preg_match('/\b(seni budaya|seni rupa|seni musik|seni tari)\b/i', $m)) {
            return 'seni budaya';
        }
        if (preg_match('/\b(prakarya|pkwu|kewirausahaan)\b/i', $m)) {
            return 'prakarya';
        }
        if (preg_match('/\b(informatika|tik|komputer)\b/i', $m)) {
            return 'informatika';
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

        // Exact match
        if ($norm_search === $norm_item) {
            return true;
        }

        // Distinct short subjects that must never cross-match
        $strict_subjects = array(
            'ipa', 'ips', 'pjok', 'ppkn', 'tik', 'informatika', 'fisika', 'kimia', 'biologi',
            'sejarah', 'geografi', 'sosiologi', 'ekonomi', 'matematika',
            'bahasa inggris', 'bahasa indonesia', 'bahasa sunda', 'bahasa jawa',
            'bahasa bali', 'bahasa lampung', 'bahasa madura', 'bahasa makassar',
            'bahasa banjar', 'bahasa dayak ngaju', 'budaya alam minangkabau'
        );

        if (in_array($norm_search, $strict_subjects) || in_array($norm_item, $strict_subjects)) {
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

        $result      = array();
        $used_ids    = array();
        $used_hashes = array();

        // Helper to validate and deduplicate questions
        $is_valid_and_unique = function($item) use (&$used_ids, &$used_hashes, $mapel) {
            if (empty($item['pertanyaan']) || empty($item['id'])) return false;
            if (isset($used_ids[$item['id']])) return false;

            // Content deduplication hash
            $clean_text = preg_replace('/[\s\W]+/u', '', mb_strtolower(trim($item['pertanyaan'])));
            if (mb_strlen($clean_text) < 5) return false;
            $hash = md5($clean_text);
            if (isset($used_hashes[$hash])) return false;

            // Special language verification
            $norm_search = $this->normalize_mapel_name($mapel);
            if ($norm_search === 'bahasa inggris') {
                // Must not contain obvious Indonesian words in English questions
                if (preg_match('/\b(tersusun dalam bentuk|paragraf di atas|tokoh utama|watak|latar tempat|pemakaian tanda baca)\b/i', $item['pertanyaan'])) {
                    return false;
                }
            }

            $used_ids[$item['id']] = true;
            $used_hashes[$hash]    = true;
            return true;
        };

        $add_items = function($list) use (&$result, $limit, $random, $is_valid_and_unique) {
            if ($random) {
                shuffle($list);
            }
            foreach ($list as $item) {
                if ($limit > 0 && count($result) >= $limit) break;
                if ($is_valid_and_unique($item)) {
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

        if ($random) {
            shuffle($result);
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

    public function build_question_key($pertanyaan, $pilihan_a = '', $pilihan_b = '')
    {
        $clean = preg_replace('/[\s\W]+/u', '', mb_strtolower(trim($pertanyaan)));
        if (mb_strlen($clean) > 10) {
            return md5($clean);
        } else {
            $pa = preg_replace('/[\s\W]+/u', '', mb_strtolower(trim($pilihan_a)));
            $pb = preg_replace('/[\s\W]+/u', '', mb_strtolower(trim($pilihan_b)));
            return md5($clean . '|' . $pa . '|' . $pb);
        }
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

        // Build seen keys map for fast duplicate detection
        $seen_keys = array();
        $max_id = 0;
        foreach ($master as $existing) {
            $p  = isset($existing['pertanyaan']) ? $existing['pertanyaan'] : '';
            $pa = isset($existing['pilihan_a']) ? $existing['pilihan_a'] : '';
            $pb = isset($existing['pilihan_b']) ? $existing['pilihan_b'] : '';
            $key = $this->build_question_key($p, $pa, $pb);
            $seen_keys[$key] = true;

            $eid = isset($existing['id']) ? (int)$existing['id'] : 0;
            if ($eid > $max_id) {
                $max_id = $eid;
            }
        }

        $next_id = $max_id + 1;
        $added_count = 0;

        $clean_mapel = preg_replace('/\b(SD|SMP|SMA|SMK|Wajib|Peminatan|Terpadu|Kesenian|Utama)\b/i', '', $mapel_name);
        $clean_mapel = trim($clean_mapel);
        if (empty($clean_mapel)) $clean_mapel = $mapel_name;

        $kelas_val = is_numeric($kelas) ? (int)$kelas : (function_exists('parse_kelas_number') ? parse_kelas_number($kelas) : 0);
        $jenjang_val = !empty($jenjang) ? strtoupper(trim($jenjang)) : 'SMA';

        foreach ($items as $item) {
            $pertanyaan = isset($item['pertanyaan']) ? trim($item['pertanyaan']) : '';
            if (empty($pertanyaan)) continue;

            $pa = isset($item['pilihan_a']) ? $item['pilihan_a'] : '';
            $pb = isset($item['pilihan_b']) ? $item['pilihan_b'] : '';
            $key = $this->build_question_key($pertanyaan, $pa, $pb);

            // Skip if question already exists in Gudang Soal master
            if (isset($seen_keys[$key])) {
                continue;
            }
            $seen_keys[$key] = true;

            $new_entry = array(
                'id' => $next_id++,
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
