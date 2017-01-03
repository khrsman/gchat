<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_ref extends CI_Model
{

    public function __construct()
    {
        parent::__construct(); //inherit dari parent
    }


    function get_pekerjaan()
    {
        $this->db->select('id_pekerjaan id,nama deskripsi');
        $query = $this->db->get('pekerjaan');
        $result = $query->result_array();

       if ($query->num_rows() > 0) {
           return $result;
       } else {
           return FALSE;
       }
    }
    function get_agama()
    {
        $this->db->select('id_agama id,deskripsi');
        $query = $this->db->get('ref_agama');
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }
    function get_pendidikan()
    {
        $this->db->select('id_pendidikan id,deskripsi');
        $query = $this->db->get('ref_pendidikan');
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }
    function get_provinsi()
    {
        $this->db->select('id_provinsi ,nama deskripsi');
        $query = $this->db->get('ref_provinsi');
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }
    function get_kabupaten($id_provinsi)
    {
        if ($id_provinsi){
            $this->db->where('id_provinsi',$id_provinsi);
        }
        $this->db->select('id_kabupaten,nama deskripsi, id_provinsi');
        $query = $this->db->get('ref_kabupaten');
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_kecamatan($id_kabupaten)
    {
        if ($id_kabupaten){
            $this->db->where('id_kabupaten',$id_kabupaten);
        }
        $this->db->select('id_kecamatan,nama deskripsi, id_kabupaten');
        $query = $this->db->get('ref_kecamatan');
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }
    function get_kelurahan($id_kecamatan)
    {
        if ($id_kecamatan){
            $this->db->where('id_kecamatan',$id_kecamatan);
        }
        $this->db->select('id_kelurahan,nama deskripsi, id_kecamatan');
        $query = $this->db->get('ref_kelurahan');
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }








}
