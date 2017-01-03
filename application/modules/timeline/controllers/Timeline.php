<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/aes.php';

class Timeline extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_timeline');
    }


    function timeline_with_comment_get(){
        $share = $this->get('share');
        $user_id = $this->get('id_user');
        $user_timeline = $this->m_timeline->get_timeline_with_comment($user_id,$share);
        if (empty($user_timeline)) {
            $this->response([
                'status' => FALSE,
                'message' => 'No Status'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
          $this->response([
              'status' => TRUE,
              'message' => '',
              'data' => $user_timeline
          ], REST_Controller::HTTP_OK);
        }
    }

    function user_get(){
        //$url_web = 'http://localhost/gchat_web_api/';
        $url_web = 'http://103.28.15.3/~gchat/API/';
        $user_id = $this->get('id_user');
        $user_timeline = $this->m_timeline->get_user_timeline($user_id);
        if (empty($user_timeline)) {
            $this->response([
                'status' => FALSE,
                'message' => 'No Status'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
//            foreach($user_timeline as $key => $value)
//            {
//                $user_timeline[$key]['picture'] = $url_web.$value['picture'];
//            }
          $this->response([
              'status'    => TRUE,
              'message'   => '',
              'data'      => $user_timeline
          ], REST_Controller::HTTP_OK);
        }
    }

    function friend_get(){
        //$url_web = 'http://localhost/gchat_web_api/';
        $url_web = 'http://103.28.15.3/~gchat/API/';
        $user_id = $this->get('id_user');
        $friend_timeline = $this->m_timeline->get_friend_timeline($user_id);
        if (empty($friend_timeline)) {
            $this->response([
                'status' => FALSE,
                'message' => 'No Status'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
//            foreach($friend_timeline as $key => $value)
//            {
//                $friend_timeline[$key]['picture'] = $url_web.$value['picture'];
//            }
$this->response([
    'status'    => TRUE,
    'message'   => '',
    'data'      => $friend_timeline
], REST_Controller::HTTP_OK);
        }
    }

    function public_get(){
        //$url_web = 'http://localhost/gchat_web_api/';
        $url_web = 'http://103.28.15.3/~gchat/API/';
        $user_id = $this->get('id_user');
        $public_timeline = $this->m_timeline->get_public_timeline($user_id);
        if (empty($public_timeline)) {
            $this->response([
                'status' => FALSE,
                'message' => 'No Status'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            //foreach($public_timeline as $key => $value)
//            {
//                $public_timeline[$key]['picture'] = $url_web.$value['picture'];
//            }
$this->response([
    'status'    => TRUE,
    'message'   => '',
    'data'      => $public_timeline
], REST_Controller::HTTP_OK);
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

    function comment_get(){
        $id_timeline = $this->get('id_timeline');
        $comment = $this->m_timeline->get_timeline_comment($id_timeline);
        if (empty($comment)) {
            $this->response([
                'status' => FALSE,
                'message' => 'No Status'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->response([
                'status' => TRUE,
                'message' => $comment
            ], REST_Controller::HTTP_OK);
        }
    }

    function insert_comment_post(){
        $this->empty_validator('comment', $this->input->post('comment'));
        $this->empty_validator('id_user', $this->input->post('id_user'));
        $this->empty_validator('id_timeline', $this->input->post('id_timeline'));

        $comment = $this->input->post('comment');
        $id_timeline = $this->input->post('id_timeline');
        $id_user = $this->input->post('id_user');

        $data = array(
            'id_timeline' => $id_timeline,
            'id_user' => $id_user,
            'comment' => $comment);

        $status = $this->m_timeline->insert_comment($data);
        if ($status == 'Success') {
            $this->response([
                'status' => TRUE,
                'message' => "Success",
                'data' => $data
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => $status,
                'data' => NULL
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

    }


    function update_post()
    {
        $this->empty_validator('Status', $this->input->post('status'));
        $this->empty_validator('Caption', $this->input->post('caption'));

        $id_user = $this->input->post('id_user');
        $caption = $this->input->post('caption');
        $status = $this->input->post('status');
        $share = $this->input->post('share');
        $media_type = $this->input->post('media_type');

        $media = $this->insert_media($id_user);

        $data = array(
            'id_user' => $id_user,
            'status' => $status,
            'caption' => $caption,
            'share' => $share,
            'media_type' => $media_type,
            'media' => $media,
            'time_created' => date("Y-m-d H:i:s"),
            'service_action' => "INSERT");
        $status = $this->m_timeline->insert($data);
        if ($status == 'Success') {
            $this->response([
                'status' => TRUE,
                'message' => "Success",
                'data' => $data
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => $status,
                'data' => NULL
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    function insert_media($userid){
      if($_FILES){
        if ($_FILES['media']['size'] >= 50000000){
            $this->response([
                'status' => FALSE,
                'message' => 'File is too big, max 50MB is allowed'
            ], REST_Controller::HTTP_BAD_REQUEST);
            exit();
        } ;

        if (isset($_FILES['media'])){
            $filename =  'TL_'.$userid.'__'.$_FILES['media']['name'];
            //$url_web = 'http://localhost/gchat_web_api/';
            $url_web = 'http://103.28.15.3/~gchat/API/';
            $url_upload = $url_web.'upload.php?filename='.$filename;
            $ch = curl_init();
            $cfile = new CURLFile($_FILES['media']['tmp_name'],$_FILES['media']['type'],$_FILES['media']['name']);
            $data = array("myimage"=>$cfile);

            if ((strpos($_FILES['media']['type'], 'image') === false) && (strpos($_FILES['media']['type'], 'video') === false)) {
                $this->response([
                    'status' => FALSE,
                    'message' => 'File must be an image or video'
                ], REST_Controller::HTTP_BAD_REQUEST);
                exit();
            } else{
                curl_setopt($ch, CURLOPT_URL, $url_upload);
                curl_setopt($ch, CURLOPT_POST,true);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
                $resnponse = curl_exec($ch);

                $data = array(
                    'id_user'         => $userid,
                    'picture'         => $url_web.'image/'.$filename
                );

                if($resnponse == true){
                    return $data['picture'];
                } else {
                    echo "error: ". curl_error($ch);
                }
            }
        }

        }
    }


    function public_aes_get(){

        $user_id = $this->get('id_user');
        $public_timeline = $this->m_timeline->get_public_timeline($user_id);
        if (empty($public_timeline)) {
            $this->response([
                'status' => FALSE,
                'message' => 'No Status'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            echo serialize($public_timeline);
           //$this->set_response($public_timeline, REST_Controller::HTTP_OK);
        }


//$inputText = "My text to encrypt";
//$inputKey = "My text to encrypt";
//$blockSize = 256;
//$aes = new AES($inputText, $inputKey, $blockSize);
//$enc = $aes->encrypt();
//$aes->setData($enc);
//$dec=$aes->decrypt();
//echo "After encryption: ".$enc."<br/>";
//echo "After decryption: ".$dec."<br/>";

    }

}
