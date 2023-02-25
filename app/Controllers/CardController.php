<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TblUserAccountModel;
use App\Models\TblPaymentModel;
use App\Models\TblPaymentDetailModel;
use App\Models\TblRegisteredCourseModel;
use App\Models\TblComposeMessageModel;

class CardController extends BaseController
{
    //added AS 
    public function direct_deposit(){
		error_report_check();
        $RegisterClass = new \RegisterClass();

        $TblUserAccountModel=new TblUserAccountModel();
        $TblPaymentModel=new TblPaymentModel();
        $TblPaymentDetailModel=new TblPaymentDetailModel();
        $TblRegisteredCourseModel=new TblRegisteredCourseModel();
        $TblUserAccountModel=new TblUserAccountModel();

        $user_id=$this->session->get('user_id');
        $email =$this->session->get('user_email');

        $check_student=$TblUserAccountModel->where('id',$user_id)->first();

        $data['user_id']=$this->session->get('user_id');
        $data['PaymentDate'] = time();
        $paymentType = $this->session->get('paymentType');
        if($paymentType == 1){
            $second = 30 * 24 * 3600;
        }elseif($paymentType == 2){
            $second = 30 * 6 * 24 * 3600;
        }elseif($paymentType == 3){
            $second = 30 * 12 * 24 * 3600;
        }elseif($paymentType == 4){
            $second = 30 * 3 * 24 * 3600;
        }
        $endDate   = $data['PaymentDate'] + $second;
        $data['PaymentEndDate']   = $data['PaymentDate'] + $second;
        $data['total_cost']		  = $this->session->get('totalCost');
        $data['payment_duration'] = $paymentType;
        $data['payment_status']   = "pending";
        $data['SenderEmail'] 	  = $this->session->get('user_email');
        
        $data['customerId']       = null;
        $data['subscriptionId']   = null;
        $data['paymentType'] 	  = 3;
        $data['invoiceId']        = null;

        $TblPaymentModel->insert($data);
        $paymentId = $TblPaymentModel->getInsertID();

        $rs_course = $this->session->get('courses');
        // echo '<pre>';
		// print_r($rs_course);
		// die();
        foreach($rs_course as $singleCourse)
        {
            $pay['paymentId']=$paymentId;
            $pay['courseId']=$singleCourse;
			$this->db->table('tbl_payment_details')->insert($pay);
        }
	
        $courseEnd = $endDate;
        $today_timestamp = time();
     
        $TblRegisteredCourseModel->where('user_id',$user_id)->where('endTime <',$today_timestamp)->set(['status'=>0])->update();
    
        foreach ($rs_course as $singleCourse) {
            $course['course_id'] = $singleCourse;
            $rs_course_cost    = $RegisterClass->getCourseCost($course['course_id']);
            $course['cost']    = $rs_course_cost[0]['courseCost'];
            $course['user_id'] = $user_id;
            $course['created'] = time();
            $course['endTime'] = $courseEnd;
            // echo '<pre>';print_r($course);die();
           $RegisterClass->basicInsert('tbl_registered_course', $course);
        }
        $user_data['payment_status'] = 'Completed';
        $user_data['subscription_type'] = 'signup';
        $sub_end_date = date('Y-m-d',$data['PaymentEndDate']);
        //$user_data['end_subscription'] = $sub_end_date;
	
        if($check_student->end_subscription != null){
            $end_subscription = $check_student->end_subscription;
            if($sub_end_date > $end_subscription){
                $user_data['end_subscription']  = $sub_end_date;
            }else{
                $user_data['end_subscription']  = $end_subscription;
            }
        }else{
            $user_data['end_subscription']  = $sub_end_date;
        }
        $TblUserAccountModel->where('id',$data['user_id'])->set($user_data)->update();
        
        //check for student/parent
        if ($check_student->user_type == 6) {
        $TblUserAccountModel->where('id', $check_student->parent_id)->set($user_data)->update();
        $this->session->set('userType',6);
        }
		
        //mail and send message
        if ($check_student->user_type == 6) {
			
			$parentDetails = $this->db->table('tbl_useraccount')->where('id',$check_student->parent_id)->get()->getRow();
			
        $par_email = $parentDetails->user_email;
        $par_name = $parentDetails->name;
        $country_id = $parentDetails->country_id;
		
        $this->sendInboxMessage($par_email,$par_name,$rs_course,$paymentType,$this->session->get('totalCost'),$country_id,$user_id);
	
        $this->sendEmailMessage($par_email,$par_name,$rs_course,$paymentType,$this->session->get('totalCost'),$country_id,$user_id);
		
        }else{
        $this->sendInboxMessage($email,$check_student->name,$rs_course,$paymentType,$this->session->get('totalCost'),$check_student->country_id,$user_id);
        $this->sendEmailMessage($email,$check_student->name,$rs_course,$paymentType,$this->session->get('totalCost'),$check_student->country_id,$user_id);
        }
        
        return redirect()->to(base_url('direct-request'));

    }

