<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reset_password extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }


   // public function index() {
//        echo 'kaharisman';
//        header('Location: keterangan.pdf');
//        exit;
 //   }

    function confirm(){

        $decrypted_username = str_replace(array('-', '_', '~'), array('+', '/', '='), $this->input->get('code'));
        $this->update_password($decrypted_username);
        echo 'Your password has been reset, please relogin with your new password';
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





}
