<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TblUserAccountModel;
use App\Models\TblSetting;
use App\Models\DirectDepositeAdminSettingModel;
use App\Models\TblReferralUserModel;
use App\Libraries\FaqClass;

class RegisterController extends BaseController
{
    public function __construct()
    {
       helper('text');
       helper('commonmethods_helper'); 
    }

    public function showSignUp()
    {
        
     
        //echo $this->request->uri->getSegments(1);die();
        //echo $uri->getSegment(1);die();
        $FaqClass = new \FaqClass();
        $RegisterClass = new \RegisterClass();

        $this->session->destroy();
        $data['registration_slug_type']=$this->request->uri->getSegment(1);;


        $data['user']=$RegisterClass->getUserType();
        $data['back_url'] = '/';

        if (strpos(current_url(), 'trial') == false) { 

            $data['video_help'] = $FaqClass->videoSerialize(1, 'video_helps');
            $data['video_help_serial'] = 1;
        } 
        else { 
            $data['video_help'] = $FaqClass->videoSerialize(5, 'video_helps');
            $data['video_help_serial'] = 5;
        } 

        return view('registration/signup',$data);
    }

    public function selectCountry($registrationType,$userType)
    {
        
        $RegisterClass = new \RegisterClass();

        $_SESSION['userType'] = $userType;
        $_SESSION['registrationType'] = $registrationType;

       

        if (!$this->session->get('userType')) {
            redirect('/signup');
        }
        else 
        {
            $data['country_db']=$RegisterClass->getCountry();
            $registrationType = $this->session->get('registrationType');
            $userType = $this->session->get('userType');
            if ($userType == 1) {
                $user = 'parent';
            } elseif ($userType == 2) {
                $user = 'upper_level_student';
            } elseif ($userType == 3) {
                $user = 'tutor';
            } elseif ($userType == 4) {
                $user = 'school';
            } elseif ($userType == 5) {
                $user = 'corporate';
            }
            //echo $_SERVER['HTTP_REFERER'];die();

            if ($_SERVER['HTTP_REFERER'] == base_url('/signup')) 
            {
                $data['back_url'] = $_SERVER['HTTP_REFERER'];
            }
            else
            {
                $data['back_url'] = base_url('/signup');
            }
            
           
            return view('registration/select_country',$data);
        }
    }

    public function selectCourse()
    {
       
        error_reporting(0);
        $FaqClass = new \FaqClass();
        $RegisterClass = new \RegisterClass();

        if ($this->request->getVar('submit')  == "submit")
        {
           
            $user_id = $this->session->get('user_id');
        
            $course_data['courses'] =$this->request->getVar('course');
            $course_data['totalCost'] =$this->request->getVar('totalCost');
            $course_data['token'] =$this->request->getVar('token');
            $course_data['paymentType'] = $this->request->getVar('paymentType'); 
            $course_data['children'] = $this->request->getVar('children');

            if (!empty($this->request->getVar('direct_debit'))) {
                $course_data['payment_process'] = $this->request->getVar('direct_debit');
            }
            if (!empty($this->request->getVar('no_direct_debit'))) {
                $course_data['payment_process'] = $this->request->getVar('no_direct_debit');
            }
            if (!empty($this->request->getVar('direct_deposit'))) {
                $course_data['payment_process'] = $this->request->getVar('direct_deposit');
            }
            
            
            // echo "<pre>";print_r($course_data);die();
            // $tbl_payment = $this->db->where('user_id',$user_id)->order_by('id','desc')->limit(1)->get('tbl_payment')->row();
            // if($tbl_payment){
            //     $startDate = $tbl_payment->PaymentDate;
            //     $endDate   = $tbl_payment->PaymentEndDate;
            //     $total_cost   = $tbl_payment->total_cost;
            //     $today = time();
            //     if($endDate > $today){
            //         $diff = $endDate - $startDate;
            //         $remainingDiff = $endDate - $today;
            //         $totalDay = floor($diff/(60*60*24));
            //         $remainingDays = floor($remainingDiff/(60*60*24));
                    
            //         $perDayCost = $total_cost/$totalDay;
            //         $remainingCost = $remainingDays * $perDayCost;
            //         if($remainingCost > $this->input->post('totalCost')){
            //             $course_data['totalCost'] = 0;
            //         }else{
            //             $cost = $this->input->post('totalCost') - $remainingCost;
            //             $course_data['totalCost'] = $cost;
                        
            //         }
                    
            //     }
                
            //     $register_courses = $this->db->where('user_id',$user_id)->where('cost <>',0)->get('tbl_registered_course')->result_array();
            //     $registerCourse = [];
            //     foreach($register_courses as $key => $course){
            //         $registerCourse[$key] = $course['course_id'];
            //     }
                
            // }
            
            
            // echo $this->session->userdata('registrationType');
            // echo "<br>";
            // echo $this->session->userdata('userType');
            //echo $this->session->get('userType');die();
            
            $this->session->set($course_data);

            if ($this->session->get('registrationType') == 'trial') {
                if ($this->session->get('userType')==1) {
                    return redirect()->to(base_url('/student_form'));
                }elseif ($this->session->get('userType')==6) {
                    return redirect()->to(base_url('/student_form'));
                } elseif ($this->session->get('userType')==2) {
                    return redirect()->to(base_url('/upper_level_student_form'));
                } elseif ($this->session->get('userType')==3) {
                    return redirect()->to(base_url('/tutor_form'));
                } elseif ($this->session->get('userType')==4) {
                    return redirect()->to(base_url('/school_form'));
                } elseif ($this->session->get('userType')==5) {
                    $this->session->set('teacher',$_POST['teacher']);
                    return redirect()->to(base_url('/corporate_form'));
                }
            }else{
                if ($course_data['payment_process'] == 3) {
                    return redirect()->to(base_url('direct_deposit'));                    
                }else{
                    return redirect()->to(base_url('paypal'));  
                }
            }
        }
       
        if ( !empty($_SESSION['registrationType']) && $_SESSION['registrationType'] == "trial") 
        { 
            $data['video_help'] = $FaqClass->videoSerialize(6, 'video_helps');
            $data['video_help_serial'] = 6;
        }
        else
        {
            $data['video_help'] = $FaqClass->videoSerialize(2, 'video_helps');
            $data['video_help_serial'] = 2;
        }

        if (($_SERVER['HTTP_REFERER'] == base_url('/corporate_form') ) || ( $_SERVER['HTTP_REFERER'] == base_url('/tutor_form') ) || ($_SERVER['HTTP_REFERER'] == base_url('/school_form') ) || ($_SERVER['HTTP_REFERER'] == base_url('/student_form') ) || ($_SERVER['HTTP_REFERER'] == base_url('/upper_level_student_form') ) || ($_SERVER['HTTP_REFERER'] == base_url('/student_form') ) ) 
        {
            $data['back_url'] =  $this->session->get('back_urlRegistration');
        }else{
      
            $data['back_url'] = $_SERVER['HTTP_REFERER'];
            $this->session->set('back_urlRegistration', $_SERVER['HTTP_REFERER']);
        }
    
        $user_id = $this->session->get('user_id');
        $TblUserAccountModel=new TblUserAccountModel();
        $TblSetting=new TblSetting();
        $DirectDepositeAdminSettingModel=new DirectDepositeAdminSettingModel();
        $TblReferralUserModel=new TblReferralUserModel();

        $countryIdd = $TblUserAccountModel->select('country_id')->where('id',$user_id)->first();
        $countryIdd =$countryIdd->country_id;

        if (isset($_POST['country'])) {
            $countryIdd = $_POST['country'];
        }
        
        if (isset($countryIdd)) {
            // echo $countryIdd;
            // $countryIdd =$this->session->userdata('countryId');
            if (isset($_POST['country'])) {
                $countryIdd = $_POST['country'];
            }
           
            $this->session->set('countryId', $countryIdd);
            $user_id = $this->session->get('user_id');
            // $subscription_type = ($this->session->userdata('registrationType') == 'trial' ? 2 : 1);
            $data['subscription_type'] = ($this->session->get('registrationType') == 'trial' ? 2 : 1);
            if(!empty($user_id)){
                $data['subscription_type'] = ($this->session->get('registrationType') == 'trial' ? 1 : 1);
                $this->session->set('registrationType', '');
            }
            $tbl_setting = $TblSetting->where('setting_key','days')->first();
           
            $duration = $tbl_setting->setting_value;
            $date = date('Y-m-d');
            $d1  = date('Y-m-d', strtotime('-'.$duration.' days', strtotime($date)));
            $trialEndDate = strtotime($d1);
            //if($this->session->userdata('userType') != 4 && $this->session->userdata('userType') != 5)
            //if ($this->session->userdata('userType') != 4 && $this->session->userdata('userType') != 5) {
            // $data['course_details'] = $this->RegisterModel->getCourse($this->session->userdata('userType'), $countryIdd, $subscription_type);
            
           
            //added AS 
            if ($this->session->get('userType') == 6) {

                $this->session->set('userType', 1);
            }
            
            //echo '1021'.$this->session->get('userType');die();
            $data['course_details'] = $RegisterClass->getCourse($this->session->get('userType'), $countryIdd);
            // echo '<pre>';
            // print_r($data['course_details']);die();
           
            $builder = $this->db->table('tbl_registered_course');
            $builder->where('user_id',$user_id);
            $builder->where('cost <>',0);
            $builder->where('endTime >',time());
            $query = $builder->get();
            $register_courses=$query->getResultArray();
     
            $registerCourse = [];
            foreach($register_courses as $key => $course){
                $registerCourse[$key] = $course['course_id'];
            }
           
            $data['register_course'] = $registerCourse;
            if (!$data['course_details'] && $this->session->get('userType') != 4 && $this->session->get('userType') != 5) {
                
                $this->session->set('country_error', 'Actually we have no service for this country that you select. Please select another country');
                //redirect('/select_country');
               //echo 'hiii akhne asece';die();
                return redirect()->to(base_url('select_country')); 
            }
            //}
            $checkDepositDetails = $DirectDepositeAdminSettingModel->where('country_id',$countryIdd)->first();
      
            if(isset($checkDepositDetails)){
                $data['direct_deposit_by_contry'] = $checkDepositDetails->active_status;
            }
            // $data['back_url'] = base_url().'select_country';
            $data['refferalUser'] = $TblReferralUserModel->where('user_id',$user_id)->where('status',0)->first();
          
  
            if ($this->session->get('userType')==1) {
                return view('registration/select_course', $data);
            }elseif ($this->session->get('userType')==6) {
                return view('registration/select_course', $data);
            } elseif ($this->session->get('userType')==2) {
                return view('registration/select_course_for_upper_level', $data);
            } elseif ($this->session->get('userType')==3) {
                // echo "<pre>";print_r($data);die();
                return view('registration/select_course_for_tutor', $data);
            } elseif ($this->session->get('userType')==4) {
                return view('registration/select_course_for_school', $data);
            } elseif ($this->session->get('userType')==5) { 

                return view('registration/select_course_for_corporate', $data);
            } 
        } else {
            return redirect()->to(base_url('/signup'));
        }
        
    }

