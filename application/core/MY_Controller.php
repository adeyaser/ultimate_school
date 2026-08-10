<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public $user_data = null;
    public $role = null;
    public $school_info = null;
    public $active_ta = null;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper(array('url', 'html', 'file', 'turnstile'));

        // Fetch School Info & Active Academic Year
        $this->school_info = $this->db->get('sekolah')->row_array();
        $this->active_ta = $this->db->get_where('tahun_ajaran', array('is_active' => 1))->row_array();

        // Populate user session if logged in
        if ($this->session->userdata('is_login')) {
            $this->user_data = array(
                'id'        => $this->session->userdata('user_id'),
                'username'  => $this->session->userdata('username'),
                'full_name' => $this->session->userdata('full_name'),
                'email'     => $this->session->userdata('email'),
                'role'      => $this->session->userdata('role'),
                'photo'     => $this->session->userdata('photo')
            );
            $this->role = $this->session->userdata('role');
        }
    }

    protected function check_auth()
    {
        if (!$this->session->userdata('is_login')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('auth');
        }
    }

    protected function check_role($allowed_roles = array())
    {
        $this->check_auth();
        if (!in_array($this->role, $allowed_roles)) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            redirect('dashboard');
        }
    }

    protected function render_page($view, $data = array())
    {
        $this->load->model('M_Acl');
        $user_role = isset($this->user_data['role']) ? $this->user_data['role'] : 'super_admin';

        $sek = $this->db->get_where('sekolah', array('id' => 1))->row_array();
        if ($sek) {
            $this->school_info = $sek;
        }

        $db_jenjang = isset($this->school_info['jenjang']) ? $this->school_info['jenjang'] : 'SD';
        $active_jenjang = $this->session->userdata('active_jenjang');

        // If session active_jenjang is empty OR database setting was updated, sync automatically
        if (empty($active_jenjang) || $this->session->userdata('last_db_jenjang') !== $db_jenjang) {
            $active_jenjang = $db_jenjang;
            $this->session->set_userdata('active_jenjang', $active_jenjang);
            $this->session->set_userdata('last_db_jenjang', $db_jenjang);
        }

        // Dynamically resolve NPSN & Kepala Sekolah based on active_jenjang mode
        if ($active_jenjang === 'SD' && !empty($this->school_info['npsn_sd'])) {
            $this->school_info['npsn'] = $this->school_info['npsn_sd'];
        } elseif ($active_jenjang === 'SMP' && !empty($this->school_info['npsn_smp'])) {
            $this->school_info['npsn'] = $this->school_info['npsn_smp'];
        } elseif ($active_jenjang === 'SMA' && !empty($this->school_info['npsn_sma'])) {
            $this->school_info['npsn'] = $this->school_info['npsn_sma'];
        }

        if ($active_jenjang === 'SD' && !empty($this->school_info['kepala_sd'])) {
            $this->school_info['kepala_sekolah'] = $this->school_info['kepala_sd'];
        } elseif ($active_jenjang === 'SMP' && !empty($this->school_info['kepala_smp'])) {
            $this->school_info['kepala_sekolah'] = $this->school_info['kepala_smp'];
        } elseif ($active_jenjang === 'SMA' && !empty($this->school_info['kepala_sma'])) {
            $this->school_info['kepala_sekolah'] = $this->school_info['kepala_sma'];
        }

        $data['user_data']      = $this->user_data;
        $data['school_info']    = $this->school_info;
        $data['active_ta']      = $this->active_ta;
        $data['active_jenjang'] = $active_jenjang;
        $data['allowed_menus']  = $this->M_Acl->get_allowed_menu_codes($user_role);
        $data['content_view']   = $view;

        $this->load->view('templates/layout', $data);
    }

    protected function ensure_db_connection()
    {
        if (!isset($this->db->conn_id) || !is_object($this->db->conn_id) || @$this->db->conn_id->ping() === false) {
            @$this->db->close();
            $this->db->initialize();
        }
    }
}
