<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class auth extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_auth');
    }

    public function login_post()
    {
        $username = $this->post('username');
        $password = $this->post('password');

        if (!isset($username) || empty($username) || !isset($password) || empty($password)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Username dan password tidak boleh kosong'
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $data_login = $this->m_auth->get_login_data($username);
            if ($data_login == FALSE) {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Username dan password tidak cocok'
                ], REST_Controller::HTTP_BAD_REQUEST);
            } else {
                if ($data_login['password'] != sha1(md5(strrev($password)))) {
                    $this->response([
                        'status' => FALSE,
                        'message' => 'c'
                    ], REST_Controller::HTTP_BAD_REQUEST);
                } else {
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Login success',
                        'data' => $this->m_auth->get_user_detail($data_login['id_user'])
                    ], REST_Controller::HTTP_OK);
                }
            }
        }
    }



    public function finance_login_post()
    {
        $username = $this->post('username');
        $password = $this->post('password');

        if (!isset($username) || empty($username) || !isset($password) || empty($password)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Username dan password tidak boleh kosong'
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $data_login = $this->m_auth->get_finance_login_data($username);
            if ($data_login == FALSE) {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Username dan password tidak cocok'
                ], REST_Controller::HTTP_BAD_REQUEST);
            } else {
                if ($data_login['password'] != sha1(md5(strrev($password)))) {
                    $this->response([
                        'status' => FALSE,
                        'message' => 'Username dan password tidak cocok'
                    ], REST_Controller::HTTP_BAD_REQUEST);
                } else {
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Login success',
                        'data' => $this->m_auth->get_finance_user_detail($data_login['id_user'])
                    ], REST_Controller::HTTP_OK);
                }
            }
        }
    }



    function empty_validator($input_name,$input){
        if (empty($input) || $input == ""){
            $this->response([
                'status' => FALSE,
                'message' => $input_name.' cannot be empty'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }



}