        public function sendInboxMessage($email,$name,$rs_course,$paymentType,$total_cost,$country_id,$user_id){
            $TblComposeMessageModel=new TblComposeMessageModel();
            $AdminClass = new \AdminClass();
            $RegisterClass = new \RegisterClass();

            $getDepositDetails = $AdminClass->getDepositDetails('direct_deposit_admin_setting',$country_id);
           
            if($paymentType == 1){
                 $month = 1;
             }elseif($paymentType == 2){
                 $month = 6;
             }elseif($paymentType == 3){
                 $month = 12;
             }elseif($paymentType == 4){
                 $month = 3;
             }
             
             $html = '';
             $html .= '<div class="mailbody" style="padding: 0px 25px;">';
             $html .= '<p>Dear : '.$name.'</p><br>';
             $html .= '<p>Thank you for subscribing Q-study </p>';
             $html .= '<p>Your Choosen Product:</p> ';
             foreach ($rs_course as $singleCourse) {
                 $course['course_id'] = $singleCourse;
                 $rs_course_cost = $RegisterClass->getCourseCost($course['course_id']);
                 $course_cost    = $rs_course_cost[0]['courseCost'];
                 $courseName     = $rs_course_cost[0]['courseName'];
                 $html .= '<p>'.$courseName.'  $'.$course_cost.'</p> ';
                 
             } 
             $html .= '<p>Duration: '.$month.' Month</p> ';
             $html .= '<p>Total $: '.$total_cost.'</p> ';
             $html .= '<p>Please make payment of <span style="color:#333192;">$'.$total_cost.'</span> to Q-study</p>';
             $html .= '<div>'.nl2br($getDepositDetails->inbox_message).'</div> <br>';
             $html .= '<p style="color:#333192;line-height: 20px;margin-bottom: 8px;">After payment has been made, please email or message to your payment information in the Q-study contact of the front page of the student so we can active your acount without delay.</p>';
             $html .= '<p style="color:#333192;line-height: 20px;margin-bottom: 8px;">Moreover, You can watch the video how to send payment information. </p>';
             $html .= '<p style="color:#333192;line-height: 20px;"> Please write your name,email address and your Ref.Link as the reference.  </p>';
             $html .= '<p>Thanks</p>';
             $html .= '<p><b>Q-Study</b></p>';
             $html .= '</div>';
             
             $data['message']    = $html;
             $data['reciver_id'] = $user_id;
             $data['date_time']  = date('Y-m-d H:i a');
             $data['date']       = date('Y-m-d');
             $data['time']       = time();
             
             $this->db->table('tbl_compose_message')->insert($data);
             $insert_id = $TblComposeMessageModel->getInsertID();
             return $insert_id;
             
        }

