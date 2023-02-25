<?php

namespace App\Controllers;

use AdminClass;
use App\Controllers\BaseController;

class ModuleController extends BaseController
{

    public function __construct()
    {
        $this->session=session();
        $user_id              = $this->session->get('user_id');
        $user_type            = $this->session->get('userType');
        $this->loggedUserId   = $user_id;
        $this->loggedUserType = $user_type;

        $PreviewClass=new \PreviewClass();
        $user_info = $PreviewClass->userInfo($user_id);
        
        if ($user_info[0]['countryCode'] == 'any') {
            $user_info[0]['zone_name'] = 'Australia/Lord_Howe';
        }
        
        $this->site_user_data = array(
            'userType' => $user_type,
            'zone_name' => $user_info[0]['zone_name'],
            'country_id' => $user_info[0]['country_id'],
        );
    }

    
    public function get_draw_image()
    {
       
        //$this->load->library('image_lib');
        $img = $_POST['imageData'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        $path = 'assets/uploads/preview_draw_images/';
        $draw_file_name = 'draw'.uniqid();
        $file = $path . $draw_file_name . '.png';
        file_put_contents($file, $data);

        // $imginfo = getimagesize($file);
        // $imgwidth = $imginfo[0];
        // $imgheight = $imginfo[1];
        
        // $config['image_library'] = 'gd2';
        // $config['source_image'] = $file;
        // $config['maintain_ratio'] = true;
        // // $config['width'] = 400;
        // // $config['height'] = 250;

        // $config['width'] =  $imgwidth;
        // $config['height'] = $imgheight;

        // $this->image_lib->initialize($config);
        // $this->image_lib->resize();
        
        echo base_url().'/'.$file;
    }

    public function all_module()
    { 
        $FaqClass=new \FaqClass();
        $TutorClass=new \TutorClass();
        $AdminClass=new \AdminClass();
        $TutorClass=new \TutorClass();
        $ModuleClass=new \ModuleClass();

        $_SESSION['prevUrl'] = $_SERVER['HTTP_REFERER'];
        if (strpos($_SESSION['prevUrl'], "edit-module") || strpos($_SESSION['prevUrl'], "add-module")) {
            if (!empty($_GET['country'])) {
                $_SESSION['prevUrl'] = base_url('/qstudy/view_course/?country=') . $_GET['country'];
            } else {
                $_SESSION['prevUrl'] = base_url('/qstudy/view_course/');
            }
        }
        
        $data['video_help'] = $FaqClass->videoSerialize(25,'video_helps'); //rakesh
        $data['video_help_serial'] = 25;
        $user_id = $this->session->get('user_id');
        $data['user_info'] = $TutorClass->userInfo($user_id);
        $conditions = [
            'user_id' => $user_id,
            'country' => isset($_GET['country']) ? $_GET['country'] : '',
        ];

        $conditions = array_filter($conditions);
        //$data['all_module'] = $this->Admin_model->search('tbl_module', $conditions);
        $data['all_module'] = $AdminClass->getModule('tbl_module', $conditions);

        // $data['headerlink'] = $this->load->view('dashboard_template/headerlink', $data, true);
        // $data['header']     = $this->load->view('dashboard_template/header', $data, true);
        // $data['footerlink'] = $this->load->view('dashboard_template/footerlink', '', true);


        $data['all_grade'] = $TutorClass->getAllInfo('tbl_studentgrade');
        $data['all_modules']= $TutorClass->getAllInfo('tbl_moduletype');
       
        $data['all_subject'] = $TutorClass->getInfo('tbl_subject', 'created_by', $user_id);
        //echo "<pre>";print_r($data['all_subject']);die();
        $data['all_course']=$this->db->table('tbl_course')->get()->getResultArray(); 
        // $data['allRenderedModType'] = $this->renderAllModuleType();

        $data['allsubjects']  = $ModuleClass->getAllSubjects($user_id);
        $data['allchapters']  = $ModuleClass->getAllChapters($user_id);

        $country_id = $this->session->get('selCountry');
        //echo 'jiii1146';die();
        $data['courses'] = $ModuleClass->getAllCourse($country_id);
        //echo "<pre>";print_r($data['courses']);die();
        /*=============================================================================
                                    pagination code
        ===============================================================================*/
        // $this->load->library('pagination');

        // $config = array();
		// $config["base_url"] = base_url()."/details-module";
		// $config["total_rows"] = $ModuleClass->countTblNewModuleRows();
		
		// $config["per_page"] = 10;
		// $config["uri_segment"] = 2;

		// $config['full_tag_open'] = '<ul class="pagination">';        
        // $config['full_tag_close'] = '</ul>';        
        // $config['first_link'] = 'First';        
        // $config['last_link'] = 'Last';        
        // $config['first_tag_open'] = '<li class="page-item"><span class="page-link">';        
        // $config['first_tag_close'] = '</span></li>';        
        // $config['prev_link'] = '&laquo';        
        // $config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';        
        // $config['prev_tag_close'] = '</span></li>';        
        // $config['next_link'] = '&raquo';        
        // $config['next_tag_open'] = '<li class="page-item"><span class="page-link">';        
        // $config['next_tag_close'] = '</span></li>';        
        // $config['last_tag_open'] = '<li class="page-item"><span class="page-link">';        
        // $config['last_tag_close'] = '</span></li>';        
        // $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';        
        // $config['cur_tag_close'] = '</a></li>';        
        // $config['num_tag_open'] = '<li class="page-item"><span class="page-link">';        
        // $config['num_tag_close'] = '</span></li>';

		// // $config['attributes'] = array('class' => 'myclass');
		// $this->pagination->initialize($config);

		// $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
		// $data["links"] = $this->pagination->create_links();
         $data['all_module_questions'] = $ModuleClass->getTblNewModule();
    
        foreach($data['all_module_questions'] as $key => $value){
            $data['all_module_questions'][$key]['country_name'] = $ModuleClass->getCountryName($value['country']);
            $data['all_module_questions'][$key]['course_name'] = $ModuleClass->getCourseName($value['chapter']);
        }
        // echo '<pre>';
        // print_r($data['all_module_questions']);
        // die();
        // $data['countryName'] = $this->ModuleModel->getAllCourse($country_id);all_module_questions

        $data['allCountry'] = $this->db->table('tbl_country')->get()->getResultArray();
        // echo "<pre>"; print_r($data['all_module_questions']); die();


        // check password added shvou
        $data['checkNullPw'] = $this->db->table('tbl_setting')->where("setting_key", "qstudyPassword")->where("setting_type !=", '')->get()->getResultArray();

        return view('module/all_module', $data);

    }

    public function createModule($id="")
    {
        $FaqClass=new \FaqClass();
        $TutorClass=new \TutorClass();
        $AdminClass=new \AdminClass();
        $ModuleClass=new \ModuleClass();

        if(empty($id)){
            $this->db->table('tbl_pre_module_temp')->truncate();
            $module_info['module_name'] = null;
            $module_info['grade_id'] = null;
            $module_info['module_type'] = null;
            $module_info['course_id'] = null;
            $module_info['show_student'] = null;
            $module_info['serial'] = null;
        
            $this->session->set('module_info_creadiential', $module_info);
        }
        $this->session->remove('module_status');
        $this->session->remove('module_edit_id');
        $this->session->remove('param_module_id');
        $data['module_cre_info'] = $this->session->get('module_info_creadiential');
   
       

        $_SESSION['prevUrl'] = $_SERVER['HTTP_REFERER'];

        if (strpos($_SESSION['prevUrl'], "edit-module") || strpos($_SESSION['prevUrl'], "add-module")) {
            if (!empty($_GET['country'])) {
                $_SESSION['prevUrl'] = base_url('/qstudy/view_course/?country=') . $_GET['country'];
            } else {
                $_SESSION['prevUrl'] = base_url('/qstudy/view_course/');
            }
        }

        $data['video_help'] = $FaqClass->videoSerialize(25, 'video_helps'); //rakesh
        $data['video_help_serial'] = 25;

        $user_id = $this->session->get('user_id');
        $data['user_info'] = $TutorClass->userInfo($user_id);
        $conditions = [
            'user_id' => $user_id,
            'country' => isset($_GET['country']) ? $_GET['country'] : '',
        ];
        $conditions = array_filter($conditions);
        //$data['all_module'] = $this->Admin_model->search('tbl_module', $conditions);
        $data['all_module'] = $AdminClass->getModule('tbl_module', $conditions);

        // $data['headerlink'] = $this->load->view('dashboard_template/headerlink', $data, true);
        // $data['header']     = $this->load->view('dashboard_template/header', $data, true);
        // $data['footerlink'] = $this->load->view('dashboard_template/footerlink', '', true);
        
        $data['all_grade']          = $TutorClass->getAllInfo('tbl_studentgrade');
        $data['all_module_type']    = $TutorClass->getAllInfo('tbl_moduletype');


        $data['all_subject']        = $TutorClass->getInfo('tbl_subject', 'created_by', $user_id);
        $data['all_course']        = $this->db->table('tbl_course')->get()->getResultArray();
        $data['allRenderedModType'] = $this->renderAllModuleType();

        $data['module_types'] = $ModuleClass->getModuleType();
        $country_id = $this->session->get('selCountry');
        $data['courses'] = $ModuleClass->getAllCourse($country_id);

        $data['allCountry']  = $this->db->table('tbl_country')->get()->getResultArray();
 
        // $data['module_types'] = $this->ModuleModel->getModuleType();
   
        $data['tbl_pre_module_temp'] = $ModuleClass->getTblPreModuleTempCourse();
        $question_list = [];
        foreach ($data['tbl_pre_module_temp'] as $key => $row) {
            $question_list[] = $ModuleClass->getTblQuestion($row['question_id']);
            $user_id = $this->session->get('user_id');
            $find_lists = $ModuleClass->getQuestions($row['question_type'],$user_id,$country_id);
               $i=1;
               $order = 0;
               foreach($find_lists as $question){
                    if($question['id']==$row['question_id']){
                        $order = $i;
                      break;
                    }
               $i++;}
              // echo $order.'<br>';
            $question_list[$key]['order'] = $order;
            $question_list[$key]['question_order'] = $row['question_order'];

            $question_list[$key]['tbl_id'] = $row['id'];
        }
        // die();
        foreach ($question_list as $key => $type) {
            $question_list[$key]['question_name'] = $ModuleClass->getTblQuestionType($type['questionType']);
            $question_list[$key]['count'] = $ModuleClass->getAllTblQuestion($type['questionType']);
        }
        $data['question_list'] = $question_list;
        $data['loggedUserType'] = $this->session->get('userType');
        // echo "<pre>"; print_r($question_list); die();
        
        $studentIds = $TutorClass->allStudents(['sct_id' => $user_id]);
        //echo "<pre>"; print_r($studentIds);die;
        $data['allStudents']  = $this->renderStudentIds($studentIds);
        $data['allsubjects']  = $ModuleClass->getAllSubjects($user_id);
        $data['allchapters']  = $ModuleClass->getAllChapters($user_id);

        // check password added shvou
        $data['checkNullPw'] = $this->db->table('tbl_setting')->where("setting_key", "qstudyPassword")->where("setting_type !=", '')->get()->getResultArray();
        return view('module/create_module', $data);
    }

    public function renderAllModuleType($selectedId = -1)
    {
        $ModuleClass=new \ModuleClass();
        $option      = '';
        $option     .= '<option value="">--Moduletype--</option>';
        $moduleTypes = $ModuleClass->allModuleType();

        foreach ($moduleTypes as $moduleType) {
            $sel     = ($moduleType['id'] == $selectedId) ? 'selected' : '';
            $option .= '<option value="'.$moduleType['id'].'" '.$sel.'>'.$moduleType['module_type'].'</option>';
        }

        return $option;
    }//end renderAllModuleType()

    public function renderStudentIds($studentIds, $selectedIds = '')
    {
        $StudentClass=new \StudentClass();
        $sel    = [];
        $stdIds = [];
        if (strlen($selectedIds) > 1) {
            $stdIds = json_decode($selectedIds);
        }

        $option  = '';
        $option .= '<option value="">--Student--</option>';
        if ($studentIds) {
            foreach ($studentIds as $studentId) {
                $stInfo  = $StudentClass->getInfo('tbl_useraccount', 'id', $studentId);
                $sel     = in_array($studentId, $stdIds) ? "selected" : "";
                $option .= '<option value="'.$studentId.'" '.$sel.'>'.$stInfo[0]['name'].'</option>';
            }
        }
       /* print_r($studentIds);
        echo '<br>';
        print_r($selectedIds);
        die;*/
        return $option;
    }//end renderStudentIds()

    public function save_module_info(){
        $module_info['module_name'] = $this->request->getVar('module_name');
        $module_info['grade_id'] = $this->request->getVar('grade_id');
        $module_info['module_type'] = $this->request->getVar('module_type');
        $module_info['course_id'] = $this->request->getVar('course_id');
        $module_info['show_student'] = $this->request->getVar('show_student');
        $module_info['serial'] = $this->request->getVar('serial');
        $module_info['trackerName'] = $this->request->getVar('trackerName');
        $module_info['individualName'] = $this->request->getVar('individualName');
        $module_info['enterDate'] = $this->request->getVar('enterDate');
        $module_info['isSms'] = $this->request->getVar('isSms');
        $module_info['isAllStudent'] = $this->request->getVar('isAllStudent');
        $module_info['video_link_1'] = $this->request->getVar('video_link_1');
        $module_info['instruct_1'] = $this->request->getVar('instruct_1');
        $module_info['videoName'] = $this->request->getVar('videoName');
        $module_info['timeStart'] = $this->request->getVar('timeStart');
        $module_info['timeEnd'] = $this->request->getVar('timeEnd');
        $module_info['optTime'] = $this->request->getVar('optTime');
        $module_info['subject_id'] = $this->request->getVar('subject_id');
        $module_info['chapter_id'] = $this->request->getVar('chapter_id');
        
        
        $this->session->set('module_info_creadiential',$module_info);
    }

    public function getIndividualStudent()
    {
        $ModuleClass=new \ModuleClass();
        $uType = $this->session->get('userType');
        if ($uType==1 || $uType==2 || $uType==6) {
            //user type parent, upper student,student shouldn't add module
            $this->session->set('error_msg', "You've no access to view this page");
            return redirect()->to(base_url('/'));
        }

        $student_grade = $this->request->getVar('studentGrade');
        $tutor_type = $this->request->getVar('tutor_type');
        $country_id = '';
        $subject = '';
        $user_id = '';
        $subject_name = '';
        $course_id = '';
        
        if (($this->request->getVar('course_id'))) {
            $course_id = $this->request->getVar('course_id');
        }
        if ($this->request->getVar('country_id') != '') {
            $country_id = $this->request->getVar('country_id');
        }
        if ($tutor_type == 7 && $this->request->getVar('subject') != '') {
            // $subject = $this->input->post('subject');

            // $subject_info = $this->ModuleModel->search('tbl_subject', ['subject_id'=>$subject]);

            // $subject_name = $subject_info[0]['subject_name'];
        }if ($tutor_type == 3) {
            $user_id = $this->session->get('user_id');
        }
        
        $students = $ModuleClass->getIndividualStudent($student_grade, $tutor_type, $country_id, $subject_name, $user_id, $course_id);
        foreach ($students as $row) {
            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
        }
    }

    public function saveNewModuleQuestion()
    {
        // echo "<pre>"; print_r($_POST); die();
        // echo "<pre>"; print_r($_GET['country']); die();
        $AdminClass=new \AdminClass();
        $ModuleClass=new \ModuleClass();

        $uType=$this->session->get('userType');
        if ($uType == 1 || $uType == 2 || $uType == 6) {
            //user type parent, upper student,student shouldn't add module
            $this->session->set('error_msg', "You've no access to view this page");
            return redirect()->to(base_url('/'));
        }

        $post = $this->request->getVar();
        if ($post['moduleType'] == 1 || $post['moduleType'] == 2 || $post['moduleType'] == 5) {
            $post['dateCreated'] = date('Y-m-d');
        }
        $date = $post['dateCreated'];

        $startTime = date('Y-m-d', strtotime($date)) . ' ' . $post['startTime'];
        $endTime = date('Y-m-d', strtotime($date)) . ' ' . $post['endTime'];

        $video_link = str_replace('</p>', '', $_POST['video_link']);
        $video_array = array_filter(explode('<p>', $video_link));

        $new_array = array();
        foreach ($video_array as $row) {
            $new_array[] = strip_tags($row);
        }
        // print_r(json_encode($video_array));die;
        // $video_link[] = $this->input->post('video_link');

        //$clean             = $this->security->xss_clean($post);
        $optionalTime      = explode(':', isset($post['optTime']) ? $post['optTime'] : "0:0");
        $optionalHour      = isset($optionalTime[0]) ? (int)$optionalTime[0] * 60 * 60 : 0; //second
        $optionalMinute    = isset($optionalTime[1]) ? (int)$optionalTime[1] * 60    : 0; //second

        //get users latest module order
        $mods = $AdminClass->search('tbl_module', ['user_id' => $this->session->get('user_id')]);
        if (count($mods)) {
            $allOrders = array_column($mods, 'ordering');
            $maxOrder = max($allOrders);
            $nextOrder = $maxOrder + 1;
        } else {
            $nextOrder = 0;
        }
        //echo $clean['moduleType'].'///'.$clean['studentGrade'];die();
        $module_check = $ModuleClass->get_module_serial($post['moduleType'],$post['studentGrade'],$post['course_id']);
        //print_r($module_check);die();
        if(!empty($module_check)){
           $serial_no = $module_check['max_serial']+1;
        }else{
            $serial_no = 1;
        }
        
        $moduleTableData   = [];
        $moduleTableData[] = [
            'moduleName'        => $post['moduleName'],
            'ordering'          => $nextOrder,
            'trackerName'       => $post['trackerName'],
            'instruction'       => $post['instruction'],
            'individualName'    => isset($post['individualName']) ? $post['individualName'] : '',
            'isSMS'             => isset($post['isSMS']) ? $post['isSMS'] : 0,
            'isAllStudent'      => isset($post['isAllStudent']) ? $post['isAllStudent'] : 0,
            'individualStudent' => isset($post['individualStudent']) ? json_encode($post['individualStudent']) : '',
            'course_id'         => isset($post['course_id']) ? $post['course_id'] : '',
            'video_link'        => json_encode($new_array),
            'video_name'        => isset($_POST['video_name']) ? $_POST['video_name'] : '',
            'subject'           => $post['subject'],
            'chapter'           => $post['chapter'],
            'country'           => $this->session->get('selCountry'),
            'studentGrade'      => $post['studentGrade'],
            'moduleType'        => $post['moduleType'],
            'user_id'           => $this->session->get('user_id'),
            'user_type'         => $this->session->get('userType'),
            'exam_date'         => isset($post['dateCreated']) ? strtotime($post['dateCreated']) : 0,
            'exam_start'        => isset($post['startTime']) ? ($startTime) : 0,
            'exam_end'          => isset($post['endTime']) ? ($endTime) : 0,
            'optionalTime'      => $optionalHour + $optionalMinute,
            'show_student'      => isset($_POST['show_student']) ? $_POST['show_student'] : 0,
            //'serial'      => isset($_POST['serial']) ? $_POST['serial'] : 0,
            'serial'      => $serial_no,
        ];


        // echo "<pre>"; print_r($moduleTableData); die();
      
        // Save module info first
        $moduleId = $ModuleClass->insert('tbl_module', $moduleTableData);
        $module_insert_id = $this->db->insertID();
       
        // If ques order set record those to tbl_modulequestion table
        $arr   = [];
        $items = $ModuleClass->getTblPreModuleTempCourse();
        // echo "<pre>"; print_r($items); die();

        if (count($items)) {
            foreach ($items as $item) {
                $arr[] = [
                    'question_id'    => $item['question_id'],
                    'question_type'  => $item['question_type'],
                    'module_id'      => $moduleId,
                    'question_order' => $item['question_order'],
                    'created'        => time(),
                ];
            }
            $ModuleClass->insert('tbl_modulequestion', $arr);
            $this->session->set('module_msg', 'Save Successfully');

            
        }

        if ($post['moduleType'] == 2) {
            $repetition_data = [];
            $a = [];
            $i = 0;
            $j = 1;
            $date = date('Y-m-d');
            while ($j < 365) {
                $j = $i * 30 + 1;
                $a[] = $j . '_' . date('Y-m-d', strtotime($date . ' +' . $j . ' days'));;
                $j += 1;
                $a[] = $j . '_' . date('Y-m-d', strtotime($date . ' +' . $j . ' days'));;
                $i++;
                if ($j == 362) {
                    break;
                }
            }

            $repetition_days = json_encode($a);
            $this->db->table('tbl_module')->where('id', $module_insert_id)->update(['repetition_days' => $repetition_days]);
        }
        $this->db->table('tbl_pre_module_temp')->truncate();
        // echo "<pre>"; print_r($arr); die();

        return redirect()->to(base_url().'/all-module');
        // $this->session->set_flashdata('success_msg', 'Module Saved Successfully.');
        // redirect('all-module');
    } //end

    public function deleteNewModule($moduleId)
    {
        $ModuleClass=new \ModuleClass();

        $builder = $this->db->table('tbl_module');
        $builder->select('*');
        $builder->where('id', $moduleId);
        $query_new = $builder->get();
        $chk_exits = $query_new->getResultArray();
        
        $course_id = $chk_exits[0]['course_id'];
        $grade_id = $chk_exits[0]['studentGrade'];
        $moduleType = $chk_exits[0]['moduleType'];
        $user_id = $chk_exits[0]['user_id'];
        $serial = $chk_exits[0]['serial'];

        $builder = $this->db->table('tbl_module');
        $builder->select('*');
        $builder->where('moduleType', $moduleType);
        $builder->where('studentGrade', $grade_id);
        $builder->where('course_id', $course_id);
        $builder->where('user_id', $this->loggedUserId);
        $builder->where('serial >', $serial);
        $query_new = $builder->get();
        $chk_exits = $query_new->getResultArray();

        // echo '<pre>';
        // print_r($chk_exits);
        // die();

        $ModuleClass->deleteTblNewModule($moduleId);
        $ModuleClass->deleteTblNewModuleQuestion($moduleId);

        
        if(!empty($chk_exits)){
             
            foreach($chk_exits as $result){
               $module_id = $result['id'];
               $new_serial = $result['serial']-1;

               $datas['serial'] = $new_serial;
               $builder = $this->db->table('tbl_module');
               $builder->where('id', $module_id);
               $builder->update($datas);

            }

        }

        // echo "<pre>"; print_r($items); die();
        $this->session->set('delete_success', "Successfully Deleted !!");
        //redirect(base_url() . 'details-module', 'refresh');
        return redirect()->to(base_url().'/all-module');
    } 

    public function module_preview($modle_id, $question_order_id)
    {
     
        $uri = new \CodeIgniter\HTTP\URI(current_url());
        $TutorClass=new \TutorClass();
        $ModuleClass=new \ModuleClass();
        $data['order'] = $uri->getSegment('4'); 
        $_SESSION['q_order'] =$uri->getSegment('4'); 
        $_SESSION['q_order_module'] = $uri->getSegment('3'); 
        echo "lll".'gfh h  g hg ht h';die();
        
        $data['time_zone_new']=$this->site_user_data['zone_name'];
        $data['user_info']  = $TutorClass->userInfo($this->session->get('user_id'));
        $data['userType'] = $data['user_info'][0]['user_type'];
        
        //date_default_timezone_set($this->site_user_data['zone_name']);
        $exact_time = time();
        $this->session->set('exact_time', $exact_time);
        
        $data['question_info_s'] = $TutorClass->getModuleQuestion($modle_id, $question_order_id, null);
        $data['main_module'] = $TutorClass->getInfo('tbl_module', 'id', $modle_id);
        // echo '<pre>';
        // print_r($data); 
        // die();
        
        $data['total_question'] = $TutorClass->getModuleQuestion($modle_id, null, 1);
        
        $data['page_title']     = '.:: Q-Study :: Tutor yourself...';
       ;
        $data['quesOrder'] = $question_order_id;

        //if question not found
        if (!$data['question_info_s'][0]['id']) {
            $question_order_id = $question_order_id + 1;
            return redirect()->to(base_url('get_tutor_tutorial_module/'.$modle_id.'/'.$question_order_id));
        }
		
        if (isset($data['question_info_s'][0])) {
            $quesInfo = json_decode($data['question_info_s'][0]['questionName']);
            
            if ($data['question_info_s'][0]['questionType'] == 1) {
                
                $_SESSION['q_order_2'] = $uri->getSegment('4'); 
                return view('module/preview/preview_general',$data);
            } elseif ($data['question_info_s'][0]['questionType'] == 2) {

                $_SESSION['q_order_2'] = $uri->getSegment('4'); 
                return view('module/preview/preview_true_false',$data);

            } elseif ($data['question_info_s'][0]['questionType'] == 3) {
              
                $_SESSION['q_order_2'] = $uri->getSegment('4'); 
                $data['question_info_vcabulary'] = json_decode($data['question_info_s'][0]['questionName']);
                return view('module/preview/preview_vocabulary',$data);

            } elseif ($data['question_info_s'][0]['questionType'] == 4) {

                $_SESSION['q_order_2'] = $uri->getSegment('4'); 
                $data['question_info_vcabulary'] = $quesInfo;
                return view('module/preview/preview_multiple_choice',$data);

            } elseif ($data['question_info_s'][0]['question_type'] == 5) {

                $_SESSION['q_order_2'] = $this->uri->segment('3'); 
                $data['question_info_vcabulary'] = json_decode($data['question_info_s'][0]['questionName']);
                $data['maincontent'] = $this->load->view('module/preview/preview_multiple_response', $data, true);
            } elseif ($data['question_info_s'][0]['questionType'] == 6) {
             
                $_SESSION['q_order_2'] = $uri->getSegment('3'); 
                // skip quiz
                $data['numOfRows']    = isset($quesInfo->numOfRows) ? $quesInfo->numOfRows : 0;
                $data['numOfCols']    = isset($quesInfo->numOfCols) ? $quesInfo->numOfCols : 0;
                $data['questionBody'] = isset($quesInfo->question_body) ? $quesInfo->question_body : '';
                $data['questionId']   = $data['question_info_s'][0]['question_id'];
                $quesAnsItem          = $quesInfo->skp_quiz_box;
                $items                = indexQuesAns($quesAnsItem);

                $data['skp_box']     = renderSkpQuizPrevTable($items, $data['numOfRows'], $data['numOfCols']);
                return view('module/preview/skip_quiz',$data);
            } elseif ($data['question_info_s'][0]['question_type'] == 7) {
                $_SESSION['q_order_2'] = $this->uri->segment('3'); 
                //
                $data['question_info_left_right'] = json_decode($data['question_info_s'][0]['questionName']);
                $data['maincontent'] = $this->load->view('module/preview/preview_matching', $data, true);
            } elseif ($data['question_info_s'][0]['questionType'] == 8) {
                $_SESSION['q_order_2'] = $this->uri->segment('3'); 
                // assignment
                $data['questionBody']    = isset($quesInfo->question_body) ? $quesInfo->question_body : '';
                $items                   = $quesInfo->assignment_tasks;
                $data['totalItems']      = count($items);
                $data['assignment_list'] = renderAssignmentTasks($items);
                $data['maincontent']     = $this->load->view('module/preview/assignment', $data, true);
            } elseif ($data['question_info_s'][0]['questionType'] == 9) {
                 $_SESSION['q_order_2'] = $this->uri->segment('3'); 

                $info = array();
                $titles = array();
                $title = array();
                $questionList = json_decode($data['question_info_s'][0]['questionName'] , true);
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

                $paragraph = json_decode($data['question_info_s'][0]['questionName'] , true);
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

                $data['maincontent'] = $this->load->view('module/preview/module_preview_storyWrite', $data, true);  

            } elseif ($data['question_info_s'][0]['questionType'] == 10) {
               // echo 'sddd1';die();
                $_SESSION['q_order_2'] = $uri->getSegment('4'); 
                $data['question_info'] = json_decode($data['question_info_s'][0]['questionName'], true);
                return view('module/preview/preview_times_table',$data);
            } elseif ($data['question_info_s'][0]['questionType'] == 11) {
                $_SESSION['q_order_2'] = $uri->getSegment('4'); 
                $data['question_info']   = json_decode($data['question_info_s'][0]['questionName'], true);
                return view('module/preview/preview_algorithm',$data);
            } elseif ($data['question_info_s'][0]['questionType'] == 12) {
                $_SESSION['q_order_2'] = $this->uri->segment('3'); 
                $data['question_info']   = json_decode($data['question_info_s'][0]['questionName'], true);
                $data['maincontent']     = $this->load->view('module/preview/workout_quiz', $data, true);
            } elseif ($data['question_info_s'][0]['questionType'] == 13) {
                $_SESSION['q_order_2'] = $this->uri->segment('3'); 
                $data['question_info_vcabulary'] = $quesInfo;
                $data['maincontent']             = $this->load->view('module/preview/preview_matching_workout', $data, true);
            }elseif ($data['question_info_s'][0]['questionType'] == 14) {
                $_SESSION['q_order_2'] = $uri->getSegment('4');
                if (!empty($_SERVER['HTTP_REFERER'])) {
                $_SESSION["previous_page"] = $_SERVER['HTTP_REFERER'];

                $data["last_question_order"] = $_SESSION['q_order_2'];
                // print_r($_SESSION["previous_page"]); die();
                $data['question_info_vcabulary'] = json_decode($data['question_info_s'][0]['questionName']);
                // print_r(['question_info_vcabulary']); die();
                $tutorialId = $data['question_info_s'][0]['question_id'];
                $data['tutorialInfo'] = $TutorClass->getInfo('for_tutorial_tbl_question', 'tbl_ques_id', $tutorialId);
                return view('module/preview/preview_tutorial',$data);
            }
            else{
                // print_r($_SESSION["previous_page"]); die();
                redirect($_SESSION["previous_page"]);

            }

                
            }elseif ($data['question_info_s'][0]['questionType'] == 15)
            {
                $data['question_item']=$data['question_info_s'][0]['questionType'];
                $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
                $data['question_info_ind'] = $data['question_info'];
                if (isset($data['question_info_ind']->percentage_array))
                {
                    $data['ans_count'] = count((array)$data['question_info_ind']->percentage_array);
                }else
                {
                    $data['ans_count'] = 0;
                }
                return view('module/preview/preview_workout_quiz_two',$data);

            }elseif ($data['question_info_s'][0]['questionType'] == 16)
            {
                $data['question_item']=$data['question_info_s'][0]['questionType'];
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
                    $inv=0;
                    foreach ($question_step as $key => $value) {
                        $qus_setup_array[$key]['question_step'] = $value[0];
                        $qus_setup_array[$key]['clue'] = $value[1];
                        $qus_setup_array[$key]['ecplanation'] = $value[2];
                        $qus_setup_array[$key]['answer_status'] = $value[3];
                        if($value[3] == 0){
                            $qus_setup_array[$key]['order'] = $k;
                            $k = $k + 1;
                        }else{
                            $qus_setup_array[$key]['order'] = $inv;
                            $inv--;
                        }
                    }
                    $data['qus_setup_array'] = $qus_setup_array;


                    $this->session->set_userdata('question_setup_answer_order', 1);
                }

                if (isset($data['qus_setup_array'])) {
                   
                    $question_step_details = $data['qus_setup_array'];

                    shuffle($question_step_details);
                    $data['question_step_details'] = $question_step_details;
                }

                //                echo '<pre>';
                //                print_r($data['question_info_ind']);
                //                die();
                return view('module/preview/preview_memorization_quiz',$data);

            }elseif ($data['question_info_s'][0]['questionType'] == 17) {
                $data['question_item'] = $data['question_info_s'][0]['questionType'];
                $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
                // echo '<pre>';
                // print_r($data['question_info_s'][0]);
                // die();
                
                $data['question_info_ind'] = $data['question_info'];
                if (isset($data['question_info_ind']->percentage_array)) {
                    $data['ans_count'] = count((array)$data['question_info_ind']->percentage_array);
                } else {
                    $data['ans_count'] = 0;
                }
                $question_id = $data['question_info_s'][0]['id'];
                $data['idea_info'] = $ModuleClass->getIdeaInfo('idea_info', $question_id);
                $data['idea_description'] = $ModuleClass->getIdeaDescription('idea_description', $question_id);
                return view('module/preview/preview_creative_quiz',$data);
            }elseif ($data['question_info_s'][0]['questionType'] == 18) {
                //echo 'asce re';die();
                $_SESSION['q_order_2'] = $uri->getSegment('4');
                $data['sentence_matching'] = $quesInfo;
                //echo "<pre>";print_r($data['question_info_s'][0]['question_id']);die();
                $data['sentence_questions'] = json_decode($data['question_info_s'][0]['questionName']);
                $data['sentence_answers'] = json_decode($data['question_info_s'][0]['answer']);
                return view('module/preview/preview_sentence_matching',$data);

            }elseif ($data['question_info_s'][0]['questionType'] == 19) {
                //print_r($data['question_info_s'][0]);die();
                $_SESSION['q_order_2'] = $uri->getSegment('3');
                $data['word_memorization'] = $quesInfo;
                $data['word_questions'] = json_decode($data['question_info_s'][0]['questionName'], true);
                $data['word_answers'] = json_decode($data['question_info_s'][0]['answer'], true);
                
                return view('module/preview/preview_word_memorization',$data);
            }
			  elseif ($data['question_info_s'][0]['questionType'] == 20) {
                //print_r($data['question_info_s'][0]);die();
                $_SESSION['q_order_2'] = $uri->getSegment('4');
                $data['comprehension_info'] = $quesInfo;
                $data['com_questions'] = json_decode($data['question_info_s'][0]['questionName'], true);
                $data['com_answers'] = json_decode($data['question_info_s'][0]['answer'], true);
                
                return view('module/preview/preview_comprehension', $data);
            }elseif ($data['question_info_s'][0]['questionType'] == 21) {
                //print_r($data['question_info_s'][0]);die();
                $_SESSION['q_order_2'] = $uri->getSegment('4');
                $data['grammer_info'] = $quesInfo;
                $data['grammer_questions'] = json_decode($data['question_info_s'][0]['questionName'], true);
                $data['grammer_answers'] = json_decode($data['question_info_s'][0]['answer'], true);
                
                return view('module/preview/preview_grammer',$data);
            }elseif ($data['question_info_s'][0]['questionType'] == 22) {
                //print_r($data['question_info_s'][0]);die();
                $_SESSION['q_order_2'] = $uri->getSegment('4');
                $data['glossary_info'] = $quesInfo;
                $data['glossary_questions'] = json_decode($data['question_info_s'][0]['questionName'], true);
                $data['glossary_answers'] = json_decode($data['question_info_s'][0]['answer'], true);
                
                return view('module/preview/preview_glossary',$data);
            }
            
        } else {
           
            $data['maincontent']     = $this->load->view('module/preview/moduleWithoutQues', $data, true);
        } // no question to preview
        

    }//end module_preview()

