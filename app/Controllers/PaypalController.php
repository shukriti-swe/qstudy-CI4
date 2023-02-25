<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class PaypalController extends BaseController
{
    public function cancelSubscription()
    {
        
        //$this->load->model('Admin_model');
        $AdminClass=new  \AdminClass();

        $loggedUserId = $this->session->get('user_id');
        if (!$loggedUserId) {
            return redirect()->to(base_url('/'));
        }

        $userPaymentInfo = $AdminClass->search('tbl_payment', ['user_id'=>$loggedUserId]);
        
        if (count($userPaymentInfo)) {
            $profileId=$userPaymentInfo[0]['subscriptionId'];
            
            $user = "shakil124_api1.gmail.com";
            $secret = "69SYZWDVXT7G49J8";
            $signature = 'Amzw67HwFOe7PrZRlpTioAViKMi0AozU6gZFHlmdXLmaGNNe-p2iRJfc';

            $api_request =  'USER=' . urlencode($user)
                            .'&PWD=' . urlencode($secret)
                            .'&SIGNATURE=' . urlencode($signature)
                            .'&VERSION=76.0'
                            .'&METHOD=ManageRecurringPaymentsProfileStatus'
                            .'&PROFILEID=' . urlencode($profileId). '&ACTION=cancel';
            
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp'); // For live transactions, change to 'https://api-3t.paypal.com/nvp'

            curl_setopt($ch, CURLOPT_VERBOSE, 1);

            // Uncomment these to turn off server and peer verification
            //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 2);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);

            // Set the API parameters for this transaction
            curl_setopt($ch, CURLOPT_POSTFIELDS, $api_request);

            // Request response from PayPal
            $response = curl_exec($ch);
            
            // If no response was received from PayPal there is no point parsing the response
            
            if (! $response) {
                //die('Calling PayPal to change_subscription_status failed: ' . curl_error($ch) . '(' . curl_errno($ch) . ')');
                echo 'subscription not canceled';
            } else { //update useraccount and tbl_payment
                $AdminClass->updateInfo('tbl_useraccount', 'id', $loggedUserId, ['payment_status'=>'Incomplete']);
                $AdminClass->updateInfo('tbl_payment', 'user_id', $loggedUserId, ['payment_status'=>'Incomplete']);
                $this->session->set('success_msg', 'Subscription Canceled');
                return redirect()->to(base_url('/'));
            }
 
            curl_close($ch);
        } else {
            echo 'User has no payment info';
        }
    }

    public function paypal_notify()
    {  
        $file = 'paypal_'.time().rand(9,9999).'.txt';
        $x = serialize($_POST);
        file_put_contents($file,$x);
        //paypal_16205012111000.txt
        //$x = file_get_contents('paypal_16232194844410.txt');
        $y = unserialize($x);
		
        // echo "<pre>"; 
        // print_r($y);die;
        
        //$this->load->helper('commonmethods_helper');
        /*sendmail([
            'to'=>'shakil147258@gmail.com',
            'subject'=>'subject',
            'message'=>json_encode($_POST),
        ]);*/
        
        $req = 'cmd=_notify-validate';
        //mail("ai.shobujice@gmail.com","My subject","Message Description");
        foreach($_POST as $key => $value) {
            $value = urlencode(stripslashes($value));
            $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value);
            $req .= "&$key=$value";
        }
        

        reset($_POST);
        $datas = print_r($_POST, true);
        //mail("ai.shobujice@gmail.com","My subject",$datas);
        //die();
        /* save payer info to database */

        //$data['UserId'] = $_POST['custom'];
        $userId_courseId = explode(',', $y['custom']);
        $data['user_id'] = $userId_courseId[0];
        //check for student/parent
        $courseID = $data['user_id'];
        $check_student = $this->db->table('tbl_useraccount')->where('id',$data['user_id'])->get()->getRow();
        
        
        if ($check_student->user_type == 6) {
          $data['user_id']=$userId_courseId[0];
        }else{
          $data['user_id']=$userId_courseId[0];
        }
        
        $data['PaymentDate'] = time();
        $paymentType = $userId_courseId[1];
        if ($paymentType == 1) {
            $second = 30 * 24 * 3600;
            //$second = 24 * 3600;
        } elseif ($paymentType == 2) {
            $second = 30 * 6 * 24 * 3600;
        } elseif ($paymentType == 3) {
            $second = 30 * 12 * 24 * 3600;
        }elseif ($paymentType == 4) {
            $second = 30 * 3 * 24 * 3600;
        }
        $endDate =  $data['PaymentDate'] + $second;
        $date = date('Y-m-d',$endDate);
        
        //echo $date;die();
        $data['PaymentEndDate'] = $data['PaymentDate'] + $second;
        $data['total_cost']     = $y['mc_gross'];
       // $data['PackageId'] = $y['item_number'];
        $data['payment_status'] = $y['payment_status'];
        $data['SenderEmail']    = $y['payer_email'];
        $data['paymentType']    = 2; //maybe paypal
        $data['subscriptionId'] = $y['subscr_id'];
        $data['customerId']     = $y['payer_id'];
        $data['payment_duration'] = $userId_courseId[1];
        $data['invoiceId']      = $y['txn_id']; 
        $data['payment_info']   = json_encode($x);
		
        $check_tnx = $this->db->table('tbl_payment')->where('invoiceId',$data['invoiceId'])->get()->getResultArray();
        if (count($check_tnx) > 0 ){
            echo "match invoice";die();
        }
        
        array_shift($userId_courseId);
        array_shift($userId_courseId);
        

        $instra = print_r($data, true);
        

        $ch = curl_init('https://ipnpb.sandbox.paypal.com/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        
        if (!($res = curl_exec($ch))) {
            // error_log("Got " . curl_error($ch) . " when processing IPN data");
            curl_close($ch);
            exit;
        }
        curl_close($ch);
        
        //echo "<pre>";print_r(strcmp($res, "VERIFIED"));die;
        // inspect IPN validation result and act accordingly
        if (strcmp($res, "VERIFIED") == 0){
            
            $userID = $data['user_id'];
           
            $this->db->table('tbl_payment')->insert($data);
            $paymentId=$this->db->insertID();
            $today_timestamp = time();
            if ($userId_courseId) {
                foreach ($userId_courseId as $dacourseId) {
                  $pay['paymentId']= $paymentId;
                  $pay['courseId'] = $dacourseId;
                  $this->db->table('tbl_payment_details')->insert($pay);
                }
                
                $this->db->table('tbl_registered_course')->where('user_id',$courseID)->where('endTime <',$today_timestamp)->update(['status'=>0]);
                
                $RegisterClass=new \RegisterClass();
                foreach ($userId_courseId as $singleCourse) {
                    $course['course_id'] = $singleCourse;
                    $rs_course_cost    = $RegisterClass->getCourseCost($course['course_id']);
                    $course['cost']    = $rs_course_cost[0]['courseCost'];
                    $course['user_id'] = $courseID;
                    $course['created'] = time();
                    $course['endTime'] = $endDate;
                    $RegisterClass->basicInsert('tbl_registered_course', $course);
                }
                
                
            }


            // echo "<pre>";print_r($data);die;
            
            $user_data['payment_status'] = $data['payment_status'];
            $user_data['subscription_type'] ='signup';
            if($check_student->end_subscription != null){
                $end_subscription = $check_student->end_subscription;
                if($date > $end_subscription){
                    $user_data['end_subscription']  = $date;
                }else{
                    $user_data['end_subscription']  = $end_subscription;
                }
            }else{
                
                $user_data['end_subscription']  = $date;
                
            }
            //$this->db->set('payment_status', $data['payment_status']);
            $builder = $this->db->table('tbl_useraccount');
            $builder->where('id', $userID);
            $builder->update($user_data);
            
            //check for student/parent
            if ($check_student->user_type == 6) {
              $builder = $this->db->table('tbl_useraccount');  
              $builder->where('id', $check_student->parent_id);
              $builder->update($user_data);
              $this->session->set('userType', 6);
              
              $refUsers = $this->db->table('tbl_referral_users')->where('user_id',$check_student->id)->where('status',0)->get()->getRow();
            
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
                     $ckrfuser['referral_point'] = $referralPoint + $old_referral_point;
                     $ckrfuser['total_point']    = $referralPoint + $totalPoint;
                     $this->db->table('product_poinits')->where('user_id',$reffInUser)->update($ckrfuser);
                    }else{
                     $ckrfuser['user_id'] = $reffInUser;
                     $ckrfuser['referral_point'] = $referralPoint;
                     $ckrfuser['total_point']    = $referralPoint;
                     $this->db->table('product_poinits')->insert($ckrfuser);
                    }
    
    
                    $checkrefferByUser = $this->db->table('product_poinits')->where('user_id',$refferByUser)->getRow();
    
                    if (!empty($checkrefferByUser)) {
                     $totalByPoint = $checkrefferByUser->total_point;
                     $old_referral_point = $checkrefferByUser->referral_point;
                     $ckrfByuser['referral_point'] = $ref_taken_point + $old_referral_point;
                     $ckrfByuser['total_point']    = $ref_taken_point + $totalByPoint;
                     $this->db->table('product_poinits')->where('user_id',$refferByUser)->update($ckrfByuser);
                    }else{
                     $ckrfByuser['user_id'] = $refferByUser;
                     $ckrfByuser['referral_point'] = $ref_taken_point;
                     $ckrfByuser['total_point']    = $ref_taken_point;
                     $this->db->table('product_poinits')->insert($ckrfByuser);
                    }
    
                    $this->db->table('tbl_referral_users')->where('user_id',$check_student->id)->update(['status' => 1]);
    
                   
                }
            }

            //insert payment info
            // IPN message values depend upon the type of notification sent.
            // To loop through the &_POST array and print the NV pairs to the screen:
            foreach ($y as $key => $value) {
                echo $key . " = " . $value . "<br>";
            }
        } elseif (strcmp($res, "INVALID") == 0) {
            $file = 'paypalError_'.time().rand(9,9999).'.txt';
            $x = 'error';
            file_put_contents($file, $x);
        }

        header("HTTP/1.1 200 OK");
    }
   
	 public function no_debit_paypal_notify()
    {
		
		error_report_check();
        $RegisterClass=new  \RegisterClass();
        $file = 'paypalnd_'.time().rand(9,9999).'.txt';
        $x = serialize($_POST);
        file_put_contents($file, $x);
        // $x = file_get_contents('paypalnd_16223752966217.txt');
        $y = unserialize($x);
        // echo "<pre>"; 
        // print_r($y);
        
        /*sendmail([
            'to'=>'shakil147258@gmail.com',
            'subject'=>'subject',
            'message'=>json_encode($_POST),
        ]);*/
        
        $req = 'cmd=_notify-validate';
        //mail("ai.shobujice@gmail.com","My subject","Message Description");
        foreach ($_POST as $key => $value) {
            $value = urlencode(stripslashes($value));
            $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value);
            $req .= "&$key=$value";
        }

        reset($_POST);
        $datas = print_r($_POST, true);
        //mail("ai.shobujice@gmail.com","My subject",$datas);
        $userId_courseId=explode(',', $y['custom']);
        $data['user_id']=$userId_courseId[0];
        //check for student/parent
        $courseID = $data['user_id'];
        $check_student = $this->db->table('tbl_useraccount')->where('id',$courseID)->get()->getRow();
	
        if ($check_student->user_type == 6) {
          $data['user_id']=$check_student->id;
        }else{
          $data['user_id']=$userId_courseId[0];
        }
        
        $data['PaymentDate'] = time();
        $paymentType = $userId_courseId[1];
        if ($paymentType == 1) {
            $second = 30 * 24 * 3600;
            //$second = 24 * 3600;
        } elseif ($paymentType == 2) {
            $second = 30 * 6 * 24 * 3600;
        } elseif ($paymentType == 3) {
            $second = 30 * 12 * 24 * 3600;
        }elseif ($paymentType == 4) {
            $second = 30 * 3 * 24 * 3600;
        }
        $endDate =  $data['PaymentDate'] + $second;
        $date = date('Y-m-d',$endDate);
        $data['PaymentEndDate'] = $data['PaymentDate'] + $second;
        $data['total_cost']     = $y['mc_gross'];
       // $data['PackageId'] = $y['item_number'];
        $data['payment_status'] = $y['payment_status'];
        $data['SenderEmail']    = $y['payer_email'];
        $data['paymentType']    = 2; //maybe paypal
        $data['subscriptionId'] = 'No Debit';
        $data['customerId']     = $y['payer_id'];
        $data['payment_duration'] = $userId_courseId[1];
        $data['invoiceId']      = $y['txn_id'];
        $data['payment_info']   = json_encode($x); 
        // echo "<pre>"; print_r($data);die();
        
        $check_tnx = $this->db->table('tbl_payment')->where('invoiceId',$data['invoiceId'])->get()->getResultArray();
        if (count($check_tnx) > 0 ){
            echo "match invoice";die();
        }
        
        array_shift($userId_courseId);
        array_shift($userId_courseId);
        

        $instra = print_r($data, true);
        

        $ch = curl_init('https://ipnpb.sandbox.paypal.com/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        
        if (!($res = curl_exec($ch))) {
            // error_log("Got " . curl_error($ch) . " when processing IPN data");
            curl_close($ch);
            exit;
        }
        curl_close($ch);
        //file_put_contents('abc.txt',$res);
		$test_data=strcmp($res,"VERIFIED");
		file_put_contents('abc1.txt',$test_data."\r\n");

        // inspect IPN validation result and act accordingly
        if (strcmp($res,"VERIFIED") == 100) {
			file_put_contents('abc2.txt',$res);
			
            $this->db->table('tbl_payment')->insert($data);
            $paymentId=$this->db->insertID();
            $today_timestamp = time();
            if ($userId_courseId != null) {
                foreach ($userId_courseId as $dacourseId) {
                      $pay['paymentId']=$paymentId;
                      $pay['courseId'] =$dacourseId;
                      $this->db->table('tbl_payment_details')->insert($pay);
                }

                $this->db->table('tbl_registered_course')->where('user_id',$courseID)->where('endTime <',$today_timestamp)->update(['status'=>0]);
				
                foreach ($userId_courseId as $singleCourse) {
                    $course['course_id'] = $singleCourse;
                    $rs_course_cost    = $RegisterClass->getCourseCost($course['course_id']);
                    $course['cost']    = $rs_course_cost[0]['courseCost'];
                    $course['user_id'] = $courseID;
                    $course['created'] = time();
                    $course['endTime'] = $endDate;
                    $RegisterClass->basicInsert('tbl_registered_course',$course);
                }
                
            }

			file_put_contents('abc3.txt','test');
            //$instra = print_r($data, true);
            //$notification_msg = 'Your Subscription with the Payment of $' .$_POST['mc_gross']. ' for the Package of '.$package_info[0]['PackageName'].' is complete';
            $user_data['payment_status'] = $data['payment_status'];
            $user_data['subscription_type'] ='signup';
            
            if($check_student->end_subscription != null){
				
                $end_subscription = $check_student->end_subscription;
                if($date > $end_subscription){
                    $user_data['end_subscription']  = $date;
                }else{
                    $user_data['end_subscription']  = $end_subscription;
                }
            }else{
                $user_data['end_subscription']  = $date;
            }
            
            file_put_contents('user_id.txt',$userID);
			file_put_contents('user_data.txt',$user_data);
            $userID = $data['user_id'];
            //$this->db->set('payment_status', $data['payment_status']);
            $this->db->table('tbl_useraccount')->where('id',$userID)->update($user_data);

            
            //check for student/parent
            if ($check_student->user_type == 6) {
              $this->db->table('tbl_useraccount')->where('id', $check_student->parent_id)->update($user_data);
              $this->session->set('userType', 6);
              
              $refUsers = $this->db->table('tbl_referral_users')->where('user_id',$check_student->id)->where('status',0)->get()->getRow();
            
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
                     $ckrfuser['referral_point'] = $referralPoint + $old_referral_point;
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
                     $ckrfByuser['referral_point'] = $referralPoint + $old_referral_point;
                     $ckrfByuser['total_point']    = $ref_taken_point + $totalByPoint;
                     $this->db->table('product_poinits')->where('user_id',$refferByUser)->update($ckrfByuser);
                    }else{
                     $ckrfByuser['user_id'] = $refferByUser;
                     $ckrfByuser['referral_point'] = $ref_taken_point;
                     $ckrfByuser['total_point']    = $ref_taken_point;
                     $this->db->table('product_poinits')->insert($ckrfByuser);
                    }
    
                    $this->db->table('tbl_referral_users')->where('user_id',$check_student->id)->update(['status' => 1]);
    
                   
                }
            }

            //insert payment info
            // IPN message values depend upon the type of notification sent.
            // To loop through the &_POST array and print the NV pairs to the screen:
            foreach ($y as $key => $value) {
                echo $key . " = " . $value . "<br>";
            }
        } elseif (strcmp($res, "INVALID") == 0) {
        }

        header("HTTP/1.1 200 OK");
    }
    
    public function no_debit_paypal_notify_qusStore()
    {
        
        $file = 'paypalnd_'.time().rand(9,9999).'.txt';
        $x = serialize($_POST);
        file_put_contents($file, $x);
        // $x = file_get_contents('paypalnd_16213302463664.txt');
        $y = unserialize($x);
        echo "<pre>"; 
        //print_r($y);die;
        
        $this->load->helper('commonmethods_helper');
        
        $req = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value) {
            $value = urlencode(stripslashes($value));
            $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value);
            $req .= "&$key=$value";
        }

        reset($_POST);
        $datas = print_r($_POST, true);
        /* save payer info to database */

        //$data['UserId'] = $_POST['custom'];
        $userId_courseId=explode(',', $y['custom']);
        $data['user_id']=$userId_courseId[0];
        //check for student/parent
        $check_student = $this->db->where('id',$data['user_id'])->get('tbl_useraccount')->row();
        if ($check_student->user_type == 6) {
          $data['user_id']=$check_student->id;
        }else{
          $data['user_id']=$userId_courseId[0];
        }
        
        $data['PaymentDate'] = time();
        $paymentType = $userId_courseId[1];
        $resource_sbject = $userId_courseId[2];
        if ($paymentType == 1) {
            $second = 30 * 24 * 3600;
            //$second = 24 * 3600;
        } elseif ($paymentType == 2) {
            $second = 30 * 6 * 24 * 3600;
        } elseif ($paymentType == 3) {
            $second = 30 * 12 * 24 * 3600;
        }
        $endDate =  $data['PaymentDate'] + $second;
        $date = date('Y-m-d',$endDate);
        //echo $date;die();
        $data['PaymentEndDate'] = $data['PaymentDate'] + $second;
        $data['total_cost']     = $y['mc_gross'];
       // $data['PackageId'] = $y['item_number'];
        $data['payment_status'] = $y['payment_status'];
        $data['SenderEmail']    = $y['payer_email'];
        $data['paymentType']    = 2; //maybe paypal
        $data['subscriptionId'] = 'No Debit';
        $data['customerId']     = $y['payer_id'];
        $data['payment_duration'] = $userId_courseId[1];
        $data['invoiceId']      = $y['txn_id']; 
        $data['subject']        = $resource_sbject; 
        $data['payment_info']   = json_encode($x);
        
        
        //   echo "<pre>"; 
        //   print_r($data);
        //   die();
        $check_tnx = $this->db->where('invoiceId',$data['invoiceId'])->get('tbl_qs_payment')->result_array();
        if (count($check_tnx) > 0 ){
            echo "match invoice";die();
        }
        

        $instra = print_r($data, true);
        
        $ch = curl_init('https://ipnpb.sandbox.paypal.com/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        $res = curl_exec($ch);
        
        
        if (!($res = curl_exec($ch))) {
            // error_log("Got " . curl_error($ch) . " when processing IPN data");
            curl_close($ch);
            exit;
        }
        curl_close($ch);
            
        $this->db->insert('tbl_qs_payment', $data);
        $paymentId=$this->db->insert_id();
        
        // inspect IPN validation result and act accordingly
        if (strcmp($res, "VERIFIED") == 0) {

            //insert payment info
            // IPN message values depend upon the type of notification sent.
            // To loop through the &_POST array and print the NV pairs to the screen:
            foreach ($y as $key => $value) {
                echo $key . " = " . $value . "<br>";
            }
        } elseif (strcmp($res, "INVALID") == 0) {
        }

        header("HTTP/1.1 200 OK");
    }
}
