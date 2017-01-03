<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Core_banking_model extends CI_Model {

	public function __construct(){
		parent::__construct(); //inherit dari parent
	}


    //  LOYALTY ACCOUNT
    function get_loyalty_account_by_user($id_user){
        $this->db->select('*');
        $this->db->from('mcb_rekening_loyalti');
        $this->db->where('id_user',$id_user);

        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data']; } else { return FALSE; }
    }

    function update_loyalty_account_batch($data){
        $this->db->update_batch('mcb_rekening_loyalti', $data, 'no_rekening_loyalti');
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }



    //  VIRTUAL ACCOUNT
    function get_virtual_account_by_user($id_user){
        $this->db->select('*');
        $this->db->from('mcb_rekening_virtual');
        $this->db->where('id_user',$id_user);

        $query = $this->db->get();

        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data']; } else { return FALSE; }
    }

    function update_virtual_account($data){
        $virtual_account_number = $data['no_rekening_virtual'];
        unset($data['no_rekening_virtual']);
        $this->db->where('no_rekening_virtual',$virtual_account_number);
        $this->db->update('mcb_rekening_virtual', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }



    //  TRANSAKSI
    function insert_transaksi($data){
        $this->db->insert('mcb_transaksi_rekening', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }



    //  LOG TRANSAKSI
    function insert_log_transaksi($data){
        $this->db->insert('mcb_log_transaksi', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }


    function insert_log_transaksi_non_member($data){
        $this->db->insert('mcb_log_transaksi_non_member', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }








    // NON MEMBER
    function get_non_member_by_rekening($no_rekening){
        $this->db->select('*');
        $this->db->from('mcb_rekening_non_member');
        $this->db->where('no_rekening',$no_rekening);

        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data'][0]; } else { return FALSE; }
    }

    function get_non_member_by_koperasi($id_koperasi){
        $this->db->select('*');
        $this->db->from('mcb_rekening_non_member');
        $this->db->where('id_koperasi',$id_koperasi);

        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data']; } else { return FALSE; }   
    }

    function update_non_member($data){
        $non_member_norek = $data['no_rekening'];
        unset($data['no_rekening']);
        $this->db->where('no_rekening',$non_member_norek);
        $this->db->update('mcb_rekening_non_member', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }


    // PROFIT RULE
    function get_profit_rule_sharing_by_koperasi($id_koperasi){
        $this->db->select('*');
        $this->db->from('profit_rule_sharing');
        $this->db->where('id_koperasi',$id_koperasi);

        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data'][0]; } else { return FALSE; }   
    }


     function get_profit_rule_koperasi_by_koperasi($id_koperasi){
        $this->db->select('*');
        $this->db->from('profit_rule_koperasi');
        $this->db->where('id_koperasi',$id_koperasi);

        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data']; } else { return FALSE; }   
    }


    function get_koperasi_by_id($id_koperasi){
        $this->db->select('*');
        $this->db->from('koperasi');
        $this->db->where('id_koperasi',$id_koperasi);

        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data'][0]; } else { return FALSE; }   
    }




    // REKENING SMIDUMAY UTAMA
    function insert_log_transaksi_smidumay_utama($data){
        $this->db->insert('log_transaksi_utama_smi', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }
    
    function update_smidumay_utama($data){
        $no_rekening = $data['no_rekening'];
        unset($data['no_rekening']);
        $this->db->where('no_rekening',$no_rekening);
        $this->db->update('mcb_rekening_utama_smi', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    function get_smidumay_utama_by_rekening($no_rekening){
        $this->db->select('*');
        $this->db->from('mcb_rekening_utama_smi');
        $this->db->where('no_rekening',$no_rekening);

        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data'][0]; } else { return FALSE; }
    }

    function get_smidumay_utama(){
        $this->db->select('*');
        $this->db->from('mcb_rekening_utama_smi');

        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data']; } else { return FALSE; }
    }




    // REKENING SMIDUMAY
    function insert_log_transaksi_smidumay($data){
        $this->db->insert('log_transaksi_smidumay', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    function update_smidumay($data){
        $no_rekening = $data['no_rekening'];
        unset($data['no_rekening']);
        $this->db->where('no_rekening',$no_rekening);
        $this->db->update('mcb_rekening_smidumay', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    function get_smidumay_by_rekening($no_rekening){
        $this->db->select('*');
        $this->db->from('mcb_rekening_smidumay');
        $this->db->where('no_rekening',$no_rekening);

        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data'][0]; } else { return FALSE; }
    }

    function get_smidumay(){
        $this->db->select('*');
        $this->db->from('mcb_rekening_smidumay');

        $query = $this->db->get();
        $result['data']  = $query->result_array();
        $result['count'] = $query->num_rows();

        if($query->num_rows() > 0){ return $result['data']; } else { return FALSE; }
    }


}

