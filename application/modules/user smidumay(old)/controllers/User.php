<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class user extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_user');
    }

    function detail_get()
    {
        $user_id = $this->get('id');
        $user = $this->m_user->get_user_detail($user_id);

        if (empty($user)) {
            $this->response([
                'status' => FALSE,
                'message' => 'User not found'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->set_response($user, REST_Controller::HTTP_OK);
        }
    }

    public function login_post()
    {
        $username = $this->post('username');
        $password = $this->post('password');

        if (!isset($username) || empty($username) || !isset($password) || empty($password)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Username & password cannot be empty'
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $data_login = $this->m_user->get_login_data($username);
            if ($data_login == FALSE) {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Username or password do not match'
                ], REST_Controller::HTTP_BAD_REQUEST);
            } else {
                if ($data_login['password'] != sha1(md5(strrev($password)))) {
                    $this->response([
                        'status' => FALSE,
                        'message' => 'Username or password do not match'
                    ], REST_Controller::HTTP_BAD_REQUEST);
                } else {
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Login success',
                        'data' => $this->m_user->get_user_detail($data_login['id_user'])
                    ], REST_Controller::HTTP_OK);
                }
            }
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

    function register_post(){

        $this->empty_validator('Username',$this->input->post('username'));
        $this->empty_validator('Password',$this->input->post('password'));
        $this->empty_validator('Nomor KTP',$this->input->post('nomor_ktp'));
        $this->empty_validator('Nama Depan',$this->input->post('nama_depan'));
        $this->empty_validator('Nomor Anggota',$this->input->post('nomor_anggota'));
        $this->empty_validator('Tempat Lahir',$this->input->post('tempat_lahir'));
        $this->empty_validator('Tempat Lahir',$this->input->post('tanggal_lahir'));
        $this->empty_validator('Telepon',$this->input->post('telepon'));
        $this->empty_validator('Agama',$this->input->post('agama'));
        $this->empty_validator('Agama',$this->input->post('email'));

        $jenis_kelamin = $this->input->post('jenis_kelamin');
        if (($jenis_kelamin !== "L") and ($jenis_kelamin !== "P")){
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong format of Jenis Kelamin'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $id_user = "9".time();
        $id_alamat = "13".time();

        $username = $this->input->post('username');
        $pass = strrev($this->input->post('password'));
        $password = sha1(md5($pass));

        $lahir = new DateTime($this->input->post('tanggal_lahir'));
        $tgl_lahir =  $lahir->format('Y-m-d');


        $user_info = array(
            'id_user' 		=> $id_user,
            'koperasi' 		=> '',
            'komunitas' 		=> '',
            'username' 		=> $username,
            'password' 		=> $password,
            'status_active'  => "1",
            'level' 			=> "3",
            'status_validasi'=> 'N',
            'service_time' 	=> date("Y-m-d H:i:s"),
            'service_action' => "insert",
            'service_user'	=> 'GCHAT');

        $user_detail = array(
            'id_user' 		=> $id_user,
            'no_ktp' 		=> $this->input->post('nomor_ktp'),
            'nama_lengkap' 	=> $this->input->post('nama_depan')."&nbsp;".$this->input->post('nama_belakang'),
            'nama_depan' 	=> $this->input->post('nama_depan'),
            'nama_belakang' => $this->input->post('nama_belakang'),
            'no_anggota' 	=> $this->input->post('nomor_anggota'),
            'tempat_lahir' 	=> $this->input->post('tempat_lahir'),
            'tgl_lahir' 	=> $tgl_lahir,
            'alamat' 		=> $this->input->post('alamat'),
            'jabatan'		=>$this->input->post('jabatan'),
            'pekerjaan' 	=> $this->input->post('pekerjaan'),
            'telp' 			=> $this->input->post('telepon'),
            'telp2' 		=> $this->input->post('telepon2'),
            'telp3' 		=> $this->input->post('telepon3'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'golongan_darah'=> $this->input->post('golongan_darah'),
            'agama' 		=> $this->input->post('agama'),
            'npwp' 			=> $this->input->post('npwp'),
            'email' 		=> $this->input->post('email'),
            'user_ver' 		=> sha1(md5(strrev($this->input->post('pin')))),
            'pendidikan_terakhir' 	=> $this->input->post('pendidikan'),
            'jumlah_tanggungan'  	=> $this->input->post('jumlah_tanggungan'),
            'jumlah_hp_aktif' 	  	=> $this->input->post('jumlah_hp'),
            'jumlah_akun_bank' 		=> $this->input->post('jumlah_bank'),
            'jumlah_kartu_kredit' 	=> $this->input->post('jumlah_cc'),
            'jumlah_sepeda_motor' 	=> $this->input->post('jumlah_motor'),
            'jumlah_mobil' 			=> $this->input->post('jumlah_mobil'),
            'jumlah_rumah' 			=> $this->input->post('jumlah_rumah'),
            'service_time' 			=> date('Y/m/d H:i:s'),
            'service_action' 		=> 'insert',
            'service_user' 			=> 'GCHAT');


        $alamat = array('id_user' => $id_user,
            'id_alamat' =>$id_alamat,
            'pengirim_nama' => $this->input->post('nama_depan')."&nbsp;".$this->input->post('nama_belakang'),
            'pengirim_kelurahan' => $this->input->post('kelurahan'),
            'pengirim_kecamatan' => $this->input->post('kecamatan'),
            'pengirim_kabupaten' => $this->input->post('kabuptan'),
            'pengirim_provinsi' => $this->input->post('provinsi'),
            'pengirim_kode_pos' => $this->input->post('kode_pos'),
            'pengirim_no_tlp' => $this->input->post('telepon'),
            'status_default' => '1');


//        print_r($user_info);
//        print_r($user_detail);
//        print_r($alamat);

        $this->m_user->insert($user_info,$user_detail,$alamat);

        $this->response([
            'status'    => TRUE,
            'message'   => 'Insert Success',
            'data'      => $this->m_user->get_user_detail($id_user)
        ], REST_Controller::HTTP_OK);
    }




}
