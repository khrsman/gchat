<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_aes extends CI_Model
{

    public function __construct()
    {
        parent::__construct(); //inherit dari parent
    }

    function get_user_timeline($user_id)
    {

        $query = $this->db->query('SELECT tl.id_user, info.username, info.full_name, info.picture, tl.id_timeline,tl.caption,tl.status,tl.media_type,tl.media,tl.share,tl.time_created '.
            'FROM timeline tl '.
            'join user_info_gchat info on tl.id_user = info.id_user '.
            'where tl.id_user = "'.$user_id.'"');


//        $this->db->select('id_user,id_timeline,caption,status,media_type,media,share,time_created');
//        $this->db->where('id_user', $user_id);
//        $query = $this->db->get('timeline');

       if ($query->num_rows() > 0) {
           $result = $query->result_array();
           return $result;
       } else {
           return FALSE;
       }
    }

    function get_friend_timeline($user_id)
    {
         $query = $this->db->query('SELECT tl.id_user, info.username, info.full_name, info.picture, tl.id_timeline,tl.caption,tl.status,tl.media_type,tl.media,tl.share,tl.time_created '.
             'FROM timeline tl '.
             'join user_info_gchat info on tl.id_user = info.id_user '.
                        'where tl.id_user in (select id_friends from contact where tl.id_user = "'.$user_id.'") '.
                        'and tl.share in ("PUBLIC","FRIENDS")');

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_public_timeline()
    {
        $query = $this->db->query('SELECT tl.id_user, info.username, info.full_name, info.picture, tl.id_timeline,tl.caption,tl.status,tl.media_type,tl.media,tl.share,tl.time_created '.
            'FROM timeline tl '.
            'join user_info_gchat info on tl.id_user = info.id_user '.
            'where share = "PUBLIC"');

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_timeline_comment($id_timeline)
    {
        $query = $this->db->query('SELECT info.username, info.full_name, info.picture,id_comment,id_timeline,comment,tl.time_created time '.
            'FROM timeline_comment tl '.
            'join user_info_gchat info on tl.id_user = info.id_user '.
            'where id_timeline = '."$id_timeline".'');


        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return FALSE;
        }
    }


    function insert($data)
    {
        if($this->db->insert('timeline', $data)){
            return 'Success';
        }else{
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return  'Fail : '.$error['message'];
        }
    }


    function insert_comment($data)
    {
        if($this->db->insert('timeline_comment', $data)){
            return 'Success';
        }else{
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return  'Fail : '.$error['message'];
        }

    }


    function get_timeline_with_comment($user_id, $share)
    {

        #query timeline
        $query = $this->db->query('SELECT tl.id_user, info.username, info.full_name, info.picture, tl.id_timeline,tl.caption,tl.status,tl.media_type,tl.media,tl.share,tl.time_created '.
            'FROM timeline tl '.
            'left join user_info_gchat info on tl.id_user = info.id_user '.
            'where share = "' . $share . '" and tl.id_user = ' . "$user_id" . '');
        $data_timeline = $query->result_array();


        #query comment timeline
        $data_comment = $this->db->query('SELECT tl.id_user, info.username, info.full_name, info.picture,id_comment,id_timeline,comment,tl.time_created '.
            'FROM timeline_comment tl '.
            'join user_info_gchat info on tl.id_user = info.id_user ')->result_array();
        $with_comment = array();
        $uhuy = array();

        #looping data timeline
        foreach ($data_timeline as $key => $val) {
            $content = array();
            $id_timeline = $val['id_timeline'];

            #looping data comment untuk id timeline n
            foreach ($data_comment as $k=>$v){
                if($id_timeline == $v['id_timeline']) {
                    $content[] = [
                        'id_user' => $v['id_user'],
                        'username' => $v['username'],
                        'full_name' => $v['full_name'],
                        'picture' => $v['picture'],
                        'comment' => $v['comment'],
                        'time' => $v['time_created'] ];
                }
            }
           $uhuy['response'] = $content;
            $with_comment[] = array_merge($data_timeline[$key],$uhuy);
        }


        if ($query->num_rows() > 0) {
            //$result = $query->result_array();
            return $with_comment;
        } else {
            return FALSE;
        }
    }


}
