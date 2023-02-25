<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use RegisterClass;
use AdminClass;
use StudentClass;

class Login extends BaseController
{

    public function loginChk()
    {

        $user_name = $this->request->getVar('user_name');
        $password = $this->request->getVar('password');

        $builder =$this->db->table('tbl_useraccount');     
        $builder->where('user_email',$user_name);
        $builder->where('user_pawd',md5($password));
        $query_result = $builder->get();
        $result = $query_result->getRow();

        // echo '<pre>';
        // print_r($result);die();
        $m_data = array();
        
        if ($result) {

            if ($result->subscription_type == 'trial') {
                $trail_period = trailPeriod();
                $Date =  date("Y-m-d");
                $x = date('Y-m-d', $result->created);
                $y = strtotime($x. ' + '.$trail_period[0]['setting_value'].' days');
              
                if ($y <= strtotime( date('Y-m-d'))) {

                    $name_data = array();
                    $name_data['user_email'] = $result->user_email;
                    $name_data['user_id'] = $result->id;
                    $name_data['subscription_type'] = $result->subscription_type;
                    $name_data['payment_status'] = $result->payment_status;
                    $name_data['userType'] = $result->user_type;                    
                    $this->session->set($name_data);
                    echo 1;
                    //echo 2;
                }else{
 
                    $name_data = array();
                    $name_data['user_email'] = $result->user_email;
                    $name_data['user_id'] = $result->id;
                    $name_data['subscription_type'] = $result->subscription_type;
                    $name_data['payment_status'] = $result->payment_status;
                    $name_data['userType'] = $result->user_type;
                    
                    $this->session->set($name_data);
                    echo 1;
                }

            }else{
                $name_data = array();
                $name_data['user_email'] = $result->user_email;
                $name_data['user_id'] = $result->id;
                $name_data['subscription_type'] = $result->subscription_type;
                $name_data['payment_status'] = $result->payment_status;
                $name_data['userType'] = $result->user_type;
                $this->session->set($name_data);
              
                echo 1;
            }
            //if($name_data['subscription_type'] == 'signup' && $name_data['payment_status'] == '') {
                //echo 2;
            //} else {
             //   echo 1;
            //}
        } else {
            $error_msg = 0;
            echo $error_msg;
        }
    }

