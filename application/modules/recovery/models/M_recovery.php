<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_recovery extends CI_Model
{

    public function __construct()
    {
        parent::__construct(); //inherit dari parent
    }

    function get_username_by_email($email){
        $query = $this->db->query("SELECT username, full_name FROM user_info_gchat WHERE email = '$email'");
        $result = $query->result_array();

        if($query->num_rows() >= 1){
            return $result[0];
        }
        else {
            return FALSE;
        }
    }

    function update_password($username){
        $this->load->library('encrypt');
        $this->db->select('username');
        $this->db->from('user_info');
        $this->db->where('username', $this->encrypt->decode($username));
        $result = $this->db->get();

        if($result->num_rows() >= 1){
            $data = array('password' => sha1(md5(strrev($this->input->post('password')))));
            $this->db->where('username', $this->encrypt->decode($username));
            $this->db->update('user_info', $data);
            return $result;
        }
        else{
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

}
