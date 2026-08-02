<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Tabungan extends CI_Model {

    public function get_tabungan_kelas($kelas_id, $tahun_ajaran_id)
    {
        $this->db->select('murid.id as murid_id, murid.nisn, murid.nis, users.full_name, tabungan.id as tabungan_id, tabungan.saldo_awal, tabungan.saldo_akhir');
        $this->db->from('murid');
        $this->db->join('users', 'users.id = murid.user_id');
        $this->db->join('tabungan', 'tabungan.murid_id = murid.id AND tabungan.kelas_id = '.$kelas_id.' AND tabungan.tahun_ajaran_id = '.$tahun_ajaran_id, 'left');
        $this->db->where('murid.kelas_id', $kelas_id);
        $this->db->where('murid.status_murid', 'Aktif');
        $this->db->order_by('users.full_name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_or_create_tabungan($murid_id, $kelas_id, $tahun_ajaran_id)
    {
        $tabungan = $this->db->get_where('tabungan', array(
            'murid_id' => $murid_id,
            'kelas_id' => $kelas_id,
            'tahun_ajaran_id' => $tahun_ajaran_id
        ))->row_array();

        if (!$tabungan) {
            $this->db->insert('tabungan', array(
                'murid_id' => $murid_id,
                'kelas_id' => $kelas_id,
                'tahun_ajaran_id' => $tahun_ajaran_id,
                'saldo_awal' => 0,
                'saldo_akhir' => 0
            ));
            $tabungan_id = $this->db->insert_id();
            return $this->db->get_where('tabungan', array('id' => $tabungan_id))->row_array();
        }

        return $tabungan;
    }

    public function tambah_transaksi($tabungan_id, $jenis, $nominal, $keterangan, $input_by)
    {
        $this->db->trans_start();

        $tabungan = $this->db->get_where('tabungan', array('id' => $tabungan_id))->row_array();
        $saldo_sebelum = (float)$tabungan['saldo_akhir'];
        
        if ($jenis === 'Setoran') {
            $saldo_sesudah = $saldo_sebelum + (float)$nominal;
        } else {
            $saldo_sesudah = $saldo_sebelum - (float)$nominal;
        }

        // Insert transaksi
        $this->db->insert('transaksi_tabungan', array(
            'tabungan_id' => $tabungan_id,
            'jenis' => $jenis,
            'nominal' => $nominal,
            'saldo_sebelum' => $saldo_sebelum,
            'saldo_sesudah' => $saldo_sesudah,
            'tanggal' => date('Y-m-d'),
            'keterangan' => $keterangan,
            'input_by' => $input_by
        ));

        // Update tabungan master
        $this->db->where('id', $tabungan_id);
        $this->db->update('tabungan', array('saldo_akhir' => $saldo_sesudah));

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_riwayat_transaksi($tabungan_id)
    {
        $this->db->select('transaksi_tabungan.*, users.full_name as nama_input');
        $this->db->from('transaksi_tabungan');
        $this->db->join('users', 'users.id = transaksi_tabungan.input_by');
        $this->db->where('transaksi_tabungan.tabungan_id', $tabungan_id);
        $this->db->order_by('transaksi_tabungan.id', 'DESC');
        return $this->db->get()->result_array();
    }
}
