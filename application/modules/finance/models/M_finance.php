<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_finance extends CI_Model
{

    public function __construct()
    {
        parent::__construct(); //inherit dari parent
        $this->db_web_app = $this->load->database('db_koperasi',TRUE);
    }

    function get_saldo_rekening_tabungan_anggota($user_id){
        $this->db_web_app->select('no_rekening no_rekening,saldo');
        $this->db_web_app->where('id_user', $user_id);
        $this->db_web_app->limit('1');
        $query = $this->db_web_app->get('mcb_rekening_tabungan');

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_saldo_rekening_virtual_anggota($user_id){
        $this->db_web_app->select('no_rekening_virtual no_rekening,saldo');
        $this->db_web_app->where('id_user', $user_id);
        $this->db_web_app->limit('1');
        $query = $this->db_web_app->get('mcb_rekening_virtual');

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }
    function get_saldo_rekening_loyalti_anggota($user_id){
        $this->db_web_app->select('no_rekening_loyalti no_rekening,saldo');
        $this->db_web_app->where('id_user', $user_id);
        $this->db_web_app->limit('1');
        $query = $this->db_web_app->get('mcb_rekening_loyalti');

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }


    function transfer($user_id,$user_target,$data_user,$data_target){

        $this->db_web_app->where('id_user', $user_id);
        $this->db_web_app->update('mcb_rekening_virtual', $data_user);

        $this->db_web_app->where('id_user', $user_target);
        $this->db_web_app->update('mcb_rekening_virtual', $data_target);
        return true;
    }


    function get_history_transaksi($user_id){

        // history nya pake history_produk (ganti)
        $query = $this->db->query("SELECT no_transaksi, 'PULSA' jenis, DATE_FORMAT(tanggal_transaksi,'%d-%m-%Y') tanggal, harga_gerai harga,  'detail'  FROM gerai_transaksi where id_user = '91460136320'
 union all
SELECT no_transaksi, 'PRODUK' jenis,
				DATE_FORMAT(tanggal_transaksi,'%d-%m-%Y') tanggal,
				total_harga harga, 'detail'
FROM transaksi trx where id_user = '91460136320'");

        $history = array();
foreach ($query->result_array() as $key=>$value){


//    if ($value['jenis'] == 'PULSA'){
//        //$history[$key]['jenis'] = 'PULSA';
//      //  $history[$key]['tanggal'] = $value['tanggal'];
//      //  $history[$key]['total_harga'] = $value['harga'];
//    } else if ($value['jenis'] == 'PRODUK'){
//        $no_transaksi = 0;
//        if ($no_transaksi == $value['no_transaksi']){
//
//        } else {
//            $history[$key]['jenis'] = 'PRODUK';
//            $history[$key]['tanggal'] = $value['tanggal'];
//            $history[$key]['total_harga'] = $value['harga'];
//        }
//
//    }


}
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }


}
