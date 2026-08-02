<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Murid extends CI_Model {

    public function get_all($kelas_id = null, $status_murid = 'Aktif', $jenjang = null)
    {
        $this->db->select('murid.*, users.full_name, users.email, users.gender, users.phone, users.photo, kelas.nama_kelas, kelas.tingkat, kelas.jurusan, tahun_ajaran.nama as nama_tahun_ajaran');
        $this->db->from('murid');
        $this->db->join('users', 'users.id = murid.user_id');
        $this->db->join('kelas', 'kelas.id = murid.kelas_id', 'left');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id = murid.tahun_ajaran_id', 'left');
        if ($kelas_id) {
            $this->db->where('murid.kelas_id', $kelas_id);
        }
        if ($status_murid) {
            $this->db->where('murid.status_murid', $status_murid);
        }
        if ($jenjang === null) {
            $CI =& get_instance();
            $jenjang = $CI->session->userdata('active_jenjang');
        }
        if ($jenjang && $jenjang !== 'ALL') {
            $this->db->where('kelas.jenjang', $jenjang);
        }
        $this->db->order_by('users.full_name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->select('murid.*, users.full_name, users.email, users.username, users.gender, users.phone, users.address, users.photo, kelas.nama_kelas, kelas.tingkat, kelas.jurusan, tahun_ajaran.nama as nama_tahun_ajaran');
        $this->db->from('murid');
        $this->db->join('users', 'users.id = murid.user_id');
        $this->db->join('kelas', 'kelas.id = murid.kelas_id', 'left');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id = murid.tahun_ajaran_id', 'left');
        $this->db->where('murid.id', $id);
        return $this->db->get()->row_array();
    }

    public function get_by_user_id($user_id)
    {
        return $this->get_by_id($this->db->get_where('murid', array('user_id' => $user_id))->row('id'));
    }

    public function insert($user_data, $murid_data, $ortu_data = null)
    {
        $this->db->trans_start();

        // 1. Check if user with this email or username already exists to prevent MySQL 1062 error
        $user_id = null;
        if (!empty($user_data['email'])) {
            $existing_user = $this->db->get_where('users', array('email' => $user_data['email']))->row_array();
            if ($existing_user) {
                $user_id = $existing_user['id'];
            }
        }
        if (!$user_id && !empty($user_data['username'])) {
            $existing_user = $this->db->get_where('users', array('username' => $user_data['username']))->row_array();
            if ($existing_user) {
                $user_id = $existing_user['id'];
            }
        }

        if ($user_id) {
            $update_u = array(
                'full_name' => $user_data['full_name'],
                'phone'     => $user_data['phone'],
                'address'   => $user_data['address'],
                'status'    => 'active',
                'role'      => 'murid'
            );
            if (isset($user_data['jenjang'])) {
                $update_u['jenjang'] = $user_data['jenjang'];
            }
            $this->db->where('id', $user_id);
            $this->db->update('users', $update_u);
        } else {
            $user_data['role'] = 'murid';
            $user_data['password'] = password_hash($user_data['password'], PASSWORD_BCRYPT);
            $this->db->insert('users', $user_data);
            $user_id = $this->db->insert_id();
        }

        // 2. Check if murid record for this user_id already exists
        $existing_murid = $this->db->get_where('murid', array('user_id' => $user_id))->row_array();
        if ($existing_murid) {
            $murid_id = $existing_murid['id'];
            $this->db->where('id', $murid_id);
            $this->db->update('murid', $murid_data);
        } else {
            $murid_data['user_id'] = $user_id;
            $this->db->insert('murid', $murid_data);
            $murid_id = $this->db->insert_id();
        }

        if ($ortu_data) {
            $existing_ortu = $this->db->get_where('orang_tua', array('murid_id' => $murid_id))->row_array();
            if ($existing_ortu) {
                $this->db->where('id', $existing_ortu['id']);
                $this->db->update('orang_tua', $ortu_data);
            } else {
                $ortu_data['murid_id'] = $murid_id;
                $this->db->insert('orang_tua', $ortu_data);
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function update($murid_id, $user_id, $user_data, $murid_data, $ortu_data = null)
    {
        $this->db->trans_start();

        if (isset($user_data['password']) && !empty($user_data['password'])) {
            $user_data['password'] = password_hash($user_data['password'], PASSWORD_BCRYPT);
        } else {
            unset($user_data['password']);
        }

        $this->db->where('id', $user_id);
        $this->db->update('users', $user_data);

        $this->db->where('id', $murid_id);
        $this->db->update('murid', $murid_data);

        if ($ortu_data) {
            $check = $this->db->get_where('orang_tua', array('murid_id' => $murid_id))->row_array();
            if ($check) {
                $this->db->where('murid_id', $murid_id);
                $this->db->update('orang_tua', $ortu_data);
            } else {
                $ortu_data['murid_id'] = $murid_id;
                $this->db->insert('orang_tua', $ortu_data);
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_ortu($murid_id)
    {
        return $this->db->get_where('orang_tua', array('murid_id' => $murid_id))->row_array();
    }

    // Dokumen Murid (Pemberkasan)
    public function get_dokumen($murid_id)
    {
        return $this->db->get_where('dokumen_murid', array('murid_id' => $murid_id))->result_array();
    }

    public function save_dokumen($data)
    {
        return $this->db->insert('dokumen_murid', $data);
    }

    public function verify_dokumen($id, $status, $keterangan, $verified_by)
    {
        $this->db->where('id', $id);
        return $this->db->update('dokumen_murid', array(
            'status' => $status,
            'keterangan' => $keterangan,
            'verified_by' => $verified_by,
            'verified_at' => date('Y-m-d H:i:s')
        ));
    }

    public function delete_dokumen($id)
    {
        return $this->db->delete('dokumen_murid', array('id' => $id));
    }
}
