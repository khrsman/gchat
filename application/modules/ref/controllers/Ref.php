<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class ref extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_ref');
    }

    function pekerjaan_get()
    {
        $data = $this->m_ref->get_pekerjaan();
        $this->response([
                'status' => True,
                'message' => 'Data pekerjaan',
                'data' => $data
            ], REST_Controller::HTTP_OK);

    }

    function agama_get()
    {
        $data = $this->m_ref->get_agama();
        $this->response([
            'status' => True,
            'message' => 'Data agama',
            'data' => $data
        ], REST_Controller::HTTP_OK);

    }

    function pendidikan_get()
    {
        $data = $this->m_ref->get_pendidikan();
        $this->response([
            'status' => True,
            'message' => 'Data pendidikan',
            'data' => $data
        ], REST_Controller::HTTP_OK);

    }

    function provinsi_get()
    {
        $data = $this->m_ref->get_provinsi();
        $this->response([
            'status' => True,
            'message' => 'Data provinsi',
            'data' => $data
        ], REST_Controller::HTTP_OK);

    }

    function kabupaten_get()
    {
        $id_provinsi = $this->get('id_provinsi')  ? $this->get('id_provinsi') : '';
        $data = $this->m_ref->get_kabupaten($id_provinsi);
        $this->response([
            'status' => True,
            'message' => 'Data kabupaten',
            'data' => $data
        ], REST_Controller::HTTP_OK);

    }
    function kecamatan_get()
    {
        $id_kabupaten = $this->get('id_kabupaten')  ? $this->get('id_kabupaten') : '';
        $data = $this->m_ref->get_kecamatan($id_kabupaten);
        $this->response([
            'status' => True,
            'message' => 'Data kecamatan',
            'data' => $data
        ], REST_Controller::HTTP_OK);
    }

    function kelurahan_get()
    {
        $id_kecamatan = $this->get('id_kecamatan')  ? $this->get('id_kecamatan') : '';
        $data = $this->m_ref->get_kelurahan($id_kecamatan);
        $this->response([
            'status' => True,
            'message' => 'Data kelurahan',
            'data' => $data
        ], REST_Controller::HTTP_OK);

    }






}
