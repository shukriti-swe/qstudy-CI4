<?php

class ModuleClass
{
    public function __construct(){
        $this->db = db_connect();         
    }

	 public function insertInfo($table, $data)
    {
        $this->db->table($table)->insert($data);
    }
	
    public function getAllCourse($country_id){

        $builder = $this->db->table('tbl_course');
        $builder->select('*');
        $builder->where('country_id', $country_id);
        $query_new=$builder->get();
        return $query_new->getResultArray();
    }

    public function countTblNewModuleRows(){
        
        $builder = $this->db->table('tbl_module');
        $builder->select('*');
        $result = $builder->countAllResults();
        return $result;
    }

    public function getTblNewModule(){
        $this->session=session();
        $country_id = $this->session->get('selCountry');
        $user_id = $this->session->get('user_id');

        $builder = $this->db->table('tbl_module');
        $builder->select('*,tbl_module.id as id');
        $builder->join('tbl_course', 'tbl_module.course_id=tbl_course.id', 'left');
        $builder->join('tbl_subject', 'tbl_subject.subject_id=tbl_module.subject', 'left');
        $builder->join('tbl_chapter', 'tbl_chapter.id=tbl_module.chapter', 'left');
        $builder->where('tbl_module.country',$country_id);
        $builder->where('tbl_module.user_id',$user_id);
        $builder->orderBy('moduleType','asc');
        $builder->orderBy('studentGrade','asc');
        $builder->orderBy('course_id','asc');
        $builder->orderBy('serial','asc');
        $query_new = $builder->get();
        $result = $query_new->getResultArray();
        // echo $this->db->last_query();die();
        // echo "<pre>";print_r($result);die();
        return $result;
    }

    public function getCountryName($id){
        $builder = $this->db->table('tbl_country');
        $builder->select('*');
        $builder->where('id', $id);
        $query_new = $builder->get();
        $result = $query_new->getRowArray();
        return $result;
    }

    public function getCourseName($id){
        $builder = $this->db->table('tbl_course');
        $builder->select('*');
        $builder->where('id', $id);
        $query_new = $builder->get();
        $result = $query_new->getRowArray();
        return $result;
    }

    public function allModuleType()
    {
        return $this->db->table('tbl_moduletype')->select('*')->get()->getResultArray();
    }//end allModuleType()

    public function getModuleType(){

        $builder = $this->db->table('tbl_moduletype');
        $builder->select('*');
        $query_new = $builder->get();
        return $query_new->getResultArray();

    }

    public function getTblPreModuleTempCourse(){

        $builder = $this->db->table('tbl_pre_module_temp');
        $builder->select('*');
        $builder->orderBy('question_order', 'ASC');
        $query_new = $builder->get();
        return $query_new->getResultArray();
    }

    public function getTblQuestion($questionId){
        $builder = $this->db->table('tbl_question');
        $builder->select('*');
        $builder->where('id', $questionId);
        $query_new = $builder->get();
        return $query_new->getRowArray();
    }

    public function getQuestions($question_type,$user_id){

        $builder = $this->db->table('tbl_question');
        $builder->select('*');
        $builder->where('questionType', $question_type);
        $builder->where('user_id', $user_id);
        $query_new = $builder->get();
        $result = $query_new->getResultArray();
        return $result;
    }

    public function getTblQuestionType($questionType){
        $builder=$this->db->table('tbl_questiontype');
        $builder->select('questionType');
        $builder->where('id', $questionType);
        $query_new = $builder->get();
        return $query_new->getRowArray();
    }

    public function getAllTblQuestion($questionId){
        $builder=$this->db->table('tbl_question');
        $builder->select('*');
        $builder->where('questionType', $questionId);
        return $builder->countAllResults();
    }

    public function getAllSubjects($user_id){
        $builder=$this->db->table('tbl_subject');
        $builder->select('*');
        $builder->where('created_by', $user_id);
        $query_new = $builder->get();
        $result = $query_new->getResultArray();
        return $result;
    }

    public function getAllChapters($user_id){
        $builder=$this->db->table('tbl_chapter');
        $builder->select('*');
        $builder->where('created_by', $user_id);
        $query_new = $builder->get();
        $result = $query_new->getResultArray();
        return $result;
    }