    public function school_form()
    {
        // echo 'asce re';
        // echo '<pre>';
        // print_r($_POST);
        // die();
        error_reporting(0);
        $FaqClass = new \FaqClass();
        $RegisterClass = new \RegisterClass();

        if ($_SESSION['registrationType'] == "trial") {
            $data['video_help'] = $FaqClass->videoSerialize(7, 'video_helps');
            $data['video_help_serial'] = 7;
        }else{
            $data['video_help'] = $FaqClass->videoSerialize(3, 'video_helps');
            $data['video_help_serial'] = 3;
        }
        //echo $this->session->get('teacher_number');
      
        if (isset($_POST['teacher']) || $this->session->get('teacher_number')) {
            $data['back_url'] = base_url().'/select_country/signup/4';
            if (isset($_POST['teacher'])){
                //$this->form_validation->set_rules('teacher', 'teacher', 'callback_teacher_number_check');
                $check='1234';
                if ($check=='123') 
                {
                    $this->session->set_userdata('teacher_number_error', 'Number of teacher can not be less than 1');
                    redirect('/select_course');
                } else {
                    $this->session->set('teacher_number', $_POST['teacher']);
                    if (isset($_POST['paymentType'])) {
                        $this->session->set('paymentType', $_POST['paymentType']);
                        $this->session->set('totalCost', $_POST['totalCost']);
                    }
                    $data['teacher_number']=$_POST['teacher'];
                    $data['country_db']=$RegisterClass->getSpecificCountry($this->session->get('countryId'));

                    return view('registration/school_form', $data);
                }
            } else {
                if (isset($_POST['paymentType'])) {
                    $this->session->set('paymentType', $_POST['paymentType']);
                    $this->session->set('totalCost', $_POST['totalCost']);
                }
                $data['country_db']=$RegisterClass->getSpecificCountry($this->session->get('countryId'));
                $data['teacher_number']=$this->session->get('teacher_number');
  
                return view('registration/school_form',$data);
            }
        } else {
            return redirect()->to(base_url('/signup'));
        }
    }

    public function save_school()
    {
        
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass = new \RegisterClass();
        $error=[];
        if (!$this->validate('schollValidate')) 
        {
            $validation = $this->validator;
     
            $error=$validation->getErrors();
            //return $this->response->setJSON($error);
        }

        $teacher = $this->request->getVar('teacher');  
        $password_teacher = $this->request->getVar('password_teacher');
        $confirm_password_teacher = $this->request->getVar('confirm_password_teacher');
      
        
        $mobileExists =$StudentClass->getInfo('tbl_useraccount', 'user_mobile', $_POST['full_number']);
   
        
        if (count($mobileExists)) {
           echo json_encode("mobile_number_error");
           exit();
        }
    
        $flag = 0;
   
        if ($error)
        {
            $error=$error;
            $flag++;
        }
        
        if ($this->myValidation($teacher) == false) {
            $error['teacher_error']= 'teacher name can not be blank';
            $flag++;
        }
      
        if ($this->checkPasswordConfirmPassword($password_teacher,$confirm_password_teacher) == false) {
          
            $error['confirm_password']= 'confirm_password_teacher error';
            $flag++;
        }
   
        if ($flag > 0) {
            foreach($error as $errors)
            {
                $errot_new[]=$errors.'<br>';
            
            }
         
            echo json_encode($errot_new);
            exit;
        }
     
        $data['number'] = rand(10000, 99999);
        $this->session->set('random_number',$data['number']);
        $RegisterClass->save_random_digit($data);

        $this->session->set('school_name', $_POST['school_name']);
        $this->session->set('email', $_POST['email']);
        $this->session->set('password', $_POST['password']);
        $this->session->set('full_number', $_POST['full_number']);
        $this->session->set('website', $_POST['website']);
        $this->session->set('user_mobile', $_POST['full_number']);
        $this->session->set('user_phone', $_POST['phone']);
        //$this->session->set_userdata('mobile',$_POST['mobile']);
        $rs_data = array();
        for ($i = 0; $i < count($_POST['teacher']); $i++) {
            $data_std['name'] = $_POST['teacher'][$i];
            $data_std['user_pawd'] = $_POST['password_teacher'][$i];
            $rs_data[] = $data_std;
        }
        $this->session->set('teachers', $rs_data);
      


        $data['number'] = rand(10000, 99999);
        // $content = urlencode("Q-Study Registration Code: ".$data['number']);
        // $url = "https://platform.clickatell.com/messages/http/send?apiKey=iyypKonpQNOHUBMv4wngVA==&to=" . $_POST['full_number'] . "&content=$content";
        $settins_Api_key=$AdminClass->getSmsApiKeySettings();
        $settins_sms_messsage=$AdminClass->getSmsMessageSettings();

    
        $register_code_string = $settins_sms_messsage[0]['setting_value'];
        $find = array("{{register_code}}");
        $replace = array($data['number']);
        $message = str_replace($find, $replace, $register_code_string);

        $api_key = $settins_Api_key[0]['setting_value'];
        $content = urlencode($message);

      
        //echo '<pre>';print_r($_POST);echo $_POST['full_number'];die;
        $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $_POST['full_number'] . "&content=$content";

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
       
        $send_msg_status = json_decode($result);
        if(isset($send_msg_status))
        {
            if (count($send_msg_status->messages) > 0 && $send_msg_status->messages[0]->accepted == 1) {
           
                $this->session->set('random_number', $data['number']);
            }
          
        }

        $RegisterClass->save_random_digit($data);

        echo json_encode('success');
    }


