<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TblSetting;

class TutorController extends BaseController
{
    public function index()
    {
        error_reporting(0);
        $FaqClass = new \FaqClass();
        $RegisterClass = new \RegisterClass();
        $TutorClass = new \TutorClass();
        $AdminClass = new \AdminClass();

        if ($this->session->get('userType') == 3) {
            $data['video_help'] = $FaqClass->videoSerialize(2, 'video_helps');
            $data['video_help_serial'] = 2;
        }
        $data['user_info'] = $TutorClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        
        $data['checkDirectDepositPendingCourse'] = $AdminClass->getDirectDepositPendingCourse($this->session->get('user_id'));
        $data['checkRegisterCourses'] = $AdminClass->getActiveCourse($this->session->get('user_id'));
        
        $TblSetting=new TblSetting();
        $tbl_setting = $TblSetting->where('setting_key','days')->first();

        $duration = $tbl_setting->setting_value;
        $date = date('Y-m-d');
        $d1  = date('Y-m-d', strtotime('-'.$duration.' days', strtotime($date)));
        $trialEndDate = strtotime($d1);
        
        $inactive_user_info = $AdminClass->getInfoInactiveUserCheck('tbl_useraccount', 'subscription_type', 'trial',$trialEndDate,$this->session->get('user_id'));
        
        $data['inactive_user_check'] = count($inactive_user_info);
        //echo $data['inactive_user_check'];die();
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('tutor/tutors_dashboard', $data);
    }


