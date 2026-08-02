<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Users extends CI_Model {

    public function get_all($role = null, $jenjang = null)
    {
        if ($role && $role !== 'ALL') {
            $this->db->where('role', $role);
        }
        if ($jenjang && $jenjang !== 'ALL') {
            $this->db->group_start();
            $this->db->where('jenjang', $jenjang);
            $this->db->or_where('jenjang', 'ALL');
            $this->db->group_end();
        }
        $this->db->order_by('id', 'DESC');
        return $this->db->get('users')->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('users', array('id' => $id))->row_array();
    }

    public function insert($data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('users', array('id' => $id));
    }
}
