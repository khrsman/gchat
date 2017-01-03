<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_contact extends CI_Model
{

    public function __construct()
    {
        parent::__construct(); //inherit dari parent
    }

    function get_list($user_id,$condition,$data)
    {
        $and_where = '';
        if($condition == 'name'){
           $and_where = ' and usr.full_name like "%'.$data.'%"';
        }
        $query = $this->db->query('SELECT contact.id_user,'.
                                'usr.id_user id_friend,'.
                                'usr.username,'.
                                'contact.alias alias,'.
                                'usr.email,'.
                                'usr.full_name,'.
                                'usr.phone,'.
                                'usr.picture,'.
                                'usr.last_seen,'.
                                'usr.time_created,'.
                                'usr.status_active '.
                        'from contact '.
                        'join user_info_gchat usr on contact.id_friends = usr.id_user '.
                        'where contact.id_user ="'.$user_id.'"'.$and_where.'');

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_suggest($user_id)
    {
        $query = $this->db->query('select ' .
            'id_friends id_user, '.
            '(select username from user_info_gchat where id_user = contact.id_friends) username, '.
            '(select email from user_info_gchat where id_user = contact.id_friends) email, '.
            '(select full_name from user_info_gchat where id_user = contact.id_friends) full_name, '.
            '(select phone from user_info_gchat where id_user = contact.id_friends) phone, '.
            '(select picture from user_info_gchat where id_user = contact.id_friends) picture, '.
            '(select last_seen from user_info_gchat where id_user = contact.id_friends) last_seen, '.
            '(select time_created from user_info_gchat where id_user = contact.id_friends) time_created, '.
            '(select status_active from user_info_gchat where id_user = contact.id_friends) status_active, '.
            'count(*) common_friends_count, ' .
			'GROUP_CONCAT(usr.username) common_friends_username, ' .
			'GROUP_CONCAT(usr.id_user) common_friends_id_user, ' .
            'GROUP_CONCAT(usr.full_name) common_friends_fullname ' .
            'from contact ' .
            'join user_info_gchat usr on contact.id_user = usr.id_user ' .
            'where ' .
            'contact.id_user in (SELECT id_friends FROM contact where id_user = "' . $user_id . '") ' .
            'and id_friends not in (SELECT id_friends FROM `contact` where id_user = "' . $user_id . '") ' .
            'and id_friends <> "' . $user_id . '" ' .
            'group by id_friends ' .
            'order by common_friends_count desc, rand() ' .
            'limit 5');

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }



    function get_user_by_parameter($parameter,$data)
    {
        $where = '';
        if($parameter == 'username'){
            $where = 'where usr.username = "'.$data.'"';
        }
        if($parameter == 'phone'){
            $where = 'where usr.phone like "%'.$data.'%"';
        }
        $query = $this->db->query('SELECT '.
            'usr.id_user id_user,'.
            'usr.username,'.
            'usr.email,'.
            'usr.full_name,'.
            'usr.phone,'.
            'usr.picture,'.
            'usr.last_seen,'.
            'usr.time_created,'.
            'usr.status_active '.
            'from user_info_gchat usr '.
            ' '.$where.'');

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }


    function check_friends($id_user,$id_friends)
    {
       $this->db->select('id_user');
        $this->db->where('id_user',$id_user);
        $this->db->where('id_friends',$id_friends);
       $query =  $this->db->get('contact');

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }

    }

    function insert($data)
    {
        if($this->db->insert('contact', $data)){
            return 'Success';
        }else{
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return  'Gagal '.$error['message'];
        }
    }

    function remove_friends($id_user, $id_friends)
    {
        $this->db->where('id_user',$id_user);
        $this->db->where('id_friends',$id_friends);

        if($this->db->delete('contact')){
            return 'Success';
        }else{
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return  'Gagal : '.$error['message'];
        }

    }

    function update_block($data,$id_user,$id_friends){
        $this->db->where('id_user', $id_user);
        $this->db->where('id_friends', $id_friends);

        if($this->db->update('contact',$data)){
            return 'Success';
        }else{
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return  'Gagal '.$error['message'];
        }
    }

    function update_alias($data,$id_user,$id_friends){
        $this->db->where('id_user', $id_user);
        $this->db->where('id_friends', $id_friends);

        if($this->db->update('contact',$data)){
            return 'Success';
        }else{
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return  'Gagal :'.$error['message'];
        }
    }





}