        public function sendEmailMessage($email,$name,$rs_course,$paymentType,$total_cost,$country_id,$user_id)
        {
            $AdminClass = new \AdminClass();
            $RegisterClass = new \RegisterClass();

            $getDepositDetails = $AdminClass->getDepositDetails('direct_deposit_admin_setting',$country_id);

            if($paymentType == 1){
                 $month = 1;
             }elseif($paymentType == 2){
                 $month = 6;
             }elseif($paymentType == 3){
                 $month = 12;
             }elseif($paymentType == 4){
                 $month = 3;
             }
             $template = $AdminClass->getInfo('table_email_template', 'email_template_type', 'Direct_Deposit');
             
     
             
             $html = '';
             foreach ($rs_course as $singleCourse) {
                 $course['course_id'] = $singleCourse;
                 $rs_course_cost = $RegisterClass->getCourseCost($course['course_id']);
                 $course_cost    = $rs_course_cost[0]['courseCost'];
                 $courseName     = $rs_course_cost[0]['courseName'];
                 $html .= '<span style="font-size: 14px;">'.$courseName.'  $'.$course_cost.'</span><br> ';
                 
             }
             $email_template = str_replace("{{coruse_list}}" , $html , $template[0]['email_template'] );
             
             $email_template = str_replace("{{parentname}}" , $name , $email_template );
             $email_template = str_replace("{{total_amount}}" , $total_cost , $email_template );
             $email_template = str_replace("{{total_duration}}" , $month , $email_template );
             $email_template = str_replace("{{money}}" , $total_cost , $email_template);
             $email_template = str_replace("{{bank_details}}" , nl2br($getDepositDetails->inbox_message) ,$email_template);
             
             $mail_data['to'] = 'shovoua@gmail.com';//$userEmail;
             $mail_data['subject'] = 'Q-study Direct Deposit';
             ;
             $mail_data['message'] = $email_template;
             $this->sendEmail($mail_data);
             
   }

