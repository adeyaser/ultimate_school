<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_TahunAjaran extends CI_Model {

    public function get_all()
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get('tahun_ajaran')->result_array();
    }

    public function get_active()
    {
        return $this->db->get_where('tahun_ajaran', array('is_active' => 1))->row_array();
    }

    public function insert($data)
    {
        if (isset($data['is_active']) && $data['is_active'] == 1) {
            $this->db->update('tahun_ajaran', array('is_active' => 0));
        }
        return $this->db->insert('tahun_ajaran', $data);
    }

    public function update($id, $data)
    {
        if (isset($data['is_active']) && $data['is_active'] == 1) {
            $this->db->update('tahun_ajaran', array('is_active' => 0));
        }
        $this->db->where('id', $id);
        return $this->db->update('tahun_ajaran', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('tahun_ajaran', array('id' => $id));
    }
}