    public function editModule($moduleId)
    {
        $FaqClass=new \FaqClass();
        $TutorClass=new \TutorClass();
        $ModuleClass=new \ModuleClass();

        $_SESSION['prevUrl'] = $_SERVER['HTTP_REFERER'];
        $data['video_help'] = $FaqClass->videoSerialize(26, 'video_helps'); //rakesh
        $data['video_help_serial'] = 26;

        $_SESSION["moduleId"] = $moduleId; 
        $uType = $this->loggedUserType;
        if ($uType==1 || $uType==2 || $uType==6) {
            //user type parent, upper student,student shouldn't add module
            $this->session->set('error_msg', "You've no access to view this page");
            redirect('/');
        }

        $this->session->remove('data');
        $this->session->remove('obtained_marks');
        $this->session->remove('total_marks');
        
        $user_id                = $this->session->get('user_id');
        $data['loggedUserType'] = $this->loggedUserType;
        $data['user_info']      = $TutorClass->userInfo($user_id);

        $module = $ModuleClass->moduleInfo($moduleId);
        if (!sizeof($module)) {
            $this->session->set('error_msg', 'Module not exists.');
            return redirect()->to(base_url('all-module'));
        } else {
            $chaps = $this->get_chapter_name($module['subject'], $module['chapter']);
            $data['all_chapters'] = $chaps;
            $module['chapter'] = $chaps;
            $this->session->set('modInfo', $module);
        }
        
        if ($module['studentGrade'] <= 12) {
            $data['get_course'] = $ModuleClass->getInfo('tbl_course', 'user_type', 1);
        } else {
            $data['get_course'] = $ModuleClass->getInfo('tbl_course', 'user_type', 2);
        }

        $moduleQuestion = $ModuleClass->moduleQuestion($moduleId);
        $quesOrdrMap    = [];
        foreach ($moduleQuestion as $temp) {
            $quesOrdrMap[$temp['question_id']] = $temp['question_order'];
        }

        $data['qoMap']      = $quesOrdrMap;
        $user_id            = $this->session->get('user_id');

        $data['module_info']       = $module;
        $data['all_country']       = $this->renderAllCountry($module['country']);
        $data['all_subjects']      = $this->renderAllSubject($module['subject']);
        //$data['all_chapters']      = $this->renderAllChapter($module['chapter']);
        //echo '<pre>';print_r($data['all_chapters']);die;
        $data['all_module_type']   = $this->renderAllModuleType($module['moduleType']);
        $data['all_question_type'] = $TutorClass->getAllInfo('tbl_questiontype');

        $optionalHour              = $module['optionalTime']>3600 ? sprintf('%02d', $module['optionalTime']/3600) : "00";
        $optionalMinute            = sprintf('%02d', ($module['optionalTime']/60) - ($optionalHour*60));
        $data['optionalTime']      = (string)$optionalHour.':'.$optionalMinute;

        $data['instruction_video'] = json_decode($data['module_info']['video_link']);
        $data['instruction_video'] = (is_array($data['instruction_video']) && count($data['instruction_video'])) ? $data['instruction_video'][0] : '';
        $data['ins'] = $data["module_info"]["instruction"];
        
        foreach ($data['all_question_type'] as $row) {
            $question_list[$row['id']] = $TutorClass->getUserQuestion('tbl_question', $row['id'], $user_id);
        }
        $data['all_question'] = $question_list;
        
        $indivStdIds          = $module['individualStudent'];
        
        if ($this->loggedUserType==7) { //q-stydy need this kinda filter
            $conditions = [
                'subject_name'   =>$module['subject'],
                'student_grade'  =>$module['studentGrade'],
                'country_id'     => $module['country'],
            ];
            $studentIds           = $TutorClass->allStudents($conditions);
        } else { //others don't . I dont know if I'm getting maaad :/
            $studentIds           = $TutorClass->allStudents(['sct_id' => $user_id]);
        }


        $data['allStudents']  = $this->renderStudentIds($studentIds, $indivStdIds);

        return view('module/edit_module', $data);

    }//end editModule()


