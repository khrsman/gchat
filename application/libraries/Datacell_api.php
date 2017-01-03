<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


class Datacell_api {

    protected $CI;


    var $endpoint_primary_1;
    var $endpoint_primary_2;
    var $userid;
    var $password;

    public function __construct()

    {

        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();
        $this->config();

        $this->CI->load->model('datacell_model');
    }



    private function config()

    {   
         // API CONFIG 

        $this->endpoint_primary_1 = 'http://202.152.62.2:7715/SANMADAMEGAINDONESIA.php';
        $this->endpoint_primary_2 = 'http://117.104.201.18:5515/SANMADAMEGAINDONESIA.php';
        $this->userid   = '62SMI706';
        $this->password = '160317';
    }



    private function signature($msisdn,$time){

        if (empty($msisdn)||empty($time)) {
            return FALSE;
        }else{

            /*
                a = 4 digit terakhir no. msisdn + waktu
                b = 4 digit pertama userid + password 6 digit
            */

            $a       = substr($msisdn, -4).$time;
            $b       = substr($this->userid, 0, 4).$this->password;
            $a_xor_b = $a^$b;

            $sgn     = base64_encode($a_xor_b);

            return $sgn;
        }

    }



    // Main API Loader

    private function call($perintah,$oprcode,$msisdn,$ref_trxid,$service_user){

        
        if (empty($perintah) || empty($oprcode) || empty($msisdn)  || empty($ref_trxid) || empty($service_user)  ) {
            return FALSE;
        }


        $userid     = $this->userid;
        $password   = $this->password;
        $time       = date('His');
        $sgn        = $this->signature($msisdn,$time);

        if ($sgn==FALSE) {
            return FALSE;
        }

        // Array untuk dikirim ke DATACELL
        $datacell_array = array(
            'perintah'  => $perintah,
            'oprcode'   => $oprcode,
            'userid'    => $userid,
            'time'      => $time,
            'msisdn'    => $msisdn,
            'ref_trxid' => $ref_trxid,
            'sgn'       => $sgn,
            );



        // INSERT DATACELL_ARRAY KE DATABASE UNTUK LOG
        $data_topup = $datacell_array;
        $data_topup['service_user']     = $service_user;
        $data_topup['service_time']     = date('Y-m-d H:i:s');
        $data_topup['service_action']   = 'insert';
        $insert_topup = $this->CI->datacell_model->insert_topup($data_topup);


        $xml = '<?xml version="1.0" ?><datacell>';
        $xml .= "<perintah>".$perintah."</perintah>";
        $xml .= "<oprcode>".$oprcode."</oprcode>";
        $xml .= "<userid>".$userid."</userid>";
        $xml .= "<time>".$time ."</time>";
        $xml .= "<msisdn>".$msisdn."</msisdn>";
        $xml .= "<ref_trxid>".$ref_trxid."</ref_trxid>";
        $xml .= "<sgn>".$sgn."</sgn>";
        $xml.="</datacell>";

        // set_time_limit(0);
        // return $xml;


//        $url = $this->endpoint_primary_1;
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url );
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
//        curl_setopt($ch, CURLOPT_POST, true );
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml );
//
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
//        curl_setopt($ch, CURLOPT_VERBOSE, true);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//
//        $result = curl_exec($ch);
//        $errmsg = curl_error($ch);
//        $cInfo  = curl_getinfo($ch);
//        curl_close($ch);

        #pln
        /*$result = '<?xml version="1.0"?><datacell><resultcode>0</resultcode><message>Tagihan PLN a/n JAINI 543201014757 adalah sebesar 60894.Untuk Bayar ketik: BAYAR.PLN.WEB.Pin.543201014757.NoHpPlg atau Email</message><trxid>0</trxid><ref_trxid>1086402029514</ref_trxid></datacell>';
        */

        #speedy
        $result = '<?xml version="1.0"?><datacell><resultcode>0</resultcode><message>Tagihan TELKOM 1 Bulan a/n PT.ASTEL 0213863333 adalah sebesar63360.Untuk Bayar ketik: BAYAR.TELKOM.WEB.Pin.0218672720.63360.NoHpPlg atau Email</message><trxid>0</trxid><ref_trxid>427726666</ref_trxid><datacell>';


        // KALO MAU DIAKTIFKAN TINGGAL uNCOMMENT
        //$result = NULL;

        if (empty($result)) {
            return FALSE;
        }


        $response = $this->xml_to_array($result);

        if ($response==FALSE) {
            return FALSE;
        }else{
            /*
                Struktur Response
                $response['resultcode']
                $response['message']
                $response['trxid']
                $response['ref_trxid']
            */

            // INSERT RESPONSE KE DATABASE
            $data_respon = $response;
            $data_respon['service_time'] = date('Y-m-d H:i:s');
            $insert_respon = $this->CI->datacell_model->insert_respon($data_respon);


            // UPDATE TOPUP STATUS
            $data_update_topup['ref_trxid']     = $response['ref_trxid'];
            $data_update_topup['is_pending']    = 'false';
            


            $is_error = $this->is_error_message($response['resultcode'],$response['message']);

            if ($response['resultcode']==0 && $is_error['status']==FALSE) {
                
                $data_response = array(
                    'status'    => TRUE,
                    'message'   => $response
                );

                $data_update_topup['is_success'] = 'true';
            }else{

                $response['message'] = $is_error['message'];
                $data_response = array(
                    'status'    => FALSE,
                    'message'   => $response
                );

                $data_update_topup['is_success'] = 'false';
            }
            $update_topup = $this->CI->datacell_model->update_topup($data_update_topup);

                   
            return $data_response;

        }

    }


    private function is_error_message($resultcode=NULL,$message=NULL){
        
        if ($resultcode==NULL) {
            $result = array(
                'status'    => TRUE,
                'message'   => 'No Response'
                );

            return $result;
        }

        if ($message==NULL) {
            $result = array(
                'status'    => TRUE,
                'message'   => 'No Message'
                );

            return $result;
        }

        switch ($resultcode) {
            case 0:
                $err_message = explode('.', $message);
                if (isset($err_message[0]) && $err_message[0]==="Permintaan gagal diproses") {
                    unset($err_message[0]);
                    $result = array(
                        'status'    => TRUE,
                        'message'   => implode('.', $err_message)
                        );

                    return $result;
                }

                if ($message === "Invalid Userid/IP") {
                    $result = array(
                        'status'    => TRUE,
                        'message'   => 'Invalid Userid/IP'
                        );

                    return $result;
                }

                break;
            
            case 999:
                $err_message = explode(',', $message);
                if (isset($err_message[0]) && $err_message[0]==="GAGAL") {
                    unset($err_message[0]);
                    $result = array(
                        'status'    => TRUE,
                        'message'   => implode(',', $err_message)
                        );

                    return $result;
                }else{
                    $result = array(
                        'status'    => TRUE,
                        'message'   => $message
                        );

                    return $result;
                }

                break;


            default:
            
                $result = array(
                    'status'    => FALSE,
                    'message'   => 'No Error'
                    );

                return $result;

                break;
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
                if (isset($v['value'])) {
                    $response[strtolower($v['tag'])] = $v['value'];
                }else{
                    $response[strtolower($v['tag'])] = NULL;
                }
                
            }

        }
        return $response;
    }



    function charge($data){

        $perintah = "charge";

        if (!isset($data['produk']) || !isset($data['tujuan']) || !isset($data['memberreff'])  || !isset($data['service_user'])) {
            return FALSE;
        }else{
            $oprcode        = $data['produk'];
            $msisdn         = $data['tujuan'];
            $ref_trxid      = $data['memberreff'];
            $service_user   = $data['service_user'];

            $call = $this->call($perintah,$oprcode,$msisdn,$ref_trxid,$service_user);
            if ($call==FALSE) {
                return FALSE;
            }else{
                return $call;
            }    
        }

        

    }







}