    public function student_form()
    {
        error_reporting(0);
        $FaqClass = new \FaqClass();
        $RegisterClass = new \RegisterClass();

        if (!empty($_SESSION['trail_suspend']) && $_SESSION['trail_suspend'] == 1 ) {
            return redirect()->to(base_url('/paypal'));
        }
        if ($_SESSION['registrationType'] == "trial") {
            $data['video_help'] = $FaqClass->videoSerialize(7, 'video_helps');
            $data['video_help_serial'] = 7;
        }else{
            $data['video_help'] = $FaqClass->videoSerialize(3, 'video_helps');
            $data['video_help_serial'] = 3;
        }

        if (isset($_POST['token'])) {
            if ($this->session->get('registrationType') !='trial') {
                //$this->validate_student_course_signup();
            } else {
                //$this->validate_student_course_trial();
            }
        }
        if ($this->session->get('userType')==1 || $this->session->get('userType')==6) {
            $data['back_url'] = base_url().'/redirect_url';
            if (isset($_POST['children']) || $this->session->get('childrens') || $this->session->get('children')) {
                $children =$this->session->get('childrens');
                if (empty($children)) {
                   $children =$this->session->get('children'); 
                }
                if (isset($_POST['children']) && $_POST['children'] && $_POST['course']) {
                    $children = $_POST['children'];
                    if ($children < 1) {
                        return redirect()->to(base_url('/signup'));
                    }
                    $this->session->set('childrens', $children);
                    $this->session->set('courses', $_POST['course']);
                    if ($this->session->get('registrationType') != 'trial') {
                        $this->session->set('paymentType', $_POST['paymentType']);
                        $this->session->set('totalCost', $_POST['totalCost']);
                    }
                }
                if ($children) {
                    $data['country_db']=$RegisterClass->getSpecificCountry($this->session->get('countryId'));
                    $data['chil_number']=$children;
                    return view('registration/student_form',$data);
                }
            } else {
                return redirect()->to(base_url('/signup'));
            }
        } else {
            return redirect()->to(base_url('/signup'));
        }
    }

    private function validate_upper_student_course_signup()
    {
        $input = $this->validate([
            'paymentType' => 'required',
            'totalCost' => 'required',
        ]);
      
        $flag=0;
        $error='';
        if (!$input){
            $error.= $this->validator;
            $flag++;
        }
        
        $course = $this->input->post('course');
        if (!$course) {
            $error.= '<p>At least Select One course</p>';
            $flag++;
        }
        if ($flag > 0) {
            return redirect()->to(base_url('/select_course'));
            exit;
        } else {
            return true;
        }
    }
    
    private function validate_upper_student_course_trial()
    {
        $flag=0;
        $error='';
        $course = $this->request->getVar('course');
        if (!$course) {
            $error.= '<p>At least Select One course</p>';
            $flag++;
        }
        if ($flag > 0) {
            return redirect()->to(base_url('/select_course'));
            exit;
        } else {
            return true;
        }
    }

    public function upper_level_student_form()
    {
        // echo '<pre>';
        // print_r($_SESSION);
        // die();
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();
        $FaqClass = new \FaqClass();

        if (!empty($_SESSION['trail_suspend']) && $_SESSION['trail_suspend'] == 1 ) {
            return redirect()->to(base_url('/paypal'));
        }
        if ($_SESSION['registrationType'] == "trial") {
            $data['video_help']=$FaqClass->videoSerialize(7, 'video_helps');
            $data['video_help_serial'] = 7;
        }else{
            $data['video_help']=$FaqClass->videoSerialize(3, 'video_helps');
            $data['video_help_serial'] = 3;
        }

        if (isset($_POST['token'])) {
            if ($this->session->get('registrationType') !='trial') {
                $this->validate_upper_student_course_signup();
            } else {
                $this->validate_upper_student_course_trial();
            }
        }
        if ($this->session->get('userType')==2) {
            if (isset($_POST['paymentType']) || $this->session->get('paymentType') || $this->session->get('registrationType') == 'trial') {
                $data['back_url'] = base_url().'/redirect_url';
                if (isset($_POST['paymentType'])) {
                    $this->session->set('courses', $_POST['course']);
                    $this->session->set('paymentType', $_POST['paymentType']);
                    $this->session->set('totalCost', $_POST['totalCost']);
                }
                if ($this->session->get('registrationType') == 'trial' && isset($_POST['course'])) {
                    $this->session->set('courses', $_POST['course']);
                }
                $data['country_db']=$RegisterClass->getSpecificCountry($this->session->get('countryId'));
                return view('registration/upper_level_student_form', $data);
            } else {
                return redirect()->to(base_url('/signup'));
            }
        } else {
            return redirect()->to(base_url('/signup'));
        }
    }

    public function tutor_form()
    {
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();
        $FaqClass = new \FaqClass();

        if (!empty($_SESSION['trail_suspend']) && $_SESSION['trail_suspend'] == 1 ) {
            return redirect()->to(base_url('/paypal'));
        }

        if (  !empty($_SESSION['registrationType']) && $_SESSION['registrationType'] == "trial" ) {
            $data['video_help'] = $FaqClass->videoSerialize(7, 'video_helps');
            $data['video_help_serial'] = 7;
        }else{
            $data['video_help'] = $FaqClass->videoSerialize(3, 'video_helps');
            $data['video_help_serial'] = 3;
        }
        if ($this->session->get('userType') == 3) {
            // if (isset($_POST['paymentType']) || $this->session->userdata('paymentType')) {
            $data['back_url'] = base_url().'/redirect_url';
            // echo "string";die;
            if (isset($_POST['paymentType'])) 
            {
                $this->session->set('paymentType', $_POST['paymentType']);
                $this->session->set('totalCost', $_POST['totalCost']);
            }
            if (isset($_POST['course']))
            {
                $this->session->set('tutor_course', $_POST['course']);
            }

            $data['country_db']=$RegisterClass->getSpecificCountry($this->session->get('countryId'));
            return view('registration/tutor_form',$data);
            // }
        } else {
            return redirect()->to(base_url('/signup'));
        }
    }

	  public function save_tutor()
    {
     
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();
        $FaqClass = new \FaqClass();
      
        $error=[];
        if (!$this->validate('tutorValidate')) 
        {
            $validation = $this->validator;
     
            $error=$validation->getErrors();
            //return $this->response->setJSON($error);
        }

        $student = $this->request->getVar('student');
        $mobile = $this->request->getVar('mobile');

        // echo '<pre>';print_r($this->session->userdata('courses'));die;
     
        $mobileExists = $AdminClass->getInfo('tbl_useraccount', 'user_mobile', $_POST['full_number']);
        if (count($mobileExists)) {
           echo json_encode("mobile_number_error");
           exit();
        }

        
        $flag = 0;
        if ($error)
        {
            $error=$error;
            $flag++;
        }

        if ($flag > 0) {
            foreach($error as $errors)
            {
                $errot_new[]=$errors.'<br>';
            
            }
         
            echo json_encode($errot_new);
            exit;
        }

        $this->session->set('tutor_name', $_POST['tutor_name']);
        $this->session->set('email', $_POST['email']);
        $this->session->set('password', $_POST['password']);
        $this->session->set('user_mobile', $_POST['full_number']);
        $data['number'] = rand(10000, 99999);
        // $content = urlencode("Q-Study Registration Code: ".$data['number']);
        // $url = "https://platform.clickatell.com/messages/http/send?apiKey=iyypKonpQNOHUBMv4wngVA==&to=" . $_POST['full_number'] . "&content=$content";
        $settins_Api_key = $AdminClass->getSmsApiKeySettings();
        $settins_sms_messsage =$AdminClass->getSmsMessageSettings();

        $register_code_string = $settins_sms_messsage[0]['setting_value'];
        $find = array("{{register_code}}");
        $replace = array($data['number']);
        $message = str_replace($find, $replace, $register_code_string);

        $api_key = $settins_Api_key[0]['setting_value'];
        $content = urlencode($message);


        //echo '<pre>';print_r($_POST);echo $_POST['full_number'];die;
        $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $_POST['full_number'] . "&content=$content";

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
        // print_r($result);die;
        $send_msg_status = json_decode($result);

        $this->session->set('random_number', $data['number']);
        if(isset($send_msg_status))
        {
            if (count($send_msg_status->messages) > 0 && $send_msg_status->messages[0]->accepted == 1) {
           
                $this->session->set('random_number', $data['number']);
            }
        }
        $RegisterClass->save_random_digit($data);

        echo json_encode('success');
    }
	
