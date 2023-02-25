<?php

class PreviewClass
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
         $builder->join('zone', 'UPPER(tbl_country.countryCode) = zone.country_code', 'LEFT');
         $builder->where('tbl_useraccount.id', $user_id);
 
         $query = $builder->get();
         $data=$query->getResultArray();
         return $data;
     }

     public function getInfo($table, $colName, $colValue) {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName,$colValue);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getIdeaInfo($table, $ques_id) {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where("question_id", $ques_id);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getIdeaDescription($table, $ques_id) {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where("question_id", $ques_id);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getPreviewIdeaInfo($ques_id,$idea_no) {
        $builder = $this->db->table('idea_tutor_ans ita');
        $builder->select('*');
        $builder->join("tbl_useraccount u","u.id = ita.tutor_id");
        $builder->where("question_id", $ques_id);
        $builder->where("idea_no", $idea_no);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getQuestionDetails($table,$question_id) {

        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where('id', $question_id);
        $query = $builder->get();
        return $query->getResultArray();
    }

}