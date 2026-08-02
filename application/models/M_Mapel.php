<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Mapel extends CI_Model {

    public function get_all($jenjang = null)
    {
        if ($jenjang === null) {
            $CI =& get_instance();
            $jenjang = $CI->session->userdata('active_jenjang');
        }
        if ($jenjang && $jenjang !== 'ALL') {
            $this->db->where('jenjang', $jenjang);
        }
        $this->db->order_by('kelompok', 'ASC');
        $this->db->order_by('nama_mapel', 'ASC');
        return $this->db->get('mata_pelajaran')->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('mata_pelajaran', array('id' => $id))->row_array();
    }

    public function insert($data)
    {
        return $this->db->insert('mata_pelajaran', $data);
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('mata_pelajaran', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('mata_pelajaran', array('id' => $id));
    }

    // Guru Mapel
    public function get_guru_mapel($mapel_id = null)
    {
        $this->db->select('guru_mapel.*, guru.nip, users.full_name as nama_guru, mata_pelajaran.kode_mapel, mata_pelajaran.nama_mapel');
        $this->db->from('guru_mapel');
        $this->db->join('guru', 'guru.id = guru_mapel.guru_id');
        $this->db->join('users', 'users.id = guru.user_id');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.id = guru_mapel.mata_pelajaran_id');
        if ($mapel_id) {
            $this->db->where('guru_mapel.mata_pelajaran_id', $mapel_id);
        }
        return $this->db->get()->result_array();
    }

    public function assign_guru_mapel($guru_id, $mapel_id)
    {
        return $this->db->replace('guru_mapel', array(
            'guru_id' => $guru_id,
            'mata_pelajaran_id' => $mapel_id
        ));
    }

    // Jadwal
    public function get_jadwal($kelas_id = null, $tahun_ajaran_id = null)
    {
        $this->db->select('jadwal.*, kelas.nama_kelas, mata_pelajaran.kode_mapel, mata_pelajaran.nama_mapel, users.full_name as nama_guru');
        $this->db->from('jadwal');
        $this->db->join('kelas', 'kelas.id = jadwal.kelas_id');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mata_pelajaran_id');
        $this->db->join('guru', 'guru.id = jadwal.guru_id');
        $this->db->join('users', 'users.id = guru.user_id');
        if ($kelas_id) {
            $this->db->where('jadwal.kelas_id', $kelas_id);
        }
        if ($tahun_ajaran_id) {
            $this->db->where('jadwal.tahun_ajaran_id', $tahun_ajaran_id);
        }
        $this->db->order_by('FIELD(jadwal.hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu")');
        $this->db->order_by('jadwal.jam_mulai', 'ASC');
        return $this->db->get()->result_array();
    }

    public function save_jadwal($data)
    {
        $existing = $this->db->get_where('jadwal', array(
            'kelas_id'  => $data['kelas_id'],
            'hari'      => $data['hari'],
            'jam_mulai' => $data['jam_mulai']
        ))->row_array();

        if ($existing) {
            $this->db->where('id', $existing['id']);
            $this->db->update('jadwal', $data);
            return 'updated';
        } else {
            $this->db->insert('jadwal', $data);
            return 'inserted';
        }
    }

    public function delete_jadwal($id)
    {
        return $this->db->delete('jadwal', array('id' => $id));
    }
}

