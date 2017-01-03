<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_user extends CI_Model
{

    public function __construct()
    {
        parent::__construct(); //inherit dari parent
    }

    function get_user_detail($user_id)
    {
        $this->db->select('id_user,username,email,full_name,phone,picture,last_seen,status,time_created,status_active');
        $this->db->where('id_user', $user_id);
        $query = $this->db->get('user_info_gchat');
        $result = $query->result_array();

       if ($query->num_rows() > 0) {
           return $result[0];
       } else {
           return FALSE;
       }
    }

   function set_picture($data,$id_user){
       $this->db->where('id_user',$id_user);
       $this->db->update('user_info_gchat',$data);
   }
    function set_name($data,$id_user){
        $this->db->where('id_user',$id_user);
        $this->db->update('user_info_gchat',$data);
    }

    function set_status($data,$id_user){
        $this->db->where('id_user',$id_user);
        $this->db->update('user_info_gchat',$data);
    }

    function update_profile($data,$id_user){
        $this->db->where('id_user', $id_user);

        if($this->db->update('user_info_gchat',$data)){
            return 'Success';
        }else{
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return  'Gagal '.$error['message'];
        }
    }

    function update_picture($data,$id_user){
        $this->db->where('id_user', $id_user);

        if($this->db->update('user_info_gchat',$data)){
            return 'Success';
        }else{
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return  'Gagal '.$error['message'];
        }
    }

}
