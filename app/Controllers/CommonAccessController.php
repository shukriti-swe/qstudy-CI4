<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class CommonAccessController extends BaseController
{

    public function __construct()
    {

        $this->session=session();
        $user_id              = $this->session->get('user_id');
        $user_type            = $this->session->get('userType');
        $this->loggedUserId   = $user_id;
        $this->loggedUserType = $user_type;

    }
    public function imageUpload()
    {
        // echo '<pre>';
        // print_r($_FILES);die();

        $image_new = $this->request->getFile('file');
   
        if ($image_new != '') {
            $images_name = $image_new->getRandomName();
            $image=$image_new->move(ROOTPATH . '/public/assets/uploads/', $images_name);
        }
        $error = array();
        if ($image) {
            $base = base_url() . '/assets/uploads/' . $images_name;
            echo '{"fileName":"' . $images_name. '","uploaded":1,"url":"' . $base . '"}';
         
        } else {
            $error = 'image do not uploaded';
            print_r($error);
        }
    }


    public function contactUs()
    {
        $FaqClass=new \FaqClass();
        $AdminClass=new \AdminClass();

        $post = $this->request->getVar();
        //$clean = $this->security->xss_clean($post);
        if (!$post) {
            $user_id = $this->session->get('user_id');
            if ($user_id != '') {
                $data['user_info'] = $this->db->table('tbl_useraccount')->where('id',$user_id)->get()->getRow();

                $date = date('Y-m-d H:i:s');
                $time = strtotime($date);
                $startTime = date("Y-m-d H:i:s", strtotime('-2 minutes', $time));

                $this->db->table('feedback_files')->where('user_id',$user_id)->where('time <',$startTime)->where('status',0)->delete();

                $data['uploaded_files'] = $this->db->table('feedback_files')->where('user_id',$user_id)->where('status',0)->get()->getResultArray();

            }


            $faqItem = $FaqClass->info(['item_type'=>'contact_us']);
            $data['qStudyContactInfo'] = isset($faqItem['body'])?$faqItem['body']:null;
            $data['contacts_email'] = $this->db->table('tbl_setting')->where('setting_key','contact_email')->get()->getRow();
            // echo "<pre>";print_r($data['uploaded_files']);die;
            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
            return view('faqs/contact_us', $data);
        } else {
            $dataToInsert = [
                'user_name' => $post['userName'],
                'user_email' => $post['userEmail'],
                'message_body' => $post['userMessage'],
                'sent_at' =>date('Y-m-d H:i:s'),
                'updated_at' =>date('Y-m-d H:i:s'),
            ];
            // echo '<pre>';
            // print_r($dataToInsert);
            // die();
            $message = '';
            $message .= '<p>Name: '.$post['userName'].'</p>';
            $message .= '<p>Email: '.$post['userEmail'].'</p>';
            $message .= '<p>Message: '.$post['userMessage'].'</p>';
            $mailData = [
                'to' => "info@q-study.com",
                'subject' => 'Contact from Q-Study',
                'message' => $message,
            ];
            //$this->load->helper('commonmethods_helper');
            sendMail($mailData);
            $AdminClass->insertInfo('user_message', $dataToInsert);
            $this->session->set('success_msg', 'Message Sent Successfully');
            return redirect()->to(base_url('/'));
        }
    }

    public function feedbackfileUpload(){
        $userId = $this->request->getvar('userId');
        // $config = array(
        //     'upload_path'   => 'assets/uploads/feedback/',
        //     'allowed_types' => 'jpg|png|JPEG|pdf|PNG',
        //     'overwrite'     => 1,                       
        // );
        $files = $_FILES;
        $images = array();
        $contact_image = $this->request->getFileMultiple('filename');

        if(!empty($contact_image))
        {
            foreach ($contact_image as $key => $image) {
                $image_name = $image->getRandomName();
                $image_upload=$image->move(ROOTPATH . 'public/assets/uploads/feedback',$image_name);
                // $_FILES['abc']['name']= $files['filename']['name'][$key];
                // $_FILES['abc']['type']= $files['filename']['type'][$key];
                // $_FILES['abc']['tmp_name']= $files['filename']['tmp_name'][$key];
                // $_FILES['abc']['error']= $files['filename']['error'][$key];
                // $_FILES['abc']['size']= $files['filename']['size'][$key];
    
    
    
                $fullname = $image_name;
    
                $fileName = $image_name;
                $images[$key] = $fileName;
    
                // echo "<pre>";print_r($_FILES);
                if ($image_upload) {
                    $upData['filename'] = $fileName;
                    $upData['user_id']  = $userId;
                    $upData['time']     = date('Y-m-d H:i:s');
                    $this->db->table('feedback_files')->insert($upData);
                } else {
                    $error = 'Image do not uploaded perfectly';
                    print_r($error);
                }
    
            }
        }
     
        return redirect()->to(base_url('contact_us'));
    }

    public function send_feedback(){
       // echo 'jii';die();
        $AdminClass=new \AdminClass();
        $userId  = $this->session->get('user_id');

        $uploaded_files = $this->db->table('feedback_files')->where('user_id',$userId)->where('status',0)->get()->getResultArray();
        $userInfo = $this->db->table('tbl_useraccount')->where('id',$userId)->get()->getRow();

        $data['refLink'] = $this->request->getVar('ref_link');
        $data['feedback_topic'] = $this->request->getVar('feedback_topic');
        $data['details_body']   = $this->request->getVar('details_body');
        $messageBody   = $this->request->getVar('details_body');
        $data['name']   = $userInfo->name;
        $data['user_email']   = $userInfo->user_email;
        $unique_id = random_string('alnum',10);
        //echo $unique_id;die();
        $emailTo = $this->db->table('tbl_setting')->where('setting_key','contact_email')->get()->getRow();
        $to = $emailTo->setting_value;

        $dataToInsert = [
            'user_name' => $userInfo->name,
            'user_email' => $userInfo->user_email,
            'message_body' => $this->request->getVar('details_body'),
            'refLink' => $this->request->getVar('ref_link'),
            'feedback_topic' => $this->request->getVar('feedback_topic'),
            'sent_at' =>date('Y-m-d H:i:s'),
            'updated_at' =>date('Y-m-d H:i:s'),
            'user_id' =>$userId,
            'unique_id' => $unique_id,
            'status' =>'pending',
        ];
        $message = '';
        $message .= '<p>Name: '.$userInfo->name.'</p>';
        $message .= '<p>Email: '.$userInfo->user_email.'</p>';
        $message .= '<p>Message: '.$messageBody.'</p>';
        $mailData = [
            'to' => $to,
            'subject' => 'Contact from Q-Study',
            'message' => $message,
        ];
        sendMailAttachment($mailData,$uploaded_files,$userId);
        $AdminClass->insertInfo('user_message', $dataToInsert);
        $this->session->set('success_msg', 'Message Sent Successfully');
        $this->db->table('feedback_files')->where('user_id',$userId)->where('status',0)->update(['status'=>1,'unique_id'=>$unique_id]);
        return redirect()->to(base_url('contact_us'));
        // echo "<pre>";
        // print_r($this->input->post());
        // print_r($uploaded_files);
    }

    public function searchDictionaryWord()
    {
        $FaqClass=new \FaqClass();
        $QuestionClass=new \QuestionClass();

        if (!isset($_SESSION['userType']) || empty($_SESSION['userType']) ) {
            $_SESSION['prevUrl'] = base_url('/');
        }

        if (isset($_SESSION['userType']) || !empty($_SESSION['userType'])  ) {

            if (($_SESSION['userType']) == 3 ) {
                $_SESSION['prevUrl'] = base_url('/tutor/view_course');
            }
        }

        if (isset($_SESSION['userType'])) {
            if ($_SESSION['userType'] == 3 || $_SESSION['userType'] == 4 || $_SESSION['userType'] == 5 ) {
                $data['video_help'] = $FaqClass->videoSerialize(23, 'video_helps'); //rakesh
                $data['video_help_serial'] = 23;
            }else{
                $data['video_help'] = $FaqClass->videoSerialize(10, 'video_helps'); //rakesh
                $data['video_help_serial'] = 10;
            }
        }

        $data['allWords'] = $QuestionClass->allDictionaryWord();
        $data['pageType'] = "q-dictionary";

        return view('tutor/question/search_dictionary_word', $data);

    }

    public function searchTutor()
    {
        error_report_check();
        $FaqClass=new \FaqClass();
        $StudentClass=new \StudentClass();
        $TutorClass=new \TutorClass();
  
        if($this->loggedUserId)
        {
            $_SESSION['prevUrl'] = $_SERVER['HTTP_REFERER'];
        }else
        {
            $_SESSION['prevUrl'] = base_url().'welcome';
        }

        $post = $this->request->getVar();
        //$clean = $this->security->xss_clean($post);

        $data['video_help'] = $FaqClass->videoSerialize(8, 'video_helps'); //rakesh
        $data['video_help_serial'] = 8;

        $data['country_list'] = $StudentClass->getAllInfo(' tbl_country');
        //$data['subject_list'] = $this->tutor_model->uniqueColVals('tbl_subject', 'subject_name');
        $data['subject_list'] = $TutorClass->get_tutor_subject();
        $data['user_info'] = $TutorClass->userInfo($this->loggedUserId);
        $data['city_list'] = $TutorClass->uniqueColVals('additional_tutor_info', 'city');
        $data['state_list'] = $TutorClass->uniqueColVals('additional_tutor_info', 'state');
        

        $data['searchItems'] = [];
        if (!$post) {
            $data['searchItems'] = [];
        } else {
            $conditions = array_filter($post);
            $conditions['user_type'] = 3;
            if (isset($conditions['country_id'])) {
                $conditions['country_id'] =  (int) $post['country_id'];
            }
            
            $tutors = $TutorClass->tutorInfo($conditions);
            $data['searchItems'] = $tutors;
        }
  
        return view('tutor/tutor_search',$data);
    }

    public function showTutorProfile($userId)
    {
        $TutorClass=new \TutorClass();
        $AdminClass=new \AdminClass();

        $conditions = [
            'tbl_useraccount.id'=>$userId,
            'tbl_useraccount.user_type'=>3,
        ];
        $tutor = $TutorClass->tutorInfo($conditions);

        if (!isset($tutor[0])) {
            return view('errors/html/error_404.php');
        } else {
            $data['tutor_info'] = $tutor[0];
            $country = $TutorClass->getRow('tbl_country', 'id', $data['tutor_info']['country_id']);
            $data['tutor_info']['country'] = $country['countryName'];

            $conditions = [
                'user_id' =>$userId,
                'word_approved' =>1,
            ];
            $approvedTotal = $AdminClass->search('tbl_question', $conditions);
            $approvedTotal = count($approvedTotal);
            $data['word_approved'] = $approvedTotal;
            $_SESSION['prevUrl'] = $_SERVER['HTTP_REFERER'];
            return view('tutor/tutor_profile', $data);
        }
    }

    public function viewLandPageItem($item)
    {

        $FaqClass=new \FaqClass();
        $data['video_help'] = $FaqClass->videoSerialize(9, 'video_helps'); //rakesh
        $data['video_help_serial'] = 9;

        $item = $FaqClass->info(['item_type'=>$item]);
        
        if (count($item)) {
            $data['item_type'] = ($item['item_type']=='how_it_works') ? 'video' : 'text';
            $data['body'] = strlen($item['body'])?$item['body'] : '';
            $data['title'] = strlen($item['title'])?$item['title'] : '';
        } else {
            $data['body'] = '';
            $data['title'] = 'Not Found';
            $data['item_type'] = '';
        }

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
    

        return view('faqs/frontPageItems/viewItems',$data);
    }


    public function viewFaq($id)
    {
        //echo $id;die();
        $FaqClass=new \FaqClass();
        $data['faq'] = $FaqClass->info(['id'=>$id]);
        // echo '<pre>';
        // print_r($data['faq']);
        // die();
        if (!count($data['faq'])) {
            return view('errors/html/error_404');
        }

        // if (!empty($_SERVER['HTTP_REFERER'])) {
        //     if (strpos($_SERVER['HTTP_REFERER'],"/video")) {
        //     $_SESSION['prevUrl'] = $_SESSION['prevUrl'] = base_url('/');
        //     }else{
        //         $_SESSION['prevUrl'] = $_SERVER['HTTP_REFERER'];
        //     }
        // }

        $data['video_help'] = $FaqClass->videoSerialize(10, 'video_helps');
        $data['video_help_serial'] = 10;

        $_SESSION['prevUrl'] = base_url('/').'/faq/view/33';

        $data['allFaqs'] = $FaqClass->allFaqs();
        
        return view('faqs/view_faq',$data);

    }
    public function emailNotExists()
    {
        $AdminClass=new \AdminClass();
        $post = $this->request->getVar();
        $email = $post['email'];

        $emailExists = $AdminClass->getInfo('tbl_useraccount', 'user_email', $email);
        
        echo count($emailExists)>0 ? 'false' : 'true';
    }
	
	 public function countMessage(){
        $userId  = $this->session->get('user_id');
		 $messages = $this->db->table('tbl_compose_message')->where('reciver_id',$userId)->where('status',0)->orderBy('id','desc')->limit(7)->get()->getResultArray();
		 if(count($messages) > 0){
			 echo 1;
		 }else{
			 echo 0;
		 }       
    }
}