    public function sure_save_tutor()
    {
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();
        $FaqClass = new \FaqClass();
        //print_r($this->session->get('random_number')); die();
        if ($_POST['random']==$this->session->get('random_number')) { 

            $data['subscription_type'] = $this->session->get('registrationType');
            $data['user_type'] = $this->session->get('userType');
            $data['country_id'] = $this->session->get('countryId');
            $data['name'] = $this->session->get('tutor_name');
            $data['user_email'] = $this->session->get('email');
            $data['user_pawd'] = md5($this->session->get('password'));
            $data['user_mobile'] = $this->session->get('user_mobile');
            $data['SCT_link'] = $this->randomString();
            $data['created'] = time();
            // echo '<pre>';
            // print_r($data);
            // die();
            $tutor_id = $RegisterClass->saveUser($data);
            // $tutor_course = $this->session->userdata('tutor_course');
            $tutor_course = $this->session->get('courses');
            //echo "<pre>";print_r($tutor_course);die();
            $additionalTableData = array();
            $additionalTableData['tutor_id'] = $tutor_id;
            $additionalTableData['created_at'] = date('Y-m-d h:i:s');
            $additionalTableData['updated_at'] = date('Y-m-d h:i:s');
            $RegisterClass->basicInsert('additional_tutor_info', $additionalTableData);
            if (count($tutor_course)){
                $courseUserMap = [];
                foreach ($tutor_course as $course) {
                    $course_info = $RegisterClass->getInfo('tbl_course', 'id', $course);
                    $courseUserMap = [
                        'course_id' => $course,
                        'user_id' => $tutor_id,
                        'created' => time(),
                        'cost'    => $course_info[0]['courseCost'],
                    ];
                    $RegisterClass->basicInsert('tbl_registered_course', $courseUserMap);
                }
            }

            $this->session->set('user_id', $tutor_id);
            $this->session->set('SCT_link', $data['SCT_link'] );
            $this->session->set('courseName', 'You paid as a tutor');


            //username and password send

            $settins_sms_status   = $AdminClass->getSmsType("Template Activate Status");

            if ($settins_sms_status[0]['setting_value'] ) {

                $settins_Api_key = $AdminClass->getSmsApiKeySettings();
                $settins_sms_messsage = $AdminClass->getSmsType("Tutor Registration");

                $register_code_string = $settins_sms_messsage[0]['setting_value'];
                $message = str_replace( "{{ username }}" , $this->session->get('email') , $register_code_string);
                $message = str_replace( "{{ password }}" , ($this->session->get('password')) , $message);

                $api_key = $settins_Api_key[0]['setting_value'];
                $content = urlencode($message);

                $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $this->session->get('user_mobile') . "&content=$content";

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

            }

            if ($this->session->get('registrationType') != 'trial') {
                echo "1";
                
            } else {
                $this->tutor_mailTemplate($this->session->get('tutor_name'), $this->session->get('email'), $this->session->get('password'), $data['SCT_link']);
                echo "2";
            }


        }else{
            echo 0;
        }
    }
    function tutor_mailTemplate($tutorName, $tutorEmail, $tutorPassword, $SCT_link)
    {
        $RegisterClass=new  \RegisterClass();
        $template = $RegisterClass->getInfo('table_email_template', 'email_template_type', $this->session->get('userType'));
        
        if ($template) {
            $subject = $template[0]['email_template_subject']; //->email_template_subject;
            $template_message = $template[0]['email_template']; //->email_template;
            
            $find = array("{{tutorName}}","{{tutor_email}}","{{tutor_password}}","{{tutor_sct_link}}");
            $replace = array($tutorName,$tutorEmail,$tutorPassword,$SCT_link);
            $message = str_replace($find, $replace, $template_message);
            $mail_data['to'] = $tutorEmail;
            $mail_data['subject'] = $template[0]['email_template_subject'];
            ;
            $mail_data['message'] = $message;
            
            $this->sendEmail($mail_data);
        }
        return true;
    }

    public function corporate_form()
    {
        
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();
        $FaqClass = new \FaqClass();

        if ($_SESSION['registrationType'] == "trial") {
            $data['video_help'] = $FaqClass->videoSerialize(7, 'video_helps');
            $data['video_help_serial'] = 7;
        }else{
            $data['video_help'] = $FaqClass->videoSerialize(3, 'video_helps');
            $data['video_help_serial'] = 3;
        }

        if ($this->session->get('userType') == 5) {

            if (isset($_POST['course']))
            {
                $this->session->set('corporate_course', $_POST['course']);
            }
            // echo '<pre>';
            // print_r($_SESSION);
            // die();
            //|| $this->session->get('teacher')
            if (isset($_POST['teacher']) || $this->session->get('teacher')) {
                if (isset($_POST['paymentType'])) {
                    $this->session->set('paymentType', $_POST['paymentType']);
                    $this->session->set('totalCost', $_POST['totalCost']);
                }
                $data['back_url'] = base_url().'/select_country/signup/5';

                if (isset($_POST['teacher'])) {
                    $input = $this->validate([
                        'teacher' => 'required',
                    ]);
                    //$this->form_validation->set_rules('teacher', 'teacher', 'callback_teacher_number_check');
                    if (!$input) 
                    {
                        $this->session->set('teacher_number_error', 'Number of teacher can not be less than 1');
                        return redirect()->to(base_url('/select_course')); 
                    } 
                    else 
                    {
                        $this->session->set('teacher_number', $_POST['teacher']);
                        $data['teacher_number'] = $_POST['teacher'];
                        $data['country_db']=$RegisterClass->getSpecificCountry($this->session->get('countryId'));

                        return view('registration/corporate_form', $data);
                    }
                } else {
                    $data['country_db']=$RegisterClass->getSpecificCountry($this->session->get('countryId'));
                    $data['teacher_number']=$this->session->get('teacher_number');

                    return view('registration/corporate_form', $data);
                }
            } else {
                //echo 'jiiiii akhane kno asce';die();
                return redirect()->to(base_url('/signup')); 
            }
        } else {
            return redirect()->to(base_url('/signup')); 
        }
    }