    public function newEditModule($moduleId,$id="")
    {
        error_report_check();
        $FaqClass=new \FaqClass();
        $ModuleClass=new \ModuleClass();
        $TutorClass=new \TutorClass();

        $this->session->remove('module_edit_status');
        $this->session->remove('module_status_edit_id');
        $this->session->remove('module_status');
        $this->session->remove('param_module_id');
        //echo "hello"; die();
        $module_session_info = $this->session->get('edit_module_info_creadiential');

        if(empty($id) && $module_session_info['module_id']!=$moduleId){
           
            $module_info['module_name'] = null;
            $module_info['grade_id'] = null;
            $module_info['module_type'] = null;
            $module_info['course_id'] = null;
            $module_info['show_student'] = null;
            $module_info['serial'] = null;
            $module_info['module_id'] = $moduleId;
        
            $this->session->set('edit_module_info_creadiential', $module_info);
        }
        if($id==2){
            $this->session->remove('edit_module_info_creadiential');
        }
        $data['module_cre_info'] = $this->session->set('edit_module_info_creadiential');
        
        // echo "<pre>";print_r($data['module_cre_info']);die();


        $_SESSION['prevUrl'] = $_SERVER['HTTP_REFERER'];
        $data['video_help'] = $FaqClass->videoSerialize(26, 'video_helps'); //rakesh
        $data['video_help_serial'] = 26;

        $_SESSION["moduleId"] = $moduleId;
        $data["module_id"] = $moduleId;
        $uType = $this->loggedUserType;
        if ($uType == 1 || $uType == 2 || $uType == 6) {
            //user type parent, upper student,student shouldn't add module
            $this->session->set('error_msg', "You've no access to view this page");
            return redirect()->to(base_url('/'));
        }

        $this->session->remove('data');
        $this->session->remove('obtained_marks');
        $this->session->remove('total_marks');

        $user_id                = $this->session->get('user_id');
        $data['loggedUserType'] = $this->loggedUserType;
        $data['user_info']      = $TutorClass->userInfo($user_id);
        
        
        $data['questions'] = $ModuleClass->getEditModuleInfo($moduleId);
        $country_id = $this->session->get('selCountry');
        foreach($data['questions'] as $key=>$ques){
            $user_id = $this->loggedUserId;
            $find_lists = $ModuleClass->getQuestions($ques['question_type'],$user_id,$country_id);
               $i=1;
               $order = 0;
               foreach($find_lists as $question){
                    if($question['id']==$ques['question_id']){
                        $order = $i;
                      break;
                    }
               $i++;}

              $data['questions'][$key]['order'] = $order;
        }

        $module = $ModuleClass->newModuleInfo($moduleId);
        // echo '<pre>'; print_r($module); die();

        $course_id = $module['course_id'];
        $moduleType = $module['moduleType'];
        $subject = $module['subject'];

        if($moduleType ==1){
            $data['subjects'] = $ModuleClass->getSubjectBycourse($course_id);
            $data['chapters'] = $ModuleClass->getChapterBycourse($subject);
        }else{
            $data['subjects'] = null;
            $data['chapters'] = null;
        }
        //echo "<pre>";print_r($data['chapters']);die();
        $data['module_info'] = $module;
        $data['courses'] = $ModuleClass->getAllCourse($country_id);
        $data['module_types'] = $ModuleClass->getModuleType();


        $user_id = $this->session->get('user_id');
        // echo '<pre>'; print_r($data['courses']); die();
        
        $data['loggedUserType'] = $this->loggedUserType;



        $optionalHour              = $module['optionalTime'] > 3600 ? sprintf('%02d', $module['optionalTime'] / 3600) : "00";
        $optionalMinute            = sprintf('%02d', ($module['optionalTime'] / 60) - ($optionalHour * 60));
        $data['optionalTime']      = (string)$optionalHour . ':' . $optionalMinute;

        $data['instruction_video'] = json_decode($data['module_info']['video_link']);
        $data['instruction_video'] = (is_array($data['instruction_video']) && count($data['instruction_video'])) ? $data['instruction_video'][0] : '';
        $data['ins'] = $data["module_info"]["instruction"];

        $indivStdIds          = $module['individualStudent'];

        if ($this->loggedUserType == 7) { //q-stydy need this kinda filter
            $conditions = [
                'subject_name'   => $module['subject'],
                'student_grade'  => $module['studentGrade'],
                'country_id'     => $module['country'],
            ];
            $studentIds           = $TutorClass->allStudents($conditions);
        } else { //others don't . I dont know if I'm getting maaad :/
            $studentIds           = $TutorClass->allStudents(['sct_id' => $user_id]);
        }
        $data['allsubjects']  = $ModuleClass->getAllSubjects($user_id);
        $data['allchapters']  = $ModuleClass->getAllChapters($user_id);

        $data['allStudents']  = $this->renderStudentIds($studentIds, $indivStdIds);
       
      
        return view('module/new_edit_module',$data);
    }


