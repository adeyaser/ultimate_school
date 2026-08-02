<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Pembayaran extends CI_Model {

    public function get_all_tagihan($murid_id = null, $status = null)
    {
        $this->db->select('pembayaran.*, murid.nisn, murid.nis, users.full_name as nama_murid, kelas.nama_kelas');
        $this->db->from('pembayaran');
        $this->db->join('murid', 'murid.id = pembayaran.murid_id');
        $this->db->join('users', 'users.id = murid.user_id');
        $this->db->join('kelas', 'kelas.id = murid.kelas_id', 'left');
        if ($murid_id) {
            $this->db->where('pembayaran.murid_id', $murid_id);
        }
        if ($status) {
            $this->db->where('pembayaran.status', $status);
        }
        $this->db->order_by('pembayaran.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->select('pembayaran.*, murid.nisn, murid.nis, users.full_name as nama_murid, kelas.nama_kelas');
        $this->db->from('pembayaran');
        $this->db->join('murid', 'murid.id = pembayaran.murid_id');
        $this->db->join('users', 'users.id = murid.user_id');
        $this->db->join('kelas', 'kelas.id = murid.kelas_id', 'left');
        $this->db->where('pembayaran.id', $id);
        return $this->db->get()->row_array();
    }

    public function create_tagihan($data)
    {
        $data['terbayar'] = 0;
        $data['sisa'] = $data['nominal'];
        $data['status'] = 'Belum Lunas';
        return $this->db->insert('pembayaran', $data);
    }

    public function bayar_tagihan($pembayaran_id, $jumlah_bayar, $metode, $keterangan, $input_by, $bukti_path = null)
    {
        $this->db->trans_start();

        $tagihan = $this->db->get_where('pembayaran', array('id' => $pembayaran_id))->row_array();
        $terbayar_baru = (float)$tagihan['terbayar'] + (float)$jumlah_bayar;
        $sisa_baru = (float)$tagihan['nominal'] - $terbayar_baru;

        $status = ($sisa_baru <= 0) ? 'Lunas' : 'Belum Lunas';
        if ($sisa_baru < 0) { $sisa_baru = 0; }

        // Update tagihan
        $this->db->where('id', $pembayaran_id);
        $this->db->update('pembayaran', array(
            'terbayar' => $terbayar_baru,
            'sisa' => $sisa_baru,
            'status' => $status
        ));

        // Insert detail
        $this->db->insert('pembayaran_detail', array(
            'pembayaran_id' => $pembayaran_id,
            'jumlah_bayar' => $jumlah_bayar,
            'tanggal_bayar' => date('Y-m-d'),
            'metode' => $metode,
            'bukti_path' => $bukti_path,
            'input_by' => $input_by,
            'keterangan' => $keterangan
        ));

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_detail_pembayaran($pembayaran_id)
    {
        $this->db->select('pembayaran_detail.*, users.full_name as nama_kasir');
        $this->db->from('pembayaran_detail');
        $this->db->join('users', 'users.id = pembayaran_detail.input_by');
        $this->db->where('pembayaran_detail.pembayaran_id', $pembayaran_id);
        $this->db->order_by('pembayaran_detail.id', 'DESC');
        return $this->db->get()->result_array();
    }
}
