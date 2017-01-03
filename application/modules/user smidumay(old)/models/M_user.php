<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_user extends CI_Model
{

    public function __construct()
    {
        parent::__construct(); //inherit dari parent
    }


    //region user smidumay
    function get_user_detail($user_id)
    {
        $this->db->select('username,id_user,foto');
        $this->db->where('id_user', $user_id);
        $query = $this->db->get('user_info');
        $result = $query->result_array();

       if ($query->num_rows() > 0) {
           return $result[0];
       } else {
           return FALSE;
       }
    }

    function get_login_data($username)
    {
        $this->db->select('password,id_user');
        $this->db->where('username', $username);
        $query = $this->db->get('user_info');
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result[0];
        } else {
            return FALSE;
        }
    }

    function insert($user_info,$user_detail,$alamat)
    {
        $this->db->insert('user_info', $user_info);
        $this->db->insert("user_detail", $user_detail);
        $this->db->insert('user_alamat', $alamat);
    }

    //endregion


    //region user gchat
    //endregion



}
