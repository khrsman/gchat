<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class chat extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_chat');

    }

    function history_get(){
		 $user_id = '5';
        $get_history = $this->m_chat->get_by_user($user_id);		
         $this->response($get_history);
    }

    function detail_post(){

        $get_chat = $this->m_chat->get($chat_id);
    }

    function create_post(){

        $insert = $this->m_chat->insert($data);
    }

    function edit_post(){

        $edit = $this->m_chat->edit($data);
    }

    function remove_delete(){

        $remove = $this->m_chat->remove($data);   
    }


    

}