    public function save_corporate()
    { 
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();
        $FaqClass = new \FaqClass();

        $error=[];
        if (!$this->validate('corporateValidate')) 
        {
            $validation = $this->validator;
     
            $error=$validation->getErrors();
            //return $this->response->setJSON($error);
        }


        $teacher = $this->request->getVar('teacher');
        // add
        $mobile = $this->request->getVar('mobile');
        $mobileExists = $AdminClass->getInfo('tbl_useraccount', 'user_mobile', $_POST['full_number']);
        if (count($mobileExists)) {
           echo json_encode("mobile_number_error");
           exit();
        }

        
        $flag = 0;
        if ($error) {
            $error=$error;
            $flag++;
        }
        // if ($this->myValidation($teacher) == false) {
        //     $error.= '<p>teacher name can not be blank</p>';
        //     $flag++;
        // }
        if ($flag > 0) {
            foreach($error as $errors)
            {
                $errot_new[]=$errors.'<br>';
            
            }
         
            echo json_encode($errot_new);
            exit;
        }
         $data['number'] = rand(10000, 99999);
         $this->session->set('random_number',$data['number']);
         $RegisterClass->save_random_digit($data);

        $this->session->set('corporate_name', $_POST['corporate_name']);
        $this->session->set('email', $_POST['email']);
        $this->session->set('password', $_POST['password']);

        $this->session->set('full_number', $_POST['full_number']);
        $this->session->set('website', $_POST['website']);
        $this->session->set('user_mobile', $_POST['full_number']);
        $this->session->set('user_phone', $_POST['phone']);
        //$this->session->set_userdata('mobile',$_POST['mobile']);
        // $rs_data = array();
        // for ($i = 0; $i < count($_POST['teacher']); $i++) {
        //     $data_std['name'] = $_POST['teacher'][$i];
        //     $rs_data[] = $data_std;
        // }
        // $this->session->set('teachers', $rs_data);

        //confirmation code sent

        $data['number'] = rand(10000, 99999);
        $settins_Api_key = $AdminClass->getSmsApiKeySettings();
        $settins_sms_messsage = $AdminClass->getSmsMessageSettings();

        $register_code_string = $settins_sms_messsage[0]['setting_value'];
        $find = array("{{register_code}}");
        $replace = array($data['number']);
        $message = str_replace($find, $replace, $register_code_string);

        $api_key = $settins_Api_key[0]['setting_value'];
        $content = urlencode($message);

        //echo '<pre>';print_r($_POST);echo $_POST['full_number'];die;
        $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $_POST['full_number'] . "&content=$content";

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
        // print_r($result);die;
        $send_msg_status = json_decode($result);
        $this->session->set('random_number', $data['number']);
        if(isset($send_msg_status))
        {
            if (count($send_msg_status->messages) > 0 && $send_msg_status->messages[0]->accepted == 1) {
                $this->session->set('random_number', $data['number']);
            }
        }    

        $RegisterClass->save_random_digit($data);

        echo json_encode('success');
    }
	
    public function sure_corporate_data_save()
    {
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();
        $FaqClass = new \FaqClass();

		//print_r($this->session->get('random_number'));die();
        if($_POST['random']==$this->session->get('random_number')){ 
           // $rs_teachers = $this->session->userdata('teachers');

            $data['children_number'] = $this->session->get('teacher_number');
            $data['subscription_type'] = $this->session->get('registrationType');
            $data['user_type'] = $this->session->get('userType');
            $data['country_id'] = $this->session->get('countryId');
            $data['name'] = $this->session->get('corporate_name');
            $data['user_email'] = $this->session->get('email');
            $data['user_pawd'] = md5($this->session->get('password'));

            $data['user_mobile'] = ($this->session->get('full_number'));
            $data['user_mobile'] = ($this->session->get('user_phone'));
            $data['website'] = ($this->session->get('website'));
            
            //$data['user_mobile']=$this->session->userdata('mobile');
            $data['SCT_link'] = $this->randomString();
            $data['created'] = time();
            $corporate_id = $RegisterClass->saveUser($data);
            
            // $teacher_list = array();
            // foreach ($rs_teachers as $singleTeacher) {
            //     $teacher_raw_data = array();
            //     $st['name'] = $singleTeacher['name'];
            //     $pieces = explode(" ", $st['name']);
            //     $random_number = rand(100, 999);
            //     $st['user_email'] = $pieces[0];
            //     $st['user_pawd'] = md5($pieces[0] . $random_number);
            //     $teacher_raw_data['teacher_user_name'] = $pieces[0];
            //     $teacher_raw_data['teacher_password'] = $pieces[0] . $random_number;
            //     $st['parent_id'] = $corporate_id;
            //     $st['country_id'] = $this->session->userdata('countryId');
            //     $st['user_type'] = 3;
            //     $st['SCT_link'] = $data['SCT_link'];
            //     $st['created'] = time();
                
            //     //rakesh corporate

            //     $tutor_id = $this->RegisterModel->saveUser($st);

            //     $tutor_course = $this->session->userdata('corporate_course');


            //     if (count($tutor_course)){
            //         $courseUserMap = [];
            //         foreach ($tutor_course as $course) {
            //             $course_info = $this->RegisterModel->getInfo('tbl_course', 'id', $course);
            //             $courseUserMap = [
            //                 'course_id' => $course,
            //                 'user_id' => $tutor_id,
            //                 'created' => time(),
            //                 'cost' => $course_info[0]['courseCost'],
            //             ];

            //             $this->RegisterModel->basicInsert('tbl_registered_course', $courseUserMap);
            //         }
            //     }

            //     $teacher_list[] = $teacher_raw_data;
            // }

            //username and password send

            $settins_sms_status   = $AdminClass->getSmsType("Template Activate Status");

            if ($settins_sms_status[0]['setting_value'] ) { 

                $settins_Api_key = $AdminClass->getSmsApiKeySettings();
                $settins_sms_messsage = $AdminClass->getSmsType("Corparate Registration");

                $register_code_string = $settins_sms_messsage[0]['setting_value'];
                $message = str_replace( "{{ username }}" , $this->session->get('email') , $register_code_string);
                $message = str_replace( "{{ password }}" , ($this->session->get('password')) , $message);
                $message = str_replace( "{{ C_username }}" , $data['user_email'] , $message);
                $message = str_replace( "{{ C_password }}" , $data['user_pawd'], $message);

                $api_key = $settins_Api_key[0]['setting_value'];
                $content = urlencode($message);

                $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . ($this->session->get('user_mobile')) . "&content=$content";

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
                
            }

            $this->session->set('user_id', $corporate_id);
            //$this->corporate_mailTemplate($this->session->userdata('corporate_name'), $this->session->userdata('email'), $this->session->userdata('password'), $teacher_list);


            if ($this->session->get('registrationType') != 'trial') {
            
             echo 1;

            } else {
				echo 2;
                //redirect('corporate_mail');
            }
        }
        else
        {
            echo '0';
        }   
            
    }
    
    public function save_student()
    {
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();

        $error=[];
        if (!$this->validate('StudentValidate')) 
        {
            $validation = $this->validator;
     
            $error=$validation->getErrors();
            //return $this->response->setJSON($error);
        }

        $student=$this->request->getVar('student');

        $flag=0;
        if($error)
        {
            $error= $error;
            $flag++;
        }

        $mobile = $this->request->getVar('mobile');
        // echo $_POST['full_number']; die;
        $mobileExists = $StudentClass->getInfo('tbl_useraccount', 'user_mobile', $_POST['full_number']);
        // echo '<pre>';
        // print_r($mobileExists);
        // die();
        if (count($mobileExists)) {
           echo json_encode("mobile_number_error");
           exit();
        }


        if ($this->myValidation($student) == false) {
            $error['student_error']='student name can not be blank';
            $flag++;
        }

        if ($flag > 0) {
            foreach($error as $errors)
            {
                $errot_new[]=$errors.'<br>';
            
            }
         
            echo json_encode($errot_new);
            exit;
        }

        // echo '<pre>';print_r($_POST);die;

        $data['number'] = rand(10000, 99999);
        // $content = urlencode("Q-Study Registration Code: ".$data['number']);
        // $url = "https://platform.clickatell.com/messages/http/send?apiKey=iyypKonpQNOHUBMv4wngVA==&to=" . $_POST['full_number'] . "&content=$content";
        $settins_Api_key = $AdminClass->getSmsApiKeySettings();
        $settins_sms_messsage = $AdminClass->getSmsMessageSettings();

        $register_code_string = $settins_sms_messsage[0]['setting_value'];
        $find = array("{{register_code}}");
        $replace = array($data['number']);
        $message = str_replace($find, $replace, $register_code_string);

        $api_key = $settins_Api_key[0]['setting_value'];
        $content = urlencode($message);
        //echo '<pre>';print_r($_POST);echo $_POST['full_number'];die;
        $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $_POST['full_number'] . "&content=$content";

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
        //print_r($result);die;
        $send_msg_status = json_decode($result);
        $this->session->set('random_number', $data['number']);
        if(isset($send_msg_status))
        {
            if (count($send_msg_status->messages) > 0 && $send_msg_status->messages[0]->accepted == 1) {
                $this->session->set('random_number', $data['number']);
            }
        }

        $abc=$RegisterClass->save_random_digit($data);

        $this->session->set('parent_name', $_POST['parent_name']);
        $this->session->set('email', $_POST['email']);
        $this->session->set('password', $_POST['password']);
        $this->session->set('mobile', $_POST['full_number']);

        $rs_data=array();
        for ($i = 0; $i < count($_POST['student']); $i++) {
            $data_std['name'] = $_POST['student'][$i];
            $data_std['grade'] = $_POST['grade'][$i];
            $data_std['SCT'] = $_POST['SCT'][$i];
            $rs_data[]=$data_std;
        }

        $this->session->set('students',$rs_data);
        echo json_encode('success');
    }

