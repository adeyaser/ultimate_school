<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Ppdb extends CI_Model {

    public function get_all($tahun_ajaran_id = null, $jenjang = null)
    {
        $this->db->select('pendaftaran_ppdb.*, tahun_ajaran.nama as nama_tahun_ajaran');
        $this->db->from('pendaftaran_ppdb');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id = pendaftaran_ppdb.tahun_ajaran_id');
        if ($tahun_ajaran_id) {
            $this->db->where('pendaftaran_ppdb.tahun_ajaran_id', $tahun_ajaran_id);
        }
        if (empty($jenjang)) {
            $CI =& get_instance();
            $jenjang = $CI->session->userdata('active_jenjang');
        }
        if ($jenjang && $jenjang !== 'ALL') {
            $this->db->where('pendaftaran_ppdb.jenjang', $jenjang);
        }
        $this->db->order_by('pendaftaran_ppdb.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('pendaftaran_ppdb', array('id' => $id))->row_array();
    }

    public function get_by_no_pendaftaran($no)
    {
        return $this->db->get_where('pendaftaran_ppdb', array('no_pendaftaran' => $no))->row_array();
    }

    public function insert($data)
    {
        // Generate unique reg number if not set
        if (!isset($data['no_pendaftaran']) || empty($data['no_pendaftaran'])) {
            $data['no_pendaftaran'] = 'PPDB-' . date('Ymd') . '-' . rand(1000, 9999);
        }
        $this->db->insert('pendaftaran_ppdb', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('pendaftaran_ppdb', $data);
    }
}
