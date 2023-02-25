<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TblUserAccountModel;
use App\Models\TblSetting;
use App\Models\TblTutorCommision;

class AdminController extends BaseController
{
    public function index()
    {
        $AdminClass = new \AdminClass();
		$FaqClass = new \FaqClass();
        if ($this->session->get('userType') == 0) {
            $data['video_help'] = $FaqClass->videoSerialize(4, 'video_helps');
            $data['video_help_serial'] = 4;
        }
        $data['user_info'] = $AdminClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return view('admin/admin_dashboard', $data);
    }

    public function all_area()
    {
        $AdminClass = new \AdminClass();

        $data['user_info'] = $AdminClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = '';
        $data['page_section'] = '';
	
        return view('admin/all_area/all_area', $data);
    }

    public function user_list()
    {
         error_reporting(0);
         $AdminClass = new \AdminClass();
         $TblUserAccountModel=new TblUserAccountModel();
		 //$this->db->where('subscription_type','guest')->where('end_subscription',null)->update('tbl_useraccount',(['unlimited'=>1]));
		 if ($_GET['name'] != null || $_GET['user_type'] != null || $_GET['country_id'] != null) {
            
            $data['total_registered'] = $AdminClass->total_registered_search($_GET['name'], $_GET['user_type'],$_GET['country_id']);
            $data['today_registered'] = $AdminClass->today_registered_search($_GET['name'], $_GET['user_type'],$_GET['country_id']);
        }else{
            $data['total_registered'] = $AdminClass->total_registered();
            $data['today_registered'] = $AdminClass->today_registered();

        }
        $data['tutor_with_10_student'] = $AdminClass->tutor_with_10_student();


        $total_income =  $AdminClass->getTotalIncome();
        $daily_income =  $AdminClass->getDailyIncome();
        $total  = $total_income[0];
        $dailyIncome  = $daily_income[0];

        $data['total_income'] = (isset($total->total_cost) && $total->total_cost > 0)?$total->total_cost:0;
        $data['daily_income'] = (isset($dailyIncome->daily_income) && $dailyIncome->daily_income > 0)?$dailyIncome->daily_income:0;
        
        
        $trial = 0;
        $guest = 0;
        $pending = 0;
        $total_registeredCount = 0;
        $today_registeredCount = 0;

        foreach ($data['total_registered'] as $key => $value) {

            if ( ( $value['subscription_type'] == 'signup' || $value['subscription_type'] == 'direct_deposite' ) && $value['end_subscription'] != "" ) {

                if ( $value['end_subscription'] >= date("Y-m-d") ) {
                    $total_registeredCount++;
                }

            }

            if ( ( $value['subscription_type'] == 'signup' || $value['subscription_type'] == 'direct_deposite' ) && $value['end_subscription'] != "" ) {

                if ( date("Y-m-d" , $value['created'] ) == date("Y-m-d") ) {
                    $today_registeredCount++;
                }
            }


            if ($value['subscription_type'] == "trial" ) {
                $trial++;
            }

            if ($value['subscription_type'] == "guest" ) {
                $guest++;
            }

            if ($value['subscription_type'] == "direct_deposite"  &&  $value['direct_deposite'] == 0 ) {
                $pending++;
            }
        }


        $data['trial'] = $trial;
        $data['guest'] = $guest;
        $data['pending'] = $pending;

        $data['total_registeredCount'] = $total_registeredCount;
        $data['today_registeredCount'] = $today_registeredCount;

        
        $data['tutor_with_50_vocabulary'] = $AdminClass->tutor_with_50_vocabulary();
        date_default_timezone_set('Australia/Canberra');
        $date = date('Y-m-d');
        $data['get_todays_data'] = $AdminClass->get_todays_data($date);
        
        $data['user_type'] = $AdminClass->getAllInfo('tbl_usertype');
        $data['all_country'] = $AdminClass->getAllInfo('tbl_country');
        
        $data['user_info'] = $AdminClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'User List';
        $data['page_section'] = 'User';

        $groupboard_assigner = $AdminClass->groupboard_req();

        if (count($groupboard_assigner)) {
            $groupboard_taker = $AdminClass->groupboard_taker();
        }
        $groupboard_require = count($groupboard_assigner) - count($groupboard_taker);

        foreach ($groupboard_assigner as $key => $value) {
            $groupboard_assigner_s[] = $value['user_id'];
        }

        foreach ($groupboard_taker as $key => $value) {
            $groupboard_taker_s[] = $value['id'];
        }


        $data['groupboard_require'] = $groupboard_require;
        $data['groupboard_assigner'] = isset($groupboard_assigner_s) ? $groupboard_assigner_s : [];
        $data['groupboard_taker'] = isset($groupboard_taker_s) ? $groupboard_taker_s : [];
        
        
        
        $signup_users = $TblUserAccountModel->where('subscription_type','signup')->where('subscription_status !=',1)->where('end_subscription >',date('Y-m-d'))->findAll();
    
        foreach ($signup_users as $key => $value) {
            $TblUserAccountModel->where('id',$value->id)->set(['subscription_status' => 1])->update();
        }
   
        
        return view('admin/user/user_list', $data); 
    }

    public function notification()
    {
        error_reporting(0);
        $AdminClass = new \AdminClass();
        $RegisterClass=new \RegisterClass();
        $TblSetting=new TblSetting();

        
        $tbl_setting = $TblSetting->where('setting_key','days')->first();
       
        $duration    = $tbl_setting->setting_value;
        $date        = date('Y-m-d');
        $d1          = date('Y-m-d', strtotime('-'.$duration.' days', strtotime($date)));
        $trialEndDate= strtotime($d1);

        //$data['direct_deposit_count'] = $this->Admin_model->getInfoDirectDepositUserCount('tbl_useraccount', 'subscription_type', 'direct_deposite');
        $data['direct_deposit_count'] = count($AdminClass->getInfoDirectDepositUserList());
        $builder = $this->db->table('user_message');
        $builder->select('COUNT(user_id) as total');
        $builder->where('user_id !=',null);
        $builder->where('status !=','seen');
        $builder->groupBy('user_id');
        $query = $builder->get();
        $email_messages =$query->getResultArray();
     
       
        $data['email_messages'] = count($email_messages);
          // echo '<pre>';
        // print_r($email_messages );die();
        $limit = 999999;
        $offset = 0;
        
        //$data['trial_user_info'] = $this->Admin_model->getInfoTrialUser('tbl_useraccount', 'subscription_type', 'trial',$limit,$offset);
        $data['trial_user_info'] = $AdminClass->getInfoTrialActiveUser('tbl_useraccount', 'subscription_type', 'trial',$trialEndDate,$limit,$offset);
        $data['trial_user_info'] = count($data['trial_user_info']);
        
        $inactive_user_info = $AdminClass->getInfoInactiveUser('tbl_useraccount','subscription_type', 'signup',$limit,$offset);
        $inactive_trial_user_info = $AdminClass->getInfoInactiveTrialUser('tbl_useraccount', 'subscription_type', 'trial',$trialEndDate,$limit,$offset);
        $margeBoughtInactiveUser = array_merge($inactive_user_info, $inactive_trial_user_info);
        $data['inactive_user_info'] = count($inactive_trial_user_info);
        //echo "<pre>";print_r($add_attributes);die();
     
        $signup_user_info = $AdminClass->getAllSignupUsers('tbl_useraccount', 'subscription_type', 'signup',$limit,$offset);
        $data['signup_user_info'] = count($signup_user_info);
        
        
        $suspend_user_info = $AdminClass->getInfoSuspendUser('tbl_useraccount', 'suspension_status', 1,$limit,$offset);
        $data['suspend_user_info'] = count($suspend_user_info);
        
        $guest_user_info = $AdminClass->getAllguestUsers('tbl_useraccount', 'subscription_type', 'guest',$limit,$offset);
        $data['guest_user_info'] = count($guest_user_info);

        
        $parent_list = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'user_type', 1,$limit,$offset);
        $data['parent_list'] = count($parent_list);