    public function myValidation($student)
    {
        foreach ($student as $singleSt) {
            if ($singleSt) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function sure_data_save()
    {
        
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();

        //print_r($this->session->get('random_number'));die();
        //  print_r($_POST['random']);die();
         if ($_POST['random'] == $this->session->get('random_number')) {
            if ($this->session->get('registrationType') !='trial') {
                
                if ($this->session->get('paymentType')==1 ) {
                $data['end_subscription'] = date('Y-m-d', strtotime(date("Y-m-d", strtotime(date('Y-m-d'))) . " +30 day"));
                }

                if ($this->session->get('paymentType')==2) {
                $data['end_subscription'] = date('Y-m-d', strtotime(date("Y-m-d", strtotime(date('Y-m-d'))) . " +182 day"));
                }

                if ($this->session->get('paymentType')==3) {
                $data['end_subscription'] = date('Y-m-d', strtotime(date("Y-m-d", strtotime(date('Y-m-d'))) . " +365 day"));
                }
            }
            $rs_student = $this->session->get('students');
            $rs_course  = $this->session->get('courses');
            
            $data['children_number']  = $this->session->get('childrens');
            $data['subscription_type']= $this->session->get('registrationType');
            $data['user_type']        = $this->session->get('userType');
            $data['country_id']       = $this->session->get('countryId');
            $data['name']             = $this->session->get('parent_name');
            $data['user_email']       = $this->session->get('email');
            $data['user_pawd']        = md5($this->session->get('password'));
            $data['user_mobile']      = $this->session->get('mobile');
            $data['created']          = time();

            $data['SCT_link'] = random_string('alnum', 10);
         
            $parent_id  = $RegisterClass->saveUser($data);
            $student_list = array();
            
            foreach ($rs_student as $singleStudent) {

                $raw_st_data=array();
                $st['name']    = $singleStudent['name'];
                $pieces        = explode(" ", $st['name']);
                $random_number = rand(100, 999);
                $st['user_email']=$pieces[0];
                $raw_st_data['st_name']    =$pieces[0];
                $raw_st_data['st_password']=$pieces[0].$random_number;
                $st['user_pawd']=md5($pieces[0].$random_number);

                $user_pswd[]    = ($pieces[0].$random_number);
                $this->session->set('st_password', $user_pswd);
                $st['parent_id']=$parent_id;
                $st['user_type']=6;
                $st['country_id']       = $this->session->get('countryId');
                $st['subscription_type']= $this->session->get('registrationType');

                $st['student_grade']    = $singleStudent['grade'];
                $st['created']          = time();
     
                $st['SCT_link'] = random_string('alnum', 10);
                // echo '<pre>';
                // print_r($st['SCT_link']);
                // die();
             
                $student_id = $RegisterClass->basicInsert('tbl_useraccount', $st);
                
                foreach ($rs_course as $singleCourse) {
                    $course['course_id']=$singleCourse;
                    $rs_course_cost     =$RegisterClass->getCourseCost($course['course_id']);
                    $course['cost']    = 0;//$rs_course_cost[0]['courseCost'];
                    
                    if($st['subscription_type']=='trial'){
                    $course['endTime'] = time()+24*3600;
                    }
                    $course['user_id'] = $student_id;
                    $course['created'] = time();
                    
                    $RegisterClass->basicInsert('tbl_registered_course', $course);   
                }
              
                $st['SCT_link'] = $singleStudent['SCT'];
                if ($st['SCT_link']) {
                    $sct_link=$RegisterClass->getInfo('tbl_useraccount', 'SCT_link', $st['SCT_link']);

                    if ($sct_link) {
                        $usertype = $sct_link[0]['user_type'];
                        if ($usertype == 6) {
                            $referral['user_id']      = $student_id;
                            $referral['refferalUser'] = $sct_link[0]['id'];
                            $referral['refferalLink'] = $st['SCT_link'];
                            $RegisterClass->refferalLinkInsert('tbl_referral_users', $referral);
                            
                        }else{

                            $enrl['sct_id'] = $sct_link[0]['id'];
                            $enrl['st_id'] = $student_id;
                            $enrl['sct_type'] = $sct_link[0]['user_type'];
                            $RegisterClass->basicInsert('tbl_enrollment', $enrl);

                        }
                    }
                }
                $student_list[]=$raw_st_data;
                $this->session->set('student_list', $student_list);
            }
            //echo 'asceee114';die();
            $courseName='';
            foreach ($rs_course as $singleCourse) {
                $course['course_id']=$singleCourse;
                $rs_course_cost=$RegisterClass->getCourseCost($course['course_id']);
                $course['cost']=$rs_course_cost[0]['courseCost'];
                $courseName .= $rs_course_cost[0]['courseName'];
                $course['user_id']=$parent_id;
                $course['created']=time();
                //$this->RegisterModel->basicInsert('tbl_registered_course', $course);
            }
            
            
            $this->session->set('user_id', $parent_id);
            $this->session->set('courseName', $courseName);

            //username and password send
            $settins_sms_status   = $AdminClass->getSmsType("Template Activate Status");

            if ($settins_sms_status[0]['setting_value'] ) {
                
                $settins_Api_key = $AdminClass->getSmsApiKeySettings();
                $settins_sms_messsage = $AdminClass->getSmsType("Parent Registration");
                

                $register_code_string = $settins_sms_messsage[0]['setting_value'];
                $message = str_replace( "{{ username }}" , $this->session->get('email') , $register_code_string);
                $message = str_replace( "{{ password }}" , $this->session->get('password'),$message);
                $message = str_replace( "{{ C_username }}" , $st['user_email'] , $message);
                $message = str_replace( "{{ C_password }}" , $pieces[0].$random_number , $message);

                $api_key = $settins_Api_key[0]['setting_value'];
                $content = urlencode($message);

                $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $this->session->get('mobile') . "&content=$content";

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
                // print_r($result);die;
                $send_msg_status = json_decode($result);

            }

            if ($this->session->get('registrationType') != 'trial') {
                echo 1;
            } 
            else 
            {
                echo 2;
            }

            $this->mailTemplate($this->session->get('parent_name'), $this->session->get('email'), $this->session->get('password'), $student_list);
            
        } 
        else 
        {
            echo 0;
        }
    }

    public function mailTemplate($parent_name, $parent_email, $parent_password, $student_list)
    {
        
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();

        $userName = $parent_name;
        $userEmail = $parent_email;
        $userPassword = $parent_password;
        
        $template = $RegisterClass->getInfo('table_email_template', 'email_template_type', $this->session->get('userType'));
        // echo '<pre>';
        // print_r($template);
        // die();
        $student_number = sizeof($student_list);
        if ($template) {
   
            $subject = $template[0]['email_template_subject'];
            $template_message = $template[0]['email_template'];

            $firstPos = strpos('[[[studentdata]]]', $template_message);
            $lastPos = strpos('[[[/studentdata]]]', $template_message);
            $sub_str_message = substr($template_message, $firstPos, $lastPos);
            // echo '<pre>';
            // print_r($sub_str_message);
            // die();
            $St_message='';
            $st_data = '';
            foreach ($student_list as $single_child) {
                $st_data .=
                "<div style='overflow:hidden ;  margin-bottom:20px;'>
                <div style='width:70%; float:left; text-align:left;'>
                <p>Username</p>
                <p>{$single_child['st_name']}</p>
                </div>
                <div style='width:30%; float:left; text-align:right;'>
                <p>Password</p>
                <p>{$single_child['st_password']}</p>
                </div>
                </div>";
            }

            $find = array("{{student_number}}","{{student_block}}","{{parentName}}","{{parent_email}}","{{parent_password}}");
            $replace = array($student_number,$st_data,$userName,$userEmail,$userPassword);
            $message = str_replace($find, $replace, $template_message);
            // echo '<pre>';
            // print_r($message);
            // die();
            $mail_data['to'] = $userEmail;
            $mail_data['subject'] = $template[0]['email_template_subject'];
            ;
            $mail_data['message'] = $message;
        
            sendMail($mail_data);
        }
        return true;
    }

    private function after_send_mail_user_show_view($type)
    {

        $RegisterClass=new  \RegisterClass();

        $registrationType = $this->session->get('registrationType');

        $mail_data['reg_type'] = $registrationType;
        $mail_data['registration_type'] = $type;
        $mail_data['mail_user_info'] = $RegisterClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));

        return $mail_data;
        
    }
    public function parent_trial_mail()
    {
        
        $type=0;
        $mail_data=$this->after_send_mail_user_show_view($type);
      
        return view('registration/registration_compleate',$mail_data);
    }

