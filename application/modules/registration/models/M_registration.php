<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_registration extends CI_Model
{

    public function __construct()
    {
        parent::__construct(); //inherit dari parent
        $this->db_web_app = $this->load->database('db_smidumay',TRUE);
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

        function insert($user_info)
    {
        if($this->db->insert('user_info_gchat', $user_info)){
            return 'Success';
        }else{
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return  'Fail : '.$error['message'];
        }

    }


    function create_user_web($user_info, $user_info_web,$user_detail_web){

//
//        foreach ($this->get_question()->result() as $row){
//            $question_answer = array('id_user' => $id_user,
//                'id_pertanyaan' => $row->id_pertanyaan,
//                'jawaban' => $this->input->post($row->id_pertanyaan));
//
//            $this->db->insert('user_answer_question', $question_answer);
//        }

        $message = $this->insert($user_info);

        if ($message == 'Success') {
            $this->db_web_app->insert("user_info", $user_info_web);
            $this->db_web_app->insert("user_detail", $user_detail_web);
            return $message;
        } else{
            $message =  'Fail : ';//.$error['message'];
            return $message;
        }


    }





}
