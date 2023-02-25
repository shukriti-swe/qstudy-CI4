<?php

class LoginClass
{
    public function __construct(){
        $this->db = db_connect();         
    }

    public function parent_pw_check_info($user_id,$password) {

        $builder = $this->db->table('tbl_useraccount');
		$builder->select('*');
		$builder->where('id',$user_id);
		$builder->where('user_pawd',md5($password));
		$query_result = $builder->get();
		$result = $query_result->getResultArray();
		return count($result);
	}
}