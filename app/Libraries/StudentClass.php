<?php

class StudentClass
{
    public function __construct(){
        $this->db = db_connect();         
    }

    public function registeredCourse($user_id)
    {
          $builder = $this->db->table('tbl_registered_course');
          $builder->select('tbl_registered_course.user_id,tbl_course.*');
          $builder->join('tbl_course','tbl_course.id = tbl_registered_course.course_id');
          $builder->where('user_id',$user_id);
          $builder->where('endTime >',time());
          $builder->where('status',1);
          $query = $builder->get();
          $data=$query->getResultArray();

          return $data;
    }

    public function all_assigners_new($loggedStudentId)
    {
        $data=$this->db->query( "SELECT * FROM `tbl_useraccount` "
        . "WHERE user_type = 7 OR "
        . "id IN (SELECT sct_id FROM tbl_enrollment WHERE st_id = $loggedStudentId) AND user_type = 3")->getResultArray();

        return $data;
    }

    public function get_sct_enrollment_info($stId, $sctType)
    {
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('tbl_useraccount.*,tbl_enrollment.sct_id,tbl_enrollment.st_id');
        $builder->join('tbl_enrollment', 'tbl_useraccount.id=tbl_enrollment.sct_id');
        $builder->where('user_type', $sctType);
        if ($sctType != 3) {
            $builder->where('parent_id', null);
        }
        $builder->where('st_id', $stId);  
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }


