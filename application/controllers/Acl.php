<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Acl extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin'));
        $this->load->model('M_Acl');
    }

    public function index()
    {
        $data['title']       = 'Manajemen ACL & Tree Role System';
        $data['roles_tree']  = $this->M_Acl->get_roles_tree();
        $data['roles_list']  = $this->M_Acl->get_all_roles();
        $data['menus_tree']  = $this->M_Acl->get_menus_tree();
        $data['menus_list']  = $this->M_Acl->get_all_menus();

        $selected_role_id    = $this->input->get('role_id', true) ? $this->input->get('role_id', true) : 1;
        $data['selected_role_id'] = $selected_role_id;
        $data['role_matrix'] = $this->M_Acl->get_role_permissions($selected_role_id);

        $this->render_page('acl/index', $data);
    }

    public function simpan_matrix()
    {
        $role_id     = $this->input->post('role_id', true);
        $permissions = $this->input->post('perms', true);

        if ($role_id && is_array($permissions)) {
            $this->M_Acl->save_role_matrix($role_id, $permissions);
            $this->session->set_flashdata('success', 'Matriks Hak Akses (ACL Matrix) berhasil disimpan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui matriks hak akses.');
        }

        redirect('acl?role_id=' . $role_id . '&tab=matrix');
    }
}