    public function redirect_url()
    {
        return redirect()->to(base_url('/select_course'));
    }

    public function save_upper_student()
    {
        
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();

         $error=[];
        if (!$this->validate('upperlevelStudentValidate')) 
        {
            $validation = $this->validator;
     
            $error=$validation->getErrors();
            //return $this->response->setJSON($error);
        }


        $student=$this->request->getVar('student');

        $mobile = $this->request->getVar('mobile');

        $mobileExists = $AdminClass->getInfo('tbl_useraccount', 'user_mobile', $_POST['full_number']);
        if (count($mobileExists)) {
           echo json_encode("mobile_number_error");
           exit();
        }
        
        $flag=0;
        if ($error) {
            $error=$error;
            $flag++;
        }

        if ($flag > 0) {
            foreach($error as $errors)
            {
                $errot_new[]=$errors.'<br>';
            
            }
         
            echo json_encode($errot_new);
            exit;
        }
		
        $data['number'] = rand(10000, 99999);
        $settins_Api_key = $AdminClass->getSmsApiKeySettings();
        $settins_sms_messsage = $AdminClass->getSmsMessageSettings();
        // echo '<pre>';
        // print_r($settins_Api_key);
        // die();
        $register_code_string = $settins_sms_messsage[0]['setting_value'];
        $find = array("{{register_code}}");
        $replace = array($data['number']);
        $message = str_replace($find, $replace, $register_code_string);

        $api_key = $settins_Api_key[0]['setting_value'];
        $content = urlencode($message);
        //echo '<pre>';print_r($_POST);echo $_POST['full_number'];die;
        $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $_POST['full_number'] . "&content=$content";

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
        
        $send_msg_status = json_decode($result);
        // echo "<pre>";print_r(count($send_msg_status->messages));die();
         $this->session->set('random_number', $data['number']);
        if(isset($send_msg_status))
        {
            if (count($send_msg_status->messages) > 0 && $send_msg_status->messages[0]->accepted == 1) {
                $this->session->set('random_number', $data['number']);
                //echo 'message send';
            }
        }
		
        // echo $this->session->userdata('random_number');die();

        $RegisterClass->save_random_digit($data);

        $this->session->set('upper_student_name', $_POST['upper_student_name']);
        $this->session->set('email', $_POST['email']);
        $this->session->set('password', $_POST['password']);
        $this->session->set('mobile', $_POST['full_number']);
        echo json_encode('success');
    }

    public function sure_upper_student_data_save()
    {
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();
		//echo $this->session->get('random_number');die();
        if ($_POST['random']==$this->session->get('random_number')) {
            //echo $this->session->get('registrationType');die();
            $rs_course=$this->session->get('courses');
            $data['subscription_type']=$this->session->get('registrationType');
            $data['user_type']=$this->session->get('userType');
            $data['country_id']=$this->session->get('countryId');
            $data['name']=$this->session->get('upper_student_name');
            $data['user_email']=$this->session->get('email');
            $data['user_pawd']=md5($this->session->get('password'));
            $data['user_mobile']=$this->session->get('mobile');
            $data['student_grade']=13;
            $data['created']=time();
            // echo '<pre>';
            // print_r($data);
            // die();
            $upper_student_id = $RegisterClass->saveUser($data);

            $courseName='';
            foreach ($rs_course as $singleCourse) {
                $course['course_id']=$singleCourse;
                $rs_course_cost=$RegisterClass->getCourseCost($course['course_id']);
                if($data['user_type'] == 'trial'){
                    $course['cost'] = 0;
                }else{
                    $course['cost'] = $rs_course_cost[0]['courseCost'];
                }
                $courseName .= $rs_course_cost[0]['courseName'];
                $course['user_id']=$upper_student_id;
                $course['created']=time();
                $RegisterClass->basicInsert('tbl_registered_course', $course);
            }
            
            
            $this->session->set('user_id', $upper_student_id);
            $this->session->set('courseName', $courseName);

            $settins_sms_status=$AdminClass->getSmsType("Template Activate Status");
            if ($settins_sms_status[0]['setting_value'] ) {

                $settins_Api_key =$AdminClass->getSmsApiKeySettings();
                $settins_sms_messsage =$AdminClass->getSmsType("Upper level student");

                $register_code_string = $settins_sms_messsage[0]['setting_value'];
                $message = str_replace( "{{ username }}" , $this->session->get('email') , $register_code_string);
                $message = str_replace( "{{ password }}" , ($this->session->get('password')) , $message);

                $api_key = $settins_Api_key[0]['setting_value'];
                $content = urlencode($message);
                
                $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $this->session->get('mobile') . "&content=$content";

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

            }

            if ($this->session->get('registrationType') != 'trial') {
                echo 1;
            } else {
                $this->student_mailTemplate($this->session->get('upper_student_name'), $this->session->get('email'), $this->session->get('password'));
                echo 2;
            }
        } else {
            echo 0;
        }
    }

    public function sure_school_data_save()
    {
   
        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();
        
		//print_r($this->session->get('random_number'));die();
        if($_POST['random']==$this->session->get('random_number')){
        $rs_teachers = $this->session->get('teachers');

        $data['children_number'] = $this->session->get('teacher_number');
        //$data['subscription_type'] = $this->session->userdata('registrationType');
        $data['user_type'] = $this->session->get('userType');
        $data['country_id'] = $this->session->get('countryId');
        $data['name'] = $this->session->get('school_name');
        $data['user_email'] = $this->session->get('email');
        $data['user_pawd'] = md5($this->session->get('password'));
        $data['user_mobile'] = ($this->session->get('user_mobile'));
        $data['user_phone'] = ($this->session->get('user_phone'));
        $data['website'] = ($this->session->get('website'));
        //  $data['user_mobile'] = $this->session->userdata('mobile');
        $data['SCT_link'] = $this->randomString();
        $data['created'] = time();
        $school_id = $RegisterClass->saveUser($data);
        $teacher_list = array();
        foreach ($rs_teachers as $singleTeacher) {
            $raw_te_data = array();
            $st['name'] = $singleTeacher['name'];

              //$pieces = explode(" ", $st['name']);

            $random_number = rand(100, 999);

            $st['user_email'] = $singleTeacher['name'];
            $raw_te_data['teacher_user_name'] = $singleTeacher['name'];
            $raw_te_data['teacher_password'] = $singleTeacher['user_pawd'];
            $st['user_pawd'] = md5($singleTeacher['user_pawd']);
            $st['country_id'] = $this->session->get('countryId');
            $st['user_type'] = 3;
            $st['parent_id'] = $school_id;
              $st['SCT_link'] = $data['SCT_link'];//$this->randomString();
              $st['created'] = time();
              $RegisterClass->basicInsert('tbl_useraccount', $st);
              $teacher_list[] = $raw_te_data;
        }

             $settins_sms_status   = $AdminClass->getSmsType("Template Activate Status");

            if ($settins_sms_status[0]['setting_value'] ) { 

                //username and password send

                $settins_Api_key = $AdminClass->getSmsApiKeySettings();
                $settins_sms_messsage = $AdminClass->getSmsType("School Registration");

                $register_code_string = $settins_sms_messsage[0]['setting_value'];
                $message = str_replace( "{{ username }}" , $this->session->get('email') , $register_code_string);
                $message = str_replace( "{{ password }}" , ($this->session->get('password')) , $message);
                $message = str_replace( "{{ T_username }}" , $st['user_email'] , $message);
                $message = str_replace( "{{ T_password }}" , $singleTeacher['user_pawd'] , $message);

                $api_key = $settins_Api_key[0]['setting_value'];
                $content = urlencode($message);

                $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . ($this->session->get('user_mobile')) . "&content=$content";

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

            }
  
            echo 1;

            $this->session->set('user_id',$school_id);

            $this->school_mailTemplate($this->session->get('school_name'), $this->session->get('email'), $this->session->get('password'), $teacher_list);

        }  
    }

