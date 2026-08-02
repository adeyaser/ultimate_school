<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Guru extends CI_Model {

    public function get_all($jenjang = null)
    {
        $this->db->select('guru.*, users.full_name, users.email, users.phone, users.gender, users.photo, users.status, users.jenjang');
        $this->db->from('guru');
        $this->db->join('users', 'users.id = guru.user_id');
        if ($jenjang === null) {
            $CI =& get_instance();
            $jenjang = $CI->session->userdata('active_jenjang');
        }
        if ($jenjang && $jenjang !== 'ALL') {
            $this->db->group_start();
            $this->db->where('users.jenjang', $jenjang);
            $this->db->or_where('users.jenjang', 'ALL');
            $this->db->group_end();
        }
        $this->db->order_by('guru.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->select('guru.*, users.full_name, users.email, users.username, users.phone, users.gender, users.birth_date, users.address, users.photo');
        $this->db->from('guru');
        $this->db->join('users', 'users.id = guru.user_id');
        $this->db->where('guru.id', $id);
        return $this->db->get()->row_array();
    }

    public function get_by_user_id($user_id)
    {
        return $this->db->get_where('guru', array('user_id' => $user_id))->row_array();
    }

    public function insert($user_data, $guru_data)
    {
        $this->db->trans_start();
        
        $user_data['role'] = 'guru';
        $user_data['password'] = password_hash($user_data['password'], PASSWORD_BCRYPT);
        $this->db->insert('users', $user_data);
        $user_id = $this->db->insert_id();

        $guru_data['user_id'] = $user_id;
        $this->db->insert('guru', $guru_data);
        $guru_id = $this->db->insert_id();

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function update($guru_id, $user_id, $user_data, $guru_data)
    {
        $this->db->trans_start();

        if (isset($user_data['password']) && !empty($user_data['password'])) {
            $user_data['password'] = password_hash($user_data['password'], PASSWORD_BCRYPT);
        } else {
            unset($user_data['password']);
        }

        $this->db->where('id', $user_id);
        $this->db->update('users', $user_data);

        $this->db->where('id', $guru_id);
        $this->db->update('guru', $guru_data);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function delete($guru_id)
    {
        $guru = $this->get_by_id($guru_id);
        if ($guru) {
            $this->db->delete('users', array('id' => $guru['user_id']));
            return $this->db->delete('guru', array('id' => $guru_id));
        }
        return false;
    }

    // Absensi Guru
    public function get_absensi_guru($tanggal = null)
    {
        $this->db->select('absensi_guru.*, guru.nip, users.full_name');
        $this->db->from('absensi_guru');
        $this->db->join('guru', 'guru.id = absensi_guru.guru_id');
        $this->db->join('users', 'users.id = guru.user_id');
        if ($tanggal) {
            $this->db->where('absensi_guru.tanggal', $tanggal);
        }
        return $this->db->get()->result_array();
    }

    public function save_absensi_guru($data)
    {
        return $this->db->replace('absensi_guru', $data);
    }
}
