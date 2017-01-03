<?php


//jfkdajskfklai efiodjaskfjisjnmkf ahrfdkjahiukndsdfjkhdafjn
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Store extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_store');
    }

    function cart_content_get(){
        $this->empty_validator('User', $this->input->get('id_user'));
        $id_user = $this->input->get('id_user');
        $cart = $this->m_store->get_cart($id_user);

        if($cart){
            $data = array();
            foreach($cart as $key => $value){
                $produk = $this->m_store->get_produk_detail($value['id_produk']);
                $data[$key]['id_cart'] = $value['id_cart'];
                $data[$key]['id_user'] = $value['id_user'];
                $data[$key]['qty'] = $value['qty'];
//             foreach($value as $keyy => $valuee ){
//                 $data[$key][$keyy] = $valuee;
//             }
                foreach($produk as $keyp => $valuep ){
                    $data[$key]['detail_produk'] = $valuep;
                }
            }

            if (empty($data)) {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Cart is empty'
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->set_response($data, REST_Controller::HTTP_OK);
            }

        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Cart is empty'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    function add_to_cart_post(){
        $this->empty_validator('User', $this->input->post('id_user'));
        $this->empty_validator('Produk', $this->input->post('id_produk'));
        $this->empty_validator('Quantity', $this->input->post('qty'));
        $this->empty_validator('Quantity', $this->input->post('price'));
        $id_user = $this->input->post('id_user');
        $id_produk = $this->input->post('id_produk');
        $qty = $this->input->post('qty');
        $price = $this->input->post('price');

        $data = array(
            'id_user' => $id_user,
            'id_produk' => $id_produk,
            'price' => $price,
            'qty' => $qty
        );

        $status = $this->m_store->insert_cart($data);
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

    function update_cart_item_post(){
        $this->empty_validator('ID Cart', $this->input->post('id_cart'));
        $this->empty_validator('User', $this->input->post('id_user'));
        $this->empty_validator('Produk', $this->input->post('id_produk'));
        $this->empty_validator('Quantity', $this->input->post('qty'));
        $this->empty_validator('Price', $this->input->post('price'));
        $id_cart = $this->input->post('id_cart');
        $id_user = $this->input->post('id_user');
        $id_produk = $this->input->post('id_produk');
        $price = $this->input->post('price');
        $qty = $this->input->post('qty');

        $data = array(
            'id_user' => $id_user,
            'id_produk' => $id_produk,
            'price' => $price,
            'qty' => $qty
        );



        $status = $this->m_store->update_cart($id_cart,$data);
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

    function remove_from_cart_post(){
        $this->empty_validator('ID Cart', $this->input->post('id_cart'));
        $id_cart = $this->input->post('id_cart');
        $status = $this->m_store->delete_cart($id_cart);
        if ($status == 'Success') {
            $this->response([
                'status' => TRUE,
                'message' => "Success",
                'data' => NULL
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => $status,
                'data' => NULL
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    function list_koperasi_get(){

        //$this->empty_validator('Kabupaten', $this->input->get('kabupaten'));
        $param_query['filter_kabupaten'] = $this->input->get('kabupaten');
        $param_query['filter_kodepos'] = $this->input->get('kodepos');

        $koperasi = $this->m_store->search_koperasi(NULL,NULL,NULL,$param_query);
//        print_r($koperasi);
//        die;
        if (empty($koperasi)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Koperasi tidak ditemukan'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
          $this->response([
              'status' => TRUE,
              'message' => '',
              'data' => $koperasi
          ], REST_Controller::HTTP_OK);

        }
    }

    function list_produk_koperasi_get(){
        $this->empty_validator('Koperasi', $this->input->get('id_koperasi'));
        $param_query['filter_koperasi'] = $this->input->get('id_koperasi');
        //$param_query['filter_kodepos'] = $this->input->get('filter_kodepos');

        $page = $this->input->get('page');;
        $limit = 10;
        $offset = ($page-1)*10;

        $produk = $this->m_store->get_produk_koperasi(NULL,$limit,$offset,$param_query);
        if (empty($produk)) {
            $this->response([
                'status' => FALSE,
                'message' => 'No Koperasi Found'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->set_response($produk, REST_Controller::HTTP_OK);
        }
    }

    function list_produk_sembako_get(){
        $this->empty_validator('Koperasi', $this->input->get('id_koperasi'));
        $param_query['filter_koperasi'] = $this->input->get('id_koperasi');
        //$param_query['filter_kodepos'] = $this->input->get('filter_kodepos');

        $produk = $this->m_store->get_produk_koperasi(NULL,NULL,NULL,$param_query);
        if (empty($produk)) {
            $this->response([
                'status' => FALSE,
                'message' => 'No Koperasi Found'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->set_response($produk, REST_Controller::HTTP_OK);
        }
    }

    function search_produk_by_name_post(){
        $this->empty_validator('Produk', $this->input->post('produk'));
        $this->empty_validator('Page', $this->input->post('produk'));
        $this->empty_validator('kategori', $this->input->post('kategori'));

        $kategori = $this->input->post('kategori');
        $page = !is_null($this->input->post('page')) && is_numeric($this->input->post('page'))? $this->input->post('page') : '1';

        $limit = 10;
        $offset = ($page-1)*10;


        $produk = $this->m_store->search_produk($this->input->post('produk'),$limit,$offset,$kategori);
        if (empty($produk)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Product not found',
                'data' => NULL
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->response([
                'status' => TRUE,
                'data' => $produk
            ], REST_Controller::HTTP_OK);
        }
    }
    function search_produk_by_kategori_post(){
        $this->empty_validator('Page', $this->input->post('produk'));
        $this->empty_validator('kategori', $this->input->post('kategori'));

        $kategori = $this->input->post('kategori');
        $page = !is_null($this->input->post('page')) && is_numeric($this->input->post('page'))? $this->input->post('page') : '1';

        $limit = 3;
        $offset = ($page-1)*3;

        $produk = $this->m_store->search_produk(NULL,$limit,$offset,$kategori);
        if (empty($produk)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Product not found',
                'data' => NULL
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->response([
                'status' => FALSE,
                'data' => $produk
            ], REST_Controller::HTTP_OK);
        }
    }

    function list_kategori_produk_get(){
        $filter = $this->input->get('filter');
        $kategori = $this->m_store->get_kategori_produk($filter);
        if (empty($kategori)) {
            $this->response([
                'status' => FALSE,
                'message' => 'No Kategori Found'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->set_response($kategori, REST_Controller::HTTP_OK);
        }
    }

    function list_produk_user_get(){
        $this->empty_validator('User', $this->input->get('id_user'));
        $param_query['filter_owner_produk'] = $this->input->get('id_user');

        $produk = $this->m_store->get_produk_koperasi(NULL,NULL,NULL,$param_query);
        if (empty($produk)) {
            $this->response([
                'status' => FALSE,
                'message' => 'No Product'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->response([
                'status' => TRUE,
                'message' => 'Success',
                'data' =>$produk
            ], REST_Controller::HTTP_OK);
        }
    }

    function add_produk_post(){

        $id_user = $this->input->post('id_user');
        $nama = $this->input->post('nama_produk');
        $deskripsi = $this->input->post('deskripsi');
        $warna = $this->input->post('warna');
        $tipe = $this->input->post('tipe');
        $kategori = $this->input->post('kategori');
        $berat = $this->input->post('berat');
        $harga_normal = $this->input->post('harga_normal');
        $harga_diskon = $this->input->post('harga_diskon');
        $jumlah_stok = $this->input->post('jumlah_stok');

        $id_produk = "2" . date('dmYHis');

        $produk_kategori = array('id_produk' => $id_produk,
            'id_kategori' => $this->input->post('kategori'));

        $produk = array(
            'id_produk' => $id_produk,
            'nama' => $nama,
            'desk' => $deskripsi,
            'warna' => $warna,
            'tipe' => $tipe,
            'berat' => $berat,
            'price_n' => $harga_normal ,
            'price_s' => $harga_diskon,
            'qty' => $jumlah_stok,
            'terjual' => '0',
            'user' => $id_user,
            'status' => 1,
            'service_time' => date("Y-m-d H:i:s"),
            'service_action' => 'insert_mobile',
            'owner' => '3',
            'service_user' => $id_user);

        if($this->m_store->add_produk($produk,$produk_kategori)){
            $this->response([
                'status' => TRUE,
                'message' => 'Success'
            ], REST_Controller::HTTP_OK);
        } else $this->response([
            'status' => FALSE,
            'message' => 'Error'
        ], REST_Controller::HTTP_BAD_REQUEST);
    }

    function detail_produk_get(){
        $this->empty_validator('Produk', $this->input->get('id_produk'));
        $id_produk = $this->input->get('id_produk');

        $produk = $this->m_store->get_produk_detail($id_produk);
        if (empty($produk)) {
            $this->response([
                'status' => FALSE,
                'message' => 'No Produk Found'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->set_response($produk, REST_Controller::HTTP_OK);
        }
    }


    public function order_get(){
        $this->empty_validator('User ID', $this->input->get('id_user'));
        $user_id = $this->input->get('id_user');
        $data = $this->m_store->get_order($user_id);

        if (!$data) {
            $this->response([
                'status' => FALSE,
                'message' => 'Failed'
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $this->response([
                'status' => TRUE,
                'message' => 'Success',
                'data' => $data
            ], REST_Controller::HTTP_OK);
        }
}
    public function detail_order_post(){
        $this->empty_validator('User ID', $this->input->post('id_user'));
        $this->empty_validator('No Transaksi', $this->input->post('no_transaksi'));
        $no_transaksi = $this->input->post('no_transaksi');
        $user_id = $this->input->post('id_user');
        $data = $this->m_store->get_order_detail($user_id,$no_transaksi);

        if (!$data) {
            $this->response([
                'status' => FALSE,
                'message' => 'Failed'
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $this->response([
                'status' => TRUE,
                'message' => 'Success',
                'data' => $data
            ], REST_Controller::HTTP_OK);
        }
    }


    function remove_produk_get(){
        $this->empty_validator('Produk', $this->input->get('id_produk'));
        $id_produk = $this->input->get('id_produk');

        $produk = $this->m_store->remove_produk($id_produk);
        if (!$produk) {
            $this->response([
                'status' => FALSE,
                'message' => 'Failed'
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $this->response([
                'status' => TRUE,
                'message' => 'Success'
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

    function check_out_post(){
                   $get_post       = $this->input->post();
                    $id_user        = $this->post('id_user');
                    $now            = date('Y-m-d H:i:s');
                    $service_action = 'insert_mobile';
                    $id_transaction = "76".time();

                    $total_harga_produk     = NULL;
                    $total_harga_produk_s   = NULL;

            $cart = $this->m_store->get_cart($id_user);
                    foreach ($cart as $k => $v) {
                        //$get_product = $this->store_product_model->get($v['id']);
                        $get_product = $this->m_store->get_produk_detail($v['id_produk']);
                        if ($get_product==FALSE) {
                            # Produk ID nya ternyata gak ada, harus gimana?
                            $data_transaksi_detail[] = NULL;
                            $data_product_update[] = NULL;
                        }else{
                            $data_transaksi_detail[] = array(
                                'no_transaksi'        => $id_transaction,
                                'id_produk'           => $v['id_produk'],
                                'harga'               => $v['price'],
                                'jumlah'              => $v['qty'],
                                'service_time_produk' => $now,
                            );
                            $data_product_update[]  = array(
                                'id_produk'     => $v['id_produk'],
                                'qty'           => $get_product[0]['qty']-$v['qty'],
                                'terjual'       => $get_product[0]['terjual']+$v['qty'],
                                'service_time'  => date('Y-m-d H:i:s'),
                                'service_action'  => 'update_mobile',
                                'service_user'  => $id_user
                            );

                            $subtotal_harga_produk  = $get_product[0]['harga_pasar']*$v['qty'];
                            $total_harga_produk     = $total_harga_produk+$subtotal_harga_produk;

                            $subtotal_harga_produk_s  = $get_product[0]['harga_member']*$v['qty'];
                            $total_harga_produk_s     = $total_harga_produk_s+$subtotal_harga_produk_s;
                        }
                    }


                    $data_transaksi = array(
                        'no_transaksi'      => $id_transaction,
                        'id_user'           => $id_user,
                        'tanggal_transaksi' => $now,
                        'total_harga'       => $total_harga_produk,
                        'tanggal'   => date('d'),
                        'bulan'     => date('m'),
                        'tahun'     => date('Y'),
                        'keterangan'        => 'Total Price_N = '.$total_harga_produk.', Total Price_S = '.$total_harga_produk_s,
                        'service_time'      => $now,
                        'service_action'    => $service_action,
                        'service_user'      => $id_user,
                    );



// INTEGRASI CORE BANKING
        $this->load->library('core_banking_api');
        $total_debit        = $total_harga_produk;
        $kode_transaksi     = 'COMMERCE';
        $jenis_transaksi    = 'COMMERCE TRANSAKSI ID : '.$id_transaction;
        $debet_virtual_account = $this->core_banking_api->debit_virtual_account($id_user,$total_debit,$kode_transaksi,$jenis_transaksi,$id_transaction);


        // DEBET VIRTUAL ACCOUNT
        if ($debet_virtual_account['status']!=FALSE) {
            $total_point = $total_harga_produk - $total_harga_produk_s;
            $sumber_dana = 'COMMERCE';


            // DEBET VIRTUAL ACCOUNT BERHASIL. DEPOSIT POINT LOYALTI
            $share_profit = $this->core_banking_api->share_profit($id_user, $total_point, $sumber_dana, $jenis_transaksi, $id_transaction);
            if ($share_profit['status']!=FALSE) {
                $insert_transaksi = $this->m_store->insert_transaksi($data_transaksi);
                if ($insert_transaksi==FALSE) {
                    $this->response([
                        'status' => FALSE,
                        'message' => 'Transaction failed: insert transaction'
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }else{

                    $insert_detail_transaksi = $this->m_store->insert_detail_batch($data_transaksi_detail);
                    if ($insert_detail_transaksi==FALSE) {
                        $this->response([
                            'status' => FALSE,
                            'message' => 'Transaction failed: insert detail transaction'
                        ], REST_Controller::HTTP_BAD_REQUEST);
                    }else{

                        # Berhasil hore..
                        // JUST CHECK UNIDENTIFIED FIELD
                        if (!isset($get_post['pengirim_kelurahan'])||empty($get_post['pengirim_kelurahan'])) {
                            $get_post['pengirim_kelurahan'] = NULL;
                        }
                        if (!isset($get_post['pengirim_kecamatan'])||empty($get_post['pengirim_kecamatan'])) {
                            $get_post['pengirim_kecamatan'] = NULL;
                        }
                        if (!isset($get_post['penerima_kelurahan'])||empty($get_post['penerima_kelurahan'])) {
                            $get_post['penerima_kelurahan'] = NULL;
                        }
                        if (!isset($get_post['penerima_kecamatan'])||empty($get_post['penerima_kecamatan'])) {
                            $get_post['penerima_kecamatan'] = NULL;
                        }


                        $data_pengiriman = array(
                            'no_transaksi'       => $id_transaction,
                            'pengirim_nama'      => $get_post['pengirim_nama'],
                            'pengirim_alamat'    => $get_post['pengirim_alamat'],
                            'pengirim_kelurahan' => $get_post['pengirim_kelurahan'],
                            'pengirim_kecamatan' => $get_post['pengirim_kecamatan'],
                            'pengirim_kabupaten' => $get_post['pengirim_kabupaten'],
                            'pengirim_provinsi'  => $get_post['pengirim_provinsi'],
                            'pengirim_kode_pos'  => $get_post['pengirim_kode_pos'],
                            'pengirim_no_tlp'    => $get_post['pengirim_no_tlp'],

                            'penerima_nama'      => $get_post['penerima_nama'],
                            'penerima_alamat'    => $get_post['penerima_alamat'],
                            'penerima_kelurahan' => $get_post['penerima_kelurahan'],
                            'penerima_kecamatan' => $get_post['penerima_kecamatan'],
                            'penerima_kabupaten' => $get_post['penerima_kabupaten'],
                            'penerima_provinsi'  => $get_post['penerima_provinsi'],
                            'penerima_kode_pos'  => $get_post['penerima_kode_pos'],
                            'penerima_no_tlp'    => $get_post['penerima_no_tlp'],
                        );

                        $insert_pengiriman = $this->m_store->insert_pengiriman($data_pengiriman);
                        if ($insert_pengiriman==FALSE) {
                            $this->response([
                                'status' => FALSE,
                                'message' => 'Transaction failed: insert sender data'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                        }else{
                            # Berhasil hore 2..
                            $update_product = $this->m_store->update_batch($data_product_update);

                            $this->response([
                                'status' => TRUE,
                                'message' => "Success . ".$debet_virtual_account['message'],
                                'data' => NULL
                            ], REST_Controller::HTTP_OK);
                        }

                    }

                }

            }else{
                // DEPOSIN LOYALTI GAGAL

                $this->response([
                    'status' => FALSE,
                    'message' => 'Transaction failed: insert sender data'.$debet_virtual_account['message']
                ], REST_Controller::HTTP_BAD_REQUEST);

            }

        }else{
            // DEBIT REKENING VIRTUAL GAGAL
            $this->response([
                'status' => FALSE,
                'message' => 'Transaction failed: insert sender data'.$debet_virtual_account['message']
            ], REST_Controller::HTTP_BAD_REQUEST);

        }




        }




}