    public function editNewSubject(){
        $data['subject_name'] = $this->request->getVar('subject_name');
        $subject_id = $this->request->getVar('subject_id');

        $update = $this->db->table('tbl_subject')->where('subject_id', $subject_id)->update($data);
        if($update){
          echo 1;
        }else{
          echo 2;
        }
    }

    public function deleteSubjectByModule(){
        $subject_id = $this->request->getVar('subject_id');
        $delete = $this->db->table('tbl_subject')->where('subject_id', $subject_id)->delete();
        if($delete){
           echo 1;
        }else{
            echo 2;
        }

    }

    public function editNewChapter(){
        $data['chapterName'] = $this->request->getVar('chapter_name');
        $chapter_id = $this->request->getVar('chapter_id');
        $update = $this->db->table('tbl_chapter')->where('id', $chapter_id)->update($data);
        if($update){
          echo 1;
        }else{
          echo 2;
        }
    }

    public function deleteChapterByModule(){
        $chapter_id = $this->request->getVar('chapter_id');

        $delete = $this->db->table('tbl_chapter')->where('id', $chapter_id)->delete();
        if($delete){
           echo 1;
        }else{
            echo 2;
        }

    }
    public function edit_module_info(){

        $module_info['module_name'] = $this->request->getVar('module_name');
        $module_info['grade_id'] = $this->request->getVar('grade_id');
        $module_info['module_type'] = $this->request->getVar('module_type');
        $module_info['course_id'] = $this->request->getVar('course_id');
        $module_info['show_student'] = $this->request->getVar('show_student');
        $module_info['serial'] = $this->request->getVar('serial');
        $module_info['module_id'] = $this->request->getVar('module_id');
        $module_info['trackerName'] = $this->request->getVar('trackerName');
        $module_info['individualName'] = $this->request->getVar('individualName');
        $module_info['enterDate'] = $this->request->getVar('enterDate');
        $module_info['isSms'] = $this->request->getVar('isSms');
        $module_info['isAllStudent'] = $this->request->getVar('isAllStudent');
        $module_info['video_link_1'] = $this->request->getVar('video_link_1');
        $module_info['instruct_1'] = $this->request->getVar('instruct_1');
        $module_info['videoName'] = $this->request->getVar('videoName');
        $module_info['timeStart'] = $this->request->getVar('timeStart');
        $module_info['timeEnd'] = $this->request->getVar('timeEnd');
        $module_info['optTime'] = $this->request->getVar('optTime');
        $module_info['subject_id'] = $this->request->getVar('subject_id');
        $module_info['chapter_id'] = $this->request->getVar('chapter_id');
        
        $this->session->set('edit_module_info_creadiential', $module_info);
    }