    public function getIndividualStudent($student_grade, $tutor_type, $country_id, $subject_name, $user_id, $course_id)
    {
        /*echo 'two';
        print_r(strip_tags(trim(htmlspecialchars($subject_name, ENT_QUOTES))));
        die;*/
        $builder=$this->db->table('tbl_useraccount');
        $builder->select('tbl_useraccount.*');
        if ($tutor_type == 3) {
            $builder->join('tbl_enrollment', 'tbl_useraccount.id = tbl_enrollment.st_id', 'LEFT');
            $builder->where('tbl_enrollment.sct_id', $user_id);
        }
        if ($tutor_type == 7 && $course_id != '') {
            $builder->join('tbl_registered_course', 'tbl_useraccount.id = tbl_registered_course.user_id', 'LEFT');
            $builder->join('tbl_course', 'tbl_registered_course.course_id = tbl_course.id', 'LEFT');
            $builder->join('tbl_subject', 'tbl_course.courseName = tbl_subject.subject_name', 'LEFT');
            // $this->db->where('tbl_course.courseName', strip_tags(trim(html_entity_decode($subject_name, ENT_QUOTES))));
            $builder->where('tbl_course.id', $course_id);
        }
        
        $builder->where('tbl_useraccount.student_grade', $student_grade);
        if ($country_id != '') {
            $builder->where('tbl_useraccount.country_id', $country_id);
        }
        
        $query = $builder->get();
        //echo $this->db->last_query();
        return $query->getResultArray();
    }

    public function get_module_serial($module_type, $grade, $course_id){
        $builder=$this->db->table('tbl_module');
        $builder->select('MAX(serial) as max_serial');
        $builder->where('moduleType', $module_type);
        $builder->where('studentGrade', $grade);
        $builder->where('course_id', $course_id);
        $query_new = $builder->get();
        return $query_new->getRowArray();

    }

    public function insert($tableName, $data)
    {
        $res = $this->db->table($tableName)->insertBatch($data);

        return $res ? $this->db->insertID() : null;
    }

    public function deleteTblNewModule($module_id){
        $this->db->table('tbl_module')->where('id', $module_id)->delete();
    }

    public function deleteTblNewModuleQuestion($module_id){
        $this->db->table('tbl_modulequestion')->where('module_id', $module_id)->delete();
    }

    
    public function getEditModuleInfo($module_id){

        $this->session=session();
        $builder=$this->db->table('tbl_edit_module_temp');
        $builder->select('*');
        $builder->where('module_id', $module_id);
        $query_new = $builder->get();
        $result = $query_new->getResultArray();

        if(empty($result)){
            $builder=$this->db->table('tbl_modulequestion');
            $builder->select('*');
            $builder->where('module_id', $module_id);
            $query_new = $builder->get();
            $results = $query_new->getResultArray();

            $this->db->table('tbl_edit_module_temp')->truncate();

            foreach($results as $mod){
                $country_id = $this->session->get('selCountry');
                $data['module_id'] = $mod['module_id'];
                $data['question_id'] = $mod['question_id'];
                $data['question_type'] = $mod['question_type'];
                $data['question_order'] = $mod['question_order'];
                $data['country'] = $country_id;

                $this->db->table('tbl_edit_module_temp')->insert($data);
            }

                $builder=$this->db->table('tbl_edit_module_temp');
                $builder->select('*,tbl_edit_module_temp.id as tbl_id');
                $builder->join('tbl_questiontype', 'tbl_edit_module_temp.question_type=tbl_questiontype.id', 'left');
                $builder->orderBy('tbl_edit_module_temp.question_order', 'ASC');
                $query_new = $builder->get();
                $results = $query_new->getResultArray();
                return $results;
        }else{

            $builder=$this->db->table('tbl_edit_module_temp');
            $builder->select('*,tbl_edit_module_temp.id as tbl_id');
            $builder->join('tbl_questiontype', 'tbl_edit_module_temp.question_type=tbl_questiontype.id', 'left');
            $builder->orderBy('tbl_edit_module_temp.question_order', 'ASC');
            $query_new = $builder->get();
            $results = $query_new->getResultArray();
            return $results; 
        } 
        
    }

    public function newModuleInfo($moduleId)
    {
        $res = $this->db->table('tbl_module')->where('id', $moduleId)->get()->getResultArray();

        return isset($res[0])?$res[0]:[];
    }//end moduleInfo()


