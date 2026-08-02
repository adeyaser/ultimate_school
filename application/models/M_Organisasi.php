<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Organisasi extends CI_Model {

    public function get_by_level($level = 'Sekolah', $kelas_id = null)
    {
        $this->db->select('struktur_organisasi.*, users.full_name, users.photo, users.role, kelas.nama_kelas');
        $this->db->from('struktur_organisasi');
        $this->db->join('users', 'users.id = struktur_organisasi.user_id');
        $this->db->join('kelas', 'kelas.id = struktur_organisasi.kelas_id', 'left');
        $this->db->where('struktur_organisasi.level', $level);
        if ($kelas_id) {
            $this->db->where('struktur_organisasi.kelas_id', $kelas_id);
        }
        $this->db->order_by('struktur_organisasi.urutan', 'ASC');
        return $this->db->get()->result_array();
    }

    public function save($data)
    {
        return $this->db->insert('struktur_organisasi', $data);
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('struktur_organisasi', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('struktur_organisasi', array('id' => $id));
    }
}
