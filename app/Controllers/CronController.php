<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class CronController extends BaseController
{
    public function proceed_email()
    {
		
        error_report_check();
        $MessageClass = new \MessageClass();

        $today_date=date('Y-m-d');
        $all_schedule=$this->db->table('message_schedule')->where('schedule_date',$today_date)->get()->getResultArray();
    
        if(isset($all_schedule))
        {

            foreach($all_schedule as  $all_schedules)
            {
               
                $message_info = $MessageClass->getInfo('messages', 'id', $all_schedules['message_id']);
               
                 foreach($message_info as $message_infos)
                 {
                  
                        if ((isset($message_infos['school_id']) && isset($message_infos['student_grade'])) || isset($message_infos['corporate_id']) && isset($message_infos['student_grade'])) {
                        
                            if(isset($message_infos['school_id']) && isset($message_infos['student_grade']))
                            {
                                $allStudent = $MessageClass->get_all_student_by_grade($message_infos['student_grade'],$message_infos['school_id']);
                             
                            }
                            if(isset($message_infos['corporate_id']) && isset($message_infos['student_grade']))
                            {
                                $allStudent = $MessageClass->get_all_student_by_grade($message_infos['student_grade'],$message_infos['corporate_id']);
                               
                            }
                       
                            
                        }

                        if ((isset($message_infos['school_id']) && ($message_infos['student_grade'] == '')) || isset($message_infos['corporate_id']) && ($message_infos['student_grade'] == '')) 
                        {
                            if(isset($message_infos['school_id']))
                            {
                                $allStudent = $MessageClass->get_all_student_by_school($message_infos['school_id']);
                            }
                            if(isset($message_infos['corporate_id']))
                            {
                                $allStudent = $MessageClass->get_all_student_by_school($message_infos['corporate_id']);
                            }
                           
                        }
                        // if (($message_infos['email_for_school'] == 1) || ($message_infos['email_for_corporate'] == 1)) {
                        //     $allStudent = $MessageClass->get_all_student_by_school($message_infos['student_grade']);
                        // }
                   
             
                        $message_topic = $MessageClass->getInfo('message_topics', 'id', $message_infos['topic']);
                           
                        if (isset($message_topic[0]['topic']) && $message_topic[0]['topic'] != '')
                        {
                            $message_topic = $message_topic[0]['topic'];
                        }else
                        {
                            $message_topic = 'message from q-study';
                        } 
                      
                        if(isset($allStudent))			
						{
                            $allStudentIds = array_column(array_values(array_column($allStudent, null, 'st_id')), 'st_id');

                            $emailToSent = $MessageClass->allStudentEmail($allStudentIds);
                           
                            $mailData['subject']=$message_topic;
                            $mailData['body']=$message_info[0]['body'];
                            foreach ($emailToSent as $student) {

                                $studentEmail = $student['student_email'];
                                $parentEmail = $student['parent_email'];
                
                                if ($student['type'] == 2) {
                                    // $mailData = [
                                    //     'to' => $studentEmail,
                                    //     'subject' => $message_topic,
                                    //     'message' => $message_info[0]['body'],
                                    // ];
                                    // sendMail($mailData);
                                } else {
                            
                                    $mailData[]=$parentEmail;

                                  
                                }
                            }
                            // echo '<pre>';
                            // print_r($mailData);die();
                            sendMail_new($mailData);
                        }
                 } 
            }
        }
   
        echo 'thanks';
    }
}
