<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_gerai extends CI_Model
{

    public function __construct()
    {
        parent::__construct(); //inherit dari parent
    }


    function get_list_operator()
    {
       $link = "www.smidumay.com/assets/compro/IMAGE/provider/";

        $this->db->select('gerai_operator.kode_operator, concat("'.$link.'",icon) icon');
        $this->db->where('gerai_operator.kode_operator <> "PLN"');
        $this->db->join('gerai_icon', 'gerai_icon.kode_operator = gerai_operator.kode_operator');
        $this->db->from('gerai_operator');
        //$this->db->group_by('kode_operator');
        $query = $this->db->get();
        //echo $this->db->last_query();
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_list_operator_pln_pra()
    {
        $link = "www.smidumay.com/assets/compro/IMAGE/provider/pembayaran/listrikprabayar.png";

        $this->db->select('gerai_operator.kode_operator, "'.$link.'" icon');
        $this->db->where('gerai_operator.kode_operator = "PLN"');
        $this->db->join('gerai_icon', 'gerai_icon.kode_operator = gerai_operator.kode_operator');
        $this->db->from('gerai_operator');
        //$this->db->group_by('kode_operator');
        $query = $this->db->get();
        //echo $this->db->last_query();
        //echo $this->db->last_query();
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_nominal($parameter=NULL){
        $this->db->select('kode_vendor, kode_operator, kode_produk, nominal_produk, nama_produk, harga_vendor, '.
                          'harga_gerai,kode_kategori_produk');
        $this->db->from('gerai_vendor_produk');

        if ($parameter!=NULL && is_array($parameter)) {
            foreach ($parameter as $k => $v) {
                $this->db->where($k,$v);
            }
        }else{
            $this->db->limit(100);
        }

        $this->db->where('harga_vendor < harga_gerai');
        $this->db->group_by('nominal_produk');
        $this->db->group_by('kode_operator');
        $this->db->order_by('CAST(nominal_produk AS DECIMAL(10)) ASC');

        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data']; } else { return FALSE; }
    }


    function insert_transaksi_gerai($data){
        $this->db->insert('gerai_transaksi', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }


    function get_nominal_pln($parameter=NULL){
        $this->db->select('*');
        $this->db->from('gerai_vendor_produk');

        if ($parameter!=NULL && is_array($parameter)) {
            foreach ($parameter as $k => $v) {
                $this->db->where($k,$v);
            }
        }else{
            $this->db->limit(100);
        }

        $this->db->where('harga_vendor < harga_gerai');
        $this->db->group_by('nominal_produk');
        $this->db->group_by('kode_operator');
        $this->db->order_by('CAST(nominal_produk AS DECIMAL(10)) ASC');


        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data']; } else { return FALSE; }
    }

    function get_anggota_koperasi_by_id($id_user){
        $this->db->select('*');
        $this->db->from('user_info');
        $this->db->join('koperasi','koperasi.id_koperasi=user_info.koperasi','LEFT');
        $this->db->join('user_detail','user_detail.id_user=user_info.id_user','LEFT');
        $this->db->where('user_info.level',3);
        $this->db->where('user_info.id_user',$id_user);
        $this->db->where('user_info.status_active',1);

        $this->db->limit(1);

        $query = $this->db->get();
        $result['data']  = $query->result_array();

        if($query->num_rows() > 0){ return $result['data'][0]; } else { return FALSE; }
    }

    function insert_pln($data){
        $this->db->insert('gerai_transaksi', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    function get_admin_fee($parameter=NULL){
        $this->db->select('*');
        $this->db->from('gerai_vendor_produk');

        if ($parameter!=NULL && is_array($parameter)) {
            foreach ($parameter as $k => $v) {
                $this->db->where($k,$v);
            }
        }else{
            $this->db->limit(10);
        }

        $this->db->where('harga_vendor < harga_gerai');
        $this->db->group_by('kode_operator');
        $this->db->order_by('CAST(harga_vendor AS DECIMAL(10)) ASC');


        $query = $this->db->get();

//        echo $this->db->last_query();
//        die;
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data']; } else { return FALSE; }
    }



}
