<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_OcrSoal extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->ensure_table_exists();
    }

    private function ensure_db()
    {
        if (!isset($this->db->conn_id) || !is_object($this->db->conn_id) || @$this->db->conn_id->ping() === false) {
            @$this->db->close();
            $this->db->initialize();
        }
    }

    private function ensure_table_exists()
    {
        try {
            $this->ensure_db();
            $sql = "CREATE TABLE IF NOT EXISTS `materi_ocr_soal` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `bank_soal_id` int(11) DEFAULT NULL,
              `user_id` int(11) NOT NULL,
              `judul_materi` varchar(255) DEFAULT NULL,
              `image_path` text DEFAULT NULL,
              `ocr_text` longtext NOT NULL,
              `ringkasan_materi` longtext DEFAULT NULL,
              `jumlah_soal` int(11) DEFAULT 5,
              `jenis_soal` varchar(50) DEFAULT 'Pilihan Ganda',
              `tingkat_kesulitan` varchar(50) DEFAULT 'Sedang',
              `generated_json` longtext DEFAULT NULL,
              `status` enum('draft', 'summarized', 'completed') DEFAULT 'draft',
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `bank_soal_id` (`bank_soal_id`),
              KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            @$this->db->query($sql);
        } catch (Exception $e) {} catch (Throwable $e) {}
    }

    public function save_session($data)
    {
        try {
            $this->ensure_db();
            $this->db->insert('materi_ocr_soal', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            return 0;
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function update_session($id, $data)
    {
        try {
            $this->ensure_db();
            $this->db->where('id', $id);
            return $this->db->update('materi_ocr_soal', $data);
        } catch (Exception $e) {
            return false;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function get_session($id)
    {
        try {
            $this->ensure_db();
            $this->db->where('id', $id);
            return $this->db->get('materi_ocr_soal')->row_array();
        } catch (Exception $e) {
            return null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public function get_by_bank($bank_soal_id)
    {
        try {
            $this->ensure_db();
            $this->db->where('bank_soal_id', $bank_soal_id);
            $this->db->order_by('id', 'DESC');
            return $this->db->get('materi_ocr_soal')->result_array();
        } catch (Exception $e) {
            return array();
        } catch (Throwable $e) {
            return array();
        }
    }

    /**
     * Ekstraksi OCR Teks dari Gambar Menggunakan Multimodal Vision AI
     */
    public function extract_text_from_image($image_path, $mime_type = 'image/jpeg')
    {
        if (!file_exists($image_path)) {
            return array('status' => 'error', 'message' => 'Berkas gambar materi tidak ditemukan di server.');
        }

        $image_data = file_get_contents($image_path);
        if (empty($image_data)) {
            return array('status' => 'error', 'message' => 'Berkas gambar kosong.');
        }

        $base64_image = base64_encode($image_data);

        $ocr_prompt = "Kamu adalah sistem OCR (Optical Character Recognition) profesional berakurasi tinggi.\n"
            . "Tugasmu: Ekstrak SELURUH teks, paragraf, judul, poin-poin, rumus/persamaan matematika, tabel, dan butir latihan dari gambar materi pembelajaran ini.\n\n"
            . "ATURAN:\n"
            . "1. Tuliskan teks persis seperti yang tertulis dalam gambar bahasa Indonesia atau bahasa aslinya.\n"
            . "2. Pertahankan urutan paragraf, penomoran, dan poin penting secara terstruktur.\n"
            . "3. Jika ada persamaan matematika atau rumus, tuliskan dalam format LaTeX bersih atau teks jelas.\n"
            . "4. JANGAN menambahkan basa-basi pembuka atau penutup, langsung berikan teks hasil ekstraksi OCR secara lengkap.";

        // 1. Coba Ekstraksi dengan Google Gemini Vision API (Multi-Key Failover)
        $gemini_result = $this->call_gemini_vision($ocr_prompt, $base64_image, $mime_type);
        if ($gemini_result['status'] === 'success' && !empty(trim($gemini_result['text']))) {
            return array(
                'status' => 'success',
                'text' => trim($gemini_result['text']),
                'engine' => 'Google Gemini Vision AI'
            );
        }

        // 2. Coba Ekstraksi dengan OpenRouter Vision (GPT-4o / Gemini Fallback)
        $openrouter_result = $this->call_openrouter_vision($ocr_prompt, $base64_image, $mime_type);
        if ($openrouter_result['status'] === 'success' && !empty(trim($openrouter_result['text']))) {
            return array(
                'status' => 'success',
                'text' => trim($openrouter_result['text']),
                'engine' => 'OpenRouter Vision AI (' . $openrouter_result['model'] . ')'
            );
        }

        $error_msg = isset($gemini_result['message']) ? $gemini_result['message'] : 'Gagal mengekstrak teks dari gambar.';
        return array('status' => 'error', 'message' => $error_msg);
    }

    /**
     * Membuat Ringkasan (Summary) Materi & Poin Kunci Menggunakan AI
     */
    public function generate_summary($ocr_text, $topik = '', $mapel = '', $kelas = '')
    {
        $prompt = "Kamu adalah asisten ahli kurikulum dan materi pembelajaran sekolah Indonesia.\n"
            . "Berikut adalah teks hasil scan OCR dari buku/materi pelajaran:\n"
            . "--- AWAL MATERI OCR ---\n"
            . $ocr_text . "\n"
            . "--- AKHIR MATERI OCR ---\n\n"
            . "Mata Pelajaran: " . ($mapel ? $mapel : 'Umum') . "\n"
            . "Kelas/Tingkat: " . ($kelas ? $kelas : 'Semua Tingkat') . "\n"
            . ($topik ? "Fokus Topik: " . $topik . "\n" : "") . "\n"
            . "TUGAS:\n"
            . "1. Buat RANGKUMAN & KESIMPULAN materi yang padat, jelas, dan komprehensif.\n"
            . "2. Tuliskan POIN-POIN POKOK & KONSEP KUNCI yang paling penting untuk dipahami siswa.\n"
            . "3. Identifikasi TOPIK/INDIKATOR yang sangat layak dijadikan butir soal ujian.\n\n"
            . "Formatkan hasil ringkasan secara rapi dengan judul dan poin-poin bertanda centang/bullet.";

        $ai_response = $this->call_text_ai($prompt, 1500);
        if ($ai_response['status'] === 'success' && !empty(trim($ai_response['text']))) {
            return array(
                'status' => 'success',
                'summary' => trim($ai_response['text']),
                'engine' => $ai_response['engine']
            );
        }

        return array('status' => 'error', 'message' => $ai_response['message']);
    }

    /**
     * Membuat Butir Soal Berdasarkan Hasil Summary & Materi OCR
     */
    public function generate_soal_from_summary($summary_text, $ocr_text, $options = array(), $bank_info = array())
    {
        $jumlah             = isset($options['jumlah']) ? (int)$options['jumlah'] : 5;
        $jenis              = isset($options['jenis']) ? $options['jenis'] : 'Pilihan Ganda';
        $tingkat_kesulitan  = isset($options['tingkat_kesulitan']) ? $options['tingkat_kesulitan'] : 'Sedang';
        $instruksi_tambahan = isset($options['instruksi_tambahan']) ? $options['instruksi_tambahan'] : '';

        $mapel = isset($bank_info['nama_mapel']) ? $bank_info['nama_mapel'] : 'Umum';
        $kelas = isset($bank_info['nama_kelas']) ? $bank_info['nama_kelas'] : 'Umum';
        $judul = isset($bank_info['judul']) ? $bank_info['judul'] : '';

        $prompt = "Kamu adalah guru dan pakar pembuat soal evaluasi belajar sekolah Indonesia yang profesional.\n"
            . "Berdasarkan RINGKASAN MATERI dan TEKS SUMBER berikut, buatkan {$jumlah} butir soal ujian berkualitas tinggi:\n\n"
            . "--- RINGKASAN MATERI & KONSEP KUNCI ---\n"
            . $summary_text . "\n\n"
            . "--- CUPLIKAN MATERI SUMBER (OCR) ---\n"
            . mb_substr($ocr_text, 0, 2000) . "\n\n"
            . "SPESIFIKASI SOAL:\n"
            . "- Mata Pelajaran: {$mapel}\n"
            . "- Kelas: {$kelas}\n"
            . "- Judul Paket: {$judul}\n"
            . "- Jenis Soal: {$jenis}\n"
            . "- Tingkat Kesulitan: {$tingkat_kesulitan}\n"
            . ($instruksi_tambahan ? "- Instruksi Tambahan: {$instruksi_tambahan}\n" : "")
            . "\nATURAN PENTING:\n"
            . "1. Soal HARUS menguji pemahaman konsep, analisis, atau pemecahan masalah berdasarkan materi di atas.\n"
            . "2. Soal harus bervariasi dan tidak boleh repetitif.\n"
            . "3. Format Matematika: Jangan pakai dollar untuk angka tunggal biasa. Untuk rumus/pecahan gunakan LaTeX bersih diapit $ (contoh: $\\frac{1}{2}$).\n"
            . "4. WAJIB format output HANYA array JSON murni tanpa markdown/backticks/teks pembuka/penutup:\n"
            . "[\n"
            . "  {\n"
            . "    \"pertanyaan\": \"teks pertanyaan\",\n"
            . "    \"jenis\": \"{$jenis}\",\n"
            . "    \"pilihan_a\": \"pilihan jawaban A\",\n"
            . "    \"pilihan_b\": \"pilihan jawaban B\",\n"
            . "    \"pilihan_c\": \"pilihan jawaban C\",\n"
            . "    \"pilihan_d\": \"pilihan jawaban D\",\n"
            . "    \"pilihan_e\": \"pilihan jawaban E\",\n"
            . "    \"kunci_jawaban\": \"A/B/C/D/E untuk Pilihan Ganda, atau uraian kunci jika Essay\",\n"
            . "    \"pembahasan\": \"penjelasan ringkas dan relevan dengan materi\",\n"
            . "    \"bobot\": 10,\n"
            . "    \"tingkat_kesulitan\": \"{$tingkat_kesulitan}\"\n"
            . "  }\n"
            . "]\n";

        $ai_response = $this->call_text_ai($prompt, max(1500, $jumlah * 300), true);
        if ($ai_response['status'] !== 'success' || empty($ai_response['text'])) {
            return array('status' => 'error', 'message' => $ai_response['message']);
        }

        $soal_array = $this->extract_json_array($ai_response['text']);
        if (!is_array($soal_array) || empty($soal_array)) {
            // Coba parsing teks konvensional / markdown jika AI menjawab dengan format teks biasa
            $soal_array = $this->parse_bulk_text_to_array($ai_response['text'], $jenis, $tingkat_kesulitan);
        }

        if (!is_array($soal_array) || empty($soal_array)) {
            return array(
                'status' => 'error',
                'message' => 'Gagal menguraikan format JSON dari AI. Silakan coba klik generate ulang.',
                'raw_text' => $ai_response['text']
            );
        }

        return array(
            'status' => 'success',
            'data' => $soal_array,
            'total' => count($soal_array),
            'engine' => $ai_response['engine']
        );
    }

    /**
     * Panggilan Google Gemini Multimodal Vision API
     */
    private function call_gemini_vision($prompt, $base64_image, $mime_type = 'image/jpeg')
    {
        $raw_api_keys = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
        if (empty($raw_api_keys) || $raw_api_keys === 'YOUR_GEMINI_API_KEY_HERE') {
            return array('status' => 'error', 'message' => 'API Key Google Gemini belum dikonfigurasi.');
        }

        $api_key_list = array_filter(array_map('trim', explode(',', $raw_api_keys)));
        if (empty($api_key_list)) {
            return array('status' => 'error', 'message' => 'Daftar API Key Gemini kosong.');
        }

        shuffle($api_key_list);

        $vision_models = array('gemini-flash-latest', 'gemini-3.5-flash', 'gemini-2.5-pro');
        $last_err = 'Gagal menghubungi server Gemini Vision.';

        foreach ($vision_models as $v_model) {
            $endpoint_base = 'https://generativelanguage.googleapis.com/v1beta/models/' . $v_model . ':generateContent';

            foreach ($api_key_list as $active_key) {
                $endpoint = $endpoint_base . "?key=" . $active_key;

                $post_data = array(
                    'contents' => array(
                        array(
                            'parts' => array(
                                array('text' => $prompt),
                                array(
                                    'inline_data' => array(
                                        'mime_type' => $mime_type,
                                        'data' => $base64_image
                                    )
                                )
                            )
                        )
                    ),
                    'generationConfig' => array(
                        'temperature' => 0.1,
                        'maxOutputTokens' => 4096
                    )
                );

                $ch = curl_init($endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 120);

                $response = curl_exec($ch);
                $curl_err = curl_error($ch);
                curl_close($ch);

                if (!$curl_err && !empty($response)) {
                    $res_json = json_decode($response, true);
                    if (isset($res_json['candidates'][0]['content']['parts'][0]['text'])) {
                        $raw_ocr = trim($res_json['candidates'][0]['content']['parts'][0]['text']);
                        // Bersihkan intro/outro basa-basi AI jika ada
                        $clean_ocr = preg_replace('/^(?:Berikut\s+adalah\s+(?:hasil\s+)?(?:ekstraksi\s+)?teks\s+[^\n]*:\s*|Teks\s+hasil\s+OCR:\s*)/i', '', $raw_ocr);
                        $clean_ocr = trim($clean_ocr);

                        return array(
                            'status' => 'success',
                            'text'   => !empty($clean_ocr) ? $clean_ocr : $raw_ocr,
                            'engine' => 'Google Gemini Vision (' . $v_model . ')'
                        );
                    } elseif (isset($res_json['error']['message'])) {
                        $last_err = 'Gemini (' . $v_model . ') Error: ' . $res_json['error']['message'];
                    }
                }
            }
        }

        return array('status' => 'error', 'message' => $last_err);
    }

    /**
     * Panggilan OpenRouter Vision API (Fallback)
     */
    private function call_openrouter_vision($prompt, $base64_image, $mime_type = 'image/jpeg')
    {
        $raw_or_keys = defined('OPENROUTER_API_KEY') ? OPENROUTER_API_KEY : '';
        if (empty($raw_or_keys)) {
            return array('status' => 'error', 'message' => 'OpenRouter API Key belum tersedia.');
        }

        $or_key_list = array_filter(array_map('trim', explode(',', $raw_or_keys)));
        if (empty($or_key_list)) {
            return array('status' => 'error', 'message' => 'Daftar OpenRouter Key kosong.');
        }

        shuffle($or_key_list);

        $endpoint = defined('OPENROUTER_API_ENDPOINT') ? OPENROUTER_API_ENDPOINT : 'https://openrouter.ai/api/v1/chat/completions';
        $model = 'openai/gpt-4o-mini';

        foreach ($or_key_list as $active_key) {
            $post_data = array(
                'model' => $model,
                'max_tokens' => 3000,
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => array(
                            array('type' => 'text', 'text' => $prompt),
                            array(
                                'type' => 'image_url',
                                'image_url' => array(
                                    'url' => 'data:' . $mime_type . ';base64,' . $base64_image
                                )
                            )
                        )
                    )
                )
            );

            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $active_key,
                'HTTP-Referer: http://localhost/ultimate_school',
                'X-Title: Ultimate School OCR Vision'
            ));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);

            $response = curl_exec($ch);
            $curl_err = curl_error($ch);
            curl_close($ch);

            if (!$curl_err && !empty($response)) {
                $res_json = json_decode($response, true);
                if (isset($res_json['choices'][0]['message']['content'])) {
                    return array(
                        'status' => 'success',
                        'text' => $res_json['choices'][0]['message']['content'],
                        'model' => $model
                    );
                }
            }
        }

        return array('status' => 'error', 'message' => 'Gagal memproses Vision OCR melalui OpenRouter.');
    }

    /**
     * Panggilan Multi-AI Text Engine (Gemini -> Groq -> GitHub Models -> OpenRouter)
     */
    private function call_text_ai($prompt, $max_tokens = 2000, $json_mode = false)
    {
        $candidate_providers = array('gemini', 'groq', 'github_models', 'openrouter_gpt4o_mini');
        $last_error = 'Tidak ada AI engine yang merespon.';

        foreach ($candidate_providers as $p) {
            if ($p === 'gemini') {
                $raw_api_keys = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
                if (empty($raw_api_keys) || $raw_api_keys === 'YOUR_GEMINI_API_KEY_HERE') continue;

                $api_key_list = array_filter(array_map('trim', explode(',', $raw_api_keys)));
                if (empty($api_key_list)) continue;
                shuffle($api_key_list);

                $endpoint_base = defined('GEMINI_API_ENDPOINT') ? GEMINI_API_ENDPOINT : 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';

                foreach ($api_key_list as $active_key) {
                    $endpoint = $endpoint_base . "?key=" . $active_key;

                    $post_data = array(
                        'contents' => array(
                            array('parts' => array(array('text' => $prompt)))
                        ),
                        'generationConfig' => array(
                            'temperature' => 0.4,
                            'maxOutputTokens' => max(4096, $max_tokens)
                        )
                    );
                    if ($json_mode) {
                        $post_data['generationConfig']['responseMimeType'] = 'application/json';
                    }

                    $ch = curl_init($endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 120);

                    $response = curl_exec($ch);
                    $curl_err = curl_error($ch);
                    curl_close($ch);

                    if (!$curl_err && !empty($response)) {
                        $res_json = json_decode($response, true);
                        if (isset($res_json['candidates'][0]['content']['parts'][0]['text'])) {
                            return array(
                                'status' => 'success',
                                'text' => $res_json['candidates'][0]['content']['parts'][0]['text'],
                                'engine' => 'Google Gemini AI'
                            );
                        }
                    }
                }
            } elseif ($p === 'groq') {
                $raw_groq_keys = defined('GROQ_API_KEY') ? GROQ_API_KEY : '';
                if (empty($raw_groq_keys)) continue;

                $groq_key_list = array_filter(array_map('trim', explode(',', $raw_groq_keys)));
                if (empty($groq_key_list)) continue;
                shuffle($groq_key_list);

                $endpoint = defined('GROQ_API_ENDPOINT') ? GROQ_API_ENDPOINT : 'https://api.groq.com/openai/v1/chat/completions';

                foreach ($groq_key_list as $active_key) {
                    $post_data = array(
                        'model' => 'llama-3.3-70b-versatile',
                        'messages' => array(
                            array('role' => 'user', 'content' => $prompt)
                        )
                    );

                    $ch = curl_init($endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $active_key
                    ));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 120);

                    $response = curl_exec($ch);
                    $curl_err = curl_error($ch);
                    curl_close($ch);

                    if (!$curl_err && !empty($response)) {
                        $res_json = json_decode($response, true);
                        if (isset($res_json['choices'][0]['message']['content'])) {
                            return array(
                                'status' => 'success',
                                'text' => $res_json['choices'][0]['message']['content'],
                                'engine' => 'Groq Cloud (Llama 3.3 70B)'
                            );
                        }
                    }
                }
            } elseif ($p === 'github_models') {
                $raw_gh_keys = defined('GITHUB_MODELS_API_KEY') ? GITHUB_MODELS_API_KEY : '';
                if (empty($raw_gh_keys)) continue;

                $gh_key_list = array_filter(array_map('trim', explode(',', $raw_gh_keys)));
                if (empty($gh_key_list)) continue;
                shuffle($gh_key_list);

                $endpoint = defined('GITHUB_MODELS_API_ENDPOINT') ? GITHUB_MODELS_API_ENDPOINT : 'https://models.github.ai/inference/chat/completions';

                foreach ($gh_key_list as $active_key) {
                    $post_data = array(
                        'model' => 'gpt-4o-mini',
                        'messages' => array(
                            array('role' => 'user', 'content' => $prompt)
                        )
                    );

                    $ch = curl_init($endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $active_key,
                        'X-GitHub-Api-Version: 2022-11-28',
                        'User-Agent: Ultimate-School-App'
                    ));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 120);

                    $response = curl_exec($ch);
                    $curl_err = curl_error($ch);
                    curl_close($ch);

                    if (!$curl_err && !empty($response)) {
                        $res_json = json_decode($response, true);
                        if (isset($res_json['choices'][0]['message']['content'])) {
                            return array(
                                'status' => 'success',
                                'text' => $res_json['choices'][0]['message']['content'],
                                'engine' => 'GitHub Models (GPT-4o Mini)'
                            );
                        }
                    }
                }
            } elseif (strpos($p, 'openrouter') === 0) {
                $raw_or_keys = defined('OPENROUTER_API_KEY') ? OPENROUTER_API_KEY : '';
                if (empty($raw_or_keys)) continue;

                $or_key_list = array_filter(array_map('trim', explode(',', $raw_or_keys)));
                if (empty($or_key_list)) continue;
                shuffle($or_key_list);

                $endpoint = defined('OPENROUTER_API_ENDPOINT') ? OPENROUTER_API_ENDPOINT : 'https://openrouter.ai/api/v1/chat/completions';

                foreach ($or_key_list as $active_key) {
                    $post_data = array(
                        'model' => 'openai/gpt-4o-mini',
                        'max_tokens' => $max_tokens,
                        'messages' => array(
                            array('role' => 'user', 'content' => $prompt)
                        )
                    );

                    $ch = curl_init($endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $active_key
                    ));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 120);

                    $response = curl_exec($ch);
                    $curl_err = curl_error($ch);
                    curl_close($ch);

                    if (!$curl_err && !empty($response)) {
                        $res_json = json_decode($response, true);
                        if (isset($res_json['choices'][0]['message']['content'])) {
                            return array(
                                'status' => 'success',
                                'text' => $res_json['choices'][0]['message']['content'],
                                'engine' => 'OpenRouter (GPT-4o Mini)'
                            );
                        }
                    }
                }
            }
        }

        return array('status' => 'error', 'message' => $last_error);
    }

    /**
     * Helper Ekstraksi Array JSON dari Teks Balasan AI
     */
    private function extract_json_array($raw_text)
    {
        $parsed = json_decode($raw_text, true);
        $res = $this->normalize_soal_array($parsed);
        if ($res !== false) return $res;

        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', trim($raw_text));
        $cleaned = preg_replace('/\s*```$/i', '', $cleaned);
        $parsed = json_decode($cleaned, true);
        $res = $this->normalize_soal_array($parsed);
        if ($res !== false) return $res;

        $start_pos = strpos($raw_text, '[');
        $end_pos   = strrpos($raw_text, ']');

        if ($start_pos !== false && $end_pos !== false && $end_pos > $start_pos) {
            $json_substr = substr($raw_text, $start_pos, $end_pos - $start_pos + 1);
            $parsed = json_decode($json_substr, true);
            $res = $this->normalize_soal_array($parsed);
            if ($res !== false) return $res;

            $sanitized = preg_replace('/[\x00-\x1F\x7F]/', '', $json_substr);
            $sanitized = preg_replace('/,\s*([\}\]])/', '$1', $sanitized);
            $parsed = json_decode($sanitized, true);
            $res = $this->normalize_soal_array($parsed);
            if ($res !== false) return $res;
        }

        $start_pos_obj = strpos($raw_text, '{');
        $end_pos_obj   = strrpos($raw_text, '}');
        if ($start_pos_obj !== false && $end_pos_obj !== false && $end_pos_obj > $start_pos_obj) {
            $json_substr_obj = substr($raw_text, $start_pos_obj, $end_pos_obj - $start_pos_obj + 1);
            $parsed = json_decode($json_substr_obj, true);
            $res = $this->normalize_soal_array($parsed);
            if ($res !== false) return $res;
        }

        if (preg_match_all('/\{[^{}]*"(?:pertanyaan|question)"[^{}]*\}/is', $raw_text, $matches)) {
            $items = array();
            foreach ($matches[0] as $json_obj_str) {
                $obj = json_decode($json_obj_str, true);
                if (is_array($obj) && !empty($obj['pertanyaan'])) {
                    $items[] = $obj;
                }
            }
            if (!empty($items)) return $items;
        }

        return false;
    }

    private function normalize_soal_array($parsed)
    {
        if (!is_array($parsed)) return false;

        // If it's a direct indexed array of question items
        if (isset($parsed[0]) && is_array($parsed[0])) {
            return $parsed;
        }

        // If it's wrapped in an object key
        foreach (array('soal', 'questions', 'items', 'data', 'soal_list', 'daftar_soal', 'quiz') as $k) {
            if (isset($parsed[$k]) && is_array($parsed[$k]) && !empty($parsed[$k])) {
                return $parsed[$k];
            }
        }

        // Check if the single object is itself a question
        if (isset($parsed['pertanyaan']) && !empty($parsed['pertanyaan'])) {
            return array($parsed);
        }

        return false;
    }

    /**
     * Fallback parser: Mengubah format teks biasa / markdown menjadi array soal terstruktur
     */
    public function parse_bulk_text_to_array($raw_text, $jenis = 'Pilihan Ganda', $tingkat_kesulitan = 'Sedang')
    {
        $raw_text = str_replace("\r\n", "\n", $raw_text);
        
        $soal_text = $raw_text;
        $kunci_text = '';

        if (preg_match('/KUNCI\s+JAWABAN(.*)$/is', $raw_text, $matches, PREG_OFFSET_CAPTURE)) {
            $offset = $matches[0][1];
            $soal_text  = trim(substr($raw_text, 0, $offset));
            $kunci_text = trim(substr($raw_text, $offset));
        }

        // 1. Parse Kunci Jawaban
        $kunci_map = array();
        if (!empty($kunci_text)) {
            $lines = explode("\n", $kunci_text);
            $order_idx = 1;
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || preg_match('/^(KUNCI|SOAL|No\t|No\s+Jawaban|Semoga)/i', $line)) continue;

                if (preg_match('/^(\d+)[\.\s\t]+([A-Ea-e])[\.\s\t\)\-]*([^\n]*)$/i', $line, $m)) {
                    $num = (int)$m[1];
                    $ans = strtoupper($m[2]);
                    $pem = trim($m[3]);
                    $kunci_map[$num] = array('kunci' => $ans, 'pembahasan' => $pem);
                } elseif (preg_match('/^([A-Ea-e])[\s\t\)\-\.]*([^\n]*)$/i', $line, $m)) {
                    $ans = strtoupper($m[1]);
                    $pem = trim($m[2]);
                    $kunci_map[$order_idx] = array('kunci' => $ans, 'pembahasan' => $pem);
                    $order_idx++;
                }
            }
        }

        // 2. Parse Blocks
        $blocks = preg_split('/(?=\n\s*\d+[\.\)]\s+)/', "\n" . $soal_text);
        $result = array();
        $counter = 1;

        foreach ($blocks as $block) {
            $block = trim($block);
            if (empty($block)) continue;

            if (preg_match('/^(\d+)[\.\)]\s*(.*)$/s', $block, $m)) {
                $q_num = (int)$m[1];
                $content = trim($m[2]);

                $lines = explode("\n", $content);
                $q_lines = array();
                $opts = array('A' => '', 'B' => '', 'C' => '', 'D' => '', 'E' => '');
                $curr_opt = null;
                $kunci_inline = '';
                $pembahasan_inline = '';

                foreach ($lines as $line) {
                    $trim_line = trim($line);
                    
                    if (preg_match('/^(?:Kunci|Jawaban(?:\s+Benar)?|Key)[\s\:\-]+([A-Ea-e])\b(?:\s*[\-\–]\s*(.*))?$/i', $trim_line, $km)) {
                        $kunci_inline = strtoupper($km[1]);
                        if (!empty($km[2])) $pembahasan_inline = trim($km[2]);
                        continue;
                    }
                    if (preg_match('/^(?:Pembahasan|Penjelasan)[\s\:\-]+(.*)$/i', $trim_line, $pm)) {
                        $pembahasan_inline = trim($pm[1]);
                        continue;
                    }

                    if (preg_match('/^[\*\-]?\s*([a-eA-E])[\.\)]\s*(.*)$/i', $trim_line, $opt_m)) {
                        $curr_opt = strtoupper($opt_m[1]);
                        $opts[$curr_opt] = trim($opt_m[2]);
                    } elseif ($curr_opt !== null) {
                        $opts[$curr_opt] .= "\n" . $trim_line;
                    } else {
                        $q_lines[] = $line;
                    }
                }

                $q_text = trim(implode("\n", $q_lines));
                if (empty($q_text)) continue;

                $final_kunci = !empty($kunci_inline) ? $kunci_inline : (isset($kunci_map[$q_num]['kunci']) ? $kunci_map[$q_num]['kunci'] : (isset($kunci_map[$counter]['kunci']) ? $kunci_map[$counter]['kunci'] : 'A'));
                $final_pembahasan = !empty($pembahasan_inline) ? $pembahasan_inline : (isset($kunci_map[$q_num]['pembahasan']) ? $kunci_map[$q_num]['pembahasan'] : (isset($kunci_map[$counter]['pembahasan']) ? $kunci_map[$counter]['pembahasan'] : ''));

                $result[] = array(
                    'pertanyaan'        => $q_text,
                    'jenis'             => $jenis,
                    'pilihan_a'         => trim($opts['A']),
                    'pilihan_b'         => trim($opts['B']),
                    'pilihan_c'         => trim($opts['C']),
                    'pilihan_d'         => trim($opts['D']),
                    'pilihan_e'         => trim($opts['E']),
                    'kunci_jawaban'     => $final_kunci,
                    'pembahasan'        => $final_pembahasan,
                    'bobot'             => 10,
                    'tingkat_kesulitan' => $tingkat_kesulitan
                );
                $counter++;
            }
        }

        return !empty($result) ? $result : false;
    }
}
