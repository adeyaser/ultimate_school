<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Eskul extends CI_Model {

    public function get_all()
    {
        $this->db->select('eskul.*, guru.nip, users.full_name as nama_pembina, (SELECT COUNT(*) FROM eskul_peserta WHERE eskul_peserta.eskul_id = eskul.id AND eskul_peserta.status = "Aktif") as total_peserta');
        $this->db->from('eskul');
        $this->db->join('guru', 'guru.id = eskul.pembina_id');
        $this->db->join('users', 'users.id = guru.user_id');
        return $this->db->get()->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->select('eskul.*, users.full_name as nama_pembina');
        $this->db->from('eskul');
        $this->db->join('guru', 'guru.id = eskul.pembina_id');
        $this->db->join('users', 'users.id = guru.user_id');
        $this->db->where('eskul.id', $id);
        return $this->db->get()->row_array();
    }

    public function insert($data)
    {
        return $this->db->insert('eskul', $data);
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('eskul', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('eskul', array('id' => $id));
    }

    public function get_peserta($eskul_id)
    {
        $this->db->select('eskul_peserta.*, murid.nisn, murid.nis, users.full_name as nama_murid, kelas.nama_kelas');
        $this->db->from('eskul_peserta');
        $this->db->join('murid', 'murid.id = eskul_peserta.murid_id');
        $this->db->join('users', 'users.id = murid.user_id');
        $this->db->join('kelas', 'kelas.id = murid.kelas_id');
        $this->db->where('eskul_peserta.eskul_id', $eskul_id);
        return $this->db->get()->result_array();
    }

    public function enroll_peserta($eskul_id, $murid_id)
    {
        return $this->db->replace('eskul_peserta', array(
            'eskul_id' => $eskul_id,
            'murid_id' => $murid_id,
            'status' => 'Aktif',
            'tanggal_bergabung' => date('Y-m-d')
        ));
    }
}
