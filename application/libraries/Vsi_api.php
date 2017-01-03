<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


class Vsi_api {

    protected $CI;


    var $endpoint_primary_1;
    var $endpoint_primary_2;
    var $endpoint_backup_1;
    var $endpoint_backup_2;
    var $userid;
    var $pin;

    public function __construct()

    {

        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();
        $this->config();

        $this->CI->load->model('vsi_model');
    }



    private function config()

    {   
         // API CONFIG 
        $this->endpoint_primary_1 = 'http://103.23.20.158:1025/trx.xml';
        // $this->endpoint_primary_1 = 'http://localhost/smidumay/dashboard/vsi_server/send_response';
        $this->endpoint_primary_2 = 'http://103.43.45.110:1025/trx.xml';

        $this->endpoint_backup_1 = 'http://103.23.20.158:1026/trx.xml';
        $this->endpoint_backup_2 = 'http://103.43.45.110:1026/trx.xml';
        
        $this->userid   = 'P0288';
        $this->pin 		= '0288smi3';
    }




    // Main API Loader
    /*
		1. userid = kode / id reseller yang di dapatkan setelah terdaftar
		2. pwd = password / pin transaksi
		3. memberreff = kode transaksi / id referensi dari sisi mitra
		4. produk = kode produk yang ingin di transaksikan
		5. tujuan = nomor tujuan pengisian / pembelian
    */
    private function call($produk,$tujuan,$memberreff,$service_user,$enable_log=TRUE){

        
        if (empty($produk) || empty($tujuan) || empty($memberreff)  || empty($service_user) ) {
            return FALSE;
        }


        $userid     = $this->userid;
        $pin   		= $this->pin;

        // Array untuk dikirim ke DATACELL
        $vsi_array = array(
            'userid'  		=> $this->userid,
            'pwd'   		=> $this->pin,
            'memberreff'    => $memberreff,
            'produk'		=> $produk,
            'tujuan'    	=> $tujuan,
            );



        // INSERT LOG TOPUP
        $log_vsi_topup = $vsi_array;
        unset($log_vsi_topup['pwd']);
        $log_vsi_topup['is_pending']        = 'true';
        $log_vsi_topup['is_success']        = 'false';
        $log_vsi_topup['service_user']      = $service_user;
        $log_vsi_topup['service_time']      = date('Y-m-d H:i:s');
        $log_vsi_topup['service_action']    = 'INSERT';

        $insert_topup = $this->CI->vsi_model->insert_topup($log_vsi_topup);

        $vsi_query = http_build_query($vsi_array);
        $url = $this->endpoint_primary_1;
        
        $url_query = $url.'?'.$vsi_query;
        
        # KHUSUS UNTUK BLOCK AKSES. KALO MAU DIBUKA UNCOMMENT AJA
        /*
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url_query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec ($ch);
        curl_close ($ch);
        */
        
        # KHUSUS UNTUK BLOCK AKSES. KALO MAU DIBUKA COMMENT AJA


        $result = NULL;
        $result = ' <respon>
					<tanggal>[TANGGAL TRANSAKSI]</tanggal>
					<idagen>[ID AGEN / ID MEMBER]</idagen>
					<refid>[KODE REFERENSI]</refid>
					<produk>[KODE PRODUK]</produk>
					<tujuan>[ID PELANGGAN / NO HP]</tujuan>
					<data>[DATA TRANSAKSI]</data>
					<trxid>[ID TRANSAKSI]</trxid>
					<rc>[KODE RESPON]</rc>
					<response_code>[KODE RESPON]</response_code>
					<response_message>[TEKS RESPON]</response_message>
					<token>[TOKEN PLN]<token/>
					<pesan>[PESAN / KETERANGAN]</pesan>
				</respon>';

		// further processing ....
		if (empty($result)) {
            return FALSE;
        }


        $response = $this->xml_to_array($result);
        if ($response==FALSE) {
            return FALSE;
        }else{
            /*
                Struktur Response
                <respon>
					<tanggal>[TANGGAL TRANSAKSI]</tanggal>
					<idagen>[ID AGEN / ID MEMBER]</idagen>
					<refid>[KODE REFERENSI]</refid>
					<produk>[KODE PRODUK]</produk>
					<tujuan>[ID PELANGGAN / NO HP]</tujuan>
					<data>[DATA TRANSAKSI]</data>
					<trxid>[ID TRANSAKSI]</trxid>
					<rc>[KODE RESPON]</rc>
					<response_code>[KODE RESPON]</response_code>
					<response_message>[TEKS RESPON]</response_message>
					<token>[TOKEN PLN]<token/>
					<pesan>[PESAN / KETERANGAN]</pesan>
				</respon>
            */

             // INSERT LOG RESPON
            $log_vsi_respon = $response;
            $log_vsi_respon['memberreff']       = $memberreff;
            $log_vsi_respon['service_user']     = $service_user;
            $log_vsi_respon['service_time']     = date('Y-m-d H:i:s');
            $log_vsi_respon['service_action']   = 'INSERT';
            $log_vsi_respon['created_at']       = date('Y-m-d H:i:s');

            $insert_respon = $this->CI->vsi_model->insert_respon($log_vsi_respon);

            // UPDATE LOG TOPUP
            $log_vsi_update_topup = array(
                'is_pending'    => 'false',
                'memberreff'    => $memberreff,
                'service_user'  => $service_user,
                'service_time'  => date('Y-m-d H:i:s'),
                'service_action' => 'UPDATE',
                'created_at'    => date('Y-m-d H:i:s')
                );
            if ($response['response_code']=='0000') {
                $log_vsi_update_topup['is_success'] = 'true';
            }
            $update_topup = $this->CI->vsi_model->update_topup($log_vsi_update_topup);

            if ($response['response_code']=='0000') {
                $data_response = array(
                    'status'    => TRUE,
                    'message'   => $response
                    );
            }else{
                $data_response = array(
                    'status'    => FALSE,
                    'message'   => $response
                    );
            }
            
           
            return $data_response;

        }





    }



