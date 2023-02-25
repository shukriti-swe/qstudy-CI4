<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PrizeWonUsersModel;
use App\Models\TblRegisteredCourseModel;
use App\Models\TblUserAccountModel;
use App\Models\TblClassRoomModel;
use App\Models\TblPaymentModel;
use App\Models\ProductPointModel;
use App\Models\ModulePointModel;
use App\Models\DailyModuleModel;
use App\Models\TblAdminPointModel;
use App\Models\TargetPointModel;
use App\Models\StudentGradeLogModel;
use TutorClass;

class StudentController extends BaseController
{
    public function __construct()
    {
        $this->session=session();
        $user_id = $this->session->get('user_id');
        $user_type = $this->session->get('userType');
        $this->loggedUserId = $this->session->get('user_id');
        $this->loggedUserType = $this->session->get('userType');
        
        $PreviewClass=new \PreviewClass();
        $user_info = $PreviewClass->userInfo($user_id);
		
        if ($user_info[0]['countryCode'] == 'any') {
            // $user_info[0]['zone_name'] = 'Australia/Lord_Howe';
            $user_info[0]['zone_name'] = 'Australia/Sydney';
        }

        $this->site_user_data = array(
            'userType' => $user_type,
            'zone_name' => $user_info[0]['zone_name'],
            'student_grade' => $user_info[0]['student_grade'],
        );
    }
    public function index()
    {
        
        error_reporting(0);
        $StudentClass = new \StudentClass();
        $FaqClass = new \FaqClass();
        $AdminClass=new  \AdminClass();
        
        $prize_won = new PrizeWonUsersModel();
        $total_prize_won= $prize_won->where('user_id', $this->session->get('user_id'))->where('status', 'unavailable')->findAll();
        $data['checkUnavailableProduct'] =count($total_prize_won);

        $data['registered_courses'] = $StudentClass->registeredCourse($this->session->get('user_id'));

        $TblRegisteredCourseModel=new TblRegisteredCourseModel();
        //$checkCourseEndDate  = $TblRegisteredCourseModel->where('user_id',$this->session->get('user_id'))->where('endTime <',time())->update(['status'=>0]);
        
        $all_parents = $StudentClass->all_assigners_new($this->session->get('user_id'));
      
        $i = 0;
        $allTutor = array();
        $TblUserAccountModel=new TblUserAccountModel();
        foreach ($all_parents as $row) 
        {  
            $ckSchoolCorporateExits = $TblUserAccountModel->whereIn('user_type',[4,5])->where('SCT_link',$row['SCT_link'])->findAll();
            
            if (count($ckSchoolCorporateExits) == 0) {
                $allTutor[] = $row;
            }

            $get_child_info = $TblUserAccountModel->where( 'parent_id', $row['id'])->findAll();

            if ($get_child_info) {
                $allTutor[$i]['child_info'] = $get_child_info;
            }
            $i++;
        } 
        
        unset($allTutor[0]);
        $data['all_teachers'] = $allTutor;
       
        $get_involved_school = $StudentClass->get_sct_enrollment_info($this->session->get('user_id'), 4);

        $all_parents = $StudentClass->all_assigners_new($this->session->get('user_id'));
        
        $i = 0;
        $allSchoolTutor = array();

        if (count($get_involved_school)) {
            foreach ($all_parents as $row) {
                if ($row['SCT_link'] == $get_involved_school[0]['SCT_link']) {
                    $allSchoolTutor[] = $row;
                }
            }
        }
        $data['allSchoolTutors'] = $allSchoolTutor;
        
        $get_involved_corporate = $StudentClass->get_sct_enrollment_info($this->session->get('user_id'), 5);

        $all_parents = $StudentClass->all_assigners_new($this->session->get('user_id'));

        $i = 0;
        $allCorporateTutor = array();
 
        if (count($get_involved_corporate)) {
            foreach ($all_parents as $row) {
                if ($row['SCT_link'] == $get_involved_corporate[0]['SCT_link']) {
                    $allCorporateTutor[] = $row;
                }
            }
        }
        $data['allCorporateTutors'] = $allCorporateTutor;

        $TblClassRoomModel=new TblClassRoomModel();

        $ckWhiteboard  = $TblClassRoomModel->where('end_time <', time())->findAll();
        
        foreach ($ckWhiteboard as $key => $value) {
            
            $roomInfo = $StudentClass->getInfo('tbl_classrooms', 'id', $value['id']);
            $url_data = $roomInfo[0]['class_url'];
            $roomInfo = $StudentClass->deleteInfo('tbl_classrooms', 'id', $value['id']);
            $toUpdate['in_use'] = 0;
            $StudentClass->updateInfo('tbl_available_rooms', 'room_id', $url_data, $toUpdate);
        }
        
        if ($this->session->get('userType') == 6) {
            $data['video_help'] = $FaqClass->videoSerialize(3, 'video_helps');
            $data['video_help_serial'] = 3;
        }
        $session_data = [
            'prevUrl' => base_url('/') . 'student',
        ];
        $this->session->set($session_data);
       
        $StudentClass->deleteInfo('tbl_temp_tutorial_mod_ques', 'st_id', $this->session->get('user_id'));

        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        // echo '<pre>';
        // print_r($data['user_info']);die();
        $parent_id = $data['user_info'][0]['parent_id'];

        $TblPaymentModel=new TblPaymentModel();
        $payment_details = $TblPaymentModel->where('user_id', $parent_id)->limit(1)->orderBy('id', 'desc')->get();
       
       $payment_id = $payment_details->id; 
       $payment_courses  = $StudentClass->paymentCourse($payment_id);

       $st_colaburation = 0;
       foreach ($payment_courses as $pc => $value) {
           $val[$pc] = $value['id'];
           if ($val[$pc] == 44) {
               $st_colaburation = $st_colaburation + 1;
           }
       }
       $data['st_colaburation'] = $st_colaburation;

       $student_colaburation = 0;
       $payCourses = $StudentClass->payment_list_Courses($parent_id);
        foreach ($payCourses as $payCours => $value) {
            $val[$payCours] = $value['id'];
            if ($val[$payCours] == 44) {
                $student_colaburation = $student_colaburation + 1;
            }
        }
 
       $data['student_colaburation'] = $student_colaburation;

       $havtutor_2 = array();
       $havtutor = $StudentClass->getInfo_tutor('tbl_enrollment', 'st_id', $this->session->get('user_id'));

       foreach ($havtutor as $key => $value)
       {
        $havtutor_2[] = $StudentClass->getInfo('tbl_classrooms', 'tutor_id', $value['sct_id']);
       }

       $links = array();
    
       foreach ($havtutor_2 as $key => $value) {
           if (count($value)) {
               if ($value[0]['all_student_checked']) {
                   $link[0] = base_url('/yourClassRoom/') . $value[0]['id'];
                   $link[1] = $value[0]['tutor_name'];
                   $links[] = $link;
                   $link = array();
               } else {
                   $x = json_decode($value[0]['students']);
                   foreach ($x as $key => $val) {
                       if ($val == $this->session->userdata('user_id')) {
                           $link[0] = base_url('/yourClassRoom/') . $value[0]['id'];
                           $link[1] = $value[0]['tutor_name'];
                           $links[] = $link;
                           $link = array();
                       }
                   }
               }
           }
       }
      
       $data['class_rooms'] = $links;

       $user_id = $this->session->get('user_id');
       $ProductPointModel=new ProductPointModel();
       $ModulePointModel=new ModulePointModel();
       $DailyModuleModel=new DailyModuleModel();
       $TblAdminPointModel=new TblAdminPointModel();
       $TargetPointModel=new TargetPointModel();
       $StudentGradeLogModel=new StudentGradeLogModel();

       $data['productPoint'] = $ProductPointModel->where('user_id', $user_id)->first();
       $data['modulePoint'] = $ModulePointModel->where('user_id', $user_id)->first();
       $modulePoint = $ModulePointModel->where('user_id', $user_id)->first();
     
       $data['numOfLession'] = count($DailyModuleModel->where('user_id', $user_id)->where('status', 0)->findAll());

       $point = $TblAdminPointModel->first();

       $target =count($TargetPointModel->where('user_id', $user_id)->find());
      
       if ($target == 0) {
        $trg['user_id'] = $user_id;
        $trg['target']  = 1;
        $trg['targetPoint']  = $point->target_point;
        $trg['date'] = date('Y-m-d');
        $TargetPointModel->insert($trg);

       }
       
       // update tagret
       if ($data['numOfLession'] == 30) 
       {
           $upTarget = $TargetPointModel->where('user_id', $user_id)->first();
           $target = $upTarget->target;
           $upTrg['target'] = $target + 1;
           $percentage = (300 * 10) / 100;
           if ($upTarget->targetPoint < $modulePoint->point) {
            $upTrg['targetPoint'] = $upTarget->targetPoint + $percentage;
            } else {
                $upTrg['targetPoint'] = $upTarget->targetPoint - $percentage;
            }
            $upTrg['date'] = date('Y-m-d');
            $TargetPointModel->where('user_id', $user_id)->update($upTrg);

            $DailyModuleModel->where('user_id', $user_id)->update(['status' => 1]);

            $ModulePointModel->where('user_id', $user_id)->update(['point' => 0]); 
        }
        
        $data['gradeCheck'] = $StudentGradeLogModel->where('user_id', $user_id)->first();
        $gradeCheck =$StudentGradeLogModel->where('user_id', $user_id)->findAll();

         if (count($gradeCheck) == 0) {
            $user = $TblUserAccountModel->where('id', $user_id)->first();
            $user_grade = $user->student_grade;
            $gData['user_id'] = $user_id;
            $gData['grade']   = $user_grade;
            $StudentGradeLogModel->insert($gData);
        }

       $data['point'] = $TargetPointModel->where('user_id', $user_id)->first();

        /*==============================================================
                        Student Answer Notification
        ===============================================================*/

        $id = $this->session->get('user_id');
        $data['getModuleType'] =$StudentClass->studentAnswerNotification($id);
        $data['getIdeaInfos'] =$StudentClass->getStudentIdeaInfo($id);

        foreach($data['getModuleType'] as $key1 => $value1){
            foreach($data['getIdeaInfos'] as $key2 => $value2){
                if($key1 == $key2){
                    $data['getIdeaInfos'][$key2]['modtype'] = $value1['moduleType'];
                }
            }
        }

        //check direct deposit courses
        $checkDirectDepositCourse = $AdminClass->getDirectDepositCourse($user_id);
        $checkDirectDepositPendingCourse = $AdminClass->getDirectDepositPendingCourse($user_id);

        $data['checkRegisterCourses'] = $AdminClass->getActiveCourse($user_id);
        $data['checkDirectDepositCourse'] = $checkDirectDepositCourse;
        $data['checkDirectDepositCourseStatus'] = $checkDirectDepositPendingCourse;
         
    //    echo '<pre>';
    //    print_r($data);
    //    die();
        return view('students/students_dashboard',$data);
    }


