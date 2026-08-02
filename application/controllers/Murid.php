<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Murid extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_role(array('super_admin', 'admin', 'kepala_sekolah', 'guru', 'wali_kelas'));
        $this->load->model(array('M_Murid', 'M_Kelas', 'M_TahunAjaran'));
    }

    public function index()
    {
        $data['title'] = 'Administrasi Data Murid';
        $kelas_id = $this->input->get('kelas_id', true);
        $data['kelas_list'] = $this->M_Kelas->get_all();
        $data['murid_list'] = $this->M_Murid->get_all($kelas_id);
        $data['selected_kelas'] = $kelas_id;

        $this->render_page('murid/index', $data);
    }

    public function detail($id)
    {
        $data['title']   = 'Detail Data Murid';
        $data['murid']   = $this->M_Murid->get_by_id($id);
        $data['ortu']    = $this->M_Murid->get_ortu($id);
        $data['dokumen'] = $this->M_Murid->get_dokumen($id);

        $this->render_page('murid/detail', $data);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Data Siswa Baru';
        $data['kelas_list'] = $this->M_Kelas->get_all();
        $data['ta_active']  = $this->M_TahunAjaran->get_active();

        $this->render_page('murid/form', $data);
    }

    public function simpan()
    {
        $user_data = array(
            'username'  => $this->input->post('username', true),
            'email'     => $this->input->post('email', true),
            'password'  => $this->input->post('password', true) ? $this->input->post('password', true) : '12345678',
            'full_name' => $this->input->post('full_name', true),
            'gender'    => $this->input->post('gender', true),
            'phone'     => $this->input->post('phone', true),
            'address'   => $this->input->post('address', true),
            'status'    => 'active'
        );

        $murid_data = array(
            'nisn'            => $this->input->post('nisn', true),
            'nis'             => $this->input->post('nis', true),
            'kelas_id'        => $this->input->post('kelas_id', true),
            'tahun_ajaran_id' => $this->input->post('tahun_ajaran_id', true),
            'tempat_lahir'    => $this->input->post('tempat_lahir', true),
            'tanggal_lahir'   => $this->input->post('tanggal_lahir', true),
            'agama'           => $this->input->post('agama', true),
            'alamat_tinggal'  => $this->input->post('alamat_tinggal', true),
            'status_murid'    => 'Aktif',
            'tanggal_masuk'   => $this->input->post('tanggal_masuk', true) ? $this->input->post('tanggal_masuk', true) : date('Y-m-d')
        );

        $ortu_data = array(
            'ayah_nama'      => $this->input->post('ayah_nama', true),
            'ayah_pekerjaan' => $this->input->post('ayah_pekerjaan', true),
            'ayah_telepon'   => $this->input->post('ayah_telepon', true),
            'ibu_nama'       => $this->input->post('ibu_nama', true),
            'ibu_pekerjaan'  => $this->input->post('ibu_pekerjaan', true),
            'ibu_telepon'    => $this->input->post('ibu_telepon', true)
        );

        $res = $this->M_Murid->insert($user_data, $murid_data, $ortu_data);
        if ($res) {
            $this->session->set_flashdata('success', 'Data siswa berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan data siswa.');
        }
        redirect('murid');
    }

    public function verifikasi_dokumen($dokumen_id)
    {
        $status = $this->input->post('status', true);
        $keterangan = $this->input->post('keterangan', true);
        $murid_id = $this->input->post('murid_id', true);

        $this->M_Murid->verify_dokumen($dokumen_id, $status, $keterangan, $this->user_data['id']);
        $this->session->set_flashdata('success', 'Status verifikasi dokumen diperbarui.');
        redirect('murid/detail/' . $murid_id);
    }

    public function upload_dokumen()
    {
        $murid_id = $this->input->post('murid_id', true);
        $jenis_dokumen = $this->input->post('jenis_dokumen', true);
        $nama_file = $this->input->post('nama_file', true);

        $file_path = '';
        if (!empty($_FILES['file_dokumen']['name'])) {
            $config['upload_path']   = './uploads/dokumen/';
            $config['allowed_types'] = 'pdf|jpg|png|jpeg|docx';
            $config['max_size']      = 10240; // 10MB
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('file_dokumen')) {
                $upload_data = $this->upload->data();
                $file_path = 'uploads/dokumen/' . $upload_data['file_name'];
            }
        }

        if ($file_path) {
            $doc_data = array(
                'murid_id'      => $murid_id,
                'jenis_dokumen' => $jenis_dokumen,
                'nama_file'     => $nama_file ? $nama_file : $jenis_dokumen,
                'file_path'     => $file_path,
                'status'        => 'Verified',
                'verified_by'   => $this->user_data['id'],
                'verified_at'   => date('Y-m-d H:i:s')
            );
            $this->M_Murid->save_dokumen($doc_data);

            // Automatically update student profile photo if Pas Foto is uploaded
            if ($jenis_dokumen === 'Foto') {
                $user_id = $this->db->get_where('murid', array('id' => $murid_id))->row('user_id');
                if ($user_id) {
                    $this->db->where('id', $user_id)->update('users', array('photo' => $file_path));
                }
            }

            $this->session->set_flashdata('success', 'Dokumen & Foto Profil siswa berhasil diunggah.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengunggah berkas dokumen.');
        }

        redirect('murid/detail/' . $murid_id);
    }

    public function hapus_dokumen($id, $murid_id)
    {
        $this->M_Murid->delete_dokumen($id);
        $this->session->set_flashdata('success', 'Dokumen siswa berhasil dihapus.');
        redirect('murid/detail/' . $murid_id);
    }
}
