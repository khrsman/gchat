<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Gerai extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_gerai');
        $this->load->library('core_banking_api');
    }

    function empty_validator($input_name,$input){
        if (empty($input) || $input == ""){
            $this->response([
                'status' => FALSE,
                'message' => $input_name.' cannot be empty'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    function list_operator_get()
    {
        $data = $this->m_gerai->get_list_operator();
        $this->response([
            'status' => True,
            'message' => 'Data Operator',
            'data' => $data
        ], REST_Controller::HTTP_OK);
    }

    function list_operator_pln_pra_get()
    {
        $data = $this->m_gerai->get_list_operator_pln_pra();
        $this->response([
            'status' => True,
            'message' => 'Data Operator',
            'data' => $data
        ], REST_Controller::HTTP_OK);

    }
    function list_nominal_pln_pra_get(){
        $parameter_produk	= array(
            'nominal_10'		=> '10000',
            'nominal_50' 	=> '50000'
        );

        $this->response([
            'status' => true,
            'message' =>"Success",
            'data' => $parameter_produk
        ], REST_Controller::HTTP_OK);
    }


    function topup_get(){

           // $data['form_action'] = site_url().'gerai/pulsa/topup/'.$this->uri->segment(4);

        $url = 'http://localhost/gchat_web_api/assets/provider/';
        //$url = 'http://103.28.15.3/~gchat/API/assets/provider/';
        $provider = $this->get('provider');


            switch ($provider) {

                case 'indosat':

                    $data['operator_id']	= 'INDOSAT';
                    $data['products']		=  $this->get_product($data['operator_id']);
                    $data['provider_logo'] 	= $url.'/'.'indosat.png';
                    $data['provider_name'] 	= 'Indosat';

                    break;

                case 'telkomsel':

                    $data['operator_id']	= 'TELKOMSEL';
                    $data['products']		=  $this->get_product($data['operator_id']);
                    $data['provider_logo'] 	= $url.'/'.'telkomsel.png';
                    $data['provider_name'] 	= 'Telkomsel';

                    break;

                case 'xl':

                    $data['operator_id']	= 'XL';
                    $data['products']		=  $this->get_product($data['operator_id']);
                    $data['provider_logo'] 	= $url.'/'.'XL.png';
                    $data['provider_name'] 	= 'XL';

                    break;

                case 'esia':

                    $data['operator_id']	= 'ESIA';
                    $data['products']		=  $this->get_product($data['operator_id']);
                    $data['provider_logo'] 	= $url.'/'.'esia.png';
                    $data['provider_name'] 	= 'Esia';

                    break;

                case 'smartfren':

                    $data['operator_id']	= 'SMARTFREN';
                    $data['products']		=  $this->get_product($data['operator_id']);
                    $data['provider_logo'] 	= $url.'/'.'smartfren.png';
                    $data['provider_name'] 	= 'Smartfren';

                    break;

                case 'flexi':

                    $data['operator_id']	= 'FLEXI';
                    $data['products']		=  $this->get_product($data['operator_id']);
                    $data['provider_logo'] 	= $url.'/'.'flexi.png';
                    $data['provider_name'] 	= 'Flexi';

                    break;

                case 'axis':

                    $data['operator_id']	= 'AXIS';
                    $data['products']		=  $this->get_product($data['operator_id']);
                    $data['provider_logo'] 	= $url.'/'.'axis.png';
                    $data['provider_name'] 	= 'Axis';

                    break;

                case 'tri':

                    $data['operator_id']	= 'THREE';
                    $data['products']		=  $this->get_product($data['operator_id']);
                    $data['provider_logo'] 	= $url.'/'.'3.png';
                    $data['provider_name'] 	= 'Tri';


                    break;

                case 'ceria':

                    $data['operator_id']	= 'CERIA';
                    $data['products']		= $this->get_product($data['operator_id']);
                    $data['provider_logo'] 	= $url.'/'.'3.png';
                    $data['provider_name'] 	= 'Ceria';

                    break;

                default:

                    $this->response([
                        'status' => false,
                        'message' =>"Kode produk salah",
                        'data' => NULL
                    ], REST_Controller::HTTP_BAD_REQUEST);

                    break;

            }

        $this->response([
            'status' => TRUE,
            'message' =>'Success',
            'data' => $data
        ], REST_Controller::HTTP_OK);

    }



    function topup_confirm_post(){

        $id_user = $this->post('id_user');
        $pin = $this->post('pin');
        $provider_name = $this->post('provider_name');
        $provider_logo = $this->post('provider_logo');
        $phone_number = $this->post('phone_number');
        $operator_id = $this->post('operator_id');
        $nominal = $this->post('nominal');


        $this->empty_validator('ID user',$id_user);
        $this->empty_validator('pin',$pin);
        $this->empty_validator('Provider Name',$provider_name);
        $this->empty_validator('Provider Logo',$provider_logo);
        $this->empty_validator('Phone Number',$phone_number);
        $this->empty_validator('Operator ID',$operator_id);
        $this->empty_validator('Nominal',$nominal);

            $post = $this->input->post();

            $get_product = $this->get_product($operator_id,$nominal);
            if ($get_product==FALSE) {
                $this->response([
                    'status' => false,
                    'message' =>'Product not found',
                    'data' => NULL
                ], REST_Controller::HTTP_BAD_REQUEST);

            }else{
                $get_product = $get_product[0];
            }

            if ($nominal!=$get_product['nominal_produk']) {
                $this->response([
                    'status' => false,
                    'message' =>'Nominal not match',
                    'data' => NULL
                ], REST_Controller::HTTP_BAD_REQUEST);
            }

            $service_user 	= $id_user;
            $service_action = 'INSERT_MOBILE';
            $trasaction_id 	= '13'.time();

            $data_insert = array(
                'no_transaksi' 			=> $trasaction_id,
                'kode_vendor' 			=> $get_product['kode_vendor'],
                'kode_operator' 		=> $get_product['kode_operator'],
                'kode_produk' 			=> $get_product['kode_produk'],
                'kode_kategori_produk' 	=> $get_product['kode_kategori_produk'],
                'nama_produk' 			=> $get_product['nama_produk'],
                'nominal_produk' 		=> $get_product['nominal_produk'],
                'harga_vendor'			=> $get_product['harga_vendor'],
                'harga_gerai'			=> $get_product['harga_gerai'],
                'jenis_transaksi'		=> 'BELI',
                'id_user'				=> $service_user,
                'msisdn'				=> $phone_number,
                'tanggal_transaksi'		=> date('Y-m-d H:i:s'),
                'tanggal'			=> date('d'),
                'bulan'				=> date('m'),
                'tahun'				=> date('Y'),
                'service_time'			=> date('Y-m-d H:i:s'),
                'service_user'			=> $service_user,
                'service_action'		=> $service_action
            );

            $this->load->library('vsi_api');

            $data = array(
                'service_user' 	=> $service_user,
                'produk'   		=> $get_product['kode_produk'],
                'tujuan'    	=> $post['phone_number'],
                'memberreff' 	=> $trasaction_id,
            );


            $data_report['provider_name'] 	= $post['provider_name'];
            $data_report['provider_logo'] 	= $post['provider_logo'];
            $data_report['product'] 		= $get_product;
            $data_report['data_insert'] 	= $data_insert;

            $permission = $this->core_banking_api->debit_virtual_account_permission($service_user,$get_product['harga_gerai']);



            if ($permission['status']==FALSE) {
                $this->response([
                    'status' => false,
                    'message' =>'Failed: '.$permission['message'],
                    'data' => NULL
                ], REST_Controller::HTTP_BAD_REQUEST);
            }

            // REQUEST KE VSI
            $request_charge = $this->vsi_api->charge($data);




            if ($request_charge==FALSE) {
                $data_insert['keterangan']  = 'Internal Server Error. API Error';
                $insert = $this->m_gerai->insert_transaksi_gerai($data_insert);
                $this->response([
                    'status' => false,
                    'message' =>'Failed: Transaksi Gagal.Maaf, Internal Server sedang ada gangguan.',
                    'data' => NULL
                ], REST_Controller::HTTP_BAD_REQUEST);

            }elseif ($request_charge['status']==FALSE) {

                $data_insert['ref_trxid']   = $request_charge['message']['trxid'];
                $data_insert['status'] 		= 'GAGAL';
                $data_insert['keterangan']  = $request_charge['message']['response_message'];
                $insert = $this->m_gerai->insert_transaksi_gerai($data_insert);
                $this->response([
                    'status' => false,
                    'message' =>'Failed: '.$request_charge['message']['response_message'],
                    'data' => NULL
                ], REST_Controller::HTTP_BAD_REQUEST);

            }else{

                //JUST FOR TEST
                /*print_r('<pre>');
                print_r($request_charge);
                print_r('</pre>');*/

                $data_insert['ref_trxid']   = $request_charge['message']['trxid'];
                $data_insert['status'] 		= 'SUKSES';
                $data_insert['keterangan']  = $request_charge['message']['response_message'];
                $insert = $this->m_gerai->insert_transaksi_gerai($data_insert);

                $this->load->library('core_banking_api');
                $total_debit 		= $get_product['harga_gerai'];
                $kode_transaksi 	= 'GERAI';
                $jenis_transaksi 	= 'TOPUP PULSA '.$get_product['nama_produk'];
                $debet_virtual_account = $this->core_banking_api->debit_virtual_account($id_user,$total_debit,$kode_transaksi,$jenis_transaksi,$trasaction_id);

                // REQUEST KE DATACELL BERHASIL. DEBET VIRTUAL ACCOUNT
                if ($debet_virtual_account['status']!=FALSE) {
                    $total_point 		= $get_product['harga_gerai']-$get_product['harga_vendor'];
                    $sumber_dana 		= 'GERAI';

                    // DEBET VIRTUAL ACCOUNT BERHASIL. DEPOSIT POINT LOYALTI
                    $share_profit = $this->core_banking_api->share_profit($id_user,$total_point,$sumber_dana,$jenis_transaksi,$trasaction_id);

                    if ($share_profit['status']!=FALSE) {

                        // DEPOSTI LOYALTI BERHASIL. INSERT TRANSAKSI GERAI
                        /*$data_insert['ref_trxid']   = $request_charge['message']['trxid'];
                        $data_insert['status'] 		= 'SUKSES';
                        $data_insert['keterangan']  = $request_charge['message']['response_message'];
                        $insert = $this->gerai_transaksi_model->insert($data_insert);*/

                        if ($insert!=FALSE) {
                            // INSERT TRANSAKSI GERAI BERHASIL

                            $this->response([
                                'status' => true,
                                'message' =>'Topup Success'
                            ], REST_Controller::HTTP_OK);

                        } else {
                            $this->response([
                                'status' => false,
                                'message' =>'Topup Gagal'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                        }

                    }   else{

                        $this->response([
                            'status' => false,
                            'message' => "Isi Pulsa Berhasil (Not Sharing Profit Payment). ".$debet_virtual_account['message']
                        ], REST_Controller::HTTP_BAD_REQUEST);
                    }


                }else{
                    $this->response([
                        'status' => false,
                        'message' => "Isi Pulsa Berhasil (Debet Failed) ".$debet_virtual_account['message']
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }


            }

    }

    function confirm_pln_pra_post(){
//@todo empty validator belum dimasukan
            $post = $this->input->post();

            $get_product = $this->get_product('PLN',$post['nominal']);
            if ($get_product==FALSE) {
                die('failed to get product');
            }else{
                $get_product = $get_product[0];
            }
            $service_user = $post['id_user'];
            $service_action = 'INSERT';
            $transaction_id 	= '11'.time();

            $data_insert = array(
                'no_transaksi' 			=> $transaction_id,
                'kode_vendor' 			=> $get_product['kode_vendor'],
                'kode_operator' 		=> $get_product['kode_operator'],
                'kode_produk' 			=> $get_product['kode_produk'],
                'kode_kategori_produk' 	=> $get_product['kode_kategori_produk'],
                'nama_produk' 			=> $get_product['nama_produk'],
                'nominal_produk' 		=> $get_product['nominal_produk'],
                'harga_vendor'			=> $get_product['harga_vendor'],
                'harga_gerai'			=> $get_product['harga_gerai'],
                'jenis_transaksi'		=> 'BELI',
                'id_user'				=> $post['id_user'],
                'msisdn'				=> $post['id_pelanggan'],
                'tanggal_transaksi'		=> date('Y-m-d H:i:s'),
                'tanggal'			=> date('d'),
                'bulan'				=> date('m'),
                'tahun'				=> date('Y'),
                'service_time'			=> date('Y-m-d H:i:s'),
                'service_user'			=> $service_user,
                'service_action'		=> $service_action
            );


            $this->load->library('vsi_api');

            $data = array(
                'service_user' 	=> $service_user,
                'produk'   		=> $get_product['kode_produk'],
                'tujuan'    	=> $post['id_pelanggan'],
                'memberreff' 	=> $transaction_id,
            );


        //@todo PLN aslinya post nama provider
            $data_report['provider_name'] 	= "PLN";
           // $data_report['provider_logo'] 	= $post['provider_logo'];
            $data_report['provider_logo'] 	= '-';
            $data_report['no_transaksi']	= $transaction_id;
            $data_report['msisdn']			= $post['id_pelanggan'];
            //$data_report['nama_pelanggan']	= $post['nama_pelanggan'];
            $data_report['nama_pelanggan']	= '-';
            $data_report['tarif_daya']		= '-';
            //$data_report['tarif_daya']		= $post['tarif_daya'];
            $data_report['token']			= NULL;
            $data_report['kwh']				= NULL;
            $data_report['kode_operator']	= $get_product['kode_operator'];
            $data_report['nominal_produk']	= $get_product['nominal_produk'];
            $data_report['harga_gerai']		= $get_product['harga_gerai'];


            $permission = $this->core_banking_api->debit_virtual_account_permission($post['id_user'],$get_product['harga_gerai']);


            if ($permission['status']==FALSE) {
                $data_report['flash_msg']        = TRUE;
                $data_report['flash_msg_type'] 	 = "danger";
                $data_report['flash_msg_status'] = FALSE;
                $data_report['flash_msg_text']   = $permission['message'];

                $data_insert['status'] 			= 'GAGAL';
                $data_insert['keterangan']  	= $permission['message'];
                $data_report['data_insert'] 	= $data_insert;

                $this->response([
                    'status' => false,
                    'message' =>'Permission denied',
                    'data' => NULL
                ], REST_Controller::HTTP_BAD_REQUEST);
            }


            $get_user_anggota 		= $this->m_gerai->get_anggota_koperasi_by_id($post['id_user']);
            if ($get_user_anggota==FALSE) {
                redirect('gerai/admin/pembayaran');
            }
            $data_report['user_anggota'] = $get_user_anggota;


            $get_user_anggota_virtual_account 		= $this->core_banking_model->get_virtual_account_by_user($post['id_user']);
            if (!$get_user_anggota_virtual_account) {
                redirect('gerai/admin/pembayaran');
            }
            $data_report['user_anggota_virtual_account']	= $get_user_anggota_virtual_account[0];



            // REQUEST KE VSI
            $request_charge = $this->vsi_api->charge($data);


            if ($request_charge==FALSE) {
                $data_report['flash_msg']        = TRUE;
                $data_report['flash_msg_type'] 	 = "danger";
                $data_report['flash_msg_status'] = FALSE;
                $data_report['flash_msg_text']   = "Transaksi Gagal.Maaf, Internal Server sedang terjadi gangguan.";

                $data_insert['status'] 			= 'GAGAL';
                $data_insert['keterangan']  	= 'Internal Server Error. API Error';
                $data_report['data_insert'] 	= $data_insert;

                $this->session->set_userdata('report',$data_report);
                $insert = $this->gerai_transaksi_model->insert($data_insert);

                $this->response([
                    'status' => false,
                    'message' =>'Transaksi Gagal.Maaf, Internal Server sedang terjadi gangguan.',
                    'data' => NULL
                ], REST_Controller::HTTP_BAD_REQUEST);


            }elseif ($request_charge['status']==FALSE) {
                $data_report['flash_msg']        = TRUE;
                $data_report['flash_msg_type'] 	 = "danger";
                $data_report['flash_msg_status'] = FALSE;
                $data_report['flash_msg_text']   = "Transaksi Gagal. ".$request_charge['message']['response_message'];

                $data_insert['ref_trxid']   = $request_charge['message']['trxid'];
                $data_insert['status'] 		= 'GAGAL';
                $data_insert['keterangan']  = $request_charge['message']['response_message'];
                $data_report['data_insert'] 	= $data_insert;

                $this->session->set_userdata('report',$data_report);
                $insert = $this->gerai_transaksi_model->insert($data_insert);

                $this->response([
                    'status' => true,
                    'message' =>"Transaksi Gagal. ".$request_charge['message']['response_message'],
                    'data' => NULL
                ], REST_Controller::HTTP_BAD_REQUEST);

            }else{

                $extract_response = explode('SN:', $request_charge['message']['pesan']);

                $extract_response = explode('/', $extract_response[1]);
                $data_report['kwh']			= $extract_response[4];
                $data_report['token']		= $request_charge['message']['token'];

                $data_insert['ref_trxid']   = $request_charge['message']['trxid'];
                $data_insert['status'] 		= 'SUKSES';
                $data_insert['keterangan']  = $request_charge['message']['response_message'].'. TOKEN:'.$request_charge['message']['token'];

                $data_report['data_insert'] = $data_insert;
                $this->session->set_userdata('report',$data_report);

                $insert = $this->m_gerai->insert_pln($data_insert);

                $this->load->library('core_banking_api');
                $id_user 			= $post['id_user'];
                $total_debit 		= $get_product['harga_gerai'];
                $kode_transaksi 	= 'GERAI';
                $jenis_transaksi 	= 'BELI TOKEN LISTRIK '.$get_product['nama_produk'];
                $debet_virtual_account = $this->core_banking_api->debit_virtual_account($id_user,$total_debit,$kode_transaksi,$jenis_transaksi,$transaction_id);

                // REQUEST KE DATACELL BERHASIL. DEBET VIRTUAL ACCOUNT
                if ($debet_virtual_account['status']!=FALSE) {
                    $total_point 		= $get_product['harga_gerai']-$get_product['harga_vendor'];
                    $sumber_dana 		= 'GERAI';

                    // DEBET VIRTUAL ACCOUNT BERHASIL. DEPOSIT POINT LOYALTI
                    $share_profit = $this->core_banking_api->share_profit($id_user,$total_point,$sumber_dana,$jenis_transaksi,$transaction_id);

                    if ($share_profit['status']!=FALSE) {

                        // DEPOSTI LOYALTI BERHASIL. INSERT TRANSAKSI GERAI

                        if ($insert!=FALSE) {
                            // INSERT TRANSAKSI GERAI BERHASIL
                            $data_report['flash_msg']			= TRUE;
                            $data_report['flash_msg_type'] 		= "success";
                            $data_report['flash_msg_status'] 	= TRUE;
                            $data_report['flash_msg_text']		= "Pembelian Token Listrik Berhasil. ".$debet_virtual_account['message'];
                            //$this->session->set_userdata('report',$data_report);
                           $data_return = array(
                               'provider_name' => $data_report['provider_name'],
                               'no_transaksi' => $data_report['no_transaksi'],
                               'msisdn' => $data_report['msisdn'],
                               'nama_pelanggan' => $data_report['nama_pelanggan'],
                               'tarif_daya' => $data_report['tarif_daya'],
                               'token' => $data_report['token'],
                               'kwh' =>  $data_report['kwh'],
                               'kode_operator' => $data_report['kode_operator'],
                               'nominal_produk' => $data_report['nominal_produk'],
                               'harga_gerai' => $data_report['harga_gerai'],
                               'status_message' => $data_report['flash_msg_text']);
                            $this->response([
                                'status' => false,
                                'message' =>"Sukses",
                               'data' => $data_return
                            ], REST_Controller::HTTP_OK);
                           // echo "berhasil insert";
                            //redirect('gerai/admin/listrik/pln_prabayar/report');

                        } else {
                            $data_report['flash_msg']        = TRUE;
                            $data_report['flash_msg_type'] 	 = "danger";
                            $data_report['flash_msg_status'] = FALSE;
                            $data_report['flash_msg_text']   = "Transaksi Gagal.";
                            //$this->session->set_userdata('report',$data_report);
                            $this->response([
                                'status' => false,
                                'message' =>"Transaksi Gagal.",
                                'data' => NULL
                            ], REST_Controller::HTTP_BAD_REQUEST);
                            //redirect('gerai/admin/listrik/pln_prabayar/report');
                        }

                    }else{
                        $data_report['flash_msg']			= TRUE;
                        $data_report['flash_msg_type'] 		= "success";
                        $data_report['flash_msg_status'] 	= TRUE;
                        $data_report['flash_msg_text']		= "Pembelian Token Listrik Berhasil (Not Sharing Profit Payment). ".$debet_virtual_account['message'];
                        $this->session->set_userdata('report',$data_report);


                        $this->response([
                            'status' => true,
                            'message' =>"Sukses, not sharing payment",
                            'data' => $data_report
                        ], REST_Controller::HTTP_OK);
                        //redirect('gerai/admin/listrik/pln_prabayar/report');

                        /*$data_report['flash_msg']        = TRUE;
                        $data_report['flash_msg_type'] 	 = "danger";
                        $data_report['flash_msg_status'] = FALSE;
                        $data_report['flash_msg_text']   = "Transaksi Gagal.".$share_profit['message'];
                        $this->session->set_userdata('report',$data_report);
                        redirect('gerai/admin/listrik/pln_prabayar/report');*/
                    }

                }else{
                    $data_report['flash_msg']        = TRUE;
                    $data_report['flash_msg_type'] 	 = "danger";
                    $data_report['flash_msg_status'] = FALSE;
                    $data_report['flash_msg_text']   = "Pembelian Token Listrik Berhasil (Debet Failed) ".$debet_virtual_account['message'];
                    $this->session->set_userdata('report',$data_report);

                    $this->response([
                        'status' => false,
                        'message' =>"Debet Gagal.",
                        'data' => NULL
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }


            }
    }

function cek_tagihan_post(){

    //@todo validasi input

   $post = $this->input->post();
    $kode_produk = $post['kode_produk'];

    $get_user_anggota 		= $this->m_gerai->get_anggota_koperasi_by_id($post['id_user']);
    $data['user_anggota']			= $get_user_anggota;
    $get_user_anggota_virtual_account 	= $this->core_banking_model->get_virtual_account_by_user($post['id_user']);
    $data['user_anggota_virtual_account']			= $get_user_anggota_virtual_account[0];

    $this->load->library('datacell_api');
    $service_user 		= $post['id_user'];
    $transaction_id 	= '12'.time();

    switch($kode_produk){
        case 'speedy':
            $data_request_inquiry = array(
                'service_user' 	=> $service_user,
                'produk'   		=> 'CEK.SPEEDY',
                'tujuan'    	=> $post['id_pelanggan'],
                'memberreff' 	=> $transaction_id,
            );
            $post['id_pelanggan'] = $post['id_pelanggan'];
            break;
        case 'telkom':
            $post['id_pelanggan'] = $post['kode_area'].$post['no_telepon'];
            $data_request_inquiry = array(
                'service_user' 	=> $service_user,
                'produk'   		=> 'CEK.TELKOM',
                'tujuan'    	=> $post['id_pelanggan'],
                'memberreff' 	=> $transaction_id,
            );
            //@todo validasi kode area

            break;
        default:
            $this->response([
                'status' => false,
                'message' =>"Kode produk salah",
                'data' => NULL
            ], REST_Controller::HTTP_BAD_REQUEST);
    }

    $get_product = $this->get_product($kode_produk);

    if ($get_product==FALSE) {
        die('failed to get product');
    }else{
        $data['product'] = $get_product[0];
        $data['product']['id_pelanggan'] 	= $post['id_pelanggan'];
    }

    // REQUEST INQUIRY TELKOM KE DATACELL
    $inquiry = $this->datacell_api->charge($data_request_inquiry);

    $data_report['provider_name'] 	= $post['provider_name'];
    $data_report['provider_logo'] 	= $post['provider_logo'];
    $data_report['kode_operator'] 	= $post['operator_id'];

    $data_report['no_transaksi']	= $transaction_id;
    $data_report['msisdn']			= $post['id_pelanggan'];
    array_key_exists('kode_area', $post) ? $data_report['kode_area'] = $post['kode_area']: NULL;
    $data_report['no_telepon'] 		= $post['no_telepon'];


    $data_report['nama_pelanggan']	= NULL;	//WARNING HARDCODE !!!!
    $data_report['biaya_tagihan'] 	= NULL; //WARNING HARDCODE !!!!
    $data_report['biaya_admin'] 	= $data['product']['harga_gerai'];
    $data_report['biaya_total'] 	= NULL; //WARNING HARDCODE !!!!

    if ($inquiry==FALSE) {
        $this->response([
            'status' => false,
            'message' =>'Mohon maaf, sementara tidak dapat melakukan transaksi, Internal Server sedang terjadi gangguan.',
            'data' => NULL
        ], REST_Controller::HTTP_BAD_REQUEST);

    }elseif ($inquiry['status']==FALSE) {

        $this->response([
            'status' => false,
            'message' =>"Terjadi kesalahan. ".$inquiry['message']['message'],
            'data' => NULL
        ], REST_Controller::HTTP_BAD_REQUEST);
    }


    // GET Nama Pelanggan
    $response = $inquiry['message']['message'];
    $extract_response = explode('a/n', $response);
    $extract_response = explode(trim($post['id_pelanggan']), $extract_response[1]);
    $data_report['nama_pelanggan']	= trim($extract_response[0]);

    // GET Biaya Tagihan
    $response = $inquiry['message']['message'];
    $extract_response = explode('sebesar', $response);
    $extract_response = explode('.', $extract_response[1]);
    $data_report['biaya_tagihan']	= trim($extract_response[0]);
    $data_report['biaya_total'] 	= $data_report['biaya_tagihan']+$data_report['biaya_admin'];


    $this->response([
        'status' => true,
        'data' => $data_report
    ], REST_Controller::HTTP_OK);
}

    function confirm_tagihan_post(){

        $post = $this->input->post();

        if (!isset($post['kode_produk']) || !isset($post['id_user'])  || !isset($post['no_handphone']) || !isset($post['biaya_tagihan'])  || !isset($post['biaya_admin'])  || !isset($post['biaya_total'])) {
            $this->response([
                'status' => true,
                'message' => 'Data tidak boleh ada yang kosong',
                'data' => NULL
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $kode_produk = $post['kode_produk'];
        switch($kode_produk){
            case 'speedy':
                $post['id_pelanggan'] = $post['id_pelanggan'];
                break;
            case 'telkom':
                $post['id_pelanggan'] = $post['kode_area'].$post['no_telepon'];
                break;
            default:
        }

        $get_product = $this->get_product($kode_produk);

        if ($get_product==FALSE) {
            die('get product failed');
        }else{
            $get_product = $get_product[0];
        }

        $service_user 	= $post['id_user'];
        $service_action = 'INSERT';
        $transaction_id 	= '12'.time();

        $data_insert = array(
            'no_transaksi' 			=> $transaction_id,
            'kode_vendor' 			=> $get_product['kode_vendor'],
            'kode_operator' 		=> $get_product['kode_operator'],
            'kode_produk' 			=> $get_product['kode_produk'],
            'kode_kategori_produk' 	=> $get_product['kode_kategori_produk'],
            'nama_produk' 			=> $get_product['nama_produk'],
            'nominal_produk' 		=> $post['biaya_tagihan'],
            'harga_vendor'			=> $post['biaya_tagihan']+$get_product['harga_vendor'],
            'harga_gerai'			=> $post['biaya_tagihan']+$get_product['harga_gerai'],
            'jenis_transaksi'		=> 'BAYAR',
            'id_user'				=> $post['id_user'],
            'msisdn'				=> $post['id_pelanggan'],
            'tanggal_transaksi'		=> date('Y-m-d H:i:s'),
            'tanggal'			=> date('d'),
            'bulan'				=> date('m'),
            'tahun'				=> date('Y'),
            'service_time'			=> date('Y-m-d H:i:s'),
            'service_user'			=> $service_user,
            'service_action'		=> $service_action
        );

        // $data_report['provider_name'] 	= $post['provider_name'];
        // $data_report['provider_logo'] 	= $post['provider_logo'];
        $data_report['no_transaksi']	= $transaction_id;
        $data_report['msisdn']			= $post['id_pelanggan'];
        $data_report['nama_pelanggan']	= $post['nama_pelanggan'];
        $data_report['biaya_tagihan'] 	= $post['biaya_tagihan'];
        $data_report['biaya_admin'] 	= $get_product['harga_gerai'];
        $data_report['biaya_total'] 	= $post['biaya_tagihan']+$get_product['harga_gerai'];
        $data_report['product'] 		= $get_product;
        $data_report['data_insert'] 	= $data_insert;

        $permission = $this->core_banking_api->debit_virtual_account_permission($service_user,$post['biaya_total']);


        if ($permission['status']==FALSE) {

            $this->response([
                'status' => false,
                'message' =>'Permission denied',
                'data' => NULL
            ], REST_Controller::HTTP_BAD_REQUEST);

        }
        $get_user_anggota 		= $this->m_gerai->get_anggota_koperasi_by_id($post['id_user']);

        if ($get_user_anggota==FALSE) {
            die('user anggota not exists');
        }
        $data_report['user_anggota'] = $get_user_anggota;
        $get_user_anggota_virtual_account 		= $this->core_banking_model->get_virtual_account_by_user($post['id_user']);

        if (!$get_user_anggota_virtual_account) {
            die('user tidak memiliki virtual account');
        }
        $data_report['user_anggota_virtual_account']	= $get_user_anggota_virtual_account[0];

        // REQUEST KE DATACELL

        $this->load->library('datacell_api');

        $data = array(
            'service_user' 	=> $service_user,
            'produk'   		=> $get_product['kode_produk'],
            'tujuan'    	=> $post['id_pelanggan'].'.'.$post['biaya_tagihan'].'.'.$post['no_handphone'],
            // 'tujuan'    	=> $post['id_pelanggan'].'.1000.'.$post['no_handphone'],
            'memberreff' 	=> $transaction_id,
        );

        $request_charge = $this->datacell_api->charge($data);

        if ($request_charge==FALSE) {
            $data_insert['status'] 			= 'GAGAL';
            $data_insert['keterangan']  	= 'Internal Server Error. API Error';
            $insert = $this->gerai_transaksi_model->insert($data_insert);

            $this->response([
                'status' => false,
                'message' =>'Transaksi Gagal.Maaf, Internal Server sedang terjadi gangguan.',
                'data' => NULL
            ], REST_Controller::HTTP_BAD_REQUEST);

        }elseif ($request_charge['status']==FALSE) {
            $data_insert['status'] 			= 'GAGAL';
            $data_insert['keterangan']  	= $request_charge['message']['message'];
            $insert = $this->m_gerai->insert_transaksi_gerai($data_insert);

            $this->response([
                'status' => false,
                'message' =>"Transaksi Gagal. ".$request_charge['message']['message'],
                'data' => NULL
            ], REST_Controller::HTTP_BAD_REQUEST);

        }else{

            $data_insert['ref_trxid']   = $request_charge['message']['trxid'];
            $data_insert['status'] 		= 'SUKSES';
            $data_insert['keterangan']  = $request_charge['message']['message'];
            $insert = $this->m_gerai->insert_transaksi_gerai($data_insert);

            $data_report['data_insert'] = $data_insert;

            $this->load->library('core_banking_api');
            $id_user 			= $this->session->userdata('id_user');
            $total_debit 		= $post['biaya_total'];
            $kode_transaksi 	= 'GERAI';
            $jenis_transaksi 	= 'BAYAR internet : '.$get_product['nama_produk'];
            $debet_virtual_account = $this->core_banking_api->debit_virtual_account($id_user,$total_debit,$kode_transaksi,$jenis_transaksi,$transaction_id);

            // REQUEST KE DATACELL BERHASIL. DEBET VIRTUAL ACCOUNT
            if ($debet_virtual_account['status']!=FALSE) {
                $total_point 		= $get_product['harga_gerai']-$get_product['harga_vendor'];
                $sumber_dana 		= 'GERAI';

                // DEBET VIRTUAL ACCOUNT BERHASIL. DEPOSIT POINT LOYALTI
                $share_profit = $this->core_banking_api->share_profit($id_user,$total_point,$sumber_dana,$jenis_transaksi,$transaction_id);

                if ($share_profit['status']!=FALSE) {
                    // DEPOSTI LOYALTI BERHASIL. INSERT TRANSAKSI GERAI

                    if ($insert!=FALSE) {
                        // INSERT TRANSAKSI GERAI BERHASIL
                        $this->response([
                            'status' => True,
                            'message' =>"Pembayaran Tagihan Berhasil. ".$debet_virtual_account['message'],
                            'data' => $data_insert
                        ], REST_Controller::HTTP_OK);

                    } else {
                        $this->response([
                            'status' => false,
                            'message' =>"Transaksi Gagal",
                            'data' => NULL
                        ], REST_Controller::HTTP_BAD_REQUEST);
                    }

                }else{
                    $this->response([
                        'status' => True,
                        'message' =>"Pembayaran Tagihan Berhasil (Not Sharing Profit Payment). ".$debet_virtual_account['message'],
                        'data' => $data_insert
                    ], REST_Controller::HTTP_OK);
                }

            }else{
                $this->response([
                    'status' => True,
                    'message' =>"Pembayaran Tagihan Berhasil (Debet Failed) ".$debet_virtual_account['message'],
                    'data' => $data_insert
                ], REST_Controller::HTTP_OK);
            }


        }


    }

    function get_product($operator_id,$nominal=NULL){

        switch($operator_id){
            case 'telkom' :
                $parameter_produk	= array(
                    'kode_operator'			=> $operator_id,
                    'kode_kategori_produk' 	=> 'TELEPON',
                    'jenis_transaksi' 		=> 'BAYAR',
                );
                break;
            case 'speedy' :
                $parameter_produk	= array(
                    'kode_operator'			=> $operator_id,
                    'kode_kategori_produk' 	=> 'INTERNET',
                    'jenis_transaksi' 		=> 'BAYAR',
                );
                break;
            case 'pln_prabayar' :
                $parameter_produk	= array(
                    'kode_operator'			=> $operator_id,
                    'kode_kategori_produk' 	=> 'LISTRIK',
                    'jenis_transaksi' 		=> 'BELI',
                );
                if ($nominal!=NULL) {
                    $parameter_produk['nominal_produk'] = $nominal;
                }
                break;
            default:
                $parameter_produk	= array(
                    'kode_operator'			=> $operator_id,
                    'kode_kategori_produk' 	=> 'PULSA',
                    'jenis_transaksi' 		=> 'BELI',
                );
                if ($nominal!=NULL) {
                    $parameter_produk['nominal_produk'] = $nominal;
                }
//                $this->response([
//                    'status' => false,
//                    'message' =>"Kode produk salah",
//                    'data' => NULL
//                ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $get_product = $this->m_gerai->get_admin_fee($parameter_produk);
        return $get_product;
    }

}
