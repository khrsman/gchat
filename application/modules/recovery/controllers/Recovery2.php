<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Recovery2 extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }


    public function index() {
        echo 'kaharisman';
//        header('Location: keterangan.pdf');
//        exit;
    }

}
