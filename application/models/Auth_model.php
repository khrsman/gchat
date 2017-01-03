<?php

class Auth_model extends CI_Model {

// @todo semuanya belum ke database db_web_app

    public function __construct()

    {

            parent::__construct();

    }



    function login($data) {

        $this->db->select('*');

        $this->db->from('user_info');

        $this->db->where('username', $data['username']);

        $this->db->where('password', $data['password']);

        // $this->db->where('password', $data['password']);

        

        $query = $this->db->get();

        $kembali['data']  = $query->result_array();

        $kembali['count'] = $query->num_rows();

        

        $result = $query;



        if($result->num_rows() > 0){

            

            $username = $result->row()->username;



            //begin koperasi

            if($result->row()->level == '2'){

                echo "koperasi";

                $this->db->select('user_info.id_user, user_info.foto, user_info.username, user_info.password, koperasi.nama as nama_lengkap, user_info.level, user_info.last_login, koperasi.parent_koperasi, koperasi.id_koperasi, koperasi.status_active as status_active ');

                $this->db->from('user_info');

                $this->db->join('koperasi', 'koperasi.id_user = user_info.id_user');

                $this->db->where('user_info.username', $username);

                $result = $this->db->get();

                if($result->num_rows() > 0){

                    // return $result->result();

                    $kembali['data_com'] = $result->result();

                }

                else {

                    // return FALSE;

                }

            } //end of koperasi





            // begin komunitas

            else if ($result->row()->level == '4'){

                echo "komunitas";

                $this->db->select('user_info.id_user,

                                   user_info.foto,

                                   user_info.username,

                                   user_info.password,

                                   komunitas.nama as nama_lengkap,

                                   user_info.level, user_info.last_login,

                                   komunitas.id_komunitas,

                                   komunitas.status_active as status_active');







                $this->db->from('user_info');

                $this->db->join('komunitas', 'komunitas.id_user = user_info.id_user');

                $this->db->where('user_info.username', $username);

                $result = $this->db->get();

                if($result->num_rows() > 0){

                    // return $result->result();

                    $kembali['data_com'] = $result->result();

                }

                else {

                    // return FALSE;

                }

            }









            else{

                // echo "member & admin";

                $this->db->select('user_info.id_user, user_info.username, user_info.foto, user_info.password, user_detail.nama_lengkap, user_info.level, user_info.last_login, user_info.koperasi, user_info.komunitas');

                $this->db->where('user_info.username', $username);

                $this->db->from('user_info');

                $this->db->join('user_detail', 'user_detail.id_user = user_info.id_user');

                $result = $this->db->get();

                if($result->num_rows() > 0){

                    // return $result->result();

                    $kembali['data_com'] = $result->result();

                }

                else {

                    // return FALSE;

                }

            }

        }





        if($query->num_rows() > 0){ return $kembali; } else { return FALSE; }

    }







    function update_last_login($username){

        $this->db->where('username', $username);

        $data = array('last_login' => date("Y-m-d H:i:s"),

                        'session_token' => "5".time() );

        $this->db->update('user_info', $data);

    }





    



    

    function insert_user(){



        $password = sha1(md5(strrev($this->input->post('password'))));

        $id_user = "9".time();

        $user_info = array('id_user' => $id_user,

                          'username' => $this->input->post('username'),

                          'password' => $password,

                          'status_active' => 1,

                          'level' => "5",

                          'last_login'=> date('H:i:s'),

                          'service_time' => date('Y/m/d H:i:s'),

                          'service_action' => 'insert');



        $user_detail = array('id_user' => $id_user,

                             'nama_lengkap' => $this->input->post('nama_depan')."&nbsp;".$this->input->post('nama_belakang'),

                             'nama_depan' => $this->input->post('nama_depan'),

                             'nama_belakang' => $this->input->post('nama_belakang'),

                             'alamat' => $this->input->post('alamat'),

                             'jenis_kelamin' => $this->input->post('jkel'),

                             'jabatan' => $this->input->post('jabatan'),

                             'email' => $this->input->post('email'),

                             'telp' => $this->input->post('telp'),

                             'service_time' => date('Y/m/d H:i:s'),

                             'service_action' => 'insert');

        

        foreach ($this->get_question()->result() as $row){

            $question_answer = array('id_user' => $id_user,

                                     'id_pertanyaan' => $row->id_pertanyaan,

                                     'jawaban' => $this->input->post($row->id_pertanyaan));



            $this->db->insert('user_answer_question', $question_answer);

        }

        

        

        $this->db->insert("user_info", $user_info);

        $this->db->insert("user_detail", $user_detail);

    }



    function cek_username($username){



        $this->db->select('username');

        $this->db->where('username', $username);

        $query = $this->db->get('user_info');



        if($query->num_rows() == 0){

            return TRUE;

        }

        else {

            return FALSE;

        }



    }

    function get_user_detail_by_id($id_user){

        $this->db->select('*');
        $this->db->where('id_user', $id_user);
        $query = $this->db->get('user_detail');
        $data = $query->result_array();

        if($query->num_rows() > 0){
            return $data[0];
        }
        else {
            return FALSE;
        }

    }


    function get_user_info_by_id($id_user){

        $this->db->select('*');
        $this->db->where('id_user', $id_user);
        $query = $this->db->get('user_info');
        $data = $query->result_array();


        if($query->num_rows() > 0){
            return $data[0];
        }
        else {
            return FALSE;
        }

    }


    function verify($data){

        $this->db->where('email_verification_code', $data['email_verification_code']);

        $this->db->update('user_info' ,$data);



        return $this->db->affected_rows() > 0 ? TRUE : FALSE;

    }



    function get_question(){

        return $this->db->get('user_question');

    }

    

    function cek_status_active_koperasi($id){

            $this->db->select('status_active');

            $this->db->where('id_koperasi', $id);

            return $this->db->get('koperasi');

    }



    function cek_status_active_komunitas($id){

            $this->db->select('status_active');

            $this->db->where('id_komunitas', $id);

            return $this->db->get('komunitas');

    }


    function cek_email($email){
    $query = $this->db->query("SELECT email FROM koperasi WHERE koperasi.email = '$email'
                               UNION 
                               SELECT email FROM komunitas WHERE komunitas.email =  '$email'
                               UNION 
                               SELECT email FROM user_detail WHERE user_detail.email = '$email'");


        if($query->num_rows() > 0){
            return $query;
        }
        else {
            return FALSE;
        }
    }

    function get_username_by_email($email){
    $query = $this->db->query("SELECT user_info.username, koperasi.nama as nama FROM user_info LEFT JOIN koperasi ON koperasi.id_user = user_info.id_user WHERE koperasi.email = '$email'
                               UNION 
                               SELECT user_info.username, komunitas.nama as nama FROM user_info LEFT JOIN komunitas ON komunitas.id_user = user_info.id_user WHERE komunitas.email =  '$email'
                               UNION 
                               SELECT user_info.username, user_detail.nama_depan as nama FROM user_info LEFT JOIN user_detail ON user_detail.id_user = user_info.id_user WHERE user_detail.email =  '$email'");


        if($query->num_rows() == 1){
            return $query;
        }
        else {
            return FALSE;
        }
    }


    function update_password($username){
            $this->db->select('username');
            $this->db->from('user_info');
            $this->db->where('username', $this->encrypt->decode($username));
            $result = $this->db->get();

            if($result->num_rows() == 1){
                $data = array('password' => sha1(md5(strrev($this->input->post('password')))));
                $this->db->where('username', $this->encrypt->decode($username));
                $this->db->update('user_info', $data);
                return $result;
            }
            else{
                return FALSE;
            }

    }



    function cek_reset_password_username($username){
        $this->db->select('username');
        $this->db->from('user_info');
        $this->db->where('username', $this->encrypt->decode($username));
        $result = $this->db->get();

        if($result->num_rows() == 1){
            return $result;
        }
        else {
            return FALSE;
        }

    }



}