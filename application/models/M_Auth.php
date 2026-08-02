<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Auth extends CI_Model {

    public function verify_login($username, $password)
    {
        $this->db->group_start();
        $this->db->where('username', $username);
        $this->db->or_where('email', $username);
        $this->db->group_end();
        
        $user = $this->db->get('users')->row_array();

        if ($user && $user['status'] === 'active') {
            // Verify password using password_verify
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    public function get_user_profile($user_id, $role)
    {
        if ($role === 'guru' || $role === 'wali_kelas') {
            $this->db->select('users.*, guru.nip, guru.nuptk, guru.pendidikan_terakhir, guru.status_kepegawaian');
            $this->db->from('users');
            $this->db->join('guru', 'guru.user_id = users.id', 'left');
            $this->db->where('users.id', $user_id);
            return $this->db->get()->row_array();
        } elseif ($role === 'murid') {
            $this->db->select('users.*, murid.id as murid_table_id, murid.nisn, murid.nis, murid.kelas_id, kelas.nama_kelas');
            $this->db->from('users');
            $this->db->join('murid', 'murid.user_id = users.id', 'left');
            $this->db->join('kelas', 'kelas.id = murid.kelas_id', 'left');
            $this->db->where('users.id', $user_id);
            return $this->db->get()->row_array();
        }
        return $this->db->get_where('users', array('id' => $user_id))->row_array();
    }
}