   public function derect_request()
	{
        error_reporting(0);

        $PreviewClass = new \PreviewClass();
        $RegisterClass = new \RegisterClass();

		if ($this->session->get('user_id') != '') {

			$user_id = $this->session->get('user_id');
			$st_password = $this->session->get('st_password');
			$user_info = $PreviewClass->userInfo($user_id);
			$template = $RegisterClass->getInfo('table_email_template', 'email_template_type', 10);
			//Added AS
            if($_SESSION['userType'] == 6){
                $parent_id = $user_info[0]['parent_id'];
			    $parent_user_info = $PreviewClass->userInfo($parent_id);
			    $data['parent_email'] = $parent_user_info[0]['user_email'];
            }
		    //echo $parent_id;echo '<pre>';print_r($_SESSION['childrens']);die();
			if ( isset($_SESSION['courses'][0]) ) {
				$money = $RegisterClass->getInfo('tbl_course', 'id',  $_SESSION['courses'][0] );
			}
			
			$bank = $RegisterClass->getInfo('tbl_setting', 'setting_type', 'bank_account');

			$toUpdate['subscription_type'] = "signup";
			$toUpdate['direct_deposite'] = 0;
		    //$this->Tutor_model->updateInfo('tbl_useraccount', 'id', $user_id , $toUpdate );


			$email_template = str_replace("{{parentname}}" , $user_info[0]['name'] , $template[0]['email_template'] );

			if ( $_SESSION['countryId'] == 8 ) {

				$currency_convrt = $RegisterClass->getInfo('tbl_setting', 'setting_key', 'currency_convert_BDT');
				$tk = (int) $_SESSION['totalCost'] * $currency_convrt[0]['setting_value'];
				$email_template = str_replace("{{money}}" , $tk." BDT" , $email_template );
			}else{
				$email_template = str_replace("{{money}}" , $_SESSION['totalCost']." $" , $email_template );
			}
			
			$email_template = str_replace("{{acount_name}}" , $bank[0]['setting_value'] , $email_template );
			$email_template = str_replace("{{bsb}}" , $bank[1]['setting_value'] , $email_template );
			$email_template = str_replace("{{account_number}}" , $bank[2]['setting_value'] , $email_template );
			$email_template = str_replace("{{bank}}" , $bank[3]['setting_value'] , $email_template );


			if (!empty($_SESSION['trail_suspend']) && $_SESSION['trail_suspend'] == 1 ) {
                return redirect()->to(base_url('/'));
	        }

			if ($_SESSION['userType'] == 1) {
				$email_template = str_replace("{{parent_email}}" , $user_info[0]['user_email'] , $email_template );
				$email_template = str_replace("{{parent_password}}" ,$_SESSION['password'] , $email_template );
				$email_template = str_replace("{{student_number}}" , $_SESSION['childrens'] , $email_template );


				$html = '';

	            foreach ($_SESSION['students'] as $key => $value) {

		            $html .=
		                "<div style='overflow:hidden ;  margin-bottom:20px;'>
		                <div style='width:70%; float:left; text-align:left;'>
		                <p>Username</p>
		                <p>{$value['name']}</p>
		                </div>
		                <div style='width:30%; float:left; text-align:right;'>
		                <p>Password</p>
		                <p>{$st_password[$key]}</p>
		                </div>
		                </div>";          
		          }     



	            $email_template = str_replace("{{student_block}}" , $html, $email_template );

	            $this->mailTemplate($this->session->get('parent_name'), $this->session->get('email'), $this->session->get('password'), $this->session->get('student_list'));

			}
			if ($_SESSION['userType'] == 6) {
				$email_template = str_replace("{{parent_email}}" , $parent_user_info[0]['user_email'] , $email_template );
				$email_template = str_replace("{{parent_password}}" ,$_SESSION['password'] , $email_template );
				$email_template = str_replace("{{student_number}}" , $_SESSION['childrens'] , $email_template );

                $html .=
		                "<div style='overflow:hidden ;  margin-bottom:20px;'>
		                <div style='width:70%; float:left; text-align:left;'>
		                <p>Username</p>
		                <p>{$user_info[0]['name']}</p>
		                </div>
		                <div style='width:30%; float:left; text-align:right;'>
		                <p>Password</p>
		                <p>{$_SESSION['password']}</p>
		                </div>
		                </div>";   

	            $email_template = str_replace("{{student_block}}" , $html, $email_template );

	            $this->mailTemplate($parent_user_info[0]['name'] , $parent_user_info[0]['user_email'] , $this->session->get('password'), $this->session->get('student_list'));

			}

			if ($_SESSION['userType'] == 2) {
				$email_template = str_replace("{{parent_email}}" , $_SESSION['email'] , $email_template );
				$email_template = str_replace("{{parent_password}}" ,$_SESSION['password'] , $email_template );
				$email_template = str_replace("{{student_number}}" , "" , $email_template );

				$html = '';  
	            $email_template = str_replace("{{student_block}}" , $html, $email_template );
	            
	            $email_template = str_replace("Student Limit :" , $html, $email_template );

	            $this->student_mailTemplate($this->session->get('upper_student_name'), $this->session->get('email'), $this->session->get('password'));

			}

			if ($_SESSION['userType'] == 3) {

				$email_template = str_replace("{{parent_email}}" , $_SESSION['email'] , $email_template );
				$email_template = str_replace("{{parent_password}}" ,$_SESSION['password'] , $email_template );
				$email_template = str_replace("{{student_number}}" , "" , $email_template );

				$html = '';  
	            $email_template = str_replace("{{student_block}}" , $html, $email_template );
	            $email_template = str_replace("Student Limit :" , $html, $email_template );


				$this->tutor_mailTemplate($this->session->get('tutor_name'), $this->session->get('email'), $this->session->userdata('password'), $this->session->get('SCT_link') );
			}

			if ($this->session->get('paymentType')==1 ) {
            $toUp['end_subscription'] = date('Y-m-d', strtotime(date("Y-m-d", strtotime(date('Y-m-d'))) . " +30 day"));
            }

            if ($this->session->get('paymentType')==2) {
            $toUp['end_subscription'] = date('Y-m-d', strtotime(date("Y-m-d", strtotime(date('Y-m-d'))) . " +182 day"));
            }

            if ($this->session->get('paymentType')==3) {
            $toUp['end_subscription'] = date('Y-m-d', strtotime(date("Y-m-d", strtotime(date('Y-m-d'))) . " +365 day"));
            }
            if ($this->session->get('paymentType')==4) {
            $toUp['end_subscription'] = date('Y-m-d', strtotime(date("Y-m-d", strtotime(date('Y-m-d'))) . " +90 day"));
            }

			$toUp['subscription_type'] = "signup";
			$toUp['direct_deposite'] = 0;

			//$this->Tutor_model->updateInfo('tbl_useraccount', 'user_email', $this->session->userdata('email') , $toUp);

			

            $var = [
            	"to" => $user_info[0]['user_email'],
            	"subject" => $template[0]['email_template_subject'],
            	"message" => $email_template,
            ];

            

            $this->sendEmail($var);

            $data['user_info']=$user_info;

            return view('direct_request', $data);
        }
	}