    public function student_mailTemplate($upper_student_name, $email, $password)
    {

        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();

        $Name=$upper_student_name;
        $email=$email;
        $Password=$password;
        $template=$RegisterClass->getInfo('table_email_template', 'email_template_type', $this->session->get('userType'));
        // echo $this->session->get('userType');die();
        //  echo '<pre>';
        //     print_r($template);
        //     die();

        if ($template) {
            $subject = $template[0]['email_template_subject'];
            $template_message = $template[0]['email_template'];
            
            $find = array("{{upper_student_name}}","{{upper_student_email}}","{{upper_student_password}}");
            $replace = array($Name,$email,$Password);
            $message = str_replace($find, $replace, $template_message);
            $mail_data['to'] = $email ;
            $mail_data['subject'] = $template[0]['email_template_subject'];
            ;
            $mail_data['message'] = $message;
           
            $this->sendEmail($mail_data);
        }
        return true;
    }

    public function school_mailTemplate($school_name, $schoolEmail, $schoolPassword, $teacherList)
    {

        $StudentClass=new  \StudentClass();
        $AdminClass=new  \AdminClass();
        $RegisterClass=new  \RegisterClass();
        
        $template = $RegisterClass->getInfo('table_email_template', 'email_template_type', $this->session->get('userType'));
        $teacher_number = sizeof($teacherList);
        if ($template) {
              $subject = $template[0]['email_template_subject']; //->email_template_subject;
              $template_message = $template[0]['email_template']; //->email_template;
              
              $te_data = '';
            foreach ($teacherList as $single_teacher) {
                $te_data .=
                "<div style='overflow:hidden ;  margin-bottom:20px;'>
                <div style='width:70%; float:left; text-align:left;'>
                <p>Username</p>
                <p>{$single_teacher['teacher_user_name']}</p>
                </div>
                <div style='width:30%; float:left; text-align:right;'>
                <p>Password</p>
                <p>{$single_teacher['teacher_password']}</p>
                </div>
                </div>";
            }
            
            $find = array("{{teacher_number}}","{{teacher_block}}","{{schoolName}}","{{school_email}}","{{school_password}}");
            $replace = array($teacher_number,$te_data,$school_name,$schoolEmail,$schoolPassword);
            $message = str_replace($find, $replace, $template_message);
            $mail_data['to'] = $schoolEmail;
            $mail_data['subject'] = $template[0]['email_template_subject'];
            ;
            $mail_data['message'] = $message;
            $this->sendEmail($mail_data);
        }
        return true;
    }

    public function school_mail()
    {
        $type=4; //school type
        $mail_data=$this->after_send_mail_user_show_view($type);

        return view('registration/registration_compleate',$mail_data);
    }
    public function upper_student_trial_mail()
    {
        $type=0;
        $mail_data=$this->after_send_mail_user_show_view($type);

        return view('registration/registration_compleate',$mail_data);
    }

    public function tutor_trial_mail()
    {
        $type=0;
        $mail_data=$this->after_send_mail_user_show_view($type);
        return view('registration/registration_compleate',$mail_data);
    }

    public function sendEmail($mail_data)
    {
        $mailTo        =  $mail_data['to'];
        $mailSubject   =   $mail_data['subject'];
        $message       =   $mail_data['message'];

        $email = \Config\Services::email();

        /*$config['protocol'] ='sendmail';
        $config['mailpath'] ='/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = true;*/
        $config['protocol']    = 'smtp';
        $config['SMTPCrypto']    = 'ssl';
        $config['SMTPPort']    = '465';
        $config['SMTPHost']    = 'smtppro.zoho.com';
        $config['SMTPUser']    = 'contact@q-study.com';
        $config['SMTPPass']    = 'Mn876#%2dq';
        $config['mailType']    = 'html';
        $email->initialize($config);
        
        
        $email->setFrom('contact@q-study.com','Q-study');
        $email->setTo($mailTo);
        $email->setSubject($mailSubject);
        $email->setMessage($message);
        $email->send();
     
        if (!$email->send()) {
            return $email->printDebugger();
        } else {
            return true;
        }
    }

    private function randomString($length = 10)
    {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public function checkPasswordConfirmPassword($pass, $confirm)
    {
        $i=0;
        $j=0;
        foreach ($pass as $singlePass) {
            if ($singlePass != $confirm[$i]) {
                return false;
            } else {
                $j++;
            }
            $i++;
        }
        if ($j != 0) {
            return true;
        }
    }

    public function home_page()
    {
        //echo 'hiii';die();
        return redirect()->to(base_url('/dashboard'));
    }

    public function show_paypal_form()
    {   
        $FaqClass=new \FaqClass(); 
        $SettingClass=new \SettingClass();
        
        $data['video_help'] = $FaqClass->videoSerialize(4, 'video_helps');
        $data['video_help_serial'] = 4;

        if ($this->session->get('user_id') != '') {
            $data['publish_key']=$SettingClass->getStripeKey('publish');
            return view('payment_option', $data);
        } else {
            return redirect()->to(base_url('signup'));
        }
    }

    public function go_paypal()
    {
      
        $SettingClass=new \SettingClass();
        if ($this->session->get('user_id') != '') {
            $data['url']=$SettingClass->getPaypalKey('url');
            $data['business_key']=$SettingClass->getPaypalKey('business_account');
            $data['paymentType'] = $this->session->get('paymentType');//p3 te bosbe
            $data['amount'] = $this->session->get('totalCost');
            $data['payment_process'] = $this->session->get('payment_process');
            $data['user_id'] = $this->session->get('user_id');
            $userType = $this->session->get('userType');
            $rs_course = $this->session->get('courses');
            $data['courseId'] = implode(",", $rs_course);
            if($userType == 1){
               $this->session->set('userType',6);
            }

            if(is_array($rs_course))
            {
                 $courseName=$this->db->table('tbl_course')->select('courseName')->whereIn('id',$rs_course)->get()->getResultArray();
                 $i=0;
                 foreach($courseName as $courses)
                 {
                     $course_new[$i]=$courses['courseName'];
                     $i++;
                 }
                 $course_new=implode(',',$course_new);
            }
            // else
            // {
            //     $course_new=$this->session->get('userType');
            // }



          
            
            // if ($userType == 1 || $userType == 2) {
            //     $rs_course = $this->session->userdata('courses');
            //     $data['courseId'] = implode(",", $rs_course);
            // } else {
            //     $data['courseId']='';
            // }
            // $this->session->unset_userdata('userType');courses
           // $data['package']=$this->session->get('courseName');
            
            $data['package']=$course_new;

            // echo '<pre>';
            // print_r($data);
            // die();
           
            return view('paypal-form',$data);
        } else {
            return redirect()->to(base_url('signup'));
        }
    }
	
    public function corporate_mail()
    {
        $type=5;
        $mail_data=$this->after_send_mail_user_show_view($type);
        
        return view('registration/registration_compleate',$mail_data);
    }
}