    public function getInfo($table, $colName, $colValue)
    {
        $builder = $this->db->table($table);
        $builder->where($colName,$colValue); 
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function deleteInfo($table, $colName, $colValue)
    {
        $builder = $this->db->table($table);
        $builder->where($colName,$colValue);
        $builder->delete();
    }

    public function updateInfo($table, $colName, $colValue, $data)
    {
        $builder = $this->db->table($table);
        $builder->where($colName, $colValue);
        $builder->update($data);
    }

    public function paymentCourse($payment_id)
    {
        $builder = $this->db->table('tbl_payment_details');
        $builder->select('tbl_payment_details.id,tbl_course.*');
        $builder->join('tbl_course','tbl_course.id = tbl_payment_details.courseId');
        $builder->where('paymentId',$payment_id);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }
    
    public function payment_list_Courses($user_id)
    {
        $builder = $this->db->table('tbl_registered_course');
        $builder->select('tbl_registered_course.user_id,tbl_course.*');
        $builder->join('tbl_course','tbl_course.id = tbl_registered_course.course_id');
        $builder->where('user_id',$user_id);
        $builder->where('status',1);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function getInfo_tutor($table , $col , $st_id )
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where('st_id', $st_id);
        $builder->where('sct_type', 3);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function studentAnswerNotification($user_id){

        $builder = $this->db->table('idea_correction_report');
        $builder->where('student_id', $user_id);
        $query = $builder->get();
        $results=$query->getResultArray();

    
        $resultArray = [];
        foreach($results as $result){
            $builder = $this->db->table('tbl_module');
            $builder->select('*');
            $builder->join('tbl_modulequestion', 'tbl_module.id = tbl_modulequestion.module_id');
            // $this->db->join('tbl_moduletype', 'tbl_module.moduleType = tbl_moduletype.id');
            $builder->where('tbl_modulequestion.module_id', $result['module_id']);
            $builder->where('tbl_modulequestion.question_id', $result['question_id']);
            $query = $builder->get();
            $data=$query->getRow();
            // echo $this->db->last_query();
            $resultArray[] = $data;
        }

        return $resultArray;
    
    }

    public function getStudentIdeaInfo($user_id){
        $builder = $this->db->table('idea_correction_report');
        $builder->where('student_id', $user_id);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function get_profile_info($user_id)
    {
        $builder = $this->db->table('profile');
        $builder->where('user_id', $user_id);        
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
         $builder->where('tbl_useraccount.id', $user_id);
         $query = $builder->get();
         $data=$query->getResultArray();
         
         return $data;
     }

     public function getStudentRefLink($stId)
     {
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('tbl_useraccount.*,tbl_enrollment.sct_id,tbl_enrollment.st_id');
        $builder->join('tbl_enrollment', 'tbl_useraccount.id=tbl_enrollment.sct_id');
        $builder->where('tbl_enrollment.st_id', $stId);
        $query = $builder->get();
        $data=$query->getResultArray();
         
         return $data;
     }

     public function studentRegisterCourses($studentId,$subscription_type='')
    {
        $builder = $this->db->table('tbl_registered_course');
        $builder->join('tbl_course', 'tbl_course.id = tbl_registered_course.course_id', 'left')
        ->where('user_id', $studentId)
        ->where('status', 1);
        if($subscription_type != 'trial'){
            $builder->where('endTime >',time());
        }
        if($subscription_type != 'trial'){
            $builder->where('cost <>',0);
        }
        $query = $builder->get();
        $data=$query->getResultArray();
         
        return $data;
    }
    
    public function insertId($table, $data)
    {
        $builder = $this->db->table($table);
        $builder->insert($data);
        return $this->db->insertID();
    }

    public function tutor_like_save($data){

        $builder = $this->db->table('tutor_like_info');
        $builder->select('*');
        $builder->where('question_id', $data['question_id']);
        $builder->where('module_id', $data['module_id']);
        $builder->where('idea_id', $data['idea_id']);
        $builder->where('idea_no', $data['idea_no']);
        $builder->where('tutor_id', $data['tutor_id']);
        $builder->where('student_id', $data['student_id']);
        $query = $builder->get();
        $data=$query->getRowArray();
         
        return $data;
    }

    public function get_organizing($tbl , $user_id)
    {
        $builder = $this->db->table($tbl);
        $builder->select('sct_type');
        $builder->join('tbl_useraccount', ''.$tbl.'.sct_id = tbl_useraccount.id');
        $builder->where(''.$tbl.'.sct_type = tbl_useraccount.user_type');
        $builder->where('st_id', $user_id);
        $builder->groupBy("sct_type");
        
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function studentCourses($studentId)
    {
        $builder = $this->db->table('tbl_registered_course');
        $builder->join('tbl_course', 'tbl_course.id = tbl_registered_course.course_id', 'left');
        $builder->where('user_id', $studentId);
        $builder->where('status', 1);
        $res = $builder->get()->getResultArray();
        return $res;
    }

    public function getInfo_subjects($table, $colName, $colValue)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->whereIn($colName, $colValue);
        $builder->orderBy("tbl_subject.order", "asc");
        $query = $builder->get();
        return $query->getResultArray();
    }

     //unreleased(7-31-18)
     public function studentClass($studentId)
     {
         $res = $this->db->table('tbl_useraccount')->select('student_grade , country_id')
             ->where('id', $studentId)
             ->get()
             ->getResultArray();
 
 
         // return isset($res[0]['student_grade']) ? $res[0]['student_grade']:0;
         return $res;
     }
     //unreleased(7-31-18)

     public function all_module_by_type($tutorType, $desired_result, $registered_course, $conditions)
     {
         $builder = $this->db->table('tbl_module');
         $builder->select('tbl_module.*,tbl_subject.subject_name,tbl_chapter.chapterName,tbl_useraccount.image');
         $builder->join('tbl_subject', 'tbl_module.subject = tbl_subject.subject_id', 'LEFT');
         $builder->join('tbl_chapter', 'tbl_module.chapter = tbl_chapter.id', 'LEFT');
         $builder->join('tbl_useraccount', 'tbl_useraccount.user_type = tbl_module.user_type', 'LEFT');
         
         if (count($conditions)) {
             $builder->where($conditions);
         }
         
         $builder->whereIn('tbl_module.course_id', $registered_course);
 //        Newly Added
         if ($desired_result != '') {
             $builder->where('tbl_module.subject', $desired_result);
         }
       
         if ($tutorType == 7) {
             $builder->where('tbl_useraccount.user_type', $tutorType);
         } else {
             $builder->where('tbl_module.user_id', $tutorType);
         }
         
         // $this->db->order_by('tbl_module.exam_start', 'asc');
         $builder->orderBy('tbl_subject.order', 'ASC');
         $builder->orderBy('tbl_module.ordering', 'ASC');
       
         $query = $builder->get();
         // echo $this->db->last_query();
         return $query->getResultArray();
     }


     public function student_module_ans_info($std_id, $module_id)
     {
         $builder = $this->db->table('tbl_student_answer');
         $builder->select('*');
         $builder->where('st_id', $std_id);
         $builder->where('module_id', $module_id);
         
         $query = $builder->get();
         // echo $this->db->last_query();
         return $query->getResultArray();
     }

     public function get_answer_repeated_module($user_id, $module, $today)
     {
         $builder = $this->db->table('tbl_answer_repeated_module');
         $builder->select('*');
         $builder->where('std_id', $user_id);
         $builder->where('repeat_module_id', $module);
         $builder->where('answered_date', $today);
         
         $query = $builder->get();
         return $query->getResultArray();
     }

     
    public function get_count_std_error_ans($question_order_id, $module_id, $user_id)
    {
        $builder = $this->db->table('tbl_st_error_ans');
        $builder->select('*');       
        $builder->where('st_id', $user_id);
        $builder->where('module_id', $module_id);
        $builder->where('question_order_id', $question_order_id);
        
        $query = $builder->get();
        //        echo $this->db->last_query();
        return $query->getResultArray();
    }
    

    public function student_error_ans_info($std_id, $module_id)
    {
        $builder = $this->db->table('tbl_st_error_ans');
        $builder->select('*');
        $builder->where('st_id', $std_id);
        $builder->where('module_id', $module_id);
        
        $query = $builder->get();
        //        echo $this->db->last_query();
        return $query->getResultArray();
    }

    public function repete_date_module_index_ck($module_id, $user_id)
    {
        $builder = $this->db->table('tbl_student_repetation_day');
        $builder->where('student_id', $user_id);
        $builder->where('module_id', $module_id);
        
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function studentEverydayProgree($user_id,$tutor_id){
        $tutorModule = $this->db->table('tbl_module')->select('id')
                    ->where('user_id', $tutor_id)
                    ->get()
                    ->getResultArray();
        // echo '<pre>';
        // print_r($tutorModule);die();  
            
        $tutorModule = array_column($tutorModule, 'id');
        
        $res = $this->db->table('tbl_studentprogress')->where('student_id',$user_id)->where('date_time',date('Y-m-d'))->whereIn('module', $tutorModule)->countAllResults();

        
        return $res;
        
    }

    public function getTutorialAnsInfo($table_name, $module_id, $std_id)
    {
        $builder = $this->db->table($table_name);
        $builder->select('*');
        $builder->where('st_id', $std_id);
        $builder->where('module_id', $module_id);
        
        $query = $builder->get();
        //        echo $this->db->last_query();
        return $query->getResultArray();
    }

    public function get_specific_error_ans($question_id, $question_order_id, $module_id, $user_id)
    {
        $builder = $this->db->table('tbl_st_error_ans');
        $builder->select('*');
        $builder->where('st_id', $user_id);
        $builder->where('module_id', $module_id);
        $builder->where('question_id', $question_id);
        $builder->where('question_order_id', $question_order_id);
        
        $query = $builder->get();
        //        echo $this->db->last_query();
        return $query->getResultArray();
    }

    public function get_user_informations($user_id)
    {
        $builder = $this->db->table('tbl_useraccount');
        $builder->where('id', $user_id);  
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function get_all_where_two($select, $table, $columnName, $columnValue , $std_id)
    {
        $builder = $this->db->table($table);
        $builder->select($select);
        $builder->where($columnName, $columnValue);
        $builder->where('st_id', $std_id);

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function update_tmp_module_tbl($table, $colName, $colValue, $colName_two, $colValue_two, $data)
    {
        $builder = $this->db->table($table);
        $builder->where($colName, $colValue);
        $builder->where($colName_two,$colValue_two);
        $builder->update($data);
    }

    public function insertInfo($table, $data)
    {
        $this->db->table($table)->insert($data);
    }

    public function delete_st_error_ans($question_order_id, $module_id, $user_id)
    {
        $builder = $this->db->table('tbl_st_error_ans');
        $builder->where('st_id', $user_id);
        $builder->where('module_id', $module_id);
        $builder->where('question_order_id', $question_order_id);  
        $builder->delete();
    }

    public function update_st_error_ans($question_order_id, $module_id, $user_id)
    {
        $builder = $this->db->table('tbl_st_error_ans');
        $builder->set('error_count', 'error_count+1', false);

        $builder->where('st_id', $user_id);
        $builder->where('module_id', $module_id);
        $builder->where('question_order_id', $question_order_id);
        
        $builder->update();
        //        echo $this->db->last_query();
    }

    public function getTutorialAnsInfo_($table_name, $module_id, $std_id)
    {
        $builder = $this->db->table($table_name);
        $builder->select('*');
        $builder->where('st_id', $std_id);
        $builder->where('module_id', $module_id);
        $builder->groupBy('question_order_id');
        
        $query = $builder->get();
        //        echo $this->db->last_query();
        return $query->getResultArray();
    }


    public function getTutorialAnsInfo_delete($data, $module_id, $std_id)
    {
        $builder = $this->db->table('tbl_st_error_ans');
        $builder->where('st_id', $std_id);
        $builder->where('module_id', $module_id);
        $builder->delete();

        $this->db->insertBatch('tbl_st_error_ans', $data);

    }


    public function getWhereThreewoCondition($table, $colName1, $colValue1, $colName2,  $colValue2 , $colName3, $colValue3 )
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName1, $colValue1);
        $builder->where($colName2, $colValue2);
        $builder->where($colName3, $colValue3);
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getSmsApiKeySettings()
    {
        $builder = $this->db->table('tbl_setting');
        $builder->select('*');
        $builder->where('setting_type', 'sms_api_settings');
        $query_result = $builder->get();
        return $query_result->getResultArray();
    }

    public function get_sms_response_after_module()
    {
        $builder=$this->db->table('tbl_setting');
        $builder->select('*');
        $builder->where('setting_type', 'sms_message_settings');
        $builder->where('setting_key', 'send_sms_after_module_ans');

        $query_result = $builder->get();
        return $query_result->getResultArray();
    }

    public function studentProgressStd($conditions,$module_user_type='',$course_id ='')
    {
        $tutorType  = $_SESSION['userType'];
        $userId  = $_SESSION['user_id'];
        $tutorModule = $this->db->table('tbl_module')->select('id')->where('user_id', $userId)->get()
            ->getResultArray();
         $tutorModule = array_column($tutorModule, 'id');

         if ($module_user_type != '')
         {
             $student_module = $this->db->table('tbl_module')
                 ->select('id')
                 ->where('user_type ', $module_user_type)
                 ->get()
                 ->getResultArray();
             $student_module = array_column($student_module, 'id');
         }
         if ($course_id != '')
         {
             $student_module = $this->db->table('tbl_module')
                 ->select('id')
                 ->where('course_id ',$course_id)
                 ->get()
                 ->getResultArray();
             $student_module = array_column($student_module, 'id');
         }

        $res = $this->db->table('tbl_studentprogress')
            ->where($conditions);
            //->where("module in (SELECT * from tbl_module where user_id = $userId)");
        if ($tutorType==7 ||$tutorType==3) {
            $res = $res->whereIn('module', $tutorModule);
        }
        if ($tutorType==6 && !empty($student_module)) {
            $res = $res->whereIn('module', $student_module);
        }
            $res= $res->orderBy('answerTime', 'ASC')
            ->get()
            ->getResultArray();
        return $res;
    }

    public function get_student_ideas($question_id,$modle_id)
    {
        $builder=$this->db->table('idea_student_ans');
        $builder->where('module_id', $modle_id);
        $builder->where('question_id', $question_id);
        
        $query = $builder->get();
        return $query->getResultArray();

    }

    public function get_tutor_ideas($question_id,$modle_id)
    {
      
        $builder=$this->db->table('idea_student_ans');
        //$this->db->where('module_id', $modle_id);
        $builder->where('question_id', $question_id);
        
        $query = $builder->get();
        // echo $this->db->last_query();
        // die();
        return  $query->getResultArray();

    }

    public function getQuestionMark($question_id)
    {
        $builder=$this->db->table('tbl_question');
        $builder->where('id', $question_id);
        
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function checktutorIdeaAns($module_id,$question_id,$idea_id,$user_id)
    {
        $builder=$this->db->table('idea_tutor_ans');
        $builder->where('module_id', $module_id);
        $builder->where('question_id', $question_id);
        $builder->where('idea_id', $idea_id);
        $builder->where('tutor_id', $user_id);
        
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function checkIdeaAns($module_id,$question_id,$idea_id,$user_id)
    {
        $builder=$this->db->table('idea_student_ans');
        $builder->where('module_id', $module_id);
        $builder->where('question_id', $question_id);
        $builder->where('idea_id', $idea_id);
        $builder->where('student_id', $user_id);
        
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getIdeaAnsId($user_type,$data)
    {
        if($user_type==3){
            $this->db->table('idea_tutor_ans')->insert($data);
            $insert_id = $this->db->insertID();
            return $insert_id;
        }else{

        $this->db->table('idea_student_ans')->insert($data);
        $insert_id = $this->db->insertID();
        return $insert_id;
        
        }
    }

    public function where_in($array , $tbl , $select )
    {
        $builder=$this->db->table($tbl);
        $builder->select($select);
        $builder->whereIn('id', $array);

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function get_student_progress($user_id, $module)
    {
        $builder=$this->db->table('tbl_studentprogress');
        $builder->select('*');
        $builder->where('student_id', $user_id);
        $builder->where('module', $module);
        
        $query = $builder->get();
        return $query->getResultArray();
    }
    
    public function get_today_dialogue($today_date)
    {
        $builder=$this->db->table('dialogue');
        $builder->select('*');
        $builder->where('date_to_show', $today_date);

        $query_result = $builder->get();
        return $query_result->getResultArray();
    }

    public function get_auto_repeat_dialogue()
    {
        $date = date('m-d-y');
        $getLogId = $this->getLogId($date);
        if (!empty($getLogId))
        {
            $id = $getLogId[0]['dia_Id'];
            $dialogue = $this->getAutoRepeatData($id);
            return $dialogue;

        }else{

            $getLogId = $getLogId = $this->getLogId();
            $id = $getLogId[0]['dia_Id'];

            $dialogue = $this->getAutoRepeatData($id,$not_equal=$id);
            if (!empty($dialogue))
            {
                $id = $dialogue[0]['id'];
                $data['dia_Id']=$id;
                $data['seen_date']=$date;
                $result = $this->db->table('tbl_auto_repeat_log')->where('id',1)->update($data);
                return $dialogue;
            }else
            {
                $dialogue = $this->getAutoRepeatData();
                $id = $dialogue[0]['id'];
                $data['dia_Id']=$id;
                $data['seen_date']=$date;
                $result = $this->db->table('tbl_auto_repeat_log')->where('id',1)->update($data);
                return $dialogue;
            }
        }
    }

    public function getLogId($date ='')
    {
        $builder=$this->db->table('tbl_auto_repeat_log');
        $builder->select('*');
        if ($date != '')
        {
            $builder->where('seen_date',$date);
        }
        $query_result = $builder->get();
        return $query_result->getResultArray();
    }


    public function getAutoRepeatData($id='',$not_equal='')
    {
        $builder=$this->db->table('dialogue');
        $builder->select('*');
        if ($not_equal != '')
        {
            $builder->where('id >',$id);
            $builder->where('auto_repeat',1);
        }
        if ($id != '' && $not_equal ==''){
            $builder->where('id',$id);
        }
        $builder->orderBy("id", "asc");
        $builder->limit(1);
        $query_result = $builder->get();
        return $query_result->getResultArray();
    }

    
    public function deleteInfo_mod_ques_2($user_id, $module_id)
    {
        $builder=$this->db->table('tbl_temp_tutorial_mod_ques_two');
        $builder->where('st_id', $user_id);
        $builder->where("module_id", $module_id);
        $builder->delete();
    }

    public function delete_all_st_error_ans($module_id,$user_id)
    {
        $builder=$this->db->table('tbl_st_error_ans');
        $builder->where('st_id', $user_id);
        $builder->where('module_id', $module_id);
        $builder->delete();
    }

    public function studentSubjects($studentId)
    {
        $temp = $this->studentCourses($studentId);
//        $studentCourses = array_column($temp, 'courseName');
        $studentCourses = array_column($temp, 'id');
        if (!count($studentCourses)) {
            return [];
        }


        $res = $this->db->table('tbl_module')
        ->join('tbl_subject', 'tbl_module.subject = tbl_subject.subject_id', 'LEFT')//Newly Added
        ->whereIn('tbl_module.course_id', $studentCourses)
        ->get()
//        ->where_in('tbl_subject.subject_name', $studentCourses)
//        ->get('tbl_subject')
        ->getResultArray();
       
        return $res;
    }

    public function getQuestionStore($condition)
    {
        $builder=$this->db->table('tbl_questions_store');
        $builder->select('*');
        $builder->where($condition);
        $builder->orderBy('pdf_order', 'ASC');
        $query = $builder->get();
        return $query->getResultArray();
    }
    
    public function studentName($studentId)
    {
        $res = $this->db->table('tbl_useraccount')
            ->select('name')
            ->where('id', $studentId)
            ->get()
            ->getResultArray();

        return isset($res[0]['name'])?$res[0]['name']:'';
    }

    public function getAllInfo_classRoom()
    {
        $builder=$this->db->table('tbl_classrooms');
        $builder->select("*");
        $builder->where('end_time <', time());

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function allStudents($sct_id, $country_id)
    {
        $this->session=session();
        $loggedUserId = $this->session->get('user_id');
        $loggedUserType = $this->session->get('userType');
        
        if ($loggedUserType == 1) { //parent
            $res = $this->db->table('tbl_useraccount')
                ->select('id as `st_id`')
                ->where('parent_id', $loggedUserId)
                ->get()
                ->getResultArray();
        } elseif ($loggedUserType == 7) { //q-stydy will get all students
            $builder=$this->db->table('tbl_useraccount');
            $builder->select('id as `st_id`');
//            $this->db->where('user_type', 2); //upper student
            $builder->whereIn('user_type', array(2,6)); //upper student
//            $this->db->or_where('user_type', 6); //lower student
            if (isset($country_id) && $country_id != '') {
                $builder->where('country_id', $country_id);
            }

            $query = $builder->get();
            $res = $query->getResultArray();
        } else { //corporate/school/tutor
            $builder=$this->db->table('tbl_enrollment');
            $builder->select('st_id');
            $builder->where('sct_id', $sct_id);
            
            $query = $builder->get();
            $res = $query->getResultArray();
        }
//        echo $this->db->last_query();
        return array_column($res, 'st_id');
    }
    
    public function getAllInfo($table)
    {
        $builder=$this->db->table($table);
        $builder->select('*');
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function studentByClass($class)
    {
        $res = $this->db->table('tbl_useraccount')
            ->where('student_grade', $class)
            ->get()
            ->getResultArray();

        return $res;
    }

    public function get_all_where($select, $table, $columnName, $columnValue)
    {
        $builder=$this->db->table($table);
        $builder->select($select);
        $builder->where($columnName, $columnValue);

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function get_student_ans($student_id,$question_id){
        $builder=$this->db->table('idea_student_ans');
        $builder->select('*');
        $builder->join('idea_info', 'idea_info.question_id = idea_student_ans.question_id', 'LEFT');
        $builder->where('idea_student_ans.student_id', $student_id);
        $builder->where('idea_student_ans.question_id', $question_id);
        
        $query = $builder->get();
        return $query->getResultArray();
    
    }

    public function allTutor($studentId)
    {
        return $this->db->table('tbl_enrollment')
            ->join('tbl_useraccount', 'tbl_useraccount.id=tbl_enrollment.sct_id', 'left')
            ->where('st_id', $studentId)
            ->get()
            ->getResultArray();
    }

    public function ckSchoolCorporateExits($table, $colName , $SCT_link)
    {
        $builder=$this->db->table($table);
        $builder->select('*');
        $builder->whereIn('user_type', [4,5] );
        $builder->where('SCT_link', $SCT_link);
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function studentProgress($conditions)
    {

        $tutorType  = $_SESSION['userType'];
        $userId  = $_SESSION['user_id'];
        $tutorModule = $this->db->table('tbl_module')
            ->select('id')
            ->where('user_id', $userId)
            ->get()
            ->getResultArray();
         $tutorModule = array_column($tutorModule, 'id');
         
        $res = $this->db->table('tbl_studentprogress')
            ->where($conditions);
            //->where("module in (SELECT * from tbl_module where user_id = $userId)");
        if ($tutorType==7 ||$tutorType==3) {
            $res = $res->whereIn('module', $tutorModule);
        }
            $res= $res->orderBy('answerTime', 'ASC')
            ->get()
            ->getResultArray();
        return $res;
    }

    public function chaptersOfSubject($subjectId)
    {
        $builder=$this->db->table('tbl_chapter');
        if (count($subjectId)>1) {
            $builder->whereIn('subjectId', $subjectId);
        } else {
            $builder->where('subjectId', $subjectId);
        }
        
        $query = $builder->get();
        $res=$query->getResultArray();

        return $res;
    }

    public function getAllSubjectByCourse($all_course_id,$module_type){
        $builder=$this->db->table('tbl_module');
        $builder->select('subject');
        $builder->where('moduleType', $module_type);
        $builder->where('course_id', $all_course_id);
        $builder->distinct();
    
        $query = $builder->get();
        $results =  $query->getResultArray();
    
        $subject_id = array();
        foreach($results as $result){
            if(!empty($result['subject'])){
                $subject_id[]=$result['subject'];
            }
          
        }
    
        //echo "<pre>";print_r($subject_id);die();
        return $subject_id;
    
    }

    public function getSpecificStudentProgressReport($studentId, $ideaId, $ideaNo, $questionId){
        $builder=$this->db->table('idea_correction_report as idea');
        $builder->select('idea.id,idea.teacher_correction,idea.total_point, idea.teacher_comment, idea.checked_checkbox, stdans.total_word, idea.idea_correction, idea.significant_checkbox,idea.student_id');
        $builder->join('idea_student_ans as stdans', 'idea.idea_id = stdans.idea_id');
        $builder->where('idea.student_id', $studentId);
        $builder->where('idea.idea_id', $ideaId);
        $builder->where('idea.idea_no', $ideaNo);
        $builder->where('idea.question_id', $questionId);
    
        $query = $builder->get();
        // echo $this->db->last_query();
        
        return $query->getRowArray();
    
    }
	
	 public function getQuestionStoreOrder($condition)
    {
        $builder=$this->db->table('tbl_questions_store');
        $builder->select('*');
        $builder->where($condition);
        $builder->orderBy('pdf_order', 'DESC');
        $query = $builder->get();
        return $query->getResultArray();
    }
	
	public function get_register_courses($std)
    {
        $builder=$this->db->table('tbl_registered_course');
        $builder->select('tbl_course.*');
        $builder->join('tbl_course','tbl_course.id = tbl_registered_course.course_id');
        //$builder->whereIn('user_id',$std);
        $builder->groupBy("course_id");
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function TutorStudentProgressStd($conditions,$module_user_type='',$course_id ='')
    {

        
         if ($module_user_type != '')
         {
             $student_module = $this->db->table('tbl_module')
                 ->select('id')
                 ->where('user_type ', $module_user_type)
                 ->get()
                 ->getResultArray();
             $student_module = array_column($student_module, 'id');
         }

         if ($course_id != '')
         {
             $student_module = $this->db->table('tbl_module')
                 ->select('id')
                 ->where('course_id ',$course_id)
                 ->get()
                 ->getResultArray();
             $student_module = array_column($student_module, 'id');
         }
         $builder=$this->db->table('tbl_studentprogress');
        $res = $builder->where($conditions);
            //->where("module in (SELECT * from tbl_module where user_id = $userId)");
        
        if (!empty($student_module)) {
            $res = $builder->whereIn('module', $student_module);
        }
            $builder->orderBy('answerTime', 'ASC');
            $query=$builder->get();
            $res=$query->getResultArray();
        return $res;
    }
    public function getLinkInfo($table, $colName1, $colName2, $colValue1, $colValue2)
    {
        $builder=$this->db->table($table);
        $builder->select('*');
        $builder->where($colName1, $colValue1);
        $builder->where($colName2, $colValue2);
        $query = $builder->get();
        return $query->getResultArray();
    }

    
    public function delete($tableName, $conditions)
    {
        return $res = $this->db->table($tableName)->where($conditions)->delete();
    }
}