    public function get_chapter_name($subject = 0, $selected = 0)
    {
        $TutorClass=new \TutorClass();
        $subject_id = $subject ? $subject : $this->request->getVar('subject_id');

        $all_subject_chapter = $TutorClass->getInfo('tbl_chapter', 'subjectId', $subject_id);
        //echo '<pre>';print_r($all_subject_chapter);die;
        $html = '<option value="">Select Chapter</option>';
        foreach ($all_subject_chapter as $chapter) {
            $sel = $chapter['id'] == $selected ? 'selected' : '';
            $html .= '<option value="' . $chapter['id'] . '" '.$sel.'>' . $chapter['chapterName'] . '</option>';
        }
        
        if ($subject) {
            return $html; //within controller
        } else {
            echo $html; // ajax/form submit
        }
    }

    public function renderAllCountry($selectedId = -1)
    {
        $TutorClass=new \TutorClass();

        $option    = '';
        $option   .= '<option value="">--Country--</option>';
        
        $countries = $TutorClass->getAllInfo('tbl_country');
        if ($this->loggedUserType != 7) {
            $countries = $TutorClass->getInfo('tbl_country', 'id', $this->site_user_data['country_id']);
        }
        
        foreach ($countries as $country) {
            $sel     = ($country['id'] == $selectedId) ? 'selected' : '';
            $option .= '<option value="'.$country['id'].'" '.$sel.'>'.$country['countryName'].'</option>';
        }

        return $option;
    }//end renderAllCountry()

    public function renderAllSubject($selectedId = -1)
    {
        $TutorClass=new \TutorClass();
        $option   = '';
        $option  .= '<option value="">--Subject--</option>';
        $subjects = $TutorClass->getInfo('tbl_subject', 'created_by', $this->loggedUserId);
        foreach ($subjects as $subject) {
            $sel     = ($subject['subject_id'] == $selectedId) ? 'selected' : '';
            $option .= '<option value="'.$subject['subject_id'].'" '.$sel.'>'.$subject['subject_name'].'</option>';
        }

        return $option;
    }//end renderAllSubject()

  
    public function tutorial_check_order_module_next()
    {
        $TutorClass=new \TutorClass();
        $module_id = $this->request->getVar('module_id');
        $question_order = $this->request->getVar('question_order');
        $question_id = $this->request->getVar('question_id');
        if ($question_order != '')
        {
            $question_order = $question_order+1;
            $question_info_ai = $TutorClass->getModuleQuestion($module_id, $question_order, null);
            if (!empty($question_info_ai))
            {
                $url = base_url('/').'/module_preview/'.$module_id.'/'.$question_order;
                echo json_encode($url);
            }
        }
    }

