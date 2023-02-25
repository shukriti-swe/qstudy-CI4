<?php

class SchoolClass
{
    public function __construct(){
        $this->db = db_connect();         
    }

    public function insertId($table, $data) {
        $this->db->table($table)->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
    }
    
    
    public function updateInfo($table, $colName, $colValue, $data) {
        $this->db->table($table)->where($colName, $colValue)->update($data);
    }

    public function getInfo($table, $colName, $colValue) {

        return $this->db->table($table)->select('*')->where($colName, $colValue)->get()->getResultArray();
    }

    public function userInfo($user_id) {
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('*');
        $builder->join('tbl_country','tbl_useraccount.country_id = tbl_country.id','LEFT');
        $builder->where('tbl_useraccount.id',$user_id);
        $query = $builder->get();
        return $query->getResultArray();
    }
    
    public function getTutorInfo($id)
	{
        $builder = $this->db->table('tbl_useraccount');
		$builder->select('*');
		$builder->where('tbl_useraccount.parent_id', $id);
		$query = $builder->get();
		return $query->getResultArray();
	}
}