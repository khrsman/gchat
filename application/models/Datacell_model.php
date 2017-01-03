<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Datacell_model extends CI_Model {

	public function __construct(){
		parent::__construct(); //inherit dari parent
	}
    	
	function insert_topup($data){
        $this->db->insert('gerai_datacell_topup', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    function update_topup($data){
        $ref_trxid = $data['ref_trxid'];
        unset($data['ref_trxid']);
        $this->db->where('ref_trxid',$ref_trxid);
        $this->db->update('gerai_datacell_topup', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    function insert_respon($data){
        $this->db->insert('gerai_datacell_respon', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    function insert_report($data){
        $this->db->insert('gerai_datacell_report', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    function get_vendor_price($operator_id,$operator_code){
        $this->db->select('*');
        $this->db->from('gerai_harga_vendor');
        $this->db->where('id_operator',$operator_id);
        $this->db->where('kode_operator',$operator_code);
        
        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data'][0]; } else { return FALSE; }
    }

    function get_vendor_price_by_id($operator_id){
        $this->db->select('*');
        $this->db->from('gerai_harga_vendor');
        $this->db->where('harga_datacell <= harga_gerai');
        $this->db->where('id_operator',$operator_id);
        
        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data']; } else { return FALSE; }
    }

}
