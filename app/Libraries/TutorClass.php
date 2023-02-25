<?php

class TutorClass
{
    public function __construct(){
        $this->db = db_connect();         
    }

    public function getInfo($table, $colName, $colValue)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    //    Module Section
    public function userInfo($user_id)
    {
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('*'); 
        $builder->join('tbl_country', 'tbl_useraccount.country_id = tbl_country.id', 'LEFT');
        $builder->join('zone', 'UPPER(tbl_country.countryCode) = zone.country_code', 'LEFT');
        $builder->join('additional_tutor_info', 'tbl_useraccount.id = additional_tutor_info.tutor_id', 'LEFT');
        $builder->where('tbl_useraccount.id', $user_id);

        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function updateInfo($table, $colName, $colValue, $data)
    {
        $builder = $this->db->table($table);
        $builder->where($colName, $colValue);
        $builder->update($data);
    }

    public function tutorInfo($searchParams)
    {
        $builder = $this->db->table('tbl_useraccount');
        $builder->where($searchParams);
        $builder->join('additional_tutor_info', 'tbl_useraccount.id=additional_tutor_info.tutor_id', 'left');
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function getRow($table, $colName, $colValue)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $query = $builder->get();
        $data=$query->getRowArray();

        return $data;
    }


    public function allStudents($conditions = [])
    {
        $this->session=\Config\Services::session();
        $loggedUserId   = $this->session->get('user_id');
        $loggedUserType = $this->session->get('userType');

        if ($loggedUserType == 1) {
            // parent
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('id as `st_id`');
        $builder->where('parent_id', $loggedUserId);
        $query = $builder->get();
        $res=$query->getResultArray();    
        } elseif ($loggedUserType == 7) {
            //q-study
            /*$res = $this->db
                ->select('id st_id')
                ->where('user_type', 2) //upper level
                ->or_where('user_type', 6) // 1-12 grade (maybe)
                ->get('tbl_useraccount')
                ->result_array();*/
                $res = $this->studentBySubject($conditions);
        } else {
            // corporate/school/tutor
            $builder = $this->db->table('tbl_enrollment');
            $builder->select('st_id');
            $builder->where($conditions);
            $query = $builder->get();
            $res=$query->getResultArray(); 
        }

        return array_column($res,'st_id');
    }

    public function studentBySubject($conditions)
    {
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('tbl_useraccount.id');
        $builder->join('tbl_registered_course', 'tbl_useraccount.id = tbl_registered_course.user_id', 'LEFT');
        $builder->join('tbl_course', 'tbl_registered_course.course_id = tbl_course.id', 'LEFT');
        $builder->join('tbl_subject', 'tbl_course.courseName = tbl_subject.subject_name', 'LEFT');
        
        if (isset($conditions['subject_name'])) {
            $builder->where('tbl_course.courseName', $conditions['subject_name']);
        }
        if (isset($conditions['student_grade'])) {
            $builder->where('tbl_useraccount.student_grade', $conditions['student_grade']);
        }
        if (isset($conditions['country_id'])) {
            $builder->where('tbl_useraccount.country_id', $conditions['country_id']);
        }
        

        
        // if ($conditions['country_id'] != '') {
            // $this->db->where('tbl_useraccount.country_id', $conditions['country_id']);
        // }

        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }
    
    public function getAllInfo($table)
    {
        
        $builder = $this->db->table($table);
        $builder->select('*');
		if ($table == 'tbl_questiontype')
        {
            $builder->orderBy("order_by", "asc");
        }
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
   }

   public function getUserQuestion($table, $conditions)
    {
        $builder = $this->db->table($table);
        $builder->select('*');  
        $builder->where($conditions);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function chk_value($questionType, $user_id)
    {
      $builder = $this->db->table('tbl_question');  
      $builder->select('id, questionType');
      $builder->where('questionType', $questionType);
      $builder->where('user_id', $user_id);
      $query = $builder->get();
      $data=$query->getResultArray();

      return $data;
    }

    public function last_data($id)
    {
        $builder = $this->db->table('tbl_question'); 
        $builder->select("*");
        $builder->limit(1);
        $builder->where('user_id',$id);
        $builder->orderBy('id',"DESC");
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function get_country($user_id)
    {
        $builder = $this->db->table('tbl_useraccount'); 
        $builder->select('country_id');
        $builder->where('id',$user_id);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function insertId($table, $data)
    { 
        $this->db->table($table)->insert($data);

        $insert_id = $this->db->insertID();
        return $insert_id;
    }

    public function last_id()
    {
      
      $builder = $this->db->table('tbl_question');   
      $builder->select('id');
      $builder->orderBy("id", "desc");
      $builder->where('questionType', 14);
      $builder->limit(1); 

      $query = $builder->get();
      $data=$query->getResultArray();

      return $data;
    }

    public function ideainsertId($table, $data)
    {
        $this->db->table($table)->insert($data);

        $insert_id = $this->db->insertID();
        return $insert_id;
    }

    public function idea_des_Id($table, $data)
    {
        $this->db->table($table)->insert($data);

        $insert_id = $this->db->insertID();
        return $insert_id;
    }

    public function tutor_idea_save($table, $data)
    {
        $this->db->table($table)->insert($data);
        $insert_id = $this->db->insertID();
        
        return $insert_id;
    }

    //Question Section 
    public function getQuestionInfo($type, $question_id)
    {
        //echo 'asecee';die();
        $builder = $this->db->table('tbl_question'); 
        $builder->select('*');
        $builder->where('questionType', $type);
        $builder->where('id', $question_id);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function tutor_update($id)
    {
      $builder = $this->db->table('tbl_question');   
      $builder->select('id');
      $builder->where('id', $id);
      $query = $builder->get();
      $data=$query->getResultArray();
      return $data;
    }

    public function ideaUpdateId($table, $data, $question_id){

        $this->db->table($table)->where('question_id', $question_id)->update($data);

        $builder = $this->db->table('idea_info'); 
        $builder->select('id');
        $builder->where('question_id', $question_id);
        $query = $builder->get();
        $data=$query->getResultArray();
        // print_r($result[0]);die();
        return $data[0]['id'];

    }

    public function getIdeasByQuestion($question_id){
        $builder = $this->db->table('idea_description'); 
        $builder->select('*');
        $builder->where('question_id', $question_id);
        $query = $builder->get();
        $result = $query->getResultArray();
        return $result;
    }

    public function getIdeaInfoByQuestion($question_id){
        $builder = $this->db->table('idea_info'); 
        $builder->select('*');
        $builder->where('question_id',$question_id);
        $query = $builder->get();
        $result = $query->getResultArray();
        return $result[0];
    }

    public function tutor_edit($type, $question_id)
    {
        $builder = $this->db->table('for_tutorial_tbl_question'); 
        $builder->select('for_tutorial_tbl_question.*');
        $builder->join('tbl_question', 'for_tutorial_tbl_question.tbl_ques_id = tbl_question.id', 'LEFT');
        $builder->where('tbl_question.questionType', $type);
        $builder->where('tbl_question.id', $question_id);
        $query = $builder->get();
        $result = $query->getResultArray();
        return $result;
    }

    public function getModuleQuestion($id, $question_order_id, $status)//id=>module_id
    {
        $builder = $this->db->table('tbl_modulequestion'); 
        $builder->select('*');
        $builder->join('tbl_module', 'tbl_modulequestion.module_id = tbl_module.id', 'LEFT');
        $builder->join('tbl_moduletype', 'tbl_moduletype.id = tbl_module.moduleType', 'LEFT');
        $builder->join('tbl_question', 'tbl_question.id = tbl_modulequestion.question_id', 'LEFT');
        $builder->where('tbl_modulequestion.module_id', $id);
        
        if ($status == null) {
            $builder->where('tbl_modulequestion.question_order', $question_order_id);
        } else {
            $builder->orderBy("question_order", "asc");
        }
        
        $query = $builder->get();
        $result = $query->getResultArray();
        return $result;
    }

    public function get_all_where($select, $table, $columnName, $columnValue)
    {
        $builder = $this->db->table($table); 
        $builder->select($select);
        $builder->where($columnName, $columnValue);

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getCountryId($user_id){
        $builder = $this->db->table('tbl_useraccount'); 
        $builder->select('*');
        $builder->where('id', $user_id);
        $query = $builder->get();
        $result = $query->getResultArray();
        $country_id = $result[0]['country_id'];
        return $country_id;
    }

    public function ck_schl_corporate_exist($value)
    {
        $builder = $this->db->table('tbl_useraccount'); 
        $builder->select('*');
        $builder->whereIn('user_type', [4,5] );
        $builder->where('SCT_link', $value );
        $builder->where('whiteboar_id !=', 0);

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getInfo_Alstudent($table, $colName, $colValue)
    {
        $builder = $this->db->table($table); 
        $builder->select('st_id');
        $builder->where($colName, $colValue);

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getInfo_Alstudent_two($table, $colName, $data)
    {
        $builder = $this->db->table($table); 
        $builder->select('id , user_email');
        $builder->whereIn('id', $data);
        $builder->where('user_type', 3);
        $builder->orWhere('user_type', 6);

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getClassRoomsCk($tutor_id)
    {
        $builder = $this->db->table('tbl_classrooms');
        $builder->select('*');
        $builder->where('tutor_id', $tutor_id);
        $builder->limit(1); 

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getClassRooms()
    {
        $builder = $this->db->table('tbl_available_rooms');
        $builder->select('*');
        $builder->where('in_use', 0);
        $builder->limit(1); 

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getQuestionStore()
    {
        $builder = $this->db->table('tbl_questions_store');
        $builder->select('tbl_questions_store.*,tbl_chapter.chapterName');       
        $builder->join('tbl_subject', 'tbl_questions_store.subject = tbl_subject.subject_id', 'LEFT');
        $builder->join('tbl_chapter', 'tbl_questions_store.chapter = tbl_chapter.id', 'LEFT');
        $builder->where('tbl_subject.created_by',2);
        $query = $builder->get();
        
        return $query->getResultArray();
    }

    public function deleteInfo($table, $colName, $colValue)
    {
        $builder = $this->db->table($table);
        $builder->where($colName, $colValue);
        $builder->delete();
    }

    public function get_tutor_subject()
    {
        $builder = $this->db->table('additional_tutor_info');
        $builder->select('*');
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function uniqueColVals($tableName, $column)
    {
        $res = $this->db->table($tableName)
            ->select($column)
            ->distinct()
            ->get()
            ->getResultArray();

        return $res;
    }

    public function getIQuestionCreator($question_id){
        $builder = $this->db->table('tbl_question');
        $builder->select('tbl_useraccount.name');
        $builder->join('tbl_useraccount', 'tbl_question.user_id = tbl_useraccount.id', 'LEFT');
        $builder->where('tbl_question.id', $question_id);

        $query = $builder->get();
        return $result = $query->getResultArray();
    }
    
}    