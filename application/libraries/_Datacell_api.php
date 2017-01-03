<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


class Datacell_api {

    protected $CI;


    var $endpoint;
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

        $this->endpoint = 'http://smidumay.com/datacell_server/send_response';
        // $this->endpoint = '202.152.62.2:7715/SANMADAMEGAINDONESIA.php';
        // $this->endpoint = 'http://117.104.201.18:5515/SANMADAMEGAINDONESIA.php/';
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

        $datacell_array = array_flip($datacell_array);

        $xml = new SimpleXMLElement('<datacell/>');
        array_walk_recursive($datacell_array, array ($xml, 'addChild'));

        $datacell_xml = $xml->asXML();

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


        $url = $this->endpoint;
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, true );

        // $ip_from = '202.150.213.10';
        // $ip_from = '202.150.213.2';
        // curl_setopt( $ch, CURLOPT_HTTPHEADER, array('REMOTE_ADDR: '.$ip_from, 'X_FORWARDED_FOR: '.$ip_from, 'Content-Type: text/xml'));
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );

        $result = curl_exec($ch);
        curl_close($ch);

        // return $result;

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
            $data_respon                 = $response;
            $data_respon['service_time'] = date('Y-m-d H:i:s');
            $insert_respon = $this->CI->datacell_model->insert_respon($data_respon);


            // UPDATE TOPUP STATUS
            $data_update_topup['ref_trxid']     = $response['ref_trxid'];
            $data_update_topup['is_pending']    = 'false';
            if ($response['resultcode']==0) {
                $data_update_topup['is_success'] = 'true';
            }else{
                $data_update_topup['is_success'] = 'false';
            }
            $update_topup = $this->CI->datacell_model->update_topup($data_update_topup);


            return $response;

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
                $response[strtolower($v['tag'])] = $v['value'];
            }

        }

        return $response;
    }





    function retrieve_report($xml){
        $data = $this->xml_to_array($xml);
        if ($data==FALSE) {
            return FALSE;
        }

        //insert data ke db untuk log

        /*
            [resultcode] => 0 
            [message] => SUKSES! TEL10. SN:146933008. Saldo: Rp 2879635. No: 081397382353
            [trxid] => 146933008
            [ref_trxid] => 145339124
        */

        $data_report = $data;
        $data_report['service_time'] = date('Y-m-d H:i:s');
        $insert_report = $this->CI->datacell_model->insert_report($data_report);

        return $data;

    }



    function retrieve_refund($get){

        if (!isset($get['resultcode'])) {

            return FALSE;

        }else{

            $data = array(

                'resultcode'    => $get['resultcode'],
                'msisdn'        => $get['msisdn'],
                'message'       => $get['message'],
                'trxid'         => $get['trxid'],
                'ref_trxid'     => $get['ref_trxid'],
            );



            //INSERT DATA_GET KE DATABASE UNTUK LOG
            $data_respon = $data;
            $data_respon['service_time'] = date('Y-m-d H:i:s');
            $insert_respon = $this->CI->datacell_model->insert_respon($data_respon);


            // UPDATE TOPUP STATUS
            $data_update_topup['ref_trxid']     = $data['ref_trxid'];
            $data_update_topup['is_pending']    = 'false';
            if ($data['resultcode']=='0') {
                $data_update_topup['is_success'] = 'true';
            }else{
                $data_update_topup['is_success'] = 'false';
            }
            $update_topup = $this->CI->datacell_model->update_topup($data_update_topup);

            return $data;

        }

        

    }



  

    function charge($data){

        $perintah = "charge";

        if (!isset($data['oprcode']) || !isset($data['msisdn']) || !isset($data['ref_trxid'])  || !isset($data['service_user'])) {
            return FALSE;
        }else{
            $oprcode    = $data['oprcode'];
            $msisdn     = $data['msisdn'];
            $ref_trxid  = $data['ref_trxid'];
            $service_user     = $data['service_user'];

            $call = $this->call($perintah,$oprcode,$msisdn,$ref_trxid,$service_user);
            if ($call==FALSE) {
                return FALSE;
            }else{
                return $call;
            }    
        }

        

    }







}