    public function getSubjectBycourse($course_id){
        
        $StudentClass=new \StudentClass();
        $assign_course = $StudentClass->getInfo('tbl_assign_subject', 'course_id',$course_id);
        $subjectId = json_decode($assign_course[0]['subject_id']);
        $subjects = array();
        foreach($subjectId as $value)
        {
            $sb =  $StudentClass->getInfo('tbl_subject', 'subject_id',$value);
            if (!empty($sb))
            {
                $subjects[] = $sb[0];
            }
        }
        // echo"<pre>";print_r($subjects);die();
        return $subjects;
    }


    public function getChapterBycourse($subject){
        $builder=$this->db->table('tbl_chapter');
        $builder->where('subjectId', $subject);
        $chapters = $builder->get()->getResultArray();
        return $chapters;
    }

    public function moduleInfo($moduleId)
    {
        $res = $this->db->table('tbl_module')->where('id', $moduleId)->get()->result_array();

        return isset($res[0])?$res[0]:[];
    }//end moduleInfo()

    public function getInfo($table, $colName, $colValue)
    {
        $builder=$this->db->table($table);
        $builder->select('*');
        $builder->from($table);
        $builder->where($colName, $colValue);

        $query = $builder->get();
        return $query->getResultArray();
    }
	
    public function moduleQuestion($moduleId)
    {
        $res = $this->db->table('tbl_modulequestion')->where('module_id', $moduleId)->get()->getResultArray();

        return $res;
    }

    public function deleteModuleQuestion($moduleId)
    {
        $this->db->table('tbl_modulequestion')->where('module_id',$moduleId)->delete();
    }

    public function getMaxTblPreModuleTempCourse(){
        $builder=$this->db->table('tbl_pre_module_temp');
        $builder->select('MAX(question_order) as max_size');
        $query_new = $builder->get();
        return $query_new->getRowArray();
    }

    
    public function duplicateQuestionCreate($question_id,$question_type){
        error_report_check();
        $builder=$this->db->table('tbl_question');
        $builder->select('*');
        $builder->where('id', $question_id);
        $query_new = $builder->get();
        $result = $query_new->getResultArray();
   
        $data= $result[0];
        $data['id']= '';
        $this->db->table('tbl_question')->insert($data);
        $insert_id = $this->db->insertID();

        $builder=$this->db->table('tbl_question');
        $builder->select('*');
        $builder->where('id', $insert_id);
        $query_new=$builder->get();
        $result = $query_new->getResultArray();
        return $result[0];
    }

    public function moduleQuestionDuplicate($table, $data){
        $result = $this->db->table($table)->insert($data);
        return $result;
    }

    
    public function questionSorting($table, $colName, $colValue, $data){
        $this->db->table($table)->where($colName, $colValue)->update($data);
    }

    public function getEditPreModuleTemp($module_id){
        $builder=$this->db->table('tbl_edit_module_temp');
        $builder->select('*');
        $builder->where('module_id', $module_id);
        $builder->orderBy('question_order', 'ASC');
        $query_new = $builder->get();
        return $query_new->getResultArray();
    }

    public function getMaxTblNewModuleQuestionEdit($module_id){
        $builder=$this->db->table('tbl_edit_module_temp');
        $builder->select('MAX(question_order) as max_size');
        $builder->where('module_id', $module_id);
        $query_new = $builder->get();
        return $query_new->getRowArray();
    }

    public function getDuplicateQuestion($question_id){
        $builder=$this->db->table('tbl_question');
        $builder->select('*');
        $builder->where('id', $question_id);
        $query_new = $builder->get();
        $result = $query_new->getResultArray();
        
        $data= $result[0];
        $data['id']= '';
        $this->db->table('tbl_question')->insert($data);

        $insert_id = $this->db->insertID();

        $builder=$this->db->table('tbl_question');
        $builder->select('*');
        $builder->where('id', $insert_id);
        $query_new = $builder->get();
        $result = $query_new->getResultArray();
        return $result[0];
    }

    public function studentHomework($id , $module_type)
    {

      $this->session=session();
      $builder=$this->db->table('student_homeworks');
      $builder->groupBy("assign_subject");
      $builder->where('tutor_id', $id);
      $builder->where('student_id', $this->session->get('user_id'));
      $builder->where('module_type', $module_type);
      $builder->where('status', 1);
      
      $query = $builder->get();
      return $query->getResultArray();

    }

