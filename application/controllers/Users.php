<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah'));
        $this->load->model(array('M_Users', 'M_Sekolah'));
    }

    public function index()
    {
        $data['title'] = 'Manajemen Pengguna (User Management)';
        
        $role_filter    = $this->input->get('role', true);
        $jenjang_filter = $this->input->get('jenjang', true);

        if ($jenjang_filter === null) {
            $jenjang_filter = $this->session->userdata('active_jenjang');
        }

        $data['selected_role']    = $role_filter;
        $data['selected_jenjang'] = $jenjang_filter;
        $data['users_list']       = $this->M_Users->get_all($role_filter, $jenjang_filter);

        $this->render_page('users/index', $data);
    }

    public function simpan()
    {
        $this->check_role(array('super_admin', 'admin'));
        $id = $this->input->post('id', true);

        $data = array(
            'username'  => $this->input->post('username', true),
            'email'     => $this->input->post('email', true),
            'full_name' => $this->input->post('full_name', true),
            'phone'     => $this->input->post('phone', true),
            'gender'    => $this->input->post('gender', true) ? $this->input->post('gender', true) : 'L',
            'role'      => $this->input->post('role', true),
            'jenjang'   => $this->input->post('jenjang', true) ? $this->input->post('jenjang', true) : 'ALL',
            'status'    => $this->input->post('status', true) ? $this->input->post('status', true) : 'active'
        );

        $password = $this->input->post('password', true);
        if (!empty($password)) {
            $data['password'] = $password;
        }

        // Username duplicate check
        $this->db->where('username', $data['username']);
        if ($id) {
            $this->db->where('id !=', $id);
        }
        $check_user = $this->db->get('users')->row_array();

        if ($check_user) {
            $this->session->set_flashdata('error', 'Username "' . $data['username'] . '" sudah digunakan. Gunakan username lain.');
            redirect('users');
        }

        if ($id) {
            $this->M_Users->update($id, $data);
            $this->session->set_flashdata('success', 'Data akun pengguna berhasil diperbarui!');
        } else {
            if (empty($password)) {
                $data['password'] = '123456'; // Default fallback password
            }
            $this->M_Users->insert($data);
            $this->session->set_flashdata('success', 'Akun pengguna baru berhasil dibuat!');
        }

        redirect('users');
    }

    public function hapus($id)
    {
        $this->check_role(array('super_admin', 'admin'));

        // Prevent self-delete
        if ($id == $this->user_data['id']) {
            $this->session->set_flashdata('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
            redirect('users');
        }

        $this->M_Users->delete($id);
        $this->session->set_flashdata('success', 'Akun pengguna berhasil dihapus.');
        redirect('users');
    }
}
