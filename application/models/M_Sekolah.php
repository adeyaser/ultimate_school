<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Sekolah extends CI_Model {

    public function get_school_profile()
    {
        return $this->db->get_where('sekolah', array('id' => 1))->row_array();
    }

    public function update_school_profile($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('sekolah', $data);
    }

    public function get_faqs()
    {
        return $this->db->order_by('urutan', 'ASC')->get('faq')->result_array();
    }

    public function insert_faq($data)
    {
        return $this->db->insert('faq', $data);
    }

    public function delete_faq($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('faq');
    }

    public function get_fasilitas()
    {
        return $this->db->order_by('urutan', 'ASC')->get('fasilitas')->result_array();
    }

    public function insert_fasilitas($data)
    {
        return $this->db->insert('fasilitas', $data);
    }

    public function delete_fasilitas($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('fasilitas');
    }
}
