<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Absensi extends CI_Model {

    // Presensi Murid
    public function get_absensi_murid($kelas_id, $tanggal)
    {
        $this->db->select('murid.id as murid_id, murid.nisn, murid.nis, users.full_name, absensi_murid.id as absensi_id, absensi_murid.status, absensi_murid.jam_datang, absensi_murid.keterangan');
        $this->db->from('murid');
        $this->db->join('users', 'users.id = murid.user_id');
        $this->db->join('absensi_murid', 'absensi_murid.murid_id = murid.id AND absensi_murid.tanggal = "'.$tanggal.'"', 'left');
        $this->db->where('murid.kelas_id', $kelas_id);
        $this->db->where('murid.status_murid', 'Aktif');
        $this->db->order_by('users.full_name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function save_absensi_murid($data)
    {
        return $this->db->replace('absensi_murid', $data);
    }

    public function get_rekap_absensi_murid($murid_id, $bulan = null)
    {
        $this->db->select('status, COUNT(*) as total');
        $this->db->from('absensi_murid');
        $this->db->where('murid_id', $murid_id);
        if ($bulan) {
            $this->db->where('MONTH(tanggal)', $bulan);
        }
        $this->db->group_by('status');
        return $this->db->get()->result_array();
    }

    // Presensi Guru & Staf
    public function get_absensi_guru($tanggal)
    {
        $this->db->select('guru.id as guru_id, guru.nip, guru.status_kepegawaian, users.full_name, users.phone, absensi_guru.id as absensi_id, absensi_guru.status, absensi_guru.jam_datang, absensi_guru.jam_pulang, absensi_guru.keterangan');
        $this->db->from('guru');
        $this->db->join('users', 'users.id = guru.user_id');
        $this->db->join('absensi_guru', 'absensi_guru.guru_id = guru.id AND absensi_guru.tanggal = "'.$tanggal.'"', 'left');
        $this->db->order_by('users.full_name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function save_absensi_guru($data)
    {
        return $this->db->replace('absensi_guru', $data);
    }

    public function get_absensi_self($murid_id)
    {
        $this->db->select('absensi_murid.*, kelas.nama_kelas');
        $this->db->from('absensi_murid');
        $this->db->join('kelas', 'kelas.id = absensi_murid.kelas_id', 'left');
        $this->db->where('absensi_murid.murid_id', $murid_id);
        $this->db->order_by('absensi_murid.tanggal', 'DESC');
        return $this->db->get()->result_array();
    }
}