    public function allModule($conditions = [] , $country_id='')
      {
          $this->session=session();
          $loggedUserId = $this->session->get('user_id');
          
          $builder=$this->db->table('tbl_module');
          $builder
              //->select('tbl_module.*, tbl_subject.subject_name as subject_name, tbl_chapter.chapterName as chapterName')
              ->select('tbl_module.*, tbl_subject.subject_name as subject_name, tbl_chapter.chapterName as chapterName, tbl_useraccount.name creatorName, tbl_moduletype.module_type module_type')
              ->join('tbl_subject', 'tbl_subject.subject_id=tbl_module.subject', 'left')
              ->join('tbl_useraccount', 'tbl_module.user_id=tbl_useraccount.id', 'left')
              ->join('tbl_chapter', 'tbl_chapter.id=tbl_module.chapter', 'left')
              ->join('tbl_moduletype', 'tbl_module.moduleType=tbl_moduletype.id', 'left');
              
              $builder->where('show_student', 1);
          
          if (count($conditions)) {
              $builder->where($conditions);
          } else {
              $builder->where('user_id', $loggedUserId);
          }
  
          if ($country_id !='') {
            if ($country_id == 1) {
              $builder->where('tbl_module.country', $country_id);
            }else{
                $builder->whereIn('tbl_module.country', [$country_id,'1']);
            }
          }
          
          if (isset($conditions['moduleType'])) {
              if ($conditions['moduleType'] == 3 || $conditions['moduleType'] == 4) {
                  $sub_q = $builder->query("SELECT module_id FROM tbl_student_answer")->getResultArray();
                 
                  if (!empty($sub_q)) {
                      $builder->whereNotIn('tbl_module.id', array_column($sub_q, 'module_id'));
                  }
              }
          }
          
         $builder->orderBy('tbl_subject.order', 'ASC');
         $builder->orderBy('tbl_module.ordering', 'ASC');
          // $this->db->order_by('tbl_module.id', 'ASC');
          
          $res = $builder->get()->getResultArray();
              // echo '<pre>';print_r($country_id);die;
          return $res;
      } // end allModule()


      public function getInfoByOrder($table, $colName, $colValue)
      {
          $builder=$this->db->table($table);
          $builder->select('*');
          $builder->where($colName, $colValue);
          $query = $builder->get();
          return $query->getResultArray();
      }

      public function moduleName($moduleId)
        {
            $res = $this->db->table('tbl_module')
                ->select('moduleName')
                ->where('id', $moduleId)
                ->get()
                ->getResultArray();

            return isset($res[0]['moduleName']) ? $res[0]['moduleName'] : '';
        }//end moduleName()
  
        
    public function moduleTypeName($moduleTypeId)
    {
        $res = $this->db->table('tbl_moduletype')
            ->select('module_type')
            ->where('id', $moduleTypeId)
            ->get()
            ->getResultArray();

        return isset($res[0]['module_type']) ? $res[0]['module_type'] : '';
    }//end moduleTypeName()

    public function getTblModuleInfo($id){
        $builder=$this->db->table('tbl_module');
        $builder->select('*');
        $builder->where('id', $id);
        $query_new = $builder->get();
        $result = $query_new->getRowArray();
        return $result;
    }

    public function getModuleMaxSerial($course_id,$moduleType,$studentGrade){
        $this->session=session();
        $loggedUserId = $this->session->get('user_id');

        $builder=$this->db->table('tbl_module');
        $builder->select('MAX(serial) as max_serial');
        $builder->where('moduleType', $moduleType);
        $builder->where('studentGrade', $studentGrade);
        $builder->where('course_id', $course_id);
        $builder->where('user_id', $loggedUserId);
        $query_new = $builder->get();
        //$result = $query_new->result_array();
        // echo $this->db->last_query();
        // print_r($result);die();
        return $query_new->getRowArray();
    }

