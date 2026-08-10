<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_KuisLatihan extends CI_Model {

    public function generate_kuis($murid_id, $mata_pelajaran_id, $jumlah_soal = 10)
    {
        $this->load->model(array('M_GudangSoal', 'M_Mapel', 'M_Murid'));

        // 1. First attempt: get existing questions in DB 'soal' for this subject
        $this->db->select('soal.id');
        $this->db->from('soal');
        $this->db->join('bank_soal', 'bank_soal.id = soal.bank_soal_id');
        $this->db->where('bank_soal.mata_pelajaran_id', $mata_pelajaran_id);
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

        // 3. Fallback: if no questions found for specific subject, fetch any available in repository
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
