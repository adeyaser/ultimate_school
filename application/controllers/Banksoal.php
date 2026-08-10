<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Banksoal extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru'));
        $this->load->model(array('M_BankSoal', 'M_Mapel', 'M_Kelas', 'M_Guru', 'M_GudangSoal'));
    }

    public function index()
    {
        $data['title'] = 'Repositori Bank Soal';
        $guru_id = null;
        if ($this->role === 'guru') {
            $guru = $this->M_Guru->get_by_user_id($this->user_data['id']);
            $guru_id = isset($guru['id']) ? $guru['id'] : null;
        }

        $active_jenjang = $this->session->userdata('active_jenjang');
        if (empty($active_jenjang)) {
            $active_jenjang = isset($this->school_info['jenjang']) ? $this->school_info['jenjang'] : 'SMP';
        }

        $data['active_jenjang'] = $active_jenjang;
        $data['bank_list']      = $this->M_BankSoal->get_all($guru_id, $active_jenjang);
        $data['mapel_list']     = $this->M_Mapel->get_all($active_jenjang);
        $data['kelas_list']     = $this->M_Kelas->get_all(null, $active_jenjang);
        $data['gudang_index']   = $this->M_GudangSoal->get_index_data();

        $this->render_page('bank_soal/index', $data);
    }

    public function simpan_bank()
    {
        $guru = $this->M_Guru->get_by_user_id($this->user_data['id']);
        $guru_id = isset($guru['id']) ? $guru['id'] : 1;

        $data = array(
            'kode_soal' => 'BS-' . date('Ym') . '-' . rand(100, 999),
            'mata_pelajaran_id' => $this->input->post('mata_pelajaran_id', true),
            'kelas_id' => $this->input->post('kelas_id', true),
            'guru_id' => $guru_id,
            'judul' => $this->input->post('judul', true),
            'jenis_soal' => $this->input->post('jenis_soal', true),
            'tingkat_kesulitan' => $this->input->post('tingkat_kesulitan', true),
            'durasi' => $this->input->post('durasi', true),
            'kkm' => $this->input->post('kkm', true),
            'status' => 'Published',
            'created_by' => $this->user_data['id']
        );

        $this->M_BankSoal->insert_bank_soal($data);
        $this->session->set_flashdata('success', 'Paket Bank Soal berhasil dibuat.');
        redirect('banksoal');
    }

    public function detail($bank_soal_id)
    {
        $data['title']        = 'Kelola Detail Soal Ujian';
        $data['bank_soal']    = $this->M_BankSoal->get_by_id($bank_soal_id);
        $data['soal_list']    = $this->M_BankSoal->get_soal_by_bank($bank_soal_id);
        $data['gudang_index'] = $this->M_GudangSoal->get_index_data();

        $this->render_page('bank_soal/detail', $data);
    }

    public function import_gudang_soal()
    {
        $bank_soal_id = $this->input->post('bank_soal_id', true);
        $jumlah       = (int)$this->input->post('jumlah', true);
        $is_random    = $this->input->post('mode', true) !== 'seq';

        if (empty($bank_soal_id)) {
            $this->session->set_flashdata('error', 'ID Bank Soal tidak valid.');
            redirect('banksoal');
        }

        $bank_soal = $this->M_BankSoal->get_by_id($bank_soal_id);
        if (!$bank_soal) {
            $this->session->set_flashdata('error', 'Bank soal tidak ditemukan.');
            redirect('banksoal');
        }

        $mapel   = isset($bank_soal['nama_mapel']) ? $bank_soal['nama_mapel'] : '';
        $kelas   = isset($bank_soal['nama_kelas']) ? $bank_soal['nama_kelas'] : '';
        $jenjang = isset($bank_soal['jenjang']) ? $bank_soal['jenjang'] : '';

        preg_match('/\d+/', $kelas, $m);
        $kelas_no = isset($m[0]) ? (int)$m[0] : null;

        $items = $this->M_GudangSoal->get_soal_by_filter($mapel, $kelas_no, $jenjang, $jumlah, $is_random);
        $inserted = $this->M_GudangSoal->import_to_bank_soal($bank_soal_id, $items);

        if ($inserted > 0) {
            $this->session->set_flashdata('success', "Berhasil mengimpor $inserted butir soal dari Repositori Gudang Soal IndoMMLU ($mapel $kelas).");
        } else {
            $this->session->set_flashdata('error', 'Tidak ada soal yang berhasil diimpor dari Gudang Soal.');
        }

        redirect('banksoal/detail/' . $bank_soal_id);
    }

    public function export_word($bank_soal_id)
    {
        $bank_soal = $this->M_BankSoal->get_by_id($bank_soal_id);
        if (!$bank_soal) {
            $this->session->set_flashdata('error', 'Data Bank Soal tidak ditemukan.');
            redirect('banksoal');
        }

        $soal_list = $this->M_BankSoal->get_soal_by_bank($bank_soal_id);

        $data['bank_soal']   = $bank_soal;
        $data['soal_list']   = $soal_list;
        $data['school_info'] = $this->school_info;

        $clean_title = preg_replace('/[^a-zA-Z0-9_-]/', '_', $bank_soal['judul']);
        $filename    = "Naskah_Soal_" . $clean_title . "_" . date('Ymd_His') . ".doc";

        header("Content-Type: application/vnd.ms-word; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: private, max-age=0, must-revalidate");
        header("Pragma: public");

        $this->load->view('bank_soal/export_word', $data);
    }

    public function simpan_soal_item()
    {
        $bank_soal_id = $this->input->post('bank_soal_id', true);
        $pertanyaan_post = $this->input->post('pertanyaan');

        if (is_array($pertanyaan_post)) {
            $jenis_arr      = $this->input->post('jenis');
            $pilihan_a_arr   = $this->input->post('pilihan_a');
            $pilihan_b_arr   = $this->input->post('pilihan_b');
            $pilihan_c_arr   = $this->input->post('pilihan_c');
            $pilihan_d_arr   = $this->input->post('pilihan_d');
            $pilihan_e_arr   = $this->input->post('pilihan_e');
            $kunci_arr      = $this->input->post('kunci_jawaban');
            $pembahasan_arr = $this->input->post('pembahasan');
            $bobot_arr      = $this->input->post('bobot');

            $existing = $this->M_BankSoal->get_soal_by_bank($bank_soal_id);
            $start_nomor = count($existing) + 1;
            $count = 0;

            for ($i = 0; $i < count($pertanyaan_post); $i++) {
                if (empty(trim($pertanyaan_post[$i]))) continue;

                $data = array(
                    'bank_soal_id' => $bank_soal_id,
                    'nomor_soal'   => $start_nomor + $count,
                    'pertanyaan'   => $pertanyaan_post[$i],
                    'jenis'        => isset($jenis_arr[$i]) ? $jenis_arr[$i] : 'Pilihan Ganda',
                    'pilihan_a'    => isset($pilihan_a_arr[$i]) ? $pilihan_a_arr[$i] : '',
                    'pilihan_b'    => isset($pilihan_b_arr[$i]) ? $pilihan_b_arr[$i] : '',
                    'pilihan_c'    => isset($pilihan_c_arr[$i]) ? $pilihan_c_arr[$i] : '',
                    'pilihan_d'    => isset($pilihan_d_arr[$i]) ? $pilihan_d_arr[$i] : '',
                    'pilihan_e'    => isset($pilihan_e_arr[$i]) ? $pilihan_e_arr[$i] : '',
                    'kunci_jawaban'=> isset($kunci_arr[$i]) ? $kunci_arr[$i] : '',
                    'pembahasan'   => isset($pembahasan_arr[$i]) ? $pembahasan_arr[$i] : '',
                    'bobot'        => isset($bobot_arr[$i]) && $bobot_arr[$i] ? $bobot_arr[$i] : 10,
                    'tingkat_kesulitan' => 'Sedang'
                );

                $this->M_BankSoal->insert_soal($data);
                $count++;
            }

            $this->session->set_flashdata('success', "$count Butir soal berhasil ditambahkan ke repositori.");
        } else {
            $existing = $this->M_BankSoal->get_soal_by_bank($bank_soal_id);
            $nomor = count($existing) + 1;

            $data = array(
                'bank_soal_id' => $bank_soal_id,
                'nomor_soal'   => $nomor,
                'pertanyaan'   => $this->input->post('pertanyaan', true),
                'jenis'        => $this->input->post('jenis', true),
                'pilihan_a'    => $this->input->post('pilihan_a', true),
                'pilihan_b'    => $this->input->post('pilihan_b', true),
                'pilihan_c'    => $this->input->post('pilihan_c', true),
                'pilihan_d'    => $this->input->post('pilihan_d', true),
                'pilihan_e'    => $this->input->post('pilihan_e', true),
                'kunci_jawaban'=> $this->input->post('kunci_jawaban', true),
                'pembahasan'   => $this->input->post('pembahasan', true),
                'bobot'        => $this->input->post('bobot', true) ? $this->input->post('bobot', true) : 10,
                'tingkat_kesulitan' => 'Sedang'
            );

            $this->M_BankSoal->insert_soal($data);
            $this->session->set_flashdata('success', 'Soal berhasil ditambahkan ke repositori.');
        }

        redirect('banksoal/detail/' . $bank_soal_id);
    }

    public function hapus_soal($soal_id, $bank_soal_id)
    {
        $this->M_BankSoal->delete_soal($soal_id, $bank_soal_id);
        $this->session->set_flashdata('success', 'Soal berhasil dihapus.');
        redirect('banksoal/detail/' . $bank_soal_id);
    }

    public function import_massal()
    {
        $bank_soal_id = $this->input->post('bank_soal_id', true);
        $raw_text     = $this->input->post('raw_text');

        if (empty($raw_text)) {
            $this->session->set_flashdata('error', 'Teks soal massal tidak boleh kosong.');
            redirect('banksoal/detail/' . $bank_soal_id);
            return;
        }

        $total_imported = $this->M_BankSoal->parse_and_import_bulk_soal($bank_soal_id, $raw_text);

        if ($total_imported > 0) {
            $this->session->set_flashdata('success', "Berhasil mengimpor $total_imported soal & kunci jawaban otomatis ke bank soal!");
        } else {
            $this->session->set_flashdata('error', 'Gagal memproses teks soal. Pastikan format penulisan nomor soal (1. 2. 3.) dan opsi (a. b. c. d.) sesuai.');
        }

        redirect('banksoal/detail/' . $bank_soal_id);
    }

    public function download_template()
    {
        $this->load->helper('download');

        $template_content = "TEMPLAT PENULISAN SOAL MASSAL (ULTIMATE SCHOOL SYSTEM)
====================================================================
PETUNJUK STANDAR PENULISAN:
1. Teks bacaan/wacana/puisi (jika ada) ditulis sebelum nomor soal.
2. Setiap butir pertanyaan diawali nomor angka & titik (contoh: 1.  2.  3.).
3. Pilihan opsi jawaban diawali huruf kecil & titik (a.  b.  c.  d.  e.).
4. Di bagian paling bawah, sertakan tabel/blok 'KUNCI JAWABAN + PEMBAHASAN'.
====================================================================

Teks Bacaan untuk soal nomor 1 dan 2:
Liburan sekolah kali ini, Rina dan keluarga pergi ke pantai. Mereka berangkat pagi-pagi sekali menggunakan mobil. Di perjalanan, mereka melihat pemandangan sawah yang hijau. Sesampainya di pantai, Rina sangat senang. Ia bermain pasir dan membuat istana pasir yang besar. Ayahnya berenang di laut, sedangkan ibunya duduk di bawah payung sambil membaca buku.

1. Apa yang dilakukan Rina di pantai?
a. Berenang di laut
b. Membaca buku
c. Membuat istana pasir
d. Menyiram sawah

2. Kapan Rina dan keluarganya berangkat ke pantai?
a. Siang hari
b. Pagi-pagi sekali
c. Sore hari
d. Malam hari

3. Perhatikan kalimat berikut!
\"Adik menangis karena jatuh dari sepeda.\"
Kata penghubung yang dicetak tebal (karena) menyatakan hubungan....
a. Waktu
b. Tujuan
c. Sebab-akibat
d. Cara

4. Bacalah puisi pendek di bawah ini!
Pagi ini mentari tersenyum
Burung-burung bernyanyi riang
Udara segar menerpa wajah
Semangat baru datang menyapa

Puisi di atas menggambarkan suasana....
a. Menyedihkan
b. Menakutkan
c. Meriah
d. Gembira

KUNCI JAWABAN + PEMBAHASAN
No\tJawaban\tPembahasan
1\tC\tMembuat istana pasir
2\tB\tPagi-pagi sekali
3\tC\tSebab-akibat
4\tD\tSuasana gembira";

        force_download('template_impor_soal_massal.txt', $template_content);
    }

    public function generate_ai_soal()
    {
        header('Content-Type: application/json');

        if ($this->input->method() !== 'post') {
            echo json_encode(array('status' => 'error', 'message' => 'Metode request tidak valid.'));
            return;
        }

        $provider = $this->input->post('provider', true);
        if (empty($provider)) {
            $provider = 'auto';
        }
        $bank_soal_id       = $this->input->post('bank_soal_id', true);
        $topik              = $this->input->post('topik', true);
        $jumlah             = (int)$this->input->post('jumlah', true);
        $jenis              = $this->input->post('jenis', true);
        $tingkat_kesulitan  = $this->input->post('tingkat_kesulitan', true);
        $instruksi_tambahan = $this->input->post('instruksi_tambahan', true);

        if (empty($bank_soal_id) || empty($topik)) {
            echo json_encode(array('status' => 'error', 'message' => 'Topik materi dan ID Bank Soal wajib diisi.'));
            return;
        }

        $bank_soal = $this->M_BankSoal->get_by_id($bank_soal_id);
        if (!$bank_soal) {
            echo json_encode(array('status' => 'error', 'message' => 'Data Bank Soal tidak ditemukan.'));
            return;
        }

        if ($jumlah <= 0 || $jumlah > 100) {
            $jumlah = 5;
        }

        $mapel      = isset($bank_soal['nama_mapel']) ? $bank_soal['nama_mapel'] : '';
        $kelas      = isset($bank_soal['nama_kelas']) ? $bank_soal['nama_kelas'] : '';
        $judul_bank = isset($bank_soal['judul']) ? $bank_soal['judul'] : '';

        $prompt  = "Kamu adalah pakar pembuat soal ujian sekolah Indonesia yang profesional.\n";
        $prompt .= "Buatkan {$jumlah} butir soal ujian untuk:\n";
        $prompt .= "- Mata Pelajaran: {$mapel}\n";
        $prompt .= "- Kelas/Tingkat: {$kelas}\n";
        $prompt .= "- Judul Ujian/Materi: {$judul_bank}\n";
        $prompt .= "- Topik Spesifik: {$topik}\n";
        $prompt .= "- Jenis Soal: {$jenis}\n";
        $prompt .= "- Tingkat Kesulitan: {$tingkat_kesulitan}\n";
        if (!empty($instruksi_tambahan)) {
            $prompt .= "- Instruksi Tambahan: {$instruksi_tambahan}\n";
        }

        $prompt .= "\nATURAN FORMAT MATEMATIKA: Jangan membungkus angka polos dengan tanda dollar (JANGAN tulis $1$, tulis angka polos 1). Untuk pecahan/persamaan matematika, gunakan format LaTeX yang bersih diapit $ (contoh: $\\frac{1}{4} + \\frac{1}{2}$).";
        $prompt .= "\nWAJIB: Format output HANYA array JSON murni tanpa pembungkus markdown/backticks. Array berisi JSON object untuk setiap butir soal dengan struktur:\n";
        $prompt .= "[\n";
        $prompt .= "  {\n";
        $prompt .= "    \"pertanyaan\": \"isi kalimat pertanyaan\",\n";
        $prompt .= "    \"jenis\": \"Pilihan Ganda atau Essay\",\n";
        $prompt .= "    \"pilihan_a\": \"pilihan A\",\n";
        $prompt .= "    \"pilihan_b\": \"pilihan B\",\n";
        $prompt .= "    \"pilihan_c\": \"pilihan C\",\n";
        $prompt .= "    \"pilihan_d\": \"pilihan D\",\n";
        $prompt .= "    \"pilihan_e\": \"pilihan E\",\n";
        $prompt .= "    \"kunci_jawaban\": \"A/B/C/D/E untuk Pilihan Ganda, atau kata kunci jawaban jika Essay\",\n";
        $prompt .= "    \"pembahasan\": \"penjelasan ringkas pembahasan jawaban\",\n";
        $prompt .= "    \"bobot\": 10,\n";
        $prompt .= "    \"tingkat_kesulitan\": \"{$tingkat_kesulitan}\"\n";
        $prompt .= "  }\n";
        $prompt .= "]\n";

        $raw_text = '';
        $used_provider_name = 'AI Engine';
        $last_error = 'Tidak ada provider AI yang berhasil memproses pembuatan soal.';
        $or_max_tokens = min(2500, max(800, $jumlah * 120));

        // Define provider execution order with auto-fallback cascade
        $candidate_providers = array();

        if (empty($provider) || $provider === 'auto') {
            $candidate_providers = array('gemini', 'groq', 'github_models', 'openrouter_gpt4o_mini', 'openrouter_gpt4o', 'openrouter_gemini');
        } else {
            $candidate_providers = array($provider, 'gemini', 'groq', 'github_models', 'openrouter_gpt4o_mini', 'openrouter_gpt4o');
            $candidate_providers = array_values(array_unique($candidate_providers));
        }

        foreach ($candidate_providers as $p) {
            if ($p === 'groq') {
                $raw_groq_keys = defined('GROQ_API_KEY') ? GROQ_API_KEY : '';
                if (empty($raw_groq_keys)) continue;

                $groq_key_list = array_filter(array_map('trim', explode(',', $raw_groq_keys)));
                if (empty($groq_key_list)) continue;

                shuffle($groq_key_list);

                $endpoint = defined('GROQ_API_ENDPOINT') ? GROQ_API_ENDPOINT : 'https://api.groq.com/openai/v1/chat/completions';

                foreach ($groq_key_list as $active_groq_key) {
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
                        'Authorization: Bearer ' . $active_groq_key
                    ));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 180);

                    $response   = curl_exec($ch);
                    $curl_err   = curl_error($ch);
                    curl_close($ch);

                    if (!$curl_err && !empty($response)) {
                        $res_json = json_decode($response, true);
                        if (isset($res_json['choices'][0]['message']['content']) && !empty($res_json['choices'][0]['message']['content'])) {
                            $raw_text = $res_json['choices'][0]['message']['content'];
                            $used_provider_name = 'Groq Cloud AI (Llama 3.3 70B)';
                            break 2;
                        } elseif (isset($res_json['error']['message'])) {
                            $last_error = 'Groq Error: ' . $res_json['error']['message'];
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

                foreach ($gh_key_list as $active_gh_key) {
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
                        'Authorization: Bearer ' . $active_gh_key,
                        'X-GitHub-Api-Version: 2022-11-28',
                        'User-Agent: Ultimate-School-App'
                    ));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 180);

                    $response   = curl_exec($ch);
                    $curl_err   = curl_error($ch);
                    curl_close($ch);

                    if (!$curl_err && !empty($response)) {
                        $res_json = json_decode($response, true);
                        if (isset($res_json['choices'][0]['message']['content']) && !empty($res_json['choices'][0]['message']['content'])) {
                            $raw_text = $res_json['choices'][0]['message']['content'];
                            $used_provider_name = 'GitHub Models (GPT-4o Mini)';
                            break 2;
                        } elseif (isset($res_json['error']['message'])) {
                            $last_error = 'GitHub Models Error: ' . $res_json['error']['message'];
                        }
                    }
                }
            } elseif (strpos($p, 'openrouter') === 0) {
                $raw_or_keys = defined('OPENROUTER_API_KEY') ? OPENROUTER_API_KEY : '';
                if (empty($raw_or_keys)) continue;

                $or_key_list = array_filter(array_map('trim', explode(',', $raw_or_keys)));
                if (empty($or_key_list)) continue;

                shuffle($or_key_list);

                $or_model = 'openai/gpt-4o';
                if ($p === 'openrouter_gpt4o_mini') $or_model = 'openai/gpt-4o-mini';
                elseif ($p === 'openrouter_gemini') $or_model = 'google/gemini-2.5-flash';
                elseif ($p === 'openrouter_deepseek') $or_model = 'deepseek/deepseek-chat';

                $endpoint = defined('OPENROUTER_API_ENDPOINT') ? OPENROUTER_API_ENDPOINT : 'https://openrouter.ai/api/v1/chat/completions';

                foreach ($or_key_list as $active_or_key) {
                    $post_data = array(
                        'model' => $or_model,
                        'max_tokens' => $or_max_tokens,
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
                        'Authorization: Bearer ' . $active_or_key,
                        'HTTP-Referer: http://localhost/ultimate_school',
                        'X-Title: Ultimate School'
                    ));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 180);

                    $response   = curl_exec($ch);
                    $curl_err   = curl_error($ch);
                    curl_close($ch);

                    if (!$curl_err && !empty($response)) {
                        $res_json = json_decode($response, true);
                        if (isset($res_json['choices'][0]['message']['content']) && !empty($res_json['choices'][0]['message']['content'])) {
                            $raw_text = $res_json['choices'][0]['message']['content'];
                            $used_provider_name = 'OpenRouter (' . $or_model . ')';
                            break 2;
                        } elseif (isset($res_json['error']['message'])) {
                            $last_error = 'OpenRouter Error (' . $or_model . '): ' . $res_json['error']['message'];
                        }
                    }
                }
            } else {
                // Direct Gemini API Call with multi-key pool
                $raw_api_keys = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
                if (empty($raw_api_keys) || $raw_api_keys === 'YOUR_GEMINI_API_KEY_HERE') continue;

                $api_key_list = array_filter(array_map('trim', explode(',', $raw_api_keys)));
                if (empty($api_key_list)) continue;

                shuffle($api_key_list);

                $gemini_base_url = defined('GEMINI_API_ENDPOINT') ? GEMINI_API_ENDPOINT : 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';

                foreach ($api_key_list as $active_key) {
                    $endpoint = $gemini_base_url . "?key=" . $active_key;

                    $post_data = array(
                        'contents' => array(
                            array('parts' => array(array('text' => $prompt)))
                        ),
                        'generationConfig' => array(
                            'temperature' => 0.7,
                            'responseMimeType' => 'application/json'
                        )
                    );

                    $ch = curl_init($endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 180);

                    $response   = curl_exec($ch);
                    $curl_err   = curl_error($ch);
                    curl_close($ch);

                    if (!$curl_err && !empty($response)) {
                        $res_json = json_decode($response, true);
                        if (isset($res_json['candidates'][0]['content']['parts'][0]['text'])) {
                            $raw_text = $res_json['candidates'][0]['content']['parts'][0]['text'];
                            $used_provider_name = 'Google Gemini Direct';
                            break 2;
                        } elseif (isset($res_json['error']['message'])) {
                            $last_error = 'Gemini Error: ' . $res_json['error']['message'];
                        }
                    }
                }
            }
        }

        if (empty($raw_text)) {
            echo json_encode(array('status' => 'error', 'message' => $last_error));
            return;
        }

        $this->ensure_db_connection();

        $soal_array = $this->extract_json_array($raw_text);
        $inserted_count = 0;

        if (is_array($soal_array) && !empty($soal_array)) {
            $existing = $this->M_BankSoal->get_soal_by_bank($bank_soal_id);
            $start_nomor = count($existing) + 1;

            foreach ($soal_array as $item) {
                if (empty($item['pertanyaan'])) continue;

                $jenis_item = isset($item['jenis']) && in_array($item['jenis'], array('Essay', 'Pilihan Ganda')) ? $item['jenis'] : 'Pilihan Ganda';
                $diff_item  = isset($item['tingkat_kesulitan']) && in_array($item['tingkat_kesulitan'], array('Mudah', 'Sedang', 'Sulit')) ? $item['tingkat_kesulitan'] : $tingkat_kesulitan;

                $data_soal = array(
                    'bank_soal_id'      => $bank_soal_id,
                    'nomor_soal'        => $start_nomor + $inserted_count,
                    'pertanyaan'        => $item['pertanyaan'],
                    'jenis'             => $jenis_item,
                    'pilihan_a'         => isset($item['pilihan_a']) ? $item['pilihan_a'] : '',
                    'pilihan_b'         => isset($item['pilihan_b']) ? $item['pilihan_b'] : '',
                    'pilihan_c'         => isset($item['pilihan_c']) ? $item['pilihan_c'] : '',
                    'pilihan_d'         => isset($item['pilihan_d']) ? $item['pilihan_d'] : '',
                    'pilihan_e'         => isset($item['pilihan_e']) ? $item['pilihan_e'] : '',
                    'kunci_jawaban'     => isset($item['kunci_jawaban']) ? $item['kunci_jawaban'] : '',
                    'pembahasan'        => isset($item['pembahasan']) ? $item['pembahasan'] : '',
                    'bobot'             => isset($item['bobot']) && is_numeric($item['bobot']) ? (int)$item['bobot'] : 10,
                    'tingkat_kesulitan' => $diff_item
                );

                $this->M_BankSoal->insert_soal($data_soal);
                $inserted_count++;
            }
        } else {
            // Fallback: try parsing as bulk text format
            $inserted_count = $this->M_BankSoal->parse_and_import_bulk_soal($bank_soal_id, $raw_text);
        }

        if ($inserted_count > 0) {
            $this->session->set_flashdata('success', "Berhasil membuat $inserted_count butir soal secara otomatis menggunakan AI ($used_provider_name)!");
            echo json_encode(array(
                'status' => 'success',
                'message' => "$inserted_count soal berhasil digenerate dan disimpan menggunakan $used_provider_name.",
                'provider_name' => $used_provider_name,
                'total_generated' => $inserted_count
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Gagal mengurai format balasan AI. Silakan coba kembali.'));
        }
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
}