    public function getTblNewModuleQuestion($moduleId, $new_module_id){
        $builder=$this->db->table('tbl_modulequestion');
        $builder->select('*');
        $builder->where('module_id', $moduleId);
        $query_new = $builder->get();
        $results = $query_new->getResultArray();
        
        foreach($results as $result){
           $question_id = $result['question_id'];

            $builder=$this->db->table('tbl_question');
            $builder->select('*');
            $builder->where('id', $question_id);
            $query_new = $builder->get();
            $question = $query_new->getResultArray();
            $data = $question[0];
            $data['id']= '';

            $this->db->table('tbl_question')->insert($data);
            $insert_id = $this->db->insertID();

            $builder=$this->db->table('tbl_modulequestion');
            $builder->select('MAX(question_order) as max_size');
            $builder->where('module_id', $new_module_id);
            $query_new = $builder->get();
            $max_order = $query_new->getRowArray();

            $data2['question_id'] = $insert_id;
            $data2['question_type'] = $data['questionType'];
            $data2['module_id'] = $new_module_id;
            $data2['question_order'] = $max_order['max_size'] + 1;
            
            $this->db->table('tbl_modulequestion')->insert($data2);

        }

        // $this->db->select('*');
        // $this->db->from('tbl_modulequestion');
        // $this->db->where('module_id', $new_module_id);
        // $query_new = $this->db->get();
        // $results2 = $query_new->result_array();
        // echo "<pre>";print_r($results2);die();
        return 1;
    }

    public function getTblNewModuleQuestionWithout($moduleId){
        $builder=$this->db->table('tbl_modulequestion');
        $builder->select('*');
        $builder->where('module_id', $moduleId);
        $query_new = $builder->get();
        $result = $query_new->getResultArray();
        return $result;
    }
	
	  public function allModuleForAssign($id , $tyoe)
    {
      
      $builder=$this->db->table('tbl_module');
      $builder->join('tbl_subject', 'tbl_subject.subject_id = tbl_module.subject');
      $builder->join('tbl_chapter', 'tbl_module.chapter = tbl_chapter.id');  
      $builder->select('tbl_module.id , tbl_module.moduleName , tbl_module.moduleType , tbl_module.trackerName , tbl_module.individualName , tbl_module.exam_date , tbl_subject.subject_name , tbl_module.subject , tbl_chapter.chapterName , tbl_module.moduleName');

      if ($tyoe == "course_id") {
        $builder->where('tbl_module.course_id', $id);
      }

      if ($tyoe == "module_id") {
        $builder->where('tbl_module.id', $id);
      }
      
      $query = $builder->get();
      return $query->getResultArray();

    }

    public function studentAssignedModule($st_id , $tutor_id)
    {
      $builder=$this->db->table('student_homeworks');
      $builder->where('tutor_id', $tutor_id);
      $builder->where('student_id', $st_id);
      $builder->where('status', 1);
      
      $query = $builder->get();
      return $query->getResultArray();

    }

    public function deleteAssignedModule($student_id , $tutorId )
    {
      $builder=$this->db->table('student_homeworks');  
      $builder->where('tutor_id', $tutorId);
      $builder->where('student_id', $student_id);
      $builder->delete();
    }
    
    public function studentAssignedModuleforUpdate($st_id , $tutor_id , $module_id)
    {
      $builder=$this->db->table('student_homeworks'); 
      $builder->where('tutor_id', $tutor_id);
      $builder->where('student_id', $st_id);
      $builder->where('assign_module', $module_id);
      
      $query = $builder->get();
      return $query->getResultArray();

    }

    public function getIdeaInfo($table, $ques_id) {
        $builder=$this->db->table($table);
        $builder->select('*');
        $builder->where('question_id', $ques_id);
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getIdeaDescription($table, $ques_id) {
        $builder=$this->db->table($table);
        $builder->select('*');
        $builder->where('question_id', $ques_id);
        $query = $builder->get();
        return $query->getResultArray();
    }
    public function insertId($table, $data)
    {
        $this->db->table($table)->insert($data);
        $insert_id = $this->db->insertID();
        
        return $insert_id;
    }
    public function updateInfo($table, $colName, $colValue, $data)
    {
        $this->db->table($table)->where($colName, $colValue)->update($data);
    }

    public function deleteInfo($table, $colName, $colValue)
    {
        $this->db->table($table)->where($colName, $colValue)->delete();
    }
	
	   public function AssignModuleTutuorTutorial($tutorId  , $student_id , $moduleType )
    {
      $builder=$this->db->table('student_homeworks');  
      $builder->where('tutor_id', $tutorId);
      $builder->where('student_id', $student_id);
      $builder->where('module_type', $moduleType);
      $builder->where('status', 1);
      $query = $builder->get();
      return $query->getResultArray();
    }

	 public function AssignModuleSchoolTutuorTutorial($tutorId , $student_id , $moduleType )
    {
      $builder=$this->db->table('tbl_module');  	
      $builder->where('user_id', $tutorId);
      $builder->where('moduleType', $moduleType);
      $query = $builder->get();
      return $query->getResultArray();
    }
}