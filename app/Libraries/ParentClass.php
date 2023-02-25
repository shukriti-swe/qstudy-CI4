<?php

class ParentClass
{
    public function __construct()
    {
        $this->db = db_connect();         
    }


    public function getInfo($table, $colName, $colValue) {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function userInfo($user_id) {
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('tbl_useraccount.*,tbl_country.id AS country_id,countryName,countryCode,zone.*');
        $builder->join('tbl_country', 'tbl_useraccount.country_id = tbl_country.id', 'LEFT');
        $builder->join('zone', 'UPPER(tbl_country.countryCode) = zone.country_code', 'LEFT');
        $builder->where('tbl_useraccount.id', $user_id);

        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function userChildInfo($parent_id){
        $builder = $this->db->table('tbl_useraccount');
		$builder->select('*');
        $builder->where('parent_id', $parent_id);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
	}

    public function updateInfo($table, $colName, $colValue, $data) {
        $builder = $this->db->table($table);
        $builder->where($colName, $colValue);
        $builder->update($data);
    }
}
