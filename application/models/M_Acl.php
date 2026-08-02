<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Acl extends CI_Model {

    // Fetch Roles as Hierarchical Tree (Akar Pohon)
    public function get_roles_tree($parent_id = null)
    {
        $this->db->select('r.*, p.role_name as parent_role_name');
        $this->db->from('acl_role r');
        $this->db->join('acl_role p', 'p.id = r.parent_id', 'left');
        if ($parent_id === null) {
            $this->db->where('r.parent_id IS NULL');
        } else {
            $this->db->where('r.parent_id', $parent_id);
        }
        $this->db->order_by('r.id', 'ASC');
        $roles = $this->db->get()->result_array();

        foreach ($roles as &$role) {
            $role['children'] = $this->get_roles_tree($role['id']);
        }
        return $roles;
    }

    public function get_all_roles()
    {
        $this->db->select('r.*, p.role_name as parent_role_name');
        $this->db->from('acl_role r');
        $this->db->join('acl_role p', 'p.id = r.parent_id', 'left');
        $this->db->order_by('r.id', 'ASC');
        return $this->db->get()->result_array();
    }

    // Fetch Menus as Hierarchical Tree (Akar Pohon)
    public function get_menus_tree($parent_id = 0)
    {
        $this->db->where('parent_id', $parent_id);
        $this->db->order_by('sort_order', 'ASC');
        $menus = $this->db->get('acl_menu')->result_array();

        foreach ($menus as &$menu) {
            $menu['children'] = $this->get_menus_tree($menu['id']);
        }
        return $menus;
    }

    public function get_all_menus()
    {
        $this->db->order_by('parent_id ASC, sort_order ASC');
        return $this->db->get('acl_menu')->result_array();
    }

    // Get Role Permissions Matrix
    public function get_role_permissions($role_id)
    {
        $rows = $this->db->get_where('acl_role_menu', array('role_id' => $role_id))->result_array();
        $matrix = array();
        foreach ($rows as $r) {
            $matrix[$r['menu_id']] = $r;
        }
        return $matrix;
    }

    // Save Role Permissions Matrix
    public function save_role_matrix($role_id, $menu_permissions)
    {
        $this->db->trans_start();

        foreach ($menu_permissions as $menu_id => $perms) {
            $check = $this->db->get_where('acl_role_menu', array('role_id' => $role_id, 'menu_id' => $menu_id))->row_array();
            $data = array(
                'can_view'   => isset($perms['can_view']) ? 1 : 0,
                'can_create' => isset($perms['can_create']) ? 1 : 0,
                'can_edit'   => isset($perms['can_edit']) ? 1 : 0,
                'can_delete' => isset($perms['can_delete']) ? 1 : 0
            );

            if ($check) {
                $this->db->where('id', $check['id']);
                $this->db->update('acl_role_menu', $data);
            } else {
                $data['role_id'] = $role_id;
                $data['menu_id'] = $menu_id;
                $this->db->insert('acl_role_menu', $data);
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_allowed_menu_codes($role_code)
    {
        if ($role_code === 'super_admin') {
            $res = $this->db->select('menu_code')->get('acl_menu')->result_array();
            return array_column($res, 'menu_code');
        }

        $role = $this->db->get_where('acl_role', array('role_code' => $role_code))->row_array();
        if (!$role) {
            return array('dashboard', 'home_compro');
        }

        $this->db->select('acl_menu.menu_code');
        $this->db->from('acl_role_menu');
        $this->db->join('acl_menu', 'acl_menu.id = acl_role_menu.menu_id');
        $this->db->where('acl_role_menu.role_id', $role['id']);
        $this->db->where('acl_role_menu.can_view', 1);
        $res = $this->db->get()->result_array();
        return array_column($res, 'menu_code');
    }
}
