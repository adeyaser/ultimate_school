<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Acara extends CI_Model {

    public function get_all($published_only = false)
    {
        if ($published_only) {
            $this->db->where('is_published', 1);
        }
        $this->db->order_by('tanggal_mulai', 'DESC');
        return $this->db->get('acara_sekolah')->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('acara_sekolah', array('id' => $id))->row_array();
    }

    public function insert($data)
    {
        return $this->db->insert('acara_sekolah', $data);
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('acara_sekolah', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('acara_sekolah', array('id' => $id));
    }
}
