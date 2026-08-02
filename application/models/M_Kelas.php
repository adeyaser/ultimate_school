<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Kelas extends CI_Model {

    public function get_all($tahun_ajaran_id = null, $jenjang = null)
    {
        if ($jenjang === null) {
            $CI =& get_instance();
            $jenjang = $CI->session->userdata('active_jenjang');
        }

        $this->db->select('kelas.*, tahun_ajaran.nama as nama_tahun_ajaran, tahun_ajaran.semester, users.full_name as nama_wali_kelas, guru.nip as nip_wali_kelas, (SELECT COUNT(*) FROM murid WHERE murid.kelas_id = kelas.id AND murid.status_murid = "Aktif") as total_murid');
        $this->db->from('kelas');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id = kelas.tahun_ajaran_id');
        $this->db->join('users', 'users.id = kelas.wali_kelas_id', 'left');
        $this->db->join('guru', 'guru.user_id = users.id', 'left');
        if ($tahun_ajaran_id) {
            $this->db->where('kelas.tahun_ajaran_id', $tahun_ajaran_id);
        }
        if ($jenjang && $jenjang !== 'ALL') {
            $this->db->where('kelas.jenjang', $jenjang);
        }
        $this->db->order_by('kelas.tingkat', 'ASC');
        $this->db->order_by('kelas.nama_kelas', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->select('kelas.*, tahun_ajaran.nama as nama_tahun_ajaran, users.full_name as nama_wali_kelas');
        $this->db->from('kelas');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id = kelas.tahun_ajaran_id');
        $this->db->join('users', 'users.id = kelas.wali_kelas_id', 'left');
        $this->db->where('kelas.id', $id);
        return $this->db->get()->row_array();
    }

    public function insert($data)
    {
        return $this->db->insert('kelas', $data);
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('kelas', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('kelas', array('id' => $id));
    }
}
