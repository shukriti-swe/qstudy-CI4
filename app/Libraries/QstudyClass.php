<?php
use App\Models\TblSetting;

class QstudyClass
{
    public function __construct(){
        $this->db = db_connect();         
    }

    //    Module Section
    public function userInfo($user_id) 
    {
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('*');
        $builder->join('tbl_country','tbl_useraccount.country_id = tbl_country.id','LEFT');
        $builder->where('tbl_useraccount.id', $user_id);
        $query = $builder->get();
        $data=$query->getResultArray();
        
        return $data;
    }
}  