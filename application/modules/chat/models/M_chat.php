<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class m_chat extends CI_Model
{

    public function __construct()
    {
        parent::__construct(); //inherit dari parent
    }


    function get($chat_id)
    {

    }

    function get_by_user($user_id)
    {
        $val = array(
            'npm' => '10000',
            'nama' => 'kaharisman',
            'kelas' => 'IF-123',
            'tanggalLahir' => '15 april'
        );
        return $val;
    }


    function insert()
    {
        $query = $this->db->get();
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }

    function edit()
    {
        $query = $this->db->get();
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }

    function remove()
    {
        $query = $this->db->get();
        $result = $query->result_array();

        if ($query->num_rows() > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }


}