    private function xml_to_array($xml){

        if (!isset($xml) || empty($xml)) {
            return FALSE;
        }

        $parser = xml_parser_create();
        if (!$parser) {
            return FALSE;
        }

        xml_parse_into_struct($parser, $xml, $vals, $index);
        xml_parser_free($parser);

        foreach ($vals as $k => $v) {

            if ($v['level'] == 2) {
                if (!isset($v['value'])) {
                    $response[strtolower($v['tag'])] = NULL;
                }else{
                    $response[strtolower($v['tag'])] = $v['value'];    
                }
                
            }

        }

        return $response;
    }



  

    function charge($data){



        if (!isset($data['produk']) || !isset($data['tujuan']) || !isset($data['memberreff'])  || !isset($data['service_user'])) {
            return FALSE;
        }else{
            $produk    		= $data['produk'];
            $tujuan     	= $data['tujuan'];
            $memberreff  	= $data['memberreff'];
            $service_user	= $data['service_user'];

            $call = $this->call($produk,$tujuan,$memberreff,$service_user);
            if ($call==FALSE) {
                return FALSE;
            }else{
                return $call;
            }    
        }
        

    }


    function confirm(){



        if ($this->form_validation->run() == FALSE)
        {

            $post = $this->session->flashdata('speedy');
            $this->session->set_flashdata('speedy', $post);

            if (!isset($post)) {
                $post = $this->input->post();
            }

            if (!isset($post['submit'])) {
                redirect('gerai/pembayaran');
            }

            if (!isset($post['operator_id']) || !is_login()) {
                redirect('gerai/pembayaran');
            }

            $get_product = $this->get_product('SPEEDY');
            if ($get_product==FALSE) {
                redirect('gerai/pembayaran');
                // $data['product'] = NULL;
            }else{
                $data['product'] = $get_product[0];
                $data['product']['id_pelanggan'] 	= $post['id_pelanggan'];
            }


            // REQUEST INQUIRY speedy KE DATACELL
            $this->load->library('datacell_api');

            $service_user 		= $this->session->userdata('id_user');
            $transaction_id 	= '12'.time();

            $data_request_inquiry = array(
                'service_user' 	=> $service_user,
                'produk'   		=> 'CEK.SPEEDY',
                'tujuan'    	=> $post['id_pelanggan'],
                'memberreff' 	=> $transaction_id,
            );


            $inquiry = $this->datacell_api->charge($data_request_inquiry);

            // print_r($inquiry);

            $data_report['provider_name'] 	= $post['provider_name'];
            $data_report['provider_logo'] 	= $post['provider_logo'];
            $data_report['kode_operator'] 	= $post['operator_id'];

            $data_report['no_transaksi']	= $transaction_id;
            $data_report['msisdn']			= $post['id_pelanggan'];

            $data_report['nama_pelanggan']	= NULL;	//WARNING HARDCODE !!!!
            $data_report['biaya_tagihan'] 	= NULL; //WARNING HARDCODE !!!!
            $data_report['biaya_admin'] 	= $data['product']['harga_gerai'];
            $data_report['biaya_total'] 	= NULL; //WARNING HARDCODE !!!!

            if ($inquiry==FALSE) {
                $data_report['flash_msg']        = TRUE;
                $data_report['flash_msg_type'] 	 = "danger";
                $data_report['flash_msg_status'] = FALSE;
                $data_report['flash_msg_text']   = "Mohon maaf, sementara tidak dapat melakukan transaksi, Internal Server sedang terjadi gangguan.";

                $this->session->set_userdata('report',$data_report);
                redirect('gerai/internet/speedy/report');

            }elseif ($inquiry['status']==FALSE) {
                $data_report['flash_msg']        = TRUE;
                $data_report['flash_msg_type'] 	 = "danger";
                $data_report['flash_msg_status'] = FALSE;
                $data_report['flash_msg_text']   = "Terjadi kesalahan. ".$inquiry['message']['message'];

                $this->session->set_userdata('report',$data_report);
                redirect('gerai/internet/speedy/report');
            }


            $response = $inquiry['message']['message'];
            $extract_response = explode('a/n', $response);
            $extract_response = explode(trim($post['id_pelanggan']), $extract_response[1]);

            $data_report['nama_pelanggan']	= trim($extract_response[0]);


            $response = $inquiry['message']['message'];
            $extract_response = explode('sebesar', $response);
            $extract_response = explode('.', $extract_response[1]);

            $data_report['biaya_tagihan']	= trim($extract_response[0]);
            $data_report['biaya_total'] 	= $data_report['biaya_tagihan']+$data_report['biaya_admin'];


            $data['report']			= $data_report;
            $data['provider_name'] 	= $post['provider_name'];
            $data['provider_logo'] 	= $post['provider_logo'];

            $data['form_action'] = site_url('gerai/internet/speedy/confirm');
            $data['page'] 		 = "gerai/internet/speedy/speedy_confirm_form_view";

            $this->load->view('main_view',$data);

        }
        else
        {

            $this->session->unset_userdata('report');

            $post = $this->input->post();
            if (!isset($post['operator_id']) || !isset($post['pin'])  || !isset($post['no_handphone']) || !isset($post['biaya_tagihan'])  || !isset($post['biaya_admin'])  || !isset($post['biaya_total'])  || !is_login()) {
                redirect('gerai/pembayaran');
            }

            $get_product = $this->get_product('SPEEDY');
            if ($get_product==FALSE) {
                redirect('gerai/pembayaran');
            }else{
                $get_product = $get_product[0];
            }


            $service_user 	= $this->session->userdata('id_user');
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
                'id_user'				=> $service_user,
                'msisdn'				=> $post['id_pelanggan'],
                'tanggal_transaksi'		=> date('Y-m-d H:i:s'),
                'tanggal'			=> date('d'),
                'bulan'				=> date('m'),
                'tahun'				=> date('Y'),
                'service_time'			=> date('Y-m-d H:i:s'),
                'service_user'			=> $service_user,
                'service_action'		=> $service_action
            );



            $data_report['provider_name'] 	= $post['provider_name'];
            $data_report['provider_logo'] 	= $post['provider_logo'];
            $data_report['kode_operator'] 	= $post['operator_id'];

            $data_report['no_transaksi']	= $transaction_id;
            $data_report['msisdn']			= $post['id_pelanggan'];

            $data_report['nama_pelanggan']	= $post['nama_pelanggan'];
            $data_report['biaya_tagihan'] 	= $post['biaya_tagihan'];
            $data_report['biaya_admin'] 	= $get_product['harga_gerai'];
            $data_report['biaya_total'] 	= $post['biaya_tagihan']+$get_product['harga_gerai'];


            $permission = $this->core_banking_api->debit_virtual_account_permission($service_user,$post['biaya_total']);
            if ($permission['status']==FALSE) {
                $data_report['flash_msg']        = TRUE;
                $data_report['flash_msg_type'] 	 = "danger";
                $data_report['flash_msg_status'] = FALSE;
                $data_report['flash_msg_text']   = $permission['message'];

                $data_insert['status'] 			= 'GAGAL';
                $data_insert['keterangan']  	= $permission['message'];
                $data_report['data_insert'] 	= $data_insert;

                $this->session->set_userdata('report',$data_report);
                redirect('gerai/internet/speedy/report');
            }


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
                $data_report['flash_msg']        = TRUE;
                $data_report['flash_msg_type'] 	 = "danger";
                $data_report['flash_msg_status'] = FALSE;
                $data_report['flash_msg_text']   = "Transaksi Gagal.Maaf, Internal Server sedang terjadi gangguan.";
                $this->session->set_userdata('report',$data_report);

                $data_insert['keterangan']  = 'Internal Server Error. API Error';
                $insert = $this->gerai_transaksi_model->insert($data_insert);

                redirect('gerai/internet/speedy/report');
            }elseif ($request_charge['status']==FALSE) {
                $data_report['flash_msg']        = TRUE;
                $data_report['flash_msg_type'] 	 = "danger";
                $data_report['flash_msg_status'] = FALSE;
                $data_report['flash_msg_text']   = "Transaksi Gagal. ".$request_charge['message']['message'];
                $this->session->set_userdata('report',$data_report);

                $data_insert['ref_trxid']   = $request_charge['message']['trxid'];
                $data_insert['status'] 		= 'GAGAL';
                $data_insert['keterangan']  = $request_charge['message']['message'];
                $insert = $this->gerai_transaksi_model->insert($data_insert);
                redirect('gerai/internet/speedy/report');

            }else{

                $data_insert['ref_trxid']   = $request_charge['message']['trxid'];
                $data_insert['status'] 		= 'SUKSES';
                $data_insert['keterangan']  = $request_charge['message']['message'];
                $insert = $this->gerai_transaksi_model->insert($data_insert);


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
                            $data_report['flash_msg']			= TRUE;
                            $data_report['flash_msg_type'] 		= "success";
                            $data_report['flash_msg_status'] 	= TRUE;
                            $data_report['flash_msg_text']		= "Pembayaran Tagihan Berhasil. ".$debet_virtual_account['message'];
                            $this->session->set_userdata('report',$data_report);
                            redirect('gerai/internet/speedy/report');

                        } else {
                            $data_report['flash_msg']        = TRUE;
                            $data_report['flash_msg_type'] 	 = "danger";
                            $data_report['flash_msg_status'] = FALSE;
                            $data_report['flash_msg_text']   = "Transaksi Gagal.";
                            $this->session->set_userdata('report',$data_report);
                            redirect('gerai/internet/speedy/report');
                        }

                    }else{
                        $data_report['flash_msg']			= TRUE;
                        $data_report['flash_msg_type'] 		= "success";
                        $data_report['flash_msg_status'] 	= TRUE;
                        $data_report['flash_msg_text']		= "Pembayaran Tagihan Berhasil (Not Sharing Profit Payment). ".$debet_virtual_account['message'];
                        $this->session->set_userdata('report',$data_report);
                        redirect('gerai/internet/speedy/report');
                    }


                }else{
                    $data_report['flash_msg']        = TRUE;
                    $data_report['flash_msg_type'] 	 = "danger";
                    $data_report['flash_msg_status'] = FALSE;
                    $data_report['flash_msg_text']   = "Pembayaran Tagihan Berhasil (Debet Failed) ".$debet_virtual_account['message'];
                    $this->session->set_userdata('report',$data_report);
                    redirect('gerai/internet/speedy/report');
                }


            }

            $this->cache->clean();


        }


    }







}