    public function view_course()
    {
        $TutorClass=new \TutorClass();
        $FaqClass=new \FaqClass();

        $user_id = $this->session->get('user_id');
       
        $user_type = $this->session->get('userType');
        if($user_type == 3){
          $country_id = $TutorClass->getCountryId($user_id);
          $this->session->set('selCountry', $country_id);
        }
        // echo '<pre>';
        // print_r($_SESSION);
        // die();
        $data['user_info'] = $TutorClass->userInfo($user_id);

        $ck_schl_corporate_exist = $TutorClass->ck_schl_corporate_exist($data['user_info'][0]['SCT_link'] );
        if (count($ck_schl_corporate_exist)) {
            $data['ck_schl_corporate_exist'] = $ck_schl_corporate_exist;
        }

        if ($data['user_info'][0]['subscription_type'] =="direct_deposite") {
            if ( $data['user_info'][0]['direct_deposite'] == 0 ) {
                return redirect()->to(base_url($_SERVER['HTTP_REFERER']));
            }
        }

        if(isset($_SESSION['list_submit']) && $_SESSION['list_submit'] == 1)
        {
            unset($_SESSION['list_submit']);
        }

        $data['video_help'] = $FaqClass->videoSerialize(21, 'video_helps');
        $data['video_help_serial'] = 21;


        return view('tutor/view_course', $data);
    }

    
    public function tutor_setting()
    {
        error_reporting(0);
        $FaqClass = new \FaqClass();
        $TutorClass = new \TutorClass();

        $data['video_help'] = $FaqClass->videoSerialize(19, 'video_helps');
        $data['video_help_serial'] = 19;

        $data['user_info'] = $TutorClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));

        if ($data['user_info'][0]['subscription_type'] =="direct_deposite") {
            if ( $data['user_info'][0]['direct_deposite'] == 0 ) {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('tutor/tutor_setting', $data);
    }

    public function tutor_details()
    {
        error_reporting(0);
        $StudentClass = new \StudentClass();
        $TutorClass = new \TutorClass();
        
        $data['user_info'] = $TutorClass->userInfo($this->session->get('user_id'));

        if ($data['user_info'][0]['subscription_type'] =="direct_deposite") {
            if ( $data['user_info'][0]['direct_deposite'] == 0 ) {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        // Newly Added
        $all_subject_tutor =$StudentClass->getInfo('tbl_registered_course', 'user_id', $this->session->get('user_id'));
        $whiteboard = 0;
        foreach ($all_subject_tutor as $key => $value) {
            $course_id = $value['course_id'];
            if ($course_id == 53) {
                $whiteboard = 1;
            }

        }
        $data['whiteboard'] = $whiteboard;
        //echo '<pre>';print_r($data);die;
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return view('tutor/tutor_details', $data);
    }

    public function update_tutor_details()
    {
        error_reporting(0);
        $TutorClass = new \TutorClass();

        $input = $this->validate([
            'password' => 'trim|required|min_length[6]',
            'passconf' => 'trim|required|matches[password]',
        ]);
        if (!$input ) {
            echo 0;
        } else {
            $password = md5($this->request->getVar('password'));
            $data = array(
                'user_pawd' => $password
            );
            $TutorClass->updateInfo('tbl_useraccount', 'id', $this->session->get('user_id'), $data);
            echo 1;
        }
    }

    public function tutor_upload_photo()
    {
        error_reporting(0);
        $TutorClass = new \TutorClass();

        $data['user_info'] = $TutorClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));

        if ($data['user_info'][0]['subscription_type'] =="direct_deposite") {
            if ( $data['user_info'][0]['direct_deposite'] == 0 ) {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('tutor/upload', $data);
    }

    public function tutor_file_upload()
    {
        error_reporting(0);
        $TutorClass = new \TutorClass();
        $profile_image = $this->request->getFile('file');
    
        if ($profile_image != '') {
            // if (file_exists('./admin/uploads/banner_section/' . $old_image)) {

            //     unlink('./admin/uploads/banner_section/' . $old_image);
            // }
            $profile_images = $profile_image->getRandomName();
            $image=$profile_image->move(ROOTPATH . 'public/assets/uploads', $profile_images);
            if($image)
            {
                $user_profile_picture=$profile_images ;
                $data = array(
                    'image' =>$user_profile_picture
                );
                $rs['res']=$TutorClass->updateInfo('tbl_useraccount','id',$this->session->get('user_id'),$data);
                echo 1;
            }   
            else
            {
                echo 0;  
            } 
        }
    }

    public function updateProfile()
    {
        
        error_reporting(0);
        $TutorClass = new \TutorClass();

        $_SESSION['prevUrl'] = base_url('/tutor_setting');
        $post = $this->request->getVar();

      
        // $clean = $this->security->xss_clean($post);
        // $this->form_validation->set_rules('name', 'Name', 'Required');
        // if ($this->form_validation->run()==true) {
        //     $additionalTableData = [
        //         'address'        =>isset($clean['address'])?$clean['address']:'',
        //         'city'           =>isset($clean['city'])?$clean['city']:'',
        //         'state'          =>isset($clean['state'])?$clean['state']:'',
        //         'post_code'      =>isset($clean['post_code'])?$clean['post_code']:'',
        //         'phone_num'      =>isset($clean['phone_num'])?$clean['phone_num']:'',
        //         'website'        =>isset($clean['website'])?$clean['website']:'',
        //         'short_bio'      =>isset($clean['short_bio'])?$clean['short_bio']:'',
        //         'teach_subjects' =>isset($clean['teach_subjects'])?$clean['teach_subjects']:'',
        //         'tutoring_rates' =>isset($clean['tutoring_rates'])?$clean['tutoring_rates']:'',
        //         'qualification'  =>isset($clean['qualification'])?$clean['qualification']:'',
        //         'tuition_experience'  =>isset($clean['tuition_experience'])?$clean['tuition_experience']:'',
        //         'availability'   =>isset($clean['availability'])?$clean['availability']:'',
        //         'language'       =>isset($clean['language'])?$clean['language']:'',
        //         'updated_at'     =>date('Y-m-d H:i:s'),
        //     ];

        //     $userAccountTableData = [
        //         'name'          =>$post['name'],
        //         'country_id'    =>$clean['country_id'],
        //         'user_email'    =>isset($clean['user_email'])?$clean['user_email']:'',
        //         'user_mobile'   =>isset($clean['user_mobile'])?$clean['user_mobile']:'',
        //     ];

        //     $this->tutor_model->updateInfo('additional_tutor_info', 'tutor_id', $this->loggedUserId, $additionalTableData);
        //     $this->tutor_model->updateInfo('tbl_useraccount', 'id', $this->loggedUserId, $userAccountTableData);
            
        //     $this->session->set_flashdata('success_msg', 'Account Updated Successfully');
        // } // update tutor account if post has data
        if ($this->request->getMethod() == 'post')
        {
            $input = $this->validate([
                'name' => 'required',
            ]);
            if (!$input) {
               
                $data['validation'] = $this->validator;
            }
            else
            {
               $additionalTableData = [
                'address'        =>$this->request->getVar('address'),
                'city'           =>$this->request->getVar('city'),
                'state'          =>$this->request->getVar('state'),
                'post_code'      =>$this->request->getVar('post_code'),
                'phone_num'      =>$this->request->getVar('phone_num'),
                'website'        =>$this->request->getVar('website'),
                'short_bio'      =>$this->request->getVar('short_bio'),
                'teach_subjects' =>$this->request->getVar('teach_subjects'),
                'tutoring_rates' =>$this->request->getVar('tutoring_rates'),
                'qualification'  =>$this->request->getVar('qualification'),
                'tuition_experience'=>$this->request->getVar('tuition_experience'),
                'availability'   =>$this->request->getVar('availability'),
                'language'       =>$this->request->getVar('language'),
                'updated_at'     =>date('Y-m-d H:i:s'),
              ];

             $userAccountTableData =[
                'name'          =>$post['name'],
                'country_id'    =>$post['country_id'],
                'user_email'    =>$post['user_email'],
                'user_mobile'   =>$post['user_mobile'],
             ];

                $TutorClass->updateInfo('additional_tutor_info', 'tutor_id',$this->session->get('user_id'), $additionalTableData);
                $TutorClass->updateInfo('tbl_useraccount', 'id',$this->session->get('user_id'), $userAccountTableData);
                
                $this->session->set('success_msg', 'Account Updated Successfully');
            }

        }    
        //echo 'ase ny';die();
        $conditions = [
            'tbl_useraccount.id'=>$this->session->get('user_id'),
            'tbl_useraccount.user_type'=>3,
        ];
        $tutor = $TutorClass->tutorInfo($conditions);

        $data['tutor_info'] = $tutor[0];
        $country = $TutorClass->getRow('tbl_country', 'id', $data['tutor_info']['country_id']);
        $data['tutor_info']['country'] = $country['countryName'];
        $data['tutor_info']['country_id'] = $country['id'];
        $studentIds=$TutorClass->allStudents(['sct_id' => $this->loggedUserId]);
        $data['total_std'] = count($studentIds);
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
      
        return view('tutor/update_profile', $data);
    }

    public function question_list($id = "",$param_module_id = "",$module_edit_id = "")
    {
        
        error_reporting(0);
        $FaqClass= new \FaqClass();
        $AdminClass= new \AdminClass();
        $TutorClass= new \TutorClass();
     
        if($id == 2){

            $this->session->set('module_status', $id);


            if($module_edit_id == ""){
                $this->session->remove('module_edit_id');
            }else{
                $this->session->set('module_edit_id', $module_edit_id);
            }

            if($param_module_id == ""){
                $this->session->remove('param_module_id');
            }else{
                $this->session->set('param_module_id', $param_module_id);
            }
        }else{
            if ($id == "") {
                $this->session->remove('module_status');
            } else {
                $this->session->set('module_status', $id);
            }

            if($module_edit_id == ""){
                $this->session->remove('module_edit_id');
            }else{
                $this->session->set('module_edit_id', $module_edit_id);
            }           
            if($param_module_id == ""){
                $this->session->remove('param_module_id');
            }else{
                $this->session->set('param_module_id', $param_module_id);
            }
        }

        $data['video_help'] = $FaqClass->videoSerialize(22, 'video_helps');
        $data['video_help_serial'] = 22;

        $post = $this->request->getVar();
        $post = array_filter($post);
        $get = $this->request->getVar();
        $countrySelected = 0;
        $fromQuestionEditPage = 0;
       
        if (isset($post['list_submit']) && $post['list_submit'] ==1)
        {
            $_SESSION["list_submit"] = 1;
        }
        if(isset($_SESSION["list_submit"])){

        }else{
            unset($_SESSION["modInfo"]);
        }

        //module info in flash data for all question area search param
        //if come from module edit page
        if (isset($get['type']) && ($get['type']=='edit')) {
            $data["edit_has"] = "yes";
            $mId = $get['mId'];
            $currentURL = current_url();

            $url = $currentURL."/?type=edit&mId=".$mId;
            $_SESSION["has_edit"] = $url;
            $module = $AdminClass->search('tbl_module', ['id'=>$mId]);
            // print_r($module); die();
            if (count($module)) {
                $this->session->set('modInfo', $module[0]);
            }
            $countrySelected = 1;
        } elseif (isset($get['country']) || isset($_SESSION['modInfo']['country']) || isset($_SESSION['selCountry'])) {
        
        //q-study will select country before going question-list/module, in that case need to filter by country
        if(isset($get['country']))
        {
            $country=$get['country'];
        }
        else
        {
            if(isset($_SESSION['modInfo']['country']))
            {
                $country=$_SESSION['modInfo']['country'] ;
            }
            else
            {
                if(isset($_SESSION['selCountry']))
                {
                    $country=$_SESSION['selCountry']; 
                }
                else
                {
                    $country=""; 
                }
            }
        }
      $countrySelected = 1;
    }
    elseif (empty($get['type'])) {
        unset ($_SESSION["has_edit"]);
    }
    if (isset($_SESSION['refPage']) && $_SESSION['refPage'] == 'questionEdit') {
        $fromQuestionEditPage = 1;
    }

    $data['user_info'] = $TutorClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));

    if ($data['user_info'][0]['subscription_type'] =="direct_deposite") {
        if ( $data['user_info'][0]['direct_deposite'] == 0 ) {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

  
    $user_id = $this->session->get('user_id');

    // added shvou
    $data['tutor_permission_check'] = $this->db->table('tbl_useraccount')->where('id',$user_id)->get()->getRow();

    $data['user_info'] = $TutorClass->userInfo($user_id);
    $data['all_module'] = $TutorClass->getInfo('tbl_module', 'user_id', $user_id);
    $data['all_grade'] = $TutorClass->getAllInfo('tbl_studentgrade');
    $data['all_module_type'] = $TutorClass->getAllInfo('tbl_moduletype');
    $data['all_question_type'] = $TutorClass->getAllInfo('tbl_questiontype');

    foreach ($data['all_question_type'] as $questionType) {
        $question_list[$questionType['id']] = [];
    }
 
    if (count($post) || ($countrySelected) || $fromQuestionEditPage) {
        //if get query string fetch question from query scoped module
        $mId = isset($_GET['mId']) ? $_GET['mId'] : null;
        $module = $AdminClass->search('tbl_module', ['id'=>$mId]);

        $moduleName = count($module) ? $module[0]['moduleName'] : (isset($post['moduleName']) ? $post['moduleName'] : '');
        //$country = count($module) ? $module[0]['country'] : (isset($post['country']) ? $post['country'] : (isset($get['country']) ? $get['country'] : ''));
        $country = count($module) ? $module[0]['country'] : (isset($country)?$country:'');
        $grade = isset($post['grade']) ? $post['grade'] : (isset($module[0]['studentGrade'])?$module[0]['studentGrade'] : '');
    
        $moduleType =  count($module) ? $module[0]['moduleType'] : (isset($post['moduleType']) ? $post['moduleType'] : '');
        $subject = isset($post['subject']) ? $post['subject'] :  (isset($module[0]['subject'])?$module[0]['subject'] : '');
        $chapter = isset($post['chapter']) ? $post['chapter'] :  (isset($module[0]['chapter'])?$module[0]['chapter'] : '');
        $course  = isset($post['course']) ? $post['course'] :  (isset($module[0]['course_id'])?$module[0]['course_id'] : '');
        $user_id = $this->session->get('user_id');
        if ($post) {
            //save on session for filtering(ques search button click)
            $_SESSION['modInfo'] =  [
            'moduleName' => $moduleName,
            'country' =>    $country,
            'studentGrade' => $grade,
            'moduleType' => $moduleType,
            'subject'    => $subject,
            'chapter'    => $this->get_chapter_name($subject, $chapter),
            'course'    => $course,
            ];
        }
        // echo '<pre>';
        // print_r( $_SESSION['modInfo']);die();

        //if request param for module/country/module_type then fetch module question
        //else fetch question from question table
        if (isset($post['moduleName']) ||  isset($post['moduleType']) || isset($_GET['mId'])) {
            $conditions = [
            'moduleName' => $moduleName,
            'country' =>    $country,
            'studentGrade' =>$grade,
            'moduleType' => $moduleType,
            'subject'    => $subject,
            'chapter'    => $chapter,
            'course_id'  => $course,
            'user_id' => $user_id,
            ];
            $conditions = array_filter($conditions);
            $modules = $AdminClass->search('tbl_module', $conditions);
            $moduleIds = count($modules) ? array_column($modules, 'id') : -1;
            $moduleQuestions = $AdminClass->whereIn('tbl_modulequestion', 'module_id', $moduleIds);

            $questionIds = count($moduleQuestions) ? array_column($moduleQuestions, 'question_id') : -1;
            $conditions = !empty($grade) ? ['studentgrade'=>$grade] : [];
            $questions = $AdminClass->whereIn('tbl_question', 'id', $questionIds, $conditions);
        
            foreach ($questions as $question) {
                $question_list[$question['questionType']][] = $question;
            }
            foreach ($data['all_question_type'] as $questionType) {
                if (!($question_list[$questionType['id']])) {
                    $question_list[$questionType['id']] = [];
                }
            }
        } else {
            //if params come from question edit page
            if (isset($_SESSION['modInfo'])) {
                $sSub = isset($_SESSION['modInfo']['subject'])?$_SESSION['modInfo']['subject']:'';
                $pSub = isset($post['subject'])?$post['subject']:'';
                $sChap = isset($_SESSION['modInfo']['selChapter'])?$_SESSION['modInfo']['selChapter']:'';
                $pChap = isset($post['chapter'])?$post['chapter']:'';
                $sGrade = isset($_SESSION['modInfo']['studentGrade'])?$_SESSION['modInfo']['studentGrade']:'';
                $pGrade = isset($post['grade'])?$post['grade']:'';
                $sCountry = isset($_SESSION['modInfo']['country'])?$_SESSION['modInfo']['country']:'';
                $pCountry = isset($post['country'])?$post['country']:'';

                $subject      = isset($sSub) ? $sSub : (isset($pSub) ? $pSub : '');
                $chapter      = isset($sChap) ? $sChap : (isset($pChap) ? $pChap : '');
                $studentgrade = isset($sGrade) ? $sGrade : (isset($pGrade) ? $pGrade : '');
                $country      = $country;//isset($sCountry) ? $sCountry : (isset($pCountry) ? $pCountry : '');
            } else {
                $subject      = isset($post['subject']) ? $post['subject'] : '';
                $chapter      = isset($post['chapter']) ? $post['chapter'] : '';
                $studentgrade = isset($post['grade']) ? $post['grade'] : '';
                $country = $country;//isset($post['country']) ? $post['country'] : '';
            }

            $conditions = [
            'subject'      => $subject,
            'chapter'      => $chapter,
            'studentgrade' => $studentgrade,
            'country'      => $country,
            ];
        
            $conditions = array_filter($conditions);
            $conditions['user_id'] = $user_id;
            foreach ($data['all_question_type'] as $questionType) {
                $conditions['questionType'] = $questionType['id'];
                $question_list[$questionType['id']] = $TutorClass->getUserQuestion('tbl_question', $conditions);
            }
        }
    } else {
        foreach ($data['all_question_type'] as $questionType) {
            $conditions = [
            'user_id' => $user_id,
            'questionType' => $questionType['id'],
            ];
            $question_list[$questionType['id']] = $TutorClass->getUserQuestion('tbl_question', $conditions);
        }
    }

    $data['all_question'] = $question_list;
    $data['user_id'] = $user_id;

    $data_2 = array();
    $data_3 = array();

    foreach ($data['all_question'] as $key => $value) {
        if (!empty($value)) {
            foreach ($value as $key2 => $value2) {

                $ck= $TutorClass->chk_value($key ,$user_id);
                
                foreach ($ck as $key3 => $value3) {
                    if ($value3["id"] == $value2["id"] ) {
                        $var4 = [

                            "order" => $key3,
                            "question_type" =>$key,
                            "id" => $value3["id"]
                        ];

                        array_push($data_2, $var4);
                        
                        
                    }

                }

            }

            array_push($data_3, $data_2);
            $data_2 = []; 

        }
        
    }

    if (isset($get['type']) && ($get['type']=='edit')) {
        $data["old_ques_order"] = $data_3;
        $data["last_data"] =  $TutorClass->last_data($user_id);
    }
    
    $data['subscription_type'] = $_SESSION['subscription_type'];

    $data['allCountry'] = $this->db->table('tbl_country')->get()->getResultArray();
    $data['all_subject'] = $TutorClass->getInfo('tbl_subject', 'created_by', $user_id);

    $data['all_course']  = $this->db->table('tbl_course')->get()->getResultArray();

     // check password added shvou
    $data['checkNullPw'] = $this->db->table('tbl_setting')->where("setting_key", "qstudyPassword")->where("setting_type !=", '')->get()->getResultArray();


    return view('tutor/question/question_list', $data);
   
  }

  public function get_chapter_name($subject = 0, $selected = 0)
  {
      $TutorClass= new \TutorClass();
      $subject_id = $subject ? $subject : $this->request->getVar('subject_id');

      $all_subject_chapter = $TutorClass->getInfo('tbl_chapter', 'subjectId', $subject_id);
      $html = '';
      $i=1;
      foreach ($all_subject_chapter as $chapter) {
		   if($i==1){
             $html = '<option value="">Select Chapter</option>';
          }
          $sel = $chapter['id'] == $selected ? 'selected' : '';
          $html .= '<option value="' . $chapter['id'] . '" '.$sel.'>' . $chapter['chapterName'] . '</option>';
		  $i++;
      }
      if ($subject) {
          return $html; //within controller
      } else {
          echo $html; // ajax/form submit
      }
  }

  public function create_question($item)
    {
        $TutorClass=new \TutorClass();
        $AdminClass=new \AdminClass();
        $QuestionClass=new \QuestionClass();
        $user_id = $this->session->get('user_id');

        //echo $item;die();
        $data['all_grade'] = $TutorClass->getAllInfo('tbl_studentgrade');
        $data['all_subject'] = $TutorClass->getInfo('tbl_subject', 'created_by', $user_id);
        $data['allCountry'] = $this->db->table('tbl_country')->get()->getResultArray();
        $data['all_idea'] = $QuestionClass->getIdea();
        $data['question_item'] = $item;
        // echo '<pre>';
        // print_r($data['allCountry']);die();
        $question_box = 'tutors/question/question-box';

        if ($item==1) {
            return view('tutor/question/question-box/general',$data);
        } elseif ($item==2) {
            return view('tutor/question/question-box/true-false',$data);
        } elseif ($item==3) {
            return view('tutor/question/question-box/vocabulary',$data);
        } elseif ($item==4) {
            return view('tutor/question/question-box/multiple-choice',$data);
        } elseif ($item == 5) {
            $question_box .= '/multiple-response';
        } elseif ($item==6) {
            return view('tutor/question/question-box/skip_quiz',$data);
        } elseif ($item==7) {
            $question_box .= '/matching';
        } elseif ($item == 8) {
            $this->add_assignment_question();
            //$question_box .= '/assignment';
        } elseif ($item == 9) {
            $question_box .= '/story_write';
        } elseif ($item == 10) {
            return view('tutor/question/question-box/times_table',$data); 
        } elseif ($item == 11) {
            return view('tutor/question/question-box/algorithm',$data); 
        } elseif ($item == 12) {
            $question_box .= '/workout_quiz';
        } elseif ($item == 13) {
            $question_box .= '/matching_workout';
        }
        elseif ($item == 14) {
            $data["for_disable_button"]="1";
            return view('tutor/question/question-box/tutorial',$data); 
        }elseif ($item == 15) {
            return view('tutor/question/question-box/workout_quiz_two',$data); 
        }elseif ($item == 16) {
            return view('tutor/question/question-box/memorization',$data); 
        }elseif ($item == 17) {

            $this->db->table('idea_save_temp')->truncate();
            $builder = $this->db->table('idea_info');
            $builder->select('*');
            $builder->like('image_title','Image','after');
            $query =  $builder->get();
            $results= $query->getResultArray();
    
            $image_count = count($results);
            if(empty($image_count)){
              $data['image_no']= 1;
            }else{
              $data['image_no']= $image_count+1;
            }
         return view('tutor/question/question-box/creative_quiz',$data);   
            
        }elseif($item == 18){
            return view('tutor/question/question-box/sentence_match',$data); 
        }elseif($item == 19){
            return view('tutor/question/question-box/word_memorization',$data); 
        }
		 elseif($item == 20){
            return view('tutor/question/question-box/comprehension',$data);
        }
        elseif($item == 21){
            return view('tutor/question/question-box/grammer',$data);
        }
        elseif($item == 22){
            return view('tutor/question/question-box/glossary',$data);
        }
        elseif($item == 23){
            return view('tutor/question/question-box/image_quiz',$data);
        }

        // if ($item != 8) {
        //     //$data['question_box']=$this->load->view($question_box, $datas, true);
        //     // $data['maincontent'] = $this->load->view('tutors/question/create_question', $data, true);
        //     return view('tutor/question/create_question', $data);
        // }
    }

    public function add_assignment_question()
    {
        $TutorClass=new \TutorClass();
        $user_id = $this->session->get('user_id');
        $data['all_grade'] = $TutorClass->getAllInfo('tbl_studentgrade');
        $data['all_subject'] = $TutorClass->getInfo('tbl_subject', 'created_by', $user_id);
        $country = $TutorClass->get_country($_SESSION['user_id']);
        $data['country'] = $country[0]['country_id'];

        return view('tutors/question/create_assignment_question', $data);
    }

    public function save_question_data()
    {
        error_reporting(0);
        $post = $this->request->getVar();
        
        // echo "<pre>";print_r($post);die();
        // $clean = $this->security->xss_clean($post);
        // $clean['media'] = isset($_FILES)?$_FILES:[];
    
        $instruction_link = isset($post['question_instruction']) ? $post['question_instruction'] :'';
        $instruction_link = str_replace('</p>', '', $instruction_link);
        $instruction_array = array_filter(explode('<p>', $instruction_link));
    
        $instruction_new_array = array();
        foreach ($instruction_array as $row) {
            $instruction_new_array[] = strip_tags($row); 
        }
     

        $video_link = isset($post['question_video']) ? $post['question_video'] :'';
        $video_link = str_replace('</p>', '', $video_link);
        $video_array = array_filter(explode('<p>', $video_link));
    
        $video_new_array = array();
        foreach ($video_array as $row) {
            $video_new_array[] = strip_tags($row);
        }

        $data['questionType'] = $this->request->getVar('questionType');
        $questionName = $this->request->getVar('questionName');
        $answer = $this->request->getVar('answer');
        $questionMarks = $this->request->getVar('questionMarks');
        $description = $this->request->getVar('questionDescription');
        $solution = $this->request->getVar('question_solution');
       
        
        if ($data['questionType'] == 3) {
            $questionName =  $this->processVocabulary($post);
        } if ($_POST['questionType']==4) {
            
            if (isset($_POST['question_tutorial_input']) && !empty($_POST['question_tutorial_input']) ) {
                $img_multipleChoose = array();
                $question_tutorial_input = $_POST['question_tutorial_input'];

                $img_files = explode(",",$question_tutorial_input);

                for ($i=0; $i < count($img_files); $i++) { 
                    foreach ($img_files as $key => $value) {
                            if (strpos($img_files[$i] , "IMG_".($key+1).".")) {
                               $img_multipleChoose[] = $value;
                            }
                        }
                }
            }
            
            // $_POST['questionName'] = !empty($_POST['questionName_2']) ? $_POST['questionName_2'] : $_POST['questionName'];
            // add New AS
            if (isset($_POST['questionName_1_checkbox']) && isset($_POST['questionName_2_checkbox'])) {
                $_POST['questionName'] = $_POST['questionName'];
            }else{
              $_POST['questionName'] = !empty($_POST['questionName_2']) ? $_POST['questionName_2'] : $_POST['questionName'];  
            }
           
            $questionName = $this->save_multiple_choice($_POST);
            $data['question_name_type'] = $this->request->getVar('question_name_type');
            if (isset($_POST['questionName_1_checkbox']) && isset($_POST['questionName_2_checkbox'])) {
                
                $data['question_name_type'] = 2;
            }
            $answer = json_encode($_POST['response_answer']);
                    // $answer = $_POST['response_answer'];


        } if ($_POST['questionType']==5) {
            //Same as Multiple Choice
            $questionName = $this->save_multiple_response($_POST);
            if (isset($_POST['response_answer'])) {
                $answer = json_encode($_POST['response_answer']);
            }
        } if ($data['questionType']==6) { //skip quiz
            $temp['question_body'] = isset($post['question_body'])?$post['question_body']:'';
            $temp['skp_quiz_box'] = $post['ques_ans'];
            $temp['numOfRows']     = isset($post['numOfRows']) ? $post['numOfRows'] : 0;
            $temp['numOfCols']     = isset($post['numOfCols']) ? $post['numOfCols'] : 0;
            $questionName =  json_encode($temp);
            $answer = json_encode(array_values(array_filter($post['ans'])));
        } if ($_POST['questionType']==7) {
            $questionName = $this->ques_matching_data($_POST);
            $answer = $this->ans_matching_data($_POST);
        } if ($data['questionType'] == 8) {
            // assignment
            $temp          = $this->processAssignmentTasks($post);
            $questionName  = json_encode($temp);
            $questionMarks = isset($temp['totMarks'])?$temp['totMarks']:0;
        } if ($_POST['questionType']==9) {

            if (!empty($post['rightTitle']) && !empty($post['rightIntro']) && !empty($post['lastpictureSelected']) && !empty($post['Paragraph']) && !empty($post['rightConclution']) && !empty($post['wrongTitles']) && !empty($post['wrongTitlesIncrement']) && !empty($post['pictureList'])  && !empty($post['wrongIntro']) && !empty($post['wrongIntroIncrement']) &&  !empty($post['wrongConclution']) && !empty($post['wrongConclutionIncrement']) && !empty($post['wrongPictureIncrement']) ) {

                $question['rightTitle'] = $post['rightTitle'];
                $question['rightIntro'] = $post['rightIntro'];
                $question['lastpictureSelected'] = $post['lastpictureSelected'];
                $question['Paragraph']  = $post['Paragraph'];
                $question['rightConclution'] = $post['rightConclution'];

                $answer = json_encode($question);
                $question['wrongTitles'] = $post['wrongTitles'];
                $question['wrongTitlesIncrement'] = $post['wrongTitlesIncrement'];
                $question['wrongPictureIncrement'] = $post['wrongPictureIncrement'];
                $question['wrongConclutionIncrement'] = $post['wrongConclutionIncrement'];
                $question['wrongIntro'] = $post['wrongIntro'];
                $question['wrongIntroIncrement'] = $post['wrongIntroIncrement'];
                $question['wrongConclution'] = $post['wrongConclution'];
                $question['wrongParagraphIncrement'] = $post['wrongParagraphIncrement'];

                if(isset($_POST['pictureList']) ){
                    foreach ($_POST['pictureList'] as $key => $value) {
                        if ($value != "" && $post['lastpictureSelected'] != $value ) {
                            $pictureList[] = $value;
                        } 
                    }
                }

                if (isset($pictureList)) {
                    $question['pictureList'] = $pictureList;
                    $questionName = json_encode($question);
                } else {
                    $data['errorStoryWrite'] = "Check All the Question Properly";
                }

            }else{
                $data['errorStoryWrite'] = "Check All the Question Properly";
            }

        } if ($data['questionType'] == 10) {
            $question_data['questionName'] = $post['questionName'];
            $question_data['factor1'] = $post['factor1'];
            $question_data['factor2'] = $post['factor2'];
            $questionName = json_encode($question_data);

            $answer = json_encode($post['result']);
        } if ($data['questionType'] == 11) {
            $question_data['questionName'] = $post['question_body'];
            $question_data['operator'] = $post['operator'];
        
            if ($post['operator'] == '/') {
                $question_data['divisor'] = $post['divisor'];
                $question_data['dividend'] = $post['dividend'];
                $question_data['remainder'] = $post['remainder'];
                $question_data['quotient'] = $post['quotient'];
            
                $answer = json_encode($post['remainder']);
            } if ($post['operator'] != '/') {
                $question_data['item'] = $post['item'];
                $answer = json_encode($post['result']);
            }
        
            $question_data['numOfRows']     = isset($post['numOfRows']) ? $post['numOfRows'] : 0;
            $question_data['numOfCols']     = isset($post['numOfCols']) ? $post['numOfCols'] : 0;
        
            $questionName = json_encode($question_data);
        } if ($_POST['questionType']==13) {
            $questionName = $this->save_multiple_choice($_POST);
            if (isset($_POST['response_answer'])) {
                $answer = $_POST['response_answer'];
            }
        }if ($_POST['questionType']==15)
        {
                $solution = $this->request->getVar('solution');
                $questionName = $this->save_workout_two($_POST);
                if (isset($_POST['answer'])) {
                    $answer = $_POST['answer'];
                }
        }if ($_POST['questionType'] == 16) {
            $questionName = $this->save_memorization($_POST);
            if (isset($_POST['answer'])) {
                $answer = $_POST['answer'];
            }else
            {
                $answer = '';
            }
        }if ($_POST['questionType'] == 17) {

            $questionName='Idea';
            $answer = 'NO';
            $questionMarks=25;
            $description='';
            $instruction_new_array='';
            $video_new_array='';
            $solution = 'Nothing';
            
        }if ($_POST['questionType'] == 18) {

            $answers = $_POST['answer'];

            if(!empty($answers)){
            $questions= array();
            $ans= array();

            foreach($answers as $answer){
                $ans_with_ques = explode (",,",$answer);
                $questions[]= $ans_with_ques[0];
                $ans[]= $ans_with_ques[1];
            }
            }
            $questionName= json_encode($questions);
            $answer = json_encode($ans);
            $questionMarks=15;
            $description='';
            $instruction_new_array='';
            $video_new_array='';
            $solution = 'Nothing';
        }if ($_POST['questionType'] == 19) {
            $answers = $_POST['answer'];

            if(!empty($answers)){
            $questions= array();
            $ans= array();


            foreach($answers as $answer){
                $ans_with_ques = explode (",,", $answer);
                $questions[]= $ans_with_ques[0];
                $ans[]= $ans_with_ques[1];
            }
            }
            $mydata['questions'] = $questions;
            $mydata['wrong_questions'] = $post['wrong_question'];
            // print_r($mydata);die();
            $questionName= json_encode($mydata);
            $answer = json_encode($ans);
            $questionMarks=15;
            $description='';
            $instruction_new_array='';
            $video_new_array='';
            $solution = 'Nothing';
        }
        
		if ($_POST['questionType'] == 20) {
            $check_write =1;
            foreach($_POST['options'] as $option){
               if(!empty($option)){
                $check_write =2;
               }
            }

            if($check_write==2){
               $answer = $post['option_check'][0];
            }else{
               $answer = "write";
               $questionMarks = 0;

            }
            if(!empty($post['com_question'])){
                $questionName = $post['com_question'];
            }else{
                $questionName = "";
            }
            
            $com_data = array();
            $com_data['options'] = $post['options'];
            $com_data['first_hint'] = $post['first_hint'];
            $com_data['total_rows'] = $post['total_rows'];
            $com_data['title_colors'] = $post['title_colors'];
            $com_data['second_hint'] = $post['second_hint'];
            $com_data['writing_input'] = $post['writing_input'];
            $com_data['text_one_hint'] = $post['text_one_hint'];
            $com_data['text_two_hint'] = $post['text_two_hint'];
            $com_data['image_ques_body'] = $post['image_ques_body'];
            $com_data['option_hint_set'] = $post['option_hint_set'];
            $com_data['text_one_hint_no'] = $post['text_one_hint_no'];
            $com_data['text_two_hint_no'] = $post['text_two_hint_no'];
            $com_data['note_description'] = $post['note_description'];
            $com_data['text_one_hint_color'] = $post['text_one_hint_color'];
            $com_data['text_two_hint_color'] = $post['text_two_hint_color'];
            $com_data['question_title_description'] = $post['question_title_description'];


            $description = json_encode($com_data);
        }
        if ($_POST['questionType'] == 21) {
            $answer = $post['option_check'][0];

            if(!empty($post['com_question'])){
                $questionName = $post['com_question'];
            }else{
                $questionName = "";
            }

            $grammer_data = array();
            $grammer_data['options'] = $post['option'];
            $grammer_data['total_rows'] = $post['total_rows'];
            $grammer_data['second_hint'] = $post['second_hint'];
            $grammer_data['writing_input'] = $post['writing_input'];
            $grammer_data['first_hint'] = $post['first_hint'];
            $grammer_data['second_hint'] = $post['second_hint'];
            $grammer_data['third_hint'] = $post['third_hint'];
            $grammer_data['four_hint'] = $post['four_hint'];
            $grammer_data['color_serial'] = $post['color_serial'];
            $grammer_data['text_one_hint_color'] = $post['text_one_hint_color'];
            $grammer_data['text_two_hint_color'] = $post['text_two_hint_color'];
            $grammer_data['text_four_hint_color'] = $post['text_four_hint_color'];
            $grammer_data['text_three_hint_color'] = $post['text_three_hint_color'];
            $grammer_data['question_title_description'] = $post['question_title_description'];

            $description = json_encode($grammer_data);

        }
        if ($_POST['questionType'] == 22) {

            $glossary_data['title_color'] = $post['title_color'];
            $glossary_data['question_title_description'] = $post['question_title_description'];
            $glossary_data['image_ques_body'] = $post['image_ques_body'];

            $questionName = 'no';
            $answer= 'no';
            $questionMarks = 0;
            $description = json_encode($glossary_data);
        }
        if ($_POST['questionType'] == 23) {

            $check_write =1;
            foreach($_POST['options'] as $option){
               if(!empty($option)){
                $check_write =2;
               }
            }

            if($check_write==2){
               $answer = $post['answer'];
            }else{
               $answer = "write";
               $questionMarks = 0;

            }
            // if(!empty($post['com_question'])){
            //     $questionName = $post['com_question'];
            // }else{
            //     $questionName = "";
            // }

            $image_data['help_check_one'] = $post['help_check_one'];
            $image_data['help_check_two'] = $post['help_check_two'];
            $image_data['help_check_three'] = $post['help_check_three'];
            $image_data['image_type_one'] = $post['image_type_one'];
            $image_data['image_type_two'] = $post['image_type_two'];
            $image_data['image_type_three'] = $post['image_type_three'];

            $image_data['box_one_image'] = $post['box_one_image'];
            $image_data['box_two_image'] = $post['box_two_image'];
            $image_data['box_three_image'] = $post['box_three_image'];

            $image_data['hint_one_image'] = $post['hint_one_image'];
            $image_data['hint_two_image'] = $post['hint_two_image'];
            $image_data['hint_three_image'] = $post['hint_three_image'];

            $image_data['help_check_one'] = $post['help_check_one'];
            $image_data['help_check_two'] = $post['help_check_two'];
            $image_data['help_check_three'] = $post['help_check_three'];
            $image_data['question'] = $post['question'];

            $image_data['total_rows'] = $post['total_rows'];
            $image_data['options'] = $post['options'];
            $image_data['quiz_explaination'] = $post['quiz_explaination'];
            
            $questionName = $post['quiz_question'];
            $description = json_encode($image_data);
        }



        $data['studentgrade'] = $this->request->getVar('studentgrade');
        $data['user_id'] = $this->session->get('user_id');
        $data['subject'] = $this->request->getVar('subject');
        $data['chapter'] = $this->request->getVar('chapter');
        $data['country'] = $this->request->getVar('country');
        $data['questionName'] = $questionName;
        $data['answer'] = $answer;
        $data['questionMarks'] = $questionMarks;
        $data['questionDescription'] =  $description;
        if($_POST['questionType'] == 18){
            $data['question_instruction'] = $post['question_instruct'];
        }else{
            $data['question_instruction'] = json_encode($instruction_new_array);
        }
        $data['question_video'] = json_encode($video_new_array);
        $data['isCalculator'] = $this->request->getVar('isCalculator');
        $data['question_solution'] = strlen($solution)<1 ? 'NO solution given' : $solution;

        $hour   = isset($_POST['question_time']) ? $this->request->getVar('hour') : "HH";
        $minute = isset($_POST['question_time']) ? $this->request->getVar('minute') : "MM";
        $second = isset($_POST['question_time']) ? $this->request->getVar('second') : "SS";
        
        // echo "<pre>";print_r($data);die();
        if ($data['questionType'] == 14) {
            $data["question_solution"] ="NO solution given";
            $data['answer'] = "c";
            $data['questionName'] = $this->processTutorial($post);
            $data["last_id"] = "102";

        }
       
        $data['questionTime'] = $hour.":".$minute.":".$second;
        $TutorClass=new \TutorClass();

        $chkValidation['flag'] = 1;
        if ($data['questionType'] != 8) {
            $chkValidation = $this->checkValidation($data);
        }
        if ($chkValidation['flag'] == 0) {
            echo json_encode($chkValidation);
        } else {

            $array_one = array();
            $array_two = array();
            $array_three = array();


            if (!empty($data["last_id"])) {
                $data['questionMarks'] = "0";
                //
        
                $questionId = $TutorClass->insertId('tbl_question', $data);

                $last_id = $TutorClass->last_id($data['user_id']);
                

                foreach (json_decode($data['questionName']) as $key => $value) {
                    if (!empty($value->speech_to_text)) {
                        $var = [

                            "speech_to_text"=>$value->speech_to_text

                        ];

                        array_push($array_one, $var);
                    }
                }

                foreach (json_decode($data['questionName']) as $key => $value) {
                    if (!empty($value->image)) {
                        $var = [

                            "image"=>$value->image

                        ];
                         array_push($array_two, $var);
                    }
                }

                foreach (json_decode($data['questionName']) as $key => $value) {
                    if (!empty($value->Audio)) {
                        $var = [

                            "Audio"=>$value->Audio

                        ];
                         array_push($array_three, $var);
                    }
                }

                $a = count($array_one);

                
                for ($i=0; $i <$a ; $i++) { 

                    $this->db->query('INSERT INTO `for_tutorial_tbl_question`(`speech`, `img`, `audio`, `tbl_ques_id` ,`orders` ) VALUES ("'.$array_one[$i]["speech_to_text"].'", "'.$array_two[$i]["image"].'" ,  "'.$array_three[$i]["Audio"].'", '.$last_id[0]["id"].', '.$i.' )');
                   
                }
         
             }
             else{
                $TutorClass=new \TutorClass();
                $questionId = $TutorClass->insertId('tbl_question', $data);

                if ($data['questionType'] == 17) {


                if(isset($post['short_question_allow'])){
                    $short_question_allow = $post['short_question_allow'];
                }else{
                    $short_question_allow='';
                }
                if(isset($post['shot_question_title'])){
                    $shot_question_title = $post['shot_question_title'];
                }else{
                    $shot_question_title = '';
                }
                if(isset($post['short_ques_body'])){
                    $short_ques_body = $post['short_ques_body'];
                }else{
                    $short_ques_body = '';
                }
                if(isset($post['image_ques_body'])){
                    $image_ques_body = $post['image_ques_body'];
                }else{
                    $image_ques_body = '';
                }
                if(isset($post['large_question_allow'])){
                    $large_question_allow = $post['large_question_allow'];
                }else{
                    $large_question_allow = '';
                }
                if(isset($post['large_question_title'])){
                    $large_question_title = $post['large_question_title'];
                }else{
                    $large_question_title = '';
                }
                if(isset($post['large_ques_body'])){
                    $large_ques_body = $post['large_ques_body'];
                }else{
                    $large_ques_body = '';
                }
                if(isset($post['student_title'])){
                    $student_title = $post['student_title'];
                }else{
                    $student_title = '';
                }
                if(isset($post['word_limit'])){
                    $word_limit = $post['word_limit'];
                }else{
                    $word_limit = '';
                }
                if(isset($post['time_hour'])){
                    $time_hour = $post['time_hour'];
                }else{
                    $time_hour = '';
                }
                if(isset($post['time_min'])){
                    $time_min = $post['time_min'];
                }else{
                    $time_min = '';
                }
                if(isset($post['time_sec'])){
                    $time_sec = $post['time_sec'];
                }else{
                    $time_sec = '';
                }
                if(isset($post['allow_idea'])){
                    $allow_idea = $post['allow_idea'];
                }else{
                    $allow_idea = '';
                }
                if(isset($post['add_start_button'])){
                    $add_start_button = $post['add_start_button'];
                }else{
                    $add_start_button = '';
                }

                $uType = $this->session->get('userType');
                if($uType==3){
                    $data_idea['allows_online']= 2;
                }else{
                    $data_idea['allows_online']= 1;
                }
            
                $data_idea['short_question_allow']= $short_question_allow;
                $data_idea['shot_question_title']= $shot_question_title;
                $data_idea['short_ques_body']= $short_ques_body;
                $data_idea['image_ques_body']= $image_ques_body;
                $data_idea['large_question_allow']= $large_question_allow;
                $data_idea['large_question_title']= $large_question_title;
                $data_idea['large_ques_body']= $large_ques_body;
                $data_idea['student_title']= $student_title;
                $data_idea['word_limit']= $word_limit;
                $data_idea['time_hour']= $time_hour;
                $data_idea['time_min']= $time_min;
                $data_idea['time_sec']= $time_sec;
                $data_idea['allow_idea']= $allow_idea;
                $data_idea['add_start_button']= $add_start_button;
                $data_idea['question_id']= $questionId;

                if(!empty($image_ques_body)){
                    $builder = $this->db->table('idea_info'); 
                    $builder->select('*');
                    $builder->like('image_title','Image','after');
                    $query = $builder->get();
                    $results= $builder->getResultArray();
                    $image_count = count($results);
                    $image_count = $image_count+1;
                    $data_idea['image_title'] = 'Image '.$image_count;
                    $data_idea['image_no'] = $image_count;
                }
                $TutorClass=new \TutorClass();
                $idea_id = $TutorClass->ideainsertId('idea_info', $data_idea);
                
                
                $datas[]=isset($post['question_instruction']) ? $post['question_instruction'] :'';


                $idea_description = $post['idea_details'];
                foreach($idea_description as $key => $value){

                   $idea = explode (",,,", $value); 
                //    print_r($idea);

                
                   $idea_des['question_id']= $questionId;
                   $idea_des['idea_id']= $idea_id;
                   $idea_des['idea_no']= $idea[0];
                   $idea_des['idea_name']= "Idea".$idea[0];
                   $idea_des['idea_title']= $idea[1];
                   $idea_des['idea_question']= $idea[2];
                   

                   $idea_des_id = $TutorClass->idea_des_Id('idea_description', $idea_des);

                   $qstudy_idea['question_id']= $questionId;
                   $qstudy_idea['idea_id']= $idea_id;
                   $qstudy_idea['tutor_id']= $this->session->get('user_id');
                   $qstudy_idea['idea_no']= $idea[0];
                   $qstudy_idea['student_ans']= $idea[2];
                   $qstudy_idea['submit_date']= date('Y/m/d');
                   $qstudy_idea['total_word']= $idea[2];
                   $tutor_idea_save = $TutorClass->tutor_idea_save('idea_tutor_ans', $qstudy_idea);
                }

                $this->db->table('idea_save_temp')->truncate();

              }
            }
          
            $module_status = $this->session->get('module_status');
            $module_edit_id = $this->session->get('module_edit_id');
            $param_module_id = $this->session->get('param_module_id');
            // echo $module_status; die();
            if ($module_status==1) {
                if(!empty($module_edit_id)){
                    $module_update['question_id'] = $questionId;
                    $module_update['question_type'] = $_POST['questionType'];
                    
                    $this->db->table('tbl_pre_module_temp')->where('id', $module_edit_id)->update( $module_update);
                }else{
                    $builder = $this->db->table('tbl_pre_module_temp ita');
                    $builder->select('*');
                    $query_result = $builder->get();
                    $results = $query_result->getResultArray();
                    $question_no = count($results);
                    $question_order = $question_no+1;
                    $module_insert['question_id'] = $questionId;
                    $module_insert['question_type'] = $_POST['questionType'];
                    $module_insert['question_order'] = $question_order;
                    $module_insert['question_no'] = $question_no;
                    $module_insert['question_no'] = $this->request->getVar('country');
                    $this->db->table('tbl_pre_module_temp')->insert($module_insert);
                }
                // echo $this->db->last_query(); die();
            }else if ($module_status==2) {
                if(!empty($module_edit_id)){
             
                    $module_update['question_id'] = $questionId;
                    $module_update['question_type'] = $_POST['questionType'];
                    
                    $this->db->table('tbl_edit_module_temp')->where('id', $module_edit_id)->update( $module_update);
                }else{
                    
                    $builder = $this->db->table('tbl_edit_module_temp ita');
                    $builder->select('*');
                    $query_result = $builder->get();
                    $results = $query_result->getResultArray();
                    $question_no = count($results);
                    $question_order = $question_no+1;
                    $module_insert['module_id'] = $param_module_id;
                    $module_insert['question_id'] = $questionId;
                    $module_insert['question_type'] = $_POST['questionType'];
                    $module_insert['question_order'] = $question_order;
                    $module_insert['question_no'] = $question_no;
                    $module_insert['country'] = $post['country'];
                    $this->db->table('tbl_edit_module_temp')->insert($module_insert);
                }
                // echo $this->db->last_query(); die();
            }    

            //        $this->questionMediaUpload($questionId);
            $chkValidation['question_id'] = $questionId;
            
            if ($_POST['questionType']==9)
            {
                $data = array();
                $data['first_atmp_text']  = $this->request->getVar('first_atmp_text');
                $data['second_atmp_text'] = $this->request->getVar('second_atmp_text');
                $data['three_atmp_text']  = $this->request->getVar('three_atmp_text');
                $data['question_id']  = $questionId;

                $first_input_value  = $this->request->getVar('1st_input_value');
                $second_input_value  = $this->request->getVar('2nd_input_value');
                $three_input_value  = $this->request->getVar('3rd_input_value');
                $data['1st_input_value'] = json_encode($first_input_value);
                $data['2nd_input_value'] = json_encode($second_input_value);
                $data['3rd_input_value'] = json_encode($three_input_value);
                $attemptId = $TutorClass->insertId('tbl_question_attempt', $data);
            }
            if ($_POST['questionType']== 4 ) {

                if (isset($img_multipleChoose)) {
                    foreach ($img_multipleChoose as $key => $value) {
                        $this->db->query('INSERT INTO `tbl_question_tutorial`(`speech`, `img`, `audio`, `question_id` ,`orders` ) VALUES ("none", "'.$value.'" ,  "none", '.$questionId.', '.$key.' )');
                    }
                }
            }
            echo json_encode($chkValidation);
        }
    }

    public function processVocabulary($items)
    {
        $arr['definition'] = $items['definition'];
        $arr['parts_of_speech'] = $items['parts_of_speech'];
        $arr['synonym'] = $items['synonym'];
        $arr['antonym'] = $items['antonym'];
        $arr['sentence'] = $items['sentence'];
        $arr['near_antonym'] = $items['near_antonym'];
        $arr['speech_to_text'] = $items['speech_to_text'];
        $arr['ytLinkInput'] = $items['ytLinkInput'];
        $arr['ytLinkTitle'] = $items['ytLinkTitle'];
        $arr['sentence'] = $items['sentence'];

        $uType = $this->session->get('userType');

        //$arr['vocubulary_image'] = $items['vocubulary_image'];
        for ($i = 1; $i <= $items['image_quantity']; $i++) {
            //$image = 'vocubulary_image_' . $i . '[]';
            $desired_image[] = $items['vocubulary_image_'.$i];
        }
        if ($desired_image) {
            $arr['vocubulary_image'] = $desired_image;
        }
        
        if (isset($items['existed_audio_File']) && $items['existed_audio_File'] != '') {
            $arr['audioFile'] = $items['existed_audio_File'];
        }
        error_reporting(0);
        $files = $_FILES;
        //only q-study user can upload video
        if (isset($_FILES['videoFile']) && $_FILES['videoFile']['error'][0] != 4 && $uType==7 ) {

            $validated = $this->validate([
                'videoFile' => [
                    'mime_in[mp3|mp4|3gp|ogg|wmv]',
                ],
            ]);

            if($validated)
            {
                
                $videoFile = $this->request->getFile('videoFile');
                $videoFile_name = $videoFile->getRandomName();
                $videoFile->move(ROOTPATH . 'public/assets/uploads/question_media/', $videoFile_name);
                $base = base_url() . 'assets/uploads/uploads/question_media/'.$videoFile_name;
                $arr['videoFile'] = $base . $videoFile_name;
            }
            else
            {
              $error=$this->validator;  
            }

        }
        
        if (isset($_FILES['audioFile']) && $_FILES['audioFile']['error'][0] != 4) {

            $validated = $this->validate([
                'audioFile' => [
                    'mime_in[mp3|mp4|3gp|ogg|wmv]',
                ],
            ]);

            if($validated)
            {
                
                $audioFile = $this->request->getFile('audioFile');
                $audioFile_name = $audioFile->getRandomName();
                $audioFile->move(ROOTPATH . 'public/assets/uploads/question_media/', $audioFile_name);
                $base = base_url() . 'assets/uploads/uploads/question_media/' .$audioFile;
                $arr['audioFile'] = $base . $audioFile_name;
            }
            else
            {
              $error=$this->validator;  
            }
        }
        
        return json_encode($arr);
    }

    private function save_multiple_choice($post_data)
    {
        for ($i = 1; $i <= $post_data['image_quantity']; $i++) {
            //            $image = 'vocubulary_image_' . $i . '[]';
            $desired_image[] = $post_data['vocubulary_image_'.$i];
        }
        $arr['questionName'] = $post_data['questionName'];
        //new Added AS
        if (isset($_POST['questionName_1_checkbox']) && isset($_POST['questionName_2_checkbox'])) {
            $arr['questionName_2'] = (isset($post_data['questionName_2']))?$post_data['questionName_2']:$post_data['questionNameClick'];
        }
        if ($desired_image) {
            $arr['vocubulary_image'] = $desired_image;
        }

        $combined_data = json_encode($arr);
        return $combined_data;
    }

    private function save_multiple_response($post_data)
    {
        for ($i = 1; $i <= $post_data['image_quantity']; $i++) {
            //            $image = 'vocubulary_image_' . $i . '[]';
            $desired_image[] = $post_data['vocubulary_image_'.$i];
        }
        $arr['questionName'] = $post_data['questionName'];
        if ($desired_image) {
            $arr['vocubulary_image'] = $desired_image;
        }

        $combined_data = json_encode($arr);
        return $combined_data;
    }

    public function ques_matching_data($post_data)
    {
        $array_1 = array();
        for ($i = 1; $i <= $post_data['image_quantity']; $i++) {
            $array_1[] = $post_data['match_image_1_'.$i];
        }
        $arr['left_side'] = $array_1;

        $array_2 = array();
        for ($i = 1; $i <= $post_data['image_quantity']; $i++) {
            $array_2[] = $post_data['match_image_2_'.$i];
        }

        $arr['right_side'] = $array_2;

        $arr['questionName'] = $post_data['questionName'];
        $combined_data = json_encode($arr);
        return $combined_data;
    }
    
    public function ans_matching_data($post_data)
    {
        $data_answer = array();
        for ($i = 1; $i <= $post_data['image_quantity']; $i++) {
            //            $answer = 'answer_' . $i;
            $data_answer[] = $post_data['answer_'.$i];
        }
        return json_encode($data_answer);
    }

    public function processAssignmentTasks(array $items)
    {
        $itemNum = count($items['qMark']);
        $arr     = [];
        $temp    = [];
        $temp['totMarks'] = 0;
        
        for ($a = 0; $a < $itemNum; $a++) {
            $arr[] = json_encode(
                [
                    'serial'      => $a,
                    'qMark'       => $items['qMark'][$a],
                //'obtnMark'    => $items['obtnMark'][$a],
                    'description' => $items['descriptions'][$a],
                ]
            );
            $temp['totMarks'] += $items['qMark'][$a];
        }

        $temp['question_body']    = $items['question_body'];
        $temp['assignment_tasks'] = $arr;
        return $temp;
    }//end processAssignmentTasks()

    public function save_workout_two($post_data)
    {
        $percentage_array = array();
        for ($i = 1; $i <= $post_data['image_quantity']; $i++) {
            //            $image = 'vocubulary_image_' . $i . '[]';
            $desired_image[] = $post_data['vocubulary_image_'.$i];
        }
        for ($i = 1; $i <= $post_data['image_quantity']; $i++) {
            //            $image = 'vocubulary_image_' . $i . '[]';
            $percentage_array['percentage_'.$i] = $post_data['percentage_'.$i];
        }
        $arr['questionName'] = $post_data['questionName'];
        $arr['question_hint'] = $post_data['question_hint'];
         $arr['solution'] = $post_data['solution'];
        if ($desired_image) {
            $arr['vocubulary_image'] = $desired_image;
        }
        if ($percentage_array) {
            $arr['percentage_array'] = $percentage_array;
        }

        $combined_data = json_encode($arr);
        return $combined_data;
    }

     // memorization function create by aftab
     public function save_memorization($post_data)
     {
 
         $arr = array();
         $arr_data = array();
         $arr['pattern_type'] = $post_data['pattern_type'];
         $arr['hide_component_left'] = $post_data['hide_component_left'];
         $arr['hide_component_right'] = $post_data['hide_component_right'];
         $arr['hide_alphabet'] = $post_data['hide_alphabet'];
         $arr['hide_word'] = $post_data['hide_word'];
         $arr['box_quantity'] = $post_data['box_quantity'];
 
         if (isset($post_data['questionName']))
         {
             $arr['questionName'] = $post_data['questionName'];
         }else
         {
             $arr['questionName'] = '';
         }
         if ($post_data['pattern_type'] == 1)
         {
             $left_memorize_p_one = array();
             $left_memorize_h_p_one = array();
             $right_memorize_p_one = array();
             $right_memorize_h_p_one = array();
             $checkLeftHidden = 0;
             $checkRightHidden = 0;
 
 
             for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                 $left_memorize_p_one[] = $post_data['left_memorize_p_one'][$i];
             }
             if ($left_memorize_p_one)
             {
                 $arr['left_memorize_p_one'] = $left_memorize_p_one;
             }
             for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                 $left_memorize_h_p_one[] = $post_data['left_memorize_h_p_one'][$i];
                 if ($post_data['left_memorize_h_p_one'][$i] == 1)
                 {
                     $checkLeftHidden = 1;
                 }
             }
             if (isset($post_data['hide_pattern_one_left']) && $checkLeftHidden == 0)
             {
                 $left_memorize_h_p_one = array();
                 for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                     $left_memorize_h_p_one[] = 1;
                 }
             }
             if ($left_memorize_h_p_one)
             {
                 $arr['left_memorize_h_p_one'] = $left_memorize_h_p_one;
             }
 
             for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                 $right_memorize_p_one[] = $post_data['right_memorize_p_one'][$i];
             }
             if ($right_memorize_p_one)
             {
                 $arr['right_memorize_p_one'] = $right_memorize_p_one;
             }
             for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                 $right_memorize_h_p_one[] = $post_data['right_memorize_h_p_one'][$i];
                 if ($post_data['right_memorize_h_p_one'][$i] == 1)
                 {
                     $checkRightHidden = 1;
                 }
             }
             if (isset($post_data['hide_pattern_one_right']) && $checkRightHidden == 0)
             {
                 $right_memorize_h_p_one = array();
                 for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                     $right_memorize_h_p_one[] = 1;
                 }
             }
             if ($right_memorize_h_p_one)
             {
                 $arr['right_memorize_h_p_one'] = $right_memorize_h_p_one;
             }
             if (isset($post_data['hide_pattern_one_left']) && $post_data['hide_pattern_one_left'] != '')
             {
                 $arr['hide_pattern_one_left'] = $post_data['hide_pattern_one_left'];
             }
             if (isset($post_data['hide_pattern_one_right']) && $post_data['hide_pattern_one_right'] != '')
             {
                 $arr['hide_pattern_one_right'] = $post_data['hide_pattern_one_right'];
             }
 
             $arr_data = $arr;
         }elseif ($post_data['pattern_type'] == 2)
         {
             $left_memorize_p_two = array();
             $left_memorize_h_p_two = array();
             $right_memorize_p_two = array();
             $right_memorize_h_p_two = array();
             $checkPTLeftHidden = 0;
             $checkPTRightHidden = 0;
 
 
             for ($i = 1; $i <= $post_data['box_quantity']; $i++) {
                 $left_memorize_p_two[][0] =str_replace("&#39;","'",$post_data['left_memorize_p_two_'.$i][0]);
                 // $left_memorize_p_two[][0] =str_replace("&#39;","'",strip_tags($post_data['left_memorize_p_two_'.$i][0])) ;
             }
             if ($left_memorize_p_two)
             {
                 $arr['left_memorize_p_two'] = $left_memorize_p_two;
             }
             for ($i = 1; $i <= $post_data['box_quantity']; $i++) {
                 $left_memorize_h_p_two[] = $post_data['left_memorize_h_p_two_'.$i];
                 if ($post_data['left_memorize_h_p_two_'.$i][0] == 1)
                 {
                     $checkPTLeftHidden = 1;
                 }
 
 
             }
 
             if (isset($post_data['hide_pattern_two_left']) && $checkPTLeftHidden == 0)
             {
                 $left_memorize_h_p_two = array();
                 for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                     $left_memorize_h_p_two[][0] = 1;
                 }
             }
 
             if ($left_memorize_h_p_two)
             {
                 $arr['left_memorize_h_p_two'] = $left_memorize_h_p_two;
             }
             for ($i = 1; $i <= $post_data['box_quantity']; $i++) {
                 $right_memorize_p_two[][0] = str_replace("&#39;","'",$post_data['right_memorize_p_two_'.$i][0]);
                 // $right_memorize_p_two[][0] = str_replace("&#39;","'",strip_tags($post_data['right_memorize_p_two_'.$i][0]));
             }
             if ($right_memorize_p_two)
             {
                 $arr['right_memorize_p_two'] = $right_memorize_p_two;
             }
             for ($i = 1; $i <= $post_data['box_quantity']; $i++) {
                 $right_memorize_h_p_two[] = $post_data['right_memorize_h_p_two_'.$i];
                 if ($post_data['right_memorize_h_p_two_'.$i][0] == 1)
                 {
                     $checkPTRightHidden = 1;
                 }
             }
             if (isset($post_data['hide_pattern_two_right']) && $checkPTRightHidden == 0)
             {
                 $right_memorize_h_p_two = array();
                 for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                     $right_memorize_h_p_two[][0] = 1;
                 }
             }
             if ($right_memorize_h_p_two)
             {
                 $arr['right_memorize_h_p_two'] = $right_memorize_h_p_two;
             }
             if (isset($post_data['hide_pattern_two_left']) && $post_data['hide_pattern_two_left'] != '')
             {
                 $arr['hide_pattern_two_left'] = $post_data['hide_pattern_two_left'];
             }
             if (isset($post_data['hide_pattern_two_right']) && $post_data['hide_pattern_two_right'] != '')
             {
                 $arr['hide_pattern_two_right'] = $post_data['hide_pattern_two_right'];
             }
 
 
             $arr_data = $arr;
         }elseif ($post_data['pattern_type'] == 3)
         {
             
             
 
             $arr['box_quantity_whiteboard'] = $post_data['box_quantity_whiteboard'];
         
             $whiteboard_memorize_p_three = array();
             $question_step_memorize_p_three = array();
             $clueQuestionStep = array();
             $showExplanationStep = array();
 
             for ($i = 0; $i < $post_data['box_quantity_whiteboard']; $i++) {
                 $whiteboard_memorize_p_three[$i][0] = str_replace("&#39;","'",$post_data['whiteboard_memorize_p_three_'.($i+1)][0]);
 
             }
 
 
 
             for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
 
 
                 $k = 0;
                 for($j= 1;$j < 6;$j++){
                     $clueQuestion = str_replace("&#39;","'",$post_data['clueQuestionStep_'.($i+1).'_'.$j][0]);
                     if ($clueQuestion != null) {
                         $clueQuestionStep[$i][$k] = $clueQuestion;
                         $k = $k + 1;
                     }
 
                 }
 
                 $showExplanationStep[$i][0] = str_replace("&#39;","'",$post_data['showExplanationStep_'.($i+1)][0]);
 
                 $question_step_memorize_p_three[$i][0] = str_replace("&#39;","'",$post_data['question_step_memorize_p_three_'.($i+1)][0]);
                 $question_step_memorize_p_three[$i][1] = $clueQuestionStep[$i];
                 $question_step_memorize_p_three[$i][2] = $showExplanationStep[$i][0];
 
                 $question_step_memorize_p_three[$i][3]  = str_replace("&#39;","'",($post_data['wrong_answer'.($i+1)][0])?1:0);
                 // $question_step_memorize_p_three[$i][4]  = str_replace("&#39;","'",($post_data['wrong_answer'.($i+1)][0])?($i+1):0);
             }
 
 
             $arr['whiteboard_memorize_p_three'] =  $whiteboard_memorize_p_three;
             $arr['question_step_memorize_p_three'] =  $question_step_memorize_p_three;
             // $arr['clueQuestionStep']    =  $clueQuestionStep;
             // $arr['showExplanationStep'] =  $showExplanationStep;
             // echo "<pre>";print_r($arr);die();
             $arr_data = json_encode($arr);
             return $arr_data;
 
 
             // $left_memorize_h_p_three = array();
             // $right_memorize_h_p_three = array();
             // $checkPThreeLeftHidden = 0;
             // $checkPThreeRightHidden = 0;
             // for ($i = 0; $i < $post_data['box_quantity']; $i++) {
             //     $left_memorize_h_p_three[] = $post_data['left_memorize_h_p_three'][$i];
             //     if ($post_data['left_memorize_h_p_three'][$i] == 1)
             //     {
             //         $checkPThreeLeftHidden = 1;
             //     }
             // }
 
             // if (isset($post_data['hide_pattern_three_left']) && $checkPThreeLeftHidden == 0)
             // {
             //     $left_memorize_h_p_three = array();
             //     for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
             //         $left_memorize_h_p_three[] = 1;
             //     }
             // }
 
             // if ($left_memorize_h_p_three)
             // {
             //     $arr['left_memorize_h_p_three'] = $left_memorize_h_p_three;
             // }
             // for ($i = 0; $i < $post_data['box_quantity']; $i++) {
             //     $right_memorize_h_p_three[] = $post_data['right_memorize_h_p_three'][$i];
             //     if ($post_data['right_memorize_h_p_three'][$i] == 1)
             //     {
             //         $checkPThreeRightHidden = 1;
             //     }
             // }
             // if (isset($post_data['hide_pattern_three_right']) && $checkPThreeRightHidden == 0)
             // {
             //     $right_memorize_h_p_three = array();
             //     for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
             //         $right_memorize_h_p_three[] = 1;
             //     }
             // }
             // if ($right_memorize_h_p_three)
             // {
             //     $arr['right_memorize_h_p_three'] = $right_memorize_h_p_three;
             // }
             // if (isset($post_data['hide_pattern_three_left']) && $post_data['hide_pattern_three_left'] != '')
             // {
             //     $arr['hide_pattern_three_left'] = $post_data['hide_pattern_three_left'];
             // }
             // if (isset($post_data['hide_pattern_three_right']) && $post_data['hide_pattern_three_right'] != '')
             // {
             //     $arr['hide_pattern_three_right'] = $post_data['hide_pattern_three_right'];
             // }
             // $arr_data = $this->pattern_image_upload($arr);
         }elseif($post_data['pattern_type'] == 4)
         {
             $left_memorize_p_four = array();
             $left_memorize_h_p_four = array();
             $right_memorize_p_four = array();
             $right_memorize_h_p_four = array();
             $checkLeftHidden = 0;
             $checkRightHidden = 0;
 
 
             for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                 $left_memorize_p_four[] = $post_data['left_memorize_p_four'][$i];
             }
             if ($left_memorize_p_four)
             {
                 $arr['left_memorize_p_four'] = $left_memorize_p_four;
             }
             for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                 $left_memorize_h_p_four[] = $post_data['left_memorize_h_p_four'][$i];
                 if ($post_data['left_memorize_h_p_four'][$i] == 1)
                 {
                     $checkLeftHidden = 1;
                 }
             }
             if (isset($post_data['hide_pattern_four_left']) && $checkLeftHidden == 0)
             {
                 $left_memorize_h_p_four = array();
                 for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                     $left_memorize_h_p_four[] = 1;
                 }
             }
             if ($left_memorize_h_p_four)
             {
                 $arr['left_memorize_h_p_four'] = $left_memorize_h_p_four;
             }
 
             for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                 $right_memorize_p_four[] = $post_data['right_memorize_p_four'][$i];
             }
             if ($right_memorize_p_four)
             {
                 $arr['right_memorize_p_four'] = $right_memorize_p_four;
             }
             for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                 $right_memorize_h_p_four[] = $post_data['right_memorize_h_p_four'][$i];
                 if ($post_data['right_memorize_h_p_four'][$i] == 1)
                 {
                     $checkRightHidden = 1;
                 }
             }
             if (isset($post_data['hide_pattern_four_right']) && $checkRightHidden == 0)
             {
                 $right_memorize_h_p_four = array();
                 for ($i = 0; $i < $post_data['box_quantity']; $i++) {
 
                     $right_memorize_h_p_four[] = 1;
                 }
             }
             if ($right_memorize_h_p_four)
             {
                 $arr['right_memorize_h_p_four'] = $right_memorize_h_p_four;
             }
             if (isset($post_data['hide_pattern_four_left']) && $post_data['hide_pattern_four_left'] != '')
             {
                 $arr['hide_pattern_four_left'] = $post_data['hide_pattern_four_left'];
             }
             if (isset($post_data['hide_pattern_four_right']) && $post_data['hide_pattern_four_right'] != '')
             {
                 $arr['hide_pattern_four_right'] = $post_data['hide_pattern_four_right'];
             }
 
             $arr_data = $arr;
         }
         $combined_data = json_encode($arr_data);
         return $combined_data;
     }

     public function processTutorial($items)
     {
      error_report_check();  
      $arr = array();
      $array_one = array();
      if (!empty($items['speech_to_text'])) {
          $arr['speech_to_text'] = $items['speech_to_text'];
          foreach ($arr['speech_to_text'] as $key => $value) {
              if (!empty($value["speech_to_text"])) {
                  $v = [
                      "speech_to_text" =>$value["speech_to_text"]
                  ];


                  array_push($array_one, $v);
              }
              else{
                  $v = [
                      "speech_to_text" =>"none"
                  ];
                  array_push($array_one, $v);
              }
          }
      }
      

      if (isset($items['Image'])) {

          $arr['Image'] = $items['Image'];
          foreach ($arr['Image'] as $key => $value) {
              if (!empty($value["Image"])) {

                  $v = [
                      "image" => str_replace( base_url("/assets/uploads/"), "", $value["Image"] )
                  ];

                  array_push($array_one, $v);


              }
              else{
                  $v = [
                      "image" =>"none"
                  ];
                  array_push($array_one, $v);
              }
          }
      }
      
      $uType = $this->session->get('userType');
      
      $files = $_FILES;

    //   $validated = $this->validate([
    //         'Image' => [
    //             'mime_in[gif|jpg|png|jpeg]',
    //         ],
    //     ]);
        $tutorial_image=$this->request->getFileMultiple('Image');
        $audioFile_upload=$this->request->getFileMultiple('audioFile');
        // echo '<pre>';
        // print_r($audioFile_upload);die();
    // if(!$validated)
    // {    
    //     echo 'yes'; die();
    // }
    // else
    // {
    //     echo 'asce re';die();
        if (isset($tutorial_image)) {
            foreach($tutorial_image as $k => $img){ 
                  //$file_name=rand(99,9999).time().$img->getClientOriginalName();
                  $file_name=$img->getRandomName();
                  $image_name=$img->move(ROOTPATH .'public/assets/uploads/', $file_name);   
                  if (!$image_name) {
                      $status = 'error';
                      //$msg = $this->upload->display_errors('', '');
                      $var1 =[
                       "image"=>'none'
                     ];
 
                      array_push($array_one, $var1);
 
                  } else {
 
                     $arr['image'] = $file_name;
 
                     $var1 =[
                       "image"=>$file_name
                     ];
 
                     array_push($array_one, $var1);
                  }
                  
           }
        }

    // }
    // echo 'no dies';die();  

            //  $config['upload_path'] = 'assets/uploads/question_media/';
            //  $config['allowed_types'] = 'mp3|mp4|';
            //  $config['overwrite'] = false;

             //$this->upload->initialize($config);
            // echo '<pre>';
            // print_r($_FILES['audioFile']['name']);
            // die();
             if (  !empty($audioFile_upload)) {
                 foreach($audioFile_upload as $l => $audios){
 
                         $audios_name=$audios->getRandomName();
                         $audio_upload=$audios->move(ROOTPATH . 'public/assets/uploads/question_media/', $audios_name);

                         if (!$audio_upload) {
                             $status = 'error';
                             //$audio = $this->upload->display_errors('', '');
                             $var1 =[
                              "Audio"=>'none'
                            ];

                             array_push($array_one, $var1);
                         } else {
                             //$audioFiles = $this->upload->data();

                            $var2 =[
                              "Audio"=>$audios_name
                            ];

                            array_push($array_one, $var2);
                           
                         }
                  
                  }
      
             }


      return json_encode($array_one);
      
  }

   public function checkValidation($data)
   { 
    $return_data['flag'] = 1;
        if (isset($data['errorStoryWrite'])) {
            $return_data['msg'] = 'Need to include each part right or wrong answer both.';
            $return_data['flag'] = 0;
        }else{
            if($data['questionType']==20 && $data['answer'] =='write'){
                if ($data['studentgrade'] == '') {
                    $return_data['msg'] = 'Student Grade Need To Be Selected';
                    $return_data['flag'] = 0;
                } elseif ($data['subject'] == '') {
                    $return_data['msg'] = 'Subject Need To Be Selected';
                    $return_data['flag'] = 0;
                } elseif ($data['chapter'] == '') {
                    $return_data['msg'] = 'Chapter Need To Be Selected';
                    $return_data['flag'] = 0;
                } elseif ($data['questionName'] == '') {
                    $return_data['msg'] = 'Question Can Not Be empty';
                    $return_data['flag'] = 0;
                } elseif ($data['answer'] == '') {
                    $return_data['msg'] = 'Answer Can Not Be empty';
                    $return_data['flag'] = 0;
                } elseif ($data['question_solution'] == '') {
                    $return_data['msg'] = 'Solution Can Not Be empty';
                    $return_data['flag'] = 0;
                }
            }else if($data['questionType']==22){
                if ($data['studentgrade'] == '') {
                    $return_data['msg'] = 'Student Grade Need To Be Selected';
                    $return_data['flag'] = 0;
                } elseif ($data['subject'] == '') {
                    $return_data['msg'] = 'Subject Need To Be Selected';
                    $return_data['flag'] = 0;
                } elseif ($data['chapter'] == '') {
                    $return_data['msg'] = 'Chapter Need To Be Selected';
                    $return_data['flag'] = 0;
                } elseif ($data['questionName'] == '') {
                    $return_data['msg'] = 'Question Can Not Be empty';
                    $return_data['flag'] = 0;
                } elseif ($data['answer'] == '') {
                    $return_data['msg'] = 'Answer Can Not Be empty';
                    $return_data['flag'] = 0;
                } elseif ($data['question_solution'] == '') {
                    $return_data['msg'] = 'Solution Can Not Be empty';
                    $return_data['flag'] = 0;
                }
            }else{
                if ($data['studentgrade'] == '') {
                    $return_data['msg'] = 'Student Grade Need To Be Selected';
                    $return_data['flag'] = 0;
                } elseif ($data['subject'] == '') {
                    $return_data['msg'] = 'Subject Need To Be Selected';
                    $return_data['flag'] = 0;
                } elseif ($data['chapter'] == '') {
                    $return_data['msg'] = 'Chapter Need To Be Selected';
                    $return_data['flag'] = 0;
                } elseif ($data['questionName'] == '') {
                    $return_data['msg'] = 'Question Can Not Be empty';
                    $return_data['flag'] = 0;
                } elseif ($data['answer'] == '') {
                    $return_data['msg'] = 'Answer Can Not Be empty';
                    $return_data['flag'] = 0;
                } elseif ($data['question_solution'] == '') {
                    $return_data['msg'] = 'Solution Can Not Be empty';
                    $return_data['flag'] = 0;
                }                elseif ($data['questionMarks'] == '' && $data['questionType'] !=14) {
                    $return_data['msg'] = 'Marks Can Not Be empty';
                    $return_data['flag'] = 0;
                }
            }
        }
        return $return_data;
    }

   public function get_vocabulary_word_data()
    {
        $word = $this->request->getVar('word');

        error_reporting(E_ALL);
        ini_set('display_errors',1);
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, "http://18.222.70.201:3000/?word=".$word."");
        curl_setopt($ch,CURLOPT_POST,true);

        //curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_TIMEOUT ,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        // $output contains the output string
        $data = curl_exec($ch);
        // close curl resource to free up system resources
        curl_close($ch);
        echo json_encode($data);
    }

     //    Question Edit Option
     public function question_edit($type, $question_id)
     { 
    
         //echo $question_id;die();   
         error_report_check();   
         $TutorClass=new \TutorClass();
         $AdminClass=new \AdminClass();
         $QuestionClass=new \QuestionClass();   

         if (!empty($_SESSION["has_edit"])) {
             $data["has_back_button"] =$_SESSION["has_edit"];
             
         }
         else{
             unset($_SESSION["has_edit"]);
         }
         
         
         $data['question_info'] = $TutorClass->getQuestionInfo($type, $question_id);
    
         // echo '<pre>';print_r($data['question_info']);die;
         $data['question_item'] = $type;
         $data['question_id'] = $question_id;
         $data['question_tutorial'] = $TutorClass->getInfo('tbl_question_tutorial', 'question_id', $question_id); 
         $user_id = $this->session->get('user_id');
         $data['all_grade'] = $TutorClass->getAllInfo('tbl_studentgrade');
         $data['all_subject'] = $TutorClass->getInfo('tbl_subject', 'created_by', $user_id);
         $subject_id = $data['question_info'][0]['subject'];

         $data['subject_base_chapter'] = $TutorClass->getInfo('tbl_chapter', 'subjectId', $subject_id); 

         if (count($data['question_info'])) {
             $data['allCountry'] = $this->db->table('tbl_country')->get()->getResultArray();
             $data['selCountry'] = $data['question_info'][0]['country'];
             $quesSub = $data['question_info'][0]['subject'];
             $quesChap = $data['question_info'][0]['chapter'];
             $chaps = $this->get_chapter_name($quesSub, $quesChap); //selected $quesChap
             $temp = [
                 'subject' =>$data['question_info'][0]['subject'],
                 'chapter' =>$chaps,
                 'selChapter' =>$quesChap,
                 'studentGrade' =>$data['question_info'][0]['studentgrade'],
             ];
             $this->session->set('refPage', 'questionEdit');
             $this->session->set('modInfo', $temp);
         }
       
         $qSearchParams = [
             'questionType' =>$type,
             'user_id' =>$this->session->get('user_id'),
             'country' =>$this->session->get('selCountry'),
         ];

        //  echo '<pre>';
        //  print_r($qSearchParams);die();
         //echo $question_id.'//';
         $allQuestionIds = $QuestionClass->search('tbl_question', $qSearchParams);
         $allQuestionIds = array_column($allQuestionIds, 'id');
         // print_r($allQuestionIds);
         $data['qIndex'] = array_search($question_id, $allQuestionIds);
        //  echo "<pre>";
        //  print_r($allQuestionIds);die();

         //echo $data['qIndex'].'//';die();
         //if ques not found by loggedUserId and ques type then redirect to list
         if (!is_int($data['qIndex'])) {
             return redirect()->to(base_url('question-list'));
         } else {
             $data['qIndex'] += 1;
         }
 
         
         $question_box = 'question_edit/question-box';
         if ($type == 1) {
            return view('question_edit/question-box/edit_general',$data); 
         }
         if ($type == 2) {
             return view('question_edit/question-box/edit_true-false',$data); 
         }
         if ($type == 3) {
             $que_module_check = $TutorClass->getInfo('tbl_modulequestion', 'question_id', $question_id);
             if(count($que_module_check) > 0){
                 $data['question_module_check'] = $que_module_check;
                 //echo "<pre>";print_r($que_module_check);die();
             }
             $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
             return view('question_edit/question-box/edit_vocubulary',$data); 
         }
         if ($type == 4) {
             // $keyword = "match.therssoftware.com";
             // $questionList = $this->db->select('*')->from('tbl_question')->where('questionType',4)->where("questionName LIKE '%$keyword%'")->get()->result_array();
             
             // foreach($questionList as $key => $val){
             //     $questionName = json_decode($val['questionName'],true);
             //     foreach($questionName['vocubulary_image'] as $k => $vi){
             //       $image = str_replace('http://match.therssoftware.com/qStudy/','https://q-study.com/',$vi[0]);
             //       $questionName['vocubulary_image'][$k][0] = $image;
             //     }
             //     $id = $val['id'];   
             //     $x = json_encode($questionName);
             //     $this->db->where('id',$id)->update('tbl_question',['questionName'=>$x]);
             //     // echo "<pre>";print_r($x);die();
                 
             // }
             
             $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);

             return view('question_edit/question-box/edit_multiple_choice',$data); 
         }
         if ($type == 5) {
             $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
             // echo "<pre>";print_r($data);die();
             $question_box .= '/edit_multiple_response';
         }
         if ($type == 7) {
             $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
             $data['question_answer'] = json_decode($data['question_info'][0]['answer']);
             $question_box .= '/edit_matching';
         }
         if ($type == 6) {
             $quesInfo1 = json_decode($data['question_info'][0]['questionName']);
             $items = $this->indexQuesAns($quesInfo1->skp_quiz_box);
             $data['numOfRows'] = $quesInfo1->numOfRows;
             $data['numOfCols'] = $quesInfo1->numOfCols;
             $data['skp_box'] = $this->renderSkpQuizPrevTable($items, $data['numOfRows'], $data['numOfCols'], $showAns = 1, 'edit');
 
             $data['questionBody'] = $quesInfo1->question_body;
             return view('question_edit/question-box/edit_skip_quiz',$data); 
         } if ($type == 8) {
             $this->edit_assignment_question($data);
         } if ($type == 9) {
             
             
             $info = array();
             $titles = array();
             $title = array();
 
             // print_r(json_decode($data['question_info'][0]['questionName'])); die();
             //title
 
             $wrongTitles = json_decode($data['question_info'][0]['questionName'] , true);
             $wrongTitless = $wrongTitles['wrongTitles'];
             foreach ($wrongTitless as $key => $value) {
                 $title[0] = $value;
                 $title[1] = $wrongTitless[$key];
                 $title[2] = $key;
                 $titles[] = $title;
             }
 
             if (count($titles) > 1 ) {
                 $data['titles_on'] = 1;
             }
             
 
             $title[0] = json_decode($data['question_info'][0]['questionName'])->rightTitle;
             $title[1] = "right_ones_xxx";
             $title[2] = "noWrongTitle";
             $titles[] = $title;
             shuffle($titles);
             $info['titles'] = $titles;
 
             //intro
 
             $titles = array();
             $title = array();
 
             foreach (json_decode($data['question_info'][0]['questionName'])->wrongIntro as $key => $value) {
                 $title[0] = $value;
                 $title[1] = json_decode($data['question_info'][0]['questionName'])->wrongIntroIncrement[$key];
                 $title[2] = $key;
                 $titles[] = $title;
             }
 
             if (count($titles) > 1 ) {
                 $data['intro_on'] = 1;
             }
             
             $title[0] = json_decode($data['question_info'][0]['questionName'])->rightIntro;
             $title[1] = "right_ones_xxx";
             $title[2] = "noWrongTitle";
             $titles[] = $title;
             shuffle($titles);
             $info['Intro'] = $titles;
 
             //picture
 
             $titles = array();
             $title = array();
 
             foreach (json_decode($data['question_info'][0]['questionName'])->pictureList as $key => $value) {
                 $title[0] = $value;
                 $title[1] = "wrong_ones_xxx";
                 $title[2] = $key;
                 $titles[] = $title;
             }
 
             if (count($titles) > 1 ) {
                 $data['picture_on'] = 1;
             }
 
             $title[0] = json_decode($data['question_info'][0]['questionName'])->lastpictureSelected;
             $title[1] = "right_ones_xxx";
             $title[2] = "noWrongTitle";
             $titles[] = $title;
             shuffle($titles);
             $info['picture'] = $titles;
 
             //paragraph
             $paragraph = json_decode($data['question_info'][0]['questionName'] , true);
             $paragraph = $paragraph['Paragraph'];
 
             $info['paragraph'] = $paragraph;
 
             //picture
 
             $titles = array();
             $title = array();
 
             foreach (json_decode($data['question_info'][0]['questionName'])->wrongConclution as $key => $value) {
                 $title[0] = $value;
                 $title[1] = "wrong_ones_xxx";
                 $title[2] = $key;
                 $titles[] = $title;
             }
 
             if (count($titles) > 1 ) {
                 $data['conclusion_on'] = 1;
             }
 
             $title[0] = json_decode($data['question_info'][0]['questionName'])->rightConclution;
             $title[1] = "right_ones_xxx";
             $title[2] = "noWrongTitle";
             $titles[] = $title;
             shuffle($titles);
 
             $info['conclution'] = $titles;
             $data['question'] = $info;
             $data['question_answer'] = json_decode($data['question_info'][0]['answer']);
 
             $question_box .= '/edit_storyWrite';
             
         } if ($type == 10) {
           
             $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName'], true);
             $data['question_answer'] = json_decode($data['question_info'][0]['answer'], true);
             return view('question_edit/question-box/edit_times_table',$data); 
         } if ($type == 11) {
             $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName'], true);
             $data['question_answer'] = json_decode($data['question_info'][0]['answer'], true);
             return view('question_edit/question-box/edit_algorithm',$data);  
         } if ($type == 12) {
             $question_box .= '/workout_quiz';
         } if ($type == 13) {
             $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
             $question_box .= '/edit_matching_workout';
         }if ($type == 14) {
             // $last_id = $this->tutor_model->tutor_update(5620);
             // print_r(); die();
             $data['tutor_edit'] = $TutorClass->tutor_edit($type, $question_id);
             
             $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
             return view('question_edit/question-box/edit_tutor_view',$data);
         }if ($type == 15)
        {
            $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
            return view('question_edit/question-box/edit_workout_quiz_two',$data); 
        }
        if ($type == 16)
         {
             
             
             $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
             // echo "<pre>";print_r($data['question_info_ind'] ); die();
             return view('question_edit/question-box/edit_memorization',$data); 
         }
         if ($type == 17)
         {
             $data['idea_info'] = $TutorClass->getIdeaInfoByQuestion($question_id);
             $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
             $data['q_creator_name'] = $TutorClass->getIQuestionCreator($question_id);
             //echo "<pre>";print_r($data['q_creator_name'] ); die();
 
             $this->db->table('idea_save_temp')->truncate();
             $data['ideas'] = $TutorClass->getIdeasByQuestion($question_id);
             return view('question_edit/question-box/edit_creative_quiz',$data); 
         }
         if ($type == 18)
         {
             $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
             return view('question_edit/question-box/edit_sentence_match',$data); 
         }
         if ($type == 19)
         {
             $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
             return view('question_edit/question-box/edit_word_memorize',$data); 
         }
          if ($type == 20)
        {
            $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
            return view('question_edit/question-box/edit_comprehension',$data); 
        }
        if ($type == 21)
        {
            $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
            return view('question_edit/question-box/edit_grammer',$data); 
        }
        if ($type == 22)
        {
            $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
            return view('question_edit/question-box/edit_glossary',$data); 
        }
        if ($type == 23)
        {
            $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
            return view('question_edit/question-box/edit_imageQuiz',$data); 
        }
         
        //  if ($type != 8) {
            
        //      $data['question_box'] = $this->load->view($question_box, $data, true);
 
        //      $data['headerlink'] = $this->load->view('dashboard_template/headerlink', $data, true);
        //      $data['header'] = $this->load->view('dashboard_template/header', $data, true);
        //      $data['footerlink'] = $this->load->view('dashboard_template/footerlink', $data, true);
 
        //      $data['maincontent'] = $this->load->view('question_edit/edit_question', $data, true);
        //      $this->load->view('master_dashboard', $data);
        //  }
     }

     public function update_question_data()
    {
        // echo '<pre>';
        // print_r($_SESSION);die();
        error_report_check();
        $TutorClass=new \TutorClass();
        $post = $this->request->getVar();
        //echo "<pre>";print_r($post);die();
        //$clean = $this->security->xss_clean($post);
        //$clean['media'] = isset($_FILES)?$_FILES:[];
        
        $instruction_link = str_replace('</p>', '', $post['question_instruction']);
        $instruction_array = array_filter(explode('<p>', $instruction_link));
        
        $instruction_new_array = array();
        foreach ($instruction_array as $row) {
            $instruction_new_array[] = strip_tags($row);
        }


        $video_link = isset($post['question_video']) ? $post['question_video'] :'';
        $video_link = str_replace('</p>', '', $video_link);
        $video_array = array_filter(explode('<p>', $video_link));
    
        $video_new_array = array();
        foreach ($video_array as $row) {
            $video_new_array[] = strip_tags($row);
        }
        
        $data['questionType'] = $this->request->getVar('questionType');
        $question_id = $this->request->getVar('question_id');
        
        $questionName = $this->request->getVar('questionName');
        $answer = $this->request->getVar('answer');
        $description = $this->request->getVar('questionDescription');

 
        $module_status = $this->session->get('module_status');
        $module_edit_id = $this->session->get('module_edit_id');
        $param_module_id = $this->session->get('param_module_id');

        // echo 'sssssssssss/'.$module_status;die();
        if ($module_status==1) {
            if(!empty($module_edit_id)){
               //echo "kkkkk";
                $module_update['question_id'] = $question_id;
                $module_update['question_type'] = $_POST['questionType'];
                
                $this->db->table('tbl_pre_module_temp')->where('id', $module_edit_id)->update($module_update);
                
            }else{
                $builder = $this->db->table('tbl_pre_module_temp ita');
                $builder->select('*');
                $query_result = $builder->get();
                $results = $query_result->getResultArray();
                $question_no = count($results);
                $question_order = $question_no+1;
                $module_insert['question_id'] = $question_id;
                $module_insert['question_type'] = $_POST['questionType'];
                $module_insert['question_order'] = $question_order;
                $module_insert['question_no'] = $question_no;
                $module_insert['question_no'] = $this->request->getVar('country');
                $this->db->table('tbl_pre_module_temp')->insert($module_insert);
            }
            
            // echo $this->db->last_query(); die();
        }elseif($module_status==2){
     
            if(!empty($module_edit_id)){
        
                $module_update['question_id'] = $question_id;
                $module_update['question_type'] = $_POST['questionType'];
                
                $this->db->table('tbl_edit_module_temp')->where('id', $module_edit_id)->update( $module_update);
                
            }else{
                
           
                $builder = $this->db->table('tbl_edit_module_temp');
                $builder->select('*');
                $query_result = $builder->get();
                $results = $query_result->getResultArray();
                $question_no = count($results);
                $question_order = $question_no+1;

                $module_insert['module_id'] = $param_module_id;
                $module_insert['question_id'] = $question_id;
                $module_insert['question_type'] = $_POST['questionType'];
                $module_insert['question_order'] = $question_order;
                $module_insert['question_no'] = $question_no;
                $module_insert['country'] = $this->request->getVar('country');
                // echo '<pre>';
                // print_r($module_insert);
                // die();
                $this->db->table('tbl_edit_module_temp')->insert($module_insert);
            }
        }
       
        if ($data['questionType'] == 14) {
         
            $questionName =  $this->processTutorial($post);
            // $last_id = $this->tutor_model->last_id();
            $data["last_id"] = "1000";
        }
        
        if ($data['questionType'] == 3) {
            $questionName =  $this->processVocabulary($post);

        } if ($_POST['questionType']==4) {
            $data['question_name_type'] = $this->request->getVar('question_name_type');
            if (isset($_POST['questionName_1_checkbox']) && isset($_POST['questionName_2_checkbox'])) {

                $_POST['questionName'] = $_POST['questionName'];
                $data['question_name_type'] = 2;
            }else{
              $_POST['questionName'] = !empty($_POST['questionNameClick']) ? $_POST['questionNameClick'] : $_POST['questionName'];  
            }
            
            // if ($_POST['questionNameClick'] !='') {
            //     $_POST['questionName'] = $_POST['questionNameClick'];
            // }
            
            // $data['question_name_type'] = $this->input->post('question_name_type');
            
            $questionName = $this->save_multiple_choice($_POST);
            // $answer = $_POST['response_answer'];
            $answer = json_encode($_POST['response_answer']);
        } if ($_POST['questionType']==5) {
            //Same as Multiple Choice
            $questionName = $this->save_multiple_response($_POST);
            $answer = json_encode($_POST['response_answer']);
        } if ($data['questionType']==6) {
            //skip quiz
            $temp['question_body'] = isset($post['question_body'])?$post['question_body']:'';
            $temp['skp_quiz_box'] = $post['ques_ans'];
            $temp['numOfRows']     = isset($post['numOfRows']) ? $post['numOfRows'] : 0;
            $temp['numOfCols']     = isset($post['numOfCols']) ? $post['numOfCols'] : 0;
            $questionName =  json_encode($temp);
            $answer = json_encode(array_values(array_filter($post['ans'])));
        } if ($_POST['questionType']==7) {
            $questionName = $this->ques_matching_data($_POST);
            $answer = $this->ans_matching_data($_POST);
        } if ($data['questionType'] == 8) {
            // assignment
            $temp         = $this->processAssignmentTasks($post);
            $questionName = json_encode($temp);
        } if ($_POST['questionType']==9) {

           $x = $TutorClass->getQuestionInfo(9, $question_id);
            $ques_name = json_decode($x[0]['questionName'] , true);                                                                            

            if(isset($_POST['rightTitle']) || !empty($_POST['rightTitle']) ){
                $question_data['rightTitle'] = $_POST['rightTitle'];
            }
            if(!isset($_POST['rightTitle']) || empty($_POST['rightTitle']) ){
                $question_data['rightTitle'] = $ques_name['rightTitle'];
            }
            if(isset($_POST['rightIntro']) || !empty($_POST['rightIntro']) ){
                $question_data['rightIntro'] = $_POST['rightIntro'];
            }
            if(!isset($_POST['rightIntro']) || empty($_POST['rightIntro']) ){
                $question_data['rightIntro'] = $ques_name['rightIntro'];
            }
            if(isset($_POST['lastpictureSelected']) || !empty($_POST['lastpictureSelected']) ){
                $question_data['lastpictureSelected'] = $_POST['lastpictureSelected'];
            }
            
            if(isset($_POST['rightConclution']) || !empty($_POST['rightConclution']) ){
                $question_data['rightConclution'] = $_POST['rightConclution'];
            }
            if(!isset($_POST['rightConclution']) || empty($_POST['rightConclution']) ){
                $question_data['rightConclution'] = $ques_name['rightConclution'];
            }

            $wrongTitles = array();
            $wrongTitlesIncrement = array();

            if(!isset($_POST['lastpictureSelected']) ){
                $question_data['lastpictureSelected'] = $ques_name['lastpictureSelected'];
            }

            foreach ($ques_name['wrongTitlesIncrement'] as $key => $value) {
                    $wrongTitlesIncrement[] = $value;
             }

             foreach ($ques_name['wrongTitles'] as $key => $value) {
                    $wrongTitles[] = $value;
             }

            if(isset($_POST['wrongTitles']) || !empty($_POST['wrongTitles'])  ){
                foreach ($_POST['wrongTitles'] as $key => $value) {
                    $wrongTitles[] = $value;
                }
                foreach ($_POST['wrongTitlesIncrement'] as $key => $value) {
                    $wrongTitlesIncrement[] = $value;
                }
            }

             $question_data['wrongTitles'] = $wrongTitles;
             $question_data['wrongTitlesIncrement'] = $wrongTitlesIncrement;

             $wrongIntro = array();
             $wrongIntroIncrement = array();

             foreach ($ques_name['wrongIntro'] as $key => $value) {
                    $wrongIntro[] = $value;
             }
             foreach ($ques_name['wrongIntroIncrement'] as $key => $value) {
                    $wrongIntroIncrement[] = $value;
             }

            if(isset($_POST['wrongIntro'])  ){
                
                foreach ($_POST['wrongIntro'] as $key => $value) {
                    $wrongIntro[] = $value;
                }
                foreach ($_POST['wrongIntroIncrement'] as $key => $value) {
                    $wrongIntroIncrement[] = $value;
                }
            }

             $question_data['wrongIntro'] = $wrongIntro;
             $question_data['wrongIntroIncrement'] = $wrongIntroIncrement;

             $pictureList = array();
             $wrongPictureIncrement = array();
             $PuzzleParagraph = array();

             foreach ($ques_name['pictureList'] as $key => $value) {
                    $pictureList[] = $value;
             }
             foreach ($ques_name['wrongPictureIncrement'] as $key => $value) {
                    $wrongPictureIncrement[] = $value;
             }

             if(isset($_POST['pictureList']) ){
                foreach ($_POST['pictureList'] as $key => $value) {

                    if ( isset($question_data['lastpictureSelected']) && $value != $question_data['lastpictureSelected'] ) {
                        $pictureList[] = $value;
                    }
                }

                foreach ($_POST['wrongPictureIncrement'] as $key => $value) {
                    $wrongPictureIncrement[] = $value;
                }
            }

            $question_data['wrongPictureIncrement'] = $wrongPictureIncrement;
            $question_data['pictureList'] = $pictureList; 

            $paragraph = array();
            $PuzzleParagraph = array();
            $wrongParagraphIncrement = array();

            foreach ($ques_name['wrongParagraphIncrement'] as $key => $value) {

                $wrongParagraphIncrement[$key] = $value;
                $i = $key;
            }

            if (isset($post['wrongParagraphIncrement'])) {
                foreach ($post['wrongParagraphIncrement'] as $key => $value) {
                    $wrongParagraphIncrement[$i+1] = $value;
                    $i++;
                }
            }
            $i = 0;

            $question_data['wrongParagraphIncrement'] = $wrongParagraphIncrement;  

            $para = $ques_name['Paragraph'];
            foreach ($para as $index => $value) {
                foreach ($value as $key => $val) {
                    if (count($val) == 0) {
                        unset($para[$index][$key]);
                    }
                }
            }
            foreach ($para as $index => $value) {
                if (count($value) == 0) {
                    unset($para[$index]);
                 }
            }

            $i = 1;

            foreach ($para as $key => $value) {
                $paragraph[$i] = $value;
                $i++;
            }

            if (isset($_POST['Paragraph'])) {
                foreach ($_POST['Paragraph'] as $key => $value) {
                    if ($value != "" ) {
                        $paragraph[$i] = $value;
                        $i++;
                    }
                }
            }
            $question_data['Paragraph'] = $paragraph; 

            foreach ($ques_name['wrongConclution'] as $key => $value) {
                $wrongConclution[] =  $value;
            }   

            if (isset($_POST['wrongConclution'] ) ) {
                foreach ($_POST['wrongConclution'] as $key => $value) {
                    $wrongConclution[] =  $value;
                }
            }

            $question_data['wrongConclution'] = $wrongConclution; 

            $wrongConclutionIncrement = array();
            if (isset($ques_name['wrongConclutionIncrement'])) {
                foreach ($ques_name['wrongConclutionIncrement'] as $key => $value) {
                    $wrongConclutionIncrement[] = $value; 
                }
            }
            if (isset($_POST['wrongConclutionIncrement'])) {
                foreach ($_POST['wrongConclutionIncrement'] as $key => $value) {
                    $wrongConclutionIncrement[] = $value; 
                }
            }
            $question_data['wrongConclutionIncrement'] = $wrongConclutionIncrement;

            if (!empty($question_data['rightTitle']) && !empty($question_data['rightIntro']) && !empty($question_data['lastpictureSelected']) && !empty($question_data['Paragraph']) && !empty($question_data['rightConclution']) && !empty($question_data['wrongTitles']) && !empty($question_data['pictureList']) && !empty($question_data['wrongIntro']) && !empty($question_data['wrongConclution']) ) { 

                $questionName = json_encode($question_data); 
            }else{
                $questionName ="";
            }
             

        } if ($data['questionType'] == 10) {
            $question_data['questionName'] = $post['questionName'];
            $question_data['factor1'] = $post['factor1'];
            $question_data['factor2'] = $post['factor2'];
            $questionName = json_encode($question_data);
            
            $answer = json_encode($post['result']);
        } if ($data['questionType'] == 11) {
            $question_data['questionName'] = $post['question_body'];
            $question_data['operator'] = $post['operator'];
            
            if ($post['operator'] == '/') {
                $question_data['divisor'] = $post['divisor'];
                $question_data['dividend'] = $post['dividend'];
                $question_data['remainder'] = $post['remainder'];
                $question_data['quotient'] = $post['quotient'];
                
                $answer = json_encode($post['remainder']);
            } if ($post['operator'] != '/') {
                $question_data['item'] = $post['item'];
                $answer = json_encode($post['result']);
            }
            
            $question_data['numOfRows']     = isset($post['numOfRows']) ? $post['numOfRows'] : 0;
            $question_data['numOfCols']     = isset($post['numOfCols']) ? $post['numOfCols'] : 0;
            
            $questionName = json_encode($question_data);
        }if ($data['questionType'] == 15)
            {
                $questionName = $this->save_workout_two($_POST);
                if (isset($_POST['response_answer'])) {
                    $answer = $_POST['response_answer'];
                }
        }if ($data['questionType'] == 16)
        {
            $questionName = $this->save_memorization($_POST);
            if (isset($_POST['answer'])) {
                $answer = $_POST['answer'];
            }else
            {
                $answer = '';
            }
        }if ($data['questionType'] == 18)
        {
            $answers = $_POST['answer'];

            if(!empty($answers)){
            $questions= array();
            $ans= array();

            foreach($answers as $answer){
                $ans_with_ques = explode (",,", $answer);
                $questions[]= $ans_with_ques[0];
                $ans[]= $ans_with_ques[1];
            }
            }

            $questionName= json_encode($questions);
            $answer = json_encode($ans);
            $questionMarks=15;
            $description='';
            $instruction_new_array='';
            $video_new_array='';
            $solution = 'Nothing';
            // echo "<pre>";print_r($answer);
            // echo "<pre>";print_r($questionName);
            // die();
        }if ($data['questionType'] == 19)
        {
            $answers = $_POST['answer'];

            if(!empty($answers)){
            $questions= array();
            $ans= array();


            foreach($answers as $answer){
                $ans_with_ques = explode (",,", $answer);
                $questions[]= $ans_with_ques[0];
                $ans[]= $ans_with_ques[1];
            }
            }
            $mydata['questions'] = $questions;
            $mydata['wrong_questions'] = $post['wrong_question'];
            // print_r($mydata);die();
            $questionName= json_encode($mydata);
            $answer = json_encode($ans);
            $questionMarks=15;
            $description='';
            $instruction_new_array='';
            $video_new_array='';
            $solution = 'Nothing';
        }
         if ($_POST['questionType'] == 20) {
            if(!empty($post['option_check'])){
               $answer = $post['option_check'][0];
            }else{
               $answer = "write";
            }
            if(!empty($post['com_question'])){
                $questionName = $post['com_question'];
            }else{
                $questionName = "";
            }
            
            $com_data = array();
            $com_data['options'] = $post['options'];
            $com_data['first_hint'] = $post['first_hint'];
            $com_data['total_rows'] = $post['total_rows'];
            $com_data['title_colors'] = $post['title_colors'];
            $com_data['second_hint'] = $post['second_hint'];
            $com_data['writing_input'] = $post['writing_input'];
            $com_data['text_one_hint'] = $post['text_one_hint'];
            $com_data['text_two_hint'] = $post['text_two_hint'];
            $com_data['image_ques_body'] = $post['image_ques_body'];
            $com_data['option_hint_set'] = $post['option_hint_set'];
            $com_data['text_one_hint_no'] = $post['text_one_hint_no'];
            $com_data['text_two_hint_no'] = $post['text_two_hint_no'];
            $com_data['note_description'] = $post['note_description'];
            $com_data['text_one_hint_color'] = $post['text_one_hint_color'];
            $com_data['text_two_hint_color'] = $post['text_two_hint_color'];
            $com_data['question_title_description'] = $post['question_title_description'];

            $description = json_encode($com_data);
        }
        if ($_POST['questionType'] == 21) {
            $answer = $post['option_check'][0];

            if(!empty($post['com_question'])){
                $questionName = $post['com_question'];
            }else{
                $questionName = "";
            }

            $grammer_data = array();
            $grammer_data['options'] = $post['options'];
            $grammer_data['hint_text'] = $post['hint_text'];
            $grammer_data['total_rows'] = $post['total_rows'];
            $grammer_data['second_hint'] = $post['second_hint'];
            $grammer_data['first_hint'] = $post['first_hint'];
            $grammer_data['second_hint'] = $post['second_hint'];
            $grammer_data['third_hint'] = $post['third_hint'];
            $grammer_data['four_hint'] = $post['four_hint'];
            $grammer_data['color_serial'] = $post['color_serial'];
            $grammer_data['writing_input'] = $post['writing_input'];
            $grammer_data['note_description'] = $post['note_description'];
            $grammer_data['text_one_hint_color'] = $post['text_one_hint_color'];
            $grammer_data['text_two_hint_color'] = $post['text_two_hint_color'];
            $grammer_data['text_four_hint_color'] = $post['text_four_hint_color'];
            $grammer_data['text_three_hint_color'] = $post['text_three_hint_color'];
            $grammer_data['question_title_description'] = $post['question_title_description'];

            $description = json_encode($grammer_data);
        }

        if ($_POST['questionType'] == 22) {
            
            $glossary_data['title_color'] = $post['title_color'];
            $glossary_data['question_title_description'] = $post['question_title_description'];
            $glossary_data['image_ques_body'] = $post['image_ques_body'];

            $questionName = 'no';
            $answer= 'no';
            $questionMarks = 1;
            $description = json_encode($glossary_data);
        }
        if ($_POST['questionType'] == 23) {
            
            $image_data['image_type_one'] = $post['image_type_one'];
            $image_data['image_type_two'] = $post['image_type_two'];
            $image_data['image_type_three'] = $post['image_type_three'];

            if($image_data['image_type_one']==1){
                $answer = $post['answer_one'];
            }else if($image_data['image_type_two']==1){
                $answer = $post['answer_two'];
            }else if($image_data['image_type_three']==1){
                $answer = $post['answer_three'];
            }
            
            $check_write =1;
            foreach($_POST['options'] as $option){
               if(!empty($option)){
                $check_write =2;
               }
            }

            if($check_write==2){
               $answer = $answer;
            }else{
               $answer = "write";
               $questionMarks = 0;

            }
           
            $image_data['box_one_image'] = $post['box_one_image'];
            $image_data['box_two_image'] = $post['box_two_image'];
            $image_data['box_three_image'] = $post['box_three_image'];

            $image_data['hint_one_image'] = $post['hint_one_image'];
            $image_data['hint_two_image'] = $post['hint_two_image'];
            $image_data['hint_three_image'] = $post['hint_three_image'];

            $image_data['help_check_one'] = $post['help_check_one'];
            $image_data['help_check_two'] = $post['help_check_two'];
            $image_data['help_check_three'] = $post['help_check_three'];
            $image_data['question'] = $post['question'];

            $image_data['total_rows'] = $post['total_rows'];
            $image_data['options'] = $post['options'];
            $image_data['quiz_explaination'] = $post['quiz_explaination'];
            
            $questionName = $post['quiz_question'];

            // echo "<pre>"; print_r($image_data);die();
            $description = json_encode($image_data);
        }

        
        $data['studentgrade'] = $this->request->getVar('studentgrade');
        $data['user_id'] = $this->session->get('user_id');
        $data['subject'] = $this->request->getVar('subject');
        $data['chapter'] = $this->request->getVar('chapter');
        $data['country'] = $this->request->getVar('country');
        $data['questionName'] = $questionName;
        $data['answer'] = $answer;
        $data['questionMarks'] = $this->request->getVar('questionMarks');
        $data['questionDescription'] = $description;
        if($_POST['questionType'] == 18){
            $data['question_instruction'] = $post['question_instruct'];
        }else{
            $data['question_instruction'] = json_encode($instruction_new_array);
        }
        $data['question_video'] = json_encode($video_new_array);
        $data['isCalculator'] = $this->request->getVar('isCalculator');
        $data['question_solution'] = $this->request->getVar('question_solution');
        //echo "<pre>";print_r($data);die();
        if ($data['questionType'] == 15) {
            $data['question_solution'] = $this->request->getVar('solution');
        }

        if ($data['questionType'] == 14) {
            $array_one = array();
            $array_two = array();
            $array_three = array();


            if (!empty($data["last_id"])) {

                

                $data['questionMarks'] = "0";
                // 
                // $questionId = $this->tutor_model->insertId('tbl_question', $data);

                $hour   =  $this->request->getVar('hour');
                $minute =  $this->request->getVar('minute');
                $second =  $this->request->getVar('second');

                $data['questionTime'] = $hour.":".$minute.":".$second;

                $TutorClass->updateInfo('tbl_question', 'id', $question_id, $data);

                

                $last_id = $TutorClass->tutor_update($question_id);
                

                foreach (json_decode($data['questionName']) as $key => $value) {
                    if (!empty($value->speech_to_text)) {
                        $var = [

                            "speech_to_text"=>$value->speech_to_text

                        ];

                        array_push($array_one, $var);
                    }
                }

                foreach (json_decode($data['questionName']) as $key => $value) {
                    if (!empty($value->image)) {
                        $var = [

                            "image"=>$value->image

                        ];
                         array_push($array_two, $var);
                    }
                }

                foreach (json_decode($data['questionName']) as $key => $value) {
                    if (!empty($value->Audio)) {
                        $var = [

                            "Audio"=>$value->Audio

                        ];
                         array_push($array_three, $var);
                    }
                }

                $a = count($array_one);

                $builder = $this->db->table('for_tutorial_tbl_question');  
                $builder->select('orders');
                $builder->orderBy("id", "DESC");
                $builder->limit(1); 
                $query = $builder->get();
                $orders=$query->getResultArray();

                $b = $orders[0]["orders"]+1;

                for ($i=0; $i < $a ; $i++) { 

                    $this->db->query('INSERT INTO `for_tutorial_tbl_question`(`speech`, `img`, `audio`, `tbl_ques_id` ,`orders` ) VALUES ("'.$array_one[$i]["speech_to_text"].'", "'.$array_two[$i]["image"].'" ,  "'.$array_three[$i]["Audio"].'", '.$last_id[0]["id"].', '.$b.' )');

                    $b++;

                   
                }

                echo "update";
                 
            }
        }if ($data['questionType'] == 17) {
            
            

            if(isset($post['short_question_allow'])){
                $short_question_allow = $post['short_question_allow'];
            }else{
                $short_question_allow='';
            }
            if(isset($post['shot_question_title'])){
                $shot_question_title = $post['shot_question_title'];
            }else{
                $shot_question_title = '';
            }
            if(isset($post['short_ques_body'])){
                $short_ques_body = $post['short_ques_body'];
            }else{
                $short_ques_body = '';
            }
            if(isset($post['large_question_allow'])){
                $large_question_allow = $post['large_question_allow'];
            }else{
                $large_question_allow = '';
            }
            if(isset($post['large_question_title'])){
                $large_question_title = $post['large_question_title'];
            }else{
                $large_question_title = '';
            }
            if(isset($post['large_ques_body'])){
                $large_ques_body = $post['large_ques_body'];
            }else{
                $large_ques_body = '';
            }
            if(isset($post['student_title'])){
                $student_title = $post['student_title'];
            }else{
                $student_title = '';
            }
            if(isset($post['word_limit'])){
                $word_limit = $post['word_limit'];
            }else{
                $word_limit = '';
            }
            if(isset($post['time_hour'])){
                $time_hour = $post['time_hour'];
            }else{
                $time_hour = '';
            }
            if(isset($post['time_min'])){
                $time_min = $post['time_min'];
            }else{
                $time_min = '';
            }
            if(isset($post['time_sec'])){
                $time_sec = $post['time_sec'];
            }else{
                $time_sec = '';
            }
            if(isset($post['allow_idea'])){
                $allow_idea = $post['allow_idea'];
            }else{
                $allow_idea = '';
            }
            if(isset($post['add_start_button'])){
                $add_start_button = $post['add_start_button'];
            }else{
                $add_start_button = '';
            }

            $data_idea['short_question_allow']= $short_question_allow;
            $data_idea['shot_question_title']= $shot_question_title;
            $data_idea['short_ques_body']= $short_ques_body;
            $data_idea['large_question_allow']= $large_question_allow;
            $data_idea['large_question_title']= $large_question_title;
            $data_idea['large_ques_body']= $large_ques_body;
            $data_idea['student_title']= $student_title;
            $data_idea['word_limit']= $word_limit;
            $data_idea['time_hour']= $time_hour;
            $data_idea['time_min']= $time_min;
            $data_idea['time_sec']= $time_sec; 
            $data_idea['allow_idea']= $allow_idea;
            $data_idea['add_start_button']= $add_start_button;
            $data_idea['question_id']= $question_id;
            
            
            $idea_id = $TutorClass->ideaUpdateId('idea_info', $data_idea, $question_id);
            
           

            $datas[]=isset($post['question_instruction']) ? $post['question_instruction'] :'';


            $idea_description = $post['idea_details'];
            
            $builder = $this->db->table('idea_description'); 
            $builder->select('*');
            $builder->where('question_id', 8324);
            $builder->where('idea_id',  67);
            $builder->orderBy("id", "desc");
            $builder->limit(1);
            $query = $builder->get();
            $last_idea = $query->getResultArray();

            $new_idea_no =$last_idea[0]['idea_no'];
            
            if(!empty($idea_description)){
            foreach($idea_description as $key => $value){

               $idea = explode (",", $value); 
            //    print_r($idea);

               $idea_des['question_id']= $question_id;
               $idea_des['idea_id']= $idea_id;
               $idea_des['idea_no']= $new_idea_no;
               $idea_des['idea_name']= "Idea".$idea[0];
               $idea_des['idea_title']= $idea[1];
               $idea_des['idea_question']= $idea[2];

               $idea_des_id = $TutorClass->idea_des_Id('idea_description', $idea_des);

               $qstudy_idea['question_id']= $question_id;
               $qstudy_idea['idea_id']= $idea_id;
               $qstudy_idea['tutor_id']= $this->session->get('user_id');
               $qstudy_idea['idea_no']= $new_idea_no;
               $qstudy_idea['student_ans']= $idea[2];
               $qstudy_idea['submit_date']= date('Y/m/d');
               $qstudy_idea['total_word']= $idea[2];
               $tutor_idea_save = $TutorClass->tutor_idea_save('idea_tutor_ans', $qstudy_idea);

               $new_idea_no++;
            }
        }
            
            
            $this->db->table('idea_save_temp')->truncate();
            echo "update";

          }
        else{

        $hour   = $this->request->getVar('hour');
        $minute = $this->request->getVar('minute');
        $second = $this->request->getVar('second');

        $data['questionTime'] = $hour.":".$minute.":".$second;

        if ($_POST['questionType']==9 ) {
            if ($data['questionName']) {
                $TutorClass->updateInfo('tbl_question', 'id', $question_id, $data);
                echo "update";
            }else{
                echo "Each part needs a right and wrong Question";
            }
                        
        }else{
               $TutorClass->updateInfo('tbl_question', 'id', $question_id, $data);
                echo "update";
        }

        }
    }

    public function indexQuesAns($items)
    {
        $arr = [];
        foreach ($items as $item) {
            $temp            = json_decode($item);
            if ($temp) {
                $cr              = explode('_', $temp->cr);
                $col             = $cr[0];
                $row             = $cr[1];
                $arr[$col][$row] = [
                    'type' => $temp->type,
                    'val'  => $temp->val,
                ];
            }
        }

        return $arr;
    }//end indexQuesAns()
     
    public function renderSkpQuizPrevTable($items, $rows, $cols, $showAns = 0, $pageType = '')
    {
        // print_r($items);die;
        $row = '';
        for ($i = 1; $i <= $rows; $i++) {
            $row .= '<tr>';
            for ($j = 1; $j <= $cols; $j++) {
                if ($items[$i][$j]['type'] == 'q') {
                    $row .= '<td>'
                    . '<input type="text" data_q_type="0" data_num_colofrow="'.$i.'_'.$j.'" value="'.$items[$i][$j]['val'].'" name="skip_counting[]" class="form-control rsskpin input-box  rsskpinpt'.$i.'_'.$j.'" readonly style="min-width:50px; max-width:50px; background-color:#ffb7c5;">';
                    if ($pageType = 'edit') {
                        $quesObj = [
                            'cr'   => $i.'_'.$j,
                            'val'  => $items[$i][$j]['val'],
                            'type' => 'q',
                        ];
                        $quesObj = json_encode($quesObj);
                        //                        echo $quesObj.'<pre>';
                        $row    .= '<input type="hidden" value=\''.$quesObj.'\' name="ques_ans[]" id="obj">';
                        $row .= '<input type="hidden" value="" name="ans[]" id="ans_obj">';
                    }

                    $row .= '</td>';
                } else {
                    $ansObj = [
                        'cr'   => $i.'_'.$j,
                        'val'  => $items[$i][$j]['val'],
                        'type' => 'a',
                    ];
                    $ansObj = json_encode($ansObj);
                    $val    = ($showAns == 1) ? ' value="'.$items[$i][$j]['val'].'"' : '';

                    $row .= '<td>'
                    . '<input type="text" data_q_type="0" data_num_colofrow="'.$i.'_'.$j.'" value="'.$items[$i][$j]['val'].'" name="skip_counting[]" class="form-control rsskpin input-box rsskpinpt'.$i.'_'.$j.'" readonly style="min-width:50px; max-width:50px; background-color:#baffba;">';
                    //                    $row .= '<input type="hidden" value="" name="given_ans[]" id="given_ans">';
                    if ($pageType = 'edit') {
                        $row .= '<input type="hidden" value=\''.$ansObj.'\' name="ques_ans[]" id="obj">';
                        $row .= '<input type="hidden" value=\''.$ansObj.'\' name="ans[]" id="ans_obj">';
                    }

                    $row .= '</td>';
                }//end if
            }//end for

            $row .= '</tr>';
        }//end for

        return $row;
    }//end renderSkpQuizPrevTable()

    public function input_tutor()
    {
        $qty = $this->request->getVar('qty');
		$qus_type = $this->request->getVar('qus_type');
		if(isset($qus_type) && $qus_type == 4){
			$style = 'none';
		}else{
			$style = 'block';
		}
        $output ='';
		$output_ ='';
        for ($i=0; $i <$qty ; $i++) { 

        $output .='<div class="tab row tabdata'.$i.'">';
        $output .= '<div class="col-md-7">';
        $output .='<div class="form-group">
                     <div class="col-sm-4"><label for="inputEmail3" class="col control-label">Image file</label></div>
                      <div class="col-sm-8">
                        <input type="file" class="img-validate" count_here="Image Field at tab [ '.($i+1).' ] " id="image_'.$i.'" name="Image['.$i.']" accept=".png"  required>
                        <p style="color:red;" id="img_id_'.$i.'"></p>
                      </div>
                    </div><br><br>';              
        $output .='<div class="form-group" style="display:'.$style.' !important">
                      <div class="col-sm-4"><label for="inputEmail3" class="col control-label">Audio File</label></div>
                      <div class="col-sm-8">
                        <input type="file"  id="audio_'.$i.'" name="audioFile['.$i.']"  accept=".mp3, .mp4">
                        <p style="color:red;" id="aud_id_'.$i.'"></p>
                      </div>
                    </div><br><br>';            
        $output .='<div class="form-group" style="display:'.$style.' !important">
                       <div class="col-sm-4"><label for="spchToTxt" class="col control-label">Speech to text</label></div>
                      <div class="col-sm-8">
                        <input type="text"  id="speech_'.$i.'" class="form-control" name="speech_to_text['.$i.'][speech_to_text]" >
                        <p style="color:red;" id="spch_id_'.$i.'"></p>
                      </div>
                    </div><br><br>';
        $output .='</div>';
        $output .= '<div class="col-md-5">';
        $output .= '<span style="margin-right: 10px;">Accepted Format:<b>.png</b></span>';
        $output .= '<span>Maximum File Size:<b>3MB</b></span>';
        $output .='</div>';
        $output .='</div>';                        
        }

 

        $output .='<div class="row" style="background-color: #3595d6; margin-bottom:0" >
                   
                         <div class="col-sm-12">
                         <div style="float:right; ">
                             <div class="ss_pagination" style="margin-bottom:0">
                              <div>
                                <button class="steprs" style="color: #4c4a4a; border: none; padding: 10px;font-weight: 500;" type="button" id="prevBtn" onclick="nextPrev(-999)" >Previous</button>';

                         for ($i=0; $i <$qty ; $i++) { 
                              $output .='<button style="background: none;border: none; padding: 10px;font-weight: 500;" class="steprs number_'.$i.'" style="width:45px;" id="qty" value="'.$qty.'" type="button" onclick="showFixSlide('.$i.')">'.($i+1).'</button> ';
                         }   

                            $output .='<button type="button" style="color: #4c4a4a; border: none; padding: 10px;font-weight: 500;" class="btn_work" id="nextBtn" onclick="nextPrev(99999)">Next</button>
                          </div>
                         </div>

                        </div>
                     </div>
                 </div>'; 


        // $output .='<div style="text-align:center;margin-top:40px;">';
        // for ($i=0; $i <$qty ; $i++) { 
        //     $output .='<span class="step"></span>';
        // }

        $output .='<script>
                var currentTab = 0;
                $(\'.number_\'+0).addClass("activtab");
                 // Current tab is set to be the first tab (0)
                showTab(currentTab); // Display the current tab

                var qty = $("#qty").val();

                for (i = 0; i < 4; i++) {
                          $(\'.number_\'+i).show();
                        }
                for (i = 4; i < qty; i++) {
                  $(\'.number_\'+i).hide();
                }

                function showTab(n) {
                    $(\'.tab\').hide();
                    $(\'.tabdata\'+n).show();
                }

                function showFixSlide(n) {
                      $(".steprs").each(function( index ) {
                        $(this).removeClass("activtab");
                    })
                   
                        $(\'.number_\'+n).addClass("activtab");

                    
                    console.log(n);
                    
                    currentTab = n;
                    showTab(n);
                    fixStepIndicator(n);
                }


                    function nextPrev(n){

                        //previous clicked
                        if(n <0 ){

                            currentTab = currentTab - 1;
                            if(currentTab<0) currentTab = 0;
                            console.log(currentTab);
                            fixStepIndicator(currentTab);
                            

                        }
                        //next clicked
                        else{

                           currentTab = currentTab + 1;
                           if(currentTab >= qty) currentTab = qty - 1;
                           fixStepIndicator(currentTab);
                            }
                      

                        fixStepIndicator();
                        showTab(currentTab);

                    }


                function fixStepIndicator(currentTab) {

                   x = currentTab;
      // $(".steprs").each(function( index ) {
      //                   $(this).css("background","transparent");
      //               })

                    $(\'.number_\'+parseInt(currentTab - 1)).removeClass("activtab");
                    $(\'.number_\'+parseInt(currentTab + 1)).removeClass("activtab");

                    $(\'.number_\'+currentTab).addClass("activtab");
                   if(x>=3){

                       s_1 = x+2;
                       s_2 = x-2;
                       for (i = s_2; i < s_1 + 1; i++) {
                          $(\'.number_\'+i).show();
                        }
                       for (i = 0; i < s_2; i++) {
                          $(\'.number_\'+i).hide();
                        }
                        for (i = s_1+1; i < qty; i++) {
                          $(\'.number_\'+i).hide();
                        }

                   }
                   if(x<3){

                    for (i = 0; i < 4; i++) {
                          $(\'.number_\'+i).show();
                        }
                    for (i = 4; i < qty; i++) {
                      $(\'.number_\'+i).hide();
                    }

                   }

                   if( x <= qty && x >= qty-4){
                    for (i = qty-5; i < qty; i++) {
                          $(\'.number_\'+i).show();
                        }

                    for (i = 0; i < qty-4; i++) {
                          $(\'.number_\'+i).hide();
                        }

                   }
                }
                </script>';

                                
        print_r($output);
    }


    public function studyType()
    {  
        $StudentClass=new \StudentClass();
        $FaqClass=new \FaqClass();

        $id=1;
        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        if ($id == 1) {
            $data['video_help'] = $FaqClass->videoSerialize(16, 'video_helps');
            $data['video_help_serial'] = 16;
        }
        if ($id == 2) {
            $data['video_help'] = $FaqClass->videoSerialize(17, 'video_helps');
            $data['video_help_serial'] = 17;
        }

        $_SESSION['prevUrl'] = base_url('/') . '/Tutor/organization';
        $_SESSION['prevUrl_after_student_finish_buton'] = base_url('/') . $_SERVER['PATH_INFO'];

        $data['types'] = $id;

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('tutor/study_type',$data);
    }
    
    public function whiteboard_items()
    {
        error_report_check();
        $TutorClass=new \TutorClass();
        $StudentClass=new \StudentClass();
        $FaqClass=new \FaqClass();

        $user_id = $this->session->get('user_id');

        $data['user_info'] = $TutorClass->userInfo($user_id);

        $ck_schl_corporate_exist = $TutorClass->ck_schl_corporate_exist($data['user_info'][0]['SCT_link'] );
        $data['ck_schl_corporate_exist'] =  $ck_schl_corporate_exist;
        // Newly Added
        //$all_subject_tutor =$this->Student_model->getInfo('tbl_registered_course', 'user_id', $this->session->userdata('user_id'));
        $all_subject_tutor  = $StudentClass->registeredCourse($this->session->get('user_id'));
        $whiteboard = 0;
        foreach ($all_subject_tutor as $key => $value) {
            //$course_id = $value['course_id'];
            $course_id = $value['id'];
            if ($course_id == 53) {
                $whiteboard = 1;
            }

        }
        $data['whiteboard'] = $whiteboard;
        
        //echo $data['whiteboard'];die()
        
        $userInfo = $this->db->table('tbl_useraccount')->where('id',$user_id)->get()->getRow();
        $parentId = $userInfo->parent_id;
        if($parentId != null){
             $info = $this->db->table('tbl_useraccount')->where('id',$parentId)->get()->getRow();
             if($info->user_type == 4){
                 $data['school_tutor'] = 1;
             }else{
                 $data['school_tutor'] = 0;
             }
            
        }

        $data['video_help'] = $FaqClass->videoSerialize(27, 'video_helps'); //rakesh
        $data['video_help_serial'] = 27;    

        return view('tutor/whiteboard_items',$data);
    }

    public function WhiteBoardTutor()
    {
        $StudentClass=new \StudentClass();
        $TutorClass=new \TutorClass();
        $FaqClass=new \FaqClass();

        $ckWhiteboard  =  $StudentClass->getAllInfo_classRoom();
        foreach ($ckWhiteboard as $key => $value) {
            $roomInfo = $StudentClass->getInfo('tbl_classrooms', 'id', $value['id'] );
            $url_data = $roomInfo[0]['class_url']; 

            $roomInfo = $StudentClass->deleteInfo('tbl_classrooms', 'id', $value['id']  );
            $toUpdate['in_use'] = 0;
            $TutorClass->updateInfo('tbl_available_rooms', 'room_id', $url_data, $toUpdate);
        }

        $data['video_help'] = $FaqClass->videoSerialize(28, 'video_helps'); //rakesh
        $data['video_help_serial'] = 28;

        $data['user_info'] = $TutorClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));

        if ($data['user_info'][0]['subscription_type'] =="direct_deposite") {
            if ( $data['user_info'][0]['direct_deposite'] == 0 ) {
                return redirect()->to(base_url($_SERVER['HTTP_REFERER']));
            }
        }

        $x = $TutorClass->getInfo_Alstudent('tbl_enrollment', 'sct_id' , $this->session->get('user_id'));

        $all_student_id =array();
        $all_student_details =array();

        foreach ($x as $key => $value) {
            $all_student_id[] = $value['st_id'];
        }
        foreach ($x as $key2 => $value) {
            $id = $value['st_id'];
            $student = $this->db->table('tbl_useraccount')->where('id',$id)->get()->getRow();
            $all_student_details[$key2]['id'] = $student->id;
            $all_student_details[$key2]['user_email'] = $student->user_email;
        }
        

        if (count($all_student_id)) {
            $x = $TutorClass->getInfo_Alstudent_two('tbl_useraccount', 'id' , $all_student_id);
            $ckExist = array();
            $ckExist = $TutorClass->getClassRoomsCk($this->session->get('user_id'));
        }else{
            $x =array();
        }
        // echo "<pre>";print_r($ckExist);die();
        
        $data['all_student'] = $all_student_details;//$x;


        if (isset($ckExist[0]['start_time'] )) {
            $remainder = $ckExist[0]['end_time'];

            $diff = ($remainder) - time();

            if ($diff > 0) {
                $data['ckExist'] = $ckExist;
                $min_hr_sc = round($diff/60);
                $data['min_hr_sc'] = $min_hr_sc;
            }else{
                $roomInfo = $StudentClass->getInfo('tbl_classrooms', 'id', $ckExist[0]['id'] );
   
                $url_data = $roomInfo[0]['class_url'];  
                $roomInfo = $StudentClass->deleteInfo('tbl_classrooms', 'id', $ckExist[0]['id']  );
                $toUpdate['in_use'] = 0;
                $TutorClass->updateInfo('tbl_available_rooms', 'room_id', $url_data, $toUpdate);
            }
        }
        
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('tutor/tutor_whiteboard',$data);

    }

    public function insertClass()
    {
        $TutorClass=new \TutorClass;
        $flag = 0;
        $pieces = explode("&", $_POST['data']);
        foreach ($pieces as $key => $value) {
            if ( $value == "all_student=all" ) {
                $flag = 1;
                $data['all_student_checked'] = 1;
            }
        }

        if ($flag == 0) {
            foreach ($pieces as $key => $value) {
                if ( $value != "all_student=all" ) {
                    $num[] = preg_replace('/[^0-9]/', '', $value);
                }
            }
            $data['students'] = json_encode($num);
        }
        $user_info = $TutorClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['tutor_id'] = $this->session->get('user_id');
        $data['tutor_name'] = $user_info[0]['name'];
        $data['start_time'] = time();
        $data['end_time'] = time() + 90 * 60;

        $x = $TutorClass->getClassRooms();
        $ckExist = $TutorClass->getClassRoomsCk($this->session->get('user_id'));
        // echo '<pre>';
        // print_r($ckExist);
        // die();
        if (count($x)) {
            if (count($ckExist) == 0) {
                $toUpdate['in_use'] = 1;
                $id = $TutorClass->getClassRooms();
                $TutorClass->updateInfo('tbl_available_rooms', 'id', $x[0]['id'], $toUpdate);
                $data['class_url'] = $x[0]['room_id'];
                $class_id =  $TutorClass->insertId('tbl_classrooms', $data);
                
                $class_url = base_url('/yourClassRoomTutor').'/'.$class_id;

                echo $class_url;
            }else{
                echo 1;
            }
            
        }else{
            echo 0;
        }
    }

    public function yourClassRoom($id)
    {
        $StudentClass=new \StudentClass();

        $roomInfo = $StudentClass->getInfo('tbl_classrooms', 'id', $id );

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';


        $user_info = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['ifram'] = '<iframe src="//www.groupworld.net/room/'.$roomInfo[0]['class_url'].'/conf1?need_password=false&janus=true&hide_playback=true&username='.$user_info[0]['name'].'" allow="camera;microphone" width="100%" height="600" scrolling="no" frameborder="0"></iframe>';
        
        return view('students/whiteboardDashboard',$data);

    }


    public function tutor_question_store()
    {

        error_report_check();
        $FaqClass=new \FaqClass();
        $TutorClass=new \TutorClass();
        $AdminClass=new \AdminClass();

        $data['video_help'] = $FaqClass->videoSerialize(29, 'video_helps'); //rakesh
        $data['video_help_serial'] = 29;
        
        
        $user_id = $this->session->get('user_id');
        //check direct deposit resource
        $tbl_qs_payments = $this->db->table('tbl_qs_payment')->where('user_id',$user_id)->where('PaymentEndDate >',time())->orderBy('id','desc')->limit(1)->get()->getRow();
        $payment_status = $tbl_qs_payments->payment_status;
        $data['deposit_resources_status'] = 3;
        if($payment_status == 'Completed'){
            //$data['deposit_resources_status'] = 1;//active
        }else if($payment_status == 'Pending'){
            //$data['deposit_resources_status'] = 0;//Inactive
        }
    
        $userInfo = $this->db->table('tbl_useraccount')->where('id',$user_id)->get()->getRow();
        $parentId = $userInfo->parent_id;
        if($parentId != null){

             $info = $this->db->table('tbl_useraccount')->where('id',$parentId)->get()->getRow();
             if($info->user_type == 4){
                 $data['school_tutor'] = 1;
             }else{
                 $data['school_tutor'] = 0;
             }
            
        }
           
        //echo "<pre>";print_r($data);die;

        $user_id = $this->session->get('user_id');
        $data['all_subject'] = $TutorClass->getInfo('tbl_question_store_subject', 'created_by',2);
        $data['store_data']  = $TutorClass->getQuestionStore();
        $data['user_info'] = $TutorClass->userInfo($user_id);
        $data['allCountry']= $this->db->table('tbl_country')->get()->getResultArray();
     
        return view('tutor/question_store', $data);
      
    }

    
    public function search_question_store()
    {
        error_report_check();
        $StudentClass=new \StudentClass();
        $subject_id = 0;
        $country    = 0;
        $grade      = 0;
        $post = $this->request->getVar();
        //$clean = $this->security->xss_clean($post);
        $user_id = $this->session->get('user_id');
     
        $userInfo = $this->db->table('tbl_useraccount')->where('id',$user_id)->get()->getRow();
        $parentId = $userInfo->parent_id;
        if($parentId != null){
         $info = $this->db->table('tbl_useraccount')->where('id',$parentId)->get()->getRow();
         if($info->user_type == 4){
             $school_tutor = 1;
         }else{
             $school_tutor = 0;
         }
            
        }
        // echo $school_tutor;die();
        
        
        if($post['grade'] != '')
        {
            $grade      = $post['grade'];
        }
        if($post['subject_id'] != '')
        {
            $subject_id      = $post['subject_id'];
        }
        if($post['country'] != '')
        {
            $country      = $post['country'];
        }
        
        
        //check direct deposit resource
        $tbl_qs_payments = $this->db->table('tbl_qs_payment')->where('user_id',$user_id)->where('PaymentEndDate >',time())->where('subject',$subject_id)->orderBy('id','desc')->limit(1)->get()->getRow();
      
        $end_date = isset($tbl_qs_payments)?$tbl_qs_payments->PaymentEndDate:'';
        $payment_status = isset($tbl_qs_payments)?$tbl_qs_payments->payment_status:'';
     
        if($payment_status == 'Completed'){
            $deposit_resources_status = 1;//active
        }else if($payment_status == 'Pending'){
            $deposit_resources_status = 2;//panding
        }else{
            $deposit_resources_status = 0;//Inactive
        }
        
       
        $result['error'] = 0;
        $result['msg'] = '';
        if($subject_id != 0 && $grade != 0)
        {
            
            $conditions['country']   = $country;
            $conditions['grade']     = $grade;
            $conditions['subject']   = $subject_id;
            
            $resource = $this->db->table('resource_subject_amount')->where('subject_id',$subject_id)->get()->getRow();
            $amount   = (isset($resource))?$resource->amount:0;

            $store_data = $StudentClass->getQuestionStore($conditions);
            $html = '';
			$base_url=base_url();
            if (!empty($store_data)) {
                foreach ($store_data as $key => $item) {
                    $chapter_id = $item['chapter'];
                    $chapter =  $StudentClass->getInfo('tbl_question_store_chapter', 'id',$chapter_id);
                    $html .= '<tr>';
                    if($school_tutor == 1 ){
                        $html .= '<td><a href="'.$base_url.'/download_tutor_question_store/'.$item['id'].'" store-id'.$item['id'].'>'.$chapter[0]['chapter_name'].'</a></td>';
                    }else{
                        
                        if ($item['questionStoreStatus'] == 'paid') {
                            if($deposit_resources_status == 1){
                                $html .= '<td><a href="'.$base_url.'/download_tutor_question_store/'.$item['id'].'" store-id'.$item['id'].'>'.$chapter[0]['chapter_name'].'</a></td>';
                            }else{
                                $html .= '<td><a store-id'.$item['id'].'>'.$chapter[0]['chapter_name'].'</a></td>';
                            }                      
                        }else{
                            $html .= '<td><a href="'.$base_url.'/download_tutor_question_store/'.$item['id'].'" store-id'.$item['id'].'>'.$chapter[0]['chapter_name'].'</a></td>';
                        }
                    }
                    
                    if($school_tutor == 1 ){
                        $html .= '<td><img style="width:25px;" src="'.base_url('/').'/assets/images/pdf-icon2.png">  <p style="font-size: 13px;color: #4a4193;display:inline-block;position: relative;bottom: 7px;left: 10px;">Free</p></td>';
                        
                    }else{
                        
                        if ($item['questionStoreStatus'] == 'paid') {
                            $html .= '<td><img style="width:25px;"src="'.base_url('/').'/assets/images/pdf-icon2.png">  <i style="font-size: 20px;color: #dbd526;position: relative;bottom: 7px;left: 10px;" class="fa fa-lock"></i></td>';                        
                        }else{
                            $html .= '<td><img style="width:25px;" src="'.base_url('/').'/assets/images/pdf-icon2.png">  <p style="font-size: 13px;color: #4a4193;display:inline-block;position: relative;bottom: 7px;left: 10px;">Free</p></td>';
                        }
                    }
                    $html .= '</tr>';
                } 
            }else{
                $html .= '<tr>';
                $html .= '<td></td>';
                $html .= '<td>No data found!</td>';
                $html .= '</tr>';
            }
            $result['error'] = 0;
            $result['data'] = $html;
            $result['success_amount'] = $amount;
            if($deposit_resources_status == 1){
                $result['href_url'] = "<button class='btn btn-success btn-sm'>Paid</button>"; 
                
            }else if($deposit_resources_status == 2){
                $result['href_url'] = "<button class='btn btn-danger btn-sm'>Pending</button>";
                
            }else{
                $result['href_url'] = ' <a href="questionStorePaymentOption/'.$subject_id.'" style="display: inline-block;">
                          <i class="fa fa-shopping-cart" style="font-size: 35px;margin-left: 5px"></i>
                        </a>'; 
            }
            echo json_encode($result);
            die;
        }
        $result['error'] = 1;
        $result['msg'] = 'Invalid data!';
        echo json_encode($result);
        die;
    }


    public function tutor_bank_details(){
        $data['account_detail'] = $this->db->table('tbl_tutor_account_details')->where('tutor_id',$this->session->get('user_id'))->get()->getRow();
        
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('tutor/tutor_bank_details',$data);
    }
   
    public function bank_details_submit_form(){
        $post = $this->request->getVar();
        //$clean = $this->security->xss_clean($post);
        $data['paypal_details'] = $this->request->getVar('paypal_details');
        $data['bank_details']   = $this->request->getVar('bank_details');
        $bank_paypal_details    = $this->request->getVar('bank_paypal_details');
        $data['default_option'] = isset($bank_paypal_details)?$bank_paypal_details: null;
        $data['tutor_id']       = $this->session->get('user_id');
        
        $checkDetails = $this->db->table('tbl_tutor_account_details')->where('tutor_id',$this->session->get('user_id'))->get()->getRow();
        if(isset($checkDetails)){
            $this->db->table('tbl_tutor_account_details')->where('tutor_id',$this->session->get('user_id'))->update($data);
            $this->session->set('success_msg', 'User account details updated successfully');
            return redirect()->to(base_url('tutor_details'));
        }else{
            $this->db->table('tbl_tutor_account_details')->insert($data);
            $this->session->set('success_msg', 'User account details insert successfully');
            return redirect()->to(base_url('tutor_details'));
        }
    }
	
	public function question_store()
	{
		$TutorClass=new \TutorClass();
		$AdminClass=new \AdminClass();

		$_SESSION['prevUrl'] = base_url('/').'/question-list/';
		$data['user_info'] = $TutorClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
		$user_id  = $this->session->get('user_id');
		$data['allCountry']  = $this->db->table('tbl_country')->get()->getResultArray();
		$data['all_subject'] = $TutorClass->getInfo('tbl_question_store_subject', 'created_by', $user_id);
		return view('tutor/question/question-store',$data);

	}
	
	 public function get_store_subject_amount(){
        
        $subject       = $this->request->getVar('subject_id');
        $checksubject  = $this->db->table('resource_subject_amount')->where('subject_id',$subject)->get()->getRow();
        $amount=(isset($checksubject))?$checksubject->amount:0;
        echo $amount;
    }

    public function get_store_chapter_name()
    {
        error_report_check();
        $TutorClass=new \TutorClass();
        $post = $this->request->getVar();
    
        //$clean = $this->security->xss_clean($post);

        if($post['subject_id'] != '')
        {
            $chapters = $TutorClass->getInfo('tbl_question_store_chapter', 'subject_id',$post['subject_id']);

            $html = '<option value="">Chapter</option>';
            if(count($chapters) > 0)
            {
                foreach($chapters as $chapter)
                {
                    $checked = '';
                    if($post['subject'] == $chapter['id'])
                    {
                        $checked = 'selected';
                    }
                    $html .= '<option '.$checked.' value="'.$chapter['id'].'">'.$chapter['chapter_name'].'</option>';
                }
            }
            echo $html;
        die;
        }
        
    }

    public function get_pdf_serial()
    {
        $StudentClass=new \StudentClass();
        $country    = '';
        $grade      = '';
        $subject    = '';
        $post       = $this->request->getVar();
        //$clean      = $this->security->xss_clean($post);
        $country    = $post['country'];
        $grade      = $post['grade'];
        $subject    = $post['subject_id'];
        if($country != '' && $grade != '' && $subject != '')
        {
            $conditions['country']   = $country;
            $conditions['grade']     = $grade;
            $conditions['subject']   = $subject;
            $store_data = $StudentClass->getQuestionStoreOrder($conditions);
            if(!empty($store_data))
            {
                $order = $store_data[0]['pdf_order'];

                $order = $order+1;
                echo $order;
                die;
            }else{

                echo 1;
                die;
            }
        }
        
        die;
    }
	
	public function search_store_view()
    {
        $TutorClass=new \TutorClass();
        $AdminClass=new \AdminClass();

        $_SESSION['prevUrl'] = base_url('/').'/question-store/';
        $data['user_info'] = $TutorClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $user_id  = $this->session->get('user_id');
        $data['allCountry']  = $this->db->table('tbl_country')->get()->getResultArray();

        $data['all_subject'] = $TutorClass->getInfo('tbl_question_store_subject', 'created_by', $user_id);
        return view('tutor/question/search-question-store', $data);

    }
	
	    public function save_question_store_data()
    {
        $ModuleClass=new \ModuleClass();
        if (!$this->validate('save_question_store_data')) {
            $data['validation'] = $this->validator;
            //  $array = array(
            //     'country_error'         => $this->validator('country'),
            //     'grade_error'           => $this->validator('grade'),
            //     'subject_error'         => $this->validator('subject'),
            //     'chapter_error'         => $this->validator('chapter'),
            //     'tutor_title_error'     => $this->validator('tutor_title'),
            //     'pdf_serial_error'      => $this->validator('pdf_order'),
            //     'student_title_error'   => $this->validator('student_title'),
            //    );
            //    $error['error'] = $array;
               echo json_encode($data);
               die;
        }
        else
        {
           
            $post = $this->request->getVar();
            //$clean = $this->security->xss_clean($post);
            //$clean['media'] = isset($_FILES)?$_FILES:[];
            $user_id = $this->session->get('user_id');
            $files = $_FILES;
            $data = array();
            $data['country']        = $post['country'];
            $data['grade']          = $post['grade'];
            $data['subject']        = $post['subject'];
            $data['chapter']        = $post['chapter'];
            $data['tutor_title']    = $post['tutor_title'];
            $data['student_title']  = $post['student_title'];
            $data['pdf_order']      = $post['pdf_order'];
            $data['questionStoreStatus'] = $post['questionStoreStatus'];
            $data['tutor_file']     = '';
            $data['student_file']   = '';
            
            $amount  = $post['amount'];
            $subject = $post['subject'];
            
            $checksubject  = $this->db->table('resource_subject_amount')->where('subject_id',$subject)->get()->getRow();
            if(isset($checksubject)){
                $this->db->table('resource_subject_amount')->where('subject_id',$subject)->update(['amount'=>$amount]);
            }else{
                $this->db->table('resource_subject_amount')->insert(['amount'=>$amount,'subject_id'=>$subject]);
            }
            
            // foreach($post['media'] as $key=>$file)
            // {
            //     $config['upload_path'] = 'assets/question-store';
            //     $config['allowed_types'] = 'pdf';
            //     $config['overwrite'] = false;
            //     $this->load->library('upload');
            //     $config['file_name']=rand(100,9999).'-'.time().'-'.$file['name'];
                 
            //     $this->upload->initialize($config);
            //     if (!$this->upload->do_upload($key)) {
                  
            //        }else{

            //           $imageName = $this->upload->data();
            //           if($key == 'tutor_file')
            //           {
            //             $data['tutor_file'] = 'assets/question-store/'.$imageName["file_name"];
            //           }
            //           if($key == 'student_file')
            //           {
            //             $data['student_file'] = 'assets/question-store/'.$imageName["file_name"];
            //           }
                      
            //        }
            // }
            $tutor_file=$this->request->getFile('tutor_file'); 
            $student_file=$this->request->getFile('student_file');    
            if(!empty($tutor_file))
            {
                $tutor_file_name = $tutor_file->getRandomName();
                $image_upload=$tutor_file->move(ROOTPATH . 'public/assets/question-store', $tutor_file_name);
                $data['tutor_file'] = 'public/assets/question-store/'.$tutor_file_name;
            }
            if(!empty($student_file))
            {
                $student_file_name = $student_file->getRandomName();
                $image_upload_new=$student_file->move(ROOTPATH . 'public/assets/question-store', $student_file_name);
                $data['student_file'] = 'public/assets/question-store/'.$student_file_name;
            }
            
    

            if($data['tutor_file'] == '')
               {
                     $array = array(
                        'tutor_file_error'    => "Tutor pdf can't uploaded .please try again.",
                       );
                    $error['error'] = $array;
                    echo json_encode($error);
                    die;
               }
               if($data['student_file'] == '')
               {
                     $array = array(
                        'student_file_error'    => "Student pdf can't uploaded .please try again.",
                       );
                    $error['error'] = $array;
                    echo json_encode($error);
                    die;
               }

                $ModuleClass->insertInfo('tbl_questions_store', $data);
                $success['success'] = 'Successfully added';

               echo json_encode($success);
        }
    }
	
	public function tutor_progress_type()
    {
        $FaqClass=new \FaqClass();
        $TutorClass=new \TutorClass();

        if ($this->session->get('userType') == 3) {
            $data['video_help'] = $FaqClass->videoSerialize(20, 'video_helps');
            $data['video_help_serial'] = 20;
        }
        $data['user_info'] = $TutorClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';


        return view('tutor/tutor_progress_type',$data);

    }
	
	public function assignModule($id)
    {
        $AdminClass=new \AdminClass();
        $StudentClass=new \StudentClass();

        $data['all_modules'] = $AdminClass->getInfo('tbl_module', 'course_id', $id );
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        $sct_id = $this->session->get('user_id');;
        $country_id = '';

        $data['course_id'] = $id;

        $studentIds = $StudentClass->allStudents($sct_id, $country_id);
        $data['students'] = $this->renderStudents($studentIds);

        return view('module/moduleAssign',$data);

    }

    public function renderStudents($studentIds)
    {
        $StudentClass=new \StudentClass();
        $options = '';
        foreach ($studentIds as $studentId) {
            $student = $StudentClass->userInfo($studentId);

            if (isset($student[0]) && !empty($student[0]) ) {
                $student = $student[0];
                $options .= '<option value="' . $studentId . '">' . $student['name'] . '</option>';
            }
            
        }
        return $options;
    }
	
	  public function moduleSearchFromReorder()
    {
        $ModuleClass=new \ModuleClass();
        $post = $this->request->getVar("courseId");
        $modules = $ModuleClass->allModuleForAssign($post , "course_id");
        $html = $this->renderReorderModule($modules , $this->request->getVar("students") );
        echo count($modules)?$html:'No module found';
    }

    public function renderReorderModule($modules = [] , $std_id )
    {
        $ModuleClass=new \ModuleClass();
        $mdlus = $ModuleClass->studentAssignedModule($std_id , $this->session->get('user_id') );
        $allModuleId = array();
        foreach ($mdlus as $key => $value) {
            $allModuleId[] = $value['assign_module'];
        }

        $row = '';
        foreach ($modules as $key=> $module) {

            if ($module['moduleType'] == 1 ) {
                $moduleType = "Tutorial";
            }if ($module['moduleType'] == 2 ) {
                $moduleType = "Everyday Study";
            }if ($module['moduleType'] == 3 ) {
                $moduleType = "Spacial Exam";
            }if ($module['moduleType'] == 4 ) {
                $moduleType = "Assignment";
            }

            $check = in_array($module["id"], $allModuleId) == 1 ? "checked": ""; 
			$base=base_url();
            $row .= '<tr id="'.$module['id'].'">';
            $row .= '<td>'.date('d-M-Y', $module['exam_date']).'</td>';
            $row .= '<td id="modName"> <a href="'.$base.'/module_preview/'.$module['id'].'/1" >'.$module['moduleName'].'</a> </td>';
            $row .= '<td>'.$moduleType .'</td>';
            $row .= '<td>'.$module['subject_name'].'</td>';
            $row .= '<td>'.$module['chapterName'].'</td>';
            $row .= '<td>  <div class="form-check">
                        <input type="checkbox" class="form-check-input" value="'.$module['id'].'" name="assign[]" '.$check.' >
                        <label class="form-check-label">Assign</label>
                      </div>';
            $row .= '</td>';
            $row .= '<tr>';
        }
        return $row;
    }

    public function assignModuleStudent()
    {
        error_report_check();
        $ModuleClass=new \ModuleClass();
        $data['student_id'] = $_POST['studentId'];
        $data['status'] = 1;
        $data['tutor_id'] = $this->session->get('user_id');

        $ModuleClass->deleteAssignedModule( $_POST['studentId'] , $this->session->get('user_id'));
        foreach ($_POST['assign'] as $key => $value) {

            $ckExist = $ModuleClass->studentAssignedModuleforUpdate( $_POST['studentId'] , $this->session->get('user_id') ,$value );

            if (count($ckExist)) {
            }else{

                $data['assign_module'] = $value;
                $x = $ModuleClass->allModuleForAssign($value , "module_id");
                $data['assign_subject'] = $x[0]['subject'];
                $data['subject_name']   = $x[0]['subject_name'];
                $data['chapter_name']   = $x[0]['chapterName'];
                $data['trackerName']    = $x[0]['trackerName'];
                $data['individualName'] = $x[0]['individualName'];
                $data['module_name']    = $x[0]['moduleName'];
                $data['module_type']    = $x[0]['moduleType'];
                $ModuleClass->insertInfo("student_homeworks" , $data );
            }
            
        }

        echo 1;
    }

    public function qstudyPassword($qstudyPassword)
    {
        // $this->db->from("tbl_setting");
        // $this->db->where("setting_key", "qstudyPassword");
        // $this->db->where("setting_type", $qstudyPassword);

        // $query = $this->db->get()->result_array();

        // print_r( count($query) );

        $builder = $this->db->table('tbl_setting');
        $builder->select('*');
        $builder->where("setting_key", "qstudyPassword");
        $builder->where("setting_type", $qstudyPassword);
        $query = $builder->get();
        $data=$query->getResultArray();
        print_r( count($data) );
    }

	   public function download_tutor_question_store($id)
    {

        $StudentClass=new \StudentClass();
        if (is_numeric($id)) {

           $store = $StudentClass->getInfo('tbl_questions_store', 'id',$id);

           if (isset($store[0]['tutor_file'])) 
           {
                $file=explode('/',$store[0]['tutor_file']); 
                if(file_exists('./assets/question-store/'.$file[2]))
                {
                    $chapter =  $StudentClass->getInfo('tbl_question_store_chapter', 'id',$store[0]['chapter']);
                    $url = $store[0]['tutor_file'];
                    $path_new=FCPATH.$url;
                    $content = file_get_contents($path_new); 
                    $file_name=$chapter[0]['chapter_name'].'.pdf';
        
                    header("Expires: 0");
                    header("Cache-Control: no-cache, no-store, must-revalidate");
                    header('Cache-Control: pre-check=0, post-check=0, max-age=0', false);
                    header("Pragma: no-cache");
                    header("Content-type: application/pdf");
                    header("Content-Disposition:attachment; filename=$file_name");
                    header("Content-Type: application/force-download");
                    echo $content;
                    die();
               }
               else
               {
                    echo 'have no file at directory';
               } 
            //   echo '<pre>';
            //   print_r($content);die();
             //$this->response->download($chapter[0]['chapter_name'].'.pdf',$content);
             //$this->response->download($path_new,null);
            // $this->response->download('bac.pdf',$content);
            
           }
        }
    }
}
