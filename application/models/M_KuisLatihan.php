<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_KuisLatihan extends CI_Model {

    public function generate_kuis($murid_id, $mata_pelajaran_id, $jumlah_soal = 10)
    {
        $this->load->model(array('M_GudangSoal', 'M_Mapel', 'M_Murid'));

        // 1. First attempt: get existing questions in DB 'soal' for this subject (only teacher-published bank_soal)
        $this->db->select('soal.id');
        $this->db->from('soal');
        $this->db->join('bank_soal', 'bank_soal.id = soal.bank_soal_id');
        $this->db->where('bank_soal.mata_pelajaran_id', $mata_pelajaran_id);
        $this->db->where('bank_soal.jenis_soal !=', 'Kuis Latihan Gudang Soal');
        $this->db->order_by('RAND()');
        $this->db->limit($jumlah_soal);
        $res = $this->db->get()->result_array();

        // 2. If DB questions for this subject are less than requested, fetch directly from Gudang Soal (14.977 dataset)!
        if (count($res) < $jumlah_soal) {
            $mapel = $this->M_Mapel->get_by_id($mata_pelajaran_id);
            $nama_mapel = isset($mapel['nama_mapel']) ? $mapel['nama_mapel'] : '';
            $base_mapel = preg_replace('/\b(SD|SMP|SMA|SMK|Wajib|Peminatan|Terpadu|Kesenian|Utama)\b/i', '', $nama_mapel);
            $base_mapel = trim($base_mapel);

            $murid = $this->M_Murid->get_by_id($murid_id);
            $kelas_name = isset($murid['nama_kelas']) ? $murid['nama_kelas'] : '';
            $kelas_num  = function_exists('parse_kelas_number') ? parse_kelas_number($kelas_name) : 0;

            $CI =& get_instance();
            $jenjang = isset($mapel['jenjang']) ? $mapel['jenjang'] : $CI->session->userdata('active_jenjang');
            if (empty($jenjang)) {
                if (isset($CI->school_info) && is_array($CI->school_info) && !empty($CI->school_info['jenjang'])) {
                    $jenjang = $CI->school_info['jenjang'];
                } else {
                    $sekolah = $this->db->get('sekolah')->row_array();
                    $jenjang = !empty($sekolah['jenjang']) ? $sekolah['jenjang'] : 'SMA';
                }
            }

            // Fetch questions from 14.977 Gudang Soal dataset
            $gudang_items = $this->M_GudangSoal->get_soal_by_filter($base_mapel, $kelas_num, $jenjang, $jumlah_soal, true);

            if (!empty($gudang_items)) {
                // Ensure a system Bank Soal package exists for Gudang Soal Kuis Latihan
                $system_bank = $this->db->get_where('bank_soal', array(
                    'mata_pelajaran_id' => $mata_pelajaran_id,
                    'jenis_soal' => 'Kuis Latihan Gudang Soal'
                ))->row_array();

                if (!$system_bank) {
                    $default_kelas_id = isset($murid['kelas_id']) ? $murid['kelas_id'] : 1;

                    // Resolve valid guru_id to satisfy foreign key constraint bank_soal_ibfk_3
                    $guru_mapel = $this->db->get_where('guru_mapel', array('mata_pelajaran_id' => $mata_pelajaran_id))->row_array();
                    if ($guru_mapel && !empty($guru_mapel['guru_id'])) {
                        $default_guru_id = $guru_mapel['guru_id'];
                    } else {
                        $first_guru = $this->db->select('id')->get('guru')->row_array();
                        if ($first_guru && !empty($first_guru['id'])) {
                            $default_guru_id = $first_guru['id'];
                        } else {
                            $this->db->insert('guru', array(
                                'nip' => '199000000000000001',
                                'user_id' => 1,
                                'status_kepegawaian' => 'Sistem'
                            ));
                            $default_guru_id = $this->db->insert_id();
                        }
                    }

                    $bank_data = array(
                        'kode_soal' => 'GUDANG-' . strtoupper(substr(md5($base_mapel), 0, 4)) . '-' . rand(100, 999),
                        'judul' => 'Bank Soal Kuis Latihan - ' . $nama_mapel,
                        'mata_pelajaran_id' => $mata_pelajaran_id,
                        'kelas_id' => $default_kelas_id,
                        'guru_id' => $default_guru_id,
                        'jenis_soal' => 'Kuis Latihan Gudang Soal',
                        'jumlah_soal' => 0,
                        'kkm' => 70,
                        'status' => 'Published',
                        'created_by' => 1
                    );
                    $this->db->insert('bank_soal', $bank_data);
                    $system_bank_id = $this->db->insert_id();
                } else {
                    $system_bank_id = $system_bank['id'];
                    $this->db->where('bank_soal_id', $system_bank_id);
                    $this->db->delete('soal');
                }

                // Import Gudang Soal items into 'soal' DB table
                $this->M_GudangSoal->import_to_bank_soal($system_bank_id, $gudang_items);

                // Query again from DB 'soal' table
                $this->db->select('soal.id');
                $this->db->from('soal');
                $this->db->join('bank_soal', 'bank_soal.id = soal.bank_soal_id');
                $this->db->where('bank_soal.mata_pelajaran_id', $mata_pelajaran_id);
                $this->db->order_by('RAND()');
                $this->db->limit($jumlah_soal);
                $res = $this->db->get()->result_array();
            }
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

    public function get_soal_kuis($soal_ids_json, $kuis_id = null)
    {
        $soal_ids = json_decode($soal_ids_json, true);
        if (empty($soal_ids)) return array();

        $this->db->where_in('id', $soal_ids);
        $soal_list = $this->db->get('soal')->result_array();

        if ($kuis_id && !empty($soal_list)) {
            $kuis = $this->get_kuis($kuis_id);
            if ($kuis && isset($kuis['nama_mapel'])) {
                $is_inggris = (stripos($kuis['nama_mapel'], 'Inggris') !== false);
                $has_bad_question = false;

                if ($is_inggris) {
                    foreach ($soal_list as $s) {
                        if (preg_match('/\b(kancil|petani|paragraf|adalah|merupakan|tersusun|diketahui|berikut|sejarah|geografi|taman|raja|kebudayaan|bangsa|peristiwa|pernyataan|dibawah|diatas|kalimat|kutipan|iklan|terdapat|jawaban|pembahasan)\b/i', $s['pertanyaan'])) {
                            $has_bad_question = true;
                            break;
                        }
                    }
                }

                if ($has_bad_question) {
                    $new_kuis_id = $this->generate_kuis($kuis['murid_id'], $kuis['mata_pelajaran_id'], $kuis['jumlah_soal']);
                    if ($new_kuis_id) {
                        $new_kuis = $this->get_kuis($new_kuis_id);
                        if (!empty($new_kuis['soal_ids'])) {
                            $this->db->where('id', $kuis_id);
                            $this->db->update('kuis_latihan', array('soal_ids' => $new_kuis['soal_ids']));

                            $this->db->where('id', $new_kuis_id);
                            $this->db->delete('kuis_latihan');

                            $soal_ids = json_decode($new_kuis['soal_ids'], true);
                            $this->db->where_in('id', $soal_ids);
                            $soal_list = $this->db->get('soal')->result_array();
                        }
                    }
                }
            }
        }

        return $soal_list;
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

    public function generate_kuis_from_bank($murid_id, $bank_soal_id, $jumlah_soal = 10)
    {
        $this->load->model('M_BankSoal');
        $bank = $this->M_BankSoal->get_by_id($bank_soal_id);
        if (!$bank) return false;

        $this->db->select('id');
        $this->db->where('bank_soal_id', $bank_soal_id);
        $this->db->order_by('RAND()');
        $this->db->limit($jumlah_soal);
        $res = $this->db->get('soal')->result_array();

        if (empty($res)) return false;

        $soal_ids = array_column($res, 'id');

        $data = array(
            'murid_id' => $murid_id,
            'mata_pelajaran_id' => $bank['mata_pelajaran_id'],
            'soal_ids' => json_encode($soal_ids),
            'jumlah_soal' => count($soal_ids),
            'tanggal_mulai' => date('Y-m-d H:i:s'),
            'status' => 'Sedang'
        );

        $this->db->insert('kuis_latihan', $data);
        return $this->db->insert_id();
    }

    public function generate_ai_kuis($murid_id, $mata_pelajaran_id, $topik, $jumlah_soal = 10, $tingkat_kesulitan = 'Sedang')
    {
        $this->load->model(array('M_Mapel', 'M_Murid', 'M_BankSoal'));

        $mapel = $this->M_Mapel->get_by_id($mata_pelajaran_id);
        $nama_mapel = isset($mapel['nama_mapel']) ? $mapel['nama_mapel'] : 'Umum';

        $murid = $this->M_Murid->get_by_id($murid_id);
        $kelas_name = isset($murid['nama_kelas']) ? $murid['nama_kelas'] : 'Umum';

        $prompt  = "Kamu adalah pakar pembuat soal kuis sekolah Indonesia yang profesional.\n";
        $prompt .= "Buatkan {$jumlah_soal} butir soal pilihan ganda interaktif untuk:\n";
        $prompt .= "- Mata Pelajaran: {$nama_mapel}\n";
        $prompt .= "- Tingkat/Kelas: {$kelas_name}\n";
        $prompt .= "- Topik Spesifik Kuis: {$topik}\n";
        $prompt .= "- Tingkat Kesulitan: {$tingkat_kesulitan}\n";
        $prompt .= "\nATURAN FORMAT MATEMATIKA: Jangan membungkus angka polos dengan tanda dollar (JANGAN tulis $1$, tulis angka polos 1). Untuk persamaan matematika gunakan LaTeX diapit $ (contoh: $\\frac{1}{2}$).";
        $prompt .= "\nWAJIB: Format output HANYA array JSON murni tanpa pembungkus markdown/backticks. Array berisi JSON object untuk setiap butir soal dengan struktur:\n";
        $prompt .= "[\n";
        $prompt .= "  {\n";
        $prompt .= "    \"pertanyaan\": \"isi kalimat pertanyaan\",\n";
        $prompt .= "    \"jenis\": \"Pilihan Ganda\",\n";
        $prompt .= "    \"pilihan_a\": \"pilihan A\",\n";
        $prompt .= "    \"pilihan_b\": \"pilihan B\",\n";
        $prompt .= "    \"pilihan_c\": \"pilihan C\",\n";
        $prompt .= "    \"pilihan_d\": \"pilihan D\",\n";
        $prompt .= "    \"pilihan_e\": \"pilihan E\",\n";
        $prompt .= "    \"kunci_jawaban\": \"A/B/C/D/E\",\n";
        $prompt .= "    \"pembahasan\": \"penjelasan ringkas pembahasan jawaban\",\n";
        $prompt .= "    \"bobot\": 10,\n";
        $prompt .= "    \"tingkat_kesulitan\": \"{$tingkat_kesulitan}\"\n";
        $prompt .= "  }\n";
        $prompt .= "]\n";

        $raw_text = $this->call_ai_engine($prompt, $jumlah_soal);
        if (empty($raw_text)) return false;

        $this->ensure_db_connection();

        $items = $this->extract_json_array($raw_text);
        if (!is_array($items) || empty($items)) return false;

        $default_kelas_id = isset($murid['kelas_id']) ? $murid['kelas_id'] : 1;
        $guru_mapel = $this->db->get_where('guru_mapel', array('mata_pelajaran_id' => $mata_pelajaran_id))->row_array();
        if ($guru_mapel && !empty($guru_mapel['guru_id'])) {
            $default_guru_id = $guru_mapel['guru_id'];
        } else {
            $first_guru = $this->db->select('id')->get('guru')->row_array();
            $default_guru_id = isset($first_guru['id']) ? $first_guru['id'] : 1;
        }

        $bank_data = array(
            'kode_soal' => 'AI-KUIS-' . date('Ymd') . '-' . rand(100, 999),
            'judul' => 'Kuis AI: ' . $topik . ' (' . $nama_mapel . ')',
            'mata_pelajaran_id' => $mata_pelajaran_id,
            'kelas_id' => $default_kelas_id,
            'guru_id' => $default_guru_id,
            'jenis_soal' => 'Kuis AI',
            'jumlah_soal' => count($items),
            'kkm' => 70,
            'status' => 'Published',
            'created_by' => 1
        );
        $this->db->insert('bank_soal', $bank_data);
        $bank_soal_id = $this->db->insert_id();

        $soal_ids = array();
        $nomor = 1;
        foreach ($items as $item) {
            if (empty($item['pertanyaan'])) continue;
            $soal_data = array(
                'bank_soal_id' => $bank_soal_id,
                'nomor_soal' => $nomor++,
                'pertanyaan' => $item['pertanyaan'],
                'jenis' => 'Pilihan Ganda',
                'pilihan_a' => isset($item['pilihan_a']) ? $item['pilihan_a'] : '',
                'pilihan_b' => isset($item['pilihan_b']) ? $item['pilihan_b'] : '',
                'pilihan_c' => isset($item['pilihan_c']) ? $item['pilihan_c'] : '',
                'pilihan_d' => isset($item['pilihan_d']) ? $item['pilihan_d'] : '',
                'pilihan_e' => isset($item['pilihan_e']) ? $item['pilihan_e'] : '',
                'kunci_jawaban' => isset($item['kunci_jawaban']) ? strtoupper(trim($item['kunci_jawaban'])) : 'A',
                'pembahasan' => isset($item['pembahasan']) ? $item['pembahasan'] : '',
                'bobot' => 10,
                'tingkat_kesulitan' => $tingkat_kesulitan
            );
            $inserted_id = $this->M_BankSoal->insert_soal($soal_data);
            if ($inserted_id) {
                $soal_ids[] = $inserted_id;
            }
        }

        if (empty($soal_ids)) return false;

        $kuis_data = array(
            'murid_id' => $murid_id,
            'mata_pelajaran_id' => $mata_pelajaran_id,
            'soal_ids' => json_encode($soal_ids),
            'jumlah_soal' => count($soal_ids),
            'tanggal_mulai' => date('Y-m-d H:i:s'),
            'status' => 'Sedang'
        );
        $this->db->insert('kuis_latihan', $kuis_data);
        return $this->db->insert_id();
    }

    private function call_ai_engine($prompt, $jumlah = 5)
    {
        $or_max_tokens = min(3000, max(1000, $jumlah * 150));
        $candidate_providers = array('gemini', 'groq', 'github_models', 'openrouter_gpt4o_mini', 'openrouter_gemini');

        foreach ($candidate_providers as $p) {
            if ($p === 'groq') {
                $raw_keys = defined('GROQ_API_KEY') ? GROQ_API_KEY : '';
                if (empty($raw_keys)) continue;
                $keys = array_filter(array_map('trim', explode(',', $raw_keys)));
                if (empty($keys)) continue;
                shuffle($keys);
                $endpoint = defined('GROQ_API_ENDPOINT') ? GROQ_API_ENDPOINT : 'https://api.groq.com/openai/v1/chat/completions';

                foreach ($keys as $k) {
                    $ch = curl_init($endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
                        'model' => 'llama-3.3-70b-versatile',
                        'messages' => array(array('role' => 'user', 'content' => $prompt))
                    )));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $k));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

                    $res = curl_exec($ch);
                    $err = curl_error($ch);
                    curl_close($ch);

                    if (!$err && !empty($res)) {
                        $json = json_decode($res, true);
                        if (isset($json['choices'][0]['message']['content']) && !empty($json['choices'][0]['message']['content'])) {
                            return $json['choices'][0]['message']['content'];
                        }
                    }
                }
            } elseif ($p === 'github_models') {
                $raw_keys = defined('GITHUB_MODELS_API_KEY') ? GITHUB_MODELS_API_KEY : '';
                if (empty($raw_keys)) continue;
                $keys = array_filter(array_map('trim', explode(',', $raw_keys)));
                if (empty($keys)) continue;
                shuffle($keys);
                $endpoint = defined('GITHUB_MODELS_API_ENDPOINT') ? GITHUB_MODELS_API_ENDPOINT : 'https://models.github.ai/inference/chat/completions';

                foreach ($keys as $k) {
                    $ch = curl_init($endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
                        'model' => 'gpt-4o-mini',
                        'messages' => array(array('role' => 'user', 'content' => $prompt))
                    )));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $k,
                        'X-GitHub-Api-Version: 2022-11-28',
                        'User-Agent: Ultimate-School-App'
                    ));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

                    $res = curl_exec($ch);
                    $err = curl_error($ch);
                    curl_close($ch);

                    if (!$err && !empty($res)) {
                        $json = json_decode($res, true);
                        if (isset($json['choices'][0]['message']['content']) && !empty($json['choices'][0]['message']['content'])) {
                            return $json['choices'][0]['message']['content'];
                        }
                    }
                }
            } elseif (strpos($p, 'openrouter') === 0) {
                $raw_keys = defined('OPENROUTER_API_KEY') ? OPENROUTER_API_KEY : '';
                if (empty($raw_keys)) continue;
                $keys = array_filter(array_map('trim', explode(',', $raw_keys)));
                if (empty($keys)) continue;
                shuffle($keys);
                $endpoint = defined('OPENROUTER_API_ENDPOINT') ? OPENROUTER_API_ENDPOINT : 'https://openrouter.ai/api/v1/chat/completions';
                $or_model = ($p === 'openrouter_gemini') ? 'google/gemini-2.5-flash' : 'openai/gpt-4o-mini';

                foreach ($keys as $k) {
                    $ch = curl_init($endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
                        'model' => $or_model,
                        'max_tokens' => $or_max_tokens,
                        'messages' => array(array('role' => 'user', 'content' => $prompt))
                    )));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $k));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

                    $res = curl_exec($ch);
                    $err = curl_error($ch);
                    curl_close($ch);

                    if (!$err && !empty($res)) {
                        $json = json_decode($res, true);
                        if (isset($json['choices'][0]['message']['content']) && !empty($json['choices'][0]['message']['content'])) {
                            return $json['choices'][0]['message']['content'];
                        }
                    }
                }
            } else {
                $raw_keys = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
                if (empty($raw_keys) || $raw_keys === 'YOUR_GEMINI_API_KEY_HERE') continue;
                $keys = array_filter(array_map('trim', explode(',', $raw_keys)));
                if (empty($keys)) continue;
                shuffle($keys);

                $gemini_url = defined('GEMINI_API_ENDPOINT') ? GEMINI_API_ENDPOINT : 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';

                foreach ($keys as $k) {
                    $endpoint = $gemini_url . "?key=" . $k;
                    $ch = curl_init($endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
                        'contents' => array(
                            array('parts' => array(array('text' => $prompt)))
                        )
                    )));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

                    $res = curl_exec($ch);
                    $err = curl_error($ch);
                    curl_close($ch);

                    if (!$err && !empty($res)) {
                        $json = json_decode($res, true);
                        if (isset($json['candidates'][0]['content']['parts'][0]['text']) && !empty($json['candidates'][0]['content']['parts'][0]['text'])) {
                            return $json['candidates'][0]['content']['parts'][0]['text'];
                        }
                    }
                }
            }
        }

        return false;
    }

    private function extract_json_array($raw_text)
    {
        $soal_array = json_decode($raw_text, true);
        if (is_array($soal_array)) return $soal_array;

        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', trim($raw_text));
        $cleaned = preg_replace('/\s*```$/i', '', $cleaned);
        $soal_array = json_decode($cleaned, true);
        if (is_array($soal_array)) return $soal_array;

        $start_pos = strpos($raw_text, '[');
        $end_pos   = strrpos($raw_text, ']');

        if ($start_pos !== false && $end_pos !== false && $end_pos > $start_pos) {
            $json_substr = substr($raw_text, $start_pos, $end_pos - $start_pos + 1);
            $soal_array  = json_decode($json_substr, true);
            if (is_array($soal_array)) return $soal_array;

            $sanitized = preg_replace('/[\x00-\x1F\x7F]/', '', $json_substr);
            $sanitized = preg_replace('/,\s*([\}\]])/', '$1', $sanitized);
            $soal_array = json_decode($sanitized, true);
            if (is_array($soal_array)) return $soal_array;
        }

        if (preg_match_all('/\{[^{}]*"(?:pertanyaan|question)"[^{}]*\}/is', $raw_text, $matches)) {
            $items = array();
            foreach ($matches[0] as $json_obj_str) {
                $obj = json_decode($json_obj_str, true);
                if (is_array($obj)) {
                    $items[] = $obj;
                }
            }
            if (!empty($items)) return $items;
        }

        return false;
    }

    public function ensure_db_connection()
    {
        if (!isset($this->db->conn_id) || !is_object($this->db->conn_id) || @$this->db->conn_id->ping() === false) {
            $this->db->reconnect();
        }
    }
}
