<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_auth extends CI_Model
{

    public function __construct()
    {
        parent::__construct(); //inherit dari parent
        $this->db_web_app = $this->load->database('db_koperasi',TRUE);
    }

    function get_user_detail($user_id)
    {

        $this->db->select('id_user,username,email,full_name,phone,picture,last_seen,status,time_created,status_active');
        $this->db->where('id_user', $user_id);
        $query = $this->db->get('user_info_gchat');
        $result = $query->result_array();

       if ($query->num_rows() > 0) {
           return $result[0];
       } else {
           return FALSE;
       }
    }


    function get_finance_user_detail($user_id)
    {
        $this->db_web_app->select('user_info.id_user,username');
        $this->db_web_app->where('user_info.id_user', $user_id);
        $this->db_web_app->join('user_detail', 'user_info.id_user = user_detail.id_user');
        $query = $this->db_web_app->get('user_info');
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
        $query = $this->db->get('user_info_gchat');
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result[0];
        } else {
            return FALSE;
        }
    }

    function get_finance_login_data($username)
    {
        $this->db_web_app->select('password,id_user');
        $this->db_web_app->where('username', $username);
        $query = $this->db_web_app->get('user_info');
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result[0];
        } else {
            return FALSE;
        }
    }

}
