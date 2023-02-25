<?php

class UpperLevelClass
{
    public function __construct(){
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

}   