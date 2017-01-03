<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Contact extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_contact');
    }

    function list_get()
    {
        $user_id = $this->get('id_user');
        $user = $this->m_contact->get_list($user_id,'list','');

        if (empty($user)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Tidak ada kontak',
                'data'      => NULL
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
          $this->response([
           'status'    => TRUE,
           'message'   => '',
           'data'      => $user
                          ], REST_Controller::HTTP_OK);
        }
    }

    function search_contact_by_name_post()
    {
        $user_id = $this->post('id_user');
        $name = $this->post('name');
        $user = $this->m_contact->get_list($user_id,'name',$name);

        if (empty($user)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Tidak ada kontak',
                'data'      => NULL
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
          $this->response([
           'status'    => TRUE,
           'message'   => '',
           'data'      => $user
                          ], REST_Controller::HTTP_OK);
        }
    }

    function search_user_by_phone_get()
    {
        $phone = $this->get('phone');
        $this->empty_validator('Phone',$phone);
        $user = $this->m_contact->get_user_by_parameter('phone',$phone);

        if (empty($user)) {
            $this->response([
                'status' => FALSE,
                'message' => 'User tidak ada',
                'data'      => NULL
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
          $this->response([
           'status'    => TRUE,
           'message'   => '',
           'data'      => $user
                          ], REST_Controller::HTTP_OK);
        }
    }

    function search_user_by_username_get()
    {
        $username = $this->get('username');
        $this->empty_validator('Username',$username);
        $user = $this->m_contact->get_user_by_parameter('username',$username);

        if (empty($user)) {
            $this->response([
                'status' => FALSE,
                'message' => 'User Not Found',
                'data'      => NULL
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
          $this->response([
           'status'    => TRUE,
           'message'   => '',
           'data'      => $user
                          ], REST_Controller::HTTP_OK);
        }
    }


    function empty_validator($input_name,$input){
        if (empty($input) || $input == ""){
            $this->response([
                'status' => FALSE,
                'message' => $input_name.' Tidak boleh kosong',
                'data'      => NULL
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    function add_post(){
        $this->empty_validator('id_friends',$this->input->post('id_friends'));

        $id_friends = $this->input->post('id_friends');
        $id_user = $this->input->post('id_user');

        $data = array(
            'id_user' 		=> $id_user,
            'id_friends' 		=> $id_friends,
            'status_block' 		=> 'N');

        $exists = $this->m_contact->check_friends($id_user,$id_friends);

        if($exists){
            $this->response([
                'status'    => FALSE,
                'message'   => 'Sudah didalam kontak',
                'data'      => NULL
            ], REST_Controller::HTTP_NOT_FOUND);
        } else{
           $status = $this->m_contact->insert($data);
           $this->response([
            'status'    => TRUE,
            'message'   => $status,
            'data'      => $data
                           ], REST_Controller::HTTP_OK);
            }
        }


    function remove_post(){
        $this->empty_validator('id_friends',$this->input->post('id_friends'));

        $id_friends = $this->input->post('id_friends');
        $id_user = $this->input->post('id_user');

        $status = $this->m_contact->remove_friends($id_user,$id_friends);

       if ($status= "success"){
            $this->response([
                'status'    => TRUE,
                'message'   => $status,
                'data'      => NULL
            ], REST_Controller::HTTP_OK);
           } else{
           $this->response([
               'status'    => FALSE,
               'message'   => $status,
               'data'      => NULL
           ], REST_Controller::HTTP_BAD_REQUEST);
       }

    }


//

    function block_unblock_post(){
        $this->empty_validator('Status block',$this->input->post('status_block'));

        $id_friends = $this->input->post('id_friends');
        $id_user = $this->input->post('id_user');
        $status_block = $this->input->post('status_block');

        $data = array(
            'status_block' 		=> $status_block);

        $status = $this->m_contact->update_block($data,$id_user,$id_friends);

        $this->response([
            'status'    => TRUE,
            'message'   => $status,
            'data'      => $data
        ], REST_Controller::HTTP_OK);
    }

    function update_alias_post(){
        $this->empty_validator('Alias',$this->input->post('alias'));
        $id_friends = $this->input->post('id_friends');
        $id_user = $this->input->post('id_user');
        $alias = $this->input->post('alias');

        $data = array(
            'alias' 		=> $alias);
        $status = $this->m_contact->update_alias($data,$id_user,$id_friends);
        $this->response([
            'status'    => TRUE,
            'message'   => $status,
            'data'      => $data
        ], REST_Controller::HTTP_OK);
    }

    function suggest_friends_get(){
        $user_id = $this->get('id_user');
        $this->empty_validator('User id',$user_id);
        $user = $this->m_contact->get_suggest($user_id);
        if (empty($user)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Tidak ada rekomendasi'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
          $this->response([
              'status'    => TRUE,
              'message'   => '',
              'data'      => $user
          ], REST_Controller::HTTP_OK);
        }
    }
}
