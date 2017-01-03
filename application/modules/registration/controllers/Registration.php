<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class registration extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_registration');

    }

    function empty_validator($input_name,$input){
        if (empty($input) || $input == ""){
            $this->response([
                'status' => FALSE,
                'message' => $input_name.' cannot be empty'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    function register_post(){

        $this->empty_validator('Username',$this->input->post('username'));
        $this->empty_validator('Password',$this->input->post('password'));
        $this->empty_validator('Email',$this->input->post('email'));

        $id_user = "8".time();
        $username = $this->input->post('username');
        $password = sha1(md5(strrev($this->input->post('password'))));
        $email = $this->input->post('email');
        $fullname = $this->input->post('fullname');
        $phone = $this->input->post('phone');
        $picture = $this->input->post('picture');

        $user_info = array(
            'id_user' 		=> $id_user,
            'username' 		=> $username,
            'password' 		=> $password,
            'email' 		=> $email,
            'full_name'     => $fullname,
            'phone' 		=> $phone,
            'picture' 		=> $picture,
            'last_seen' 	=> date("Y-m-d H:i:s"),
            'time_created' 	=> date("Y-m-d H:i:s"),
            'status_active'  => "Y",
            'service_time' 	=> date("Y-m-d H:i:s"),
            'service_action' => "insert",
            'service_user'	=> $id_user);

        $status = $this->m_registration->insert($user_info);

        if ($status == 'Success'){
            $this->response([
                'status'    => TRUE,
                'message'   => "Success",
                'data'      => $this->m_registration->get_user_detail($id_user)
            ], REST_Controller::HTTP_OK);
        } else{
            $this->response([
                'status'    => FALSE,
                'message'   => $status,
                'data'      => NULL
            ], REST_Controller::HTTP_BAD_REQUEST);
        }


    }

    function global_register_post(){

        $this->empty_validator('Username',$this->input->post('username'));
        $this->empty_validator('Password',$this->input->post('password'));
        $this->empty_validator('Email',$this->input->post('email'));

        $id_user = "8".time();
        $id_user_web = "9".time();
        $username = $this->input->post('username');
        $password = sha1(md5(strrev($this->input->post('password'))));
        $email = $this->input->post('email');
        $fullname = $this->input->post('fullname');
        $name = explode(" ", $fullname);
        $phone = $this->input->post('phone');
        $picture = $this->input->post('picture');

        #data user untuk gchat
        $user_info = array(
            'id_user' 		=> $id_user,
            'username' 		=> $username,
            'password' 		=> $password,
            'email' 		=> $email,
            'full_name'     => $fullname,
            'phone' 		=> $phone,
            'picture' 		=> $picture,
            'last_seen' 	=> date("Y-m-d H:i:s"),
            'time_created' 	=> date("Y-m-d H:i:s"),
            'status_active'  => "Y",
            'service_time' 	=> date("Y-m-d H:i:s"),
            'service_action' => "insert",
            'service_user'	=> $id_user);


        #data user untuk web
        $user_info_web = array('id_user' => $id_user_web,
            'username' => $username,
            'password' => $password,
            'status_active' => 1,
            'level' => "5",
            'last_login'=> date('H:i:s'),
            'service_time' => date('Y/m/d H:i:s'),
            'service_action' => 'insert_chat');

        $user_detail_web = array('id_user' => $id_user_web,
            'nama_lengkap' => $fullname,
            'nama_depan' =>  $name[0],
            'nama_belakang' =>  $name[1],
            'alamat' => "",
            'jenis_kelamin' => "",
            'email' => $email,
            'telp' => $phone,
            'service_time' => date('Y/m/d H:i:s'),
            'service_action' => 'insert_chat');

        #insert gchat
        //$status = $this->m_registration->insert($user_info);
        #insert web

        $status_web = $this->m_registration->create_user_web($user_info,$user_info_web,$user_detail_web);


        if ($status_web == 'Success'){
            $this->response([
                'status'    => TRUE,
                'message'   => "Success",
                'data'      => $this->m_registration->get_user_detail($id_user)
            ], REST_Controller::HTTP_OK);
        } else{
            $this->response([
                'status'    => FALSE,
                'message'   => $status_web,
                'data'      => NULL
            ], REST_Controller::HTTP_BAD_REQUEST);
        }


    }



}
