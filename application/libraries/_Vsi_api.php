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
        $log_vsi_topup['service_action']    = 'INSERT_MOBILE';

        $insert_topup = $this->CI->vsi_model->insert_topup($log_vsi_topup);

        $vsi_query = http_build_query($vsi_array);
        $url = $this->endpoint_primary_1;
        
        $url_query = $url.'?'.$vsi_query;


        $ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,$url_query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec ($ch);

        curl_close ($ch);

//$result = '<respon>
//					<tanggal>[TANGGAL TRANSAKSI]</tanggal>
//					<idagen>123213</idagen>
//					<refid>123213</refid>
//					<produk>[KODE PRODUK]</produk>
//					<tujuan>[ID PELANGGAN / NO HP]</tujuan>
//					<data>[DATA TRANSAKSI]</data>
//					<trxid>12333</trxid>
//					<rc>[KODE RESPON]</rc>
//					<response_code>000</response_code>
//					<response_message>[TEKS RESPON]</response_message>
//					<token>4662-7625-5203-2795-2824</token>
//<pesan>
//#32112 PLN20 ke:38919948889 SUKSES. SN:4662-7625-5203-2795-2824/TestDu""mmyPanjang8966666/R2/7000VA/35.2/1600/0/0/3891994888
//9/896666666666/0ABC2135121792B1CEF934297C5B1E7C. Sisa saldo Rp.
//59,029,550 - Rp. 18,200 = Rp. 59,011,350
//</pesan>
//				</respon>';

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
                'service_action' => 'UPDATE_MOBILE',
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







}