    public function tutorial_check_order_module_prev()
    {
        $TutorClass=new \TutorClass();
        $module_id = $this->request->getVar('module_id');
        $question_order = $this->request->getVar('question_order');
        $question_id = $this->request->getVar('question_id');
        if ($question_order != '')
        {
            $question_order = $question_order-1;
            $question_info_ai = $TutorClass->getModuleQuestion($module_id, $question_order, null);
            if (!empty($question_info_ai))
            {
                $url = base_url('/').'/module_preview/'.$module_id.'/'.$question_order;
                echo json_encode($url);
            }
        }
    }
   
    public function moduleQuestionDelete($questionId = 0)
    {
        $QuestionClass=new \QuestionClass();
        $delItems = $QuestionClass->delete('tbl_pre_module_temp', 'id', $questionId);
        if ($delItems) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function moduleDuplicateQuestion()
    {
        $ModuleClass=new \ModuleClass();
        $tblpre = $ModuleClass->getMaxTblPreModuleTempCourse();

        $questionId  = $this->request->getVar('questionId');
        $questionType  = $this->request->getVar('qType');

        $created_question = $ModuleClass->duplicateQuestionCreate($questionId,$questionType);

        $data = [
            'question_id' => $created_question['id'],
            'question_type' => $created_question['questionType'],
            'question_no' => 1,
            'question_order' => $tblpre['max_size'] + 1,
        ];
        //echo "<pre>";print_r($data);die();
        $duplicate = $ModuleClass->moduleQuestionDuplicate('tbl_pre_module_temp', $data);
        
        if ($duplicate) {
            echo 'true';
        } else {
            echo 'false';
        }
    }


    public function updateNewModuleQuestion()
    {
        // echo 11; die();
        // echo "<pre>"; print_r($_GET['country']); die();
        
        $AdminClass=new \AdminClass();
        $uType = $this->loggedUserType;
        if ($uType == 1 || $uType == 2 || $uType == 6) {
            //user type parent, upper student,student shouldn't add module
            $this->session->set('error_msg', "You've no access to view this page");
            return redirect()->to(base_url('/'));
        }

        $post = $this->request->getVar();
        // echo "<pre>"; print_r($post); die();

        if ($post['moduleType'] == 1 || $post['moduleType'] == 2 || $post['moduleType'] == 5) {
            $post['dateCreated'] = date('Y-m-d');
        }
        $date = $post['dateCreated'];

        $startTime = date('Y-m-d', strtotime($date)) . ' ' . $post['startTime'];
        $endTime = date('Y-m-d', strtotime($date)) . ' ' . $post['endTime'];

        $video_link = str_replace('</p>', '', $_POST['video_link']);
        $video_array = array_filter(explode('<p>', $video_link));

        $new_array = array();
        foreach ($video_array as $row) {
            $new_array[] = strip_tags($row);
        }
        // print_r(json_encode($video_array));die;
        //$video_link[] = $this->input->post('video_link');

        //$clean             = $this->security->xss_clean($post);
        $optionalTime      = explode(':', isset($post['optTime']) ? $post['optTime'] : "0:0");
        $optionalHour      = isset($optionalTime[0]) ? (int)$optionalTime[0] * 60 * 60 : 0; //second
        $optionalMinute    = isset($optionalTime[1]) ? (int)$optionalTime[1] * 60    : 0; //second

        //get users latest module order
        $mods = $AdminClass->search('tbl_module', ['user_id' => $this->loggedUserId]);
        if (count($mods)) {
            $allOrders = array_column($mods, 'ordering');
            $maxOrder = max($allOrders);
            $nextOrder = $maxOrder + 1;
        } else {
            $nextOrder = 0;
        }

        $builder = $this->db->table('tbl_module');
        $builder->select('*');
        $builder->where('moduleType', $post['moduleType']);
        $builder->where('studentGrade', $post['studentGrade']);
        $builder->where('course_id', $post['course_id']);
        $builder->where('user_id', $this->loggedUserId);
        $builder->where('serial', $_POST['serial']);
        $query_new = $builder->get();
        $chk_exits = $query_new->getResultArray();


        $moduleTableData   = [];
        $moduleTableData = [
            'moduleName'        => $post['moduleName'],
            'ordering'          => $nextOrder,
            'trackerName'       => $post['trackerName'],
            'instruction'       => $post['instruction'],
            'individualName'    => isset($post['individualName']) ? $post['individualName'] : '',
            'isSMS'             => isset($post['isSMS']) ? $post['isSMS'] : 0,
            'isAllStudent'      => isset($post['isAllStudent']) ? $post['isAllStudent'] : 0,
            'individualStudent' => isset($post['individualStudent']) ? json_encode($post['individualStudent']) : '',
            'course_id'         => isset($post['course_id']) ? $post['course_id'] : '',
            'video_link'        => json_encode($new_array),
            'video_name'        => isset($_POST['video_name']) ? $_POST['video_name'] : '',
            'subject'           => $post['subject'],
            'chapter'           => $post['chapter'],
            'country'           => $this->session->get('selCountry'),
            'studentGrade'      => $post['studentGrade'],
            'moduleType'        => $post['moduleType'],
            'user_id'           => $this->loggedUserId,
            'user_type'         => $this->loggedUserType,
            'exam_date'         => isset($post['dateCreated']) ? strtotime($post['dateCreated']) : 0,
            'exam_start'        => isset($post['startTime']) ? ($startTime) : 0,
            'exam_end'          => isset($post['endTime']) ? ($endTime) : 0,
            'optionalTime'      => $optionalHour + $optionalMinute,
            'show_student'      => isset($_POST['show_student']) ? $_POST['show_student'] : 0,
            'serial'      => isset($_POST['serial']) ? $_POST['serial'] : 0,
        ];

        $ModuleClass=new \ModuleClass();
        $ModuleClass->questionSorting('tbl_module', 'id', $post['id'], $moduleTableData);


        $builder = $this->db->table('tbl_module');
        $builder->select('MAX(serial) as max_serial');
        $builder->where('moduleType', $post['moduleType']);
        $builder->where('studentGrade', $post['studentGrade']);
        $builder->where('course_id', $post['course_id']);
        $builder->where('user_id', $this->loggedUserId);
        $query_new = $builder->get();
        $result_max = $query_new->getResultArray();

        /*=================new code==============*/
        $builder = $this->db->table('tbl_module');
        $builder->select('*');
        $builder->where('id', $post['id']);
        $query_new = $builder->get();
        $get_module = $query_new->getResultArray();

        $builder = $this->db->table('tbl_module');
        $builder->select('*');
        $builder->where('id !=', $post['id']);
        $builder->where('serial', $_POST['serial']);
        $builder->where('moduleType', $post['moduleType']);
        $builder->where('studentGrade', $post['studentGrade']);
        $builder->where('course_id', $post['course_id']);
        $builder->where('country', $this->session->get('selCountry'));
        $builder->where('user_id', $this->loggedUserId);
        $query_new = $builder->get();
        $get_serial = $query_new->getResultArray();
        // echo "<pre>";print_r($get_serial);die();
        // echo $_POST['serial'].'///'.$get_module[0]['serial'];die();

        if($_POST['serial']!=$get_module[0]['serial'] || !empty($get_serial)){

            if(!empty($result_max)){
                $new_sl = $result_max[0]['max_serial']+1;
            }else{
                $new_sl =1;
            }

            if(!empty($chk_exits)){
                $n_module_id=  $chk_exits[0]['id'];
                $n_data['serial']=  $new_sl;

                $builder = $this->db->table('tbl_module');
                $builder->where('id', $n_module_id);
                $builder->update($n_data);
            }

        }
        /*======new end======*/
        


        $arr   = [];
        $items = $ModuleClass->getEditPreModuleTemp($post['id']);
        // echo "<pre>"; print_r($items); die();

        if (count($items)) {
            foreach ($items as $item) {
                $arr[] = [
                    'question_id'    => $item['question_id'],
                    'question_type'  => $item['question_type'],
                    'module_id'      => $post['id'],
                    'question_order' => $item['question_order'],
                    'created'        => time(),
                ];
            }
            $this->session->remove('edit_module_info_creadiential');

            $this->db->table('tbl_modulequestion')->where('module_id', $post['id'])->delete();

            $ModuleClass->insert('tbl_modulequestion', $arr);
            $this->session->set('module_msg', 'Save Successfully');

            
        }

        $this->db->table('tbl_edit_module_temp')->truncate();
        return redirect()->to(base_url().'/all-module');
    } //end
    
    public function moduleQuestionSorting()
    {
        $ModuleClass=new \ModuleClass();
        //print_r($this->request->getVar());die();
        $order  = $this->request->getVar('order');
        $id  = $this->request->getVar('tblId');

        $data = [
            'question_order' => $order,
        ];

        $duplicate = $ModuleClass->questionSorting('tbl_pre_module_temp', 'id', $id, $data);

        // echo $this->db->GetLastQuery(); die();

        if ($duplicate) {
            echo 'true';
        } else {
            echo 'false';
        } 
    }

    public function deleteEditModuleQuestion($questionId = 0, $module_id)
    {
        $QuestionClass=new \QuestionClass();

        $delItems = $QuestionClass->delete('tbl_edit_module_temp', 'id', $questionId);
       
            $builder=$this->db->table('tbl_edit_module_temp');
            $builder->select('*');
            $query_new = $builder->get();
            $results = $query_new->getResultArray();
            
            if(empty($results)){
                $this->db->table('tbl_modulequestion')->where('module_id', $module_id)->delete();
            }

        if ($delItems) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function duplicateModuleQuestion()
    {
        $ModuleClass=new \ModuleClass();

        $moduleId  = $this->request->getVar('moduleId');
        $tblpre = $ModuleClass->getMaxTblNewModuleQuestionEdit($moduleId);
        // echo "<pre>"; print_r($tblpre['max_size']); die();

        $questionId  = $this->request->getVar('questionId');
        $questionType  = $this->request->getVar('qType');
        $copy_id = $this->request->getVar('qId');
        $question_id = $this->request->getVar('main_questionId');
        //echo "<pre>"; print_r($_POST); die();
        $question  = $ModuleClass->getDuplicateQuestion($question_id);
        
        $data = [
            'question_id' => $question['id'],
            'question_type' => $question['questionType'],
            'module_id' => $moduleId,
            'country' => $question['country'],
            'question_order' => $tblpre['max_size'] + 1,
            'created_at'        => time(),
        ];

        $duplicate = $this->db->table('tbl_edit_module_temp')->insert($data);
        //echo $this->db->last_query();die();
        if ($duplicate) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function tutorList($moduleType)
    {
        $StudentClass=new \StudentClass();

        if (!strpos($_SERVER['HTTP_REFERER'],"all_tutors_by_type")) {
            $_SESSION['prevUrl'] = $_SERVER['HTTP_REFERER'];
        }else{
            $_SESSION['prevUrl'] = base_url('/').'student/organization';
        }
        $loggedStudentId  = $this->loggedUserId;
        $studentsTutor = $StudentClass->allTutor($loggedStudentId);
        
        //all tutor ids of a student
        $allTutorIds = array_column($studentsTutor, 'id');
        //all tutor ids of a student filtered down by module type
        // $data['allTutors'] = $this->Student_model->allTutorByModuleType($moduleType, $allTutorIds);
        $all_parents = $StudentClass->all_assigners_new($loggedStudentId);
                
        $data['module_type'] = $moduleType;
        $i = 0;
        $allTutor = array();
        foreach ($all_parents as $row) {
            $ckSchoolCorporateExits = $StudentClass->ckSchoolCorporateExits('tbl_useraccount', 'SCT_link' , $row['SCT_link'] );

            if (count($ckSchoolCorporateExits) == 0 ) {
                $allTutor[] = $row;
            }


            $get_child_info = $StudentClass->getInfo('tbl_useraccount', 'parent_id', $row['id']);
            if ($get_child_info) {
                $allTutor[$i]['child_info'] = $get_child_info;
            }
            $i++;
        } 


        foreach ($all_parents as $row) {
            $get_child_info = $StudentClass->getInfo('tbl_useraccount', 'parent_id', $row['id']);
            if ($get_child_info) {
                $all_parents[$i]['child_info'] = $get_child_info;
            }
            $i++;
        }


        $data['allTutors'] = $allTutor;
        
        $data['module_type'] = $moduleType;

        return view('module/tutor_list',$data);
      
    }

    public function assign_serial_to_module()
    {
        $serial = $this->request->getvar('serial');
        $module_id= $this->request->getvar('module_id');
        $course_id = $this->request->getvar('course_id');
        $modType= $this->request->getvar('modType');
        $grade_id= $this->request->getvar('grade_id');

        $this->session->remove('edit_module_info_creadiential');

        $loggedUserId = $this->session->get('user_id');

        $builder = $this->db->table('tbl_module');
        $builder->select('*');
        $builder->where('moduleType', $modType);
        $builder->where('studentGrade', $grade_id);
        $builder->where('course_id', $course_id);
        $builder->where('user_id', $loggedUserId);
        $builder->where('serial', $serial);
        $query_new = $builder->get();
        $results = $query_new->getResultArray();

        $data['serial'] = $serial;
        $builder = $this->db->table('tbl_module');
        $builder->where('id', $module_id);
        $builder->update($data);

        $builder = $this->db->table('tbl_module');
        $builder->select('MAX(serial) as max_serial');
        $builder->where('moduleType', $modType);
        $builder->where('studentGrade', $grade_id);
        $builder->where('course_id', $course_id);
        $builder->where('user_id', $loggedUserId);
        $query_new = $builder->get();
        $result_max = $query_new->getResultArray();

        // echo '<pre>';
        // print_r($result_max);
        // die();

        if(!empty($result_max)){
            $new_sl = $result_max[0]['max_serial']+1;
        }else{
            $new_sl =1;
        }

        if(!empty($results)){
            $n_module_id=  $results[0]['id'];
            $n_data['serial']=  $new_sl;

            $builder = $this->db->table('tbl_module');
            $builder->where('id', $n_module_id);
            $builder->update($n_data);
        }

        echo 1;
    }

    public function searchModule()
    {
        $AdminClass=new \AdminClass();
        $post = $this->request->getVar();
        //$clean = $this->security->xss_clean($post);
        $conditions = array_filter($post);
        $conditions['user_id'] = $this->loggedUserId;
        $country_id = $this->session->get('selCountry');
        if(!empty($post['country'])){
            $conditions['country'] = $post['country'];
        }else{
            $conditions['country'] = $country_id;
        }
        

        //$modules = $this->QuestionModel->search('tbl_module', $conditions);
        $modules = $AdminClass->getModule('tbl_module', $conditions);
        $modTypes = ['', 'Tutorial', 'Everyday Study', 'Special Exam', 'Assignment'];
        $rows = '';
        
        foreach ($modules as $module) {
            $rows .= '<tr id="'.$module['id'].'">';
            $rows .= '<td>'.date('d-M-Y', $module['exam_date']).'</td>';
            $rows .= '<td id="modName"><a href="edit-module/'.$module['id'].'">'.$module['moduleName'].'</a></td>';
            $rows .=   '<td>'.$module['countryName']. '</td>';
            $rows .=   '<td>'.$module['studentGrade']. '</td>';
            $rows .=   '<td>'. $modTypes[$module['moduleType']]. '</td>';
            $rows .=   '<td>'.$module['subject_name']. '</td>';
            $rows .=   '<td>'.$module['chapterName']. '</td>';
            if($this->session->get('selCountry')!=1){
            // $rows .=   '<td>'.$module['courseName']. '</td>';
            }
            $rows .= '<td><i class="fa fa-clipboard" id="modDuplicateIcon" data-toggle="modal" data-target="#moduleDuplicateModal" style="color:#4c8e0c;"></i></td>';

            $rows .= '<td><a href="edit-module/'.$module['id'].'"><i class="fa fa-pencil" style="color:#4c8e0c;"></i></a></td>';

            $rows.='<td><i data-toggle="modal" data-target="#moduleDelModal" class="fa fa-trash" id="dltModOpnIcon" style="color:red;"></i></td>';
            $rows .= '</tr>';
        }

        echo $rows ? $rows : 'No module found';
    }

    public function newModuleDuplicate()
    {
        //echo "<pre>"; print_r($_POST); die();
        $ModuleClass=new \ModuleClass();

        $withQuestion = $this->request->getVar('with_question');
        $course_id = $this->request->getVar('course_id');
        $moduleType = $this->request->getVar('moduleType');
        $studentGrade = $this->request->getVar('studentGrade');
        $subject = $this->request->getVar('subject');
        $chapter = $this->request->getVar('chapter');
        $user_id = $this->session->get('user_id');
        
      
        if($withQuestion == 1){
            
            $moduleId = $this->request->getVar('module_id');
            $items = $ModuleClass->getTblModuleInfo($moduleId);
            $Max_serial = $ModuleClass->getModuleMaxSerial($course_id,$moduleType,$studentGrade);
            if(!empty($Max_serial['max_serial'])){
              $serial = $Max_serial['max_serial']+1;
            }else{
              $serial = 1; 
            }
            
            $items['id'] = null;
            $items['moduleName'] = $this->request->getVar('moduleName');
            $items['course_id'] = $this->request->getVar('course_id');
            $items['moduleType'] = $this->request->getVar('moduleType');
            $items['country'] = $this->request->getVar('country');
            $items['studentGrade'] = $this->request->getVar('studentGrade');
            $items['user_id'] = $user_id;

            if($this->request->getVar('moduleType')==1){
                $items['subject'] = $subject;
                $items['chapter'] = $chapter;
            }else{
                $items['subject'] = 0;
                $items['chapter'] = 0;
            }

            $items['serial'] = $serial;

            
            $this->db->table('tbl_module')->insert($items);
            $new_module_id = $this->db->insertID();

            $moduleQuestions = $ModuleClass->getTblNewModuleQuestion($moduleId, $new_module_id);

            echo 1;

        }else{
    
            $moduleId = $this->request->getVar('module_id');
            $items = $ModuleClass->getTblModuleInfo($moduleId);
            $Max_serial = $ModuleClass->getModuleMaxSerial($course_id,$moduleType,$studentGrade);
            if(!empty($Max_serial['max_serial'])){
              $serial = $Max_serial['max_serial']+1;
            }else{
              $serial = 1; 
            }
            
            $items['id'] = null;
            $items['moduleName'] = $this->request->getVar('moduleName');
            $items['course_id'] = $this->request->getVar('course_id');
            $items['moduleType'] = $this->request->getVar('moduleType');
            $items['country'] = $this->request->getVar('country');
            $items['studentGrade'] = $this->request->getVar('studentGrade');
            $items['user_id'] = $user_id;

            if($this->request->getVar('moduleType')==1){
                $items['subject'] = $subject;
                $items['chapter'] = $chapter;
            }else{
                $items['subject'] = 0;
                $items['chapter'] = 0;
            }
            
            $items['serial'] = $serial;

            //echo "<pre>";print_r($items);die();
            $this->db->table('tbl_module')->insert($items);
            $insertId = $this->db->insertID();

            $moduleQuestions = $ModuleClass->getTblNewModuleQuestionWithout($moduleId);

            foreach($moduleQuestions as $key => $question){
                $moduleQuestions[$key]['id'] = null;
                $moduleQuestions[$key]['module_id'] = $insertId;
            }

            foreach($moduleQuestions as $value){
                $result = $this->db->table('tbl_modulequestion')->insert($value);
                echo $result;
            }

        }

    }

    public function assign_subject_by_course_student()
    {
        $StudentClass=new \StudentClass();

        $course_id = $_POST['course_id'];
        // echo '<pre>';
        // print_r($course_id);
        // die();
        $moduleType = $_POST['moduleType'];
        if (isset($course_id) && $course_id != '')
        { 
            $html = '';
            $assign_course = $StudentClass->getInfo('tbl_assign_subject', 'course_id',$course_id);
            
            if (!empty($assign_course))
            {
                $subjectId = json_decode($assign_course[0]['subject_id']);
                $subjects = array();
               $html .= '<span class="badge badge-pill badge-primary" courseId="'.$course_id.'" id="subjectNameQ" subjectId="all" style="width: 197px;;margin:5px 5px 5px 5px; cursor: pointer;">All</span>';
               
               $subjectId = $StudentClass->getAllSubjectByCourse($course_id,$moduleType);
               
                foreach($subjectId as $value)
                {
                    $sb =  $StudentClass->getInfo('tbl_subject', 'subject_id',$value);
                    
                    if (!empty($sb))
                    {
                       $html .= '<span class="badge badge-pill badge-primary" courseId="'.$course_id.'" id="subjectNameQ" subjectId="'.$sb[0]['subject_id'].'" style="width:197px;margin:5px 5px 5px 5px; cursor: pointer; text-transform: capitalize;">'.$sb[0]['subject_name'].'</span>';
                    }
                }
                
                echo $html;
            }

        }
    }

	  public function addNewSubject(){
        
        $data['subject_name'] = $this->request->getVar('subject_name');
        
        $data['created_by'] = $this->session->get('user_id');
        
        $this->db->table('tbl_subject')->insert($data);
        $insert_id = $this->db->insertID();

        $builder = $this->db->table('tbl_subject');
        $builder->select('*');
        $builder->where('subject_id', $insert_id);
        $query_new = $builder->get();
        $subjects = $query_new->getResultArray();

        echo json_encode($subjects[0]);
        
    }
	
	 public function addNewChapter(){
        $data['chapterName'] = $this->request->getVar('chapter_name');
        $data['created_by'] = $this->session->get('user_id');
        $data['subjectId'] = $this->request->getVar('subject_id');
        //echo "<pre>";print_r($data);die();
        $this->db->table('tbl_chapter')->insert($data);
        $insert_id = $this->db->insertID();

        $builder = $this->db->table('tbl_chapter');
        $builder->select('*');
        $builder->where('id', $insert_id);
        $query_new = $builder->get();
        $Chapter = $query_new->getResultArray();

        echo json_encode($Chapter[0]);
        
    }
    public function assign_subject()
    {
        error_report_check();
        $StudentClass=new \StudentClass();
        $TutorClass=new \TutorClass();
        $data = array();
        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $tbl_assign_subject   = $TutorClass->getAllInfo('tbl_assign_subject');
        $courseInfo = array();
        $assign_course_id = array();
        if (!empty($tbl_assign_subject))
        {
            $i =0;
            foreach ($tbl_assign_subject as $tbl_assign)
            {
                $courseInfo[$i]['id']=$tbl_assign['id'];
                $course_id = $StudentClass->getInfo('tbl_course', 'id',$tbl_assign['course_id']);
                $courseInfo[$i]['course_name'] = $course_id[0]['courseName'];
                $subjectId = json_decode($tbl_assign['subject_id']);
                $subject_name = '';
                foreach($subjectId as $value)
                {
                    $sb =  $StudentClass->getInfo('tbl_subject', 'subject_id',$value);
                    if (!empty($sb))
                    {
                        $subject_name .= $sb[0]['subject_name'];
                        $subject_name .= '<br>';
                    }
                }
                $courseInfo[$i]['subject_name'] = $subject_name;
                $i++;

                $assign_course_id[] = $tbl_assign['course_id'];
            }
        }
        $data['courseInfo'] = $courseInfo;
        $all_courses       = $TutorClass->getAllInfo('tbl_course');

        $not_assign_course = array();
        $assign_course = array();
        foreach($all_courses as $all_course )
        {
            if(in_array($all_course['id'],$assign_course_id)){

            }else{
                $assign_course['id'] = $all_course['id'];
                $assign_course['courseName'] = $all_course['courseName'];
                $not_assign_course[]=$assign_course;
            }
        }
        $data['all_course'] =$not_assign_course;
        $data['all_subjects'] = $TutorClass->getInfo('tbl_subject', 'created_by', $this->loggedUserId);
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return view('module/assign-subject',$data);
    }

    public function save_assign_subject()
    {
        $ModuleClass=new \ModuleClass();
        $data = array();
        $response =[
            'error' =>false,
            'success' =>false,
            'message' =>''
        ];
        if (isset($_POST['course']) && !empty($_POST['course']))
        {
            $data['course_id'] = $_POST['course'];

        }else
        {
            $response =[
                'error' =>true,
                'success' =>false,
                'message' =>'Select Course'
            ];
            echo json_encode($response);
            die();
        }
        if (isset($_POST['subject_id']) && !empty($_POST['subject_id']))
        {
            $data['subject_id'] = json_encode($_POST['subject_id']);

        }else
        {
            $response =[
                'error' =>true,
                'success' =>false,
                'message' =>'The subject can not be empty.You must select at least one subject for a course.'
            ];
            echo json_encode($response);
            die();
        }
        $moduleId = $ModuleClass->insertId('tbl_assign_subject', $data);
        $response =[
            'error' =>false,
            'success' =>true,
            'message' =>'Successfully Inserted.'
        ];
        echo json_encode($response);
    }
    public function edit_assign_subject()
    {
        $StudentClass=new \StudentClass();
        $TutorClass=new \TutorClass();
        $html = '';
        $id = $this->request->getVar('id');
        $edit_subject_data = $StudentClass->getInfo('tbl_assign_subject', 'id',$id);
        $course_id = $edit_subject_data[0]['course_id'];
        $course = $StudentClass->getInfo('tbl_course', 'id',$course_id);
        $courseName = $course[0]['courseName'];
        $edit_data = json_decode($edit_subject_data[0]['subject_id']);
        $all_subjects = $TutorClass->getInfo('tbl_subject', 'created_by', $this->loggedUserId);

                $html = '<div class="col-md-6">
                                <div class="form-group">
                                        <label for="exampleInputEmail2" style="color:#007ac9;font-weight: bold;margin: 5px 0px;">Course</label>
                                    <div>
                                        '.$courseName.'
                                        <input type="hidden" name="assign_id" value="'.$id.'">
                             </div>
                                </div>
                  </div>';

        $html .= '
            <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleInputEmail2" style="color:#007ac9;font-weight: bold;margin: 5px 0px;">Subject</label>
                    <div class="select">';
                   foreach ($all_subjects as $all_subject) {
                       if ($all_subject['subject_name'] != '') {
                           if (in_array($all_subject['subject_id'],$edit_data)) {

                               $html .= '<p><input class="form-check-input" name="subject_id[]" type="checkbox" checked  value="' . $all_subject['subject_id'] . '">
                        <label class="form-check-label">' . $all_subject['subject_name'] . '</label></p>';
                           }else
                           {
                               $html .= '<p><input class="form-check-input" name="subject_id[]" type="checkbox" value="' . $all_subject['subject_id'] . '">
                        <label class="form-check-label">' . $all_subject['subject_name'] . '</label></p>';;
                           }
                       }
                   }

         $html .=           '</div>
                </div>
            </div>
        ';
        echo $html;
    }

    public function update_assign_subject()
    {
        $ModuleClass=new \ModuleClass();
        $data = array();
        $id = $this->request->getVar('assign_id');

        if (isset($_POST['subject_id']) && !empty($_POST['subject_id']))
        {
            $subject_id = $this->request->getVar('subject_id');

        }else
        {
            $response =[
                'error' =>true,
                'success' =>false,
                'message' =>'The subject can not be empty.You must select at least one subject for a course.'
            ];
            echo json_encode($response);
            die();
        }
        $data['subject_id'] = json_encode($subject_id);
        $ModuleClass->updateInfo('tbl_assign_subject','id',$id, $data);
        $response =[
            'error' =>true,
            'success' =>false,
            'message' =>'Updated Successfully'
        ];
        echo json_encode($response);
    }

    public function delete_assign_subject()
    {
        $ModuleClass=new \ModuleClass();
        $id = $this->request->getVar('id');
        $ModuleClass->deleteInfo('tbl_assign_subject','id',$id);
        echo json_encode('Delete Successfully');
    }

    public function updateSerialModuleQuestion(){

        // echo 'hello rafi'; die();

        // $info = $this->request->getVar();
        // echo "<pre>"; print_r($info); die();

        $serial = $this->request->getVar('serial');
        $ids = $this->request->getVar('ids');
        $question_ids = $this->request->getVar('question_ids');

        $i=1;
        foreach($question_ids as $question){
            $data['question_order'] = $i;

            $builder = $this->db->table('tbl_edit_module_temp');
            $builder->where('question_id', $question);
            $builder->update($data);

            // echo $this->db->last_query();die();
        $i++;
        }
        echo 1;
    }

    public function searchModuleByOptions(){
        $studentGrade = $this->request->getVar('studentGrade');
        $module_id = $this->request->getVar('module_id');
        $course_id = $this->request->getVar('course_id');
        $module_name = $this->request->getVar('module_name');

        $user_id = $this->session->get('user_id');
        $country_id = $this->session->get('selCountry');

        $builder = $this->db->table('tbl_module');
        $builder->select('*,tbl_module.id as id,tbl_subject.subject_name as subject_name');
        $builder->join('tbl_course', 'tbl_module.course_id=tbl_course.id', 'left');
        $builder->join('tbl_subject', 'tbl_subject.subject_id=tbl_module.subject', 'left');
        $builder->join('tbl_chapter', 'tbl_chapter.id=tbl_module.chapter', 'left');
        $builder->where('tbl_module.country',$country_id);
        $builder->where('tbl_module.user_id',$user_id);

        if(!empty($studentGrade)){
            $builder->where('studentGrade', $studentGrade);
        }
        if(!empty($module_id)){
            $builder->where('moduleType', $module_id);
        }
        if(!empty($course_id)){
            $builder->where('course_id', $course_id);
        }
        if(!empty($module_name)){
            $builder->like('moduleName', $module_name,'both');
        }
        $builder->orderBy('moduleType','asc');
        $builder->orderBy('studentGrade','asc');
        $builder->orderBy('course_id','asc');
        $builder->orderBy('serial','asc');
        $query_new = $builder->get();
        $modules = $query_new->getResultArray();

        echo json_encode($modules);
    }
	

}