    public function mailTemplate($parent_name, $parent_email, $parent_password, $student_list)
    {
        $PreviewClass = new \PreviewClass();
        $RegisterClass = new \RegisterClass();
        
        $userName = $parent_name;
        $userEmail = $parent_email;
        $userPassword = $parent_password;
        
        $template = $RegisterClass->getInfo('table_email_template', 'email_template_type', $this->session->get('userType'));
        $student_number = sizeof($student_list);
        if ($template) {
            $subject = $template[0]['email_template_subject'];
            $template_message = $template[0]['email_template'];

            $firstPos = strpos('[[[studentdata]]]', $template_message);
            $lastPos = strpos('[[[/studentdata]]]', $template_message);
            $sub_str_message = substr($template_message, $firstPos, $lastPos);
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
            $mail_data['to'] = $userEmail;
            $mail_data['subject'] = $template[0]['email_template_subject'];
            ;
            $mail_data['message'] = $message;
            $this->sendEmail($mail_data);
        }
        return true;
    }

    public function student_mailTemplate($upper_student_name, $email, $password)
    {
        $PreviewClass = new \PreviewClass();
        $RegisterClass = new \RegisterClass();

        $Name=$upper_student_name;
        $email=$email;
        $Password=$password;
        $template = $RegisterClass->getInfo('table_email_template', 'email_template_type', $this->session->get('userType'));
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

    function tutor_mailTemplate($tutorName, $tutorEmail, $tutorPassword, $SCT_link)
    {
        $PreviewClass = new \PreviewClass();
        $RegisterClass = new \RegisterClass();

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
        $config['SMTPUser']    = 'admin@q-study.com';
        $config['SMTPPass']    = 'Mn876#%2dq';
        $config['mailType']    = 'html';
        $email->initialize($config);
        
        
        $email->setFrom('admin@q-study.com','Q-study');
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

    public function card_form_submit(){	
		
		require_once(APPPATH.'stripe-php-master/init.php');
		error_report_check();
        $SettingClass=new \SettingClass();
        $RegisterClass=new \RegisterClass();

        if ($_POST['cardName'] == "derect_deposite" ) {
            return redirect()->to(base_url('direct-request'));
		}

		if(!empty($_POST['stripeToken'])){

			$token  = $_POST['stripeToken'];
			$userID = $this->session->get('user_id');
			$courseUser = $userID;
			$check_student = $this->db->table('tbl_useraccount')->where('id',$userID)->get()->getRow();
			if ($check_student->user_type == 6) {
			 	$parentDetails = $this->db->table('tbl_useraccount')->where('id',$check_student->parent_id)->get()->getRow();
			 	//$userID = $parentDetails->id;
			 	$user_email = $parentDetails->user_email;
			}else{
			 	$user_email = $this->session->get('user_email');
			}

			$total_cost=$this->session->get('totalCost') * 100;
            $payment_process = $this->session->get('payment_process');
            
                    
			
			// echo $payment_process;die();
			//set api key
			/*$this->db->select('*');
			$this->db->from('tbl_stripe_api_key');
			$this->db->where('type',0);
			$api_key=$this->db->get()->result_array();
			fi4*/
			$publish_key=$SettingClass->getStripeKey('publish');
			// echo '<pre>';
			// print_r($publish_key);
			// die();
		    $sereet_key=$SettingClass->getStripeKey('seccreet');
			/*$stripe = array(
			  "secret_key"      => "sk_test_XxfxLa9eNGyO4BMFo3EXcrGl",
			  "publishable_key" => "pk_test_8d5s2El2JNAMmyL1xc87EpcH"
			);*/
			$stripe = array(
			  "secret_key"      => $sereet_key,// $api_key[0]['sereet_key'],
			  "publishable_key" => $publish_key //$api_key[0]['publish_key']
			);

			\Stripe\Stripe::setApiKey($stripe['secret_key']);
	
			//add customer to stripe
			$checkCustomer = $this->db->table('stripe_customer')->where('user_id',$userID)->get()->getRow();

			if (isset($checkCustomer)) {
				$customer = $checkCustomer;
				// echo "<pre>";print_r($customer->id);die();
			}else{
				$customer = \Stripe\Customer::create(array(
					'email' => $user_email,
					'source'  => $token
				));
				$this->db->table('stripe_customer')->insert(['user_id' =>$userID,'id'=> $customer->id]);
				// echo "<pre>";print_r($customer);die();
			}
			
			// subscription here
			if ($payment_process == 1) {

				$product_variation=rand(100, 999);
				$product = \Stripe\Product::create([
					'name' => 'My SaaS Platform'.'_'. $product_variation,
					'type' => 'service',
				]);
				
				$paymentType = $this->session->get('paymentType');
				if($paymentType == 1){
		            $interval_count=1;
				}elseif($paymentType == 2){
		            $interval_count=6;
				}elseif($paymentType == 3){
		            $interval_count=12;
				}elseif($paymentType == 4){
		            $interval_count=3;
				}
				$plan = \Stripe\Plan::create([
					'currency' => 'usd',
					'interval' => 'month',
		            'interval_count' => $interval_count,
					'product' => $product->id,
					'nickname' => 'Pro Plan',
					'amount' => $total_cost,
				]);
				
				$subscription = \Stripe\Subscription::create([
					'customer' =>  $customer->id,
					'items' => [['plan' =>  $plan->id]],
					
				]);

			}
				
			//echo '<pre>';print_r($subscription);die();
			//item information
			$itemName = "Rs";
			$itemNumber = "1";
			$itemPrice = $total_cost;
			$currency = "usd";
			$orderID = "SKA92712382139";
			
			if ($payment_process == 2) {
			 
				//charge a credit or a debit card
				$charge = \Stripe\Charge::create(array(
					'customer' => $customer->id,
					'amount'   => $itemPrice,
					'currency' => $currency,
					'description' => $itemName,
					'metadata' => array(
					    'order_id' => $orderID,
					)
				));

				$chargeJson = $charge->jsonSerialize();
			}
			
			// $invoice=\Stripe\InvoiceItem::create([
			// 	'amount' => $total_cost,
			// 	'currency' => 'usd',
			// 	'customer' => $customer->id,
			// 	'description' => 'One-time setup fee',
			// ]);
		
			
			//retrieve charge details
			//check whether the charge is successful
			if($subscription->object == "subscription" && $subscription->status == "active"){
				
				// order details 
				// $amount = $chargeJson['amount'];
				// $balance_transaction = $chargeJson['balance_transaction'];
				// $currency = $chargeJson['currency'];
				// $status = $chargeJson['status'];
				// $date = date("Y-m-d H:i:s");
				
				//include database config file
				//include_once 'dbConfig.php';
				$data['user_id']=$userID;
				$data['PaymentDate'] = time();
				$paymentType = $this->session->get('paymentType');
				if($paymentType == 1){
					//$second = 24 * 3600;
					$second = 30 * 24 * 3600;
				}elseif($paymentType == 2){
					$second = 30 * 6 * 24 * 3600;
				}elseif($paymentType == 3){
					$second = 30 * 12 * 24 * 3600;
				}elseif($paymentType == 4){
					$second = 30 * 3 * 24 * 3600;
				}

				$data['PaymentEndDate']   = $data['PaymentDate'] + $second;
				$data['total_cost']		  = ($subscription->plan->amount)/100;
				$data['payment_duration'] = $paymentType;
				$data['payment_status']   = $subscription->status;
				$data['SenderEmail'] 	  = $user_email;
				$data['customerId']       = $subscription->customer;
				$data['subscriptionId']   = $subscription->id;
				$data['paymentType'] 	  = 1;
				$data['invoiceId']        = $subscription->latest_invoice;
				$data['payment_info']     =json_encode($subscription);
				
				// $userID = $this->session->userdata('userType');
				// echo '<pre>';echo $userID;die();
				$this->db->table('tbl_payment')->insert($data);
			 	$paymentId = $this->db->insertID();
				$rs_course = $this->session->get('courses');
                $today_timestamp = time();
				// echo '<pre>';print_r($rs_course);die();
				foreach($rs_course as $singleCourse)
				{
					$pay['paymentId']=$paymentId;
					$pay['courseId']=$singleCourse;
					$this->db->table('tbl_payment_details')->insert($pay);
				}

				
                // $this->db->where('user_id',$courseUser)->delete('tbl_registered_course');
                $this->db->table('tbl_registered_course')->where('user_id',$courseUser)->where('endTime <',$today_timestamp)->update(['status'=>0]);

                foreach ($rs_course as $singleCourse) {
                    $course['course_id']=$singleCourse;
                    $rs_course_cost    = $RegisterClass->getCourseCost($course['course_id']);
                    $course['cost']    = $rs_course_cost[0]['courseCost'];
                    $course['user_id'] = $courseUser;
                    $course['created'] = time();
                    $course['endTime'] = $data['PaymentEndDate'];
                    $RegisterClass->basicInsert('tbl_registered_course', $course);
                }

				if ($data['payment_status'] == 'active') {
					$x = "Completed";
				}
				$user_data['payment_status'] = $x;
				$user_data['subscription_type'] = 'signup';
				$sub_end_date = date('Y-m-d',$data['PaymentEndDate']);
				
                if($check_student->end_subscription != null){
                    $end_subscription = $check_student->end_subscription;
                    if($sub_end_date > $end_subscription){
                        $user_data['end_subscription']  = $sub_end_date;
                    }else{
                        $user_data['end_subscription']  = $end_subscription;
                    }
                }else{
                    $user_data['end_subscription']  = $sub_end_date;
                }
	           
	            // $this->db->set('payment_status', $data['payment_status']);
	            $this->db->table('tbl_useraccount')->where('id', $data['user_id'])->update($user_data);

	            if ($check_student->user_type == 6) {
	            	$parent_id = $check_student->parent_id;
	            	$this->db->table('tbl_useraccount')->where('id', $parent_id)->update($user_data);
	            	$this->session->set('userType',6);
	            	//redirect('/student');
	            }

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
	                 $this->db->table('product_poinits')->insert($ckrfuser);
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

	         
				// echo '<pre>';print_r($chargeJson);
				//insert tansaction data into the database			
				//if order inserted successfully
				// if($last_insert_id && $status == 'succeeded'){
				// 	$statusMsg = "<h2>The transaction was successful.</h2><h4>Order ID: {$last_insert_id}</h4>";
				// }else{
				// 	$statusMsg = "Transaction has been failed";
				// }
				return redirect()->to(base_url('/'));

			}elseif($chargeJson['object'] == "charge" && $chargeJson['status'] == "succeeded"){
				// order details 
				// $amount = $chargeJson['amount'];
				// $balance_transaction = $chargeJson['balance_transaction'];
				// $currency = $chargeJson['currency'];
				// $status = $chargeJson['status'];
				// $date = date("Y-m-d H:i:s");
				
				//include database config file
				//include_once 'dbConfig.php';
				$data['user_id']=$userID;
				$data['PaymentDate'] = time();
				$paymentType = $this->session->get('paymentType');
				if($paymentType == 1){
					//$second = 24 * 3600;
					$second = 30 * 24 * 3600;
				}elseif($paymentType == 2){
					$second = 30 * 6 * 24 * 3600;
				}elseif($paymentType == 3){
					$second = 30 * 12 * 24 * 3600;
				}elseif($paymentType == 4){
					$second = 30 * 3 * 24 * 3600;
				}

				$data['PaymentEndDate']   = $data['PaymentDate'] + $second;
				$data['total_cost']		  = ($chargeJson['amount'])/100;
				$data['payment_duration'] = $paymentType;
				$data['payment_status']   = $chargeJson['status'];
				$data['SenderEmail'] 	  = $user_email;
				$data['customerId']       = $chargeJson['customer'];
				$data['subscriptionId']   = 'No debit';
				$data['paymentType'] 	  = 1;
				$data['invoiceId']        = $chargeJson['balance_transaction'];
				$data['payment_info']=json_encode($chargeJson);
				
				// $userID = $this->session->userdata('userType');
				//echo '<pre>';print_r($data);die();
				$this->db->table('tbl_payment')->insert($data);
			 	$paymentId = $this->db->insertID();
                $today_timestamp = time();
				$rs_course = $this->session->get('courses');
				// echo '<pre>';print_r($rs_course);die();
				foreach($rs_course as $singleCourse)
				{
					$pay['paymentId']=$paymentId;
					$pay['courseId']=$singleCourse;
					$this->db->table('tbl_payment_details')->insert($pay);
				}
				
                // $this->db->where('user_id',$courseUser)->delete('tbl_registered_course');
                $this->db->table('tbl_registered_course')->where('user_id',$courseUser)->where('endTime <',$today_timestamp)->update(['status'=>0]);

                foreach ($rs_course as $singleCourse) {
                    $course['course_id']=$singleCourse;
                    $rs_course_cost    = $RegisterClass->getCourseCost($course['course_id']);
                    $course['cost']    = $rs_course_cost[0]['courseCost'];
                    $course['user_id'] = $courseUser;
                    $course['created'] = time();
                    $course['endTime'] = $data['PaymentEndDate'];
                    $RegisterClass->basicInsert('tbl_registered_course', $course);
                }
				
                
				if ($data['payment_status'] == 'succeeded') {
					$x = "Completed";
				}
				$user_data['payment_status'] = $x;
				$user_data['subscription_type'] = 'signup';
				$sub_end_date = date('Y-m-d',$data['PaymentEndDate']);
				
                if($check_student->end_subscription != null){
                    $end_subscription = $check_student->end_subscription;
                    if($sub_end_date > $end_subscription){
                        $user_data['end_subscription']  = $sub_end_date;
                    }else{
                        $user_data['end_subscription']  = $end_subscription;
                    }
                }else{
                    $user_data['end_subscription']  = $sub_end_date;
                }
	           
	            // $this->db->set('payment_status', $data['payment_status']);
                $builder=$this->db->table('tbl_useraccount');
	            $builder->where('id', $userID);
	            $builder->update($user_data);
				
	            if ($check_student->user_type == 6) {
	            	$parent_id = $check_student->parent_id;

                    $builder=$this->db->table('tbl_useraccount');
	            	$builder->where('id', $parent_id);
	            	$builder->update($user_data);
	            	$this->session->set('userType',6);
	            	//redirect('/student');
	            }
				

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
	                 $this->db->table('product_poinits')->insert($ckrfuser);
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

	            
				return redirect()->to(base_url('/'));
			}else{
				echo 'php error';die();
				$statusMsg = "Transaction has been failed";
			}
		}else{
			echo 'Form error';die();
			$statusMsg = "Form submission error.......";
		}

		//show success or error message
		//echo $statusMsg;
	}

}