        $upper_student = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'user_type', 2,$limit,$offset);
        $data['upper_student'] = count($upper_student);


        $tutors_list = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'user_type', 3,$limit,$offset);
        $data['tutors_list'] = count($tutors_list);


        $school_list = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'user_type', 4,$limit,$offset);
        $data['school_list'] = count($school_list);


        $corporate_list = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'user_type', 5,$limit,$offset);
        $data['corporate_list'] = count($corporate_list);

        $student_list = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'user_type', 6,$limit,$offset);
        $data['student_list'] = count($student_list);

        $aus_users = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'country_id', 1,$limit,$offset);
        $data['aus_users'] = count($aus_users);


        $uk_users = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'country_id', 9,$limit,$offset);
        $data['uk_users'] = count($uk_users);

        $bd_users = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'country_id', 8,$limit,$offset);
        $data['bd_users'] = count($bd_users);

        $usa_users = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'country_id', 2,$limit,$offset);
        $data['usa_users'] = count($usa_users);

        $can_users = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'country_id', 10,$limit,$offset);
        $data['can_users'] = count($can_users);
        
        
        $deposite_resources = $AdminClass->getDepositeResources($limit,$offset);
        $data['deposite_resources'] = count($deposite_resources);
        
      
        
        // check whiteboard
        $groupboardResources = $AdminClass->whiteboardPurchesLists($limit,$offset);  //whiteboard AS 
        $data['groupboardResources'] = count($groupboardResources);
        
        $groupboardSignup = $AdminClass->whiteboardPurchesSignupLists('signup',$limit,$offset);  //whiteboard AS 
        $data['groupboardSignup'] = count($groupboardSignup);

        
        $groupboardTrialList = $AdminClass->whiteboardPurchesSignupLists('trial',$limit,$offset);  //whiteboard AS 
       
        $data['groupboardTrialList'] = count($groupboardTrialList);
        
        $CommissiontutorList = $AdminClass->tutorCommisionForAssignStudent($limit,$offset);
        // $CommissiontutorList = $this->Admin_model->getTutorCommission('tbl_tutor_commisions', 'status',0,'tutorId',$user_id);
        $data['CommissiontutorList'] = count($CommissiontutorList);
        
        
        $vocabularyCommision = $AdminClass->vocabularyCommisionCheck($limit,$offset);
        $data['vocabularyCommision'] = count($vocabularyCommision);
       
            
        $checkStudentPercentage = $AdminClass->checkStudentPercentageNotification('daily_modules',$limit,$offset);
        $ninteyPercentageMark = [];
        
        foreach($checkStudentPercentage as $key => $value){
            $total_row = $value['total_row'];
            $percentage = number_format($value['percentage']);
            
            if ($total_row >= 2 && $percentage >= 90){
                //echo $percentage;die;
                $ninteyPercentageMark[$key]['user_id'] = $value['user_id'];
                $ninteyPercentageMark[$key]['name'] = $value['name'];
            }
            
        }
        
        $data['ninteyPercentageMark'] = count($ninteyPercentageMark);
        
        
        $student_prize_list = $AdminClass->getInfoPrizeWinerUser($limit,$offset);
        // echo '<pre>';
        // print_r($student_prize_list);
        // die();
        $data['student_prize_list'] = count($student_prize_list);
        // echo "<pre>"; print_r($data['ninteyPercentageMark']);die;
        //die('noti 111');
        $creative_registers = $AdminClass->getallcreative();
        $data['total_creative_reg'] = count($creative_registers);


        $idea_created_students = $AdminClass->idea_created_students_list();
        $data['idea_created_students']= count($idea_created_students);
        
        $idea_created_tutors = $AdminClass->idea_created_tutor_list();
        $data['total_tutors']= count($idea_created_tutors);

       
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'User List';
        $data['page_section'] = 'User';
        
        // $data['headerlink'] = $this->load->view('dashboard_template/headerlink', $data, true);
        // $data['leftnav'] = $this->load->view('dashboard_template/leftnav', $data, true);
        // $data['header'] = $this->load->view('dashboard_template/header', $data, true);
        // $data['footerlink'] = $this->load->view('dashboard_template/footerlink', $data, true);

        // $data['maincontent'] = $this->load->view('admin/user/notification', $data, true);
        return view('admin/user/notification', $data);
    }

    public function direct_deposit_list()
    {
        error_reporting(0);
        $AdminClass = new \AdminClass();

        $limit = 30;
        $offset = 0;
        $data['direct_deposit_list'] = $AdminClass->getInfoDirectDepositUserAllByList($limit,$offset);

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Direct Deposit User List';
        $data['page_section'] = 'User';
        
        // $data['headerlink'] = $this->load->view('dashboard_template/headerlink', $data, true);
        // $data['leftnav'] = $this->load->view('dashboard_template/leftnav', $data, true);
        // $data['header'] = $this->load->view('dashboard_template/header', $data, true);
        // $data['footerlink'] = $this->load->view('dashboard_template/footerlink', $data, true);

        //$data['maincontent'] = $this->load->view('admin/user/direct_deposit_list', $data, true);
        return view('admin/user/direct_deposit_list', $data);
    }

    public function edit_user($user_id)
    {
        $post = $this->request->getVar();
        // echo '<pre>';
        // print_r($post);die();
        error_reporting(0);
        $AdminClass = new \AdminClass();
        $RegisterClass= new \RegisterClass();
        $QuestionClass= new \QuestionClass();
        $TblSetting=new TblSetting();
        $TblTutorCommision=new TblTutorCommision();

        if (empty($post)) {
            $data['userId'] = $user_id;

            $data['student_prize_list'] = $AdminClass->getInfoPrizeWinerUserByID($user_id,$limit,$offset);
           
            
            $tbl_setting = $TblSetting->where('setting_key','days')->first();
            $duration = $tbl_setting->setting_value;
            $date = date('Y-m-d');
            $d1  = date('Y-m-d', strtotime('-'.$duration.' days', strtotime($date)));
            $trialEndDate = strtotime($d1);
            $data['activeTrilUser'] = $AdminClass->getInfoTrialActiveUserAdmin('tbl_useraccount', 'subscription_type', 'trial',$user_id,$trialEndDate);
        
            //echo "<pre>";print_r($activeTrilUser);die;
            $data['user_type'] = $AdminClass->getAllInfo('tbl_usertype');
            $data['all_country'] = $AdminClass->getAllInfo('tbl_country');

            $data['studentsRefLink'] = $AdminClass->getStudentsRefLink($user_id);

            $data['user_info'] = $AdminClass->getInfo('tbl_useraccount', 'id', $user_id);

            if ($data['user_info'][0]['user_type']==1) { //parent
                $conditions = ['parent_id'=>$user_id];
                $data['allChild'] = $AdminClass->search('tbl_useraccount', $conditions);
            } elseif ($data['user_info'][0]['user_type']==6) { //child
                $conditions = ['id'=>$user_id];
                $data['parent'] = $AdminClass->search('tbl_useraccount', $conditions);
                $data['tutorRefLink'] = $AdminClass->getTutorRefLink($user_id);
            }
            // echo "<pre>";print_r($data['tutorRefLink']);die();

            $checkCommission = $TblTutorCommision->where('tutorId',$user_id)->where('status',0)->findAll();
            // echo '<pre>';
            // print_r($checkCommission);die();
            if ($data['user_info'][0]['user_type']==3) { 
                $data['tutorCommision'] = count($checkCommission);
            }
            
            if ($data['user_info'][0]['user_type']==3) { 
                $data['tutorpendingComissions'] = $AdminClass->getTutorCommission('tbl_tutor_commisions', 'status',0,'tutorId',$user_id);
                $data['tutorpaidComissions']    = $AdminClass->getTutorCommission('tbl_tutor_commisions', 'status',1,'tutorId',$user_id);
            }
            
            
            if ($data['user_info'][0]['user_type'] == 3) { 
                $data['account_detail'] = $this->db->table('tbl_tutor_account_details')->where('tutor_id',$user_id)->get()->getRow();
            }
            
            
            
            $vocabularyCommission = $QuestionClass->vocabularyCommission($user_id);
            // echo 'jii';
            // echo '<pre>';
            // print_r($vocabularyCommission);die();

            if (isset($vocabularyCommission)) {
                $total_approved = $vocabularyCommission->total_approved;
                $total_paid = $vocabularyCommission->total_paid + VOCABULARY_PAYMENT;
                if ($total_approved > $total_paid) {
                    $data['vocabularyCommission'] = 1;
                }else{
                    $data['vocabularyCommission'] = 0;
                }
            }
            
            
            $checkStudentPercentage = $AdminClass->checkStudentPercentage('daily_modules', 'user_id',$user_id);
            // echo '<pre>';
            // print_r($checkStudentPercentage);
            // die();
            if($checkStudentPercentage){
                $totalRow = $checkStudentPercentage[0]['total_row'];
                $percentage = number_format($checkStudentPercentage[0]['percentage']);
                if ($totalRow >= 2 && $percentage >= 90){
                    $data['studentScore'] = 1;
                    $data['studentScoreDetails'] = $AdminClass->checkStudentPercentage('daily_modules', 'user_id',$user_id);;
                }
                
            }
            
            
            $messages_users = $this->db->table('user_message')->where('user_id',$user_id)->orderBy('id','desc')->limit(1)->get()->getResultArray();
            // echo '<pre>';
            // print_r($messages_users);die();
            if(count($messages_users) > 0){
                $data['user_message'] = 1;
                $data['messages_users'] = $this->db->table('user_message')->where('user_id',$user_id)->orderBy('id','desc')->limit(1)->get()->getResultArray();
            }
            
            $data['courses'] = $this->coursesByCountry($data['user_info'][0]['country_id'], $data['user_info'][0]['id'], $type = "edit" , $data['user_info'][0]['subscription_type'] );  //whiteboard rakesh
            
            $data['whiteboard'] = $AdminClass->whiteboardPurches('tbl_registered_course', $user_id);  //whiteboard rakesh 
            
            //echo "<pre>";print_r($data['whiteboard']);die();
            $created_at = $data['user_info'][0]['created'];
            $end_subscription = $data['user_info'][0]['end_subscription'];
            
            $tbl_setting = $this->db->table('tbl_setting')->where('setting_key','days')->get()->getRow();
            $duration = $tbl_setting->setting_value;
            
            $d1 = date('Y-m-d',$created_at);
            $d2 = date('Y-m-d', strtotime($d1 . "+".$duration."days"));
            $d3 = date('Y-m-d');
            
            
            if($data['whiteboard'] == 1 && ($d2 > $d3) && $data['user_info'][0]['user_type'] == 3 && $data['user_info'][0]['subscription_type'] == 'trial'){
                $data['groupboard_trial'] =  1;
            }
            
            
            if($data['whiteboard'] == 1 && ($end_subscription > $d3) && $data['user_info'][0]['user_type'] == 3 && $data['user_info'][0]['subscription_type'] != 'trial'){
                $data['groupboard_signup'] =  1;
            }
            
            
            //check direct deposit resource
            $tbl_qs_payments = $this->db->table('tbl_qs_payment')->where('user_id',$user_id)->where('PaymentEndDate >',time())->orderBy('id','desc')->limit(1)->get()->getRow();

            $data['deposit_resources_status'] = 3;
            if(isset($tbl_qs_payments)){
                
                $end_date = $tbl_qs_payments->PaymentEndDate;
                $payment_status = $tbl_qs_payments->payment_status;
                $data['paymentType'] = $tbl_qs_payments->paymentType;
                
                $d1 = date('Y-m-d',$end_date);
                $d2 = date('Y-m-d');
                
                if($d1 > $d2){
                    $data['deposit_resources'] = 1;//active
                }else{
                    $data['deposit_resources'] = 0;//inactive
                }
                
                if($payment_status == 'Completed'){
                    $data['deposit_resources_status'] = 1; //active
                }
                
                if($payment_status == 'Pending'){
                    $data['deposit_resources_status'] = 0; //Inactive
                }
                
            }
            
            
            //check direct deposit courses
            $checkDirectDepositCourse = $AdminClass->getDirectDepositCourse($user_id);
            $checkDirectDepositPendingCourse = $AdminClass->getDirectDepositPendingCourse($user_id);
            $data['checkDirectDepositCourse'] = $checkDirectDepositCourse;
            $data['checkDirectDepositCourseStatus'] = $checkDirectDepositPendingCourse;
            
            
            $data['checkUnavailableProduct'] = $this->db->table('prize_won_users')->where('user_id',$user_id)->where('status','unavailable')->get()->getRow();
            
            //echo "<pre>";print_r( $data['checkUnavailableProduct']);die();
            
            $data['parents'] = $AdminClass->getInfo('tbl_useraccount', 'user_type', 1);
            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
            $data['page'] = 'Edit User';
            $data['page_section'] = 'User';

            // $data['headerlink'] = $this->load->view('dashboard_template/headerlink', $data, true);
            // $data['leftnav'] = $this->load->view('dashboard_template/leftnav', $data, true);
            // $data['header'] = $this->load->view('dashboard_template/header', $data, true);
            // $data['footerlink'] = $this->load->view('dashboard_template/footerlink', $data, true);

            //$data['maincontent'] = $this->load->view('admin/user/user_edit', $data, true);
            return view('admin/user/user_edit',$data);
        } else {
           
            $prize_unavailable = $this->request->getVar('student_prize_unavailable');
            if(isset($prize_unavailable)){
                $won_product_details = $this->db->table('prize_won_users')->where('user_id',$user_id)->where('status','pending')->get()->getRow();
                if(isset($won_product_details)){
                    $productPoint  = $won_product_details->productPoint;
                    $point_details = $this->db->table('product_poinits')->where('user_id',$user_id)->get()->getRow();
                    $data['bonus_point']   = $point_details->bonus_point + $productPoint;
                    $data['total_point']   = $point_details->total_point + $productPoint;
                    $data['use_point']     = $point_details->use_point   - $productPoint;
                    
                    $updatePoint = $this->db->table('product_poinits')->where('user_id',$user_id)->update($data);
                    
                    $this->db->table('prize_won_users')->where('user_id',$user_id)->where('status','pending')->update(['status'=>'unavailable']);
                } 
            }
            // echo $prize_unavailable;die;
            
            $checkUserMessage = $this->request->getVar('checkUserMessage');
            if (isset($checkUserMessage)) {
                $this->db->table('user_message')->where('user_id',$user_id)->update(['status'=>'seen']);
            }
            $tutorCommisionPaid = $this->request->getVar('tutorCommisionPaid');
            if (isset($tutorCommisionPaid)) {
                $this->db->table('tbl_tutor_commisions')->where('tutorId',$user_id)->update(['status'=>1]);
            }
            
            $deposit_resources_status = $this->request->getVar('deposit_resources_status');
            if (isset($deposit_resources_status)) {
                $this->db->table('tbl_qs_payment')->where('user_id',$user_id)->where('PaymentEndDate >',time())->where('paymentType',3)->update(['payment_status'=>'Completed']);
            }else{
                $this->db->table('tbl_qs_payment')->where('user_id',$user_id)->where('PaymentEndDate >',time())->where('paymentType',3)->update(['payment_status'=>'Pending']);
            }

            $student_prize_list = $this->request->getVar('student_prize_list');
            if (isset($student_prize_list)) {
                $this->db->table('prize_won_users')->where('user_id',$user_id)->update(['status'=>'paid']);
            }
            // added AS 
            if ($post['subscription_type'] == "guest" || $post['subscription_type'] == "signup") {
                $guest_days = (isset($post['guest_days']))?$post['guest_days']:null;
               
                if ($guest_days != null) {
                    $d1 = date('Y-m-d');
                    $d2 = date('Y-m-d', strtotime('+'.$guest_days.' days', strtotime($d1)));
                }
            }elseif($post['subscription_type'] == "trial"){
                $tbl_setting = $this->db->table('tbl_setting')->where('setting_key','days')->get()->getRow();
                $duration = $tbl_setting->setting_value;
                $d1 = date('Y-m-d');
                //$d2 = date('Y-m-d', strtotime('+'.$duration.' days', strtotime($d1)));
            }else{
                $d2 = $this->db->table('tbl_useraccount')->where('id',$user_id)->get()->getRow('end_subscription');
                if (empty($d2)) {
                    $tbl_setting = $this->db->table('tbl_setting')->where('setting_key','days')->get()->getRow();
                    $duration = $tbl_setting->setting_value;
                    $d1 = date('Y-m-d');
                    $d2 = date('Y-m-d', strtotime('+'.$duration.' days', strtotime($d1)));
                }
            }
            // echo "<pre>";print_r($clean);die();
            //$dataToUpdate = array_filter($clean);
            $ck_room_exist = $AdminClass->getInfo('tbl_available_rooms', 'room_id', $post['groupboard_id']);
            if (count($ck_room_exist) || $post['groupboard_id'] == "" ) {
                $ck_whiteboard_exist = $AdminClass->getInfo('tbl_useraccount', 'whiteboar_id', $post['groupboard_id']);

                if (empty($post['groupboard_id'])) {
                    $trial_end_date = null;
                    if (isset($post['trial_end_date']))
                    {
                        $trial_configuration = $AdminClass->getInfo('tbl_setting', 'setting_key', 'days');
                        if (isset($trial_configuration[0]['setting_value']))
                        {
                            $Date = date('Y-m-d');
                            $trial_end_date = date('Y-m-d', strtotime($Date. ' + '.$trial_configuration[0]['setting_value'].' days'));
                        }
                    }

                    if (!empty( $post['user_pawd'] )) {
                        $dataToUpdate = [
                            'user_type'=> $post['user_type'],
                            'country_id' => $post['country_id'],
                            'children_number' => isset($post['numOfChild']) ? $post['numOfChild'] : null,
                            'name' => $post['name'],
                            'user_email' => $post['user_email'],
                            'student_grade' => isset($post['grade']) ? $post['grade'] : null,
                            'user_mobile' => $post['full_phone'],
                            'trial_end_date' => $trial_end_date,
                            // 'user_mobile' => $clean['user_mobile'],
                            'user_pawd'=> md5($post['user_pawd']),
                            'SCT_link' => $post['SCT_link'],
                            'subscription_type' => $post['subscription_type'],
                            'suspension_status' => (isset($post['suspension_status']) && $post['suspension_status']==1)? 1 : 0,
                            'direct_deposite' => (isset($post['direct_deposite']) && $post['direct_deposite']==1)? 1 : 0,
                            'whiteboar_id'=> 0,
                            'tutor_permission' => $post['tutor_permission'],
                            'sms_status_stop' => $post['sms_status_stop'],
                            'end_subscription' => (isset($d2)) ? $d2:null,
                            'unlimited' => isset($post['unlimited']) ? $post['unlimited']:0,
                           ];

                    }else{

                        $dataToUpdate = [
                            'user_type'=> $post['user_type'],
                            'country_id' => $post['country_id'],
                            'children_number' => isset($post['numOfChild']) ? $post['numOfChild'] : null,
                            'name' => $post['name'],
                            'user_email' => $post['user_email'],
                            'student_grade' => isset($post['grade']) ? $post['grade'] : null,
                            'user_mobile' => $post['full_phone'],
                            'trial_end_date' => $trial_end_date,
                            // 'user_mobile' => $clean['user_mobile'],
                            'SCT_link' => $post['SCT_link'],
                            'subscription_type' => $post['subscription_type'],
                            'suspension_status' => (isset($post['suspension_status']) && $post['suspension_status']==1)? 1 : 0,
                            'direct_deposite' => (isset($post['direct_deposite']) && $post['direct_deposite']==1)? 1 : 0,
                            'whiteboar_id'=> 0,
                            'tutor_permission' => $post['tutor_permission'],
                            'sms_status_stop' => $post['sms_status_stop'],
                            'end_subscription' => (isset($d2)) ? $d2:null,
                            'unlimited' => isset($post['unlimited']) ? $post['unlimited']:0,
                        ];
                    }
                    
                    
                    
                    //if course requested, map as registered course with student id
                    if (count($post['course'])) {
                        $courseUserMap = [];
                        $registerCourseStatus = 0;
                        foreach ($post['course'] as $course) {
                            
                            $checkCourse = $AdminClass->checkRegisterCourse($course,$user_id);
                            if($checkCourse > 0){
                                $registerCourseStatus = 1;
                            }else{
                                $registerCourseStatus = 0;
                            }
                            $rs_course_cost    = $RegisterClass->getCourseCost($course['course_id']);
                            $courseUserMap[] = [
                                'course_id' => $course,
                                'user_id' => $user_id,
                                'created' => time(),
                                'cost'=> $rs_course_cost[0]['courseCost'],
                                'endTime' => (isset($d2)) ? $d2:null,
                            ];
                        }
                        
                        //remove previous records
                        //$this->Admin_model->deleteInfo('tbl_registered_course', 'user_id', $user_id);
                        //insert new courses
                        if($registerCourseStatus == 0){
                            //$this->Admin_model->insertBatch('tbl_registered_course', $courseUserMap);
                        }
                    }
                          
                    $AdminClass->updateInfo('tbl_useraccount', 'id', $user_id, $dataToUpdate);
                    if( isset($post['direct_deposite']) && $post['direct_deposite']==1 ){
                        $time = time();
                        $this->db->table('tbl_payment')->where('payment_status','pending')->where('user_id',$user_id)->where('PaymentEndDate >',$time)->where('paymentType',3)->update(['payment_status'=>'Completed','PaymentDate'=>$time ]);
                        if($post['user_type'] == 6){
                            $this->set_referral_point_deposit_user($user_id);
                        }
                        
                    }else{
                        $time = time();
                        $this->db->table('tbl_payment')->where('payment_status','Completed')->where('user_id',$user_id)->where('PaymentEndDate >',$time)->where('paymentType',3)->update(['payment_status'=>'pending']);
                    }
                    
                    $this->session->set('success_msg', 'User updated successfully');


                }else{

                    if (count($ck_whiteboard_exist) == 0 || $ck_whiteboard_exist[0]['id'] == $user_id ) {

                        $trial_end_date = null;
                        if (isset($post['trial_end_date']))
                        {
                            $trial_configuration = $AdminClass->getInfo('tbl_setting', 'setting_key', 'days');
                            if (isset($trial_configuration[0]['setting_value']))
                            {
                                $Date = date('Y-m-d');
                                $trial_end_date = date('Y-m-d', strtotime($Date. ' + '.$trial_configuration[0]['setting_value'].' days'));
                            }
                        }

                        if (!empty( $post['user_pawd'] )) {
                            $dataToUpdate = [
                                'user_type'=> $post['user_type'],
                                'country_id' => $post['country_id'],
                                'children_number' => isset($post['numOfChild']) ? $post['numOfChild'] : null,
                                'name' => $post['name'],
                                'user_email' => $post['user_email'],
                                'student_grade' => isset($post['grade']) ? $post['grade'] : null,
                                'user_mobile' => $post['full_phone'],
                                'trial_end_date' => $trial_end_date,
                                // 'user_mobile' => $clean['user_mobile'],
                                'user_pawd'=> md5($post['user_pawd']),
                                'SCT_link' => $post['SCT_link'],
                                'subscription_type' => $post['subscription_type'],
                                'suspension_status' => (isset($post['suspension_status']) && $post['suspension_status']==1)? 1 : 0,
                                'direct_deposite' => (isset($post['direct_deposite']) && $post['direct_deposite']==1)? 1 : 0,
                                'whiteboar_id'=> $post['groupboard_id'],
                                'tutor_permission' => $post['tutor_permission'],
                                'sms_status_stop' => $post['sms_status_stop'],
                                'end_subscription' => (isset($d2)) ? $d2:null,
                                'unlimited' => isset($post['unlimited']) ? $post['unlimited']:0,
                            ];

                        }else{
                            $dataToUpdate = [
                                'user_type'=> $post['user_type'],
                                'country_id' => $post['country_id'],
                                'children_number' => isset($post['numOfChild']) ? $post['numOfChild'] : null,
                                'name' => $post['name'],
                                'user_email' => $post['user_email'],
                                'student_grade' => isset($post['grade']) ? $post['grade'] : null,
                                'user_mobile' => $post['full_phone'],
                                'trial_end_date' => $trial_end_date,
                                // 'user_mobile' => $clean['user_mobile'],
                                'SCT_link' => $post['SCT_link'],
                                'subscription_type' => $post['subscription_type'],
                                'suspension_status' => (isset($post['suspension_status']) && $post['suspension_status']==1)? 1 : 0,
                                'direct_deposite' => (isset($post['direct_deposite']) && $post['direct_deposite']==1)? 1 : 0,
                                'whiteboar_id'=> $post['groupboard_id'],
                                'tutor_permission' => $post['tutor_permission'],
                                'sms_status_stop' => $post['sms_status_stop'],'end_subscription' => (isset($d2)) ? $d2:null,
                                'unlimited' => isset($post['unlimited']) ? $post['unlimited']:0,
                            ];
                        }
                        
                        
                        
                        //if course requested, map as registered course with student id
                        if (count($post['course'])) {
                            $courseUserMap = [];
                            foreach ($post['course'] as $course) {
                                $courseUserMap[] = [
                                    'course_id' => $course,
                                    'user_id' => $user_id,
                                    'created' => time(),
                                ];
                            }
                            //remove previous records
                                    //$this->Admin_model->deleteInfo('tbl_registered_course', 'user_id', $user_id);//BY AS
                            //insert new courses
                                    //$this->Admin_model->insertBatch('tbl_registered_course', $courseUserMap);//BY AS
                        }

                     
                        $AdminClass->updateInfo('tbl_useraccount', 'id', $user_id, $dataToUpdate);
                        
                        if( isset($post['direct_deposite']) && $post['direct_deposite']==1 ){
                            $time = time();
                            $this->db->table('tbl_payment')->where('payment_status','pending')->where('user_id',$user_id)->where('PaymentEndDate >',$time)->where('paymentType',3)->update(['payment_status'=>'Completed','PaymentDate'=>$time ]);
                            if($post['user_type'] == 6){
                                $this->set_referral_point_deposit_user($user_id);
                            }
                            
                        }else{
                            $time = time();
                            $this->db->table('tbl_payment')->where('payment_status','Completed')->where('user_id',$user_id)->where('PaymentEndDate >',$time)->where('paymentType',3)->update(['payment_status'=>'pending']);
                        }
                        $this->session->set('success_msg', 'User updated successfully');

                    }else{
                        $this->session->set('error_msg', 'Room has been taken before to other user');
                    }

                }
                
            }else{
                $this->session->set('error_msg', 'Room does not exists');
            }
            return redirect()->to(base_url('edit_user/'.$user_id));
        }
    }

    public function coursesByCountry($countryId, $studentId = 0, $type = ''  , $subscription_type ='')
    {
       
        error_reporting(0);
        $AdminClass = new \AdminClass();

        $courses = $AdminClass->search('tbl_course', ['country_id'=>$countryId]);
        $html = '';

        $currentURL = current_url();
        if (strpos(current_url(),"/Admin/coursesByCountry/") !==false) {
            if (!empty($this->request->uri->getSegment(4))) {
                $add_user = 1;
            }
        }

        $conditions = [
            'user_id'=>$studentId,
            'endTime >'=>time(),
        ];
        
        if ($subscription_type == "trial") {
            $conditions = [
                'user_id'=>$studentId,
            ];
        }
        $studentCourses = $AdminClass->search('tbl_registered_course', $conditions);
        $studentCourses = count($studentCourses) ? array_column($studentCourses, 'course_id') : [];
        
        foreach ($courses as $course) {
            $checked = in_array($course['id'], $studentCourses) ? 'checked' : '';
            // if (!isset($add_user) && $course['id'] == 44) {
            //     $checked = 'checked';

            // }

            if ($subscription_type == "trial") {
                $course['courseCost'] = 0; // rakesh
            }

            $html .= '<li class="text-left" style="width:210px;position: relative;">
            <p style="line-height: 18px;">
            </p><p>'.$course["courseName"].'&nbsp;</p><br>
            <p style="position: absolute;bottom: 10px;">$'.$course['courseCost'].'
            </p>
            <p class="text-right filled-in county_by_course">
            <input class="form-check-input" id="course_1" type="checkbox" name="course[]" value="'.$course['id'].'" '. $checked .'>
            </p>
            </li>';
        }
        if ($type=="edit") {
            //echo 'hit';
            return $html;
        }
        echo $html;
    }

      // add for direct deposit course referral 
      public function set_referral_point_deposit_user($userID){

        $refUsers = $this->db->table('tbl_referral_users')->where('user_id',$userID)->where('status',0)->get()->getRow();
    
        if (!empty($refUsers)) {

            $reffInUser     = $refUsers->user_id;
            $refferByUser   = $refUsers->refferalUser;

            $point = $this->db->table('tbl_admin_points')->where('id',1)->get()->getRow();
            $referralPoint   = $point->referral_point;
            $ref_taken_point = $point->ref_taken_point;


            $checkreffUsers = $this->db->table('product_poinits')->where('user_id',$reffInUser)->get()->getRow();


            if (!empty($checkreffUsers)) {
             $totalPoint = $checkreffUsers->total_point;
             $old_referral_point = $checkreffUsers->referral_point;
             $ckrfuser['referral_point'] = $old_referral_point + $referralPoint;
             $ckrfuser['total_point']    = $referralPoint + $totalPoint;
             $this->db->table('product_poinits')->where('user_id',$reffInUser)->update($ckrfuser);
            }else{
             $ckrfuser['user_id'] = $reffInUser;
             $ckrfuser['referral_point'] = $referralPoint;
             $ckrfuser['total_point']    = $referralPoint;
             $this->db->insert('product_poinits',$ckrfuser);
            }


            $checkrefferByUser = $this->db->table('product_poinits')->where('user_id',$refferByUser)->get()->getRow();

            if (!empty($checkrefferByUser)) {
             $totalByPoint = $checkrefferByUser->total_point;
             $old_referral_point = $checkrefferByUser->referral_point;
             $ckrfByuser['referral_point'] = $old_referral_point + $referralPoint;
             $ckrfByuser['total_point']    = $ref_taken_point + $totalByPoint;
             $this->db->table('product_poinits')->where('user_id',$refferByUser)->update($ckrfByuser);
            }else{
             $ckrfByuser['user_id'] = $refferByUser;
             $ckrfByuser['referral_point'] = $ref_taken_point;
             $ckrfByuser['total_point']    = $ref_taken_point;
             $this->db->table('product_poinits')->insert($ckrfByuser);
            }

            $this->db->table('tbl_referral_users')->where('user_id',$userID)->update(['status' => 1]);
           
        }
    }

    public function userAdd()
    {  
        error_report_check();
        $AdminClass=new \AdminClass();
        $post = $this->request->getVar();
        //$clean = $this->security->xss_clean($post);
        if (!$post) {
            $data['user_type'] = $AdminClass->getAllInfo('tbl_usertype');
            $data['all_country'] = $AdminClass->getAllInfo('tbl_country');

            $data['user_info'] = $AdminClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
            $data['parents'] = $AdminClass->getInfo('tbl_useraccount', 'user_type', 1);
            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
            $data['page'] = 'User List';
            $data['page_section'] = 'User';

            // $data['headerlink'] = $this->load->view('dashboard_template/headerlink', $data, true);
            // $data['leftnav'] = $this->load->view('dashboard_template/leftnav', $data, true);
            // $data['header'] = $this->load->view('dashboard_template/header', $data, true);
            // $data['footerlink'] = $this->load->view('dashboard_template/footerlink', $data, true);

            // $data['maincontent'] = $this->load->view('admin/user/user_add', $data, true);
            return view('admin/user/user_add', $data);
        } else {
            // $input = $this->validate([
            // 'subscription_type' => 'required',
            // 'name' => 'required',
            // 'email' => 'required',
            // 'password'=> 'required',
            // 'country'=> 'required',
            // ]);
            // if($input)
            // {
            //     echo 'not validate';die();
            // }
            if ($post['subscription_type'] == "guest" || $post['subscription_type'] == "signup") {
                $guest_days = (isset($post['guest_days']))?$post['guest_days']:null;
                if ($guest_days > 0) {
                    $d1 = date('Y-m-d');
                    $d2 = date('Y-m-d', strtotime('+'.$guest_days.' days', strtotime($d1)));
                }
            }else if($post['subscription_type'] == "guest"){
                
                $tbl_setting = $this->db->table('tbl_setting')->where('setting_key','days')->get()->getRow();
                $duration = $tbl_setting->setting_value;
                $d1 = date('Y-m-d');
                $d2 = null;
            }else{
                $tbl_setting = $this->db->table('tbl_setting')->where('setting_key','days')->get()->getRow();
                $duration = $tbl_setting->setting_value;
                $d1 = date('Y-m-d');
                $d2 = date('Y-m-d', strtotime('+'.$duration.' days', strtotime($d1)));

            }
        // echo "<pre>";print_r($post);echo $d2;die();

            $dataToSave = [
                'user_type'=> $post['userType'],
                'country_id' => $post['country'],
                'children_number' => isset($post['numOfChild']) ? $post['numOfChild'] : null,
                'name' => $post['name'],
                'user_email' => $post['email'],
                'user_pawd' => md5($post['password']),
                // 'user_mobile' => $clean['mobile'],
                'user_mobile' => $post['full_phone'],
                'SCT_link' => $post['refLink'],
                'created' => time(),
                'subscription_type' => $post['subscription_type'],
                'suspension_status' => (isset($post['suspension_status']) && $post['suspension_status']==1) ? 1:0,
                'parent_id' => isset($post['parentId']) ? $post['parentId']:null,
                'token' => null,
                'image' => null,
                'payment_status' => '',
                'end_subscription' => (isset($d2)) ? $d2:null,
                'unlimited' => isset($post['unlimited']) ? $post['unlimited']:0,
            ];

            if ($post['subscription_type'] == "guest") {
                $settins_Api_key = $AdminClass->getSmsApiKeySettings();
                $settins_sms_messsage = $AdminClass->getSmsType("Tutor Registration");

                $register_code_string = $settins_sms_messsage[0]['setting_value'];
                $message = str_replace( "{{ username }}" , $dataToSave['user_email'] , $register_code_string);
                $message = str_replace( "{{ password }}" ,  $post['password'] , $message);

                $api_key = $settins_Api_key[0]['setting_value'];
                $content = urlencode($message);

                $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $post['full_phone'] . "&content=$content";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 0);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                //execute post
                //$result = curl_exec($ch);
                curl_close($ch);

            }


            $insertedUserId=$AdminClass->insertId('tbl_useraccount', $dataToSave);
            $additionalTableData = array();
            $additionalTableData['tutor_id'] = $insertedUserId;
            $additionalTableData['created_at'] = date('Y-m-d h:i:s');
            $additionalTableData['updated_at'] = date('Y-m-d h:i:s');
            $AdminClass->insertInfo('additional_tutor_info', $additionalTableData);
            //if inserted user is not parent/child then record registered courses
            if ($post['userType']!=1 || $post['userType']!=6) { //parent, student
                if (count($post['course'])) {
                    $courseUserMap = [];
                    foreach ($post['course'] as $course) {
                        $courseUserMap[] = [
                            'course_id' => $course,
                            'user_id' => $insertedUserId,
                            'created' => time(),
                        ];
                    }
                    $AdminClass->insertBatch('tbl_registered_course', $courseUserMap);
                }
            }

            //if inserted user is a parent then insert his/her child too
            $userPass = [];
            if ($post['userType']==1 && $dataToSave['children_number']) {
               
                $totChild = $dataToSave['children_number'];

                for ($a=0; $a<$totChild; $a++) {
                    $pass = substr(md5(rand()), 0, 7);
                    $userPass[] = [
                        'childName' => $post['childName'][$a],
                        'childPass' => $pass,
                        'refLink'   => $post['childSCTLink'][$a],
                    ];

                    $childToSave = [
                        'name' => $post['childName'][$a],
                        'user_email' => explode(' ', $post['childName'][$a])[0], //first part of a name
                        'user_pawd' => md5($pass),
                        'user_mobile' => $post['full_phone'],
                        'parent_id' => $insertedUserId,
                        'user_type' => 6,
                        'country_id' => $post['country'],
                        'student_grade' => $post['childGrade'][$a],
                        'SCT_link' => $post['childSCTLink'][$a],
                        'created' => time(),
                        'subscription_type' => $post['subscription_type'],
                        'end_subscription' => (isset($d2)) ? $d2:null,
                        'unlimited' => isset($post['unlimited']) ? $post['unlimited']:0,
                    ];
                    $childId = $AdminClass->insertInfo('tbl_useraccount', $childToSave);

                    $settins_Api_key = $AdminClass->getSmsApiKeySettings();
                    $settins_sms_messsage = $AdminClass->getSmsType("Parent Registration");

                    $register_code_string = $settins_sms_messsage[0]['setting_value'];
                    $message = str_replace( "{{ username }}" , $dataToSave['user_email'] , $register_code_string);
                    $message = str_replace( "{{ password }}" ,  $post['password'] , $message);
                    $message = str_replace( "{{ C_username }}" , $childToSave['user_email'] , $message);
                    $message = str_replace( "{{ C_password }}" , $pass , $message);

                    $api_key = $settins_Api_key[0]['setting_value'];
                    $content = urlencode($message);

                    $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $post['full_phone'] . "&content=$content";

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

                    //if course requested, map as registered course with student id
                    if (count($post['course'])) {
                        $courseUserMap = [];
                        foreach ($post['course'] as $course) {
                            $courseUserMap[] = [
                                'course_id' => $course,
                                'user_id' => $childId,
                                'created' => time(),
                            ];
                        }
                        $AdminClass->insertBatch('tbl_registered_course', $courseUserMap);
                    }
                }
                //$this->Admin_model->insertBatch('tbl_useraccount', $childToSave);
            }

            //send mail to registered users
            if ($post['userType']) {
                userRegMail($post['name'], $post['userType'], $post['email'], $post['password'], $userPass);
            }
            
            
             if ($_POST['userType'] == 2 ) {
                
                $settins_Api_key = $AdminClass->getSmsApiKeySettings();
                $settins_sms_messsage = $AdminClass->getSmsType("Upper level student");

                $register_code_string = $settins_sms_messsage[0]['setting_value'];
                $message = str_replace( "{{ username }}" , $dataToSave['user_email'] , $register_code_string);
                $message = str_replace( "{{ password }}" ,  $post['password'] , $message);

                $api_key = $settins_Api_key[0]['setting_value'];
                $content = urlencode($message);

                $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $post['full_phone'] . "&content=$content";

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

            if ($_POST['userType'] == 3 ) {
                
                $settins_Api_key = $AdminClass->getSmsApiKeySettings();
                $settins_sms_messsage = $AdminClass->getSmsType("Tutor Registration");

                $register_code_string = $settins_sms_messsage[0]['setting_value'];
                $message = str_replace( "{{ username }}" , $dataToSave['user_email'] , $register_code_string);
                $message = str_replace( "{{ password }}" ,  $post['password'] , $message);

                $api_key = $settins_Api_key[0]['setting_value'];
                $content = urlencode($message);

                $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $post['full_phone'] . "&content=$content";

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
            

            $this->session->set('success_msg', 'User added successfully');
            return redirect()->to(base_url('user_list'));
        }
    }

    public function suspendUser($userId)
    {
        $AdminClass=new \AdminClass();
        $dataToUpdate = ['suspension_status'=>1];
        $AdminClass->updateInfo($table = "tbl_useraccount", "id", $userId, $dataToUpdate);

        $this->session->set('success_msg', 'User suspended successfully.');
        return redirect()->to(base_url('user_list'));
    }

    public function unsuspendUser($userId)
    {
        $AdminClass=new \AdminClass();
        $dataToUpdate = ['suspension_status'=>0];
        $AdminClass->updateInfo($table = "tbl_useraccount", "id", $userId, $dataToUpdate);

        $this->session->set('success_msg', 'Suspension removed successfully.');

        return redirect()->to(base_url('user_list'));
    }

    public function deleteUser()
    {
        $AdminClass=new \AdminClass();
        $post = $this->request->getVar();
        $uId = $post['uId'];
        $userInfo = $AdminClass->getRow("tbl_useraccount", 'id', $uId);
        $uType = $userInfo['user_type'];

        if ($uType==6 || $uType==2) { //1-12 lvl student, $upper lvl student
            //delete student answers
            $AdminClass->deleteInfo("tbl_student_answer", 'st_id', $uId);
            //delete progress
            $AdminClass->deleteInfo("tbl_studentprogress", 'student_id', $uId);
            //delete registered couse
            $AdminClass->deleteInfo("tbl_registered_course", 'user_id', $uId);
            //delete enrollment
            $AdminClass->deleteInfo("tbl_enrollment", 'st_id', $uId);
        } elseif ($uType==3 || $uType==4 || $uType==5 || $uType==7) { //tutor,school, corporate,q-study,
            //delete additional tutor info
            $AdminClass->deleteInfo("additional_tutor_info", 'tutor_id', $uId);
            //delete enrollment
            $AdminClass->deleteInfo("tbl_enrollment", 'sct_id', $uId);
            //delete module
            $AdminClass->deleteInfo("tbl_module", 'user_id', $uId);
            //delete module question
            $AdminClass->deleteInfo("tbl_question", 'user_id', $uId);
            //delete question
            $AdminClass->deleteInfo("tbl_question", 'user_id', $uId);
        } elseif ($uType==1) { //parent
            //get child ids of that parent
            $childIds = $AdminClass->get_all_where('id', "tbl_useraccount", "parent_id", $uId);
            //delete student details of those children
            foreach ($childIds as $child) {
                //delete student answers
                $AdminClass->deleteInfo("tbl_student_answer", 'st_id', $child['id']);
                //delete progress
                $AdminClass->deleteInfo("tbl_studentprogress", 'student_id', $child['id']);
                //delete registered couse
                $AdminClass->deleteInfo("tbl_registered_course", 'user_id', $child['id']);
                //delete enrollment
                $AdminClass->deleteInfo("tbl_enrollment", 'st_id', $child['id']);
            }
            //delete child account of that parent
            $AdminClass->deleteInfo("tbl_useraccount", 'parent_id', $uId);
        }
        //delete user account
        $AdminClass->deleteInfo("tbl_useraccount", 'id', $uId);
    }

    public function extendTrialPeriod()
    {
        $AdminClass=new \AdminClass();
        $post         = $this->request->getVar();
        $userId       = $post['userId'];
        $extendDays   = $post['extendAmound'];
        $user         = $AdminClass->getInfo('tbl_useraccount', 'id', $userId);
        $currentEndDate = isset($user[0]['trial_end_date']) ? $user[0]['trial_end_date'] : date('Y-m-d');
        $dateToSet      = date("Y-m-d", strtotime($currentEndDate. ' +'. $extendDays .'days'));
        $dataToUpdate = ['trial_end_date'=>$dateToSet];
        $AdminClass->updateInfo('tbl_useraccount', 'id', $userId, $dataToUpdate);
    }

    public function packageNotTaken()
    {
        $AdminClass=new \AdminClass();
        $post = $this->request->getVar();
        $userId = $post['userId'];
        $usersCourse = $AdminClass->getInfo('tbl_registered_course', 'user_id', $userId);
        $usersCourse = count($usersCourse)?array_column($usersCourse, 'course_id'):[];
        
        //if user taken some courses then filter the result else take all courses
        if (count($usersCourse)) {
            $courseNotTakenByUser = $AdminClass->whereNotIn('tbl_course', 'id', $usersCourse);
        } else {
            $courseNotTakenByUser = $AdminClass->getAllInfo('tbl_course');
        }
        
        $notTakenCourses = count($courseNotTakenByUser) ? array_column($courseNotTakenByUser, 'id'):[];
        $option = '';
        foreach ($notTakenCourses as $courseId) {
            $courseName = $AdminClass->courseName($courseId);
            $option .= '<option value="'.$courseId.'">' . $courseName . '</option>';
        }
        echo $option;
    }

    public function usersCurrentPackages()
    {
        $AdminClass=new \AdminClass();
        $post = $this->request->getVar();
        $userId = $post['userId'];

        $usersCourse = $AdminClass->getInfo('tbl_registered_course', 'user_id', $userId);
        $usersCourse = count($usersCourse)?array_column($usersCourse, 'course_id'):[];
        $courseNames = '';
        foreach ($usersCourse as $courseId) {
            $courseName = $AdminClass->courseName($courseId);
            $courseNames .= $courseName. ', ';
        }
        $courseNames = rtrim($courseNames, ', ');
        echo $courseNames;
    }

    public function addPackages()
    {
        $AdminClass=new \AdminClass();
        $post = $this->request->getVar();
        //$post = $this->security->xss_clean($post);
        $userId = $post['userId'];
        $pkgSelected = array_column($post['pkgSelected'], 'value');
        $dataToInsert = [];
        foreach ($pkgSelected as $pkgId) {
            $dataToInsert[] = [
                'course_id' => $pkgId,
                'user_id'   => $userId,
                'created'   => time(),
                'cost'      => 0,
            ];
        }
        $lastId  = $AdminClass->insertBatch('tbl_registered_course', $dataToInsert);
        if ($lastId) {
            echo 'success';
        }
    }

    public function trail_list(){
        
        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
        
        $tbl_setting = $this->db->table('tbl_setting')->where('setting_key','days')->get()->getRow();
        $duration    = $tbl_setting->setting_value;
        $date        = date('Y-m-d');
        $d1          = date('Y-m-d', strtotime('-'.$duration.' days', strtotime($date)));
        $trialEndDate= strtotime($d1);
        
        //$data['trial_user_info'] = $this->Admin_model->getInfoTrialUser('tbl_useraccount', 'subscription_type', 'trial',$limit,$offset);
        $data['trial_user_info'] = $AdminClass->getInfoTrialActiveUser('tbl_useraccount', 'subscription_type', 'trial',$trialEndDate,$limit,$offset);
        
        //$data['trial_user_info'] = $this->Admin_model->getInfoTrialUser('tbl_useraccount', 'subscription_type', 'trial',$limit,$offset);

        
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'User List';
        $data['page_section'] = 'User';
        
        return view('admin/user/trial_list',$data);
    }

    public function next_trial_list(){

        error_report_check();
        $AdminClass=new \AdminClass();
        //$list_trail=require_once(APPPATH.'views/email_templates/user_registration.php');

        $offset= $this->request->getVar('offset');
        $limit = 30;
        //$data['trial_user_info'] = $this->Admin_model->getInfoTrialUser('tbl_useraccount', 'subscription_type', 'trial',$limit,$offset);
        $data['trial_user_info'] = $AdminClass->getInfoTrialActiveUser('tbl_useraccount', 'subscription_type', 'trial',$trialEndDate,$limit,$offset);

        if (count($data['trial_user_info']) > 0 ){
            $response = require_once(APPPATH.'views/admin/user/list_trial_user.php');
            echo $response;
        }else{
            echo 'empty';
        }
    }

    public function signup_users(){

        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
        $data['signup_user_list'] = $AdminClass->getAllSignupUsers('tbl_useraccount', 'subscription_type', 'signup',$limit,$offset);

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Signup User List';
        $data['page_section'] = 'User';

        return view('admin/user/notification/signup_list', $data);
    }

    public function next_singup_list()
    {
       
    }

    public function inactive_users(){

        $AdminClass=new \AdminClass();
        $tbl_setting = $this->db->table('tbl_setting')->where('setting_key','days')->get()->getRow();
        $duration = $tbl_setting->setting_value;
        $date = date('Y-m-d');
        $d1  = date('Y-m-d', strtotime('-'.$duration.' days', strtotime($date)));
        $trialEndDate = strtotime($d1);
        
        $limit = 30;
        $offset = 0;
        $inactive_user_info = $AdminClass->getInfoInactiveUser('tbl_useraccount', 'subscription_type', 'signup',$limit,$offset);
        $inactive_trial_user_info = $AdminClass->getInfoInactiveTrialUser('tbl_useraccount', 'subscription_type', 'trial',$trialEndDate,$limit,$offset);
        $margeBoughtInactiveUser = array_merge($inactive_user_info, $inactive_trial_user_info);
        
        $data['inactive_user_info'] = $inactive_trial_user_info;
        // $users = $this->Admin_model->getInfoInactiveUser('tbl_useraccount', 'subscription_type', 'signup',$limit,$offset);
        $users = $inactive_trial_user_info;
        //echo "<pre>";print_r($users);die();
        foreach ($users as $key => $value) {
           $this->db->table('tbl_useraccount')->where('id',$value['id'])->update(['subscription_status' => 0]);
        }

        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Inactive User List';
        $data['page_section'] = 'User';

        return view('admin/user/inactive_users',$data);

    }

    public function next_inactive_users_list(){
        $AdminClass=new \AdminClass();
        $tbl_setting = $this->db->table('tbl_setting')->where('setting_key','days')->get()->getRow();
        $duration = $tbl_setting->setting_value;
        $date = date('Y-m-d');
        $d1  = date('Y-m-d', strtotime('-'.$duration.' days', strtotime($date)));
        $trialEndDate = strtotime($d1);
        
        $offset = $this->request->getVar('offset');
        $limit = 30;
        $inactive_user_info = $AdminClass->getInfoInactiveUser('tbl_useraccount', 'subscription_type', 'signup',$limit,$offset);
        $inactive_trial_user_info = $AdminClass->getInfoInactiveTrialUser('tbl_useraccount', 'subscription_type', 'trial',$trialEndDate,$limit,$offset);
        $margeBoughtInactiveUser = array_merge($inactive_user_info, $inactive_trial_user_info);
        // $data['inactive_user_info'] = $margeBoughtInactiveUser;
        //print_r($inactive_user_info);
        
        if(count($inactive_trial_user_info) < 1){
            echo 'empty';die();
        }
        $output ='';
        foreach ($inactive_trial_user_info as $key => $value) {
            $output .='<div class="col-md-4" style="border: 1px solid lightblue;">';
            $output .='<a href="edit_user/'.$value['id'].'"><p>'.$value['name'].'</p></a>';
            $output .='</div>';
        }
        echo $output;
    }

    public function schoolTutorNext(){
        $offset = $this->request->getVar('val');
        $parent_id  = $this->request->getVar('parent_id');
        $limit = 30;
        $teachers = $this->db->table('tbl_useraccount')->where('parent_id',$parent_id)->limit($limit,$offset)->get()->getResultArray();
        if(count($teachers) < 1){
            echo 'empty';die();
        }
        $output ='';
        foreach ($teachers as $key => $value) {
            $output .='<div class="col-sm-2">';
            $output .='<a  href="edit_user/'.$value["id"].'"> '.$value['name'].'</a>';
            $output .='</div>';
        }
        echo $output;

    }

    public function suspend_users(){

        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
        $data['suspend_user_info'] = $AdminClass->getInfoSuspendUser('tbl_useraccount', 'suspension_status', 1,$limit,$offset);

        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Suspend User List';
        $data['page_section'] = 'User';
  
        return view('admin/user/suspend_users', $data);
    }

    public function next_suspend_users_list(){
        $AdminClass=new \AdminClass();
        $offset     = $this->request->getVar('offset');
        $limit = 30;
        $inactive_user_info = $AdminClass->getInfoSuspendUser('tbl_useraccount', 'suspension_status', 1,$limit,$offset);
        //print_r($inactive_user_info);
        $output ='';
        foreach ($inactive_user_info as $key => $value) {
            $output .='<div class="col-md-4" style="border: 1px solid lightblue;">';
            $output .='<a href="edit_user/'.$value['id'].'"><p>'.$value['name'].'</p></a>';
            $output .='</div>';
        }
        echo $output;
    }

    public function guest_users(){
        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
        $data['guest_user_list'] = $AdminClass->getAllguestUsers('tbl_useraccount', 'subscription_type', 'guest',$limit,$offset);

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Signup User List';
        $data['page_section'] = 'User';


        return view('admin/user/notification/guest_user_list', $data);
        
    }

    public function parent_users(){
        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
        $data['parent_list'] = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'user_type',1,$limit,$offset);

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Signup User List';
        $data['page_section'] = 'User';
        

        return view('admin/user/notification/parent_list', $data);

    }

    public function student_users(){
        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
        $data['student_list'] = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'user_type',6,$limit,$offset);

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Signup User List';
        $data['page_section'] = 'User';
        

        return view('admin/user/notification/students', $data);

    }

    public function upper_level_users(){
        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
        $data['upper_student_list'] = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'user_type',2,$limit,$offset);

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Signup User List';
        $data['page_section'] = 'User';   

        return view('admin/user/notification/upper_student', $data);

    }

    public function tutor_users(){

        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
        $data['tutors_list'] = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'user_type',3,$limit,$offset);

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Signup User List';
        $data['page_section'] = 'User';


        return view('admin/user/notification/tutors', $data);

    }

    public function corporateList(){
        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
        $data['corporate_list'] = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'user_type',5,$limit,$offset);

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Signup User List';
        $data['page_section'] = 'User';
        
        return view('admin/user/notification/corporates', $data);

    }

    public function schoolList(){
        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
        $data['schoolList'] = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'user_type',4,$limit,$offset);

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Signup User List';
        $data['page_section'] = 'User';

        return view('admin/user/notification/schools', $data);
    }

    
    public function student_prize_list(){

        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
        $data['student_prize_list'] = $AdminClass->getInfoPrizeWinerUser($limit,$offset);

        //echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Prize Winner User List';
        $data['page_section'] = 'User';
        
        return view('admin/user/notification/student_prize_list',$data);
    }

    public function next_deposit_users_list(){
        $AdminClass=new \AdminClass();
        $offset = $this->request->getVar('offset');
        $limit = 30;
        $data['next_user_info'] = $AdminClass->getDepositeResources($limit,$offset);
        if (count($data['next_user_info']) > 0) {
            $response =require_once(APPPATH.'views/admin/user/notification/next_users_list.php');
            echo $response;
        }else{
            echo "empty";
        }
    }

    public function depositeResources(){
        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
        $data['list_of_users'] = $AdminClass->getDepositeResources($limit,$offset);

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Direct Deposit(resourse)';
        $data['page_section'] = 'User';


        return view('admin/user/notification/list_of_users', $data);

    }

    public function groupboardResources(){
        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
    
        // check whiteboard
        $data['list_of_users'] = $AdminClass->whiteboardPurchesLists($limit,$offset);

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Groupboard (resourse)';
        $data['page_section'] = 'User';
        

        return view('admin/user/notification/list_of_users', $data);

    }

    public function groupboardTrialList(){
        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
    
        // check whiteboard
        $data['list_of_users'] = $AdminClass->whiteboardPurchesSignupLists('trial',$limit,$offset); 

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Groupboard (trial)';
        $data['page_section'] = 'User';

        return view('admin/user/notification/list_of_users', $data);

    }

    public function groupboardSignup(){
        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
    
        // check whiteboard
        $data['list_of_users'] = $AdminClass->whiteboardPurchesSignupLists('signup',$limit,$offset); 

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Groupboard(signup)';
        $data['page_section'] = 'User';

        return view('admin/user/notification/list_of_users', $data);

    }

    public function tutorCommisionLists(){
        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
    
        // check whiteboard
        $data['list_of_users'] = $AdminClass->tutorCommisionForAssignStudent($limit,$offset); 
        //echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Tutor(commission)';
        $data['page_section'] = 'User';

        return view('admin/user/notification/list_of_users', $data);
    }

    public function vocabularyCommisionLists(){
        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
    
        // check vocabularyCommisionCheck
        $data['list_of_users'] = $AdminClass->vocabularyCommisionCheck($limit,$offset); 

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Vocabulary(commission)';
        $data['page_section'] = 'User';
        
        return view('admin/user/notification/list_of_users', $data);

    }

    public function ninteyPercentageMark(){

        $AdminClass=new \AdminClass();
        $limit = 30;
        $offset = 0;
        
        $checkStudentPercentage = $AdminClass->checkStudentPercentageNotification('daily_modules',$limit,$offset);
        $ninteyPercentageMark = [];
        
        foreach($checkStudentPercentage as $key => $value){
            $total_row = $value['total_row'];
            $percentage = number_format($value['percentage']);
            
            if ($total_row >= 2 && $percentage >= 90){
                //echo $percentage;die;
                $ninteyPercentageMark[$key]['user_id'] = $value['user_id'];
                $ninteyPercentageMark[$key]['name'] = $value['name'];
            }
            
        }
        
        // check whiteboard
        $data['list_of_users'] = $ninteyPercentageMark; 
    
        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Student who score 90% up';
        $data['page_section'] = 'User';
        
        return view('admin/user/notification/list_of_users', $data);

    }

    public function userEmailList(){
        
        $email_messages = $this->db->table('user_message')->select('COUNT(user_id) as total')->where('user_id !=',null)->where('status !=','seen')->groupBy('user_id')->get()->getResultArray();
        
        $data['messages_users'] = $this->db->table('user_message')->where('user_id !=',null)->where('status !=','seen')->groupBy('user_id')->get()->getResultArray();
        // print_r($data['messages_users']);die;
        $data['email_messages'] = count($email_messages);
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'User List';
        $data['page_section'] = 'User';
        
     

        return view('admin/user/notification/user_message_list',$data);
        
    }


    public function creativeUserList(){
        
        $AdminClass=new \AdminClass();
        $creative_registers = $AdminClass->getallcreativeDetails();
        $data['creative_registers']= $creative_registers;

        // echo"<pre>"; print_r($creative_registers);die();

        $data['total_creative'] = count($creative_registers);
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'User List';
        $data['page_section'] = 'User';

        return view('admin/user/notification/creative_user_list', $data);
         
    }
    

    public function  new_idea_create_student(){
        $AdminClass=new \AdminClass();
        $data['creative_students']=$AdminClass->idea_created_student_list();
        $data['created_idea_students']=$AdminClass->idea_created_students();
        
        
        $data['page_title'] = '.:: Q-Study :: New Idea created student...';
        $data['page'] = 'New Idea created student';
        $data['page_section'] = 'User';
        
        return view('admin/user/notification/new_idea_create_student', $data);

    }

    public function  new_idea_create_tutor(){
        
        $AdminClass=new \AdminClass();
        $data['all_tutors'] = $AdminClass->get_all_tutor();
        $data['idea_created_tutor'] = $AdminClass->idea_created_tutor_list();
        // echo "<pre>";print_r($data['all_tutors']);die();
        $data['page_title'] = '.:: Q-Study :: New Idea created student...';
        $data['page'] = 'New Idea created student';
        $data['page_section'] = 'User';
        
        return view('admin/user/notification/new_idea_create_tutor',$data);

    }
    public function  idea_create_tutor_list($tutor_id)
    {
        $AdminClass=new \AdminClass();
        $data['tutor_idea'] = $AdminClass->get_tutor_ideas($tutor_id);
        // echo "<pre>";print_r($data['tutor_idea']);die();

        $data['page_title'] = '.:: Q-Study :: New Idea created student...';
        $data['page'] = 'Idea created Tutor list';
        $data['page_section'] = 'Admin';
        
        return view('admin/user/idea_create_tutor_list',$data);
    }
    public function  idea_create_tutor_setting($tutor_id,$question_id)
    {
        $AdminClass=new \AdminClass();
        $TutorClass = new \TutorClass();

        $data['question_info'] = $TutorClass->getQuestionInfo(17, $question_id);
        $update_notification = $AdminClass->update_question_notification($question_id);
        $data['question_item'] = 17;
        $data['question_id'] = $question_id;
        $data['question_tutorial'] = $TutorClass->getInfo('tbl_question_tutorial', 'question_id', $question_id);
        $data['q_creator_name'] = $TutorClass->getIQuestionCreator($question_id);

        $data['all_grade'] = $TutorClass->getAllInfo('tbl_studentgrade');
        $data['all_subject'] = $TutorClass->getInfo('tbl_subject', 'created_by', $tutor_id);
        $subject_id = $data['question_info'][0]['subject'];

        $data['subject_base_chapter'] = $TutorClass->getInfo('tbl_chapter', 'subjectId', $subject_id);
        if (count($data['question_info'])) {
            $data['allCountry'] = $this->Admin_model->search('tbl_country', [1 => 1]);
            $data['selCountry'] = $data['question_info'][0]['country'];
            $quesSub = $data['question_info'][0]['subject'];
            $quesChap = $data['question_info'][0]['chapter'];
            $chaps = $this->get_chapter_name($quesSub, $quesChap); //selected $quesChap
            $temp = [
                'subject' => $data['question_info'][0]['subject'],
                'chapter' => $chaps,
                'selChapter' => $quesChap,
                'studentGrade' => $data['question_info'][0]['studentgrade'],
            ];
            $this->session->set_flashdata('refPage', 'questionEdit');
            $this->session->set_flashdata('modInfo', $temp);
        }

        $data['ideas'] = $this->tutor_model->getIdeasByQuestion($question_id);
        $data['idea_info'] = $this->tutor_model->getIdeaInfoByQuestion($question_id);
        $data['question_info_ind'] = json_decode($data['question_info'][0]['questionName']);
        $data['tutor_id'] = $tutor_id;

        $qSearchParams = [
            'questionType' => 17,
            'user_id' => $tutor_id,
            'country' => $this->session->userdata('selCountry'),
        ];

        $allQuestionIds = $this->QuestionModel->search('tbl_question', $qSearchParams);
        $allQuestionIds = array_column($allQuestionIds, 'id');
        $data['qIndex'] = array_search($question_id, $allQuestionIds);
        if (!is_int($data['qIndex'])) {
            // redirect('question-list');
        } else {
            $data['qIndex'] += 1;
        }
        // echo "<pre>";print_r($data['tutor_idea']);die();

        $data['page_title'] = '.:: Q-Study :: New Idea created student...';
        $data['page'] = 'Idea created Tutor list';
        $data['page_section'] = 'Admin';
        
        return view('admin/user/idea_create_tutor_setting',$data);
    }
    


    public function country_users_list($id){
        $AdminClass=new \AdminClass();
        //echo $id;die;
        $limit = 30;
        $offset = 0;
        if ($id == 1) {
            $data['country'] = 'Australia';
            $data['countryId'] = 1;
        }else if($id == 2){
            $data['country'] = 'USA';
            $data['countryId'] = 2;

        }else if($id == 8){
            $data['country'] = 'Bangladesh';
            $data['countryId'] = 8;

        }else if($id == 9){
            $data['country'] = 'UK';
            $data['countryId'] = 9;

        }else if($id == 10){
            $data['country'] = 'Canada';
            $data['countryId'] = 10;

        }
        $data['country_users'] = $AdminClass->getUsersTypeWaise('tbl_useraccount','country_id',$id,$limit,$offset);

        // echo "<pre>";print_r($data);die;
        $data['date'] = date('d/m/Y');
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Country Wise User List';
        $data['page_section'] = 'User';
        

        return view('admin/user/notification/country_users', $data);

    }

    public function next_aus_users_list(){
        $AdminClass=new \AdminClass();
        $offset = $this->request->getVar('offset');
        $country_id = $this->request->getVar('country_id');
        $limit = 30;
        $next_user_info = $AdminClass->getUsersTypeWaise('tbl_useraccount', 'country_id',$country_id,$limit,$offset);
        //echo '<pre>';print_r($data['next_user_info']);die();
        if (count($next_user_info) > 0) {
            $response = require_once(APPPATH.'views/admin/user/notification/next_users_list.php');
            echo $response;
        }else{
            echo "empty";
        }
    }

    public function country_list()
    {
        $AdminClass=new \AdminClass();
        $data['all_country'] = $AdminClass->getAllInfo('tbl_country');
        $data['user_info'] = $AdminClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Country List';
        $data['page_section'] = 'Country';


       return view('admin/country/country_list', $data);
    }

    public function save_country()
    {
        $AdminClass=new \AdminClass();
        $data['countryName'] = $this->request->getVar('countryName');
        $data['countryCode'] = $this->request->getVar('countryCode');

        $AdminClass->insertInfo('tbl_country', $data);
        $all_country= $AdminClass->getAllInfo('tbl_country');

        $json = array();
        $json['countryDiv'] = require_once(APPPATH.'views/admin/country/country_div.php');
        echo json_encode($json);
    }

    public function delete_country($country_id)
    {
        $AdminClass=new \AdminClass();
        $AdminClass->deleteInfo('tbl_country', 'id', $country_id);
        $this->db->table('tbl_course')->where('country_id',$country_id)->delete();
        $module_id=$this->db->table('tbl_module')->select('id')->where('country',$country_id)->get()->getResultArray();

        foreach($module_id as $key=>$module_ids)
        {
            $module_id_new[]=$module_ids['id'];
        }

        $this->db->table('tbl_modulequestion')->whereIn('module_id',$module_id_new)->delete();
        $this->db->table('tbl_module')->where('country',$country_id)->delete();

        return redirect()->to(base_url('country_list'));
    }

    public function update_country()
    {
        $AdminClass=new \AdminClass();
        $data['countryName'] = $this->request->getVar('countryName');
        $data['countryCode'] = $this->request->getVar('countryCode');

        $country_id = $this->request->getVar('id');
        $AdminClass->updateInfo('tbl_country', 'id', $country_id, $data);
        $all_country= $AdminClass->getAllInfo('tbl_country');

        $json = array();
        $json['countryDiv'] = require_once(APPPATH.'views/admin/country/country_div.php');

        echo json_encode($json);
    }

    public function country_wise()
    {
        $AdminClass=new \AdminClass();
        $data['all_country'] = $AdminClass->getAllInfo('tbl_country');
        $data['user_info'] = $AdminClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Country Wise';
        $data['page_section'] = 'Country';

        return view('admin/schedule/country_list', $data);
    }

    public function course_schedule($country_id)
    {
        $AdminClass=new \AdminClass();
        $data['country_info'] = $AdminClass->getInfo('tbl_country', 'id', $country_id);

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Country Wise';
        $data['page_section'] = 'Country';


        return view('admin/schedule/country_wise_schedule', $data);
    }

     
    public function directDepositSetting($id){
        $AdminClass=new \AdminClass();
        $post = $this->request->getVar();
        //$clean = $this->security->xss_clean($post);
        if($post){
            $country_id   = $post['country_id'];
            $data['bank_details'] = $post['bank_details'];
            $data['country_id']   = $post['country_id'];
            $data['active_status']   = $post['user_active_status']?$post['user_active_status']:0;
            $checkDepositDetails = $AdminClass->checkDepositDetails('direct_deposit_admin_setting',$country_id);
            if($checkDepositDetails > 0){
                $checkDepositDetails = $AdminClass->checkDepositDetailsUpdate('direct_deposit_admin_setting',$country_id,$data);

                $this->session->set('success_msg', 'Updated successfully');
            }else{
                $checkDepositDetails = $AdminClass->checkDepositDetailsInsert('direct_deposit_admin_setting',$country_id,$data);
                $this->session->set('success_msg', 'Insert successfully');
            }
            return redirect()->to(base_url('/directDepositSetting/'.$country_id));
        }else{
            $data['country_info'] = $AdminClass->getInfo('tbl_country', 'id', $id);
            
            $data['getDepositDetails'] = $AdminClass->getDepositDetails('direct_deposit_admin_setting',$id);
            //echo "<pre>";print_r($data['getDepositDetails']);die();
            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
            $data['page'] = 'Country Wise';
            $data['page_section'] = 'Country';

            return view('admin/schedule/directDepositSetting', $data);     
        }
        
    }

    public function emailBankDetails(){
        $AdminClass=new \AdminClass();
        $data['email_message'] = $this->request->getVar('data');
        $data['country_id'] = $this->request->getVar('country_id');
        $country_id = $this->request->getVar('country_id');
        
        $checkDepositDetails = $AdminClass->checkDepositDetails('direct_deposit_admin_setting',$country_id);
        if($checkDepositDetails > 0){
            $checkDepositDetails = $AdminClass->checkDepositDetailsUpdate('direct_deposit_admin_setting',$country_id,$data);
            
            echo '<div class="alert alert-success msg_success_add">Updated successfully</div>';
        }else{
            $checkDepositDetails = $AdminClass->checkDepositDetailsInsert('direct_deposit_admin_setting',$country_id,$data);
            
            echo '<div class="alert alert-success msg_success_add">Insert successfully</div>';
        }
    }
    

    public function inboxBankDetails(){
        $AdminClass=new \AdminClass();
        $data['inbox_message'] = $this->request->getVar('data');
        $data['country_id'] = $this->request->getVar('country_id');
        $country_id = $this->request->getVar('country_id');
        
        $checkDepositDetails = $AdminClass->checkDepositDetails('direct_deposit_admin_setting',$country_id);
        if($checkDepositDetails > 0){
            $checkDepositDetails = $AdminClass->checkDepositDetailsUpdate('direct_deposit_admin_setting',$country_id,$data);
            
            echo '<div class="alert alert-success msg_success_add">Updated successfully</div>';
        }else{
            $checkDepositDetails = $AdminClass->checkDepositDetailsInsert('direct_deposit_admin_setting',$country_id,$data);
            
            echo '<div class="alert alert-success msg_success_add">Insert successfully</div>';
        }
    }

    public function add_course_schedule()
    {
        $AdminClass=new \AdminClass();
        $data['subscription_type'] = -1;//$this->input->post('subscription_type');
        $data['user_type'] = $this->request->getVar('user_type');
        $data['country_id'] = $this->request->getVar('country_id');

        $data['country_info'] = $AdminClass->getInfo('tbl_country', 'id', $data['country_id']);
        // $data['course_info'] = $this->Admin_model->get_course($data['subscription_type'], $data['user_type'], $data['country_id']);
        $data['course_info'] = $AdminClass->get_course($data['user_type'], $data['country_id']);

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Country Wise';
        $data['page_section'] = 'Country';

        return view('admin/schedule/add_course',$data);
    }

    public function save_course_schedule()
    {
     
        $AdminClass=new \AdminClass();
        $data['country_id'] = $_POST['country_id'];
        $data['courseName'] = strip_tags(trim(html_entity_decode($_POST['courseName'], ENT_QUOTES)));
        $data['courseCost'] = $_POST['courseCost'];
        $data['is_enable'] = $_POST['is_enable'];
        $data['user_type'] = $_POST['user_type'];
        // $data['subscription_type'] = $_POST['subscription_type'];

        $course_id = $_POST['course_id'];
        //        echo $course_id;print_r($data);die;
        if ($course_id != '') {
            $AdminClass->updateInfo('tbl_course', 'id', $course_id, $data);
        } else {
            $course_id = $AdminClass->insertId('tbl_course', $data);
        }
      

        $course_details = $AdminClass->getInfo('tbl_course', 'id', $course_id);
        $box_num= $_POST['box_num'];

        $view_template=require_once(APPPATH.'views/admin/schedule/course_content_div.php');
        $json['course_id'] = $course_id;
        $json['course_content_div'] =  $view_template;
        echo json_encode($json);
    }

    public function duplicateCountry()
    { 
        //die('jiiii');
        $AdminClass=new \AdminClass();
        $post = $this->request->getVar();
        $oldCountry = isset($post['oldCountry']) ? $post['oldCountry'] : '';
        $newCountry = isset($post['newCountry']) ? $post['newCountry'] : '';
        // echo $oldCountry;
        // echo $newCountry;die();
        $newCountryCode = isset($post['newCountryCode']) ? $post['newCountryCode'] : '';
        $oldInfo = $AdminClass->search('tbl_country', ['countryName'=>$oldCountry]);
        $all_course = $this->db->table('tbl_course')->where('country_id',$oldInfo[0]['id'])->get()->getResultArray();
        //  echo '<pre>';
        //  print_r($all_course);
        //  die();
        $dataToInsert = [
            'countryName'=>$newCountry,
            'countryCode'=>$newCountryCode,
        ];
        $newCountryId = $AdminClass->insertInfo('tbl_country', $dataToInsert);
        if(isset($all_course))
        {
            foreach($all_course as $all_courses){
                $data_new['courseName']=$all_courses['courseName'];
                $data_new['year/grade']=$all_courses['year/grade'];
                $data_new['courseCost']=$all_courses['courseCost'];
                $data_new['created']=time();
                $data_new['country_id']=$newCountryId;
                $data_new['is_enable']=$all_courses['is_enable'];
                $data_new['user_type']=$all_courses['user_type'];
                $data_new['subscription_type']=$all_courses['subscription_type'];
                $this->db->table('tbl_course')->insert($data_new);
            } 
        }

        //echo $oldInfo[0]['id'];die();
        // all module with old country
        $conditions = [
            'country' => $oldInfo[0]['id']
        ];
        
        $lastInsert = $AdminClass->copy('tbl_module', $conditions, 'country', $newCountryId);
        
        //old new module map
        $oldModules = $AdminClass->search('tbl_module', $conditions);
        $totCopy = count($oldModules);
        $moduleMap = [];
        foreach ($oldModules as $old) {
            $moduleMap[] = [
                'old' => $old['id'],
                'new' => $lastInsert++,
            ];
        }
        //module question copy
        $this->moduleQuestionCopy($moduleMap, 0, $newCountryId);
        
        $this->session->set('success_msg', 'Country duplicated successfully');
        return redirect()->to(base_url('country_wise'));
    }

    public function moduleQuestionCopy($moduleMap, $newGrade = 0, $newCountry = 0)
    {
        $AdminClass=new \AdminClass();
        foreach ($moduleMap as $map) {
            $oldModuleQues = $AdminClass->search('tbl_modulequestion', ['module_id'=>$map['old']]);
            
            if (count($oldModuleQues)) {
                foreach ($oldModuleQues as $question) {
                    if ($newGrade) {
                        $changeCol='studentgrade';
                        $changeVal = $newGrade;
                        $newQuesId = $AdminClass->copy('tbl_question', ['id'=>$question['question_id']], $changeCol, $changeVal);
                    } elseif ($newCountry) {
                        $changeCol='country';
                        $changeVal = $newCountry;
                        $newQuesId = $AdminClass->copy('tbl_question', ['id'=>$question['question_id']], $changeCol, $changeVal);
                    }


                    $dataToInsert = [
                        'question_id'=>$newQuesId,
                        'question_type'=>$question['question_type'],
                        'module_id' => $map['new'],
                        'question_order' => $question['question_order'],
                        'created' =>time(),
                    ];
                    $AdminClass->insertInfo('tbl_modulequestion', $dataToInsert);
                }
            }
        }
    }

    public function duplicateGrade()
    {
        $AdminClass=new \AdminClass();
        $post  = $this->request->getVar();

        $oldCountry = isset($post['oldCountry']) ? $post['oldCountry'] : '';
        $newCountry = isset($post['newCountry']) ? $post['newCountry'] : '';
        $oldInfo = $AdminClass->search('tbl_country', ['countryName'=>$oldCountry]);
        $conditions = [
            'studentGrade' => $post['oldGrade'],
            'country' => $oldInfo[0]['id'],
        ];
        $changeCol = 'studentGrade';
        $changeVal = $post['newGrade'];
        $lastInsert = $AdminClass->copy('tbl_module', $conditions, $changeCol, $changeVal);
        
        //old new module map
        $oldModules = $AdminClass->search('tbl_module', $conditions);
        $totCopy = count($oldModules);
        $moduleMap = [];
        foreach ($oldModules as $old) {
            $moduleMap[] = [
                'old' => $old['id'],
                'new' => $lastInsert++,
            ];
        }

        //module question copy
        $this->moduleQuestionCopy($moduleMap, $post['newGrade'], 0);

        $this->session->set('success_msg', 'Grade duplicated successfully');
        return redirect()->to(base_url('country_wise'));
    }

    public function product_list(){
        $data['products'] = $this->db->table('tbl_products')->get()->getResultArray();
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Product List';
        $data['page_section'] = 'Product';;

        return view('admin/product/product_list', $data);

    }

    public function product_add(){
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Product List';
        $data['page_section'] = 'Product';
        
        return view('admin/product/add_product', $data);

    }

    
    public function add_product_submit(){
        
        // echo '<pre>';
        // print_r($this->validation->getError('name'));die();   
        if(!$this->validate('productValidate'))     
        {
            $data['validation'] = $this->validator;

            return view('admin/product/add_product', $data);
        }   
        else
        {
            $post  = $this->request->getVar();
            $image_file=$this->request->getFile('image');

            if ($image_file)
            {
                $image_name = $image_file->getRandomName();
                $image=$image_file->move(ROOTPATH . 'public/img/product/',$image_name);
                $Updata = array('image' => $image_name);
            }
    
            $Updata['product_title'] = isset($post['product_title'])?$post['product_title']:'';
            $Updata['product_details'] = isset($post['product_details'])?$post['product_details']:'';
            $Updata['product_point'] = isset($post['product_point'])?$post['product_point']:'';

            // echo '<pre>';
            // print_r($Updata);die();
            $this->db->table('tbl_products')->insert($Updata);
            //echo"<pre>";print_r($data);die;
            $this->session->set('success', 'Product Create Successfully');
            return redirect()->to(base_url('product_list'));
        }    
    }

    public function edit_product($id){

        $data['product'] = $this->db->table('tbl_products')->where('id',$id)->get()->getRow();
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Product Edit';
        $data['page_section'] = 'Product';

        return view('admin/product/edit_product', $data);
    }

    public function edit_product_submit(){
        //load file helper
        $post  = $this->request->getVar();
        $id = $post['id'];

        if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name']) ) {
            $image_file=$this->request->getFile('image');
            $image_name = $image_file->getRandomName();
            $image=$image_file->move(ROOTPATH . 'public/img/product/',$image_name);
            $Updata = array('image' => $image_name);
        }

        $Updata['product_title'] = isset($post['product_title'])?$post['product_title']:'';
        $Updata['product_details'] = isset($post['product_details'])?$post['product_details']:'';
        $Updata['product_point'] = isset($post['product_point'])?$post['product_point']:'';

        //echo"<pre>";print_r($Updata);die;
        $this->db->table('tbl_products')->where('id',$id)->update($Updata);
        $this->session->set('success', 'Product Update Successfully');
        return redirect()->to(base_url('product_list'));

    }


    public function delete_product($id){

        $product = $this->db->table('tbl_products')->where('id',$id)->get()->getRow();
        $path = base_url().'/img/product/'.$product->image;
        $update = $this->db->table('tbl_products')->where('id',$id)->delete();
        if ($update) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
        $this->session->set('success', 'Product Delete Successfully');
        return redirect()->to(base_url('product_list'));
    }


    public function product_point_admin(){

        $data['point'] = $this->db->table('tbl_admin_points')->get()->getRow();
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Product Point';
        $data['page_section'] = 'Product';

        return view('admin/product/product_point', $data);

    }

    public function update_product_point(){
        $data = $this->request->getVar();
        $this->db->table('tbl_admin_points')->where('id',1)->update($data);
        $this->session->set('success', 'Point Update Successfully');
        return redirect()->to(base_url('product_point_admin'));
    }

    public function contact_mail()
    {
        $AdminClass=new \AdminClass();
        $contacts_all = $AdminClass->getAllInfo('user_message');
        $all_contacts = array();
        foreach($contacts_all as $key => $val){
            $all_contacts[$key]['id']    = $val['id'];
            $all_contacts[$key]['user_name']    = $val['user_name'];
            $all_contacts[$key]['user_email']   = $val['user_email'];
            $all_contacts[$key]['message_body'] = $val['message_body'];
            $all_contacts[$key]['sent_at']      = $val['sent_at'];
            $all_contacts[$key]['updated_at']   = $val['updated_at'];
            $all_contacts[$key]['refLink']      = $val['refLink'];
            $all_contacts[$key]['feedback_topic'] = $val['feedback_topic'];
            $all_contacts[$key]['user_id']      = $val['user_id'];
            $all_contacts[$key]['status']       = $val['status'];
            $contacts_unique_id = $val['unique_id'];
            $all_contacts[$key]['files']       = $this->db->table('feedback_files')->where('unique_id',$contacts_unique_id)->get()->getResultArray();
        }
        $data['all_contacts'] = $all_contacts;
        $data['user_info'] = $AdminClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Contact List';
        $data['page_section'] = 'Contact';


        return view('admin/contacts/contact_list', $data);
    }
    
    public function deleteContactMessage(){
        $id = $this->request->getVar('val');

        $this->db->table('user_message')->where('id',$id)->delete();
        echo 1;
    }

        
    public function contact_info()
    {
        $post = $this->request->getVar();

        if (isset($post) && !empty($post)) {
            $data['setting_value'] = $this->request->getVar('contact');
            $this->db->table('tbl_setting')->where('setting_key','contact_email')->update($data);

            $this->session->set('success_msg', 'Contact email updated successfully');

        }
        $data['contacts_email'] = $this->db->table('tbl_setting')->where('setting_key','contact_email')->get()->getRow();
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['page'] = 'Contact Mail';
        $data['page_section'] = 'Contact';

        return view('admin/contacts/contact_info',$data);
    }
    
    public function dictionaryWordList()
    {
        $QuestionClass=new \QuestionClass();
        $allWord = $QuestionClass->groupedWordItems(); 
        $data['wordChunk'] = [];
        foreach ($allWord as $word) {
            $data['wordChunk'][$word['creator_id']][] = $word;
        }
        
        $data['page'] = 'WordList';
        $data['page_section'] = 'pay Tutor';
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('admin/q-dictionary/wordlist', $data);
    }


    public function wordForDataTable()
    {
        $QuestionClass=new \QuestionClass();
        $post = $this->request->getVar();
        $offset = isset($post['start']) ? $post['start']  : 0;
        $limit = isset($post['length']) ? $post['length'] : 5;
        
        $allWord = $QuestionClass->groupedWordItems($limit, $offset);
        
        $data['wordChunk'] = [];
        //$data=0;
        foreach ($allWord as $word) {
            $data['wordChunk'][$word['creator_id']][] = $word;
        }
        $data['data'] =[];
        foreach ($data['wordChunk'] as $userChunk) {
            $cnt=0;
            foreach ($userChunk as $word) {
                $checked = $word['word_approved'] ? "checked":"";
                $word['sl'] = ++$cnt;
                $word['ques_created_at'] = date('d M Y', $word['ques_created_at']);
                $word['view'] ='<a href="q-dictionary/approve/'.$word['word_id'].'">View</a>';
                $word['select'] = '<input class="approvalCheck" wordId="'.$word['word_id'].'" type="checkbox" name="" '.$checked.'>';
                $word['delete'] = '<a href="#" wordId="'.$word['word_id'].'" class="wordDel"> <i class="fa fa-times"></i> </a>';
                $data['data'][] = $word;
            }
        }
        //$data['draw'] =1;
        $data['recordsTotal'] =$QuestionClass->countDictWord();
        $data['recordsFiltered'] =$data['recordsTotal'];
        echo json_encode($data);
    }

    public function wordApprove($wordId)
    {
        $QuestionClass=new \QuestionClass();
        $word = $QuestionClass->search('tbl_question', ['id'=>$wordId]);
        if (!$word) {
            return view('errors/html/error_404');
        }
        $word = $word[0];
        $creatorId = $word['user_id'];
        //approve word
        $QuestionClass->update('tbl_question', 'id', $wordId, ['word_approved'=>1]);

        $dicPayInfo = $QuestionClass->search('dictionary_payment', ['word_creator'=>$creatorId]);
        
        //if user exists on dictionary_payment table then update approved word count
        //else insert
        if (count($dicPayInfo[0])) {
            $approvedBefore = $dicPayInfo[0]['total_approved'];
            $dataToUpdate = [
                'total_approved'=> $approvedBefore+1,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->QuestionModel->update(
                'dictionary_payment',
                'word_creator',
                $creatorId,
                $dataToUpdate
            );
        } else {
            $dataToInsert = [
                'word_creator' =>  $creatorId,
                'total_approved' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $QuestionClass->insert('dictionary_payment', $dataToInsert);
        }

        echo 'true';
    }

    public function wordReject($wordId)
    {
        $QuestionClass=new \QuestionClass();
        $word = $QuestionClass->search('tbl_question', ['id'=>$wordId]);
        if (!$word) {
            return view('errors/html/error_404');
        }
        $word = $word[0];
        $creatorId = $word['user_id'];

        $QuestionClass->update('tbl_question', 'id', $wordId, ['word_approved'=>0]);
        $dicPayInfo = $QuestionClass->search('dictionary_payment', ['word_creator'=>$creatorId]);
        
        //if user exists on dictionary_payment table then update approved word count
        //else insert
        if ($dicPayInfo) {
            $approvedBefore = isset($dicPayInfo[0]['total_approved'])?$dicPayInfo[0]['total_approved'] : 0;
            $dataToUpdate = [
                'total_approved'=> max(0, $approvedBefore-1),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $QuestionClass->update(
                'dictionary_payment',
                'word_creator',
                $creatorId,
                $dataToUpdate
            );
        }
        echo 'true';
    }

    public function dictionaryPayment()
    {
        $QuestionClass=new \QuestionClass();
        $data['toPay'] = $QuestionClass->wordCreatorToPay();
        
        $data['page'] = 'payTutor';
        $data['page_section'] = 'pay Tutor';
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
   
        return view('admin/q-dictionary/pay_tutor', $data);

    }
    
    public function payTutor()
    {
        $QuestionClass=new \QuestionClass();
        $post = $this->request->getVar();
        //$clean = $this->security->xss_clean($post);
        $creator = $post['creator'];
        
        $conditions = ['word_creator'=>$creator];
        $paymentHistory = $QuestionClass->search('dictionary_payment', $conditions);
        $payable = isset($paymentHistory) ? $paymentHistory[0]['total_approved']-$paymentHistory[0]['total_paid']:0;
        
        //VOCABULARY_PAYMENT=50
        if ($payable>=VOCABULARY_PAYMENT) {
            $dataToUpdate = [
                'total_paid' => $paymentHistory[0]['total_paid']+VOCABULARY_PAYMENT,
            ];
            $QuestionClass->update('dictionary_payment', 'word_creator', $creator, $dataToUpdate);
            echo 'true';
        } else {
            echo 'false';
        }   
    }

    public function addDialogue()
    {
        $AdminClass=new \AdminClass();
        $post = $this->request->getVar();
      
        if (!$post) {
           /// $this->session->remove('success_msg');
            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
            return view('dialogue/add_dialogue', $data);
        } else {
            // echo '<pre>';
            // print_r($post);die();
            date_default_timezone_set(TIME_ZONE);

            if (!$this->validate('addDialogueValidate')) {

                $this->session->set('error_msg', 'Dialogue body required');
                return redirect()->to(base_url('dialogue/add'));
            }
            $dataToInsert = [
                'body'=>$post['dialogue_body'],
                'show_whole_year' => isset($post['year']) ? $post['year'] : '',
                'date_to_show' => isset($post['date']) ? $post['date'] : '',
                'link' => isset($post['link']) ? $post['link'] : '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $insId = $AdminClass->insertInfo('dialogue', $dataToInsert);
            if ($insId) {
                //$this->session->remove('error_msg');
                $this->session->set('success_msg', 'Dialogue Added Successfully');
            } else {
                $this->session->set('error_msg', 'Dialogue Not Added');
            }
            return redirect()->to(base_url('dialogue/add'));
        }
    }

    public function allDialogue()
    {
        $AdminClass=new \AdminClass();
        $data['allDialogue'] = $AdminClass->getAllInfo('dialogue');

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
       return view('dialogue/all_dialogue', $data);
    }

    public function deleteDialogue($id)
    {
        $AdminClass=new \AdminClass();
        $AdminClass->deleteInfo('dialogue','id', $id);
    }


    public function add_auto_repeat()
    {
        $AdminClass=new \AdminClass();
        $data = array();
        $diaId = $this->request->getVar('diaId');
    
        $data['auto_repeat'] = $this->request->getVar('autoRepeat');
        $repeat_log['dia_Id'] = $diaId;
        $result = $AdminClass->updateInfo('dialogue','id',$diaId,$data);
        echo json_encode('Insert Successfully');
    }

    public function smsApiAdd()
    {
        $AdminClass=new \AdminClass();
        $data_ai['sms_api_key'] = $this->request->getVar('sms_api_key');
        $a_settings_grop = 'sms_api_settings';
        if (!$data_ai['sms_api_key']) {
            $data['settins_Api_key'] = $AdminClass->getSmsApiKeySettings();
            
            $data['settins_sms_messsage'] = $AdminClass->getSmsMessageSettings();
            
            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        
           return view('dialogue/smsSetting', $data);
        } else {
            foreach ($data_ai as $a_settings_key => $a_settings_value) {
                $rs['res'] = $AdminClass->updateSmsApiSettings($a_settings_grop, $a_settings_key, $a_settings_value);
            }

            $this->session->set('success_msg', 'Updated Successfully');
            
            return redirect()->to(base_url('sms_api/add'));
        }
    }
    
    public function sms_message()
    {
        $AdminClass=new \AdminClass();
        $data_ai['register_sms'] = $this->request->getvar('register_sms');
        $data_ai['9_pm_Sms'] = $this->request->getvar('9_pm_Sms');
        $data_ai['user_adds_sms'] = $this->request->getvar('user_adds_sms');
        $a_settings_grop = 'sms_message_settings';
        foreach ($data_ai as $a_settings_key => $a_settings_value) {
            $rs['res'] = $AdminClass->updateSmsApiSettings($a_settings_grop, $a_settings_key, $a_settings_value);
        }
        $this->session->set('success_msg', 'Updated Successfully');
        return redirect()->to(base_url('sms_api/add'));
    }


    public function sms_templetes()
    {
        $AdminClass=new \AdminClass();
        $data['user_info'] = $AdminClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['templets'] = $AdminClass->getInfo('tbl_setting', 'setting_type', "sms_message_settings_temp");

        return view('admin/sms/index',$data);
    }


    public function edit_templete($id)
    {
        $AdminClass=new \AdminClass();
        if (isset($_POST) && !empty($_POST) ) {

            if (!$this->validate('edit_templeteValidate')) {
                $this->session->set('Failed', 'Templete Can not be empty !');
                return redirect()->to($_SERVER['HTTP_REFERER']);
            }else{
                $AdminClass->updateInfo('tbl_setting', 'setting_id', $id , $_POST);
                $this->session->set('message', 'Successfully Updated');
                return redirect()->to($_SERVER['HTTP_REFERER']);
            }

        }else{

            $data['user_info'] = $AdminClass->getInfo('tbl_useraccount','id',$this->session->get('user_id'));
            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';           
            $data['templets'] = $AdminClass->getInfo('tbl_setting', 'setting_id', $id);
            return view('admin/sms/edit', $data);
        }
        
    }

    public function sms_templetes_status()
    {
        $AdminClass=new \AdminClass();
        if (isset($_POST) && !empty($_POST) ) {

            if ($this->validate('sms_templetes_templeteValidate')) {

                $AdminClass->updateInfo('tbl_setting', 'setting_key', "Template Activate Status" , $_POST);
                $this->session->set('message', 'Successfully Updated');
                return redirect()->to($_SERVER['HTTP_REFERER']);
            }

        }else{

            $data['user_info'] = $AdminClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
            

            $data['templets'] = $AdminClass->getInfo('tbl_setting', 'setting_type', "Template Activate Status");
            return view('admin/sms/status', $data);

        }
        
    }

    public function trial_period()
    {
        error_report_check();
        $AdminClass=new \AdminClass();
        $post = $this->request->getVar();
        if (!$post) {
            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';          
            $data['trial_configuration'] = $AdminClass->getInfo('tbl_setting', 'setting_type', 'trial_period');
            return view('admin/trial_period/add_trial_period',$data);
        } else {
            $trial_unlimited = [
                'setting_type' => 'trial_period',
                'setting_key' => 'unlimited',
                'setting_value' => isset($post['unlimited']) ? $post['unlimited'] : 0
            ];
            $trial_days = [
                'setting_type' => 'trial_period',
                'setting_key' => 'days',
                'setting_value' => isset($post['days']) ? $post['days'] : 0
            ];
            
            $trial_configuration = $AdminClass->getInfo('tbl_setting', 'setting_type', 'trial_period');
            if ($trial_configuration) {
                    //updateInfo($table, $colName, $colValue, $data)
                $AdminClass->updateInfo('tbl_setting', 'setting_key', 'unlimited', $trial_unlimited);
                $AdminClass->updateInfo('tbl_setting', 'setting_key', 'days', $trial_days);
            } else {
                $AdminClass->insertInfo('tbl_setting', $trial_unlimited);
                $AdminClass->insertInfo('tbl_setting', $trial_days);
            }
            if ($insId) {
                $this->session->set('success_msg', 'Dialogue Added Successfully');
            }
            return redirect()->to(base_url('trial_period'));
        }
    }

    public function add_groupboard()
    {
        $AdminClass=new \AdminClass();
        $data['user_info'] = $AdminClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('admin/groupboard/add', $data);
    }

    public function store_groupboard()
    {
      $AdminClass=new \AdminClass();  
      if ($this->validate('store_groupboardValidate')) {
            $AdminClass->insertTbl($_POST,'tbl_available_rooms');
            $this->session->set('message', 'successfully Uploaded');
            return redirect()->to($_SERVER['HTTP_REFERER']);
        }else{
            $this->session->set('Failed', 'This Groupboard Number Has Allready Been Taken '); 
            return redirect()->to($_SERVER['HTTP_REFERER']);
        }
    }

    public function all_groupboard()
    {
        $FaqClass=new \FaqClass(); 
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $glist = $FaqClass->allData('tbl_available_rooms');
        $result = array();

        foreach ($glist as $key => $value) {

            $roomIDTaken = $FaqClass->roomIDTaken($value['room_id']);

            if ( count($roomIDTaken) > 0 ) {
                $result[] = [
                    "id" => $value['id'],
                    "in_use" => $value['in_use'],
                    "room_id" => $value['room_id'],
                    "checked" => 1,
                    "user_email" => $roomIDTaken[0]['user_email'],
                    "subscription_type" => $roomIDTaken[0]['subscription_type']
                ]; 
            }else{

                $result[] = [
                    "id" => $value['id'],
                    "in_use" => $value['in_use'],
                    "room_id" => $value['room_id'],
                    "checked" => 0,
                    "user_email" => "",
                    "subscription_type" => ""
                ]; 

            }
        }

        $data['glist'] = $result;

        return view('admin/groupboard/all', $data);
    }

    public function deleteGroupboard($id='')
    {
        $FaqClass=new \FaqClass(); 
        $FaqClass->deleteVideo($id,'tbl_available_rooms');
        $this->session->set('message', 'Delted Successfully');
        return redirect()->to($_SERVER['HTTP_REFERER']);
    }

    public function edit_groupboard($id)
    {
        $FaqClass=new \FaqClass(); 
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['data'] = $FaqClass->selectData($id,'tbl_available_rooms');

        return view('admin/groupboard/edit',$data);
    }

    public function update_groupboard()
    {
  
        $FaqClass=new \FaqClass(); 
        if ($this->validate('store_groupboardValidate')) {

            $FaqClass->videoHelpeUpdate($_POST['id'] , 'tbl_available_rooms' , $_POST);
            $this->session->set('message', 'successfully Updated');
            return redirect()->to($_SERVER['HTTP_REFERER']);
        }
    }

    public function assignGroupBoard($groupboard_id)
    {
        $FaqClass=new \FaqClass(); 
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $glist = $FaqClass->allData('tbl_useraccount');
        $data['glist'] = $glist;
        $data['groupboard_id'] = $groupboard_id;

        return view('admin/groupboard/groupBoardAssign',$data);
    }

    public function storeGroupBoard()
    {
  
        error_report_check();
        $AdminClass=new \AdminClass(); 
        $data['whiteboar_id'] = $_POST['groupboard_id'];
        $data_2['whiteboar_id'] = 0;

        $datass = $AdminClass->get_all_where('id' , 'tbl_useraccount' , 'whiteboar_id' , $_POST['groupboard_id']);
        //echo 'hiii';die();
        $AdminClass->updateInfo('tbl_useraccount' , 'id' , $datass[0]['id'] , $data_2);
        $AdminClass->updateInfo('tbl_useraccount' , 'id' , $_POST['user_id'] , $data);

        $this->session->set('message', 'Updated Successfully');
        return redirect()->to($_SERVER['HTTP_REFERER']);
    }

    public function qStudyStripeSetting()
    {
        $AdminClass=new \AdminClass(); 

        $data['stripe_info'] = $AdminClass->getInfo('tbl_setting', 'setting_type',  "stripe" );
        $data['user_info_main'] = $AdminClass->getInfo('tbl_setting', 'setting_key',  "qstudyPasswordMain" );
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
    
        return view('admin/payment_setting/stripeSetting',$data);
    }

    public function stripeDetailsUpdate()
    {
        $AdminClass=new \AdminClass(); 
        if (isset($_POST['submit']) == "submit") {
            $AdminClass->updateInfoStripe('tbl_setting', 'setting_key', "test_publish_key", ['setting_value'=> $_POST['test_publish_key']]);
            $AdminClass->updateInfoStripe('tbl_setting', 'setting_key', "test_seccreet_key", ['setting_value'=> $_POST['test_seccreet_key']]);
            $AdminClass->updateInfoStripe('tbl_setting', 'setting_key', "live_publish_key", ['setting_value'=> $_POST['live_publish_key']]);
            $AdminClass->updateInfoStripe('tbl_setting', 'setting_key', "live_seccreet_key", ['setting_value'=> $_POST['live_seccreet_key']]);
            $AdminClass->updateInfoStripe('tbl_setting', 'setting_key', "mode", ['setting_value'=> $_POST['mode']]);


            $this->session->set('stripe-success', 'Updated Successfully');

        }

        $data['stripe_info'] = $AdminClass->getInfo('tbl_setting', 'setting_type',  "stripe" );
        $data['user_info_main'] = $AdminClass->getInfo('tbl_setting', 'setting_key',  "qstudyPasswordMain");
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('admin/payment_setting/stripeSetting',$data);
    }
    
    public function qStudyPaypalSetting()
    {
        $AdminClass=new \AdminClass();
        $data['paypal_info'] = $AdminClass->getInfo('tbl_setting','setting_type',"paypal" );
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';      

        return view('admin/payment_setting/paypalSetting',$data);

    }

    public function paypalDetailsUpdate()
    {
        $AdminClass=new \AdminClass();
        if (isset($_POST['submit']) == "submit") {

            $AdminClass->updateInfoPaypal('tbl_setting', 'setting_key', "test_url", ['setting_value'=> $_POST['test_url']]);
            $AdminClass->updateInfoPaypal('tbl_setting', 'setting_key', "test_business_account", ['setting_value'=> $_POST['test_business_account']]);
            $AdminClass->updateInfoPaypal('tbl_setting', 'setting_key', "test_paypal_secret", ['setting_value'=> $_POST['test_paypal_secret']]);
            $AdminClass->updateInfoPaypal('tbl_setting', 'setting_key', "test_paypal_signature", ['setting_value'=> $_POST['test_paypal_signature']]);
            $AdminClass->updateInfoPaypal('tbl_setting', 'setting_key', "mode", ['setting_value'=> $_POST['mode']]);
            $AdminClass->updateInfoPaypal('tbl_setting', 'setting_key', "live_url", ['setting_value'=> $_POST['live_url']]);
            $AdminClass->updateInfoPaypal('tbl_setting', 'setting_key', "live_business_account", ['setting_value'=> $_POST['live_business_account']]);
            $AdminClass->updateInfoPaypal('tbl_setting', 'setting_key', "live_paypal_secret", ['setting_value'=> $_POST['live_paypal_secret']]);
            $AdminClass->updateInfoPaypal('tbl_setting', 'setting_key', "live_paypal_signature", ['setting_value'=> $_POST['live_paypal_signature']]);

       
            $this->session->set('paypal-success', 'Updated Successfully');

        }

        $data['paypal_info'] = $AdminClass->getInfo('tbl_setting', 'setting_type',  "paypal" );
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';


        return view('admin/payment_setting/paypalSetting', $data);
    } 


    public function qStudyPassword()
    {

        $AdminClass=new \AdminClass();
        $getEncryptPW  = $this->db->table('tbl_setting')->where('setting_key','qstudyPasswordMainEc')->get()->getRow()->setting_type;

        if (isset($_POST['submit']) == "submit") {

            if (isset($_POST) && !empty($_POST['qStudyPass'] ) ) {
                $AdminClass->updateInfo('tbl_setting', 'setting_key', "qstudyPassword", ['setting_type'=> $_POST['qStudyPass']]);
            }

            if( !empty($_POST['qStudyPassMain']) ){
                // print_r($_POST);die();
                $AdminClass->updateInfo('tbl_setting', 'setting_key', "qstudyPasswordMain", ['setting_type'=> $_POST['qStudyPassMain']]);
                $this->db->table('tbl_useraccount')->where('user_email','qstudy@gmail.com')->update(['user_pawd'=> md5($_POST['qStudyPassMain'])]);

            }
            if( empty($_POST['qStudyPass'] ) ){
                $AdminClass->updateInfo('tbl_setting', 'setting_key', "qstudyPassword", ['setting_type'=> '']);
            }

            if(empty($_POST['qStudyPassMain'] ) ){
                $AdminClass->updateInfo('tbl_setting', 'setting_key', "qstudyPasswordMain", ['setting_type'=> '']);
                $this->db->table('tbl_useraccount')->where('user_email','qstudy@gmail.com')->update(['user_pawd'=>$getEncryptPW]);
            }
            

            $this->session->set('success', 'Updated Successfully');


        }

        $data['user_info'] = $AdminClass->getInfo('tbl_setting', 'setting_key',  "qstudyPassword");
        $data['user_info_main'] = $AdminClass->getInfo('tbl_setting', 'setting_key',  "qstudyPasswordMain");
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('admin/QStudyPassword/qStudyPassword',$data);
    } 


    public function qStudyPassword_update()
    {
        $AdminClass=new \AdminClass();

        $getEncryptPW = $this->db->table('tbl_setting')->where('setting_key','qstudyPasswordMainEc')->get()->getRow()->setting_type;
         
        
        if (isset($_POST['submit']) == "submit") {

            if (isset($_POST) && !empty($_POST['qStudyPass'] ) ) {
                $AdminClass->updateInfo('tbl_setting', 'setting_key', "qstudyPassword", ['setting_type'=> $_POST['qStudyPass']]);
            }

            if( !empty($_POST['qStudyPassMain']) ){
                // print_r($_POST);die();
                $AdminClass->updateInfo('tbl_setting', 'setting_key', "qstudyPasswordMain", ['setting_type'=> $_POST['qStudyPassMain']]);
                $this->db->table('tbl_useraccount')->where('user_email','qstudy@gmail.com')->update(['user_pawd'=> md5($_POST['qStudyPassMain'])]);

            }
            if( empty($_POST['qStudyPass'] ) ){
                $AdminClass->updateInfo('tbl_setting', 'setting_key', "qstudyPassword", ['setting_type'=> '']);
            }

            if(empty($_POST['qStudyPassMain'] ) ){
                $AdminClass->updateInfo('tbl_setting', 'setting_key', "qstudyPasswordMain", ['setting_type'=> '']);
                $this->db->table('tbl_useraccount')->where('user_email','qstudy@gmail.com')->update(['user_pawd'=>$getEncryptPW]);
            }
            

            $this->session->set('success', 'Updated Successfully');


        }

        $data['user_info'] = $AdminClass->getInfo('tbl_setting', 'setting_key',  "qstudyPassword");
        $data['user_info_main'] = $AdminClass->getInfo('tbl_setting', 'setting_key',  "qstudyPasswordMain");
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('admin/QStudyPassword/qStudyPassword',$data);
    } 

    public function idea_create_student_report($checkout_id){
        
        $AdminClass=new \AdminClass();
        $data['this_idea'] = $AdminClass->get_this_idea($checkout_id);
        $data['all_idea']  = $AdminClass->get_ideas($checkout_id);
        $data['teacher_workout']  = $AdminClass->get_admin_workout($checkout_id);
        
        $data['student_id'] = $data['this_idea'][0]['student_id'];
       
        // print_r($data['all_idea']);die();
       
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['headerlink'] = $this->load->view('dashboard_template/headerlink', $data, true);
        $data['header'] = $this->load->view('dashboard_template/header', $data, true);
        $data['footerlink'] = $this->load->view('dashboard_template/footerlink', $data, true);
        $data['maincontent'] = $this->load->view('admin/user/idea_create_student_report', $data, true);
        $this->load->view('master_dashboard', $data);
    }
	
	 public function dicItemCreatorToPay()
    {
        $QuestionClass=new \QuestionClass();
        $data['toPay'] = $QuestionClass->wordCreatorToPayCount();
        echo count($data['toPay']);
    }
	
	  public function payment_log()
    {
        $builder = $this->db->table('tbl_payment');
        $builder->select('tbl_payment.*,tbl_useraccount.name,tbl_useraccount.user_type');
        $builder->join('tbl_useraccount', 'tbl_useraccount.id = tbl_payment.user_id','left');
        $query = $builder->get();
        $data['payment_details']=$query->getResultArray();
        //$data['payment_details']=$this->db->table('tbl_payment')->get()->getResultArray();
        // echo '<pre>';
        // print_r($data);die();
        return view('admin/payment/payment_log',$data);
    }
}
