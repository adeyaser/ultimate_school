<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sekolah extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin'));
        $this->load->model('M_Sekolah');
    }

    public function index()
    {
        $data['title']     = 'Kelola Company Profile & Jenjang Sekolah';
        $data['sekolah']   = $this->M_Sekolah->get_school_profile();
        $data['faqs']      = $this->M_Sekolah->get_faqs();
        $data['fasilitas'] = $this->M_Sekolah->get_fasilitas();

        $this->render_page('sekolah/index', $data);
    }

    public function simpan()
    {
        $id = $this->input->post('id');

        // 1. Process Logo File Upload if provided
        $logo_path = $this->input->post('logo', true);
        if (!empty($_FILES['logo_file']['name'])) {
            $config['upload_path']   = './uploads/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|webp|svg';
            $config['max_size']      = 5120; // 5MB
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('logo_file')) {
                $upload_data = $this->upload->data();
                $logo_path = 'uploads/' . $upload_data['file_name'];
            }
        }

        // 2. Process Hero Media File Upload if provided
        $hero_media_path = $this->input->post('hero_media', true);
        if (!empty($_FILES['hero_media_file']['name'])) {
            $config_hero['upload_path']   = './uploads/';
            $config_hero['allowed_types'] = 'gif|jpg|png|jpeg|webp|mp4|webm';
            $config_hero['max_size']      = 51200; // 50MB
            $config_hero['encrypt_name']  = TRUE;

            $this->load->library('upload', $config_hero);
            $this->upload->initialize($config_hero);

            if ($this->upload->do_upload('hero_media_file')) {
                $upload_hero_data = $this->upload->data();
                $hero_media_path = 'uploads/' . $upload_hero_data['file_name'];
            }
        }

        $save_data = array(
            'nama_sekolah'    => $this->input->post('nama_sekolah', true),
            'jenjang'         => $this->input->post('jenjang', true) ? $this->input->post('jenjang', true) : 'SMP',
            'npsn'            => $this->input->post('npsn', true),
            'npsn_sd'         => $this->input->post('npsn_sd', true),
            'npsn_smp'        => $this->input->post('npsn_smp', true),
            'npsn_sma'        => $this->input->post('npsn_sma', true),
            'kepala_sekolah'  => $this->input->post('kepala_sekolah', true),
            'kepala_sd'       => $this->input->post('kepala_sd', true),
            'kepala_smp'      => $this->input->post('kepala_smp', true),
            'kepala_sma'      => $this->input->post('kepala_sma', true),
            'telepon'         => $this->input->post('telepon', true),
            'email'           => $this->input->post('email', true),
            'website'         => $this->input->post('website', true),
            'alamat'          => $this->input->post('alamat', true),
            'kota'            => $this->input->post('kota', true),
            'provinsi'        => $this->input->post('provinsi', true),
            'kode_pos'        => $this->input->post('kode_pos', true),
            'running_text'    => $this->input->post('running_text', true),
            'hero_title'      => $this->input->post('hero_title', true),
            'hero_subtitle'   => $this->input->post('hero_subtitle', true),
            'hero_type'       => $this->input->post('hero_type', true),
            'hero_media'      => $hero_media_path,
            'logo'            => $logo_path,
            'sambutan_kepsek' => $this->input->post('sambutan_kepsek', true),
            'visi'            => $this->input->post('visi', true),
            'misi'            => $this->input->post('misi', true),
            'facebook_url'    => $this->input->post('facebook_url', true),
            'instagram_url'   => $this->input->post('instagram_url', true),
            'youtube_url'     => $this->input->post('youtube_url', true)
        );

        $this->M_Sekolah->update_school_profile($id ? $id : 1, $save_data);
        $this->session->set_userdata('active_jenjang', $save_data['jenjang']);
        $this->session->set_flashdata('success', 'Konfigurasi Jenjang & Profil Sekolah berhasil diperbarui!');
        redirect('sekolah');
    }

    public function tambah_faq()
    {
        $faq_data = array(
            'pertanyaan' => $this->input->post('pertanyaan', true),
            'jawaban'    => $this->input->post('jawaban', true),
            'urutan'     => $this->input->post('urutan', true) ? $this->input->post('urutan', true) : 1
        );

        $this->M_Sekolah->insert_faq($faq_data);
        $this->session->set_flashdata('success', 'Item FAQ baru berhasil ditambahkan!');
        redirect('sekolah');
    }

    public function hapus_faq($id)
    {
        $this->M_Sekolah->delete_faq($id);
        $this->session->set_flashdata('success', 'Item FAQ berhasil dihapus!');
        redirect('sekolah');
    }

    public function tambah_fasilitas()
    {
        $fas_data = array(
            'nama_fasilitas' => $this->input->post('nama_fasilitas', true),
            'deskripsi'      => $this->input->post('deskripsi', true),
            'foto'           => $this->input->post('foto', true) ? $this->input->post('foto', true) : 'photo1.png',
            'urutan'         => $this->input->post('urutan', true) ? $this->input->post('urutan', true) : 1
        );

        $this->M_Sekolah->insert_fasilitas($fas_data);
        $this->session->set_flashdata('success', 'Fasilitas baru berhasil ditambahkan!');
        redirect('sekolah');
    }

    public function hapus_fasilitas($id)
    {
        $this->M_Sekolah->delete_fasilitas($id);
        $this->session->set_flashdata('success', 'Fasilitas berhasil dihapus!');
        redirect('sekolah');
    }

    public function switch_jenjang($target_jenjang = 'SD')
    {
        $valid = array('SD', 'SMP', 'SMA', 'SMK', 'ALL');
        if (in_array($target_jenjang, $valid)) {
            $this->session->set_userdata('active_jenjang', $target_jenjang);
            if ($target_jenjang !== 'ALL') {
                $this->db->update('sekolah', array('jenjang' => $target_jenjang));
                $this->session->set_userdata('last_db_jenjang', $target_jenjang);
            }
            $this->session->set_flashdata('success', 'Berhasil beralih ke Mode Sekolah: <strong>' . $target_jenjang . '</strong>');
        }

        $referrer = $this->input->server('HTTP_REFERER');
        if ($referrer) {
            redirect($referrer);
        } else {
            redirect('dashboard');
        }
    }
}

