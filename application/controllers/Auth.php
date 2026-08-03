<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_Auth');
    }

    public function index()
    {
        if ($this->session->userdata('is_login')) {
            redirect('dashboard');
        }
        $this->load->view('auth/login');
    }

    public function login()
    {
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', true);
        $turnstile_token = $this->input->post('cf-turnstile-response');

        if (empty($username) || empty($password)) {
            $this->session->set_flashdata('error', 'Username dan password wajib diisi.');
            redirect('auth');
            return;
        }

        // Validate Cloudflare Turnstile CAPTCHA
        if (!verify_turnstile($turnstile_token)) {
            $this->session->set_flashdata('error', 'Verifikasi Keamanan CAPTCHA (Cloudflare Turnstile) tidak valid. Silakan centang ulang.');
            redirect('auth');
            return;
        }

        $user = $this->M_Auth->verify_login($username, $password);

        if ($user) {
            $session_data = array(
                'is_login'  => true,
                'user_id'   => $user['id'],
                'username'  => $user['username'],
                'full_name' => $user['full_name'],
                'email'     => $user['email'],
                'role'      => $user['role'],
                'photo'     => $user['photo']
            );
            $this->session->set_userdata($session_data);
            $this->session->set_flashdata('success', 'Selamat datang kembali, ' . $user['full_name']);
            redirect('dashboard');
        } else {
            $this->session->set_flashdata('error', 'Username atau password salah / akun tidak aktif.');
            redirect('auth');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }
}