    public function forgotPassView(){
        return view('auth/pass_reset/forgot_password');
    }
    public function emailCheck(){
        $RegisterClass = new \RegisterClass();
        $email = $_POST['email'];
        $emailExists = $RegisterClass->emailCheck($email);
        if ($emailExists) {
                echo 'true'; //email exists
        } else {
            echo 'false'; //email not exists
        }
    }
    public function sendResetPassEmail()
    {
        $AdminClass = new \AdminClass();
        $StudentClass = new \StudentClass();
        //validate and check input email
        $email = $_POST['email'] ? $_POST['email'] : '';
        $getUser = $AdminClass->get_all_where('*', 'tbl_useraccount', 'user_email', $email);
        // echo "<pre>";print_r($getUser);die();

        if ($getUser) {
            $id = $getUser[0]['id'];
            $token = random_string('alnum', 8);
            $passToken = [
                'token' => $token,
            ];
            $StudentClass->updateInfo('tbl_useraccount', 'user_email', $email, $passToken);

            // $getNotification = $notification->where('id', 2)->first();


            $change = ["{app_name}"];
            $changeTo = ["Qstudy"];
            $emailSubject = str_replace($change, $changeTo, 'Reset Password');

            $link = base_url() . '/reset_pass/' . $token;
            $source = ["{receiver_name}", "{app_name}", "{link}"];
            $dist = [$getUser[0]['name'], "RS Property", $link];
            
            $email = $getUser[0]['user_email'];
            $random_number = rand(100000, 999999);
            //$random_number = '123456';
            //username and password send
            
            $this->reset_send_sms($getUser,$random_number);

            $this->reset_send_email($email, $emailSubject, $link);
            // sendMail($data);
            // echo "<pre>";
            // print_r($check);
            // die();
            $this->session->set('success_msg', 'An email has been sent...');
            return redirect()->to(base_url('/'));

        }

        
        


        

        // //username and password send

        // $settins_Api_key = $AdminClass->getSmsApiKeySettings();
        // $settins_sms_messsage = $AdminClass->getSmsType("Forgot Password");

        // $register_code_string = $settins_sms_messsage[0]['setting_value'];
        // $message = str_replace( "{{ username }}" , $getUser[0]['user_email'] , $register_code_string);
        // $message = str_replace( "{{ password }}" , $getUser[0][''] , $message);

        // $api_key = $settins_Api_key[0]['setting_value'];
        // $content = urlencode($message);

        // $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $userName[0]['user_mobile'] . "&content=$content";

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HEADER, 0);
        // curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        // curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        // curl_setopt($ch, CURLOPT_VERBOSE, true);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        // //execute post
        // $result = curl_exec($ch);

        
        // curl_close($ch);
        // // print_r($result);die;
        // $send_msg_status = json_decode($result);


        
    }
    public function reset_send_email($user_email, $emailSubject, $link){
        $email = \Config\Services::email();
        // echo '<pre>';
        // print_r($mail_data);die;
        $mailTo        =   $user_email;
        $mailSubject   =   $emailSubject;
        if($link)
        {
           $message       =   $link;
        }
        else
        {
            $message       = 'For Testing purpose'; 
        }
        // $ci->load->library('email');
        // $ci->email->set_mailtype('html');
            
        /*$config['protocol'] ='sendmail';
        $config['mailpath'] ='/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = true;*/
        $config['protocol']    = 'smtp';
        $config['SMTPCrypto']  = 'ssl';
        $config['SMTPPort']    = '465';
        $config['SMTPHost']    = 'mail.therssoftware.com';
        $config['SMTPUser']    = 'noreply@therssoftware.com';
        $config['SMTPPass']    = 'RS8723@gbm%';
        $config['mailType']    = 'html';
        $email->initialize($config);
    
    
        $email->setFrom('noreply@therssoftware.com', 'Q-study');
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

    public function reset_send_sms($getUser,$pass_reset_code){
        //echo "<pre>";print_r($getUser);die();
        $AdminClass = new \AdminClass();
        $StudentClass = new \StudentClass();

        $data['pass_reset_code'] = $pass_reset_code;
        $StudentClass->updateInfo('tbl_useraccount', 'user_email', $getUser[0]['user_email'], $data);

        $settins_Api_key = $AdminClass->getSmsApiKeySettings();

        $message = 'This is Your Password Reset Code :'.$pass_reset_code;
        
        $api_key = $settins_Api_key[0]['setting_value'];
        $content = urlencode($message);
       

        $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=".$getUser[0]['user_mobile']."&content=$content";

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
    }

    public function mailTemplate($parent_name, $parent_email, $user_type, $parent_password, $student_list)
    {
        $AdminClass = new \AdminClass();
        
        $userName = $parent_name;
        $userEmail = $parent_email;
        $userPassword = $parent_password;
        
        $template = $AdminClass->getInfo('table_email_template', 'email_template_type', 'forget_password');
        $child_number = sizeof($student_list);
        if ($template) {
            $subject = $template[0]['email_template_subject'];
            $template_message = $template[0]['email_template'];
          
            $st_data = '';
      
      if($child_number > 0){
        foreach ($student_list as $single_child) {
          $st_data .=
            "<div style='overflow:hidden ; margin-bottom:10px;'>
              <p style='margin-top:10px; text-align:left;'><strong>Student Limit {$child_number}:</strong></p>
              <div style='width:70%; float:left;  text-align:left;'>
                <p>Username</p>
                <p>{$single_child['st_name']}</p>
              </div>
              <div style='width:30%; float:left;  text-align:right;'>
                <p>Password</p>
                <p>{$single_child['st_password']}</p>
              </div>
            </div>";
        }
      }
      
      
        $find = array("{{student_block}}","{{parentName}}","{{parent_email}}","{{parent_password}}");
        $replace = array($st_data,$userName,$userEmail,$userPassword);
      
      
            // if($user_type == 2){
        // $find = array("{{upper_student_name}}","{{upper_student_email}}","{{upper_student_password}}");
        // $replace = array($Name,$email,$Password);
      // }
      
      // if($user_type == 3){
        // $find = array("{{tutorName}}","{{tutor_email}}","{{tutor_password}}","{{tutor_sct_link}}");
        // $replace = array($tutorName,$tutorEmail,$tutorPassword,$SCT_link);
      // }
      
      // if($user_type == 4){
        // $find = array("{{teacher_number}}","{{teacher_block}}","{{schoolName}}","{{school_email}}","{{school_password}}");
        // $replace = array($child_number,$st_data,$userName,$userEmail,$userPassword);
      // }
      // if($user_type == 5){
        // $find = array("{{teacher_number}}","{{teacher_block}}","{{corporateName}}","{{corporate_email}}","{{corporate_password}}");
        // $replace = array($child_number,$st_data,$userName,$userEmail,$userPassword);
      // }
      
            $message = str_replace($find, $replace, $template_message);
      
            $mail_data['to'] = $userEmail;
            $mail_data['subject'] = $template[0]['email_template_subject'];
            
            $mail_data['message'] = $message;
            // $this->sendEmail($mail_data);
        }
    
        return true;
    }

    public function passwdCheck()
    {
        $RegisterClass = new \RegisterClass();
        $phone = $_POST['phone'];
        $email = $_POST['email'];
       
        $ck = $RegisterClass->passwdChk($email , $phone );
        
        if ($ck) {
            echo 1; 
        } else {
            echo 0; 
        }
    }
    public function phoneCheck()
    {
        $RegisterClass = new \RegisterClass();
        $phone = $_POST['phone'];
        $emailExists = $RegisterClass->phoneChk($phone);

        if ($emailExists) {
            echo 1; 
        } else {
            echo 0; 
        }
    }

    public function resetPass($userToken)
    {
        $AdminClass = new \AdminClass();
        $StudentClass = new \StudentClass();
        // $getToken = $user->where('pass_reset_code', $userToken)->first();
        
        $getToken = $AdminClass->get_all_where('*', 'tbl_useraccount', 'token', $userToken);
        $data['token'] = $userToken;
        
       
        if ($getToken) {
            $data['pass_reset_code'] = $getToken[0]['pass_reset_code'];
            if ($this->request->getMethod() == 'post') {

                if (!$this->validate('resetValidate')) {
                    $data['validation'] = $this->validator;
                } else {
                    $pass = $this->request->getVar('user_pass');
                    $rePass = $this->request->getVar('re_pass');
                    
                    // echo $userToken;die();
                    if (strlen($pass == $rePass)) {
                        $passUpdate = [
                            'user_pawd' => md5($this->request->getVar('user_pass')),
                            'pass_reset_code' => '',
                            'token' => '',
                        ];

                        $StudentClass->updateInfo('tbl_useraccount', 'token', $userToken, $passUpdate);

                        $data['reset_chk'] = 'ooo';
                        $data['success'] = '<div class="alert alert-success text-center">Password update successfully!</div>';
                    } else {
                        $data['error'] = '<div class="alert alert-warning text-center">Password and confirm password does not match!</div>';
                    }
                }
            }
            return view('auth/pass_reset/reset-pass', $data);
        } else {
            $data['invalidToken'] = '<div class="alert alert-warning text-center">Invalid Token!</div>';
            return view('auth/pass_reset/invalid-token', $data);
        }
    }
	
	public function parent_password_check(){
     
        $LoginClass=new \LoginClass();
        $password = $this->request->getVar('password');
        $user_id = $this->session->get('user_id');
        $par = $this->db->table('tbl_useraccount')->where('id',$user_id)->get()->getRow();
        $parent_id = $par->parent_id;
        $result = $LoginClass->parent_pw_check_info($parent_id, $password);
        echo $result;

    }
}
