<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MessageController extends BaseController
{
    public function showAllTopics()
    {
        $MessageClass = new \MessageClass();

        $data['allTopics'] = $MessageClass->allTopics();

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('message/all_topics', $data);
    }

    public function addMessageTopic()
    {
        $MessageClass = new \MessageClass();

        $post = $this->request->getVar();
        // echo '<pre>';
        // print_r($post);die();
        $input = $this->validate([
            'messageTopic' => 'required',
        ]);
        if (!$input) {
            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

            return view('message/add_message_topic', $data);
        } 
        else 
        {
            $dataToInsert = [
                'topic' => $post['messageTopic'],
                'creator_id' => $this->session->get('user_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $MessageClass->insert('message_topics', $dataToInsert);
            $this->session->set('success_msg', 'Message topic added successfully');
            return redirect()->to(base_url('message/topics'));
        }
    }

    public function DeleteMessageTopic($topicId)
    {

        $MessageClass = new \MessageClass();
        
        $MessageClass->delete('messages', ['topic'=>$topicId]);
        $status = $MessageClass->delete('message_topics', ['id'=>$topicId]);

        echo $status ? 'true' : 'false';
    }

    public function show_all_message($topic_id)
    {
       
        $MessageClass = new \MessageClass();
        $data['all_message'] = $MessageClass->get_message_by_topic($topic_id);
        // echo '<pre>';
        // print_r($data['all_message']);
        // die();
        $data['topic_id'] = $topic_id;
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return view('message/all_message', $data);
    }

    public function add_message($topic_id)
    {
        
        $user_id=$this->session->get('user_id');
        $MessageClass = new \MessageClass();
        $data['topic'] = $MessageClass->info('message_topics', ['id'=>$topic_id]);
        $data['all_school'] = $MessageClass->getInfo('tbl_useraccount', 'user_type', 4);
        $data['single_data']=$this->db->table('tbl_useraccount')->where('id',$user_id)->get()->getRow();

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('message/add_message', $data);
    }

    public function setMessage()
    {
  		
        $MessageClass = new \MessageClass();

        $post = $this->request->getVar();
  
        $schedule_date = explode(',', $this->request->getVar('dateToShow'));
        $dataToInsert['topic'] = $post['topicId'];
        $dataToInsert['body'] = $post['body'];
        $dataToInsert['type'] = 1;
        $dataToInsert['schedule_date'] = json_encode($post['dateToShow']);
        $dataToInsert['email_for_student'] = $this->request->getVar('email_for_student');
        $dataToInsert['student_grade'] = $this->request->getVar('student_grade');
        $dataToInsert['email_for_school'] = $this->request->getVar('email_for_school');
        $dataToInsert['school_id'] = $this->request->getVar('school_id');
        $dataToInsert['email_for_corporate'] = $this->request->getVar('email_for_corporate');
        $dataToInsert['corporate_id'] = $this->request->getVar('corporate_id');
        $dataToInsert['created_by'] = $this->session->get('user_id');
        $dataToInsert['created_at'] = date('Y-m-d H:i:s');
        $dataToInsert['updated_at'] = date('Y-m-d H:i:s');
        
        $message_id = $this->request->getVar('message_id');
    
        $message_info = $MessageClass->getInfo('messages', 'id', $message_id);

        if (!$message_info) {
            $message_id = $MessageClass->insert('messages', $dataToInsert);
        } else {
            $message_id = $message_info[0]['id'];
            $MessageClass->updateInfo('messages', 'id', $message_id, $dataToInsert);
        }

        $schedule_info = $MessageClass->getInfo('message_schedule', 'message_id', $message_id);
        if ($schedule_info) {
            $MessageClass->deleteInfo('message_schedule', 'message_id', $message_id);
        }

        foreach ($schedule_date as $schedule) {
            $schedule_data['message_id'] = $message_id;
            $schedule_data['schedule_date'] = $schedule;

            $MessageClass->insert('message_schedule', $schedule_data);
        }

        $this->session->set('success_msg', 'Message setted successfully');

        return redirect()->to(base_url('message/topics'));
    }
    public function edit_message($message_id)
    {
        $MessageClass = new \MessageClass();
        $data['message_info'] = $MessageClass->message_info($message_id);
//        $data['topic'] = $this->MessageModel->info('message_topics', ['id'=>$data['message_info'][0]['topic']]);
        $data['all_school'] = $MessageClass->getInfo('tbl_useraccount', 'user_type', 4);
        $data['schedule_date'] = [];

        if ($data['message_info']) {
            $schedule = array_column($data['message_info'], 'schedule_date');

            if (($schedule[0])) {
                $data['schedule_date'] = json_encode($schedule);
            }
        }
       
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('message/edit_message', $data);
    }

    public function delete_message($message_id)
    {
        $MessageClass = new \MessageClass();
        $status = $MessageClass->delete('messages', ['id'=>$message_id]);
        $MessageClass->delete('message_schedule', ['message_id'=>$message_id]);
        
        echo $status ? 'true' : 'false';
    }

    public function proceed_email()
    {

        error_reporting(0);
        $MessageClass = new \MessageClass();

        $post = $this->request->getVar();

        $schedule_date = explode(',', $this->request->getVar('dateToShow'));
        $dataToInsert['topic'] = $post['topicId'];
        $dataToInsert['body'] = $post['body'];
        $dataToInsert['type'] = 1;
        $dataToInsert['schedule_date'] = json_encode($post['dateToShow']);
        $dataToInsert['email_for_student'] = $this->request->getVar('email_for_student');
        $dataToInsert['student_grade'] = $this->request->getVar('student_grade');
        $dataToInsert['email_for_school'] = $this->request->getVar('email_for_school');
        $dataToInsert['school_id'] = $this->request->getVar('school_id');
        $dataToInsert['created_by'] = $this->session->get('user_id');
        $dataToInsert['created_at'] = date('Y-m-d H:i:s');
        $dataToInsert['updated_at'] = date('Y-m-d H:i:s');
        $message_id = $this->request->getVar('message_id');

        $message_info = $MessageClass->getInfo('messages', 'id', $message_id);

        if (!$message_info) {
            $message_id = $MessageClass->insert('messages', $dataToInsert);
        } else {
            $message_id = $message_info[0]['id'];
            $MessageClass->updateInfo('messages', 'id', $message_id, $dataToInsert);
        }
      
        //$this->load->helper('commonmethods_helper');

        //get all scheduled email for today
        $allNotice = $MessageClass->messageForToday();

        //loop through each notice creator
       
            if ($message_info[0]['email_for_student'] == 1) {
                //get all student associated creator
                //$allStudent = $this->MessageModel->search('tbl_enrollment', ['sct_id'=>$noticeCrator]);
                $allStudent = $MessageClass->get_all_student_by_grade($message_info[0]['student_grade']);
            }
            if ($message_info[0]['email_for_school'] == 1) {
                $allStudent = $MessageClass->get_all_student_by_school($message_info[0]['student_grade']);
            }

        $message_topic = $MessageClass->getInfo('message_topics', 'id', $message_info[0]['topic']);
            if (isset($message_topic[0]['topic']) && $message_topic[0]['topic'] != '')
            {
                $message_topic = $message_topic[0]['topic'];
            }else
            {
                $message_topic = 'message from q-study';
            }
            //$noticeCrator = $notice['created_by'];
            
            //$allStudentIds = array_column($allStudent, 'st_id');
            $allStudentIds = array_column(array_values(array_column($allStudent, null, 'st_id')), 'st_id');
        
            $emailToSent = $MessageClass->allStudentEmail($allStudentIds);

            // echo '<pre>';
            // print_r($allStudent);die();
            //send notice to parent and student
            foreach ($emailToSent as $student) {

                $studentEmail = $student['student_email'];
                $parentEmail = $student['parent_email'];

                if ($student['type'] == 2) {
                    $mailData = [
                        'to' => $studentEmail,
                        'subject' => $message_info[0]['title'],
                        'message' => $message_info[0]['body'],
                    ];
                    sendMail($mailData);
                } else {
                    $mailData = [
                        'to' => $parentEmail,
                        'subject' => $message_info[0]['title'],
                        'message' => $message_info[0]['body'],
                    ];
                    sendMail($mailData);

                    //cc mail 2
                    // $mailData = [
                        // 'to' => 'aftab@sahajjo.com',
                        // 'subject' => $message_info[0]['title'],
                        // 'message' => $message_info[0]['body'],
                    // ];
                    // sendMail($mailData);
                }
            }
			$this->session->set('success_msg', 'Message Sent Successfully');
            return redirect()->to(base_url('edit_message/'.$message_id));
    }


}
