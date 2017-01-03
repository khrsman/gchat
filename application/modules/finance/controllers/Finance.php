<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Finance extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_finance');

    }

    function saldo_get(){
      $user_id = $this->get('id_user');
    	$saldo_tabungan = $this->m_finance->get_saldo_rekening_tabungan_anggota($user_id);
    	$saldo_virtual = $this->m_finance->get_saldo_rekening_virtual_anggota($user_id);
    	$saldo_loyalti = $this->m_finance->get_saldo_rekening_loyalti_anggota($user_id);

        $detail= array( 'tabungan' => $saldo_tabungan,
            'virtual' => $saldo_virtual,
            'loyalti' => $saldo_loyalti);

        if (empty($detail)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Tidak ada saldo'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
          $this->response([
              'status' => TRUE,
              'message' => '',
              'data' => $detail
          ], REST_Controller::HTTP_OK);
        }
    }

    function transfer_post(){
        // @todo tambah validasi pin
        $user_id = $this->input->post('id_user');
        $user_target = $this->input->post('target_user');
        $nominal = $this->input->post('nominal');
        $pin = $this->input->post('pin');
        $saldo_virtual_user = $this->m_finance->get_saldo_rekening_virtual_anggota($user_id);
        $saldo_virtual_target = $this->m_finance->get_saldo_rekening_virtual_anggota($user_target);

        if($saldo_virtual_user){
            if($saldo_virtual_user[0]['saldo'] < $nominal){
                $this->response([
                    'status' => FALSE,
                    'message' => 'Saldo tidak mencukupi'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else{
            $this->response([
                'status' => FALSE,
                'message' => 'user tidak memeiliki saldo virtual'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $saldo_awal_user = $saldo_virtual_user[0]['saldo'];
        $saldo_awal_target = $saldo_virtual_target[0]['saldo'];

        $saldo_akhir_user = $saldo_awal_user - $nominal;
        $saldo_akhir_target = $saldo_awal_target + $nominal;

//        echo ('saldo awal user: '.$saldo_awal_user);
//        echo ('<br> saldo awal target: '.$saldo_awal_target);
//        echo ('<br> saldo akhir user: '.$saldo_akhir_user);
//        echo ('<br> saldo akkhir target: '.$saldo_akhir_target);
//
//        die;

        $data_user = array(
            'saldo' => $saldo_akhir_user,
            'tanggal_transaksi_terakhir' => date("Y-m-d H:i:s")
        );
        $data_target = array(
            'saldo' => $saldo_akhir_target
        );

        $transfer = $this->m_finance->transfer($user_id,$user_target,$data_user,$data_target);

        if ($transfer) {
            $this->response([
                'status' => true,
                'data' => array('id_user'=> $user_id, 'saldo'=>$saldo_akhir_user),
                'message' => 'Transaksi berhasil'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Transaksi gagal'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }


    function transaction_history_get(){
        $user_id = $this->get('id_user');
        $history = $this->m_finance->get_history_transaksi($user_id);



        if (empty($history)) {
            $this->response([
                'status' => FALSE,
                'message' => 'There is no transaction'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
          $this->response([
              'status' => TRUE,
              'message' => '',
              'data' => $history
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


}
