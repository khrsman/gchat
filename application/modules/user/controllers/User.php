<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class user extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_user');
    }

    function detail_get()
    {
        $user_id = $this->get('id_user');
        $user = $this->m_user->get_user_detail($user_id);

        if (empty($user)) {
            $this->response([
                'status' => FALSE,
                'message' => 'User tidak ditemukan',
                  'data' => NULL
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
                      $this->response([
                          'status' => TRUE,
                          'message' => '',
                          'data' => $user
                      ], REST_Controller::HTTP_OK);
        }
    }

//    function update_picture_post(){
//
//            $this->empty_validator('Picture',$this->input->post('picture'));
//
//            $id_user = $this->input->post('id_user');
//            $picture = $this->input->post('picture');
//
//            $data = array(
//                'picture' 		=> $picture,
//                'service_time' 	=> date("Y-m-d H:i:s"),
//                'service_action' => "UPDATE");
//
//            $this->m_user->set_picture($data,$id_user);
//
//            $this->response([
//                'status'    => TRUE,
//                'message'   => 'Profile picture has been updated',
//                'data'      => $this->m_user->get_user_detail($id_user)
//            ], REST_Controller::HTTP_OK);
//
//    }


    function update_name_post(){

        $this->empty_validator('Name',$this->input->post('name'));

        $id_user = $this->input->post('id_user');
        $name = $this->input->post('name');

        $data = array(
            'full_name' 		=> $name,
            'service_time' 	=> date("Y-m-d H:i:s"),
            'service_action' => "UPDATE");

        $this->m_user->set_name($data,$id_user);

        $this->response([
            'status'    => TRUE,
            'message'   => 'Nama berhasil diganti',
            'data'      => $this->m_user->get_user_detail($id_user)
        ], REST_Controller::HTTP_OK);

    }

    function update_status_post(){
        $this->empty_validator('Status',$this->input->post('status'));
        $id_user = $this->input->post('id_user');
        $status = $this->input->post('status');

        $data = array(
            'status' 		=> $status,
            'service_time' 	=> date("Y-m-d H:i:s"),
            'service_action' => "UPDATE");

        $this->m_user->set_name($data,$id_user);

        $this->response([
            'status'    => TRUE,
            'message'   => 'Status telah diupdate',
            'data'      => $this->m_user->get_user_detail($id_user)
        ], REST_Controller::HTTP_OK);

    }

    function update_profile_post(){
        $id_user = $this->input->post('id_user');
        $phone = $this->input->post('phone');
        $fullname = $this->input->post('fullname');
        $email = $this->input->post('email');
        $status_active = $this->input->post('status_active');

        $this->empty_validator('Phone',$phone);
        $this->empty_validator('Full Name',$fullname);
        $this->empty_validator('Email',$email);

        $data = array(
            'phone'         => $phone,
            'full_name'         => $fullname,
            'email'         => $email,
            'status_active'         => $status_active
            );
        $status = $this->m_user->update_profile($data,$id_user);
        $this->response([
            'status'    => TRUE,
            'message'   => $status,
            'data'      => $data
        ], REST_Controller::HTTP_OK);
    }

    function empty_validator($input_name,$input){
        if (empty($input) || $input == ""){
            $this->response([
                'status' => FALSE,
                'message' => $input_name.' cannot be empty'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    function update_picture_post(){
        if (!isset($_FILES['picture'])){
            $this->response([
                'status' => FALSE,
                'message' => 'No file'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $userid = $this->input->post('id_user');
        $this->empty_validator('Id User',$userid);

        $filename =  $userid.'__'.$_FILES['picture']['name'];
        //$url_web = 'http://localhost/gchat_web_api/';
        $url_web = 'http://103.28.15.3/~gchat/API/';
        $url_upload = $url_web.'upload.php?filename='.$filename;
        $ch = curl_init();
        $cfile = new CURLFile($_FILES['picture']['tmp_name'],$_FILES['picture']['type'],$_FILES['picture']['name']);
        $data = array("myimage"=>$cfile);

        if (strpos($_FILES['picture']['type'], 'image') === false) {
            $this->response([
                'status' => FALSE,
                'message' => 'File must be an Image'
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else{
            curl_setopt($ch, CURLOPT_URL, $url_upload);
            curl_setopt($ch, CURLOPT_POST,true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
            $resnponse = curl_exec($ch);

            $data = array(
                'id_user'         => $userid,
                'picture'         => $url_web.'image/'.$filename
            );

            $this->m_user->update_picture($data,$userid);

            if($resnponse == true){
                $this->response([
                    'status'    => TRUE,
                     'message'   => 'Upload Success',
                     'data'      => $data
                ], REST_Controller::HTTP_OK);
            } else {
                echo "error: ". curl_error($ch);
            }
        }
    }



}
