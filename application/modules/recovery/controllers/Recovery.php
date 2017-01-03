<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class recovery
    extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_recovery');
    }

    public function reset_password_post()
    {
        $email = $this->input->post('email');
        $get_data = $this->m_recovery->get_username_by_email($email);
        if($get_data){
            $this->load->library('encrypt');
            $username = $get_data['username'];
            $encrypted_username = $this->encrypt->encode(($username));
            $encrypted_username = str_replace(array('+', '/', '='), array('-', '_', '~'), $encrypted_username);

            $data['nama'] = $get_data['full_name'];
            $data['username'] = $username;
            //$data['link'] = http://103.28.15.3/~gchat/API/index.php/recovery/confirm_reset/code/".$encrypted_username;
            $data['link'] = "http://localhost/gchat_web_api/index.php/reset_password/confirm?code=".$encrypted_username;

            $message = $this->load->view('recovery/email_view',$data,TRUE);

            //die( $data['link']);

            $this->load->library('email');
            $config['mailtype'] = "html";

            $this->email->initialize($config);
            $this->email->from('recovery@smidumay.com', 'SMIDUMAY Account Recovery');
            $this->email->to($email);
            $this->email->cc('');
            $this->email->bcc('');
            $this->email->subject('SMIDUMAY Account Recovery');
            $this->email->message($message);

            $this->email->send();


            $this->response([
                'status' => TRUE,
                'message' => 'Permintaan reset password telah dikirim ke alamat email'
            ], REST_Controller::HTTP_OK);
        }
        else{
            $this->response([
                'status' => FALSE,
                'message' => 'Email not registered'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

    }

    function confirm_reset_get(){
        $decrypted_username = str_replace(array('-', '_', '~'), array('+', '/', '='), $this->get('code'));
        $this->m_recovery->update_password($decrypted_username);

        $data = array(
            'password' 		=> 'password');
        $this->response([
            'status' => TRUE,
            'message' => 'Password has been reset',
            'data' => $data
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



}