    public function student_setting()
    {
    
        error_reporting(0);

        $StudentClass = new \StudentClass();
        $FaqClass = new \FaqClass();
        $AdminClass=new  \AdminClass();

        $data['video_help'] = $FaqClass->videoSerialize(13, 'video_helps');
        $data['video_help_serial'] = 13;

        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        
        $parent_detail = getParentIDPaymetStatus($data['user_info'][0]['parent_id']);
        // echo '<pre>';
        // print_r($data['user_info'][0]);
        // die();
       

        if ($parent_detail[0]['subscription_type'] == "direct_deposite") {
            if ($parent_detail[0]['direct_deposite'] == 0) {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $data['user_id'] = $this->session->get('user_id');
        $data['profile'] = $StudentClass->get_profile_info($data['user_id']);
        // echo '<pre>';
        // print_r($data['user_id']);
        // die();

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        //$data['maincontent'] = $this->load->view('students/student_setting', $data, true);
        return view('upper_level_students/upper_setting', $data);
    }

    public function student_details()
    {
        error_reporting(0);

        $StudentClass = new \StudentClass();
        $FaqClass = new \FaqClass();
        $AdminClass=new  \AdminClass();

        $data['user_info'] = $StudentClass->userInfo($this->session->get('user_id'));

        $parent_detail = getParentIDPaymetStatus($data['user_info'][0]['parent_id']);

        if ($parent_detail[0]['subscription_type'] == "direct_deposite") {
            if ($parent_detail[0]['direct_deposite'] == 0) {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $data['studentRefLink'] = $StudentClass ->getStudentRefLink($this->session->get('user_id'));
        $data['student_course'] = $StudentClass ->studentRegisterCourses($this->session->get('user_id'), $data['user_info'][0]['subscription_type']);

        // echo "<pre>";print_r($data['student_course']);die();
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('upper_level_students/student_details', $data);
    }

    public function update_student_details()
    {
        $StudentClass = new \StudentClass();
        $FaqClass = new \FaqClass();
        $AdminClass=new  \AdminClass();

        // $this->form_validation->set_rules('password', 'Password', 'trim|min_length[6]');
        // $this->form_validation->set_rules('passconf', 'passconf', 'trim|matches[password]');
        // if ($this->form_validation->run() == false) {
        // redirect('student_details');
        // } else {
        // $password = md5($this->input->post('password'));
        // $grade = $this->input->post('student_grade');

        // $data = array(
        // 'user_pawd' => $password,
        // 'student_grade' => $grade,
        // );
        // $this->Student_model->updateInfo('tbl_useraccount', 'id', $this->loggedUserId, $data);
        // $this->session->set_flashdata('success_msg', 'Account updated successfully!');
        // redirect('student_details');
        // }

        if ($this->request->getVar('password')) {
            $data['user_pawd'] = md5($this->request->getVar('password'));
        }

        $data['student_grade'] = $this->request->getVar('student_grade');

        $data['sms_status_stop'] = $this->request->getVar('sms_status_stop');
        $StudentClass->updateInfo('tbl_useraccount', 'id',$this->session->get('user_id'),$data);
        $this->session->set('success_msg', 'Account updated successfully!');
        return redirect()->to(base_url('student_details'));
    }

    public function student_upload_photo()
    {
        error_reporting(0);

        $StudentClass = new \StudentClass();
        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));

        $parent_detail = getParentIDPaymetStatus($data['user_info'][0]['parent_id']);

        if ($parent_detail[0]['subscription_type'] == "direct_deposite") {
            if ($parent_detail[0]['direct_deposite'] == 0) {
                return redirect()->to(base_url($_SERVER['HTTP_REFERER']));
            }
        }

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        //$data['maincontent'] = $this->load->view('students/upload', $data, true);
        return view('upper_level_students/upload', $data);
    }

    public function sure_student_photo_upload()
    {
        $ParentClass=new \ParentClass();
        $profile_image = $this->request->getFile('file');

        if ($profile_image != '') {

            $profile_images = $profile_image->getRandomName();
            $image=$profile_image->move(ROOTPATH . 'public/assets/uploads', $profile_images);
            if($image)
            {
                $user_profile_picture=$profile_images ;
                $data = array(
                    'image' =>$user_profile_picture
                );
                $rs['res']=$ParentClass->updateInfo('tbl_useraccount','id',$this->session->get('user_id'),$data);
                echo 1;
            }   
            else
            {
                echo 0;  
            } 
        }
  
    }
    
    public function my_enrollment()
    {
        error_reporting(0);
        $StudentClass = new \StudentClass();

        $data['user_info'] = $StudentClass->userInfo($this->session->get('user_id'));

        $parent_detail = getParentIDPaymetStatus($data['user_info'][0]['parent_id']);

        if ($parent_detail[0]['subscription_type'] == "direct_deposite") {
            if ($parent_detail[0]['direct_deposite'] == 0) {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $data['get_involved_teacher'] = $StudentClass->get_sct_enrollment_info($this->session->get('user_id'), 3);
        $data['get_involved_school'] = $StudentClass->get_sct_enrollment_info($this->session->get('user_id'), 4);
        $data['get_involved_corporate'] = $StudentClass->get_sct_enrollment_info($this->session->get('user_id'), 5);

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('upper_level_students/my_enrollment_list', $data);
    }

    public function profile_update()
    {
        error_reporting(0);
        $StudentClass = new \StudentClass();

        $return=array();
	
		if(!empty($_FILES['profile_image']['name'])){
            $profile_image = $this->request->getFile('profile_image');
            // echo '<pre>';
            // print_r($profile_image);
            // die();
            $profile_images = $profile_image->getRandomName();
            $image_upload=$profile_image->move(ROOTPATH . 'public/assets/uploads/profile', $profile_images);

			if($image_upload){
                $image = \Config\Services::image()
                ->withFile(ROOTPATH .'public/assets/uploads/profile/' . $profile_images)
                ->resize(200, 200, true, 'height')
                ->save(ROOTPATH . 'public/assets/uploads/profile/thumbnail/'.$profile_images);

			}else{
				$return['main_image_error']	= array('error' =>'Image do not uploaded!');
				//print_r($return['main_image_error']);die();
			}
		}else{
			$profile_image = 'no_image';
		}

        if($this->request->getVar('student_name')!=''){
            $data['student_name'] = $this->request->getVar('student_name');
        }else{
            $data['student_name'] =''; 
        }
        if($this->request->getVar('school_name')!=''){
            $data['school_name'] = $this->request->getVar('school_name');
        }else{
            $data['school_name'] =''; 
        }
        if($this->request->getVar('country')!=''){
            $data['country'] = $this->request->getVar('country');
        }else{
            $data['country'] =''; 
        }
        $data['profile_image'] = $profile_images;
        $data['user_id'] =  $this->session->get('user_id');

        $builder = $this->db->table('profile');
        $builder->select('*');
        $builder->where('user_id',$data['user_id']);
        $query = $builder->get();
        $result=$query->getResultArray();

        if(!empty($result)){
            $builder = $this->db->table('profile');
            $builder->where('user_id', $data['user_id']);
            $builder->update($data);
            $data['success']='Update Successfully !';
                $this->session->set($data);
                return redirect()->to(base_url('student_setting'));

        }else{
            // echo '<pre>';
            // print_r($data);die();
            $insert_id=$StudentClass->insertId('profile',$data);
            if(isset($insert_id)){
                $data['success']='Saved Successfully !';
                $this->session->set($data);
                // echo '<pre>';
                // print_r($_SERVER['HTTP_REFERER']);
                // die();
                return redirect()->to(base_url('student_setting'));
            }

        }
        
    }

    public function add_tutor_like(){
        
        error_report_check();
        $StudentClass=new \StudentClass();
        $data['question_id']= $this->request->getVar('question_id');
        $data['module_id']= $this->request->getVar('module_id');
        $data['idea_id']= $this->request->getVar('idea_id');
        $data['idea_no']= $this->request->getVar('idea_no');
        $data['tutor_id']= $this->request->getVar('tutor_id');
        $data['student_id'] = $this->session->get('user_id');
        // print_r($data);die();
        $check_like = $StudentClass->tutor_like_save($data);
       
        if(empty($check_like)){
            $data['is_like']=1;
            $this->db->table('tutor_like_info')->insert($data); 


            $builder = $this->db->table('idea_get_student_point');
            $builder->select('*');
            $builder->where('student_id', $this->session->get('user_id'));
            $query = $builder->get();
            $get_point=$query->getRowArray();
            
            $total_point = $get_point['student_point']+3;

            $data_point['question_id']=$data['question_id'];
            $data_point['student_id']=$this->session->get('user_id');
            $data_point['student_point']=$total_point;
            $data_point['purpose']="tutor like";
            $this->db->table('idea_get_student_point')->insert($data_point); 

            $builder = $this->db->table('tutor_total_like');
            $builder->select('*');
            $builder->where('tutor_id', $data['tutor_id']);
            $builder->where('question_id', $data['question_id']);
            $query = $builder->get();
            $result = $query->getRowArray();
            
            if(empty($result)){
            $like['tutor_id'] = $data['tutor_id'];
            $like['question_id'] = $data['question_id'];
            $like['total_like'] = 1;
            $this->db->table('tutor_total_like')->insert($like);
            }else{
                $like['total_like'] = $result['total_like']+1;
                $this->db->table('tutor_total_like')->where('tutor_id',$data['tutor_id'])->update($like);

            }
            $data2['total_like']=$like['total_like'];
            $data2['insert_or_update']=1;

            $builder = $this->db->table('idea_get_student_point');
            $builder->select('*');
            $builder->where('student_id', $this->session->get('user_id'));
            $builder->orderBy('id','desc');
            $builder->limit(1);
            $query = $builder->get();
            $data2['student_point'] = $query->getRowArray();

            echo json_encode($data2);

        }else{
            $builder = $this->db->table('tutor_total_like');
            $builder->select('*');
            $builder->where('tutor_id', $data['tutor_id']);
            $builder->where('question_id', $data['question_id']);
            $builder->orderBy('id','desc');
            $builder->limit(1);
            $query = $builder->get();
            $result = $query->getRowArray();

            $data2['total_like']=$result['total_like'];
            $data2['insert_or_update']=2;

            $builder = $this->db->table('idea_get_student_point');
            $builder->select('*');
            $builder->where('student_id', $this->session->get('user_id'));
            $builder->orderBy('id','desc');
            $builder->limit(1);
            $query = $builder->get();
            $data2['student_point'] = $query->getRowArray();

            echo json_encode($data2);
        }
    }
 
    public function st_answer_matching_without_form_workout_two()
    {
        
        $PreviewClass=new \PreviewClass();

        $student_answer = $_POST['checkAllFiled'];
        $question_id = $_POST['question_id'];
        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $correct_ans = $answer_info[0]['answer'];
        $percentage_array = json_decode($answer_info[0]['questionName'])->percentage_array;
        $data['student_answer'] = $student_answer;
        $data['correct_ans'] = $correct_ans;
        $data['percentage_array'] = $percentage_array;
        $correct = 1;
        $i = 1;
        foreach ($student_answer as $ans) {
            $object = 'percentage_' . $i;
            if ($ans != $percentage_array->$object) {
                $correct = 0;
            }
            $i++;
        }

        if ($_POST['ansFiled'] != $correct_ans) {
            $correct = 0;
        }
        $data['correct'] = $correct;

        echo json_encode($data);
    }


    public function st_answer_matching_workout_two()
    {
        $PreviewClass=new \PreviewClass();
        $question_id = $this->request->getVar('question_id');
        $module_id = $this->request->getVar('module_id');
        //$question_order_id = $_POST['check_order_id'] - 1;
        $question_order_id = $this->request->getVar('current_order');
        $provide_ans = $this->request->getVar('answer');

        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $ans_is_right = 'correct';

        $question_marks = $answer_info[0]['questionMarks'];
        $qus_ans = $answer_info[0]['answer'];
        if ($provide_ans == 'correct') {
            $ans_is_right = 'correct';
        } else {
            $ans_is_right = 'wrong';
            $question_marks = 0;
        }

        if ($_POST['module_type'] == 1) {
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        }
    }


    public function organization()
    {
        $FaqClass=new \FaqClass();
        $StudentClass=new \StudentClass();

        if ($this->session->get('userType') == 6) {
            $data['video_help'] = $FaqClass->videoSerialize(15, 'video_helps');
            $data['video_help_serial'] = 15;
        }

        $_SESSION['prevUrl'] = base_url('/') . 'student';
        $data['types'] = $StudentClass->get_organizing('tbl_enrollment', $this->session->get('user_id'));

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('students/organize_type', $data);
    }

    public function studyType($id)
    {
        $FaqClass=new \FaqClass();
        $StudentClass=new \StudentClass();

        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        if ($id == 1) {
            $data['video_help'] = $FaqClass->videoSerialize(16, 'video_helps');
            $data['video_help_serial'] = 16;
        }
        if ($id == 2) {
            $data['video_help'] = $FaqClass->videoSerialize(17, 'video_helps');
            $data['video_help_serial'] = 17;
        }

        $_SESSION['prevUrl'] = base_url('/') . '/student/organization';
        $_SESSION['prevUrl_after_student_finish_buton'] = base_url('/') . $_SERVER['PATH_INFO'];

        $data['types'] = $id;

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('students/study_type',$data);
    }

    public function all_tutors_by_type($tutor_id, $module_type,$is_practice=0)
    {
        $FaqClass=new \FaqClass();
        $StudentClass=new \StudentClass();
        $ModuleClass=new \ModuleClass();
         //echo $is_practice."hello";die(); 
        $_SESSION['show_tutorial_result'] = 0;
        $data['tutor_id'] = $tutor_id;
        $data['module_type'] = $module_type;
        $session_module_info = $this->session->get('data');

        /*if ($session_module_info) {
            $tutorial_ans_info = $this->Student_model->getTutorialAnsInfo('tbl_temp_tutorial_mod_ques', $session_module_info[1]['module_id'], $this->session->userdata('user_id'));
        }

        if (isset($tutorial_ans_info) && !empty($tutorial_ans_info)) {*/
        //wrong answers by student on tutorial section
        // $this->Student_model->deleteInfo('tbl_temp_tutorial_mod_ques', 'st_id', $this->session->userdata('user_id'));
        //}
        
        $this->session->remove('data');
        $this->session->remove('obtained_marks');
        $this->session->remove('total_marks');
        $this->session->remove('isFirst');


        $data['moduleType'] = $module_type;
        $data['tutorInfo'] = $StudentClass->getInfo('tbl_useraccount', 'id', $tutor_id);

        
        //If not match with today date
        //$this->delete_st_error_ans(date('Y-m-d'));


        $data['user_info'] = $StudentClass->userInfo($this->loggedUserId);
        if ($module_type == 2 && $data['tutorInfo'][0]['user_type'] == 7) {
            $get_all_course = $StudentClass->studentCourses($this->loggedUserId);
            $course_match_with_subject_key_val = array();
            foreach ($get_all_course as $course) {
                $course['subject_id'] = $course['course_id'];
                $course['subject_name'] = $course['courseName'];
                $course_match_with_subject_key_val[] = $course;
            }
        } else {
            if ($data['tutorInfo'][0]['user_type'] == 7) {
                //            $data['studentSubjects'] = $this->Student_model->studentSubjects($this->loggedUserId);
                //$subject_with_course = $this->Student_model->studentSubjects($this->loggedUserId);

                $registered_courses = $StudentClass->registeredCourse($this->session->get('user_id'));
                
                $studentSubjects = array();
                if (count($registered_courses) > 0) {
                    $oreder_s = 0;

                    foreach ($registered_courses as $sub) {

                        $assign_course = $StudentClass->getInfo('tbl_assign_subject', 'course_id', $sub['id']);

                        if (!empty($assign_course)) {
                            $subjectId = json_decode($assign_course[0]['subject_id']);

                            foreach ($subjectId as $key => $value) {

                                $sb =  $StudentClass->getInfo('tbl_subject', 'subject_id', $value);

                                if (!empty($sb)) {
                                    $studentSubjects[$oreder_s]['subject_id'] = $sb[0]['subject_id'];
                                    $studentSubjects[$oreder_s]['subject_name'] = $sb[0]['subject_name'];
                                    $studentSubjects[$oreder_s]['created_by'] = $sb[0]['created_by'];
                                }
                                $oreder_s++;
                            }
                        }
                    }
                }
                $subject_with_course = $studentSubjects;
            }

            // if ($data['tutorInfo'][0]['user_type'] == 3) {
            // $data['studentSubjects'] = $this->Student_model->getInfo('tbl_subject', 'created_by', $tutor_id);
            // }

            if ($data['tutorInfo'][0]['user_type'] == 3) {

                //$subject_with_course = $this->Student_model->get_tutor_subject($tutor_id);
                //$data['studentSubjects'] = $subject_with_course;
                // $data['studentSubjects'] = array_values(array_column($students_all_subject, null, 'subject_id'));
                $subject_with_course = $StudentClass->getInfo('tbl_subject', 'created_by', $tutor_id);
            }
             $data['studentSubjects'] = $subject_with_course;
            //$students_all_subject = array();

            //foreach ($subject_with_course as $subject_course) {
            // $set_subject = 1;
            //if ($subject_course['isAllStudent'] == 0) {
            //$individualStudent = json_decode($subject_course['individualStudent']);
            //$individualStudent = is_null($individualStudent) ? [] : $individualStudent;
            //if (sizeof($individualStudent) && in_array($this->loggedUserId, $individualStudent)) {
            //    $set_subject = 1;
            // } else {
            //   $set_subject = 0;
            // }
            //}
            //if ($set_subject == 1) {
            //    $students_all_subject[] = $subject_course;
            // }
            //}

            //$data['studentSubjects'] = array_values(array_column($students_all_subject, null, 'subject_id'));
        }

        if ($tutor_id == 2) {
            $data['registered_courses'] = $StudentClass->registeredCourse($this->session->get('user_id'));

            $first_course_subjects = array();
            if (isset($data['registered_courses'][0]['id'])) {
                $first_course = $data['registered_courses'][0]['id'];
                $course_id = $first_course;
                if (isset($course_id) && $course_id != '') {
                    $assign_course = $StudentClass->getInfo('tbl_assign_subject', 'course_id', $course_id);
                    if (!empty($assign_course)) {
                        $subjectId = json_decode($assign_course[0]['subject_id']);

                        $sb =  $StudentClass->getInfo_subjects('tbl_subject', 'subject_id', $subjectId);
                    }
                }
            }

            if (isset($sb) && $sb != '') {
                $data['first_course_subjects'] = $sb;
                $data['first_course_id'] = $first_course;
                //$data['studentSubjects'] = $sb;
            }
        }

        if (!empty($_SERVER['HTTP_REFERER'])) {
            if (strpos($_SERVER['HTTP_REFERER'], "/show_tutorial_result/") || strpos($_SERVER['HTTP_REFERER'], "/get_tutor_tutorial_module/")) {
                if (!empty($_SESSION['prevUrl_after_student_finish_buton'])) {
                    $_SESSION['prevUrl'] = $_SESSION['prevUrl_after_student_finish_buton'];
                }
            } else {
                $_SESSION['prevUrl'] = $_SERVER['HTTP_REFERER'];
            }
        }
        
    //    echo '<pre>';
    //    print_r($data);die();
        $this->session->set('is_practice', $is_practice);

        $assignModuleByTutor = array();
        $assignModuleByTutor = $ModuleClass->studentHomework($tutor_id, $module_type);
        $data['assignModuleByTutorSubjectID'] = $assignModuleByTutor;
        
        $data['has_back_button'] = 'student';
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return view('students/module/all_module_list', $data);

    }

    public function studentsModuleByQStudyNew()
    {

        error_report_check();
        $FaqClass=new \FaqClass();
        $StudentClass=new \StudentClass();
        $ModuleClass=new \ModuleClass();

        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));

        $parent_id = $data['user_info'][0]['parent_id'];
        $payment_details = $this->db->table('tbl_payment')->where('user_id', $this->session->get('user_id'))->limit(1)->orderBy('id', 'desc')->get()->getRow();
        $payment_id = $payment_details->id;
        $payment_courses  = $StudentClass->paymentCourse($payment_id);

        $posts         = $this->request->getVar();
        $tutorId      = isset($posts['tutorId']) ? $posts['tutorId'] : '';
        $st_colaburation = 0;


        $payment_courses  = $this->db->table('tbl_registered_course')->where('user_id', $this->session->get('user_id'))->where('cost <>', 0)->where('endTime >', time())->get()->getResultArray();
        //echo "<pre>";print_r($payment_courses);die;

        foreach ($payment_courses as $pc => $value) {
            $val[$pc] = $value['id'];
            if ($val[$pc] == 44) {
                $st_colaburation = $st_colaburation + 1;
            }
        }
        $data['st_colaburation'] = $st_colaburation;

        if ($tutorId == 2) {
            if ($st_colaburation == 1) {
                echo strlen($row) ? $row : 'no module found';
                die;
            }
        }

        if (count($payment_courses) == 0) {
            echo strlen($row) ? $row : 'no module found';
            die;
        }


        $data['student_error_ans'] = $StudentClass->getInfo('tbl_st_error_ans', 'st_id', $this->session->get('user_id'));

        $post         = $this->request->getVar();
        $subjectId    = isset($post['subjectId']) ? $post['subjectId'] : '';
        $courseId     = isset($post['courseId']) ? $post['courseId'] : '';
        $chapterId    = isset($post['chapterId']) ? $post['chapterId'] : '';
        $moduleType   = isset($post['moduleType']) ? $post['moduleType'] : '';
        //        $tutorType  = isset($post['tutorId']) ? $post['tutorId'] : '';
        $tutorId      = isset($post['tutorId']) ? $post['tutorId'] : '';
        $tutorInfo = $StudentClass->getInfo('tbl_useraccount', 'id', $tutorId);

        // $studentGrade = $this->Student_model->studentClass($this->loggedUserId);

        $studentGrade_country = $StudentClass->studentClass($this->loggedUserId);
        $studentGrade = $studentGrade_country[0]['student_grade'];

        if ($tutorId == 2) {
            $student_colaburation = 0;
            $payCourses = $StudentClass->payment_list_Courses($parent_id);
            foreach ($payCourses as $payCours => $value) {
                $val[$payCours] = $value['id'];
                if ($val[$payCours] == 44) {
                    $student_colaburation = $student_colaburation + 1;
                }
            }
            $data['student_colaburation'] = $student_colaburation;
        }



        if ($student_colaburation == 1) {
            echo strlen($row) ? $row : 'no module found';
            die;
        }
        
        if ($subjectId == 'all') {
            $subjectId = '';
        }

       
        if (isset($tutorInfo[0]['user_type']) && $tutorInfo[0]['user_type'] == 7) { //q-study
            
            $conditions = array(
                'subject'              => $subjectId,
                 'course_id'            => $courseId,
                // 'chapter'              => $chapterId,
                'moduleType'           => $moduleType,
                'tbl_module.user_type' => 7,
                'studentGrade'         => $studentGrade,
            );
            //            if ($moduleType == 2) {
            //                $conditions['course_id'] = $subjectId;
            //            } else {
            //                $conditions['subject'] = $subjectId;
            //            }

            $conditions = array_filter($conditions);
            // Newly Added
            $data['all_subject_student'] = $StudentClass->getInfo('tbl_registered_course', 'user_id', $this->session->get('user_id'));
            $result = array_column($data['all_subject_student'], 'course_id');

            $registered_course = implode(', ', $result);
            // echo '<pre>';
            // print_r($registered_course);
            // die();
            if ($subjectId == 'all' || $subjectId == '') {
                $desired_result = '';
            } else {
                $desired_result = $subjectId;
            }

            // $data['all_subject_qStudy'] =$this->Student_model->get_all_subject($tutorInfo[0]['user_type']);
            // $data['all_subject_student'] =$this->Student_model->get_all_subject_for_registered_student($this->session->userdata('user_id'));

            // if ($subjectId == 'all' || $subjectId == '') {
            // $first_array_q = array_column($data['all_subject_qStudy'], 'subject_id');
            // $second_array_st = array_column($data['all_subject_student'], 'subject_id');

            // $desired_result = '';
            // $result = array_intersect($first_array_q, $second_array_st);
            // if ($result) {
            // $desired_result = implode(', ', $result);
            // }
            // } else {
            // $desired_result = $subjectId;
            // }
            
            
            if ($moduleType == 2 || $moduleType == 1) {
                // echo "hello1";;die();
                // $all_module = $this->ModuleModel->allModule(array_filter($conditions));
                $all_module = $ModuleClass->allModule(array_filter($conditions), $studentGrade_country[0]['country_id']);
            } else {

                $all_module = $StudentClass->all_module_by_type($tutorInfo[0]['user_type'], $desired_result, $result, $conditions);
            }
            // echo '<pre>';print_r($all_module);die;
            // $data['maincontent'] = $this->load->view('students/qstudy_module/all_tutorial_list', $data, true);
        } else { //module created by general tutor
            $conditions = array(
                'subject'              => $subjectId,
                'chapter'              => $chapterId,
                'moduleType'           => $moduleType,
                //            'tbl_module.user_type' => $tutorType,
                'studentGrade'         => $studentGrade,
                'user_id'              => $tutorId,
            );

            $conditions = array_filter($conditions);
            // $all_module = $this->ModuleModel->allModule(array_filter($conditions));
            $all_module = $ModuleClass->allModule(array_filter($conditions), $studentGrade_country[0]['country_id']);
        }
        //echo "ooooooooooook <pre>";print_r($all_module);die();
        // $all_module = $this->ModuleModel->allModule(array_filter($conditions));

        $new_array  = array();
        $sct_info  = array();

        //echo '<pre>';print_r($all_module);die;

        foreach ($all_module as $module) {
            if ($module['isAllStudent']) {
                $sct_info[] = $module;
            } elseif (strlen($module['individualStudent'])) {
                if ($module['individualStudent']) {
                    $stIds = json_decode($module['individualStudent']);

                    if (in_array($this->loggedUserId, $stIds)) {
                        $sct_info[] = $module;
                    }
                }
            }
        }

        if ($moduleType == 2) {
            foreach ($sct_info as $idx => $module) {
                $get_student_ans_by_module = $StudentClass->student_module_ans_info($this->session->get('user_id'), $module['id']);

                if ($this->site_user_data['student_grade'] != $module['studentGrade']) {
                    unset($sct_info[$idx]);
                } elseif ($module['repetition_days']) {
                    $repition_days = json_decode($module['repetition_days']);

                    $b = array_map(array($this, 'get_repitition_days'), $repition_days); //array_map("fix1", $repition_days);

                    date_default_timezone_set($this->site_user_data['zone_name']);
                    $today = date('Y-m-d');

                    // If Date match with repeated date And module ans is available for this student
                    if (in_array($today, $b) && $get_student_ans_by_module) {
                        $get_answer_repeated_module = $StudentClass->get_answer_repeated_module($this->session->get('user_id'), $module['id'], $today);
                        $st_ans = json_decode($get_student_ans_by_module[0]['st_ans'], true);

                        // If no ans is available for wrong and data is found in tbl_answer_repeated_module for this user id, module id and today date
                        if (!in_array('wrong', array_column($st_ans, 'ans_is_right')) || $get_answer_repeated_module) { // search value in the array
                            unset($sct_info[$idx]);
                        } else { // If wrong ans is available
                            $this->insert_error_question('', $st_ans);
                            $sct_info[$idx]['is_repeated'] = 1;
                        }
                    }

                    // If today not match with repeated date But module ans is available for this student
                    elseif ($get_student_ans_by_module) {
                        unset($sct_info[$idx]);
                    }
                } elseif (($module['repetition_days'] == '' && $get_student_ans_by_module)) {
                    unset($sct_info[$idx]);
                }
            }

            // Keep array with same index to match for all type of module
            foreach ($sct_info as $module) {
                $new_array[] = $module;
            }
            $this->show_all_module($new_array);
        } else {
            $this->show_all_module($all_module);
        }
    }

    public function insert_error_question($get_student_error_ans_info, $st_ans)
    {
        $StudentClass=new \StudentClass();
        foreach ($st_ans as $row) {
            // Insert only when 'tbl_st_error_ans' is empty for this student and for this module and if the worng answer is available
            if ($row['ans_is_right'] == 'wrong') {
                $data_err['st_id'] = $this->session->get('user_id');
                $data_err['question_id'] = $row['question_id'];
                $data_err['question_order_id'] = $row['question_order_id'];
                $data_err['module_id'] = $row['module_id'];
                $data_err['error_count'] = 1;

                $get_specific_error_data = $StudentClass->get_count_std_error_ans($row['question_order_id'], $row['module_id'], $this->session->get('user_id'));

                if (!$get_specific_error_data) {
                    $this->db->table('tbl_st_error_ans')->insert($data_err);
                }
            }
        }
    }


    public function show_all_module($allModule)
    {
        //echo "hello";print_r($allModule);die();
        $is_practice = $this->session->get('is_practice');
          
     
        date_default_timezone_set($this->site_user_data['zone_name']);

        //date_default_timezone_set('Australia/Sydney');
        // echo "<pre>"; print_r($allModule);

        $now_time = date('Y-m-d H:i:s');

        $now_time_for_additional = date("Y-m-d", strtotime($now_time));

        // echo $allModule[0]['exam_end'].'<pre>';
        // echo $now_time;//die;

        // if(strtotime($now_time) < strtotime($module['exam_end'])){
        //     echo 123;
        // }
        $count = 0;

        $row = '';
        if ($allModule) {

            if ($allModule[0]['moduleType'] != 3) {
                $row .= '<input type="hidden" id="first_module_id" value="' . $allModule[0]['id'] . '">';
            }

            foreach ($allModule as $module) {
                $now_time_for_additional_2 = date("Y-m-d", strtotime($module['exam_end']));
                if ($module['moduleType'] != 3 || ($module['optionalTime'] == 0 && $module['moduleType'] == 3 && strtotime($now_time) < strtotime($module['exam_end']))) {

                    if ($module['moduleType'] == 3 && $count == 0) {
                        // print_r($module);
                        $row .= '<input type="hidden" id="first_module_id" value="' . $module['id'] . '">';
                        $count++;
                    }
                    $is_repeated = '';
                    if (isset($module['is_repeated']) && $module['is_repeated'] == 1) {
                        $is_repeated = '(Repeated Module)';
                    }
					$base=base_url();
                    $video_link = json_decode($module['video_link']);
                    $link =$base.'/get_tutor_tutorial_module/' . $module['id'] . '/1';
		
                    /*if ($video_link) {
                        $link = 'video_link/'.$module['id'].'/'.$module['moduleType'];
                    }*/

                    $row .= '<tr>';
                    //$row .= '<td><a onclick="get_permission('.$module['id'].')" href="' . $link .'">' . $module['moduleName'] . '</a></td>';
                    if (isset($module['is_repeated'])) {

                        $date = date("d/m/Y", strtotime($module['answered_date']));
                        $required_repeted_module = json_decode($module['required_repeted_module'], true);

                        $row .= '<td> <ul> ';
                        foreach ($required_repeted_module as $key => $value) {
                            $pieces = explode("_", $value);
                            $day = $pieces[0];
                            if ($key == 0) {
                               
                                $row .= '<li> <a onclick="get_permission(' . $module['id'] . ')" href="javascript:;"> <span style="color:red;text-decoration: underline;"> Repeted wrong answer </span> <span class="text-muted" > ' . $date . ' </span> <span style="color:blue;" > ( ' . $day . ' Day) </span></a> </li>';
                            } else {
                                $row .= '<li> <a onclick="get_permission(0)" href="javascript:;"> <span style="color:red;text-decoration: underline;"> Repeted wrong answer </span> <span class="text-muted" > ' . $date . ' </span> <span style="color:blue;" > ( ' . $day . ' Day) </span></a> </li>';
                            }
                        }
                        $row .= '</ul> </td>';
                        $row .= '<td>' . $module['trackerName'] . '</td>';
                        $row .= '<td>' . $module['individualName'] . '</td>';
                    } else {
                        $row .= '<td><a onclick="get_permission(' . $module['id'] . ')" href="javascript:;">' . $module['moduleName'] . '</a></td>';
                        $row .= '<td>' . $module['trackerName'] . '</td>';
                        $row .= '<td>' . $module['individualName'] . '</td>';
                    }
                    //$row .= '<td style="cursor:pointer;"><a onclick="get_permission('.$module['id'].')">' . $module['moduleName'] . $is_repeated . '</a></td>';
                    // $row .= '<td>'.$module['creatorName'].'</td>';
                    if ($module['moduleType'] == 2 &&  $module['user_id'] == 2) {
                    } else {
                        $row .= '<td>' . $module['subject_name'] . '</td>';
                        $row .= '<td>' . $module['chapterName'] . '</td>';
                    }
                    $row .= '</tr>';
                }
                if ($module['optionalTime'] != 0 && $module['moduleType'] == 3 && ($now_time_for_additional_2 == $now_time_for_additional)) {


                    if ($module['moduleType'] == 3 && $count == 0) {
                        // print_r($module);
                        $row .= '<input type="hidden" id="first_module_id" value="' . $module['id'] . '">';
                        $count++;
                    }

                    // $count++;
                    $is_repeated = '';
                    if (isset($module['is_repeated']) && $module['is_repeated'] == 1) {
                        $is_repeated = '(Repeated Module)';
                    }
					$base=base_url();
                    $video_link = json_decode($module['video_link']);
                    $link = $base.'/get_tutor_tutorial_module/' . $module['id'] . '/1';
                    /*if ($video_link) {
                        $link = 'video_link/'.$module['id'].'/'.$module['moduleType'];
                    }*/

                    $row .= '<tr>';
                    //$row .= '<td><a onclick="get_permission('.$module['id'].')" href="' . $link .'">' . $module['moduleName'] . '</a></td>';
                    $row .= '<td><a onclick="get_permission(' . $module['id'] .')" href="javascript:;">' . $module['moduleName'] . '</a></td>';
                    //$row .= '<td style="cursor:pointer;"><a onclick="get_permission('.$module['id'].')">' . $module['moduleName'] . $is_repeated . '</a></td>';
                    // $row .= '<td>'.$module['creatorName'].'</td>';
                    $row .= '<td>' . $module['trackerName'] . '</td>';
                    $row .= '<td>' . $module['individualName'] . '</td>';
                    $row .= '<td>' . $module['subject_name'] . '</td>';
                    $row .= '<td>' . $module['chapterName'] . '</td>';
                    $row .= '</tr>';
                }
            }
        }
        echo strlen($row) ? $row : 'no module found';
    }

    
    public function get_permission()
    {

        error_report_check();
        $StudentClass=new \StudentClass();
        $TutorClass=new \TutorClass();
        //print_r($_SERVER);
        $check_url =  $_SERVER['HTTP_REFERER'];
        $word = "all_tutors_by_type";
        
        if(strpos($check_url, $word) !== false){
            $this->session->set('set_url_module_list', $check_url);
        }


        $ans_time_new = time();
        $this->session->set('take_ans_time_new', $ans_time_new);
        //echo $The_new_ans_time;

        $start_exam_time_new = time(); 
        $this->session->set('start_exam_time_new', $start_exam_time_new);
        //assignModuleByTutor 
        if (isset($_POST['assignModule'])  && !empty($_POST['assignModule'])) {
            $name_data['module_id_ASSIGNmodule'] = $this->request->getVar('module_id');
            $name_data['module_id_ASSIGNmoduleID'] = $this->request->getVar('id');
            $this->session->set($name_data);
        }
	
        $module_id = $this->request->getVar('module_id');
        $get_student_ans_by_module = $StudentClass->student_module_ans_info($this->session->get('user_id'), $module_id);
        $get_student_error_ans_info = $StudentClass->student_error_ans_info($this->session->get('user_id'), $module_id);
        $module = $StudentClass->getInfo('tbl_module', 'id', $module_id);

        $link = '';
        $b = [];
										
        // echo '<pre>';
        // print_r($module[0]['repetition_days']);
        // die();

        // First check module's repitition availability
        // IF match with repeated date and data found in student ans table
        // Do insert on st_error_ans

        if ($module[0]['moduleType'] != 1 && $module[0]['repetition_days'] && $module[0]['repetition_days'] != 'null') {
	
            if($module[0]['moduleType']==2){
                $this->session->set('set_tutor_id', 1);

            }
            // $studentProgress = $this->Student_model->studentEverydayProgree($this->session->userdata('user_id'),2);
            // if($studentProgress > 0){
            //     echo 3;die();
            // }
            $ck_repetation_update =  $StudentClass->repete_date_module_index_ck($module_id, $this->session->get('user_id'));
            if (count($ck_repetation_update) > 0) {

                $moduleCreated =  date("Y-m-d", strtotime($get_student_ans_by_module[0]['created_at']));
                $repition_days = strlen($ck_repetation_update[0]['repetation']) ? json_decode($ck_repetation_update[0]['repetation']) : [1, 2, 3];
                foreach ($repition_days as $key => $value) {
                    $singel_days[] = explode("_", $value)[0];
                }
                foreach ($singel_days as $key => $a) {
                    if ($key != 0) {
                        $new_repetation_day[] = $a . '_' . date('Y-m-d', strtotime($moduleCreated . ' +' . $a . ' days'));
                    }
                }
            } else {

                $studentProgress = $StudentClass->studentEverydayProgree($this->session->get('user_id'), 2);
                // echo 'hhhh'.$this->session->get('user_id');die();
                // echo '<pre>';
				// print_r($studentProgress);
				// die();
                //echo $studentProgress;die();
                if ($studentProgress > 0) {
                    echo 3;
                    die();
                }

                $daily_modules = $this->db->table('daily_modules')->where('user_id', $this->session->get('user_id'))->where('complete_date', date('Y-m-d'))->get()->getRow();
                if ($daily_modules) {
                    echo 3;
                    die();
                }

                $repition_days = strlen($module[0]['repetition_days']) ? json_decode($module[0]['repetition_days']) : [1, 2, 3];

                if (count($get_student_ans_by_module)) {
                    $moduleCreated =  date("Y-m-d", strtotime($get_student_ans_by_module[0]['created_at']));
                    foreach ($repition_days as $key => $value) {
                        $singel_days[] = explode("_", $value)[0];
                    }
                    foreach ($singel_days as $key => $a) {
                        $new_repetation_day[] = $a . '_' . date('Y-m-d', strtotime($moduleCreated . ' +' . $a . ' days'));
                    }
                }
            }

            function fix($n)
            {
                if ($n) {
                    $val = (explode('_', $n));
                    return $val[1];
                }
            }

            $repition_days_ = isset($new_repetation_day) ? $new_repetation_day : $repition_days;

            $b = array_map("fix", $repition_days_);
            $b = count($b) ? $b : [];

            date_default_timezone_set($this->site_user_data['zone_name']);
            $today = date('Y-m-d');

            $permission = false;

            foreach ($b as $key => $value) {
                if (strtotime($today)  >= strtotime($value)) {
                    $permission = true;
                }
            }

            if ($permission && $get_student_ans_by_module) {
                $st_ans = json_decode($get_student_ans_by_module[0]['st_ans'], true);
                if ($st_ans) {
                    $_SESSION['show_tutorial_result'] = 1;
                    $this->insert_error_question($get_student_error_ans_info, $st_ans);
					$base=base_url();				
                    foreach ($st_ans as $row) {
                        if ($row['ans_is_right'] == 'wrong') {
                            $link =$base.'/get_tutor_tutorial_module/' . $module_id . '/' . $row['question_order_id'];
                            $exact_time = time();
                            $this->session->set('exact_time', $exact_time);
                            $this->session->set('exam_start', $exact_time);
                            break;
                        }
                    }
                }
            }

            if (!$permission && $get_student_ans_by_module) {
                $st_ans = json_decode($get_student_ans_by_module[0]['st_ans'], true);
                if ($st_ans) {
                    $_SESSION['show_tutorial_result'] = 1;
                    $this->insert_error_question($get_student_error_ans_info, $st_ans);
					$base=base_url();
                    foreach ($st_ans as $row) {
                        if ($row['ans_is_right'] == 'wrong') {
                            $link = $base.'/get_tutor_tutorial_module/' . $module_id . '/' . $row['question_order_id'];
                            $exact_time = time();
                            $this->session->set('exact_time', $exact_time);
                            $this->session->set('exam_start', $exact_time);
                            break;
                        }
                    }
                }
            }
			
            if (!$get_student_ans_by_module) {
				
                $tbl_module_ans = $StudentClass->getTutorialAnsInfo('tbl_temp_tutorial_mod_ques_two', $module_id, $this->session->get('user_id'));

                if (count($tbl_module_ans)) {
                    $data = json_decode($tbl_module_ans[0]['st_ans'], true);
                    $order_id = $data[count($data)]['question_order_id'];
					$base=base_url();
                    $question_order_id = $order_id + 1;
                    $question_info_s = $TutorClass->getModuleQuestion($module_id, $question_order_id, null);
                    if (count($question_info_s)) {
                        $link = $base.'/get_tutor_tutorial_module/' . $module_id . '/' . $question_order_id . '';
                    } else {
                        $link = $base.'/get_tutor_tutorial_module/' . $module_id . '/' . $order_id . '';
                    }
                } else {
					$base=base_url();
                    $link = $base.'/get_tutor_tutorial_module/' . $module_id . '/1';
                }
            }
	
        } else {
				
            $video_link = json_decode($module[0]['video_link']);

            $tbl_module_ans = $StudentClass->getTutorialAnsInfo('tbl_temp_tutorial_mod_ques_two', $module_id, $this->session->get('user_id'));
			$base=base_url();
            if (count($tbl_module_ans)) {

                // $data = json_decode($tbl_module_ans[0]['st_ans'] , true);
                $data_new = json_decode($tbl_module_ans[0]['st_ans'], true);
                foreach ($data_new as $key => $data) {
                }
				$base=base_url();
                // print_r($data);die();
                // $order_id = $data[count($data)]['question_order_id'];
                $order_id = $data['question_order_id'];
			
                $question_order_id = $order_id + 1;
                $question_info_s = $TutorClass->getModuleQuestion($module_id, $question_order_id, null);
                if (count($question_info_s)) {
                    $link = $base.'/get_tutor_tutorial_module/' . $module[0]['id'] . '/' . $question_order_id . '';
                } else {
                    $link =$base.'/get_tutor_tutorial_module/' . $module[0]['id'] . '/' . $order_id . '';
                }
            } else {
				$base=base_url();
                $link =$base.'/get_tutor_tutorial_module/'.$module[0]['id'] . '/1';
            }
	
            if ($video_link) {

                $tbl_module_ans = $StudentClass->getTutorialAnsInfo('tbl_temp_tutorial_mod_ques_two', $module_id, $this->session->get('user_id'));

                if (count($tbl_module_ans)) {
                    $data = json_decode($tbl_module_ans[0]['st_ans'], true);
                    $order_id = $data[count($data)]['question_order_id'];
					$base=base_url();	
                    $question_order_id = $order_id + 1;
                    $question_info_s = $TutorClass->getModuleQuestion($module_id, $question_order_id, null);
                    if (count($question_info_s)) {
                        $link = $base.'/video_link/' . $module[0]['id'] . '/' . $question_order_id;
                    } else {
                        $link = $base.'/video_link/' . $module[0]['id'] . '/' . $order_id;
                    }
                } else {
					$base=base_url();
                    $link =$base.'/video_link/' . $module[0]['id'] . '/' . $module[0]['moduleType'];
                }
            }
        }
	
        //print_r($link); die();
        echo $link;
    }

    public function get_tutor_tutorial_module($modle_id, $question_order_id, $is_every_study = 0)
    {

        error_report_check();
        $data['time_zone_new']=$this->site_user_data['zone_name'];
        $StudentClass=new \StudentClass();
        $TutorClass=new \TutorClass();

        $select = '*';
        $table = 'tbl_module';

        $columnName = 'id';
        $columnValue = $modle_id;
        $data['']=
        $this->session->remove('memorization_one');
        $this->session->remove('memorization_two');
        $this->session->remove('memorization_three');
        $this->session->remove('memorization_std_ans');
        $this->session->remove('memorization_three_part');
        $this->session->remove('memorization_two_part');
        $this->session->remove('memorization_one_part');
        $this->session->remove('memorization_answer_right');
        $this->session->remove('memorization_answer_wrong');
        $this->session->remove('memorize_pattern_pattern_two_student_answer');

        $this->session->remove('firstleftSerial');
        $this->session->remove('question_setup_answer_order');

        $this->session->remove('memorization_three_qus_part_answer');
        $this->session->remove('memorize_pattern_three_student_answer');
        
        $this->session->remove('memorize_pattern_four_student_answer');

        $data['user_info'] = $StudentClass->userInfo($this->session->get('user_id'));
        $module_type = $TutorClass->get_all_where($select, $table, $columnName, $columnValue);
        // Get Student Ans From tbl_student_answer
        $flag = 0;
        $get_student_ans_info = $StudentClass->getTutorialAnsInfo('tbl_student_answer', $modle_id, $this->session->get('user_id'));
        //echo '<pre>';print_r($module_type[0]['moduleType']);die();
        if ($module_type[0]['moduleType'] != 2) {
            if ($get_student_ans_info) {
                $flag = 1;
            }
        }

        if ($module_type[0]['moduleType'] == 2) {
            $repition_days = json_decode($module_type[0]['repetition_days']);
            function fix($n)
            {
                if ($n) {
                    $val = (explode('_', $n));
                    return $val[1];
                }
            }
            $b = array();
            if ($repition_days) {
                $b = array_map("fix", $repition_days);
            }

            $today = date('Y-m-d');

            if (in_array($today, $b) && $get_student_ans_info) {
                $st_ans = json_decode($get_student_ans_info[0]['st_ans'], true);

                foreach ($st_ans as $row) {
                    $get_specific_error_ans = $StudentClass->get_specific_error_ans($row['question_id'], $question_order_id, $modle_id, $this->session->get('user_id'));

                    if ($row['question_order_id'] == $question_order_id && !$get_specific_error_ans) {
                        //$flag = 1;
                    }

                    if ($row['ans_is_right'] == 'correct' && $row['question_order_id'] == $question_order_id) {
                        $flag = 1;
                    }
                }
            }
        }


        if (!$module_type || $flag) {
            redirect('error_page');
        }
   
        // echo '<pre>';
        //print_r($module_type[0]['moduleType']);die();
        if ($module_type[0]['moduleType'] == 1) {
            $tbl_module_ans = $StudentClass->getTutorialAnsInfo('tbl_temp_tutorial_mod_ques_two', $modle_id, $this->session->get('user_id'));
		
            if (count($tbl_module_ans)) {
                $data = array();
                foreach (json_decode($tbl_module_ans[0]['st_ans']) as $key => $value) {
                    $the_first_start_time_new = time() - $this->session->get('start_exam_time_new');
                    $ind_ans = array(
                        'question_order_id' => $value->question_order_id,
                        'module_type' => $value->module_type,
                        'module_id' => $value->module_id,
                        'question_id' => $value->question_id,
                        'link' => $value->link,
                        'student_ans' => $value->student_ans,
                        'workout' => $value->workout,
                        'student_taken_time' => $the_first_start_time_new,
                        'student_question_marks' => $value->student_question_marks,
                        'student_marks' => $value->student_marks,
                        'ans_is_right' => $value->ans_is_right
                    );

                    $data[$key] = $ind_ans;
                }
                $this->session->set('data', $data);
                $this->session->set('obtained_marks', $tbl_module_ans[0]['obtained_marks']);
                $this->session->set('total_marks', $tbl_module_ans[0]['total_marks']);
            }
            // echo $modle_id;
            // echo $question_order_id;
            // die();
            $data=$this->openModuleByTutorialBased($modle_id, $question_order_id);

            // echo "<pre>"; print_r($data); die();
 		
            if($data['load_view']==1)
            {
                return view('students/question_module_type_tutorial/ans_general', $data);
            }
 
            if($data['load_view']==2)
            {
                return view('students/question_module_type_tutorial/ans_true_false', $data);
            }
            if($data['load_view']==3)
            { 
              return view('students/question_module_type_tutorial/ans_vocabulary',$data);
            }  
            if($data['load_view']==4)
            {  
                 return view('students/question_module_type_tutorial/ans_multiple_choice',$data);
            }    
            if($data['load_view']==6)
            {  
                 return view('students/question_module_type_tutorial/ans_skip',$data);
            }
            if($data['load_view']==10)
            {  
                 return view('students/question_module_type_tutorial/ans_times_table',$data);
            }
            if($data['load_view']==11)
            {  
                 return view('students/question_module_type_tutorial/ans_algorithm',$data);
            }
            if($data['load_view']==14)
            {  
                 return view('students/question_module_type_tutorial/ans_tutorial',$data);
            }
            if($data['load_view']==15)
            {  
            
                 return view('students/question_module_type_tutorial/ans_workout_quiz_two',$data);
            }
            if($data['load_view']==16)
            {  
            
                 return view('students/question_module_type_tutorial/ans_memorization',$data);
            }
            if($data['load_view']==17)
            {  
            
                 return view('students/question_module_type_tutorial/ans_creative_quiz',$data);
            }
            if($data['load_view']==18)
            {  
               
                 return view('students/question_module_type_tutorial/ans_sentence_match',$data);
            }
            if($data['load_view']==19)
            {  
                
                 return view('students/question_module_type_tutorial/ans_word_memorization',$data);
            }
            if($data['load_view']==20)
            {  
                
                 return view('students/question_module_type_tutorial/ans_comprehension',$data);
            }
            if($data['load_view']==21)
            {  

                 return view('students/question_module_type_tutorial/ans_grammer',$data);
            }
            if($data['load_view']==22)
            {  
                 return view('students/question_module_type_tutorial/ans_glossary',$data);
            }
            if($data['load_view']==23)
            {  
                 return view('students/question_module_type_tutorial/ans_imageQuiz',$data);
            }

        } elseif ($module_type[0]['moduleType'] == 2) {

            $x = $_SESSION;

            if (isset($x['data_exist_session']) && $x['data_exist_session'] == 1) {
                $tbl_module_ans = $StudentClass->getTutorialAnsInfo('tbl_temp_tutorial_mod_ques_two', $modle_id, $this->session->get('user_id'));

                if (count($tbl_module_ans)) {
                    $data = array();
                    foreach (json_decode($tbl_module_ans[0]['st_ans']) as $key => $value) {
                        $the_first_start_time_new = time() - $this->session->get('start_exam_time_new');
                        $ind_ans = array(
                            'question_order_id' => $value->question_order_id,
                            'module_type' => $value->module_type,
                            'module_id' => $value->module_id,
                            'question_id' => $value->question_id,
                            'link' => $value->link,
                            'student_ans' => $value->student_ans,
                            'workout' => $value->workout,
                            'student_taken_time' => $the_first_start_time_new,
                            'student_question_marks' => $value->student_question_marks,
                            'student_marks' => $value->student_marks,
                            'ans_is_right' => $value->ans_is_right
                        );

                        $data[$key] = $ind_ans;
                    }
                    $this->session->set('data', $data);
                    $this->session->set('obtained_marks', $tbl_module_ans[0]['obtained_marks']);
                    $this->session->set('total_marks', $tbl_module_ans[0]['total_marks']);
                }
            }
            // echo '<pre>';
            // print_r($_SESSION);
            // die();
            $data=$this->openModuleByTutorialBased($modle_id, $question_order_id);
         
            if($data['load_view']==1)
            {
                return view('students/question_module_type_tutorial/ans_general', $data);
            }
 
            if($data['load_view']==2)
            {
                return view('students/question_module_type_tutorial/ans_true_false', $data);
            }
            if($data['load_view']==3)
            { 
              return view('students/question_module_type_tutorial/ans_vocabulary',$data);
            }  
            if($data['load_view']==4)
            {  
                 return view('students/question_module_type_tutorial/ans_multiple_choice',$data);
            }    
            if($data['load_view']==6)
            {  
                 return view('students/question_module_type_tutorial/ans_skip',$data);
            }
            if($data['load_view']==10)
            {  
                 return view('students/question_module_type_tutorial/ans_times_table',$data);
            }
            if($data['load_view']==11)
            {  
                 return view('students/question_module_type_tutorial/ans_algorithm',$data);
            }
            if($data['load_view']==14)
            {  
                 return view('students/question_module_type_tutorial/ans_tutorial',$data);
            }
            if($data['load_view']==15)
            {  
            
                 return view('students/question_module_type_tutorial/ans_workout_quiz_two',$data);
            }
            if($data['load_view']==16)
            {  
            
                 return view('students/question_module_type_tutorial/ans_memorization',$data);
            }
            if($data['load_view']==17)
            {  
            
                 return view('students/question_module_type_tutorial/ans_creative_quiz',$data);
            }
            if($data['load_view']==18)
            {  
               
                 return view('students/question_module_type_tutorial/ans_sentence_match',$data);
            }
            if($data['load_view']==19)
            {  
                
                 return view('students/question_module_type_tutorial/ans_word_memorization',$data);
            }
            if($data['load_view']==20)
            {  

                 return view('students/question_module_type_tutorial/ans_comprehension',$data);
            }
            if($data['load_view']==20)
            {  
                 return view('students/question_module_type_tutorial/ans_glossary',$data);
            }


        } elseif ($module_type[0]['moduleType'] == 3) {
            $data['module_info'] = $this->Student_model->module_info($modle_id, $module_type[0]['moduleType'], $data['user_info'][0]['student_grade']);

            date_default_timezone_set($data['user_info'][0]['zone_name']);
            // echo $data['user_info'][0]['zone_name'];die();
            // date_default_timezone_set('Australia/Sydney');
            $module_time = time();

            $date_now = date('Y-m-d');



            if ((strtotime($data['module_info'][0]['exam_start']) < $module_time) && (strtotime($data['module_info'][0]['exam_end']) > $module_time)) {
                $this->openModuleByTutorialBased($modle_id, $question_order_id);
            } elseif ($date_now == trim($data['module_info'][0]['exam_start']) && $data['module_info'][0]['optionalTime'] > 0) {

                $this->openModuleByTutorialBased($modle_id, $question_order_id);
            } elseif (strtotime($data['module_info'][0]['exam_end']) < $module_time && $data['module_info'][0]['optionalTime'] == 0) {

                show_404();
            } else {
                $this->session->set_flashdata('message_name', 'Your exam will start at ' . date('h:i A', strtotime($data['module_info'][0]['exam_start'])));
                redirect('all_tutors_by_type/' . $module_type[0]['user_id'] . '/' . $data['module_info'][0]['moduleType']);
            }
        } elseif ($module_type[0]['moduleType'] == 4) {
            $this->openModuleByTutorialBased($modle_id, $question_order_id);
        }
    }


    private function openModuleByTutorialBased($modle_id, $question_order_id)
    {
        // echo 11; die();

        error_report_check();
        $StudentClass=new \StudentClass();
        $ModuleClass=new \ModuleClass();
        $TutorClass=new \TutorClass();
        $PreviewClass=new \PreviewClass();

        $uri = new \CodeIgniter\HTTP\URI(current_url());
        $start_exam_time_new = time();
        $this->session->set('start_exam_time_new', $start_exam_time_new);
        $data['order'] = $uri->getSegment('3');
        $_SESSION['q_order'] = $uri->getSegment('3');
        $_SESSION['q_order_module'] = $uri->getSegment('2');

        $data['module_info'] = $StudentClass->getInfo('tbl_module', 'id', $modle_id);

        $data['user_infos'] = $StudentClass->get_user_informations($data['user_id']);
     
        if (!$data['module_info'][0]) {
            return view('erros/html/error_404');
        }
        $qstudy_module_videos = array(); //echo 'asce re1';die();
        if ($data['module_info'][0]['user_type'] == 7) {
            $qstudy_module_videos = $ModuleClass->getInfoByOrder('module_instruction_videos_new', 'module_id', $modle_id);
        }
        $data['qstudy_module_videos'] = $qstudy_module_videos;

        $isFirst = 1;
  
        $data['user_info'] = $TutorClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
      
        // Special Exam
        if (!$this->session->get('isFirst')) {
            $this->session->set('isFirst', $isFirst);
            if ($question_order_id == 1) {
                date_default_timezone_set($this->site_user_data['zone_name']);
                //echo 'Exam Time: '.$data['module_info'][0]['exam_start'].'<br>';
                $exact_time = time();
                $this->session->set('exact_time', $exact_time);
                $this->session->set('exam_start', $exact_time);
            }
        }
        // Everyday Study & Tutorial
        if (
            $data['module_info'][0]['moduleType'] == 1
            || $data['module_info'][0]['moduleType'] == 2
            || $data['module_info'][0]['moduleType'] == 4
        ) {
            date_default_timezone_set($this->site_user_data['zone_name']);
            $exact_time = time();
            $this->session->set('exact_time', $exact_time);
            // if ($question_order_id == 1) {
            //     $this->session->set_userdata('exam_start', $exact_time);
            // }

            $this->session->set('exam_start', $exact_time);
        }
      
        //****** Get Temp table data for Tutorial Module Type ******
      
        if ($data['module_info'][0]['moduleType'] == 2) {
            $table = 'tbl_student_answer';
        } else {
            $table = 'tbl_temp_tutorial_mod_ques';
        }

        $data['tutorial_ans_info'] = $StudentClass->getTutorialAnsInfo($table, $modle_id, $data['user_info'][0]['id']);
        $data['question_info_s'] = $TutorClass->getModuleQuestion($modle_id, $question_order_id, null);
        //echo $question_order_id.'abc';die();
        //echo "<pre>".$this->db->last_query();print_r($data['question_info_s']);die();
        /*if (!isset($data['question_info_s'][0])) {
            //question not exists
            show_404();
        }*/
    
        // echo $data['question_info_s'][0]['question_type']; die();

        // echo '<pre>';
        // print_r($data['question_info_s']);
        // die();

        if (!$data['question_info_s'][0]['id']) {
            $question_order_id = $question_order_id + 1;
            return redirect()->to(base_url('/get_tutor_tutorial_module/' . $modle_id . '/' . $question_order_id));
        }
        $data['total_question'] = $TutorClass->getModuleQuestion($modle_id, null, 1);
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
				
        //echo "<pre>";print_r($data['total_question']);die();
        // $data['headerlink'] = $this->load->view('dashboard_template/headerlink', $data, true);
        // $data['header'] = '';
        // $data['footerlink'] = $this->load->view('dashboard_template/footerlink', $data, true);
        
        //video link classify
        $moduleVidLinks = json_decode($data['module_info'][0]['video_link']);

        $data['moduleVid'] = count($moduleVidLinks) ? trim($moduleVidLinks[0]) : '';

        $url = $data['moduleVid']; 
        $regex_pattern = "/(youtube.com|youtu.be)\/(watch)?(\?v=)?(\S+)?/";
        $match;

        if (preg_match($regex_pattern, $url, $match)) {
            //echo "Youtube video id is: ".$match[4];
            $data['moduleVidType'] = 'youtube';
        } else {
            //echo "Sorry, not a youtube URL";
            $data['moduleVidType'] = 'general';
        }

        // echo $data['question_info_s'][0]['question_type'];die();
       
        if ($data['question_info_s'][0]['question_type'] == 1) {
            $_SESSION['q_order_2'] = $uri->getSegment('3');
            $data['load_view']=1;
            return $data;

        } elseif ($data['question_info_s'][0]['question_type'] == 2) {

            $_SESSION['q_order_2'] = $uri->getSegment('3');
            $data['load_view']=2;
            return $data;
        } elseif ($data['question_info_s'][0]['question_type'] == 3) {
            $_SESSION['q_order_2'] = $uri->getSegment('3');
            $data['question_info_vcabulary'] = json_decode($data['question_info_s'][0]['questionName']);
            $data['load_view']=3;
            return $data;

        } elseif ($data['question_info_s'][0]['question_type'] == 4) {
            $_SESSION['q_order_2'] = $uri->getSegment('3');

            $data['question_info_vcabulary'] = json_decode($data['question_info_s'][0]['questionName']);
            $data['load_view']=4;
            return $data;

        } elseif ($data['question_info_s'][0]['question_type'] == 5) {
            $_SESSION['q_order_2'] = $this->uri->segment('3');

            $data['question_info_vcabulary'] = json_decode($data['question_info_s'][0]['questionName']);
            $data['maincontent'] = $this->load->view('students/question_module_type_tutorial/ans_multiple_response', $data, true);
        } elseif ($data['question_info_s'][0]['question_type'] == 6) {

            $_SESSION['q_order_2'] = $uri->getSegment('3');
            $quesInfo = $TutorClass->getModuleQuestion($modle_id, $question_order_id, null);

            $data['question_info_s'] = $quesInfo;
            $questionType = $quesInfo[0]['questionType'];
            $quesInfo = json_decode($quesInfo[0]['questionName']);
            $data['question_info_skip'] = json_decode($data['question_info_s'][0]['questionName']);

            $data['numOfRows'] = isset($quesInfo->numOfRows) ? $quesInfo->numOfRows : 0;
            $data['numOfCols'] = isset($quesInfo->numOfCols) ? $quesInfo->numOfCols : 0;
            $data['questionBody'] = isset($quesInfo->question_body) ? $quesInfo->question_body : '';

            $data['questionId'] = $data['question_info_s'][0]['question_id'];
            $data['question_id'] = $data['question_info_s'][0]['question_id'];

            $quesAnsItem = $quesInfo->skp_quiz_box;
            $items = $this->indexQuesAns($quesAnsItem);
            $data['skp_box'] = $this->renderSkpQuizPrevTable($items, $data['numOfRows'], $data['numOfCols']);

            $data['load_view']=6;
            return $data;

        } elseif ($data['question_info_s'][0]['question_type'] == 7) {
            $_SESSION['q_order_2'] = $this->uri->segment('3');

            $data['question_info_left_right'] = json_decode($data['question_info_s'][0]['questionName']);
            $data['maincontent'] = $this->load->view('students/question_module_type_tutorial/ans_matching', $data, true);
        } elseif ($data['question_info_s'][0]['question_type'] == 8) {
            $_SESSION['q_order_2'] = $this->uri->segment('3');

            $quesInfo     = json_decode($data['question_info_s'][0]['questionName']);
            $questionBody            = isset($quesInfo->question_body) ? $quesInfo->question_body : '';
            $data['questionBody']    = $questionBody;
            $items                   = $quesInfo->assignment_tasks;
            $data['totalItems']      = count($items);
            $data['assignment_list'] = $this->renderAssignmentTasks($items);

            $data['maincontent'] = $this->load->view('students/question_module_type_tutorial/ans_assignment', $data, true);
            $this->load->view('master_dashboard', $data);
        } elseif ($data['question_info_s'][0]['questionType'] == 9) {
            $_SESSION['q_order_2'] = $this->uri->segment('3');
            $info = array();
            $titles = array();
            $title = array();
            $questionList = json_decode($data['question_info_s'][0]['questionName'], true);
            //title
            foreach (json_decode($data['question_info_s'][0]['questionName'])->wrongTitles as $key => $value) {
                $title[0] = $value;
                $title[1] = json_decode($data['question_info_s'][0]['questionName'])->wrongTitlesIncrement[$key];
                $titles[] = $title;
            }
            $title[0] = json_decode($data['question_info_s'][0]['questionName'])->rightTitle;
            $title[1] = "right_ones_xxx";
            $titles[] = $title;
            shuffle($titles);
            $info['titles'] = $titles;
            //intro
            $titles = array();
            $title = array();

            foreach (json_decode($data['question_info_s'][0]['questionName'])->wrongIntro as $key => $value) {
                $title[0] = $value;
                $title[1] = json_decode($data['question_info_s'][0]['questionName'])->wrongIntroIncrement[$key];
                $titles[] = $title;
            }

            $title[0] = json_decode($data['question_info_s'][0]['questionName'])->rightIntro;
            $title[1] = "right_ones_xxx";
            $titles[] = $title;
            shuffle($titles);
            $info['Intro'] = $titles;

            //picture

            $titles = array();
            $title = array();

            foreach (json_decode($data['question_info_s'][0]['questionName'])->pictureList as $key => $value) {
                $title[0] = $value;
                $title[1] = $questionList['wrongPictureIncrement'][$key];
                $titles[] = $title;
            }

            $title[0] = json_decode($data['question_info_s'][0]['questionName'])->lastpictureSelected;
            $title[1] = "right_ones_xxx";
            $titles[] = $title;
            shuffle($titles);
            $info['picture'] = $titles;

            //paragraph
            $paragraph = json_decode($data['question_info_s'][0]['questionName'], true);
            $paragraph = $paragraph['Paragraph'];

            $info['paragraph'] = $paragraph;

            $wrongParagraphIncrement = array();
            $w = 1;
            foreach ($paragraph as $key => $value) {
                if (isset($value['WrongAnswer'])) {
                    $wrongParagraphIncrement[$key] = $questionList['wrongParagraphIncrement'][$w];
                    $w++;
                }
            }
            $info['wrongParagraphIncrement'] = $wrongParagraphIncrement;

            //picture

            $titles = array();
            $title = array();

            foreach (json_decode($data['question_info_s'][0]['questionName'])->wrongConclution as $key => $value) {
                $title[0] = $value;
                $title[1] = $questionList['wrongConclutionIncrement'][$key];
                $titles[] = $title;
            }

            $title[0] = json_decode($data['question_info_s'][0]['questionName'])->rightConclution;
            $title[1] = "right_ones_xxx";
            $titles[] = $title;
            shuffle($titles);

            $info['conclution'] = $titles;
            $data['question'] = $info;

            $data['maincontent']     = $this->load->view('students/question_module_type_tutorial/ans_storyWrite', $data, true);
        } elseif ($data['question_info_s'][0]['question_type'] == 10) {
            $_SESSION['q_order_2'] = $uri->getSegment('3');

            $data['question_info']   = json_decode($data['question_info_s'][0]['questionName'], true);
            $data['load_view']=10;
            return $data;

        } elseif ($data['question_info_s'][0]['question_type'] == 11) {
            $_SESSION['q_order_2'] = $uri->getSegment('3');

            $data['question_info']   = json_decode($data['question_info_s'][0]['questionName'], true);
            //                echo '<pre>';print_r($data['question_info']);die;
            $data['load_view']=11;
            return $data;

        } elseif ($data['question_info_s'][0]['question_type'] == 12) {
            $_SESSION['q_order_2'] = $this->uri->segment('3');

            $data['maincontent'] = $this->load->view('students/question_module_type_tutorial/ans_workout_quiz', $data, true);
        } elseif ($data['question_info_s'][0]['question_type'] == 13) {
            $_SESSION['q_order_2'] = $this->uri->segment('3');

            $data['question_info_vcabulary'] = json_decode($data['question_info_s'][0]['questionName']);
            $data['maincontent'] = $this->load->view('students/question_module_type_tutorial/ans_matching_workout', $data, true);
        } elseif ($data['question_info_s'][0]['question_type'] == 14) {

            $_SESSION['q_order_2'] = $uri->getSegment('3');
            if (!empty($_SERVER['HTTP_REFERER'])) {
                $_SESSION["previous_page"] = $_SERVER['HTTP_REFERER'];

                $data["last_question_order"] = $_SESSION['q_order_2'];
                // print_r($_SESSION["previous_page"]); die();
                $data['question_info_vcabulary'] = json_decode($data['question_info_s'][0]['questionName']);
                // print_r(['question_info_vcabulary']); die();
                $tutorialId = $data['question_info_s'][0]['question_id'];
                $data['tutorialInfo'] = $TutorClass->getInfo('for_tutorial_tbl_question', 'tbl_ques_id', $tutorialId);

                $data['load_view']=14;
                return $data;

            } else {
                return redirect()->to(base_url($_SESSION["previous_page"]));
            }
        } elseif ($data['question_info_s'][0]['questionType'] == 15) {
            //echo 'asce re';die();
            $data['question_item'] = $data['question_info_s'][0]['questionType'];
            $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
            $data['question_info_ind'] = $data['question_info'];
            if (isset($data['question_info_ind']->percentage_array)) {
                $data['ans_count'] = count((array)$data['question_info_ind']->percentage_array);
            } else {
                $data['ans_count'] = 0; 
            }

            $data['load_view']=15;
            return $data;

        } elseif ($data['question_info_s'][0]['questionType'] == 16) {
            $data['question_item'] = $data['question_info_s'][0]['questionType'];
            $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
            $data['question_info_ind'] = $data['question_info'];



            $question_info_ind = $data['question_info'];
            $pattern_type = $question_info_ind->pattern_type;
            if ($pattern_type == 4) {
                $qus_lefts = $question_info_ind->left_memorize_p_four;
                $qus_rights = $question_info_ind->right_memorize_p_four;

                $qus_array = [];
                foreach ($qus_lefts as $key => $value) {
                    $qus_array[$key]['left'] = $value;
                    $qus_array[$key]['right'] = $qus_rights[$key];
                }
                // shuffle($qus_array);
                $data['qus_array'] = $qus_array;
            }



            if ($pattern_type == 3) {
                $question_step = $question_info_ind->question_step_memorize_p_three;

                $qus_setup_array = [];
                $k = 1;
                $inv = 0;
                foreach ($question_step as $key => $value) {
                    $qus_setup_array[$key]['question_step'] = $value[0];
                    $qus_setup_array[$key]['clue'] = $value[1];
                    $qus_setup_array[$key]['ecplanation'] = $value[2];
                    $qus_setup_array[$key]['answer_status'] = $value[3];
                    if ($value[3] == 0) {
                        $qus_setup_array[$key]['order'] = $k;
                        $k = $k + 1;
                    } else {
                        $qus_setup_array[$key]['order'] = $inv;
                        $inv--;
                    }
                }
                $data['qus_setup_array'] = $qus_setup_array;


                $this->session->set('question_setup_answer_order', 1);
            }


            if (isset($data['qus_setup_array'])) {

                $question_step_details = $data['qus_setup_array'];

                shuffle($question_step_details);
                $data['question_step_details'] = $question_step_details;
            }
            //echo "<pre>";print_r($data);die();

            $data['load_view']=16;
            return $data;

        }elseif ($data['question_info_s'][0]['questionType'] == 17) {

            $_SESSION['q_order_2'] = $uri->getSegment('4');

            $question_id=$data['question_info_s'][0]['id'];
            $data['idea_info'] = $PreviewClass->getIdeaInfo('idea_info', $question_id);
            $data['idea_description'] = $PreviewClass->getIdeaDescription('idea_description', $question_id);

            $data['user_id'] = $this->session->get('user_id');
            $data['profile'] = $StudentClass->get_profile_info($data['user_id']);
            $data['student_ideas'] = $StudentClass->get_student_ideas($question_id,$modle_id);
            $data['tutor_ideas'] = $StudentClass->get_tutor_ideas($question_id,$modle_id);
           
            $data['load_view']=17;
            
            // echo "<pre>";print_r($data['load_view']);die();

            return $data;
   
        }elseif ($data['question_info_s'][0]['questionType'] == 18) {
            $_SESSION['q_order_2'] = $uri->getSegment('3');

            $data['sentence_questions'] = json_decode($data['question_info_s'][0]['questionName']);
            $data['sentence_answers'] = json_decode($data['question_info_s'][0]['answer']);
            // print_r($data['sentence_answers']);die();
            $data['load_view']=18;
            return $data;

        }elseif ($data['question_info_s'][0]['questionType'] == 19) {
       
            $_SESSION['q_order_2'] = $uri->getSegment('3');
            $data['word_questions'] = json_decode($data['question_info_s'][0]['questionName'], true);
            $data['word_answers'] = json_decode($data['question_info_s'][0]['answer'], true);
            // print_r($data['word_answers']);die();
            $data['load_view']=19;
            return $data; 
        }
        elseif ($data['question_info_s'][0]['questionType'] == 20) {
           
            $_SESSION['q_order_2'] = $uri->getSegment('3');
            $data['comprehension_questions'] = json_decode($data['question_info_s'][0]['questionName'], true);
            $data['comprehension_answers'] = json_decode($data['question_info_s'][0]['answer'], true);
            // print_r($data['sentence_answers']);die();
            $data['load_view']=20;
            return $data; 
        }
        elseif ($data['question_info_s'][0]['questionType'] == 21) {
           
            $_SESSION['q_order_2'] = $uri->getSegment('3');
            $data['grammer_questions'] = json_decode($data['question_info_s'][0]['questionName'], true);
            $data['grammer_answers'] = json_decode($data['question_info_s'][0]['answer'], true);
            // print_r($data['sentence_answers']);die();
            $data['load_view']=21;
            return $data; 
        }
        elseif ($data['question_info_s'][0]['questionType'] == 22) {
            $_SESSION['q_order_2'] = $uri->getSegment('3');
            $data['glossary_questions'] = json_decode($data['question_info_s'][0]['questionName'], true);
            $data['glossary_answers'] = json_decode($data['question_info_s'][0]['answer'], true);
            // print_r($data['sentence_answers']);die();
            $data['load_view']=22;
            return $data; 
        }
        elseif ($data['question_info_s'][0]['questionType'] == 23) {
            $_SESSION['q_order_2'] = $uri->getSegment('3');
            $data['image_questions'] = json_decode($data['question_info_s'][0]['questionName'], true);
            $data['image_answers'] = json_decode($data['question_info_s'][0]['answer'], true);
            // print_r($data['sentence_answers']);die();
            $data['load_view']=23;
            return $data; 
        }

    }

    public function st_answer_sentence_matching()
    {

        $PreviewClass=new \PreviewClass();
        $question_id = $_POST['id']; 
        $module_id = $_POST['module_id'];
        $question_order_id = $_POST['current_order'];

        $student_answers = $_POST['answer'];
        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $question_marks = $answer_info[0]['questionMarks'];
        $question_answers = json_decode($answer_info[0]['answer']);
        //print_r($student_answers);die();
        //echo $student_ans[1];die();
        $ans_set = array();
        $ans_is_right = 'correct';
        foreach($question_answers as $key => $question_answer){
            $ans_with_ques_no = explode(",,", $student_answers[$key]);
            $student_ans = $ans_with_ques_no[0];
            $question_no = $ans_with_ques_no[1];
            $ans_set[$key] = $student_ans;
            if($question_answer==$student_ans){

            }else{
                $ans_is_right = 'wrong';
            }

        }
        $_POST['answer'] = $ans_set;

        if ($_POST['module_type'] == 1) {
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        }
    }

    public function st_answer_matching()
    {

        $PreviewClass=new \PreviewClass(); 
        $question_id = $this->request->getVar('question_id');
        $module_id = $this->request->getVar('module_id');
        //$question_order_id = $_POST['check_order_id'] - 1;
        $question_order_id = $this->request->getVar('current_order');
        $text = $this->request->getVar('answer');
        //
        $find = array('&nbsp;', '\n', '\t', '\r');
        $repleace = array('', '', '', '');
        $text = strip_tags($text);
        $text = str_replace($find, $repleace, $text);
        $text = trim($text);

        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);


        $question_marks = $answer_info[0]['questionMarks'];

        $text_1 = $answer_info[0]['answer'];
        $find = array('&nbsp;', '\n', '\t', '\r');
        $repleace = array('', '', '', '');
        $text_1 = strip_tags($text_1);
        $text_1 = str_replace($find, $repleace, $text_1);
        $text_1 = trim($text_1);

        $ans_is_right = 'correct';
        if ($text != $text_1) {
            $ans_is_right = 'wrong';
        }

        if ($_POST['module_type'] == 1) {
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        }
    }

    public function st_answer_matching_true_false()
    {
        //        echo '<pre>';print_r($_POST);
        $PreviewClass=new \PreviewClass();
        $text = $this->request->getVar('answer');
        $question_id = $this->request->getVar('question_id');
        $module_id = $this->request->getVar('module_id');
        $question_order_id = $this->request->getVar('current_order');
        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);

        $text_1 = $answer_info[0]['answer'];

        $ans_is_right = 'correct';
        if ($text != $text_1) {
            $ans_is_right = 'wrong';
        }

        $question_marks = $answer_info[0]['questionMarks'];
        if ($_POST['module_type'] == 1) {
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        }
    }

