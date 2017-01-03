<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_store extends CI_Model
{

    public function __construct()
    {
        parent::__construct(); //inherit dari parent
        $this->db_web_app = $this->load->database('db_koperasi', TRUE);
    }

    function search_koperasi($keyword = NULL, $limit = NULL, $offset = NULL, $param_query = NULL)
    {
        $this->db_web_app->select('
                koperasi.id_koperasi as id_koperasi,
                koperasi.nama as nama_koperasi,
                ref_kelurahan.nama as nama_kelurahan,
                ref_kecamatan.nama as nama_kecamatan,
                ref_kabupaten.nama as nama_kabupaten,
                ref_provinsi.nama as nama_provinsi,
                koperasi_alamat.alamat as alamat_koperasi,
                koperasi_alamat.kode_pos as kode_pos
            ',
            FALSE
        );
        $this->db_web_app->from('koperasi');
        $this->db_web_app->join('koperasi_alamat', 'koperasi_alamat.id_koperasi=koperasi.id_koperasi', 'LEFT');
        $this->db_web_app->join('ref_kelurahan', 'koperasi_alamat.kelurahan=ref_kelurahan.id_kelurahan', 'LEFT');
        $this->db_web_app->join('ref_kecamatan', 'koperasi_alamat.kecamatan=ref_kecamatan.id_kecamatan', 'LEFT');
        $this->db_web_app->join('ref_kabupaten', 'koperasi_alamat.kabupaten=ref_kabupaten.id_kabupaten', 'LEFT');
        $this->db_web_app->join('ref_provinsi', 'ref_kabupaten.id_provinsi=ref_provinsi.id_provinsi', 'LEFT');

        $this->db_web_app->where('koperasi.status_active', 1);

        if (!empty($param_query['filter_kabupaten'])) {
            if (is_array($param_query['filter_kabupaten'])) {
                foreach ($param_query['filter_kabupaten'] as $k => $v) {
                    $this->db_web_app->or_where('koperasi_alamat.kabupaten', $v['parameter']);
                }
            } else {
                $this->db_web_app->where('koperasi_alamat.kabupaten', $param_query['filter_kabupaten']);
            }

        }

        if (!empty($param_query['filter_kodepos'])) {
            if (is_array($param_query['filter_kodepos'])) {
                foreach ($param_query['filter_kodepos'] as $k => $v) {
                    $this->db_web_app->or_where('koperasi_alamat.kodepos', $v['parameter']);
                }
            } else {
                $this->db_web_app->where('koperasi_alamat.kode_pos', $param_query['filter_kodepos']);
            }
        }

        //  Keyword By
        if ($keyword != NULL) {
            $this->db_web_app->like('koperasi.nama', $keyword);
        }

        $this->db_web_app->limit($limit, $offset);
        $query = $this->db_web_app->get();
//        echo $this->db_web_app->last_query();
//        die;
        //$result['data']     = $query->result_array();
        //$result['count']    = $query->num_rows();
        //$result['count_all']= $this->count_question_all();
        //$result['count_all']= $this->db->query('SELECT FOUND_ROWS() as count')->row()->count;

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_kategori_produk($filter = NULL)
    {
        if ($filter == 'non_sembako') {
            $this->db->where('id_kategori <>', '5');
        } else if ($filter == 'sembako') {
            $this->db->where('id_kategori', '5');
        }
        $this->db->select('id_kategori,urutan,nama');
        $this->db->from('produk_kategori');
        $this->db->order_by('produk_kategori.urutan', 'ASC');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }


    function get_produk_koperasi($keyword = NULL, $limit = NULL, $offset = NULL, $param_query = NULL)
    {
        $this->db_web_app->select('
            produk.id_produk as id_produk,
            produk.nama,
            produk_kategori.nama as nama_kategori,
            produk_foto.foto_path as foto_produk,
            produk.price_n as harga_pasar,
            produk.price_s as harga_member
        ');
        $this->db_web_app->from('produk');
        $this->db_web_app->join('produk_kategori_relation', 'produk.id_produk=produk_kategori_relation.id_produk', 'LEFT');
        $this->db_web_app->join('produk_kategori', 'produk_kategori_relation.id_kategori=produk_kategori.id_kategori', 'LEFT');
        $this->db_web_app->join('produk_foto', 'produk_foto.id_produk=produk.id_produk', 'LEFT');
        $this->db_web_app->join('user_info', 'produk.user=user_info.id_user', 'LEFT');
        $this->db_web_app->join('user_detail', 'produk.user=user_detail.id_user', 'LEFT');
        $this->db_web_app->join('produk_admin', 'produk.id_produk=produk_admin.id_produk', 'LEFT');
        $this->db_web_app->where('produk.status', 1);

        // Filter
        /*if (!empty($param_query['filter_category'])) {
            if (is_array($param_query['filter_category'])) {
                foreach ($param_query['filter_category'] as $k => $v) {
                    $this->db->where('produk_kategori.id_kategori',$v['parameter']);
                }
            } else{
                $this->db->where('produk_kategori.id_kategori',$param_query['filter_category']);
            }

        }*/

        if (!empty($param_query['filter_produk_category'])) {
            if (is_array($param_query['filter_produk_category'])) {
                foreach ($param_query['filter_produk_category'] as $k => $v) {
                    $this->db_web_app->or_having('produk_kategori.id_kategori', $v['parameter']);
                }
            } else {
                $this->db_web_app->having('produk_kategori.id_kategori', $param_query['filter_produk_category']);
            }

        }

        if (!empty($param_query['filter_owner_produk'])) {
            if (is_array($param_query['filter_owner_produk'])) {
                foreach ($param_query['filter_owner_produk'] as $k => $v) {
                    $array_produk_owner[] = $v['parameter'];
                }
                $this->db_web_app->where_in('produk.owner', $array_produk_owner);
            } else {
                $this->db_web_app->where('produk.user', $param_query['filter_owner_produk']);
            }

        }
        if (!empty($param_query['filter_koperasi'])) {
            $this->db_web_app->where('user_info.koperasi', $param_query['filter_koperasi']);
        }
        //$this->db->or_where('produk_admin.user_target',$param_query['filter_koperasi']);

        // Keyword By
        if ($keyword != NULL) {
            $this->db_web_app->like('produk.nama', $keyword);
        }

        $this->db_web_app->limit($limit, $offset);
        // $this->db->order_by($param_query['sort'],$param_query['sort_order']);
//        $this->db_web_app->order_by('produk_kategori.urutan','ASC');
        $this->db_web_app->order_by("CASE WHEN tipe = 'Beras' THEN '1'
              WHEN tipe = 'Minyak' THEN '2'
              WHEN tipe = 'Telur' THEN '3'
              WHEN tipe = 'Terigu' THEN '4'
              ELSE tipe END", 'ASC');

        $query = $this->db_web_app->get();

        die($this->db_web_app->last_query());
//        $result['data']     = $query->result_array();
//        $result['count']    = $query->num_rows();
//        // $result['count_all']= $this->count_question_all();
//        $result['count_all']= $this->db->query('SELECT FOUND_ROWS() as count')->row()->count;


        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }

    function search_produk($keyword = NULL, $limit = NULL, $offset = NULL, $filter)
    {
        $this->db_web_app->select('
            produk.id_produk as id_produk,
            produk.nama,
            produk_kategori.nama as nama_kategori,
            produk_foto.foto_path as foto_produk,
            produk.price_n as harga_pasar,
            produk.price_s as harga_member
        ');
        $this->db_web_app->from('produk');
        $this->db_web_app->join('produk_kategori_relation', 'produk.id_produk=produk_kategori_relation.id_produk', 'LEFT');
        $this->db_web_app->join('produk_kategori', 'produk_kategori_relation.id_kategori=produk_kategori.id_kategori', 'LEFT');
        $this->db_web_app->join('produk_foto', 'produk_foto.id_produk=produk.id_produk', 'LEFT');
        $this->db_web_app->join('user_info', 'produk.user=user_info.id_user', 'LEFT');
        $this->db_web_app->join('user_detail', 'produk.user=user_detail.id_user', 'LEFT');
        $this->db_web_app->join('produk_admin', 'produk.id_produk=produk_admin.id_produk', 'LEFT');
        $this->db_web_app->where('produk.status', 1);

        // Filter
        if ($filter == 'sembako') {
            $this->db_web_app->where('produk_kategori.id_kategori', '5');
        } else if ($filter == 'non_sembako') {
            $this->db_web_app->where('produk_kategori.id_kategori <>', '5');
        } else {
            $this->db_web_app->where('produk_kategori.id_kategori ', $filter);
        }
        // Keyword By
        $this->db_web_app->like('produk.nama', $keyword);

        $this->db_web_app->limit($limit, $offset);
        $this->db_web_app->order_by('produk_kategori.urutan', 'ASC');
        $query = $this->db_web_app->get();

        //die($this->db_web_app->last_query());

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }


    function get_produk_detail($produk_id)
    {
        $this->load->helper('url');
        $this->db->select('produk.id_produk as id_produk,produk_kategori.nama as kategori_produk,foto_path foto, produk.nama as nama_produk, desk as deskripsi, warna, tipe, berat, price_n as harga_pasar, price_s as harga_member, qty, terjual');
        $this->db->from('produk');
        $this->db->join('produk_kategori_relation', 'produk.id_produk=produk_kategori_relation.id_produk', 'LEFT');
        $this->db->join('produk_kategori', 'produk_kategori_relation.id_kategori=produk_kategori.id_kategori', 'LEFT');
        $this->db->join('produk_foto', 'produk_foto.id_produk=produk.id_produk', 'LEFT');
        $this->db->join('user_info', 'produk.user=user_info.id_user', 'LEFT');
        $this->db->where('produk.id_produk', $produk_id);
        $this->db->where('produk.status', 1);
        $this->db->limit(1);

        $query = $this->db->get();

        // die($this->db->last_query());
        //$result['data']  = $query->result_array();
        //$result['count'] = $query->num_rows();

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $result[0]['foto'] = 'www.smidumay.com/dashboard/assets/images/produk/' . $result[0]['foto'];
            return $result;
        } else {
            return FALSE;
        }
    }


    function get_cart($id_user)
    {
        $this->db->select('id_cart,id_user,id_produk,qty,price');
        $this->db->from('store_cart');
        $this->db->where('id_user', $id_user);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }

    function insert_cart($data)
    {
        if ($this->db->insert('store_cart', $data)) {
            return 'Success';
        } else {
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return 'Fail : ' . $error['message'];
        }

    }

    function update_cart($id_cart, $data)
    {
        $this->db->where('id_cart', $id_cart);
        if ($this->db->update('store_cart', $data)) {
            return 'Success';
        } else {
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return 'Fail : ' . $error['message'];
        }
    }


    function remove_produk($id_produk)
    {
        $this->db->where('id_produk', $id_produk);
        if ($this->db->update('produk', array('service_action' => 'delete'))) {
            return 'Success';
        } else {
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return 'Fail : ' . $error['message'];
        }
    }


    function delete_cart($id_cart)
    {
        $this->db->where('id_cart', $id_cart);
        if ($this->db->delete('store_cart')) {
            return 'Success';
        } else {
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return 'Fail : ' . $error['message'];
        }
    }

    function insert_transaksi($data)
    {
        $this->db->insert('transaksi', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    function insert_detail_batch($data)
    {
        $this->db->insert_batch('detail_transaksi', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    function insert_pengiriman($data)
    {
        $this->db->insert('transaksi_pengiriman', $data);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }


    function update_batch($data)
    {
        $this->db->update_batch('produk', $data, 'id_produk');
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    function add_produk($produk, $produk_kategori)
    {

        //@todo add history produk
//        $produk_history = array('id_produk' => $id_produk,
//            'price_n' => $this->input->post('price_n'),
//            'price_s' => $this->input->post('price_s'),
//            'warna' => $this->input->post('warna'),
//            'tipe' => $this->input->post('tipe'),
//            'berat' => $this->input->post('berat'),
//            'service_time' => date("Y-m-d H:i:s"),
//            'service_action' => 'insert',
//            'qty' => $this->input->post('qty'),
//            'ket' => 'Management Produk',
//            'service_user' => $this->session->userdata('nama'));

        $this->db_web_app->insert('produk', $produk);
        $this->db_web_app->insert('produk_kategori_relation', $produk_kategori);
        //$this->db_web_app->insert('produk_history', $produk_history);
        return true;

    }

    public function  get_order($user_id)
    {

        $this->db->select('trx.no_transaksi ,usr.nama_lengkap, trx.tanggal_transaksi, trx.total_harga, dtl.status_terkirim');
        $this->db->from('`detail_transaksi dtl');
        $this->db->join('transaksi trx', 'trx.no_transaksi = dtl.no_transaksi');
        $this->db->join('user_detail usr', 'usr.id_user = trx.id_user');
        $this->db->where('id_produk in(select id_produk from produk where user ="' . $user_id . '")');
        $this->db->group_by('no_transaksi');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }

    }


    public function get_order_detail($user_id, $no_transaksi)
    {
        $query = 'SELECT dtl.jumlah qty, dtl.harga, prd.nama, prd.price_n, prd.price_s, prd.price_n - prd.price_s saving ' .
            'FROM `detail_transaksi` dtl ' .
            'join transaksi trx on trx.no_transaksi = dtl.no_transaksi ' .
            'join user_detail usr on usr.id_user = trx.id_user  ' .
            'join produk prd on prd.id_produk = dtl.id_produk ' .
            'where trx.no_transaksi = "' . $no_transaksi . '" ' .
            'and dtl.id_produk in(select id_produk from produk where user ="' . $user_id . '")';


        $query_pengirim = 'SELECT krm.*, ' .
            '(select nama from ref_provinsi where id_provinsi = krm.penerima_provinsi limit 1) as provinsi, ' .
            '(select nama from ref_kabupaten where id_kabupaten = krm.penerima_kabupaten limit 1) as kabupaten, ' .
            '(select nama from ref_kecamatan where id_kecamatan = krm.penerima_kecamatan limit 1) as kecamatan, ' .
            '(select nama from ref_kelurahan where id_kelurahan = krm.penerima_kelurahan limit 1) as kelurahan ' .
            'from transaksi_pengiriman krm where no_transaksi = "' . $no_transaksi . '"';


        $result = $this->db->query($query)->result_array();

        $result_pengiriman = $this->db->query($query_pengirim)->result_array()[0];


        $saving = $price_n = $price_s = $total = $qty = 0;

        foreach ($result as $key => $value) {
            $qty = $value['qty'] + $qty;
            $saving = $value['saving'] + $saving;
            $price_n = $value['price_n'] + $price_n;
            $price_s = $value['price_s'] + $price_s;
            $total = $value['price_n'] + $total;
        }

        $total = array('qty' => $qty,
            'harga_pasar' => $price_n,
            'harga_member' => $price_s,
            'total_saving' => $saving,
            'total_harga' => $price_n);

        $pengirim = array('nama' => $result_pengiriman['pengirim_nama'],
            'alamat' => $result_pengiriman['pengirim_alamat'],
            'provinsi' => $result_pengiriman['kelurahan'],
            'kota_kabupaten' => $result_pengiriman['kabupaten'],
            'kecamatan' => $result_pengiriman['kecamatan'],
            'kelurahan' => $result_pengiriman['kelurahan'],
            'kode_pos' => $result_pengiriman['pengirim_kode_pos'],
            'no_telp' => $result_pengiriman['pengirim_no_tlp']);

        $penerima = array('nama' => $result_pengiriman['penerima_nama'],
            'alamat' => $result_pengiriman['penerima_alamat'],
            'provinsi' => $result_pengiriman['kelurahan'],
            'kota_kabupaten' => $result_pengiriman['kabupaten'],
            'kecamatan' => $result_pengiriman['kecamatan'],
            'kelurahan' => $result_pengiriman['kelurahan'],
            'kode_pos' => $result_pengiriman['penerima_kode_pos'],
            'no_telp' => $result_pengiriman['penerima_no_tlp']);

        $data = array('item' => $result,
            'total' => $total,
            'pengirim' => $pengirim,
            'penerima' => $penerima);
        if ($result) {
            return $data;
        } else {
            return false;
        }

    }
}
