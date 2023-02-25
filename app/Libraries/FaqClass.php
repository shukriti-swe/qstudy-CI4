<?php

class FaqClass
{
    public function __construct(){
        $this->db = db_connect();         
    }
    public function videoSerialize($id , $tbl)
    {
        /*if the intended serial not overlapping do nothing then*/
        $builder = $this->db->table($tbl);
        $builder->select('*');
        //$builder->where('serial_num', $id);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function allFaqs()
    {
        return $this->db->table('faqs')->orderBy('serial', 'ASC')->get()->getResultArray();
    }

    public function lastItemId()
    {
        $res = $this->db->table('faqs')->select('id')->orderBy('id', 'DESC') ->limit(1)->get()->getResultArray();
        return isset($res[0])?$res[0]['id'] : 0;
    }

    public function info($conditions)
    {
        $res = $this->db->table('faqs')->where($conditions)->get()->getResultArray(); 
        return count($res) ? $res[0] : [];
    }

    public function update($conditions, $dataToUpdate)
    {
        $this->db->table('faqs')->where($conditions)->update($dataToUpdate);
    }

    public function delete($faqId)
    {
        $this->db->table('faqs')->where('id', $faqId)->delete();
    }

    public function serialize($id)
    {
        /*if the intended serial not overlapping do nothing then*/
        $builder = $this->db->table('faqs');
        $builder->select('*');
        $builder->where('serial', $id);
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function serialize_update($id, $serial_id)
    {
        $this->db->table('faqs')->where('id', $id)->update(['serial'=>$serial_id] );
    }

    public function reorderSerial($intendedSl)
    {
        /*if the intended serial not overlapping do nothing then*/
        $itemExists = $this->info(['serial'=>$intendedSl]);
        if (count($itemExists)) {
            $qry = "update faqs set serial=serial+1 where serial>=$intendedSl";
            $this->db->query($qry);
        }
    }

    public function insert($dataToInsert)
    {
        $this->db->table('faqs')->insert($dataToInsert);

        return $this->db->insertID();
    }

    public function insertTbl($dataToInsert , $tbl)
    {
        $this->db->table($tbl)->insert($dataToInsert);

        return $this->db->insertID();
    }

    public function allData($tbl)
    {
        $builder = $this->db->table($tbl);
        $builder->select('*');
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function selectData($id , $tbl)
    {
        /*if the intended serial not overlapping do nothing then*/
        $builder = $this->db->table($tbl);
        $builder->select('*');
        $builder->where('id', $id);

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function videoSerializeUpdate($id , $tbl , $serial_id)
    {
        /*if the intended serial not overlapping do nothing then*/
        $this->db->table($tbl)->where('id', $id)->update(['serial_num'=>$serial_id] );

    }

    public function videoHelpeUpdate($id , $tbl , $data)
    {
        /*if the intended serial not overlapping do nothing then*/

        $this->db->table($tbl)->where('id', $id)->update($data );

    }

    public function deleteVideo($faqId , $tbl)
    {
        $this->db->table($tbl)->where('id', $faqId)->delete();
    }

    public function roomIDTaken($whiteboar_id)
    {
        $builder = $this->db->table('tbl_available_rooms');
        $builder->select('*');
        $builder->join('tbl_useraccount', 'tbl_useraccount.whiteboar_id = tbl_available_rooms.room_id' , 'left');
        $builder->where('whiteboar_id', $whiteboar_id);
        $query = $builder->get();

        return ($query->getResultArray());
    }
}