    public function st_answer_matching_vocabolary()
    {

        $PreviewClass=new \PreviewClass();
        $text = strtolower($this->request->getVar('answer'));
        $question_id = $this->request->getVar('question_id');
        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);

        $module_id = $_POST['module_id'];
        // $question_order_id = $_POST['next_question'] - 1;
        $question_order_id = $_POST['current_order'];
        $text_1 = strtolower($answer_info[0]['answer']);
        $question_marks = $this->request->getVar('obtain_marks');

        $ans_is_right = 'correct';
        if ($text != $text_1) {
            $ans_is_right = 'wrong';
        }

        if ($_POST['module_type'] == 1) {
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        }
    }

    public function st_answer_matching_multiple_choice()
    {
       // print_r($_POST);die;

        $PreviewClass=new \PreviewClass();
        $question_id = $_POST['id'];
        if (isset($_POST['answer'])) {
            $text_1 = $_POST['answer'];
        } else {

            $text_1 = array();
        }

        $answer_info = $PreviewClass->getInfo('tbl_question','id',$question_id);

        $question_marks = $answer_info[0]['questionMarks'];
        //$text = $answer_info[0]['answer'];

        // $ans_is_right = 'correct';
        // if ($text != $text_1) {
        //     $ans_is_right = 'wrong';
        // }

        $text = json_decode($answer_info[0]['answer']);

        $result_count = 1;
        if ($text_1) {
            $result_count = count(array_intersect($text_1, $text));
        }

        $module_id = $_POST['module_id'];
        $question_order_id = $_POST['current_order'];

        $ans_is_right = 'correct';
        if (count($text_1) != $result_count) {
            $ans_is_right = 'wrong';
        }

        if (count($text_1) != count($text)) {
            $ans_is_right = 'wrong';
        }

        if ($_POST['module_type'] == 1) {
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        }
    }

    public function st_answer_skip()
    {
        error_report_check();
        $TutorClass=new \TutorClass();
        $module_id = $_POST['module_id'];
        //$question_order_id = $_POST['next_question'] - 1;
        $question_order_id = $_POST['current_order'];
        $post = $this->request->getVar();
        $questionId = $this->request->getVar('id');
        $text = 0;
        $text_1 = 0;

        $temp = $TutorClass->getInfo('tbl_question', 'id', $questionId);

        $answer_info = array();

        $question_marks = $temp[0]['questionMarks'];

        if (strlen(implode($post['given_ans'])) != 0) {
            $givenAns = $this->indexQuesAns($post['given_ans']);

            $savedAns = $this->indexQuesAns(json_decode($temp[0]['answer']));

            $temp2 = json_decode($temp[0]['questionName']);
            $numOfRows = $temp2->numOfRows;
            $numOfCols = $temp2->numOfCols;
            //echo $numOfRows .' ' . $numOfCols;
            $wrongAnsIndices = [];

            $answer_info = $givenAns;

            for ($row = 1; $row <= $numOfRows; $row++) {
                for ($col = 1; $col <= $numOfCols; $col++) {
                    if (isset($savedAns[$row][$col])) {
                        if (isset($givenAns[$row][$col])) {
                            $wrongAnsIndices[] = ($savedAns[$row][$col] != $givenAns[$row][$col]) ? $row . '_' . $col : null;
                        } else {
                            $wrongAnsIndices[] = $row . '_' . $col;
                        }
                    }
                }
            }

            $wrongAnsIndices = array_filter($wrongAnsIndices);
            if (count($wrongAnsIndices)) { //For False Condition
                $text_1 = 1;
            }
        }
        if (strlen(implode($post['given_ans'])) == 0) {
            $text_1 = 1;
        }

        $ans_is_right = 'correct';
        if ($text != $text_1) {
            $ans_is_right = 'wrong';
        }

        if ($_POST['module_type'] == 1) {
            //echo $text_1;die;
            $this->take_decesion_1($question_marks, $questionId, $module_id, $question_order_id, $ans_is_right, array());
        } else {
            $this->take_decesion_2($question_marks, $questionId, $module_id, $question_order_id, $ans_is_right, array());
        }
    }

    public function st_answer_times_table()
    {
        $PreviewClass=new \PreviewClass();
        $question_id = $this->request->getVar('question_id');
        $result = $this->request->getVar('answer');

        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);
        $question_marks = $answer[0]['questionMarks'];
        $answer_info['student_ans'] = $result;

        $module_id = $this->request->getVar('module_id');
        //$question_order_id = $_POST['next_question'] - 1;
        $question_order_id = $this->request->getVar('current_order');
        $text = 0;
        $text_1 = 0;
        $flag = 1;

        for ($k = 0; $k < sizeof($answer_info['student_ans']); $k++) {
            if ($answer_info['student_ans'][$k] != $answer_info['tutor_ans'][$k]) {
                $text++;
                $flag = 0;
            }
        }
        // echo 'Flag: ';echo $flag;die;
        $ans_is_right = 'correct';
        if ($flag == 0) {
            $ans_is_right = 'wrong';
        }

        $answer_info['student_ans'] = $result;
        $answer_info['flag'] = $flag;

        if ($this->request->getVar('module_type') == 1) {
            //echo 11111111;die;
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right, array());
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right, array());
        }
    }

    public function st_answer_algorithm()
    {
        $PreviewClass=new \PreviewClass();
        $question_id = $this->request->getVar('question_id');
        $result = $this->request->getVar('answer');
        //        $result['reminder'] = $this->input->post('reminder');

        if (isset($result[0])) {
            $ans_one = $result[0];
        } else {
            $ans_one = $result;
        }

        if (isset($result[1])) {
            $reminder_answer = $result[1];
        } else {
            $reminder_answer = '';
        }

        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $question_info = json_decode($answer[0]['questionName'], true);
        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);
        //        $question_marks = $answer[0]['questionMarks'];
        $question_marks = $answer[0]['questionMarks'];
        //        $answer_info['student_ans'] = $result;

        $module_id = $this->request->getVar('module_id');
        $question_order_id = $this->request->getVar('current_order');


        if ($question_info['operator'] != '/' && $result == $answer_info['tutor_ans']) {
            $ans_is_right = 'correct';
        } elseif ($question_info['operator'] == '/' && $question_info['quotient'] == $ans_one && $question_info['remainder'] == $reminder_answer) {
            $ans_is_right = 'correct';
        } else {
            $ans_is_right = 'wrong';
        }
        //        echo $ans_is_right;die;
        if ($this->request->getVar('module_type') == 1) {
            //echo 11111111;die;
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right, array());
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right, array());
        }
    }

    public function st_creative_ans_save()
    {
        $StudentClass=new \StudentClass();
        $question_id = $this->request->getVar('question_id');
        $module_id = $this->request->getVar('module_id');
        $question_order_id = $this->request->getVar('question_order_id');
        $idea_id = $this->request->getVar('idea_id');
        $idea_no = $this->request->getVar('idea_no');
        $module_type = $this->request->getVar('module_type');
        $preview_main_body = $this->request->getVar('student_ans');
        $total_word = $this->request->getVar('total_word');
        
        
        $question = $StudentClass->getQuestionMark($question_id);
        $question_marks= $question[0]['questionMarks'];
        $ans_is_right = 'idea';
        $user_id = $this->session->get('user_id');
        $user_type = $this->session->get('userType');

        
        $data['module_id'] = $module_id;
        $data['question_id'] = $question_id;
        $data['idea_id'] = $idea_id;
        $data['idea_no'] = $idea_no;
        $data['student_ans'] = $preview_main_body;
        $data['submit_date'] = date("Y/m/d");
        $data['total_word'] = $total_word;

        if($user_type==3){
            
            $data['tutor_id'] = $user_id;
            $check_idea = $StudentClass->checktutorIdeaAns($module_id,$question_id,$idea_id,$user_id);
        }else{
            
            $data['student_id'] = $user_id;
            $check_idea = $StudentClass->checkIdeaAns($module_id,$question_id,$idea_id,$user_id);
        }
        
        // if(empty($check_idea)){
        //     $idea_ans_id = $StudentClass->getIdeaAnsId($user_type,$data);
        // }else{
        //     echo 9;
        //     return;
        // }
        if(empty($check_idea)){
            $idea_ans_id = $StudentClass->getIdeaAnsId($user_type,$data);
        }else{
            $ans_is_right='correct';
        }

        
        if ($module_type == 1) {
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        }
    }

    public function st_answer_word_memorization()
    {
        $PreviewClass=new \PreviewClass();
        $question_id = $_POST['id']; 
        $module_id = $_POST['module_id'];
        $question_order_id = $_POST['current_order'];

        $student_answers = $_POST['answers'];
        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $question_marks = $answer_info[0]['questionMarks'];
        $question_answers = json_decode($answer_info[0]['answer']);
        //print_r($student_answers);die();
        //echo $student_ans[1];die();
        $ans_set = array();
        $ans_is_right = 'correct';
        foreach($question_answers as $key => $question_answer){
            $student_ans = $student_answers[$key];
            $ans_set[$key] = $student_ans;
            //echo $question_answer.'//'.$student_ans;
            if($question_answer==$student_ans){

            }else{
                $ans_is_right = 'wrong';
            }

        }
        //echo $ans_is_right;die();
        //print_r($ans_set);die();
        $_POST['answer'] = $ans_set;

        if ($_POST['module_type'] == 1) {
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        }
    }

    public function st_answer_comprehension()
    {
        error_report_check();
        $PreviewClass=new \PreviewClass();
        $ans_patern = $_POST['ans_pattern'];
        
        $question_id = $_POST['id']; 
        $module_id = $_POST['module_id'];
        $question_order_id = $_POST['current_order'];

        $student_answers = $_POST['answer'];
        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $question_marks = $answer_info[0]['questionMarks'];
        $question_answers = $answer_info[0]['answer'];
        
        $ans_set = array();
        
        if($student_answers=='write_ans'){
            $ans_is_right = 'correct';
        }else{
            $ans_is_right = 'correct';
            if($student_answers == $question_answers ){

            }else{
                $ans_is_right = 'wrong';
            }

        }

        if($ans_patern == 'skip'){
            $question_marks = 0;
            $ans_is_right = 'correct';
        }

        if ($_POST['module_type'] == 1) {
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        }
    }

    public function st_answer_grammer()
    {
        $PreviewClass=new \PreviewClass();
        $question_id = $_POST['id']; 
        $module_id = $_POST['module_id'];
        $question_order_id = $_POST['current_order'];
        if(isset($_POST['answer'])){
            $student_answers = $_POST['answer'];
        }else{
            $student_answers = '';
        }
        
        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $question_marks = $answer_info[0]['questionMarks'];
        $question_answers = $answer_info[0]['answer'];
        
        $ans_set = array();
        
        
        $ans_is_right = 'correct';
        if($student_answers == $question_answers ){

        }else{
            $ans_is_right = 'wrong';
        }

        // echo $ans_is_right;die();


        if ($_POST['module_type'] == 1) {
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        }
    }

    public function st_answer_image_quiz()
    {
        error_report_check();
        $PreviewClass=new \PreviewClass();
        $question_id = $_POST['id']; 
        $module_id = $_POST['module_id'];
        $question_order_id = $_POST['current_order'];
       
        $student_answers = $_POST['answer'];
        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $question_marks = $answer_info[0]['questionMarks'];
        $question_answers = $answer_info[0]['answer'];
        
        $ans_set = array();
        
        
        $ans_is_right = 'correct';
        if($student_answers == $question_answers ){

        }else{
            $ans_is_right = 'wrong';
        }

        // echo $ans_is_right;die();


        if ($_POST['module_type'] == 1) {
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        }
    }

    public function st_answer_glossary()
    {
        $PreviewClass = new \PreviewClass();
        // echo "<pre>";print_r($_POST);die();
        $question_id = $_POST['id']; 
        $module_id = $_POST['module_id'];
        $question_order_id = $_POST['current_order'];

        // $student_answers = $_POST['answer'];
        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $question_marks = $answer_info[0]['questionMarks'];
        
        $ans_set = array();
        
        
        $ans_is_right = 'correct';

        // echo $ans_is_right;die();


        if ($_POST['module_type'] == 1) {
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        }
    }


    private function take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right, $answer_info = null, $next_step_patten_two = null)
    {

        $PreviewClass=new \PreviewClass();
        $StudentClass=new \StudentClass();
        $TutorClass=new \TutorClass();
       //echo $ans_is_right;die();
        // $this->session->unset_userdata('data');
        //****** Get Temp table data for Tutorial Module Type ******
        $question_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        
        
        $question_info_type = '';
        $question_info_type = $question_info[0]['questionType'];
        $question_info_pattern = '';
        $question_info_pattern = json_decode($question_info[0]['questionName']);
        
        if (isset($question_info_pattern->pattern_type)) {
            $question_info_pattern = $question_info_pattern->pattern_type;
        }
        $user_id = $this->session->get('user_id');
        $tutorial_ans_info = $StudentClass->getTutorialAnsInfo('tbl_temp_tutorial_mod_ques', $module_id, $user_id);
        $obtained_marks = $this->session->get('obtained_marks');
        $total_marks = $this->session->get('total_marks');
        $ans_array = $this->session->get('data');

       
        $flag = 0;
        
        
        if (!is_array($ans_array)) {
            $ans_array = array();
            $obtained_marks = 0;
            $total_marks = 0;
            $flag = 0;
        } else {
            $question_idd = '';
            if (isset($ans_array[$question_order_id]['question_id'])) {
                $question_idd = $ans_array[$question_order_id]['question_id'];
            }
           
            if ($question_id == $question_idd) {
                $flag = 1;
            } else {
                $flag = 0;
            }

            
            if ($question_info_type == 16) {
                if ($question_info_pattern == 1) {
                    $memorization_answer = $this->request->getVar('memorization_answer');
                    $memorization_part = $this->request->getVar('memorization_one_part');
                    if ($memorization_part == 1) {
                        if (isset($_SESSION['memorization_one_part'])) {
                        } else {
                            $this->session->set('memorization_one_part', 1);
                            $ans_is_right = $memorization_answer;
                        }
                    } elseif ($memorization_part == 2) {
                        if (isset($_SESSION['memorization_two_part'])) {
                        } else {
                            $this->session->set('memorization_two_part', 2);
                            $ans_is_right = $memorization_answer;
                        }
                    } elseif ($memorization_part == 3) {
                        if (isset($_SESSION['memorization_three_part'])) {
                        } else {
                            $this->session->set('memorization_three_part', 3);
                            $ans_is_right = $memorization_answer;
                        }
                    }
                } elseif ($question_info_pattern == 3) {
                    if ($ans_is_right == 'wrong') {
                        $this->session->set('memorization_three_qus_part_answer', 'wrong');
                    }
                } else {
                    $this->session->remove('memorization_three_part');
                    $this->session->remove('memorization_two_part');
                    $this->session->remove('memorization_one_part');
                }
            } else {
                $this->session->remove('memorization_three_part');
                $this->session->remove('memorization_two_part');
                $this->session->remove('memorization_one_part');
            }
        }
        
        $student_ans = '';
        if ($this->request->getVar('answer')) {
            $student_ans = $this->request->getVar('answer');
        }

        if ($question_info_type == 20) {
            if($_POST['answer'] == 'write_ans'){
                $student_ans = $_POST['student_answer'];
            }
        }
        

        if ($tutorial_ans_info) {
            $temp_table_ans_info = json_decode($tutorial_ans_info[0]['st_ans'], true);
            $flag = 2;
        }

        
        if($question_info_type == 17){
            $ans_is_right = 'idea';
            $flag = 0;
            $question_marks=25;

        }


        if ($ans_is_right == 'correct') {
            //   echo $ans_is_right;die;

            if ($answer_info != null) {
                $student_ans = $answer_info;
                echo $answer_info;
            } else {
                //                    if ($flag != 2) {
                echo 2;
                //                    }
            }
        } else if($ans_is_right == 'idea'){
            if ($answer_info != null) {
                $student_ans = $answer_info;
                echo $answer_info;
            } else {

                // if(empty($check_idea)){
                //     echo 2;
                // }else{
                //     echo 9;

                echo 2;
              
            }

        }else {
            if ($answer_info != null) {
                $student_ans = $answer_info;
                echo $answer_info;
            } else {
                //                if ($flag != 2) {
                echo 3;
                //                }
            }

            $question_marks = 0;
        }
        
        // $fp = fopen(FCPATH . 'a.txt', 'a+');
        // fwrite($fp, "flag : $flag; question_info_type: $question_info_type");
        // fclose($fp);
        
        if ($flag == 0) {
            
            $question_info_ai = $TutorClass->getModuleQuestion($module_id, $question_order_id, null);
            

            if ($question_info_type == 11) {
                $question_info_ai[0]['questionMarks'] = $question_marks;
                $question_info_ai[0]['question_order'] = $question_order_id;
                $question_info_ai[0]['moduleType'] = $question_info_type;
                $question_info_ai[0]['module_id'] = $module_id;
                $question_info_ai[0]['question_id'] = $question_id;
            }


            if ($question_info_type == 16) {
                $ans_check = $this->session->get('memorization_three_qus_part_answer');
                if (isset($ans_check)) {
                    if ($ans_check == 'wrong') {
                        $ans_is_right = 'wrong';
                        $question_marks = 0;
                    }
                }
            }


            $link1 = base_url();

            $obtained_marks = $obtained_marks + $question_marks;
            $total_marks = $total_marks + $question_info_ai[0]['questionMarks'];
            $link2 = $link1.'/get_tutor_tutorial_module/' . $module_id . '/' . $question_order_id;
            $student_question_time_add = '';
            if (isset($_POST['student_question_time'])) {
                $student_question_time_add =  $_POST['student_question_time'];
            }
               
            //////////////// echo "<pre>";print_r($ind_ans);die();
            if ($question_info_type == 16) {

                if ($question_info_pattern == 1) {
                    $memorization_part = $this->request->getVar('memorization_one_part');
                    if ($memorization_part == 1) {
                        if (isset($_SESSION['memorization_one_part'])) {
                        } else {
                            $this->session->set('memorization_one_part', 1);
                            $student_ans = $this->request->getVar('word_matching');
                        }
                        $student_ans = $this->request->getVar('word_matching');
                    } elseif ($memorization_part == 2) {
                        if (isset($_SESSION['memorization_one_part'])) {
                        } else {
                            $this->session->set('memorization_one_part', 1);
                            $student_ans = $this->request->getVar('word_matching');
                        }
                        $student_ans = $this->request->getVar('word_matching');
                    }
                    // $student_ans =$this->input->post('word_matching');
                }


                if ($question_info_pattern == 2) {

                    $student_ansl['student'] = $this->request->getVar('right_memorize_p_two');
                    $student_ansl['left'] = $this->request->getVar('left_memorize_p_two');

                    $student_ans = json_encode($student_ansl);

                    $this->session->set('memorize_pattern_pattern_two_student_answer', $student_ans);

                    // $p = $this->session->userdata('memorize_pattern_pattern_two_student_answer');


                    // $fp = fopen(FCPATH . 'c.txt', 'a+');
                    // fwrite($fp, print_r($p, TRUE));
                    // fclose($fp);
                }

                if ($question_info_pattern == 4) {
                    $student_ansl['student'] = $this->request->getVar('word_matching');
                    $student_ansl['left'] = $this->request->getVar('left_memorize_p_four');

                    $student_ans = json_encode($student_ansl);

                    // $fp = fopen(FCPATH . 'b.txt', 'a+');
                    // fwrite($fp, print_r($student_ans, TRUE));
                    // fclose($fp);



                    $this->session->set('memorize_pattern_four_student_answer', $student_ansl);

                    $pattern_four_data = $this->session->get('memorize_pattern_four_student_answer');
                    // $fp = fopen(FCPATH . 'm.txt', 'a+');
                    // fwrite($fp, print_r($pattern_four_data, TRUE));
                    // fclose($fp);


                }
                if ($question_info_pattern == 3) {
                    $pattern_three = array();
                    $pattern_three = $this->session->get('memorize_pattern_three_student_answer');
                    $student_ans = unserialize($pattern_three);

                    // $fp = fopen(FCPATH . 'c.txt', 'a+');
                    // fwrite($fp, print_r($student_ans, TRUE));
                    // fclose($fp);


                }

                if ($flag == 0 && $question_info_pattern == 3 && $next_step_patten_two == 0) {
                    $the_first_start_time_new = time() - $this->session->get('start_exam_time_new');
                    $ind_ans = array(
                        'question_order_id' => $question_info_ai[0]['question_order'],
                        'module_type' => $question_info_ai[0]['moduleType'],
                        'module_id' => $question_info_ai[0]['module_id'],
                        'question_id' => $question_info_ai[0]['question_id'],
                        'link' => $link2,
                        'student_ans' => ($student_ans),
                        'workout' => isset($_POST['workout']) ? $_POST['workout'] : '',

                        'student_taken_time' => $the_first_start_time_new,
                        'student_question_marks' => $question_marks,
                        'student_marks' => $obtained_marks,
                        'ans_is_right' => $ans_is_right
                    );
                    $ans_array[$question_order_id] = $ind_ans;

                    $this->session->set('data', $ans_array);
                    $this->session->set('obtained_marks', $obtained_marks);
                    $this->session->set('total_marks', $total_marks);
                    $this->session->remove('memorization_three_qus_part_answer');
                }

                if ($flag == 0 && $question_info_pattern != 3) {
                    $ans_check = $this->session->get('memorization_three_qus_part_answer');
                    if (isset($ans_check)) {
                        if ($ans_check == 'wrong') {
                            $ans_is_right = 'wrong';
                            $question_marks = 0;
                        }
                    }
                    $the_first_start_time_new = time() - $this->session->get('start_exam_time_new');
                    $ind_ans = array(
                        'question_order_id' => $question_info_ai[0]['question_order'],
                        'module_type' => $question_info_ai[0]['moduleType'],
                        'module_id' => $question_info_ai[0]['module_id'],
                        'question_id' => $question_info_ai[0]['question_id'],
                        'link' => $link2,
                        'student_ans' => ($student_ans),
                        'workout' => isset($_POST['workout']) ? $_POST['workout'] : '',
                        'student_taken_time' => $the_first_start_time_new,
                        'student_question_marks' => $question_marks,
                        'student_marks' => $obtained_marks,
                        'ans_is_right' => $ans_is_right
                    );
                    $ans_array[$question_order_id] = $ind_ans;

                    // echo "<pre>";print_r($ind_ans);die();
                    $this->session->set('data', $ans_array);
                    $this->session->set('obtained_marks', $obtained_marks);
                    $this->session->set('total_marks', $total_marks);
                    $this->session->set('data', $ans_array);
                    $this->session->set('obtained_marks', $obtained_marks);
                    $this->session->set('total_marks', $total_marks);
                }
            } else {
                if ($question_info_type == 15) {
                    $answer_info = $StudentClass->getInfo('tbl_question', 'id', $question_id);
                    $percentage = $answer_info[0]['questionName'];
                    $percentage = json_decode($percentage);
                    $percentage_array = array();
                    $percentage_ans = array();
                    if (isset($percentage->percentage_array)) {
                        $percentage_array = $percentage->percentage_array;
                    }
                    foreach ($percentage_array as $key => $value) {
                        if (isset($_POST[$key])) {
                            $percentage_ans[$key]['0'] = $_POST[$key];
                            $percentage_ans[$key]['1'] = $value;
                        }
                    }
                    $student_ans = $percentage_ans;
                }
                     

                $the_first_start_time_new = time() - $this->session->get('start_exam_time_new');
                $ind_ans = array(
                    'question_order_id' => $question_info_ai[0]['question_order'],
                    'module_type' => $question_info_ai[0]['moduleType'],
                    'module_id' => $question_info_ai[0]['module_id'],
                    'question_id' => $question_info_ai[0]['question_id'],
                    'link' => $link2,
                    'student_ans' => ($student_ans),
                    'workout' => isset($_POST['workout']) ? $_POST['workout'] : '',
                    'student_taken_time' => $the_first_start_time_new,
                    'student_question_marks' => $question_marks,
                    'student_marks' => $obtained_marks,
                    'ans_is_right' => $ans_is_right
                );
                $ans_array[$question_order_id] = $ind_ans;

                $this->session->set('data', $ans_array);
                $this->session->set('obtained_marks', $obtained_marks);
                $this->session->set('total_marks', $total_marks);
            }
            

            if ($_POST['next_question'] == 0) {
                $total_ans = $this->session->get('data', $ans_array);

                $data['st_ans'] = json_encode($total_ans);
                $data['st_id'] = $this->session->get('user_id');
                $data['module_id'] = $module_id;

                if (!$tutorial_ans_info) {
                    $this->db->table('tbl_temp_tutorial_mod_ques')->insert($data);
                }
            }
        }

       

        if ($flag == 1 && $question_info_type == 16) {

            if ($question_info_pattern == 1) {
                $memorization_std_ans = $this->session->get('memorization_std_ans');
                $student_ans = json_encode($memorization_std_ans);
            }

            if ($question_info_pattern == 2) {

                $student_ans = $this->session->get('memorize_pattern_pattern_two_student_answer');

                // $fp = fopen(FCPATH . 'h.txt', 'a+');
                // fwrite($fp, print_r($student_ans, TRUE));
                // fclose($fp);
            }

            if ($question_info_pattern == 3) {

                $pattern_three = array();

                $pattern_three = $this->session->get('memorize_pattern_three_student_answer');

                $student_ans = unserialize($pattern_three);

                // $fp = fopen(FCPATH . 'd.txt', 'a+');
                // fwrite($fp, print_r($student_ans, TRUE));
                // fclose($fp);
            }

            $ans_array[$question_order_id]['student_ans'] = $student_ans;

            $this->session->set('data', $ans_array);
        }

        if ($flag == 2 && $question_info_type == 16) {


            if ($question_info_pattern == 3) {

                $pattern_three = array();

                $pattern_three = $this->session->get('memorize_pattern_three_student_answer');

                $student_ans = unserialize($pattern_three);


            }

            if ($question_info_pattern == 4) {
                    $pattern_four = $this->session->get('memorize_pattern_four_student_answer');
                    $student_ans = json_encode($pattern_four);

                }

            $ans_array[$question_order_id]['student_ans'] = $student_ans;

            $this->session->set('data', $ans_array);
        }


        $show_tutorial_result = $_SESSION['show_tutorial_result'];

        if ($flag == 2  && !empty($show_tutorial_result) && ($show_tutorial_result == 1)) {

            //            for ($i = 1; $i <= count($temp_table_ans_info); $i++) {
            foreach ($temp_table_ans_info as $key => $val) {
                //                echo $question_order_id.'<pre>';
                //                echo '<pre>';print_r($temp_table_ans_info[$key]);
                if ($temp_table_ans_info[$key]['question_order_id'] == $question_order_id) {
                    $temp_table_ans_info[$key]['ans_is_right'] = $ans_is_right;
                }
            }
            $update_value = json_encode($temp_table_ans_info);
            $st_ans['st_ans'] = $update_value;
            $StudentClass->updateInfo('tbl_temp_tutorial_mod_ques', 'id', $tutorial_ans_info[0]['id'], $st_ans);

            //            echo 6;
        }
        

        $x = $_SESSION;
        if (isset($x['data'])) {
            
            $this->session->set('data_exist_session', 1);
            $ck_tmp_module =  $StudentClass->get_all_where_two('id', 'tbl_temp_tutorial_mod_ques_two', 'module_id', $module_id, $_SESSION['user_id']);
            // print_r($ck_tmp_module);die();

            if (count($ck_tmp_module)) {
                $insert_data['st_ans'] = json_encode($ans_array);
                $insert_data['obtained_marks'] = $_SESSION['obtained_marks'];
                $insert_data['total_marks'] = $_SESSION['total_marks'];

                //echo '<pre>';print_r($insert_data);die();

                $StudentClass->update_tmp_module_tbl('tbl_temp_tutorial_mod_ques_two', 'module_id', $module_id, 'st_id', $this->session->get('user_id'), $insert_data);
            } else {
                $insert_data['st_ans'] = json_encode($_SESSION['data']);
                $insert_data['module_id'] = $module_id;
                $insert_data['st_id'] = $this->session->get('user_id');
                $insert_data['obtained_marks'] = $_SESSION['obtained_marks'];
                $insert_data['total_marks'] = $_SESSION['total_marks'];

                $StudentClass->insertInfo('tbl_temp_tutorial_mod_ques_two', $insert_data);
            }
        }
        
    }


    private function take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right, $answer_info = null)
    {

        error_report_check();
        $StudentClass=new \StudentClass();
        $TutorClass=new \TutorClass();

        $obtained_marks = $this->session->get('obtained_marks');
        $total_marks = $this->session->get('total_marks');
        $ans_array = $this->session->get('data');
        $user_id = $this->session->get('user_id');
        $question_info = $StudentClass->getInfo('tbl_question', 'id', $question_id);
        $question_type = $question_info[0]['questionType'];
        $question_info_pattern = '';
        $question_pattern = '';
        $memorization_part = '';
        $memorization_obtaine_mark_check = 1;
        $question_pattern = json_decode($question_info[0]['questionName']);



        if (isset($question_pattern->pattern_type)) {
            $question_info_pattern = $question_pattern->pattern_type;
        }
        $memorization_question_mark = $question_info[0]['questionMarks'];
        $tutorial_ans_info = $StudentClass->getTutorialAnsInfo('tbl_student_answer', $module_id, $user_id);
        $question_info_ai = $TutorClass->getModuleQuestion($module_id, $question_order_id, null);

        if ($question_type == 11) {
            $question_info_ai[0]['questionMarks'] = $question_marks;
            $question_info_ai[0]['question_order'] = $question_order_id;
            $question_info_ai[0]['moduleType'] = 2;
            $question_info_ai[0]['module_id'] = $module_id;
            $question_info_ai[0]['question_id'] = $question_id;
        }


        $flag = 0;

        if ($tutorial_ans_info) {
            $temp_table_ans_info = json_decode($tutorial_ans_info[0]['st_ans'], true);
            $flag = 2;
        }
        if (!is_array($ans_array)) {
            $ans_array = array();
            $obtained_marks = 0;
            $total_marks = 0;
        } else {

            $question_idd = '';
            if (isset($ans_array[$question_order_id]['question_id'])) {
                $question_idd = $ans_array[$question_order_id]['question_id'];
            }

            if ($question_id == $question_idd) {
                $flag = 1;
            } else {
                $flag = 0;
            }
            if ($question_type == 16) {
                if ($question_info_pattern == 1) {
                    $memorization_answer = $this->request->getVar('memorization_answer');
                    $memorization_part = $this->request->getVar('memorization_one_divider');

                    $memorization_answer_right = '';
                    $memorization_answer_wrong = 'correct';
                    if ($memorization_part == 1) {
                        if (array_key_exists('memorization_one_part', $_SESSION)) {
                        } else {
                            $this->session->set('memorization_one_part', 1);
                            $memorization_answer_right = $memorization_answer;
                            //echo $memorization_answer_right;
                            if ($memorization_answer_right == 'wrong') {
                                $memorization_answer_wrong = $memorization_answer_right;
                                $this->session->set('memorization_answer_wrong', $memorization_answer_wrong);
                                //$this->session->set_userdata('memorize_qus_wrong_ans_status_get',$memorization_answer_wrong);//added AS 6/22/21

                            }
                            $this->session->set('memorization_answer_right', $memorization_answer_right);
                        }
                    } elseif ($memorization_part == 2) {
                        if (array_key_exists('memorization_two_part', $_SESSION)) {
                        } else {
                            $this->session->set('memorization_two_part', 2);
                            $memorization_answer_right = $memorization_answer;
                            if ($memorization_answer_right == 'wrong') {
                                $memorization_answer_wrong = $memorization_answer_right;
                                $this->session->set('memorization_answer_wrong', $memorization_answer_wrong);
                                //$this->session->set_userdata('memorize_qus_wrong_ans_status_get',$memorization_answer_wrong);//added AS 6/22/21
                            }
                            $this->session->set('memorization_answer_right', $memorization_answer_right);
                        }
                    } elseif ($memorization_part == 3) {
                        if (array_key_exists('memorization_three_part', $_SESSION)) {
                        } else {
                            $this->session->set('memorization_three_part', 3);
                            $memorization_answer_right = $memorization_answer;
                            if ($memorization_answer_right == 'wrong') {
                                $memorization_answer_wrong = $memorization_answer_right;
                                $this->session->set('memorization_answer_wrong', $memorization_answer_wrong);
                                //$this->session->set_userdata('memorize_qus_wrong_ans_status_get',$memorization_answer_wrong);//added AS 6/22/21
                            }
                            $this->session->set('memorization_answer_right', $memorization_answer_right);
                            $memorization_obtaine_mark_check = 2;
                        }
                    }
                    if (array_key_exists('memorization_answer_wrong', $_SESSION)) {
                        $memorization_answer_right = $this->session->get('memorization_answer_wrong');
                        $this->session->set('memorization_answer_right', $memorization_answer_right);
                    }
                } else {
                    $this->session->remove('memorization_three_part');
                    $this->session->remove('memorization_two_part');
                    $this->session->remove('memorization_one_part');
                    $this->session->remove('memorize_qus_wrong_ans_status_get');
                }
            } else {
                $this->session->remove('memorization_three_part');
                $this->session->remove('memorization_two_part');
                $this->session->remove('memorization_one_part');
                $this->session->remove('memorize_qus_wrong_ans_status_get');
            }
        }


        $student_ans = '';

        if (isset($_POST['answer'])) {
            $student_ans = $_POST['answer'];
        }
        if ($student_ans == '' && isset($_POST['given_ans'])) {
            $student_ans = $_POST['given_ans'];
        }

        if ($ans_is_right == 'correct') {
            if ($answer_info != null && $question_info_ai[0]['moduleType'] == 2) {
                echo $answer_info;
                $student_ans = $answer_info;
            }

            if ($question_type == 11) {
                $question_info_ai[0]['moduleType'] = 2;
            }

            if ($answer_info == null && $question_info_ai[0]['moduleType'] == 2) {
                echo 2;
            }
        } else {
            if ($question_info_ai[0]['moduleType'] == 2) {
                if ($answer_info != null && $question_info_ai[0]['moduleType'] == 2) {
                    echo $answer_info;
                    $student_ans = $answer_info;
                }
                if ($answer_info == null && $question_info_ai[0]['moduleType'] == 2) {
                    echo 3; //echo 'three';
                    if ($question_type == 16) {
                        $memorize_qus_wrong_ans_status_var = 1;
                        $this->session->set('memorize_qus_wrong_ans_status_get', 'wrong'); //added AS 6/22/21
                    }
                }
            }

            $question_marks = 0;
        }


        $show_tutorial_result = $_SESSION['show_tutorial_result'];

        $my_flag = 10;

        //echo "<br>";echo "<pre>";print_r($flag);die();
        // echo "<pre>";print_r($this->session->userdata('memorization_answer_right'));echo 'Flag: ';echo $flag;die();
        if (($flag == 2 || $flag == 1)  && !empty($show_tutorial_result) && ($show_tutorial_result == 1)) {
            // if (($flag == 2 || $flag == 1)  && !empty($show_tutorial_result) ) { //change for 17/6/21

            foreach ($temp_table_ans_info as $key => $val) {
                if ($temp_table_ans_info[$key]['question_order_id'] == $question_order_id) {
                    $temp_table_ans_info[$key]['ans_is_right'] = $ans_is_right;
                    $temp_table_ans_info[$key]['student_ans'] = json_encode($student_ans);
                }
            }

            $update_value = json_encode($temp_table_ans_info);
            $st_ans['st_ans'] = $update_value;
            //$this->Student_model->updateInfo('tbl_student_answer', 'id', $tutorial_ans_info[0]['id'], $st_ans);

            $count_std_error_ans = $StudentClass->get_count_std_error_ans($question_order_id, $module_id, $user_id);

            if (isset($count_std_error_ans[0]['error_count']) && $count_std_error_ans[0]['error_count'] == 3) {
                $StudentClass->delete_st_error_ans($question_order_id, $module_id, $user_id);
                $my_flag = 2;
            } else {
                if ($ans_is_right == 'wrong') {
                    $StudentClass->update_st_error_ans($question_order_id, $module_id, $user_id);
                    $x =  $StudentClass->getTutorialAnsInfo_("tbl_st_error_ans", $module_id, $user_id);

                    foreach ($x as $key => $value) {
                        unset($value['id']);
                        $dataToUpdate[] = $value;
                    }

                    if (isset($dataToUpdate)) {
                        $StudentClass->getTutorialAnsInfo_delete($dataToUpdate, $module_id, $user_id);
                    }
                }
                if ($ans_is_right == 'correct') {
                    $StudentClass->delete_st_error_ans($question_order_id, $module_id, $user_id);
                    $my_flag = 2;
                }
            }

            if ($question_info_ai[0]['moduleType'] != 2) {
                echo 5;
            }
        }



        if ($flag == 1 && $question_type == 16 && $question_info_pattern == 1) {
            $my_flag = 1;
            $link1 = base_url();
            $link2 = $link1.'/get_tutor_tutorial_module/' . $module_id . '/' . $question_order_id;

            if (isset($_POST['student_question_time']) && $_POST['student_question_time'] != '') {
                $student_question_time = $_POST['student_question_time'];
            } else {
                $student_question_time = '';
            }
            if ($question_type == 16) {
                if ($question_info_pattern == 1) {
                    $memorization_std_ans = $this->session->get('memorization_std_ans');
                    $student_ans = $memorization_std_ans;
                }
            }

            //echo $this->session->userdata('memorize_qus_wrong_ans_status_get');
            $memorization_answer_right = $this->session->get('memorization_answer_right');
            //echo $memorization_answer_right;die();
            if ($memorization_answer_right == 'correct') {
                $obtained_marks = $memorization_question_mark;
            } else {
                $question_marks = 0;
                //$obtained_marks = 0;
            }
            //echo $memorization_answer_right;die();
            if ($memorization_part == 3 && $memorization_obtaine_mark_check == 2) {
                $obtained_marks = $obtained_marks + $question_marks;
            }

            if ($memorization_answer_right == 'correct' && $question_marks == 5) {
                if ($memorization_part == 2 && $memorization_obtaine_mark_check == 1) {
                    $obtained_marks = $question_marks;
                }
            }

            $check_memorize_qus_wrong_ans = $this->session->get('memorize_qus_wrong_ans_status_get');

            if (($question_type == 16 && $question_info_ai[0]['moduleType'] == 2) || isset($memorize_qus_wrong_ans_status_var)) {
                if ($check_memorize_qus_wrong_ans == 'wrong') {
                    $question_marks = 0;
                    $obtained_marks = 0;
                    $memorization_answer_right = $check_memorize_qus_wrong_ans;
                }
            }

            $the_first_start_time_new = time() - $this->session->get('start_exam_time_new');
            $ind_ans = array(
                'question_order_id' => $question_info_ai[0]['question_order'],
                'module_type' => $question_info_ai[0]['moduleType'],
                'module_id' => $question_info_ai[0]['module_id'],
                'question_id' => $question_info_ai[0]['question_id'],
                'link' => $link2,
                'student_ans' => ($student_ans),
                'workout' => isset($_POST['workout']) ? $_POST['workout'] : '',
                'student_taken_time' => $the_first_start_time_new,'student_question_marks' => $question_marks,
                'student_marks' => $obtained_marks,
                'ans_is_right' => $memorization_answer_right
            );
            //echo '<pre>';print_r($ind_ans);die;
            $ans_array[$question_order_id] = $ind_ans;

            $this->session->set('data', $ans_array);
            $this->session->set('obtained_marks', $obtained_marks);
            $this->session->set('total_marks', $total_marks);
            if ($question_info_ai[0]['moduleType'] != 2) {
                echo 5;
            }
        }
        if ($flag == 0) {

            $my_flag = 0;
            $link1 = base_url();
            $link2 = $link1.'/get_tutor_tutorial_module/' . $module_id . '/' . $question_order_id;

            if ($question_type != 16  && $question_info_pattern != 1) {
                $obtained_marks = $obtained_marks + $question_marks;
            }
            if ($question_type == 16  && $question_info_pattern == 4) {
                $obtained_marks = $obtained_marks + $question_marks;
            }

            $total_marks = $total_marks + $question_info_ai[0]['questionMarks'];
            if (isset($_POST['student_question_time']) && $_POST['student_question_time'] != '') {
                $student_question_time = $_POST['student_question_time'];
            } else {
                $student_question_time = '';
            }

            if ($question_type == 15) {
                $answer_info = $StudentClass->getInfo('tbl_question', 'id', $question_id);
                $percentage = $answer_info[0]['questionName'];
                $percentage = json_decode($percentage);
                $percentage_array = array();
                $percentage_ans = array();
                if (isset($percentage->percentage_array)) {
                    $percentage_array = $percentage->percentage_array;
                }
                foreach ($percentage_array as $key => $value) {
                    if (isset($_POST[$key])) {
                        $percentage_ans[$key]['0'] = $_POST[$key];
                        $percentage_ans[$key]['1'] = $value;
                    }
                }
                $student_ans = $percentage_ans;
            }
            if ($question_type == 16) {

                if ($question_info_pattern == 2) {
                    $pattern_two = array();
                    $left_memorize_p_two = $this->request->getVar('left_memorize_p_two');
                    $right_memorize_p_two = $this->request->getVar('right_memorize_p_two');
                    $pattern_two['left_memorize_p_two'] = $left_memorize_p_two;
                    $pattern_two['right_memorize_p_two'] = $right_memorize_p_two;
                    $student_ans = $pattern_two;
                }

                if ($question_info_pattern == 4) {
                    $pattern_four = array();
                    $left_memorize_p_four = $this->request->getVar('left_memorize_p_four');
                    $right_memorize_p_four = $this->request->getVar('right_memorize_p_four');
                    $pattern_two['left_memorize_p_four'] = $left_memorize_p_four;
                    $pattern_two['right_memorize_p_four'] = $right_memorize_p_four;
                    //$student_ans = $pattern_four;

                    $student_ans = json_encode($this->session->get('correct_answer'));
                }
                if ($question_info_pattern == 3) {
                    $pattern_three = array();
                    $left_memorize_p_two = $this->request->getVar('left_image_ans');
                    $right_memorize_p_two = $this->request->getVar('right_image_ans');
                    $pattern_three['left_image_ans'] = $left_memorize_p_two;
                    $pattern_three['right_image_ans'] = $right_memorize_p_two;
                    $student_ans = $pattern_three;
                }
            }
            if ($question_type == 16) {
                if ($question_info_pattern == 1) {
                    $memorization_std_ans = $this->session->get('memorization_std_ans');
                    $student_ans = $memorization_std_ans;
                }
            }
            $the_first_start_time_new = time() - $this->session->get('start_exam_time_new');
            // echo '<pre>';print_r($obtained_marks);die;
            $ind_ans = array(
                'question_order_id' => $question_info_ai[0]['question_order'],
                'module_type' => $question_info_ai[0]['moduleType'],
                'module_id' => $question_info_ai[0]['module_id'],
                'question_id' => $question_info_ai[0]['question_id'],
                'link' => $link2,
                'student_ans' => ($student_ans),
                'workout' => isset($_POST['workout']) ? $_POST['workout'] : '',
                'student_taken_time' => $the_first_start_time_new,    'student_question_marks' => $question_marks,
                'student_marks' => $obtained_marks,
                'ans_is_right' => $ans_is_right
            );
            // echo '<pre>';print_r($ind_ans);die;
            $ans_array[$question_order_id] = $ind_ans;

            $this->session->set('data', $ans_array);
            $this->session->set('obtained_marks', $obtained_marks);
            $this->session->set('total_marks', $total_marks);
            if ($question_info_ai[0]['moduleType'] != 2) {
                echo 5;
            }
        }


        if ($my_flag == 10) {
            foreach ($temp_table_ans_info as $key => $val) {
                if ($temp_table_ans_info[$key]['question_order_id'] == $question_order_id) {
                    $temp_table_ans_info[$key]['ans_is_right'] = $ans_is_right;
                    $temp_table_ans_info[$key]['student_ans'] = json_encode($student_ans);
                }
            }

            $update_value = json_encode($temp_table_ans_info);
            $st_ans['st_ans'] = $update_value;
            //$this->Student_model->updateInfo('tbl_student_answer', 'id', $tutorial_ans_info[0]['id'], $st_ans);

            $count_std_error_ans = $StudentClass->get_count_std_error_ans($question_order_id, $module_id, $user_id);

            if (isset($count_std_error_ans[0]['error_count']) && $count_std_error_ans[0]['error_count'] == 3) {
                $StudentClass->delete_st_error_ans($question_order_id, $module_id, $user_id);
            } else {
                if ($ans_is_right == 'wrong') {
                    $StudentClass->update_st_error_ans($question_order_id, $module_id, $user_id);
                    $x =  $StudentClass->getTutorialAnsInfo_("tbl_st_error_ans", $module_id, $user_id);

                    foreach ($x as $key => $value) {
                        unset($value['id']);
                        $dataToUpdate[] = $value;
                    }

                    if (isset($dataToUpdate)) {
                        $StudentClass->getTutorialAnsInfo_delete($dataToUpdate, $module_id, $user_id);
                    }
                }
                if ($ans_is_right == 'correct') {
                    $StudentClass->delete_st_error_ans($question_order_id, $module_id, $user_id);
                }
            }

            if ($question_info_ai[0]['moduleType'] != 2) {
                echo 5;
            }
        }


        $x = $_SESSION;


        // echo "<br>";
        // echo $_POST['next_question'] ;echo "<br>";
        // echo "<pre>";print_r($x);die();
        // echo 11; die();
        if ($_POST['next_question'] == 0 && $my_flag != 2) { //new add $my_flag != 2
            date_default_timezone_set($this->site_user_data['zone_name']);
            $end_time = time();
            $this->session->set('end_time', $end_time);
            $this->save_student_answer($module_id);
        }


        // echo "<br>";
        // echo $_POST['next_question'] ;echo "<br>";
        // echo "<pre>";print_r($x);die();
        // echo 11; die();
        if (isset($x['data'])) {
            $this->session->set('data_exist_session', 1);
            $ck_tmp_module =  $StudentClass->get_all_where_two('id', 'tbl_temp_tutorial_mod_ques_two', 'module_id', $module_id, $_SESSION['user_id']);

            if (count($ck_tmp_module)) {
                $insert_data['st_ans'] = json_encode($ans_array);
                $insert_data['obtained_marks'] = $_SESSION['obtained_marks'];
                $insert_data['total_marks'] = $_SESSION['total_marks'];
                // echo "<br>";echo "<pre>";print_r($x['data']);echo "<br>";
                // echo "<pre>";print_r($insert_data);echo "<br>";
                // echo "<pre>";print_r($ck_tmp_module);echo "<br>";
                // die();
                $StudentClass->update_tmp_module_tbl('tbl_temp_tutorial_mod_ques_two', 'module_id', $question_info_ai[0]['module_id'], 'st_id', $this->session->get('user_id'), $insert_data);
            } else {
                $insert_data['st_ans'] = json_encode($_SESSION['data']);
                $insert_data['module_id'] = $question_info_ai[0]['module_id'];
                $insert_data['st_id'] = $this->session->get('user_id');
                $insert_data['obtained_marks'] = $_SESSION['obtained_marks'];
                $insert_data['total_marks'] = $_SESSION['total_marks'];

                $StudentClass->insertInfo('tbl_temp_tutorial_mod_ques_two', $insert_data);
            }
        } else {
            $this->session->set('data_exist_session', 0);
        }
    }


    public function save_student_answer($module_id)
    {
        $StudentClass=new \StudentClass();
        $get_module_info = $StudentClass->getInfo('tbl_module', 'id', $module_id);
        $courseId = $get_module_info[0]['course_id'];
        $ans_array = $this->session->get('data');
        $obtained_marks = $this->session->get('obtained_marks');
        $total_marks = $this->session->get('total_marks');
        // echo "<pre>"; print_r($ans_array);die();
        if ($ans_array != "") {

            $student_taken_time = $this->session->get('end_time') - $this->session->get('exam_start');
            foreach ($ans_array as $ans) {
                if ($ans['ans_is_right'] == 'wrong') {
                    $data_er['st_id'] = $this->session->get('user_id');
                    $data_er['question_id'] = $ans['question_id'];
                    $data_er['question_order_id'] = $ans['question_order_id'];
                    $data_er['module_id'] = $ans['module_id'];
                    $data_er['error_count'] = 1;

                    $this->db->insert('tbl_st_error_ans', $data_er);
                }
            }

            $time['user_info'] = $StudentClass->userInfo($this->session->get('user_id'));

            date_default_timezone_set($time['user_info'][0]['zone_name']);
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['st_ans'] = json_encode($ans_array);
            $data['st_id'] = $this->session->get('user_id');
            $data['module_id'] = $module_id;

            $this->db->table('tbl_student_answer')->insert($data);

            $p_data['timeTaken'] = $student_taken_time;
            $p_data['answerTime'] = $this->session->get('exam_start');
            $p_data['originalMark'] = $total_marks;
            $p_data['studentMark'] = $obtained_marks;
            $p_data['student_id'] = $data['st_id'];
            $p_data['module'] = $data['module_id'];
            $p_data['percentage'] = ($obtained_marks * 100) / $total_marks;
            $p_data['moduletype'] = $get_module_info[0]['moduleType'];
            $p_data['date_time'] = date("Y-m-d");

            $tbl_studentprogress_info = $StudentClass->getWhereThreewoCondition("tbl_studentprogress", "student_id", $this->session->get('user_id'), "module", $data['module_id'], "date_time", date("Y-m-d"));

            $moduleDetails  = $StudentClass->getInfo('tbl_module', 'id', $module_id);
            $moduleCreator = $moduleDetails[0]['user_id'];
            $user_id = $this->session->get('user_id');
            //echo "<pre>"; print_r($p_data);die();

            if (count($tbl_studentprogress_info) == 0) {
                $this->db->table('tbl_studentprogress')->insert($p_data);


                // start added for prize
                $gradeCheck   = $this->db->table('student_grade_log')->where('user_id', $user_id)->get()->getRow();
                $user         = $this->db->table('tbl_useraccount')->where('id', $user_id)->get()->getRow();

                $user_grade   = (isset($user->student_grade)) ? $user->student_grade : 0;
                $latest_grade = (isset($gradeCheck->grade)) ? $gradeCheck->grade : 0;

                $per = ($obtained_marks * 100) / $total_marks;
                $point = number_format($per);
                if ($user_grade == $latest_grade &&  $moduleCreator == 2) {

                    $mData['user_id']   = $user_id;
                    $mData['module_id'] = $module_id;
                    $mData['complete_date'] = date('Y-m-d');
                    $mData['percentage'] = $point;
                    $this->db->table('daily_modules')->insert($mData);

                    $pData['user_id'] = $user_id;
                    $getPointInfo = $this->db->table('module_points')->where('user_id', $user_id)->get()->getRow();
                    if ($getPointInfo) {
                        $recent_point = $getPointInfo->point;
                        $pData['point'] = $point + $recent_point;
                        //print_r($pData);die;
                        $this->db->table('module_points')->where('user_id', $user_id)->update($pData);
                    } else {
                        $pData['point'] = $point;
                        $this->db->table('module_points')->insert($pData);
                    }


                    $getProPoint = $this->db->table('product_poinits')->where('user_id', $user_id)->get()->getRow();
                    $tr_point = $this->db->table('target_points')->where('user_id', $user_id)->get()->getRow();
                    $target_point = $tr_point->targetPoint;

                    //$pointCheck  = $this->db->where('user_id',$user_id)->get('product_poinits')->row();

                    if ($getProPoint) {
                        $proPoint['user_id'] = $user_id;
                        $sumPoint = ($getProPoint->recent_point +  $point);

                        if ($sumPoint >= $target_point) {
                            $proPoint['recent_point'] = $target_point;
                            $bnsPoint = ($sumPoint - $target_point);
                            $proPoint['bonus_point']  = $getProPoint->bonus_point + $bnsPoint;
                            $proPoint['total_point']  = $getProPoint->total_point + $point;
                        } else {
                            $proPoint['recent_point'] = $getProPoint->recent_point +  $point;
                            $proPoint['total_point']  = $getProPoint->total_point + $point;
                        }


                        $this->db->table('product_poinits')->where('user_id', $user_id)->update($proPoint);
                    } else {
                        $proPoint['user_id'] = $user_id;
                        $proPoint['recent_point'] = $point;
                        $proPoint['total_point'] = $point;
                        $this->db->table('product_poinits')->insert($proPoint);
                    }
                }
            }

            $this->session->remove('data', $ans_array);

            //      *****  For Send SMS Message to Parents  *****

            if ($get_module_info[0]['isSMS'] == 1) {

                $obtained_marks = number_format((float)$obtained_marks, 2, '.', '');

                $v_hours = floor($student_taken_time / 3600);
                $remain_seconds = $student_taken_time - $v_hours * 3600;
                $v_minutes = floor($remain_seconds / 60);
                $v_seconds = $remain_seconds - $v_minutes * 60;
                $time_hour_minute_sec = $v_hours . " : "  . $v_minutes . " : " . $v_seconds;

                $settins_Api_key = $StudentClass->getSmsApiKeySettings();
                $settins_sms_messsage = $StudentClass->get_sms_response_after_module();

                $user_email = $this->session->get('user_email');
                //$totProgress = $this->Student_model->getInfo('tbl_studentprogress', 'student_id', $data['st_id']);

                $get_module_info = $StudentClass->getInfo('tbl_module', 'id', $module_id);
                $course_id = '';
                if ($get_module_info[0]['course_id'] != 0) {
                    $course_id = $get_module_info[0]['course_id'];
                }
                $module_type = $get_module_info[0]['moduleType'];
                $module_user_type = $get_module_info[0]['user_type'];
                $conditions['student_id'] = $data['st_id'];
                $conditions['moduletype'] = $module_type;
                $totProgress = $StudentClass->studentProgressStd($conditions, $module_user_type, $course_id);

                $avg_mark = 0;
                $totPercentage = 0;
                if (count($totProgress)) {
                    $examAttended = count($totProgress);
                    $tot = 0;
                    foreach ($totProgress as $progress) {
                        if ($progress['studentMark'] == 0) {
                            $percentGained = 0;
                        } else {
                            $percentGained = (float)($progress['studentMark'] / $progress['originalMark']) * 100;
                        }
                        $percentGained = round($percentGained, 2);
                        $totPercentage += $percentGained;
                        //$tot+=$progress['percentage'];
                    }
                    $avg_mark = ($examAttended > 0) ? round((float)($totPercentage / $examAttended), 2) : 0.0;
                    //$avg_mark = round($tot/count($totProgress), 2);
                }
                $courseName = $StudentClass->getInfo('tbl_course', 'id', $courseId);
                $get_all_module_question = $StudentClass->getInfo('tbl_modulequestion', 'module_id', $module_id);
                $get_child_parent_info = $StudentClass->getInfo('tbl_useraccount', 'id', $time['user_info'][0]['parent_id']);

                $register_code_string = $settins_sms_messsage[0]['setting_value'];
                if (!empty($courseName)) {
                    $courseName = $courseName[0]['courseName'];
                    $find = array("{{user_email}}", "{{marks}}", "{{total_marks}}", "{{student_taken_time}}", "{{course_name}}", "{{avg_mark}}");
                    $replace = array($user_email, $obtained_marks, $total_marks, $time_hour_minute_sec, $courseName, $avg_mark);
                } else {
                    //$register_code_string = str_replace('in “course :{{course_name}}”',"",$register_code_string);
                    $register_code_string = str_replace('in “course:{{course_name}}”', "", $register_code_string);
                    $find = array("{{user_email}}", "{{marks}}", "{{total_marks}}", "{{student_taken_time}}", "{{avg_mark}}");
                    $replace = array($user_email, $obtained_marks, $total_marks, $time_hour_minute_sec, $avg_mark);
                }


                $AdminClass=new \AdminClass();
                $user_id = $this->session->get('user_id');
                $today_send_sms = $AdminClass->get_all_whereTwo("*", "sms_send_to_parent_today", "user_id", $user_id, "date_", date("Y-m-d"));

                if (count($today_send_sms) == 0) {
                    $userDetails = $this->db->table('tbl_useraccount')->where('id', $user_id)->get()->getRow();
                    if ($userDetails->sms_status_stop == 0 ) {
                        if( $this->session->get('is_practice')==0){
                        $message = str_replace($find, $replace, $register_code_string);
                        $api_key = $settins_Api_key[0]['setting_value'];
                        $content = urlencode($message);
                        $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $get_child_parent_info[0]['user_mobile'] . "&content=$content";

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
                        curl_setopt($ch, CURLOPT_VERBOSE, true);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                        //execute post
                        $result = curl_exec($ch);
                        curl_close($ch);

                        $send_sms['user_id'] = $this->session->get('user_id');
                        $send_sms['date_'] = date("Y-m-d");

                        $ck_exist_user = $AdminClass->get_all_where("*", "sms_send_to_parent_today", "user_id", $this->session->get('user_id'));


                        if ($ck_exist_user) {
                            $AdminClass->updateInfo("sms_send_to_parent_today", "user_id", $this->session->get('user_id'), $send_sms);
                        } else {
                            $AdminClass->insertInfo("sms_send_to_parent_today", $send_sms);
                        }
                    }
                    }
                }
            }
        }
    }

    public function st_preview_memorization_pattern_one_ans_matching()
    {
        $TutorClass=new \TutorClass();
        $data = array();
        $question_id = $this->request->getVar('question_id');
        $word_matching = $this->request->getVar('word_matching');
        $submit_cycle = $this->request->getVar('submit_cycle');
        $pattern = $this->request->getVar('pattern');
        $question_info = $TutorClass->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        $show_data_array = array();
        $word_matching_answer = array();
        $all_correct_status = 1;
        $left_memorize_h_p_one = $question_name->left_memorize_h_p_one;
        $left_memorize_p_one = $question_name->left_memorize_p_one;
        $left_memorize_p_one = array_map('strtolower', $left_memorize_p_one);

        $question_type = $question_info[0]['questionType'];
        $question_info_pattern = '';
        $question_info_pattern = json_decode($question_info[0]['questionName']);
        if (isset($question_info_pattern->pattern_type)) {
            $question_info_pattern = $question_info_pattern->pattern_type;
        }
        if ($question_type == 16) {
            if ($question_info_pattern == 1) {
                $set_array = array();
                $memorization_std_ans = array();
                $memorization_part = $this->request->getVar('memorization_one_part');
                $memorization_answer = $this->request->getVar('word_matching');
                $set_array = $this->session->get('memorization_std_ans');
                if ($memorization_part == 1) {
                    if (isset($_SESSION['memorization_one'])) {
                    } else {
                        $memorization_std_ans[0] = $memorization_answer;
                        $this->session->set('memorization_one', 1);
                        $this->session->set('memorization_std_ans', $memorization_std_ans);
                    }
                } elseif ($memorization_part == 2) {
                    if (isset($_SESSION['memorization_two'])) {
                    } else {
                        $memorization_std_ans[0] = $set_array[0];
                        $memorization_std_ans[1] = $memorization_answer;
                        $this->session->set('memorization_two', 1);
                        $this->session->set('memorization_std_ans', $memorization_std_ans);
                    }
                }
            }
        }

        if ($submit_cycle != 1) {


            foreach ($left_memorize_p_one as $key => $item) {
                if ($left_memorize_h_p_one[$key] == 1) {
                    $show_data_array[] = $item;
                } else {
                    $show_data_array[] = '';
                }
            }

            foreach ($show_data_array as $key => $show_data) {
                if ($show_data != '') {
                    $word_matching_item = $word_matching[$key];

                    if (preg_replace('/\s+/', '', strtolower($show_data)) ==  preg_replace('/\s+/', '', strtolower($word_matching_item))) {
                        $word_matching_answer[] = 1;
                    } else {
                        $word_matching_answer[] = 0;
                        $all_correct_status = 0;
                    }
                } else {
                    $word_matching_answer[] = 2;
                }
            }
            $data_array = array();
            foreach ($word_matching_answer as $key => $value) {
                if ($value != 1) {
                    $data_array[] = $left_memorize_p_one[$key];
                } else {
                    $data_array[] = '';
                }
            }
            $data['word_matching_answer'] = $word_matching_answer;
            $data['data_array'] = $data_array;
            $data['all_correct_status'] = $all_correct_status;
            $data['status'] =  0;
        } else {
            $word_matching = $this->request->getVar('word_matching');
            $show_data_array = array();
            $left_memorize_h_p_one = $question_name->left_memorize_h_p_one;
            $left_memorize_p_one = $question_name->left_memorize_p_one;
            $left_memorize_p_one = array_map('strtolower', $left_memorize_p_one);
            $correct_status = 1;
            $leftSileData = array();
            $word_matching_answer = array();

            foreach ($left_memorize_p_one as $key => $item) {
                if (preg_replace('/\s+/', '', strtolower($left_memorize_p_one[$key]))  == preg_replace('/\s+/', '', strtolower($word_matching[$key]))) {
                    $show_data_array[$key][0] = $item;
                    $show_data_array[$key][1] = 1;
                    $leftSileData[$key][0] = '';
                    $leftSileData[$key][1] = 1;
                    $word_matching_answer[] = 1;
                } else {
                    $correct_status = 0;
                    $show_data_array[$key][0] = '';
                    $show_data_array[$key][1] = 0;
                    $leftSileData[$key][0] = $item;
                    $leftSileData[$key][1] = 0;
                    $word_matching_answer[] = 0;
                }
            }
            $data['word_matching_answer'] =  $word_matching_answer;
            $data['leftSileData'] =  $leftSileData;
            $data['all_correct_ans'] =  $show_data_array;
            $data['status'] =  1;
            $data['correct_status'] =  $correct_status;
            $data['word_matching'] = $word_matching;
        }

        echo json_encode($data);
    }

    public function st_preview_memorization_pattern_one_ok()
    {
        $PreviewClass=new \PreviewClass();
        $StudentClass=new \StudentClass();
        $qus_ans = 0;
        $question_marks = 0;
        $question_id = $this->request->getVar('question_id');
        $module_id = $this->request->getVar('module_id');
        $question_order_id = $this->request->getVar('current_order');
        $submit_cycle = $this->request->getVar('submit_cycle');
        $memorization_answer = $this->request->getVar('memorization_answer');
        $text = 0;
        $text_1 = 0;

        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        if (isset($answer_info[0]['questionMarks'])) {
            $question_marks = $answer_info[0]['questionMarks'];
        }
        $ans_is_right = 'correct';
        if ($memorization_answer == 'correct') {
            if (isset($answer_info[0]['questionMarks'])) {
                $question_marks = $answer_info[0]['questionMarks'];
            }
        } else {
            $ans_is_right = 'wrong';
        }

        if ($_POST['module_type'] == 2) {
            $table = 'tbl_student_answer';
        } else {
            $table = 'tbl_temp_tutorial_mod_ques';
        }

        $tutorial_ans_info = $StudentClass->getTutorialAnsInfo($table, $module_id, $_SESSION['user_id']);

        if (count($tutorial_ans_info) == 0) {
            if ($ans_is_right == "wrong") {

                $dataArray = $_SESSION['data'];
                if (count($dataArray)) {

                    $dataArray[$_POST['current_order']]['ans_is_right']  = "wrong";
                    $dataArray[$_POST['current_order']]['student_marks']  = 0;
                    $this->session->set('data', $dataArray);

                    $dataArray = $_SESSION['data'];
                    $total_marks = 0;

                    foreach ($dataArray as $key => $value) {
                        $total_marks += $value['student_marks'];
                    }

                    $_SESSION['obtained_marks'] = $total_marks;
                }
            }
        }
        // echo $ans_is_right;echo $_POST['module_type'];die();

        if ($_POST['module_type'] == 1) {
            $this->take_decesion_1($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        } else {
            $this->take_decesion_2($question_marks, $question_id, $module_id, $question_order_id, $ans_is_right);
        }
    }

    public function indexQuesAns($items)
    {
        // print_r($items);die;
        $arr = [];
        foreach ($items as $item) {
            $temp = json_decode($item);
            if ($temp == '') {
            } else {
                $cr = explode('_', $temp->cr);
                //print_r($cr);die;
                $col = $cr[0];
                $row = $cr[1];
                $arr[$col][$row] = array(
                    'type' => $temp->type,
                    'val' => $temp->val
                );
            }
        }
        return $arr;
    }

    public function renderSkpQuizPrevTable($items, $rows, $cols, $showAns = 0)
    {
        //print_r($items);die;
        $row = '';
        for ($i = 1; $i <= $rows; $i++) {
            $row .= '<div class="sk_out_box">';
            for ($j = 1; $j <= $cols; $j++) {
                if ($items[$i][$j]['type'] == 'q') {
                    $row .= '<div class="sk_inner_box"><input type="button" data_q_type="0" data_num_colofrow="" value="' . $items[$i][$j]['val'] . '" name="skip_counting[]" class="form-control input-box  rsskpinpt' . $i . '_' . $j . '" readonly style="min-width:50px; max-width:50px"></div>';
                } else {
                    $ansObj = array(
                        'cr' => $i . '_' . $j,
                        'val' => $items[$i][$j]['val'],
                        'type' => 'a',
                    );
                    $ansObj = json_encode($ansObj);
                    $val = ($showAns == 1) ? ' value="' . $items[$i][$j]['val'] . '"' : '';

                    $row .= '<div class="sk_inner_box"><input autocomplete="off" type="text" ' . $val . ' data_q_type="0" data_num_colofrow="' . $i . '_' . $j . '" value="" name="skip_counting[]" class="form-control input-box ans_input  rsskpinpt' . $i . '_' . $j . '"  style="min-width:50px; max-width:50px">';
                    $row .= '<input type="hidden" value="" name="given_ans[]" id="given_ans">';
                    $row .= '</div>';
                }
            }
            $row .= '</div>';
        }

        return $row;
    }

    public function stu_preview_memorization_pattern_one_matching()
    {
        $TutorClass=new \TutorClass();
        $show_data_array = array();
        $question_id = $this->request->getVar('question_id');
        $start_memorization_one_value = $this->request->getVar('start_memorization_one_value');
        $question_info = $TutorClass->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        if ($start_memorization_one_value == 1) {
            $show_data_array['show_data_array'] = $this->memorization_ans_data($question_name);
            $show_data_array['all_correct'] = 1;
        } else {
            $show_data_array['show_data_array'] = $this->memorization_hide_data($question_name);
            $show_data_array['all_correct'] = 0;
        }
        echo json_encode($show_data_array);
    }

    public function memorization_ans_data($question_name)
    {

        $show_data_array = array();
        $left_memorize_h_p_one = $question_name->left_memorize_h_p_one;
        $left_memorize_p_one = $question_name->left_memorize_p_one;
        $left_memorize_p_one = array_map('strtolower', $left_memorize_p_one);
        foreach ($left_memorize_p_one as $key => $item) {
            if ($left_memorize_h_p_one[$key] == 0) {
                $show_data_array[$key][0] = $item;
                $show_data_array[$key][1] = 0;
            } else {
                $show_data_array[$key][0] = $item;
                $show_data_array[$key][1] = 1;
            }
        }
        return $show_data_array;
    }

    //preview_memorization
    public function memorization_hide_data($question_name)
    {
        $show_data_array = array();
        $left_memorize_h_p_one = $question_name->left_memorize_h_p_one;
        $left_memorize_p_one = $question_name->left_memorize_p_one;
        $left_memorize_p_one = array_map('strtolower', $left_memorize_p_one);
        foreach ($left_memorize_p_one as $key => $item) {
            if ($left_memorize_h_p_one[$key] == 0) {
                $show_data_array[] = $item;
            } else {
                $show_data_array[] = '';
            }
        }
        return $show_data_array;
    }

    public function st_show_tutorial_result($module)
    {  
        error_report_check(); 
        $StudentClass=new \StudentClass();
        $TutorClass=new \TutorClass();
        $AdminClass=new \AdminClass();

        $this->session->remove('correct_answer');
        $this->session->remove('memorize_pattern_three_student_answer');
        $this->session->remove('memorization_three_qus_part_answer');
        $this->session->remove('question_setup_answer_order');

     

        $_SESSION['show_tutorial_result'] = 1;
        $records = $_SESSION;
        $question_id = array_column($records['data'], 'question_id');
        // echo 'jooo';
        // echo '<pre>';
        // print_r($records);die();
        $questions =  $StudentClass->where_in($question_id, 'tbl_question', 'questionType');
        $questionType = array_column($questions, 'questionType');
        $diff_result = array_diff($questionType, [12]);

        if (count($diff_result) == 0) {
            $_SESSION['all_workout_quiz_q'] = 1;
        } else {
            $_SESSION['all_workout_quiz_q'] = 0;
        }
        //echo 'asce re12';die();
        if (!empty($this->session->get('module_id_ASSIGNmodule')) && ($this->session->get('module_id_ASSIGNmodule') == $module)) {
            $dta['status'] = 0;
            $TutorClass->updateInfo('student_homeworks', 'id', $this->session->get('module_id_ASSIGNmoduleID'), $dta);
        }
  
        $user_id = $this->session->get('user_id');
        $data['module_info'] = $TutorClass->getInfo('tbl_module', 'id', $module);
        $data['obtained_marks'] = $StudentClass->get_student_progress($user_id, $module);

        // echo "<pre>";print_r($this->session->userdata('obtained_marks'));die;
        if ($data['module_info'][0]['moduleType'] == 2 && $data['module_info'][0]['optionalTime'] != 0 && empty($data['obtained_marks'])) {
            $std_ans = json_encode($this->session->get('data'));
            $obtained_marks = $this->session->get('obtained_marks');
            $total_marks = $this->session->get('total_marks');
            $student_taken_time = $this->session->get('end_time') - $this->session->get('exam_start');
            $std_ans_module_data['st_id'] = $user_id;
            $std_ans_module_data['st_ans'] = $std_ans;
            $std_ans_module_data['module_id'] = $module;
            $this->db->table('tbl_student_answer')->insert($std_ans_module_data);
            $p_data['timeTaken'] = $student_taken_time;
            $p_data['answerTime'] = $this->session->get('exam_start');
            $p_data['originalMark'] = $total_marks;
            $p_data['studentMark'] = $obtained_marks;
            $p_data['student_id'] = $user_id;
            $p_data['module'] = $module;
            $p_data['percentage'] = ($obtained_marks * 100) / $total_marks;
            $p_data['moduletype'] = 2;
            $p_data['date_time'] = date("Y-m-d");

            $tbl_studentprogress_info = $StudentClass->getWhereThreewoCondition("tbl_studentprogress", "student_id", $this->session->get('user_id'), "module", $module, "date_time", date("Y-m-d"));



            if (count($tbl_studentprogress_info) == 0) {

                $this->db->table('tbl_studentprogress')->insert($p_data);

                // end added for prize
            }
            $data['obtained_marks'] = $StudentClass->get_student_progress($user_id, $module);
        }
       
        if ($data['module_info'][0]['moduleType'] == 1) {
            
            //assignModuleByTutor
            $tutorial_ans_info = $StudentClass->getTutorialAnsInfo('tbl_temp_tutorial_mod_ques', $module, $user_id);

            if ($tutorial_ans_info[0]['full_complete'] == 0) {
                $all_time = $_SESSION['data'];

                // $time_taken=0;
                // foreach($all_time as $for_time){ 
                //     $time_taken= $time_taken+$for_time[student_taken_time];
                // }
                $new_times = $this->session->get('exact_time');
                $now_timw = time();
                $time_taken = $now_timw - $new_times;
                $std_ans = json_encode($this->session->get('data'));
                $obtained_marks = $this->session->get('obtained_marks');
                $total_marks = $this->session->get('total_marks');
                $student_taken_time = time() - $this->session->get('exam_start');
                $all_time = $_SESSION['data'];

                $time_taken = 0;
                foreach ($all_time as $for_time) {
                    // echo $for_time[student_taken_time]."<br>";
                   // $time_taken = $time_taken + $for_time[student_taken_time];
                }
                $the_new_total_taken_time=time()-$this->session->get('start_exam_time_new');

                $the_new_total_ans_time=$this->session->get('take_ans_time_new');
                
               // echo time()."now".$the_new_total_taken_time."next".$this->session->userdata('start_exam_time_new');
            //    echo 'jjjj'.$the_new_total_ans_time;die();
                

                $p_data['timeTaken'] = $the_new_total_taken_time;
                //$p_data['timeTaken'] = $student_taken_time;
                $p_data['answerTime'] = $the_new_total_ans_time;
                $p_data['originalMark'] = $total_marks;
                $p_data['studentMark'] = $obtained_marks;
                $p_data['student_id'] = $user_id;
                $p_data['module'] = $module;
                $p_data['percentage'] = ($obtained_marks * 100) / $total_marks;
                $p_data['moduletype'] = 1;
                $tbl_studentprogress_id = $StudentClass->insertId('tbl_studentprogress', $p_data);

                $tbl_std_ans['st_id'] = $user_id;
                $tbl_std_ans['st_ans'] = json_encode($_SESSION['data']);
                $tbl_std_ans['module_id'] = $module;
                $tbl_std_ans['created_at'] = date("Y-m-d H:i:s");
                $tbl_std_ans['tbl_studentprogress_id'] = $tbl_studentprogress_id;

                $StudentClass->insertId('tbl_student_answer_tutorial', $tbl_std_ans);

                $data['obtained_marks'] = $StudentClass->get_student_progress($user_id, $module);

                $toUpdate['full_complete'] = 1;
                $StudentClass->updateInfo('tbl_temp_tutorial_mod_ques', 'id', $tutorial_ans_info[0]['id'], $toUpdate);
            }
        }

        $get_dialogue = $StudentClass->get_today_dialogue(date('m/d/Y'));
        // if (!$get_dialogue) {
        // $get_dialogue = $this->Student_model->get_whole_year_dialogue(date('Y'));
        // } 
        if (!$get_dialogue) {
            $get_dialogue = $StudentClass->get_auto_repeat_dialogue();
        }
        $data['dialogue'] = $get_dialogue;

        // echo date('m/d/Y');
        // echo '<pre>';print_r($data['module_info']);die;

        $tutorial_ans_info = array();
        if ($data['module_info']) {
            if ($data['module_info'][0]['moduleType'] == 1) {
                $get_tutorial_ans_info = $StudentClass->getTutorialAnsInfo('tbl_temp_tutorial_mod_ques', $module, $user_id);

                $tutorial_ans_info = array();
                if (isset($get_tutorial_ans_info[0])) {
                    $tutorial_ans_info = json_decode($get_tutorial_ans_info[0]['st_ans'], true);
                }

                $data['obtained_marks'] = $this->session->get('obtained_marks');
                $data['total_marks'] = $this->session->get('total_marks');
            } elseif ($data['module_info'][0]['moduleType'] == 2) {
                $tutorial_ans_info = $StudentClass->getTutorialAnsInfo_('tbl_st_error_ans', $module, $user_id);

                // $tutorial_ans_info = json_decode($get_tutorial_ans_info[0]['st_ans'],TRUE);
                //echo "<pre>";print_r($tutorial_ans_info);die;
                $module_id = $module;
            } else {
                $get_tutorial_ans_info = $StudentClass->getTutorialAnsInfo('tbl_student_answer', $module, $user_id);
                $tutorial_ans_info = json_decode($get_tutorial_ans_info[0]['st_ans'], true);
            }

            // if($tutorial_ans_info) {
            $data['user_info'] = $TutorClass->getInfo('tbl_useraccount', 'id', $user_id);

            if ($data['module_info'][0]['moduleType'] == 1) {
                $parent_info = $TutorClass->getInfo('tbl_useraccount', 'id', $data['user_info'][0]['parent_id']);
                $settins_sms_status   = $AdminClass->getSmsType("Template Activate Status");

                if ($settins_sms_status[0]['setting_value']) {
                    $v_hours = floor($student_taken_time / 3600);
                    $remain_seconds = $student_taken_time - ($v_hours * 3600);
                    $v_minutes = floor($remain_seconds / 60);
                    $v_seconds = $remain_seconds - $v_minutes * 60;

                    $time_hour_minute_sec = $v_hours . " : "  . $v_minutes . " : " . $v_seconds;

                    $data['time_hour_minute_sec'] = $time_hour_minute_sec;
                    $data['parent_info']          = $parent_info[0]['user_mobile'];
                }
            }
            if (($module_info[0]['moduleType'] == 2 && !$tutorial_ans_info) ||
                ($module_info[0]['moduleType'] == 1 && $flag != 1) || $module_info[0]['moduleType'] == 3 || $module_info[0]['moduleType'] == 4
            ) {
            }
            $data['tutorial_ans_info'] = $tutorial_ans_info;
            $data['module_info'] = $TutorClass->getInfo('tbl_module', 'id', $module);
            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

            return view('students/show_module_result', $data);

        } else {

            redirect('error');
        }
    }

    public function finish_all_module_question($module_id, $point)
    {
        error_report_check();
        // echo "hello";die();
        $StudentClass=new \StudentClass();

        $this->session->remove('start_exam_time_new');
        $this->session->remove('take_ans_time_new');
       
        $_SESSION['show_tutorial_result'] = 0;
        $user_id = $this->session->get('user_id');
        $module  = $StudentClass->getInfo('tbl_module','id',$module_id);
        $moduleCreator = $module[0]['user_id'];

         $moduleType = $module[0]['moduleType'];
       


        $StudentClass->deleteInfo_mod_ques_2($user_id, $module_id);

        $gradeCheck = $this->db->table('student_grade_log')->where('user_id', $user_id)->get()->getRow();
        $user = $this->db->table('tbl_useraccount')->where('id', $user_id)->get()->getRow();

        $user_grade   = $user->student_grade;
        $latest_grade = $gradeCheck->grade;
        // echo  $user_grade;
        // echo "<br>";
        // echo $latest_grade;
		

        $tutorial_ans_info = $StudentClass->getTutorialAnsInfo('tbl_temp_tutorial_mod_ques', $module_id, $user_id);
        if ($tutorial_ans_info) {
            $StudentClass->deleteInfo('tbl_temp_tutorial_mod_ques', 'id', $tutorial_ans_info[0]['id']);
        }

        $b = array();
        date_default_timezone_set($this->site_user_data['zone_name']);
        $today = date('Y-m-d');

        //        First check in module has repitition days
        //        Second check if available then check is repitition days match with today

        if ($module[0]['repetition_days']) {
            $get_student_ans_by_module = $StudentClass->student_module_ans_info($this->session->get('user_id'), $module[0]['id']);
            $moduleCreated =  date("Y-m-d", strtotime($get_student_ans_by_module[0]['created_at']));

            $ck_repetation_update =  $StudentClass->repete_date_module_index_ck($module_id, $user_id);

            // $this->Student_model->repete_date_module_index($module_id, json_encode($update_new_repeted_day));
            // echo "<br>";
            // echo count($ck_repetation_update);
            // echo "<br>";

            if (count($ck_repetation_update) > 0) {

                // echo 111; print_r($ck_repetation_update); die();

                $repition_days = strlen($ck_repetation_update[0]['repetation']) ? json_decode($ck_repetation_update[0]['repetation']) : [1, 2, 3];
                foreach ($repition_days as $key => $value) {
                    $singel_days[] = explode("_", $value)[0];
                }
                foreach ($singel_days as $key => $a) {
                    if ($key != 0) {
                        $new_repetation_day[] = $a . '_' . date('Y-m-d', strtotime($moduleCreated . ' +' . $a . ' days'));
                    }
                }

                $repetation_insert['student_id'] = $user_id;
                $repetation_insert['module_id'] = $module_id;
                $repetation_insert['repetation'] = json_encode($new_repetation_day);

                if (count($new_repetation_day)) {
                    //echo 12;die;
                    $StudentClass->updateInfo('tbl_student_repetation_day',  'id', $ck_repetation_update[0]['id'], $repetation_insert);
                }
                //die;

            } else {
                //echo 222;die();
                // start added for prize
                // if ($user_grade == $latest_grade && $moduleType == 2 && $moduleCreator == 2) {

                //     $mData['user_id']   = $user_id;
                //     $mData['module_id'] = $module_id;
                //     $mData['complete_date'] = date('Y-m-d');
                //     $mData['percentage']= $point;
                //     $this->db->insert('daily_modules',$mData);

                //     $pData['user_id'] = $user_id;
                //     $getPointInfo = $this->db->where('user_id',$user_id)->get('module_points')->row();
                //     if ($getPointInfo) {
                //         $recent_point = $getPointInfo->point;
                //         $pData['point'] = $point + $recent_point;
                //         //print_r($pData);die;
                //         $this->db->where('user_id',$user_id)->update('module_points',$pData);
                //     }else{
                //         $pData['point'] = $point;
                //         $this->db->insert('module_points',$pData);
                //     }



                //     $getProPoint = $this->db->where('user_id',$user_id)->get('product_poinits')->row();
                //     $tr_point = $this->db->where('user_id',$user_id)->get('target_points')->row();
                //     $target_point = $tr_point->targetPoint;

                //     //$pointCheck  = $this->db->where('user_id',$user_id)->get('product_poinits')->row();

                //     if ($getProPoint) {
                //         $proPoint['user_id'] = $user_id;
                //         $sumPoint = ($getProPoint->recent_point +  $point);

                //         if ($sumPoint >= $target_point) {
                //             $proPoint['recent_point'] = $target_point;
                //             $bnsPoint = ($sumPoint - $target_point);
                //             $proPoint['bonus_point']  = $getProPoint->bonus_point + $bnsPoint;
                //             $proPoint['total_point']  = $getProPoint->total_point + $point;

                //         }else{
                //             $proPoint['recent_point'] = $getProPoint->recent_point +  $point;
                //             $proPoint['total_point']  = $getProPoint->total_point + $point;
                //         }


                //         $this->db->where('user_id',$user_id)->update('product_poinits',$proPoint);

                //     }else{
                //         $proPoint['user_id'] = $user_id;
                //         $proPoint['recent_point'] = $point;
                //         $proPoint['total_point'] = $point;
                //         $this->db->insert('product_poinits',$proPoint);
                //     }
                // }

                // end added for prize


                $repition_days = strlen($module[0]['repetition_days']) ? json_decode($module[0]['repetition_days']) : [1, 2, 3];
                foreach ($repition_days as $key => $value) {
                    $singel_days[] = explode("_", $value)[0];
                }

                foreach ($singel_days as $key => $a) {
                    $new_repetation_day[] = $a . '_' . date('Y-m-d', strtotime($moduleCreated . ' +' . $a . ' days'));
                }

                // print_r($singel_days); 
                // print_r($new_repetation_day); die();
                // echo 'yes';die();

                $repetation_insert['student_id'] = $user_id;
                $repetation_insert['module_id'] = $module_id;
                $repetation_insert['repetation'] = json_encode($new_repetation_day);

                if (count($new_repetation_day)) {
                    $StudentClass->insertInfo('tbl_student_repetation_day',  $repetation_insert);
                }
            }

            function fix($n)
            {
                if ($n) {
                    $val = (explode('_', $n));
                    return $val[1];
                }
            }

            $b = array_map("fix", $repition_days);
            $b = count($b) ? $b : [];
        }

        //        if today is not available in repitition days then delete
        //        Delete tbl_st_error_ans data for Everyday Study


        if (!in_array($today, $b)) {
            $student_error_ans_info = $StudentClass->student_error_ans_info($user_id, $module_id);
            if ($student_error_ans_info) {
                if ($module[0]['moduleType'] == 2 && $module[0]['optionalTime'] != 0) {
                    $StudentClass->delete_all_st_error_ans($module_id, $user_id);
                } else {
                    $StudentClass->delete_all_st_error_ans($module_id, $user_id);
                }
            }
        }
	
        if (in_array($today, $b)) {
            $data['std_id'] = $user_id;
            $data['repeat_module_id'] = $module_id;
            $data['answered_date'] = $today;

            $this->db->table('tbl_answer_repeated_module')->insert($data);
        }
        if (!$this->request->isAJAX()) {
        if($this->session->get('set_url_module_list')){
			$get_url =$this->session->get('set_url_module_list');

            $this->session->remove('set_url_module_list');
            return redirect()->to($get_url);
        }}
        
        //if (!$this->request->isAJAX()) {
           // return redirect()->to(base_url('/'));
         //}else{
             //echo "success";
        // }

        
    }

    public function studentsModuleByQStudy()
    {
        error_report_check();
        $StudentClass=new \StudentClass();
        $ModuleClass=new \ModuleClass();
        $AdminClass=new \AdminClass();
        $user_id = $this->session->get('user_id');

        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $user_info = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $parent_id = $data['user_info'][0]['parent_id'];
        $payment_details = $this->db->table('tbl_payment')->where('user_id', $this->session->get('user_id'))->limit(1)->orderBy('id','desc')->get()->getRow();
        $payment_id = $payment_details->id;


        $posts         = $this->request->getVar();
        $tutorId      = isset($posts['tutorId']) ? $posts['tutorId'] : '';
        //$payment_courses  = $this->Student_model->paymentCourse($payment_id);
        $payment_courses  = $this->db->table('tbl_registered_course')->where('user_id', $user_id)->where('cost <>', 0)->where('endTime >',time())->get()->getResultArray();
        if ($tutorId == 2) {
            $st_colaburation = 0;
            foreach ($payment_courses as $pc => $value) {
                $val[$pc] = $value['id'];
                if ($val[$pc] == 44) {
                    $st_colaburation = $st_colaburation + 1;
                }
            }
            $data['st_colaburation'] = $st_colaburation;
            //echo $st_colaburation;die();
            if ($st_colaburation == 1) {
                echo strlen($row) ? $row : 'no module found';
                die;
            }
        }


        $data['student_error_ans'] = $StudentClass->getInfo('tbl_st_error_ans', 'st_id', $this->session->get('user_id'));

        $post         = $this->request->getVar();
        $subjectId    = isset($post['subjectId']) ? $post['subjectId'] : '';
        $chapterId    = isset($post['chapterId']) ? $post['chapterId'] : '';
        $moduleType   = isset($post['moduleType']) ? $post['moduleType'] : '';
        $repetition   = isset($post['repetition']) ? $post['repetition'] : '';
        // echo $moduleType;die;
        //        $tutorType  = isset($post['tutorId']) ? $post['tutorId'] : '';
        $tutorId      = isset($post['tutorId']) ? $post['tutorId'] : '';
        $tutorInfo = $StudentClass->getInfo('tbl_useraccount', 'id', $tutorId);

        $studentGrade_country = $StudentClass->studentClass($this->loggedUserId);
        $studentGrade = $studentGrade_country[0]['student_grade'];


        if ($tutorId == 2) {
            $student_colaburation = 0;
            $payCourses = $StudentClass->payment_list_Courses($parent_id);
            foreach ($payCourses as $payCours => $value) {
                $val[$payCours] = $value['id'];
                if ($val[$payCours] == 44) {
                    $student_colaburation = $student_colaburation + 1;
                }
            }
            $data['student_colaburation'] = $student_colaburation;


            $checkDirectDepositPendingCourse = $AdminClass->getDirectDepositPendingCourse($user_id);
            $checkActiveCourse = $AdminClass->getActiveCourse($user_id);
            //  echo $checkDirectDepositPendingCourse;die();
            if ($checkDirectDepositPendingCourse > 0 && $checkActiveCourse == 0) {
                echo strlen($row) ? $row : 'no module found';
                die;
            }
        }

        if ($user_info[0]['subscription_type'] == "trial") {
            $createAt = $user_info[0]['created'];
            $check_trial_days = getTrailDate($createAt, $this->db);
        }
        //echo $days;die();
        if (isset($check_trial_days)) {
            if ($check_trial_days <= 0) {
                if (count($payment_courses) == 0) {
                    echo strlen($row) ? $row : 'no module found';
                    die;
                }
            }
        } else {
            if (count($payment_courses) == 0) {
                echo strlen($row) ? $row : 'no module found';
                die;
            }
        }

        if ($subjectId == 'all') {
            $subjectId = '';
        }

        if (isset($tutorInfo[0]['user_type']) && $tutorInfo[0]['user_type'] == 7) { //q-study
            $conditions = array(
                //            'subject'              => $subjectId,
                //            'course_id'            => ($moduleType == 2) ? $subjectId : 0,
                'chapter'              => $chapterId,
                'moduleType'           => $moduleType,
                'tbl_module.user_type' => 7,
                'studentGrade'         => $studentGrade,
            );
            if ($moduleType == 2) {
                $conditions['course_id'] = $subjectId;
            } else {
                $conditions['subject'] = $subjectId;
            }

            $conditions = array_filter($conditions);

            // Newly Added
            $data['all_subject_student'] = $StudentClass->getInfo('tbl_registered_course', 'user_id', $this->session->get('user_id'));
            $result = array_column($data['all_subject_student'], 'course_id');

            $registered_course = implode(', ', $result);
            if ($subjectId == 'all' || $subjectId == '') {
                $desired_result = '';
            } else {
                $desired_result = $subjectId;
            }

            // $data['all_subject_qStudy'] =$this->Student_model->get_all_subject($tutorInfo[0]['user_type']);
            // $data['all_subject_student'] =$this->Student_model->get_all_subject_for_registered_student($this->session->userdata('user_id'));

            // if ($subjectId == 'all' || $subjectId == '') {
            // $first_array_q = array_column($data['all_subject_qStudy'], 'subject_id');
            // $second_array_st = array_column($data['all_subject_student'], 'subject_id');

            // $desired_result = '';
            // $result = array_intersect($first_array_q, $second_array_st);
            // if ($result) {
            // $desired_result = implode(', ', $result);
            // }
            // } else {
            // $desired_result = $subjectId;
            // }


            if ($moduleType == 2) {
                // $all_module = $this->ModuleModel->allModule(array_filter($conditions));
                $all_module = $ModuleClass->allModule(array_filter($conditions), $studentGrade_country[0]['country_id']);
            } else {
                $all_module = $StudentClass->all_module_by_type($tutorInfo[0]['user_type'], $desired_result, $result, $conditions);
            }
            // $data['maincontent'] = $this->load->view('students/qstudy_module/all_tutorial_list', $data, true);
        } else { //module created by general tutor
            $conditions = array(
                'subject'              => $subjectId,
                'chapter'              => $chapterId,
                'moduleType'           => $moduleType,
                // 'tbl_module.user_type' => $tutorType,
                'studentGrade'         => $studentGrade,
                'user_id'              => $tutorId,
            );

            $conditions = array_filter($conditions);

            // $all_module = $this->ModuleModel->allModule(array_filter($conditions));
            $all_module = $ModuleClass->allModule(array_filter($conditions), $studentGrade_country[0]['country_id']);
            //echo '<pre>';print_r(array_filter($conditions));die;
        }

        // $all_module = $this->ModuleModel->allModule(array_filter($conditions));

        $new_array  = array();
        $sct_info  = array();


        foreach ($all_module as $module) {
            if ($module['isAllStudent']) {
                $sct_info[] = $module;
            } elseif (strlen($module['individualStudent'])) {
                if ($module['individualStudent']) {
                    $stIds = json_decode($module['individualStudent']);
                    if (in_array($this->loggedUserId, $stIds)) {
                        $sct_info[] = $module;
                    }
                }
            }
        }


        //echo '<pre>';print_r($sct_info);
        // echo '<pre>';print_r($this->loggedUserId);die;
        //here change this condition for tutor assign module get by following assigning structure  Added AS
        if ($moduleType == 1 ||  $moduleType == 2) {
            foreach ($sct_info as $idx => $module) {

                $get_student_ans_by_module = $StudentClass->student_module_ans_info($this->session->get('user_id'), $module['id']);

                if ($this->site_user_data['student_grade'] != $module['studentGrade']) {
                    unset($sct_info[$idx]);
                } elseif (json_decode($module['repetition_days']) != '' && $module['repetition_days'] != 'null') {
                    $repition_days = json_decode($module['repetition_days']);
                    $repet_day = $repition_days;

                    $singel_days = array();
                    $new_repetation_day = array();
                    $didnt_answered_repeted_module = array();


                    if ($repition_days != '' && $get_student_ans_by_module) {
                        $st_ans = json_decode($get_student_ans_by_module[0]['st_ans'], true);

                        if (!in_array('wrong', array_column($st_ans, 'ans_is_right'))) { // search value in the array
                        } else {

                            $moduleCreated =  date("Y-m-d", strtotime($get_student_ans_by_module[0]['created_at']));
                        }

                        $ck_repetation_update =  $StudentClass->repete_date_module_index_ck($module['id'], $this->session->get('user_id'));


                        if (count($ck_repetation_update) == 0) {

                            foreach ($repet_day as $key => $value) {
                                $singel_days[] = explode("_", $value)[0];
                            }
                            foreach ($singel_days as $key => $a) {
                                $new_repetation_day[] = $a . '_' . date('Y-m-d', strtotime($moduleCreated . ' +' . $a . ' days'));
                            }

                            $b = array_map(array($this, 'get_repitition_days'), $new_repetation_day);

                            date_default_timezone_set($this->site_user_data['zone_name']);
                            $today = date('Y-m-d');
                            $didnt_answered_repeted_module = array();

                            foreach ($b as $k => $value) {
                                if ($value <= $today) {
                                    $didnt_answered_repeted_module[] = $new_repetation_day[$k];
                                }
                            }
                        } else {

                            $repition_days = json_decode($ck_repetation_update[0]['repetation'], true);

                            foreach ($repition_days as $key => $value) {
                                $singel_days[] = explode("_", $value)[0];
                            }
                            foreach ($singel_days as $key => $a) {
                                $new_repetation_day[] = $a . '_' . date('Y-m-d', strtotime($moduleCreated . ' +' . $a . ' days'));
                            }
                            $b = array_map(array($this, 'get_repitition_days'), $new_repetation_day);

                            date_default_timezone_set($this->site_user_data['zone_name']);
                            $today = date('Y-m-d');
                            $didnt_answered_repeted_module = array();

                            foreach ($b as $k => $value) {
                                if ($value <= $today) {
                                    $didnt_answered_repeted_module[] = $new_repetation_day[$k];
                                }
                            }
                        }



                        if ((in_array($today, $b) && $get_student_ans_by_module) || count($didnt_answered_repeted_module) > 0) {

                            $get_answer_repeated_module = $StudentClass->get_answer_repeated_module($this->session->get('user_id'), $module['id'], $today);

                            $st_ans = json_decode($get_student_ans_by_module[0]['st_ans'], true);

                            if (!in_array('wrong', array_column($st_ans, 'ans_is_right'))) { // search value in the array
                                unset($sct_info[$idx]);
                            } else { // If wrong ans is available
                                $this->insert_error_question('', $st_ans);
                                $key = array_search($today, $b) + 1;

                                $sct_info[$idx]['is_repeated'] = 1;
                                $sct_info[$idx]['answered_date'] = $moduleCreated;
                                $sct_info[$idx]['required_repeted_module'] = json_encode($didnt_answered_repeted_module);
                            }
                        } elseif ($get_student_ans_by_module) {
                            unset($sct_info[$idx]);
                        }
                    }
                } elseif ((($module['repetition_days'] == '' && $get_student_ans_by_module) || $module['repetition_days'] == 'null')) {
                    unset($sct_info[$idx]);
                }
            }

            // Keep array with same index to match for all type of module
            foreach ($sct_info as $module) {
                $new_array[] = $module;
            }
            //echo "<pre>";print_r($new_array);die();
            if ($repetition != null) {
                $this->show_repetition_module($new_array);
            } else {
                $this->show_all_module($new_array);
            }
        } else {
            // $this->show_all_module($all_module);
            $this->show_all_module($sct_info);
        }
    }


    public function show_repetition_module($allModule)
    {
        date_default_timezone_set($this->site_user_data['zone_name']);
        $now_time = date('Y-m-d H:i:s');

        $now_time_for_additional = date("Y-m-d", strtotime($now_time));

        // echo $allModule[0]['exam_end'].'<pre>';
        // echo strtotime($now_time);die;
        $count = 0;

        $row = '';
        if ($allModule) {

            if ($allModule[0]['moduleType'] != 3) {
                $row .= '<input type="hidden" id="first_module_id" value="' . $allModule[0]['id'] . '">';
            }

            foreach ($allModule as $module) {
                $now_time_for_additional_2 = date("Y-m-d", strtotime($module['exam_end']));
                if ($module['moduleType'] != 3 || ($module['optionalTime'] == 0 && $module['moduleType'] == 3 && strtotime($now_time) < strtotime($module['exam_end']))) {

                    if ($module['moduleType'] == 3 && $count == 0) {
                        // print_r($module);
                        $row .= '<input type="hidden" id="first_module_id" value="' . $module['id'] . '">';
                        $count++;
                    }
                    $is_repeated = '';
                    if (isset($module['is_repeated']) && $module['is_repeated'] == 1) {
                        $is_repeated = '(Repeated Module)';
                    }
					$base=base_url();
                    $video_link = json_decode($module['video_link']);
                    $link =$base.'/get_tutor_tutorial_module/' . $module['id'] . '/1';
                    /*if ($video_link) {
                        $link = 'video_link/'.$module['id'].'/'.$module['moduleType'];
                    }*/

                    $row .= '<tr>';
                    //$row .= '<td><a onclick="get_permission('.$module['id'].')" href="' . $link .'">' . $module['moduleName'] . '</a></td>';
                    if (isset($module['is_repeated'])) {

                        $date = date("d/m/Y", strtotime($module['answered_date']));
                        $required_repeted_module = json_decode($module['required_repeted_module'], true);

                        $row .= '<td> <ul> ';
                        foreach ($required_repeted_module as $key => $value) {
                            $pieces = explode("_", $value);
                            $day = $pieces[0];
                            if ($key == 0) {
                                $row .= '<li> <a onclick="get_permission(' . $module['id'] . ')" href="javascript:;"> <span style="color:red;text-decoration: underline;"> Repeted wrong answer </span> <span class="text-muted" > ' . $date . ' </span> <span style="color:blue;" > ( ' . $day . ' Day) </span></a> </li>';
                            } else {
                                $row .= '<li> <a onclick="get_permission(0)" href="javascript:;"> <span style="color:red;text-decoration: underline;"> Repeted wrong answer </span> <span class="text-muted" > ' . $date . ' </span> <span style="color:blue;" > ( ' . $day . ' Day) </span></a> </li>';
                            }
                        }
                        $row .= '</ul> </td>';
                        $row .= '<td>' . $module['trackerName'] . '</td>';
                        $row .= '<td>' . $module['individualName'] . '</td>';
                    } else {
                        // $row .= '<td><a onclick="get_permission('.$module['id'].')" href="javascript:;">' . $module['moduleName'] . '</a></td>';
                        // $row .= '<td>' . $module['trackerName'] . '</td>';
                        // $row .= '<td>' . $module['individualName'] . '</td>';
                    }
                    //$row .= '<td style="cursor:pointer;"><a onclick="get_permission('.$module['id'].')">' . $module['moduleName'] . $is_repeated . '</a></td>';
                    // $row .= '<td>'.$module['creatorName'].'</td>';
                    if ($module['moduleType'] == 2 &&  $module['user_id'] == 2) {
                    } else {
                        $row .= '<td>' . $module['subject_name'] . '</td>';
                        $row .= '<td>' . $module['chapterName'] . '</td>';
                    }
                    $row .= '</tr>';
                }
                if ($module['optionalTime'] != 0 && $module['moduleType'] == 3 && ($now_time_for_additional_2 == $now_time_for_additional)) {


                    if ($module['moduleType'] == 3 && $count == 0) {
                        // print_r($module);
                        $row .= '<input type="hidden" id="first_module_id" value="' . $module['id'] . '">';
                        $count++;
                    }

                    // $count++;
                    $is_repeated = '';
                    if (isset($module['is_repeated']) && $module['is_repeated'] == 1) {
                        $is_repeated = '(Repeated Module)';
                    }
					$base=base_url();
                    $video_link = json_decode($module['video_link']);
                    $link =$base.'/get_tutor_tutorial_module/' . $module['id'] . '/1';
                    /*if ($video_link) {
                        $link = 'video_link/'.$module['id'].'/'.$module['moduleType'];
                    }*/

                    $row .= '<tr>';
                    //$row .= '<td><a onclick="get_permission('.$module['id'].')" href="' . $link .'">' . $module['moduleName'] . '</a></td>';
                    $row .= '<td><a onclick="get_permission(' . $module['id'] . ')" href="javascript:;">' . $module['moduleName'] . '</a></td>';
                    //$row .= '<td style="cursor:pointer;"><a onclick="get_permission('.$module['id'].')">' . $module['moduleName'] . $is_repeated . '</a></td>';
                    // $row .= '<td>'.$module['creatorName'].'</td>';
                    $row .= '<td>' . $module['trackerName'] . '</td>';
                    $row .= '<td>' . $module['individualName'] . '</td>';
                    $row .= '<td>' . $module['subject_name'] . '</td>';
                    $row .= '<td>' . $module['chapterName'] . '</td>';
                    $row .= '</tr>';
                }
            }
        }
        echo strlen($row) ? $row : 'no module found';
    }


    public function whiteboard_items()
    {
        $FaqClass=new \FaqClass();
        $data=array();
        if ($this->session->get('userType') == 3) {
            $data['video_help'] = $FaqClass->videoSerialize(6, 'video_helps');
        }
        if ($this->session->get('userType') == 4) {
            $data['video_help'] = $FaqClass->videoSerialize(8, 'video_helps');
        }

        return view('students/whiteboard_items',$data);

    }

    public function std_question_store()
    {
        $StudentClass=new \StudentClass();
        $AdminClass=new \AdminClass();

        $data['user_info'] = $StudentClass->userInfo($this->loggedUserId);
       $data['allCountry']  = $this->db->table('tbl_country')->get()->getResultArray();

        $subject_with_course = $StudentClass->studentSubjects($this->loggedUserId);
        $students_all_subject = array();
        foreach ($subject_with_course as $subject_course) {
            $set_subject = 1;
            if ($subject_course['isAllStudent'] == 0) {
                $individualStudent = json_decode($subject_course['individualStudent']);
                $individualStudent = is_null($individualStudent) ? [] : $individualStudent;
                if (sizeof($individualStudent) && in_array($this->loggedUserId, $individualStudent)) {
                    $set_subject = 1;
                } else {
                    $set_subject = 0;
                }
            }
            if ($set_subject == 1) {
                $students_all_subject[] = $subject_course;
            }
        }
        $data['studentSubjects'] = array_values(array_column($students_all_subject, null, 'subject_id'));
        $data['registered_courses'] = $StudentClass->registeredCourse($this->session->get('user_id'));
        $std_subjects =  array();
        if (isset($data['registered_courses'][0]['id'])) {

            $courses = $data['registered_courses'];
            foreach ($courses as $course) {
                $assign_course = $StudentClass->getInfo('tbl_assign_subject', 'course_id', $course['id']);
                if (!empty($assign_course)) {
                    $subjectId = json_decode($assign_course[0]['subject_id']);
                    $i = 0;
                    foreach ($subjectId as $value) {
                        $sb =  $StudentClass->getInfo('tbl_subject', 'subject_id', $value);
                        if (!empty($sb)) {
                            $std_subjects[] = $sb;
                        }
                        $i++;
                    }
                }
            }
        }
        $data['std_subjects'] = $StudentClass->getInfo('tbl_question_store_subject', 'created_by', 2);
        $first_subject = $std_subjects[0][0]['subject_id'];
        $chapter =  $StudentClass->getInfo('tbl_chapter', 'subjectId', $first_subject);
        $first_chapter = $chapter[0]['id'];
        $data['chapterName'] = $chapter[0]['chapterName'];
        $grade = $data['user_info'][0]['student_grade'];
        $conditions['grade']     = $grade;
        $conditions['subject']   = $first_subject;
        // $conditions['chapter']   = $first_chapter;

        $data['store_data'] = $StudentClass->getQuestionStore($conditions);

        return view('students/question_store',$data);

    }

    public function get_question_store_data()
    {
        $StudentClass=new \StudentClass();
        $subject_id = 0;
        $grade      = 0;
        $post = $this->request->getVar();
        //$clean = $this->security->xss_clean($post);
        $subject_id = $post['sub_id'];
        $grade      = $post['grade'];
        $result['error'] = 0;
        $result['msg'] = '';
        if ($subject_id != 0 && $grade != 0) {
            $conditions['grade']     = $grade;
            $conditions['subject']   = $subject_id;
            $store_data = $StudentClass->getQuestionStore($conditions);
            $html = '';
            if (!empty($store_data)) {
                foreach ($store_data as $key => $item) {
                    $chapter_id = $item['chapter'];
                    $chapter =  $StudentClass->getInfo('tbl_question_store_chapter', 'id', $chapter_id);
                    $html .= '<tr>';
                    $html .= '<td><a href="download_question_store/' . $item['id'] . '" store-id' . $item['id'] . '>' . $chapter[0]['chapter_name'] . '</a></td>';
                    $html .= '<td><img style="width:25px;"src="' . base_url('/') . 'assets/images/pdf-icon2.png"></td>';
                    $html .= '</tr>';
                }
            } else {
                $html .= '<tr>';
                $html .= '<td></td>';
                $html .= '<td>No data found!</td>';
                $html .= '</tr>';
            }
            $result['error'] = 0;
            $result['data'] = $html;
            echo json_encode($result);
            die;
        }
        $result['error'] = 1;
        $result['msg'] = 'Invalid data!';
        echo json_encode($result);
        die;
    }

    
    public function search_question_store()
    {
        $StudentClass=new \StudentClass();

        $subject_id = 0;
        $country    = 0;
        $grade      = 0;
        $post = $this->request->getVar();
        //$clean = $this->security->xss_clean($post);

        if ($post['grade'] != '') {
            $grade      = $post['grade'];
        }
        if ($post['subject_id'] != '') {
            $subject_id      = $post['subject_id'];
        }
        if ($post['country'] != '') {
            $country      = $post['country'];
        }

        $result['error'] = 0;
        $result['msg'] = '';
        if ($subject_id != 0 && $grade != 0 &&  $country != 0) {
            $conditions['country']   = $country;
            $conditions['grade']     = $grade;
            $conditions['subject']   = $subject_id;

            $store_data = $StudentClass->getQuestionStore($conditions);
            $html = '';
            if (!empty($store_data)) {
                foreach ($store_data as $key => $item) {
                    $chapter_id = $item['chapter'];
                    $chapter =  $StudentClass->getInfo('tbl_question_store_chapter', 'id', $chapter_id);
                    $html .= '<tr>';
                    $html .= '<td><a href="'.base_url('/').'/download_question_store/' . $item['id'] . '" store-id' . $item['id'] . '>' . $chapter[0]['chapter_name'] . '</a></td>';
                    $html .= '<td><img style="width:25px;"src="' . base_url('/') . '/assets/images/pdf-icon2.png"></td>';
                    $html .= '</tr>';
                }
            } else {
                $html .= '<tr>';
                $html .= '<td></td>';
                $html .= '<td>No data found!</td>';
                $html .= '</tr>';
            }
            $result['error'] = 0;
            $result['data'] = $html;
            echo json_encode($result);
            die;
        }
        $result['error'] = 1;
        $result['msg'] = 'Invalid data!';
        echo json_encode($result);
        die;
    }
   
    public function student_progress_step_7()
    {
        $StudentClass=new \StudentClass();
        $data['registered_courses'] = $StudentClass->registeredCourse($this->session->get('user_id'));

        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        //echo "<pre>";print_r($data);die();

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return view('students/student_progress_step_qstudy',$data);
    }

 
    public function student_progress_step()
    {
        $StudentClass=new \StudentClass();
        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['types'] = $StudentClass->get_organizing('tbl_enrollment', $this->session->get('user_id'));

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('students/student_progress_step',$data);

    }


    public function renderedChapters($subjectId)
    {
        //echo $subjectId;die();
        $StudentClass=new \StudentClass();
        $chapters = $StudentClass->chaptersOfSubject($subjectId);
        $row = '<option value="">Select Chapter</option>';
        foreach ($chapters as $chapter) {
            $row .= '<option value="' . $chapter['id'] . '">' . $chapter['chapterName'] . '</option>';
        }
        echo $row;
    }

    public function student_progress_report($studentId, $ideaId, $ideaNo, $questionId)
    {
        $StudentClass=new \StudentClass();
        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['types'] = $StudentClass->get_organizing('tbl_enrollment', $this->session->get('user_id'));


        $data['specific_std_report'] = $StudentClass->getSpecificStudentProgressReport($studentId, $ideaId, $ideaNo, $questionId);
        // echo "<pre>"; print_r($data['specific_std_report']); die();

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('students/student_progress_report',$data);
    }
	
	
    public function download_question_store($id)
    {

        $StudentClass=new \StudentClass();
        if (is_numeric($id)) {

            $store = $StudentClass->getInfo('tbl_questions_store', 'id', $id);


            if (isset($store[0]['student_file'])) 
            {
                 $file=explode('/',$store[0]['student_file']); 
                 if(file_exists('./assets/question-store/'.$file[2]))
                 {
                     $chapter =  $StudentClass->getInfo('tbl_question_store_chapter', 'id',$store[0]['chapter']);
                     $url = $store[0]['student_file'];
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

    public function st_preview_memorization_pattern_one_try()
    {
        $TutorClass=new \TutorClass();
        $data = array();
        $all_check_hint = $this->request->getVar('all_check_hint');
        $question_id = $this->request->getVar('question_id');
        $correctAnswerStd = $this->request->getVar('correctAnswer');
        $question_info = $TutorClass->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        $correctAnswer = explode(",", $correctAnswerStd);
        $show_data_array = $this->memorization_hide_data($question_name);
        $left_memorize_p_one = $question_name->left_memorize_p_one;
        $left_memorize_p_one = array_map('strtolower', $left_memorize_p_one);
        $show_correct_ans = array();
        $show_error_ans = array();

        foreach ($correctAnswer as $key => $item) {
            if ($item == 1) {
                $show_correct_ans[] = $left_memorize_p_one[$key];
            } else {
                $show_correct_ans[] = '';
            }
        }
        $data['show_data_array'] = $show_data_array;
        if ($all_check_hint == 1) {
            foreach ($correctAnswer as $key => $item) {
                if ($item != 1) {
                    $show_error_ans[] = $left_memorize_p_one[$key];
                } else {
                    $show_error_ans[] = '';
                }
            }
            $data['show_data_array'] = $show_error_ans;
            $data['all_check_hint'] = 1;
        }

        $data['show_correct_ans'] = $show_correct_ans;

        echo json_encode($data);
    }

    
    public function get_ref_link()
    {
        $StudentClass=new \StudentClass();
        $user_type = $this->request->getVar('user_type');
        $st_id = $this->session->get('user_id');
        $enrollment_info = $StudentClass->get_sct_enrollment_info($st_id, $user_type);
        echo json_encode($enrollment_info);
    }

 public function save_ref_link()
    {
        error_report_check();
        $StudentClass=new \StudentClass();
        $data_link = $this->request->getVar('link');
	 
        if (!empty($data_link)) {
            $userType = $this->request->getVar('userType');
            $j = 0;
            foreach ($data_link as $single_link) {
                if ($single_link) {
                    $get_link_validate = $StudentClass->getLinkInfo('tbl_useraccount', 'SCT_link', 'user_type', $single_link, $userType);
                    if (!$get_link_validate) {
                        $j++;
                    }
                }
            }

            if ($j > 0) {
                echo 2;
            } else {
                //                $this->Student_model->delete_enrollment($userType, $this->session->userdata('user_id'));

                foreach ($data_link as $single_link) {
                    $get_link_status = $StudentClass->getInfo('tbl_useraccount', 'SCT_link', $single_link);
                    //                    $get_link_status = $this->Student_model->getLinkInfo('tbl_useraccount', 'SCT_link', 'user_type', $single_link, $userType);
				   //echo '<pre>';
						//print_r($get_link_status);
						//die();
                    if ($get_link_status) {
                        foreach ($get_link_status as $row) {
                            $enrollment_info = $StudentClass->getLinkInfo('tbl_enrollment', 'sct_id', 'st_id', $row['id'], $this->session->get('user_id'));
                            if (!$enrollment_info) {
                                $link['sct_id'] = $row['id'];
                                $link['sct_type'] = $row['user_type'];
                                $link['st_id'] = $this->session->get('user_id');
                                $StudentClass->insertInfo('tbl_enrollment', $link);
                            }

                            $checkCommission = $StudentClass->getLinkInfo('tbl_tutor_commisions', 'tutorId', 'student_id', $row['id'], $this->session->get('user_id'));
                            $tutorId = $row['id'];
                            $tudorDetails = $this->db->table('tbl_useraccount')->where('id', $tutorId)->get()->getRow();
                            $parentID = $tudorDetails->parent_id;
                            $userType = $tudorDetails->user_type;
                            $school_tutor = 0;
                            if ($parentID != null) {
                                $parentDetails = $this->db->table('tbl_useraccount')->where('id', $parentID)->get()->getRow();
                                $parentuserType = $parentDetails->user_type;
                                if ($parentuserType == 4) {
                                    $school_tutor = 1;
                                }
                            }
                            if (!$checkCommission && $userType == 3 && $school_tutor == 0) {
                                $data['tutorId'] = $row['id'];
                                $data['amount']  = 10;
                                $data['date']  = date('Y-m-d');
                                $data['student_id'] = $this->session->get('user_id');
                                $StudentClass->insertInfo('tbl_tutor_commisions', $data);
                            }
                        }
                    }
                }

                echo 1;
            }
        } else {
            echo 0;
        }
    }
    public function removeRefLink()
    {
        $StudentClass=new \StudentClass();
        $post = $this->request->getVar();
        $ref = $post['sct_link'];
        // $tutorInfo = $this->Student_model->search('tbl_useraccount', ['sct_link'=>$ref]);
        // if (!isset($tutorInfo[0]['id'])) {
        //     echo 'Tutor not exists';
        //     return 0;
        // }
        // $tutorId = $tutorInfo[0]['id'];

        $conditions = [
            'st_id' => $this->loggedUserId,
            'sct_id' => $ref,
        ];
        $StudentClass->delete('tbl_enrollment', $conditions);
        echo $this->db->getLastQuery();

    }

    public function preview_memorization_pattern_one_try()
    {
        $TutorClass=new \TutorClass();
        $data = array();
        $all_check_hint = $this->request->getVar('all_check_hint');
        $question_id = $this->request->getVar('question_id');
        $correctAnswerStd = $this->request->getVar('correctAnswer');
        $question_info = $TutorClass->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        $correctAnswer = explode(",", $correctAnswerStd);
        $show_data_array = $this->memorization_hide_data($question_name);
        $left_memorize_p_one = $question_name->left_memorize_p_one;
        $left_memorize_p_one = array_map('strtolower', $left_memorize_p_one);
        $show_correct_ans = array();
        $show_error_ans = array();

        foreach ($correctAnswer as $key => $item) {
            if ($item == 1) {
                $show_correct_ans[] = $left_memorize_p_one[$key];
            } else {
                $show_correct_ans[] = '';
            }
        }
        $data['show_data_array'] = $show_data_array;
        if ($all_check_hint == 1) {
            foreach ($correctAnswer as $key => $item) {
                if ($item != 1) {
                    $show_error_ans[] = $left_memorize_p_one[$key];
                } else {
                    $show_error_ans[] = '';
                }
            }
            $data['show_data_array'] = $show_error_ans;
            $data['all_check_hint'] = 1;
        }

        $data['show_correct_ans'] = $show_correct_ans;

        echo json_encode($data);
    }
	
	 public function AssignModuleTutuorTutorial()
    {
        $ModuleClass=new \ModuleClass();
        $data = $ModuleClass->AssignModuleTutuorTutorial($_POST['tutorId'], $this->session->get('user_id'), $_POST['moduleType']);
        if (count($data)) {
            echo 1;
        } else {
            echo "no module found";
        }
    }
	
	
     public function AssignModuleSchoolTutuorTutorial()
    {
        //$query = $this->db->where('student_id',$this->session->userdata('user_id'))
        //->where('moduletype',$_POST['moduleType'])->get('tbl_studentprogress')->result();
        //echo '<pre>';print_r($query);die();
        $ModuleClass=new \ModuleClass();
        $data = $ModuleClass->AssignModuleSchoolTutuorTutorial($_POST['tutorId'], $this->session->get('user_id'), $_POST['moduleType']);
        // echo '<pre>';print_r($data);die();
        if (count($data)) {
            $i = '';
            $j = 0;
            foreach ($data as $key => $value) {
                $md_id =  $value['id'];
                $query = $this->db->table('tbl_studentprogress')->where('student_id', $this->session->get('user_id'))->where('moduletype', $_POST['moduleType'])->where('module', $md_id)->get()
                    ->getResult();

                if (count($query)) {
                    $i = "no module found";
                } else {
                    $j = 1;
                }
            }

            if ($j == 1) {
                echo 1;
                die();
            } else {
                echo "no module found";
            }
        } else {
            echo "no module found";
        }
        //echo '<pre>';print_r($data);die();

    }
	
	 public function q_study_course()
    {
        error_report_check();
        $StudentClass=new \StudentClass();
        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));

        $parent_detail = getParentIDPaymetStatus($data['user_info'][0]['parent_id']);

        if ($parent_detail[0]['subscription_type'] == "direct_deposite") {
            if ($parent_detail[0]['direct_deposite'] == 0) {
                return redirect()->to(base_url($_SERVER['HTTP_REFERER']));
            }
        }

        $data['tutor_type'] = 7;
        return view('students/student_course/q_study_course',$data);
    }

    public function tutor_course()
    {
        error_report_check();
        $StudentClass=new \StudentClass();
        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));

        $parent_detail = getParentIDPaymetStatus($data['user_info'][0]['parent_id']);

        if ($parent_detail[0]['subscription_type'] == "direct_deposite") {
            if ($parent_detail[0]['direct_deposite'] == 0) {
                return redirect()->to(base_url($_SERVER['HTTP_REFERER']));
            }
        }

        $data['tutor_type'] = 3;

        if ($_SESSION['userType'] == 2) {
            $data['tutor_list'] = 1;
        }

        return view('students/student_course/q_study_course', $data);
    }
}
