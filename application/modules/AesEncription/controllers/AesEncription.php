<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/aes.php';

class AesEncription extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_aes');
    }

    function public_aes_get(){

        $user_id = $this->get('id_user');
        $public_timeline = $this->m_aes->get_public_timeline($user_id);
        if (empty($public_timeline)) {
            $this->response([
                'status' => FALSE,
                'message' => 'No Status'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {

            $inputText = serialize($public_timeline);
            $aes = new AES($inputText);
            $enc = $aes->encrypt();

            $this->response([
                'status' => TRUE,
                'message' => "Success",
                'data' => $enc
            ], REST_Controller::HTTP_OK);


        }




    }

}
