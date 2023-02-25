<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PreviewClass;

class PreviewController extends BaseController
{
    
    public function __construct()
    {
        $this->session=session();
        $user_type = $this->session->get('userType');
        $user_id = $this->session->get('user_id');
        $PreviewClass=new \PreviewClass();
        $user_info = $PreviewClass->userInfo($user_id);
        $this->site_user_data = array(
            'userType' => $user_type,
            'zone_name' => $user_info[0]['zone_name'],
            'student_grade' => $user_info[0]['student_grade'],
        );
    }
    public function question_preview($question_item, $question_id)
    {
        error_report_check();
        date_default_timezone_set($this->site_user_data['zone_name']);
        $data['time_zone_new']=$this->site_user_data['zone_name'];
        $exact_time = time();
        $this->session->set('exact_time', $exact_time);
        // echo $question_item;die();
        if ($question_item == 1) {
            $data=$this->general($question_item, $question_id);
            return view('preview/general_preview',$data);
        } elseif ($question_item == 2) {
            $data=$this->true_false($question_item, $question_id);
            return view('preview/true_false_preview', $data);
        } elseif ($question_item == 3) {
            $data=$this->preview_vocubulary($question_item, $question_id);
            return view('preview/preview_vocubulary', $data);
        } elseif ($question_item==4) {
            $data=$this->preview_multiple_choice($question_item, $question_id);
            return view('preview/preview_multiple_choice', $data);
        } elseif ($question_item==5) {
            //$data=$this->preview_multiple_response($question_item, $question_id);
            //return view('preview/preview_multiple_response', $data); 
        } elseif ($question_item == 6) 
        {
            $PreviewClass=new \PreviewClass();    
            $TutorClass=new \TutorClass();

            $quesInfo     = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
            $data['question_info_s']   = $quesInfo;
            $questionType = $quesInfo[0]['questionType'];
            $quesInfo     = json_decode($quesInfo[0]['questionName']);
            $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
            $data['userType'] = $data['userType'][0]['user_slug'];
            // common view file
         

            $data['question_id'] = $question_id;
            $data['question_item'] = $question_item;

            if ($questionType == 8) {
                // Assignment
                $questionBody            = isset($quesInfo->question_body) ? $quesInfo->question_body : '';
                $data['questionBody']    = $questionBody;
                $items                   = $quesInfo->assignment_tasks;
                $data['totalItems']      = count($items);
                $data['assignment_list'] = $this->renderAssignmentTasks($items);
                $data['maincontent']     = $this->load->view('preview/assignment', $data, true);
                return view('preview/preview_assignment',$data);
            } elseif ($questionType == 6) {
                // skip quiz
                $data['numOfRows']    = isset($quesInfo->numOfRows) ? $quesInfo->numOfRows : 0;
                $data['numOfCols']    = isset($quesInfo->numOfCols) ? $quesInfo->numOfCols : 0;
                $data['questionBody'] = isset($quesInfo->question_body) ? $quesInfo->question_body : '';
                // print_r($data['questionBody']); die();
                $data['questionId']   = $question_id;
                
                $quesAnsItem          = $quesInfo->skp_quiz_box;

                $items = $this->indexQuesAns($quesAnsItem);

                $data['skp_box'] = $this->renderSkpQuizPrevTable($items, $data['numOfRows'], $data['numOfCols']);

                $user_id             = $this->session->get('user_id');
                $data['all_grade']   = $TutorClass->getAllInfo('tbl_studentgrade');
                $data['all_subject'] = $TutorClass->getInfo('tbl_subject', 'created_by', $user_id);
                return view('preview/preview_skip_quiz',$data);
            }//end if
        } elseif ($question_item == 7) {
            $this->preview_matching($question_item, $question_id);
        } elseif ($question_item == 8) {
            $this->preview_skip($question_item, $question_id);
        } elseif ($question_item == 9) {
            $this->preview_story_Write($question_item, $question_id);
        } elseif ($question_item == 10) {
            $data=$this->preview_times_table($question_item, $question_id);
            return view('preview/preview_times_table',$data);
        } elseif ($question_item == 11) {
            $data=$this->preview_algorithm($question_item, $question_id);
            return view('preview/preview_algorithm',$data);
        } elseif ($question_item == 12) {
            $this->preview_workout_quiz($question_item, $question_id);
        } elseif ($question_item == 13) {
            $this->preview_multiple_choice($question_item, $question_id);
        }elseif ($question_item == 14) {
            $data=$this->preview_tutor($question_item, $question_id);
            return view('preview/preview_tutor',$data);
        }elseif ($question_item == 15)
        {
            $data=$this->preview_workout_quiz_two($question_item, $question_id);
            return view('preview/preview_workout_quiz_two',$data);
        }elseif ($question_item == 16)
        {
            $data=$this->preview_memorization_quiz($question_item, $question_id);
            return view('preview/preview_memorization_quiz',$data);
        }elseif ($question_item == 17)
        {
            $data=$this->preview_creative_quiz($question_item, $question_id);
            return view('preview/preview_creative_quiz',$data);
        }elseif ($question_item == 18)
        {
            $data=$this->preview_sentence_match($question_item, $question_id);
            return view('preview/preview_sentence_match',$data);

        }elseif ($question_item == 19)
        {
            $data=$this->preview_word_matching($question_item, $question_id);
            return view('preview/preview_word_matching',$data);
        }
		elseif ($question_item == 20)
        {
            $data=$this->preview_comprehension($question_item,$question_id);
            return view('preview/preview_comprehension',$data);
        }
        elseif ($question_item == 21)
        {
            $data=$this->preview_grammer($question_item, $question_id);
            return view('preview/preview_grammer',$data);
        }
        elseif ($question_item == 22)
        {
            $data=$this->preview_glossary($question_item, $question_id);
            return view('preview/preview_glossary',$data);
        }
        elseif ($question_item == 23)
        {
            $data=$this->preview_imageQuiz($question_item, $question_id);
            return view('preview/preview_imageQuiz',$data);
        }
        
    }

    private function preview_imageQuiz($question_item, $question_id)
    {
        $PreviewClass=new \PreviewClass();
        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        $data['question_info_ind'] = $data['question_info'];

        $data['image_info'] = $PreviewClass->getQuestionDetails('tbl_question', $question_id);
        
        $question_info_ind = $data['question_info'];

        return $data;
    }

	private function preview_glossary($question_item, $question_id)
    {
        $PreviewClass=new \PreviewClass();
        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        $data['question_info_ind'] = $data['question_info'];

        $data['glossary_info'] = $PreviewClass->getQuestionDetails('tbl_question', $question_id);
        
        $question_info_ind = $data['question_info'];

       return $data;
    }

    private function preview_grammer($question_item, $question_id)
    {
        $PreviewClass=new \PreviewClass();

        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        $data['question_info_ind'] = $data['question_info'];

        $data['grammer_info'] = $PreviewClass->getQuestionDetails('tbl_question', $question_id);
        
        $question_info_ind = $data['question_info'];

        return $data;
    }

    private function preview_comprehension($question_item, $question_id)
    {
        $PreviewClass=new \PreviewClass();

        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        $data['question_info_ind'] = $data['question_info'];

        $data['comprehension_info'] = $PreviewClass->getQuestionDetails('tbl_question', $question_id);
        
        $question_info_ind = $data['question_info'];

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
       
        return $data;
    }
	
    private function general($question_item, $question_id)
    {   
        $PreviewClass=new \PreviewClass();
        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return $data;

    }

    private function true_false($question_item, $question_id)
    {
        $PreviewClass=new \PreviewClass();

        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        //$data['maincontent'] = $this->load->view('preview/true_false', $data, true);
        return $data;

    }

    private function preview_vocubulary($question_item, $question_id)
    {
        $PreviewClass=new \PreviewClass();

        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        
        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        
        return $data;
    }

    private function preview_multiple_choice($question_item, $question_id)
    {
        $PreviewClass=new \PreviewClass();

        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
        
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return $data;
    
    }

    public function renderAssignmentTasks(array $items)
    {
        $row = '';
        foreach ($items as $task) {
            $task = json_decode($task);
            $row .= '<tr id="'.($task->serial + 1).'">';
            $row .= '<td>'.($task->serial + 1).'</td>';
            $row .= '<td>'.$task->qMark.'</td>';
            //$row .= '<td>'.$task->obtnMark.'</td>';
            $row .= '<td><i class="fa fa-eye qDtlsOpenModIcon" data-toggle="modal" data-target="#quesDtlsModal"></i></td>';
            $row .= '<input type="hidden" id="hiddenTaskDesc" value="'.$task->description.'">';
            $row .= '</tr>';
        }

        return $row;
    }//end renderAssignmentTasks()

    public function indexQuesAns($items)
    {
        $arr = [];
        foreach ($items as $item) {
            $temp = json_decode($item);
            if ($temp == '')
            {

            }else{
                $cr = explode('_', $temp->cr);
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
        for ($i=1; $i<=$rows; $i++) {
            $row .='<div class="sk_out_box">';
            for ($j=1; $j<=$cols; $j++) {
                if ($items[$i][$j]['type']=='q') {
                    $row .= '<div class="sk_inner_box"><input type="button" data_q_type="0" data_num_colofrow="" value="'.$items[$i][$j]['val'].'" name="skip_counting[]" class="form-control input-box  rsskpinpt'.$i.'_'.$j.'" readonly style="min-width:50px; max-width:50px"></div>';
                } else {
                    $ansObj = array(
                        'cr'=>$i.'_'.$j,
                        'val'=> $items[$i][$j]['val'],
                        'type'=> 'a',
                        );
                    $ansObj = json_encode($ansObj);
                    $val = ($showAns==1)?' value="'.$items[$i][$j]['val'].'"' : '';
                    $row .= '<div class="sk_inner_box"><input autocomplete="off" type="text" '.$val.' data_q_type="0" data_num_colofrow="'.$i.'_'.$j.'" value="" name="skip_counting[]" class="form-control input-box ans_input  rsskpinpt'.$i.'_'.$j.'"  style="min-width:50px; max-width:50px">';
                    $row .= '<input type="hidden" value="" name="given_ans[]" id="given_ans">';
                    $row .='</div>';
                }
            }
            $row .= '</div>';
        }
        return $row;
    }

    public function preview_times_table($question_item, $question_id)
    {
        $PreviewClass=new \PreviewClass();
        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $data['question_info'] = json_decode($data['question_info_s'][0]['questionName'], true);
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return $data;
    }


    public function preview_algorithm($question_item, $question_id)
    {
        $PreviewClass=new \PreviewClass();

        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        //print_r($data['question_info_s']);die();
        $data['question_info'] = json_decode($data['question_info_s'][0]['questionName'], true);
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return $data;
    }

    private function preview_workout_quiz_two($question_item, $question_id)
    {
        $PreviewClass=new \PreviewClass();
        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
    
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        $data['question_info_ind'] = $data['question_info'];
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
   
        if (isset($data['question_info_ind']->percentage_array))
        {
            $data['ans_count'] = count((array)$data['question_info_ind']->percentage_array);
        }else
        {
            $data['ans_count'] = 0;
        }
        return $data;
    }

    
    private function preview_tutor($question_item, $question_id)
    {
        $PreviewClass=new \PreviewClass();
        $TutorClass=new \TutorClass();

        $array_one = array();
        $array_two = array();
        $array_three = array();

        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);

        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
      
        $tutorialId = $data['question_info_s'][0]['id'];
        $data['tutorialInfo'] = $TutorClass->getInfo('for_tutorial_tbl_question', 'tbl_ques_id', $tutorialId);
        
        return $data;
    }

    private function preview_memorization_quiz($question_item, $question_id)
    {
        $PreviewClass=new \PreviewClass();
        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
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
                if($value[3] == 0){
                    $qus_setup_array[$key]['order'] = $k;
                    $k = $k + 1;
                }else{
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
        // echo '<pre>';
        // print_r($data);
        // die();
        
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return $data;
    }

    public function preview_creative_quiz($question_item, $question_id)
    {
        $PreviewClass=new \PreviewClass();
        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        $data['question_info_ind'] = $data['question_info'];
        $data['idea_info'] = $PreviewClass->getIdeaInfo('idea_info', $question_id);
        $data['idea_description'] = $PreviewClass->getIdeaDescription('idea_description', $question_id);
        //$data['profile'] = $this->Student_model->get_profile_info($this->session->userdata('user_id'));

        $question_info_ind = $data['question_info'];
        
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return $data;
    }

    public function get_preview_idea_info(){
        $PreviewClass=new \PreviewClass();
        $idea = $this->request->getVar('idea');
        $get_idea = explode(",", $idea);
        $question_id = $get_idea[0];
        $idea_no = $get_idea[1];
 
        $get_idea = $PreviewClass->getPreviewIdeaInfo($question_id,$idea_no);
        
        echo json_encode($get_idea[0]); 
 
     }

     public function preview_sentence_match($question_item, $question_id){
        $PreviewClass=new \PreviewClass();
        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);

        $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        $data['question_info_ind'] = $data['question_info'];

        $data['sentence_info'] = $PreviewClass->getQuestionDetails('tbl_question', $question_id);
        
        $question_info_ind = $data['question_info'];

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return $data;
    }

    public function preview_word_matching($question_item, $question_id){
        $PreviewClass=new \PreviewClass();
        $data['user_info'] = $PreviewClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
        $data['userType']  = $PreviewClass->getInfo('tbl_usertype', 'id', $this->session->get('userType'));
        $data['userType'] = $data['userType'][0]['user_slug'];
        $data['question_info_s'] = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $data['question_info'] = json_decode($data['question_info_s'][0]['questionName']);
        $data['question_id'] = $question_id;
        $data['question_item'] = $question_item;
        $data['question_info_ind'] = $data['question_info'];

        $data['word_match_info'] = $PreviewClass->getQuestionDetails('tbl_question', $question_id);
        
        $question_info_ind = $data['question_info'];

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        
        return $data;
    }

    public function answer_matching()
    {
        error_report_check();
        $PreviewClass=new \PreviewClass();

        $question_id = $this->request->getVar('question_id');
        $module_id = $this->request->getVar('module_id');
        $question_order_id = $this->request->getVar('current_order');
        $text = $this->request->getVar('answer');
        
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
        
        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1);
    }

    
    public function take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, $answer_info = null,$next_step_patten_two = null)
    {
		
        $PreviewClass=new \PreviewClass();
        $TutorClass=new \TutorClass();
        //****** Get Temp table data for Tutorial Module Type ******
        $user_id = $this->session->get('user_id');
        
        $question_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);

        $question_info_type = '';
        $question_info_type = $question_info[0]['questionType'];
        $question_info_pattern = '';
        $question_pattern = '';
        $memorization_part = '';
        $memorization_obtaine_mark_check = 1;
        $question_pattern = json_decode($question_info[0]['questionName']);
        if (isset($question_pattern->pattern_type))
        {
            $question_info_pattern = $question_pattern->pattern_type;
        }
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
            
            if ($question_info_type == 16)
            {
                if ($question_info_pattern == 1)
                {
                    $memorization_answer = $this->request->getVar('memorization_answer');
                    $memorization_part = $this->request->getVar('memorization_one_part');
                    if ($memorization_part == 1)
                    {
                        if (isset($_SESSION['memorization_one_part']))
                        {

                        }else{
                            $this->session->set('memorization_one_part',1);
                            $ans_is_right = $memorization_answer;
                        }
                    }elseif($memorization_part == 2){
                        if (isset($_SESSION['memorization_two_part']))
                        {

                        }else{
                            $this->session->set('memorization_two_part',2);
                            $ans_is_right = $memorization_answer;
                        }
                    }elseif ($memorization_part == 3){
                        if (isset($_SESSION['memorization_three_part']))
                        {

                        }else{
                            $this->session->set('memorization_three_part',3);
                            $ans_is_right = $memorization_answer;
                            $memorization_obtaine_mark_check = 2;
                        }
                    }
                }
                
                if ($question_info_pattern == 3)
                {
                     if ($text == $text_1) {

                     }else{
                        $this->session->set('memorization_three_qus_part_answer','wrong');
                     }
                }
                
            }else
            {
                $this->session->remove('memorization_three_part');
                $this->session->remove('memorization_two_part');
                $this->session->remove('memorization_one_part');
            }
        }

        //echo $text."///".$text_1;die();
        if ($text == $text_1) {
            $ans_is_right = 'correct';
            if ($answer_info != null) {
                $student_ans = $answer_info;
                echo $answer_info;
            } else {
                   echo 2;

            }

        } else {
            $ans_is_right = 'wrong';
            if ($answer_info != null) {
                $student_ans = $answer_info;
                echo $answer_info;
            } else {
                   echo 3;
            }
            $question_marks = 0;
        }
        
        if ($question_info_type == 16) {

            if ($flag == 0 && $question_info_pattern == 3 && $next_step_patten_two == 0) {

                $ans_check = $this->session->get('memorization_three_qus_part_answer');
                if (isset($ans_check)) {
                    if($ans_check == 'wrong'){
                        $ans_is_right = 'wrong';
                        $question_marks = 0;
                    }
                }
                $question_info_ai = $TutorClass->getModuleQuestion($module_id, $question_order_id, null);

                $link1 = base_url();
                $total_marks = $total_marks + $question_marks;
                $obtained_marks = $obtained_marks + $question_marks;

                $link2 = $link1 .'get_tutor_tutorial_module/' . $module_id . '/' . $question_order_id;

                $ind_ans = array(
                    'question_order_id' => $question_info_ai[0]['question_order'],
                    'module_type' => $question_info_ai[0]['moduleType'],
                    'module_id' => $question_info_ai[0]['module_id'],
                    'question_id' => $question_info_ai[0]['question_id'],
                    'link' => $link2,
                    'ans_is_right' => $ans_is_right
                    );

                $ans_array[$question_order_id] = $ind_ans;

                $this->session->set('data', $ans_array);
                $this->session->set('obtained_marks', $obtained_marks);
                $this->session->set('total_marks', $total_marks);
                $this->session->remove('memorization_three_qus_part_answer');
            }



             if ($flag == 0 && $question_info_pattern != 3) {
                $question_info_ai = $TutorClass->getModuleQuestion($module_id, $question_order_id, null);

                $link1 = base_url();
                $total_marks = $total_marks + $question_marks;
                $obtained_marks = $obtained_marks + $question_marks;

                $link2 = $link1 . 'get_tutor_tutorial_module/' . $module_id . '/' . $question_order_id;

                $ind_ans = array(
                    'question_order_id' => $question_info_ai[0]['question_order'],
                    'module_type' => $question_info_ai[0]['moduleType'],
                    'module_id' => $question_info_ai[0]['module_id'],
                    'question_id' => $question_info_ai[0]['question_id'],
                    'link' => $link2,
                    'ans_is_right' => $ans_is_right
                    );

                $ans_array[$question_order_id] = $ind_ans;

                $this->session->set('data', $ans_array);
                $this->session->set('obtained_marks', $obtained_marks);
                $this->session->set('total_marks', $total_marks);
            }

        }

        if ($flag == 0 && $question_info_type != 16) {
            $question_info_ai = $TutorClass->getModuleQuestion($module_id, $question_order_id, null);

            $link1 = base_url();
            $total_marks = $total_marks + $question_marks;
            $obtained_marks = $obtained_marks + $question_marks;

            $link2 = $link1 . 'get_tutor_tutorial_module/' . $module_id . '/' . $question_order_id;

            $ind_ans = array(
                'question_order_id' => $question_info_ai[0]['question_order'],
                'module_type' => $question_info_ai[0]['moduleType'],
                'module_id' => $question_info_ai[0]['module_id'],
                'question_id' => $question_info_ai[0]['question_id'],
                'link' => $link2,
                'ans_is_right' => $ans_is_right
                );
            $ans_array[$question_order_id] = $ind_ans;
            

            $this->session->set('data', $ans_array);
            $this->session->set('obtained_marks', $obtained_marks);
            $this->session->set('total_marks', $total_marks);
            
        }       

    }

    public function show_tutorial_result($module)
    {
        //echo $module;die();
        $TutorClass=new \TutorClass();
        $user_id = $this->session->get('user_id');
        $data['module_info'] = $TutorClass->getInfo('tbl_module', 'id', $module);
//        $data['obtained_marks'] = $this->Student_model->get_student_progress($user_id, $module);
        $tutorial_ans_info = $this->session->get('data');
        $data['obtained_marks'] = $this->session->get('obtained_marks');
        // echo '<pre>';
        // print_r($data);die();
//        $tutorial_ans_info = array();
//        if ($data['module_info'][0]['moduleType'] == 1) {
//            $get_tutorial_ans_info = $this->Student_model->getTutorialAnsInfo('tbl_temp_tutorial_mod_ques', $module, $user_id);
//            $tutorial_ans_info = json_decode($get_tutorial_ans_info[0]['st_ans'], true);
//            $module_id = $tutorial_ans_info[1]['module_id'];
//            $data['obtained_marks'] = $this->session->userdata('obtained_marks');
//        } elseif ($data['module_info'][0]['moduleType'] == 2) {
//            $tutorial_ans_info = $this->Student_model->getTutorialAnsInfo('tbl_st_error_ans', $module, $user_id);
//            //            $tutorial_ans_info = json_decode($get_tutorial_ans_info[0]['st_ans'],TRUE);
//            $module_id = $tutorial_ans_info[0]['module_id'];
//        } else {
//            $get_tutorial_ans_info = $this->Student_model->getTutorialAnsInfo('tbl_student_answer', $module, $user_id);
//            $tutorial_ans_info = json_decode($get_tutorial_ans_info[0]['st_ans'], true);
//            $module_id = $tutorial_ans_info[1]['module_id'];
//        }
        
        // if($tutorial_ans_info) {
        $data['user_info'] = $TutorClass->getInfo('tbl_useraccount', 'id', $user_id);
        
        $data['tutorial_ans_info'] = $tutorial_ans_info;
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        

        return view('module/preview/show_module_result',$data);
 
        // } else {
            // redirect('error');
        // }
    }


    public function preview_answer_matching_multiple_choice()
    {
        $PreviewClass=new \PreviewClass();
        $question_id = $_POST['question_id'];
        
        if(isset($_POST['answer_reply'])){
            $text_1 = $_POST['answer_reply'];
        }else{
            $text_1 = 0;
        }

        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        // $text = $answer_info[0]['answer'];
        // Added AS
        $text = json_decode($answer_info[0]['answer']);
        $question_marks = $answer_info[0]['questionMarks'];
        
        if($text_1==0){
            $result_count = 0;
        }else{
            $result_count = count(array_intersect($text_1, $text));
        }
        
        
        $module_id = $_POST['module_id'];
        
        //$question_order_id = $_POST['next_question'] - 1;
        $question_order_id = $_POST['current_order'];
        
        // $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1);
        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, count($text), $result_count);
    }

    public function answer_multiple_matching()
    {
        $PreviewClass=new \PreviewClass();
        $total = $_POST['total_ans'];

        $question_id = $_POST['question_id'];
        $st_ans = array();
        
        for ($i = 1; $i <= $total; $i++) {
            $ans_id = 'answer_' . $i;
            $st_ans[] = $_POST[$ans_id];
        }
        
        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);
        $question_marks = $answer[0]['questionMarks'];
        $answer_info['student_ans'] = $st_ans;

        $module_id = $_POST['module_id'];
        //$question_order_id = $_POST['next_question'] - 1;
        $question_order_id = $_POST['current_order'];
        $text = 0;
        $text_1 = 0;
        $flag = 1;
        for ($k = 0; $k < sizeof($answer_info['student_ans']); $k++) {
            if ($answer_info['student_ans'][$k] != $answer_info['tutor_ans'][$k]) {
                $text++;
                $flag = 0;
            }
        }
        $answer_info['student_ans'] = $st_ans;
        $answer_info['flag'] = $flag;

        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, json_encode($answer_info));
    }

    
    public function preview_answer_matching_true_false()
    {
        $PreviewClass=new \PreviewClass();
        if (!$this->validate('answer_machingValidate')) {
            echo 1;
        } else {
            $text = $this->request->getVar('answer');
            $question_id = $this->request->getVar('question_id');

            $module_id = $_POST['module_id'];
            // $question_order_id = $_POST['next_question'] - 1;
            $question_order_id = $_POST['current_order'];
            $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
            $text_1 = $answer_info[0]['answer'];
            $question_marks = $answer_info[0]['questionMarks'];
            
            $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1);
        }
    }

    public function preview_answer_matching_vocabolary()
    {
        $PreviewClass=new \PreviewClass();
        if (!$this->validate('answer_machingValidate')) {
            echo 1;
        } else {
            $text = strtolower($this->request->getVar('answer'));
            $question_id = $this->request->getVar('question_id');
            $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);

            $module_id = $_POST['module_id'];
            // $question_order_id = $_POST['next_question'] - 1;
            $question_order_id = $_POST['current_order'];
            $text_1 = strtolower($answer_info[0]['answer']);

            $question_marks = $answer_info[0]['questionMarks'];

            $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1);
        }
    }

    public function preview_answer_times_table()
    {

        $PreviewClass=new \PreviewClass();
        $question_id = $this->request->getVar('question_id');
        $result = $this->request->getVar('result');
        $st_ans = array();
        
        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);
        $question_marks = $answer[0]['questionMarks'];
        $answer_info['student_ans'] = $result;
        
        $module_id = $_POST['module_id'];
        //$question_order_id = $_POST['next_question'] - 1;
        $question_order_id = $_POST['current_order'];
        $text = 0;
        $text_1 = 0;
        $flag = 1;
        
        for ($k = 0; $k < sizeof($answer_info['student_ans']); $k++) {
            if ($answer_info['student_ans'][$k] != $answer_info['tutor_ans'][$k]) {
                $text++;
                $flag = 0;
            }
        }
        
        $answer_info['student_ans'] = $result;
        $answer_info['flag'] = $flag;

//       $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, json_encode($answer_info));
        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, array());
    }
    
    public function preview_answer_algorithm()
    {
        //echo '<pre>';print_r($this->request->getVar());die;
        $PreviewClass=new \PreviewClass();
        $question_id = $this->request->getVar('question_id');
        $result = $this->request->getVar('answer');
        
        if(!empty($result)){
            $ans_one = $result[0];
            $reminder_answer = $result[1];
        }else{
            $ans_one = $result;
            $reminder_answer = $result;
        }
        

        $text = 1;
        $module_id = $this->request->getVar('module_id');
        
        $question_order_id = $this->request->getVar('current_order');
        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $question_info = json_decode($answer[0]['questionName'], true);

        // echo 'hiiii';die();
        $question_marks = $answer[0]['questionMarks'];

        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);
        $text_1 = 1;
        
        //  echo '<pre>';print_r($question_info['operator']);die;
        
        $question_marks = $answer[0]['questionMarks'];
        if ($question_info['operator'] != '/' && $result == $answer_info['tutor_ans']) {
            $text_1 = 1;
        } elseif ($question_info['operator'] == '/' && $question_info['quotient'] == $ans_one && $question_info['remainder'] == $reminder_answer) {
            $text_1 = 1;
        } else {
            $text_1++;
        }
        
        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, array());
    }

        
    public function preview_answer_matching_workout_two()
    {
        $PreviewClass=new \PreviewClass();
        $provide_ans ='';
        $qus_ans =0;
        $question_id = $this->request->getVar('question_id');
        $module_id = $this->request->getVar('module_id');
        //$question_order_id = $_POST['check_order_id'] - 1;
        $question_order_id = $this->request->getVar('current_order');
        //$text = $this->input->post('answer');
        $text = 0;
        $text_1 = 0;

        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);

        $ans_is_right = 'correct';
        $question_marks = 0;

        $provide_ans = $_POST['answer'];
        $qus_ans = $answer_info[0]['answer'];
        if ($provide_ans == 'correct')
        {
            $text_1 =0;
            $question_marks = $answer_info[0]['questionMarks'];
        }else{
            $ans_is_right = 'wrong';
            $text_1 =1;
        }
        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, array());

    }

    public function preview_memorization_pattern_four_ok()
    {
        $PreviewClass=new \PreviewClass();
        $qus_ans =0;
        $question_marks = 0;
        $question_id = $this->request->getVar('question_id');
        $module_id = $this->request->getVar('module_id');
        $question_order_id = $this->request->getVar('current_order');
        $submit_cycle = $this->request->getVar('submit_cycle');
        $memorization_answer = $this->request->getVar('memorization_answer');
        $text = 0;
        $text_1 = 0;

        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        if ($memorization_answer == 'correct')
        {
            if (isset($answer_info[0]['questionMarks']))
            {
                $question_marks = $answer_info[0]['questionMarks'];
            }
        }else{
            $text_1 = 1;
        }

        // if ( $text_1 == 0 ) {
        //     $dataArray = $_SESSION['data'];
        // echo "<pre>";print_r($dataArray[$_POST['current_order']]['ans_is_right']);die();
        //     if (count($dataArray)) {
        //         $dataArray[$_POST['current_order']]['ans_is_right']  = "wrong";
        //         $this->session->set_userdata('data', $dataArray);
        //     }
        // }

        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, array());
    }

    public function preview_memorization_pattern_four_matching()
    {
        $TutorClass=new \TutorClass();
        $show_data_array = array();
        $question_id = $this->request->getVar('question_id');
        $start_memorization_four_value = $this->request->getVar('start_memorization_four_value');
        $question_info = $TutorClass->getInfo('tbl_question','id',$question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        if ($start_memorization_four_value == 1)
        {
            $show_data_array['show_data_array'] = $this->memorization_ans_data_four($question_name);
            $show_data_array['all_correct'] = 1;
        }else
        {
            $show_data_array['show_data_array'] = $this->memorization_hide_data_four($question_name);
            $show_data_array['all_correct'] = 0;
        }
       echo json_encode($show_data_array);
    }

    public function module_preview_memorization_pattern_four_ans_matching()
    {
        $data = array();
        $question_id = $this->input->post('question_id');
        $word_matching = $this->input->post('word_matching');
        $submit_cycle = $this->input->post('submit_cycle');
        $pattern = $this->input->post('pattern');
        $question_info = $this->tutor_model->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        $show_data_array = array();
        $word_matching_answer = array();
        $all_correct_status = 1;
        $left_memorize_h_p_four = $question_name->left_memorize_h_p_four;
        $right_memorize_h_p_four = $question_name->right_memorize_h_p_four;

        $left_memorize_p_four = $question_name->left_memorize_p_four;
        $left_memorize_p_four = array_map('strtolower', $left_memorize_p_four);

        $right_memorize_p_four = $this->input->post('right_memorize_p_four');
        $right_memorize_p_four = array_map('strtolower', $right_memorize_p_four);
        $this->session->set_userdata('correct_answer', $right_memorize_p_four);

        if ($submit_cycle != 1)
        {
            foreach ($right_memorize_p_four as $key=>$item) {
                if ($right_memorize_h_p_four[$key] == 1)
                {
                    $show_data_array[] = $item;
                }else
                {
                    $show_data_array[] = '';
                }
            }
            foreach($show_data_array as $key=>$show_data)
            {
                if ($show_data != '')
                {
                    $word_matching_item = $word_matching[$key];
                    if ( preg_replace('/\s+/', '', strtolower($show_data))  == preg_replace('/\s+/', '', strtolower($word_matching_item)) )
                    {
                        $word_matching_answer[]=1;
                    }else
                    {
                        $word_matching_answer[]=0;
                        $all_correct_status = 0;
                    }
                }else
                {
                    $word_matching_answer[]=2;
                }
            }
            $data_array = array();
            foreach ($word_matching_answer as $key=>$value)
            {
                if ($value != 1)
                {
                    $data_array[] =$left_memorize_p_four[$key];
                }else
                {
                    $data_array[] = '';
                }
            }
            $data['word_matching_answer'] =$word_matching_answer;
            $data['data_array'] =$data_array;
            $data['all_correct_status'] =$all_correct_status;
            $data['status'] =  0;
        }else{
            $word_matching = $this->input->post('word_matching');
            $show_data_array = array();
            $left_memorize_h_p_four = $question_name->left_memorize_h_p_four;
            $left_memorize_p_four = $question_name->left_memorize_p_four;
            $left_memorize_p_four = array_map('strtolower', $left_memorize_p_four);
            $correct_status = 1;
            $leftSileData = array();
            $word_matching_answer = array();
            foreach ($left_memorize_p_four as $key=>$item) {
                if ( preg_replace('/\s+/', '', strtolower($left_memorize_p_four[$key]))  == preg_replace('/\s+/', '', strtolower($word_matching[$key])) )    
                {
                    $show_data_array[$key][0] = $item;
                    $show_data_array[$key][1] = 1;
                    $leftSileData[$key][0] = '';
                    $leftSileData[$key][1] = 1;
                    $word_matching_answer[] = 1;

                }else
                {
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
        }

        $data['correct_answer'] =  $this->session->userdata['correct_answer'];

        echo json_encode($data);
    }

    public function module_preview_memorization_pattern_one_ok()
    {

        $PreviewClass=new \PreviewClass();
        $qus_ans =0;
        $question_marks = 0;
        $question_id = $this->request->getVar('question_id');
        $module_id = $this->request->getVar('module_id');
        $question_order_id = $this->request->getVar('current_order');
        $submit_cycle = $this->request->getVar('submit_cycle');
        $memorization_answer = $this->request->getVar('memorization_answer');
        $text = 0;
        $text_1 = 0;

        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        if ($memorization_answer == 'correct')
        {
            if (isset($answer_info[0]['questionMarks']))
            {
                $question_marks = $answer_info[0]['questionMarks'];
            }
        }else{
            $dataArray = $_SESSION['data'];
            if (count($dataArray)) {
                $dataArray[$_POST['current_order']]['ans_is_right']  = "wrong";
                $this->session->set('data', $dataArray);
            }
        }
        if ( $text_1 == 0 ) {
            $dataArray = $_SESSION['data'];
            if (count($dataArray)) {
                $dataArray[$_POST['current_order']]['ans_is_right']  = "correct";
                $this->session->set('data', $dataArray);
            }
        }

        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, array());
    }
    

    public function preview_memorization_pattern_two_try()
    {
        $TutorClass=new \TutorClass();
        $data = array();
        $question_id = $this->request->getVar('question_id');
        $correctAnswer = $this->request->getVar('correctAnswer');
        $submit_cycle = $this->request->getVar('submit_cycle');
        $question_info = $TutorClass->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        $correctAnswer = explode(",",$correctAnswer);
        $left_memorize_p_one = $question_name->left_memorize_p_one;
        $show_correct_ans = array();
        $next = array();
        foreach ($correctAnswer as $key=>$item) {
            if ($item == 1)
            {
                $show_correct_ans[] = $left_memorize_p_one[$key];
            }else
            {
                $show_correct_ans[] = '';
            }
        }
        foreach ($correctAnswer as $key=>$value)
        {
            if ($value == 1)
            {
                $item = $left_memorize_p_one[$key];
                $split_array = str_split(trim($item), 1);
                $next[$key][0] = $split_array;
                $next[$key][1] = 1;
            }else
            {
                $item = $left_memorize_p_one[$key];
                $split_array = str_split(trim($item), 1);
                $countHints = count($split_array);
                $maxShow = $countHints - 3;
                for($hints = 0;$hints<$countHints;$hints++)
                {
                    if (isset($split_array[$hints]))
                    {
                        $cycle = $submit_cycle;
                        $cycle = $submit_cycle - 1;
                        if ( $hints <= $cycle && $hints <$maxShow)
                        {
                            $next[$key][0] = $split_array[$hints];
                        }else{
                            $next[$key][0] = '';
                        }
                    }
                }
                $next[$key][1] = 0;
            }
        }

        $data['next'] = $next;
        $data['show_correct_ans'] = $show_correct_ans;
        echo json_encode($data);
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
        $correctAnswer = explode(",",$correctAnswerStd);
        $show_data_array = $this->memorization_hide_data($question_name);
        $left_memorize_p_one = $question_name->left_memorize_p_one;
        $left_memorize_p_one = array_map('strtolower', $left_memorize_p_one);
        $show_correct_ans = array();
        $show_error_ans = array();

        foreach ($correctAnswer as $key=>$item) {
            if ($item == 1)
            {
                $show_correct_ans[] = $left_memorize_p_one[$key];
            }else
            {
                $show_correct_ans[] = '';
            }
        }
        $data['show_data_array']=$show_data_array;
        if ($all_check_hint == 1)
        {
            foreach ($correctAnswer as $key=>$item) {
                if ($item != 1) {
                    $show_error_ans[] = $left_memorize_p_one[$key];
                } else {
                    $show_error_ans[] = '';
                }
            }
            $data['show_data_array']=$show_error_ans;
            $data['all_check_hint']=1;
        }

        $data['show_correct_ans']=$show_correct_ans;

        echo json_encode($data);
    }
    

    public function module_preview_memorization_pattern_one_ans_matching()
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
        if ($submit_cycle != 1)
        {
            foreach ($left_memorize_p_one as $key=>$item) {
                if ($left_memorize_h_p_one[$key] == 1)
                {
                    $show_data_array[] = $item;
                }else
                {
                    $show_data_array[] = '';
                }
            }
            foreach($show_data_array as $key=>$show_data)
            {
                if ($show_data != '')
                {
                    $word_matching_item = $word_matching[$key];
                    if ( preg_replace('/\s+/', '', strtolower($show_data))  == preg_replace('/\s+/', '', strtolower($word_matching_item)) )
                    {
                        $word_matching_answer[]=1;
                    }else
                    {
                        $word_matching_answer[]=0;
                        $all_correct_status = 0;
                    }
                }else
                {
                    $word_matching_answer[]=2;
                }
            }
            $data_array = array();
            foreach ($word_matching_answer as $key=>$value)
            {
                if ($value != 1)
                {
                    $data_array[] =$left_memorize_p_one[$key];
                }else
                {
                    $data_array[] = '';
                }
            }
            $data['word_matching_answer'] =$word_matching_answer;
            $data['data_array'] =$data_array;
            $data['all_correct_status'] =$all_correct_status;
            $data['status'] =  0;
        }else{
            $word_matching = $this->request->getVar('word_matching');
            $show_data_array = array();
            $left_memorize_h_p_one = $question_name->left_memorize_h_p_one;
            $left_memorize_p_one = $question_name->left_memorize_p_one;
            $left_memorize_p_one = array_map('strtolower', $left_memorize_p_one);
            $correct_status = 1;
            $leftSileData = array();
            $word_matching_answer = array();
            foreach ($left_memorize_p_one as $key=>$item) {
                if ( preg_replace('/\s+/', '', strtolower($left_memorize_p_one[$key]))  == preg_replace('/\s+/', '', strtolower($word_matching[$key])) )    
                {
                    $show_data_array[$key][0] = $item;
                    $show_data_array[$key][1] = 1;
                    $leftSileData[$key][0] = '';
                    $leftSileData[$key][1] = 1;
                    $word_matching_answer[] = 1;

                }else
                {
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
        }

        echo json_encode($data);
    }

    public function preview_memorization_p_two_start_memorization()
    {
        $question_id = $this->request->getVar('question_id');
        $question_name = $this->getQuestionById($question_id);

        $left_memorize_p_two = $question_name->left_memorize_p_two;
        $left_memorize_h_p_two = $question_name->left_memorize_h_p_two;
        $right_memorize_p_two = $question_name->right_memorize_p_two;
        $right_memorize_h_p_two = $question_name->right_memorize_h_p_two;
        $left_content = array();
        $right_content = array();
        if (isset($question_name->hide_pattern_two_left))
        {
            $hide_pattern_two_left = $question_name->hide_pattern_two_left;
            $left_content = $this->contentModifyByHidden($left_memorize_p_two,$left_memorize_h_p_two);
        }else
        {
            $hide_pattern_two_left = 0;
            $left_content = $this->contentModify($left_memorize_p_two);
        }
        if (isset($question_name->hide_pattern_two_right))
        {
            $hide_pattern_two_right = $question_name->hide_pattern_two_right;
            $right_content = $this->contentModifyByHidden($right_memorize_p_two,$right_memorize_h_p_two);
        }else{
            $hide_pattern_two_right = 0;
            $right_content = $this->contentModify($right_memorize_p_two);
        }

        $data['right_content'] = $right_content;
        $data['left_content'] = $left_content;
        echo json_encode($data);
    }

    public function preview_memorization_p_two_ans_matching()
    {
        $data = array();
        $question_id = $this->request->getVar('question_id');
        $left_memorize_p_two_ans = $this->request->getVar('left_memorize_p_two');
        $right_memorize_p_two_ans = $this->request->getVar('right_memorize_p_two');
        $cycle = $this->request->getVar('pattern_two_cycle');
        $question_name = $this->getQuestionById($question_id);
        $left_memorize_p_two = $question_name->left_memorize_p_two;
        $left_memorize_h_p_two = $question_name->left_memorize_h_p_two;
        $right_memorize_p_two = $question_name->right_memorize_p_two;
        $right_memorize_h_p_two = $question_name->right_memorize_h_p_two;
        
        $this->session->set('firstleftSerial',$left_memorize_p_two_ans);
        $left_content = array();
        $right_content = array();
        if (isset($question_name->hide_pattern_two_left))
        {
            $hide_pattern_two_left = $question_name->hide_pattern_two_left;
            $left_content = $this->MemorizationAnswerMatching($cycle,$left_memorize_p_two,$left_memorize_p_two_ans,$left_memorize_h_p_two);
        }
        // if (isset($question_name->hide_pattern_two_right))
        // {
        //     $hide_pattern_two_right = $question_name->hide_pattern_two_right;
        //     $right_content = $this->MemorizationAnswerMatching($cycle,$right_memorize_p_two,$right_memorize_p_two_ans,$right_memorize_h_p_two);
        // }
        $right_content = $this->MemorizationAnswerMatchingTwo($left_memorize_p_two_ans,$right_memorize_p_two_ans);
        $cycle = $cycle + 2;
        $data['cycle'] = $cycle;
        $data['left_content'] = $left_content;
        $data['right_content'] = $right_content;
        echo json_encode($data);
    }

    public function MemorizationAnswerMatching($cycle,$tutorAns,$stdAns,$hiddenContent)
    {
        $data = array();
        $matchingAnswer = array();
        $correct = 1;
        $singleSentences = array();
        $word = array();
        foreach($hiddenContent as $key=>$item)
        {
            $TAns = str_replace(array('.', ' ', "\n", "\t", "\r"), '', strip_tags($tutorAns[$key][0]));
            
            $SAns = str_replace(array('.', ' ', "\n", "\t", "\r"), '', $stdAns[$key]);
            

            if ($item[0] == 1)
            {
                if ($TAns === $SAns)
                {
                    $matchingAnswer[$key][0] =  strip_tags($tutorAns[$key][0]);
                    $matchingAnswer[$key][1] =  1;
                }else{
                    $matchingAnswer[$key][0] =  $stdAns[$key];
                    $matchingAnswer[$key][1] =  0;
                    $correct = 0;
                }
            }else
            {
                $matchingAnswer[$key][0] = strip_tags($tutorAns[$key][0]);
                $matchingAnswer[$key][1] = 2;
            }

        }
        if ($correct == 0)
        {
            foreach ($tutorAns as $key=>$tutorAn) {

                if ($hiddenContent[$key][0] == 1)
                {
                    $word[$key][] = explode(" ",trim($tutorAn[0]));
                }
            }
            $data['clue']= $this->clueArray($cycle,$word);
        }
        $data['matchingAnswer']=$matchingAnswer;
        $data['correct']=$correct;
        return $data;
    }

    public function MemorizationAnswerMatchingTwo($left_memorize_p_two_ans,$right_memorize_p_two_ans){
        $data = array();
        $matchingAnswer = array();
        $correct = 1;
        $singleSentences = array();
        $word = array();
        foreach ($left_memorize_p_two_ans as $key => $value) {
            $left_result_val = $value;
            $right_result_val = $right_memorize_p_two_ans[$key];

            if ($left_result_val == $right_result_val) {
                $matchingAnswer[$key][0] =  $right_result_val;
                $matchingAnswer[$key][1] =  1;
            }else{
                $matchingAnswer[$key][0] =  $right_result_val;
                $matchingAnswer[$key][1] =  0;
                $correct = 0;
            }

        }
        if ($correct == 0)
        {
            foreach ($tutorAns as $key=>$tutorAn) {

                if ($hiddenContent[$key][0] == 1)
                {
                    $word[$key][] = explode(" ",trim($tutorAn[0]));
                }
            }
            $data['clue']= $this->clueArray($cycle,$word);
        }

        $data['matchingAnswer']=$matchingAnswer;
        $data['correct']=$correct;
        return $data;
        // echo "<pre>";print_r($right_result_val);die();
    }

    public function preview_memorization_pattern_two_take_decesion()
    {
        $PreviewClass=new \PreviewClass();

        $qus_ans =0;
        $question_marks = 0;
        $question_id = $this->request->getVar('question_id');
        $module_id = $this->request->getVar('module_id');
        $question_order_id = $this->request->getVar('current_order');
        $memorization_answer = $this->request->getVar('memorization_answer');
        //$submit_cycle = $this->input->post('submit_cycle');
        $text = 0;
        $text_1 = 0;

        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        if ($memorization_answer == 'correct')
        {
            $ans_is_right = 'correct';
            if (isset($answer_info[0]['questionMarks']))
            {
                $question_marks = $answer_info[0]['questionMarks'];
            }
        }else{
            $ans_is_right = 'wrong';
            $text_1 =1;
        }
        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, array());
    }

    public function preview_memorization_p_three_start_memorization()
    {
        $data = array();
        $question_id = $this->request->getVar('question_id');
        $question_name = $this->getQuestionById($question_id);
        $left_memorize_h_p_three = $question_name->left_memorize_h_p_three;
        $right_memorize_h_p_three = $question_name->right_memorize_h_p_three;
        $left_memorize_p_three = $question_name->left_memorize_p_three;
        $right_memorize_p_three = $question_name->right_memorize_p_three;
        $html = '';
        $i = 1;
        foreach($left_memorize_p_three as $key=>$left_data)
        {
            $html .= '<div class="row" style="margin-bottom: 10px;">';
            if ($left_memorize_h_p_three[$key] == 1)
            {
                $html .= '<div class="col-sm-5" style="border:1px solid #ccc;">';
                $html .= '<button valueId="left_image_ans_'.$i.'" imageId ="left_'.$i.'" type="button" class="show_all_images left_'.$i.'" style="width: 100%;height: 150px;">click</button>';
                $html .='<img src="" id="left_'.$i.'" style="margin: auto;" class="img-responsive">';
                $html .= '<input type="hidden" name="left_image_ans[]" class="left_image_ans_'.$i.'">';
                $html .= '</div>';

            }else{
                $html .= '<div class="col-sm-5" style="border:1px solid #ccc;">';
                $html .= '<img style="height:150px;margin: auto;" class="img-responsive" src="'.base_url().'/assets/uploads/'.$left_data.'">';
                $html .= '<input type="hidden" name="left_image_ans[]" class="left_image_ans_'.$i.'">';
                $html .= '</div>';
            }

            $html .= '<div class="col-sm-1">
                                            <span class="left_r_p_box_two_'.$i.'" style="display:none;position: absolute;font-weight:bold;top: 5px;color: green;">✓</span><span class="left_w_p_box_two_'.$i.'" style="display:none;position: absolute;font-weight:bold;top: 5px;color: red;">✖</span>
                                        </div>';

            if ($right_memorize_h_p_three[$key] == 1)
            {
                $html .= '<div class="col-sm-5" style="border:1px solid #ccc;">';
                $html .= '<button valueId="right_image_ans_'.$i.'" imageId ="right_'.$i.'" type="button" class="show_all_images right_'.$i.'" style="width: 100%;height: 150px;">click</button>';
                $html .='<img src="" id="right_'.$i.'" style="margin: auto;" class="img-responsive">';
                $html .= '<input type="hidden" name="right_image_ans[]" class="right_image_ans_'.$i.'">';
                $html .= '</div>';

            }else{
                $html .= '<div class="col-sm-5" style="border:1px solid #ccc;">';
                $html .= '<img style="height:150px;margin: auto;" class="img-responsive" src="'.base_url().'/assets/uploads/'.$right_memorize_p_three[$key].'">';
                $html .= '<input type="hidden" name="right_image_ans[]" class="right_image_ans_'.$i.'">';
                $html .= '</div>';
            }

            $html .= '<div class="col-sm-1">
                                            <span class="right_r_p_box_two_'.$i.'" style="display:none;position: absolute;font-weight:bold;top: 5px;color: green;">✓</span><span class="right_w_p_box_two_'.$i.'" style="display:none;position: absolute;font-weight:bold;top: 5px;color: red;">✖</span>
                                        </div>';
            $html .= '</div>';
            $i++;
        }

        echo json_encode($html);
    }


    public function module_preview_memorization_pattern_three_ans_matching()
    {
        $data = array();
        $question_id = $this->request->getVar('question_id');
        // $left_image_ans = $this->input->post('left_image_ans');
        // $right_image_ans = $this->input->post('right_image_ans');
        $order = $this->request->getVar('order');
        $question_name = $this->getQuestionById($question_id);

        $question_step = $question_name->question_step_memorize_p_three;
        $wrong_order_check = 0;
        $qus_setup_array = [];
        $last_step = 0;
        foreach ($question_step as $key => $value) {
            $qus_setup_array[$key]['question_step'] = $value[0];
            $qus_setup_array[$key]['clue'] = $value[1];
            $qus_setup_array[$key]['ecplanation'] = $value[2];
            $qus_setup_array[$key]['answer_status'] = $value[3];
            $qus_setup_array[$key]['order'] = $key +1;
            if($value[3] == 0){
                $last_step = $last_step + 1;
            }

            if ($order == $key +1) {
                if ($value[3] == 1) {
                    $wrong_order_check = 1;
                }
            }
        }
        $data1['qus_setup_array'] = $qus_setup_array;
        $data['wrong_order_check'] = $wrong_order_check;
        $data['last_answer_order'] = $order;
        $data['next_step'] = 1;
        $activeOrder =  $this->session->get('question_setup_answer_order');
        $data['active_order'] = $activeOrder;
        // echo $last_step;die();
        if ($order == $activeOrder) {

            $data['answer_status'] = 1;
            $correct = 1;

            if($activeOrder == $last_step){
                $data['next_step'] = 0;
            }else{
                $data['next_step'] = 1;
            }
            $this->session->set('question_setup_answer_order',$activeOrder + 1);

            $data['active_order'] = $activeOrder +1;
        }else{
            $data['answer_status'] = 0;
            $correct = 0;
        }


        $question_step_details = $data['qus_setup_array'];

        if ($correct == 0)
        {
            $data['correct'] = $correct;
        }else{
            $data['correct'] = $correct;
        }

        // echo "<pre>";print_r($data);die();
        echo json_encode($data);
        
        
        // $data = array();
        // $question_id = $this->input->post('question_id');
        // $left_image_ans = $this->input->post('left_image_ans');
        // $right_image_ans = $this->input->post('right_image_ans');
        // $question_name = $this->getQuestionById($question_id);
        // $left_memorize_h_p_three = $question_name->left_memorize_h_p_three;
        // $right_memorize_h_p_three = $question_name->right_memorize_h_p_three;
        // $left_memorize_p_three = $question_name->left_memorize_p_three;
        // $right_memorize_p_three = $question_name->right_memorize_p_three;

        // $leftAnsMatching = array();
        // $rightAnsMatching = array();
        // $correct = 1;
        // foreach($left_memorize_p_three as $key=>$leftData)
        // {
        //     if ($left_memorize_h_p_three[$key] == 1)
        //     {
        //         if ($leftData == $left_image_ans[$key])
        //         {
        //             $leftAnsMatching[] = 1;
        //         }else
        //         {
        //             $leftAnsMatching[] = 0;
        //             $correct = 0;
        //         }

        //     }else{
        //         $leftAnsMatching[] = 2;
        //     }

        // }
        // foreach($right_memorize_p_three as $key=>$rightData)
        // {
        //     if ($right_memorize_h_p_three[$key] == 1)
        //     {
        //         if ($rightData == $right_image_ans[$key])
        //         {
        //             $rightAnsMatching[] = 1;
        //         }else
        //         {
        //             $rightAnsMatching[] = 0;
        //             $correct = 0;
        //         }

        //     }else{
        //         $rightAnsMatching[] = 2;
        //     }

        // }
        // $data['leftAnsMatching'] = $leftAnsMatching;
        // $data['rightAnsMatching'] = $rightAnsMatching;
        // if ($correct == 0)
        // {
        //     $data['correct'] = $correct;
        // }else{
        //     $data['correct'] = $correct;
        // }

        // echo json_encode($data);
    }

    public function preview_memorization_pattern_three_take_decesion()
    {
        error_report_check();
        $PreviewClass=new \PreviewClass();
        $qus_ans =0;
        $question_marks = 0;
        $question_id = $this->request->getVar('question_id');
        $module_id = $this->request->getVar('module_id');
        $question_order_id = $this->request->getVar('current_order');
        $memorization_answer = $this->request->getVar('memorization_answer');
        //$submit_cycle = $this->input->post('submit_cycle');
        $text = 0;
        $text_1 = 0;

        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        if ($memorization_answer == 'correct')
        {
           
            if (isset($answer_info[0]['questionMarks']))
            {
                $question_marks = $answer_info[0]['questionMarks'];
            }
        }else{

            $text_1 =1;
        }
        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, array(),$next_step_patten_two);
    }


    public function preview_memorization_pattern_three_try_again()
    {

        $data = array();
        $leftAns = explode(",",$this->request->getVar('leftAns'));
        $rightAns = explode(',',$this->request->getVar('rightAns'));

        $question_id = $this->request->getVar('question_id');
        $left_image_ans = $this->request->getVar('left_image_ans');
        $right_image_ans = $this->request->getVar('right_image_ans');
        $question_name = $this->getQuestionById($question_id);
        $left_memorize_h_p_three = $question_name->left_memorize_h_p_three;
        $right_memorize_h_p_three = $question_name->right_memorize_h_p_three;
        $left_memorize_p_three = $question_name->left_memorize_p_three;
        $right_memorize_p_three = $question_name->right_memorize_p_three;

        $html = '';
        $i = 1;
        foreach($left_memorize_p_three as $key=>$left_data)
        {
            $html .= '<div class="row" style="margin-bottom: 10px;">';
            if ($left_memorize_h_p_three[$key] == 1)
            {
                $html .= '<div class="col-sm-5" style="border:1px solid #ccc;">';

                if ($leftAns[$key] == 1)
                {
                    $html .= '<button valueId="left_image_ans_'.$i.'" imageId ="left_'.$i.'" type="button" class="show_all_images left_'.$i.'" style="width: 100%;height: 150px;position: absolute;opacity: 0;">click</button>';

                    $html .='<img sid="left_'.$i.'" style="margin: auto;height:150px;" class="img-responsive" src="'.base_url().'/assets/uploads/'.$left_data.'">';
                    $html .= '<input type="hidden" name="left_image_ans[]" class="left_image_ans_'.$i.'" value="'.$left_data.'">';
                }else
                {
                    $html .= '<button valueId="left_image_ans_'.$i.'" imageId ="left_'.$i.'" type="button" class="show_all_images left_'.$i.'" style="width: 100%;height: 150px;">click</button>';

                    $html .='<img src="" id="left_'.$i.'" style="margin: auto;" class="img-responsive">';
                    $html .= '<input type="hidden" name="left_image_ans[]" class="left_image_ans_'.$i.'">';
                }
                $html .= '</div>';

            }else{
                $html .= '<div class="col-sm-5" style="border:1px solid #ccc;">';
                $html .= '<img style="height:150px;margin: auto;" class="img-responsive" src="'.base_url().'/assets/uploads/'.$left_data.'">';

                $html .= '<input type="hidden" name="left_image_ans[]" class="left_image_ans_'.$i.'">';

                $html .= '</div>';
            }
            if ($leftAns[$key] == 1)
            {
                $html .= '<div class="col-sm-1">
                                            <span class="left_r_p_box_two_'.$i.'" style="display:block;position: absolute;font-weight:bold;top: 5px;color: green;">✓</span><span class="left_w_p_box_two_'.$i.'" style="display:none;position: absolute;font-weight:bold;top: 5px;color: red;">✖</span>
                                        </div>';
            }else
            {
                $html .= '<div class="col-sm-1">
                                            <span class="left_r_p_box_two_'.$i.'" style="display:none;position: absolute;font-weight:bold;top: 5px;color: green;">✓</span><span class="left_w_p_box_two_'.$i.'" style="display:none;position: absolute;font-weight:bold;top: 5px;color: red;">✖</span>
                                        </div>';
            }


            if ($right_memorize_h_p_three[$key] == 1)
            {
                $html .= '<div class="col-sm-5" style="border:1px solid #ccc;">';

                if ($rightAns[$key] == 1)
                {
                    $html .= '<button valueId="right_image_ans_'.$i.'" imageId ="right_'.$i.'" type="button" class="show_all_images right_'.$i.'" style="width: 100%;height: 150px;position: absolute;opacity: 0;">click</button>';
                    $html .='<img  id="right_'.$i.'" style="margin: auto;height:150px;" class="img-responsive" src="'.base_url().'/assets/uploads/'.$right_memorize_p_three[$key].'">';
                    $html .= '<input type="hidden" name="right_image_ans[]" class="right_image_ans_'.$i.'" value="'.$right_memorize_p_three[$key].'">';
                }else
                {
                    $html .= '<button valueId="right_image_ans_'.$i.'" imageId ="right_'.$i.'" type="button" class="show_all_images right_'.$i.'" style="width: 100%;height: 150px;">click</button>';
                    $html .='<img src="" id="right_'.$i.'" style="margin: auto;" class="img-responsive">';
                    $html .= '<input type="hidden" name="right_image_ans[]" class="right_image_ans_'.$i.'">';
                }

                $html .= '</div>';

            }else{
                $html .= '<div class="col-sm-5" style="border:1px solid #ccc;">';
                $html .= '<img style="height:150px;margin: auto;" class="img-responsive" src="'.base_url().'/assets/uploads/'.$right_memorize_p_three[$key].'">';

                $html .= '<input type="hidden" name="right_image_ans[]" class="right_image_ans_'.$i.'">';
                $html .= '</div>';
            }

            if ($rightAns[$key] == 1)
            {
                $html .= '<div class="col-sm-1">
                                            <span class="right_r_p_box_two_'.$i.'" style="display:block;position: absolute;font-weight:bold;top: 5px;color: green;">✓</span><span class="right_w_p_box_two_'.$i.'" style="display:none;position: absolute;font-weight:bold;top: 5px;color: red;">✖</span>
                                        </div>';
            }else
            {
                $html .= '<div class="col-sm-1">
                                            <span class="right_r_p_box_two_'.$i.'" style="display:none;position: absolute;font-weight:bold;top: 5px;color: green;">✓</span><span class="right_w_p_box_two_'.$i.'" style="display:none;position: absolute;font-weight:bold;top: 5px;color: red;">✖</span>
                                        </div>';
            }

            $html .= '</div>';
            $i++;
        }

        echo json_encode($html);
    }

    public function preview_memorization_pattern_three_ok()
    {
        echo '<pre>';
        print_r($_POST);
        echo '<br>';
        die();
    }


    public function clueArray($cycle,$words)
    {
        $html ='';
        foreach ($words as $word)
        {
            $countW = count($word);
            $html .= '<div style="overflow: hidden">';
            for($i = 0;$i<$countW;$i++)
            {
                $countT = count($word[$i]);
                for($j = 0;$j<=$countT;$j++)
                {
                    if (isset($word[$i][$j]))
                    {
                        if ($j <= $cycle)
                        {
                            $html .= '<div style="float:left;height: 35px;min-width: 30px;margin-bottom:5px;margin-right:5px;display: inline-block;padding: 5px;border: 1px solid #ccc;">'.$word[$i][$j].'</div>';
                        }else
                        {
                            $html .= '<div style="float:left;height: 35px;min-width: 30px;margin-bottom:5px;margin-right:5px;display: inline-block;padding: 5px;border: 1px solid #ccc;"> </div>';
                        }

                    }
                }
            }
            $html .= '</div>';
        }
        return $html;
    }

    public function contentModify($data)
    {
        
        $modifyData = array();
        
        foreach($data as $key => $value)
        {
            $modifyData[$key]['left'] = $value[0];
            $modifyData[$key]['sl']   = $key+1;
        }

        shuffle($modifyData);
        return $modifyData;
    }

    public function contentModifyByHidden($data,$checkData)
    {
        $modifyData = array();
        foreach($data as $key=>$value)
        {
            if ($checkData[$key][0] == 1)
            {
                $modifyData[] = '';
            }else
            {
                $modifyData[] = strip_tags($value[0]);
            }
        }
        return $modifyData;
    }

    public function getQuestionById($question_id)
    {
        $TutorClass=new \TutorClass();

        $question_info = $TutorClass->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        return $question_name;
    }

    public function module_preview_memorization_pattern_one_matching()
    {
        $TutorClass=new \TutorClass();
        $show_data_array = array();
        $question_id = $this->request->getVar('question_id');
        $start_memorization_one_value = $this->request->getVar('start_memorization_one_value');
        $question_info = $TutorClass->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        if ($start_memorization_one_value == 1)
        {
            $show_data_array['show_data_array'] = $this->memorization_ans_data($question_name);
            $show_data_array['all_correct'] = 1;
        }else
        {
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
        foreach ($left_memorize_p_one as $key=>$item) {
            if ($left_memorize_h_p_one[$key] == 0)
            {
                $show_data_array[$key][0] = $item;
                $show_data_array[$key][1] = 0;
            }else
            {
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
        foreach ($left_memorize_p_one as $key=>$item) {
            if ($left_memorize_h_p_one[$key] == 0)
            {
                $show_data_array[] = $item;
            }else
            {
                $show_data_array[] = '';
            }
        }
        return $show_data_array;
    }


    public function memorization_ans_data_four($question_name)
    {

        $show_data_array = array();
        $left_memorize_h_p_four = $question_name->left_memorize_h_p_four;
        $left_memorize_p_four = $question_name->left_memorize_p_four;
        $right_memorize_p_four = $question_name->right_memorize_p_four;
        $left_memorize_p_four = array_map('strtolower', $left_memorize_p_four);
        foreach ($left_memorize_p_four as $key=>$item) {
            if ($left_memorize_h_p_four[$key] == 0)
            {
                $show_data_array[$key][0] = '';
                $show_data_array[$key][1] = 0;
            }else
            {
                $show_data_array[$key][0] = $item;
                $show_data_array[$key][1] = 1;
            }
        }

        shuffle($show_data_array);
        return $show_data_array;
    }

        //preview_memorization
        public function memorization_hide_data_four($question_name)
        {
            $show_data_array = array();
            $left_memorize_h_p_four = $question_name->left_memorize_h_p_four;
            $left_memorize_p_four = $question_name->left_memorize_p_four;
            $right_memorize_p_four = $question_name->right_memorize_p_four;
            $left_memorize_p_four = array_map('strtolower', $left_memorize_p_four);
            $right_memorize_p_four = array_map('strtolower', $right_memorize_p_four);
            foreach ($left_memorize_p_four as $key=>$item) {
                    if ($left_memorize_h_p_four[$key] == 0)
                    {
                        $show_data_array[$key]['left'] = $item;
                        $show_data_array[$key]['right'] = $right_memorize_p_four[$key];
                    }else
                    {
                        $show_data_array[$key]['left'] = '';
                        $show_data_array[$key]['right'] = '';
                    }
            }
    
            shuffle($show_data_array);
            return $show_data_array;
    }
    
   
    public function module_preview_memorization_pattern_four_try()
    {
        $TutorClass=new \TutorClass();
        $data = array();
        $correctAnswerSession = $this->session->get('correct_answer');
        $all_check_hint = $this->request->getVar('all_check_hint');
        $question_id = $this->request->getVar('question_id');
        $correctAnswerStd = $this->request->getVar('correctAnswer');
        $question_info = $TutorClass->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        $correctAnswer = explode(",",$correctAnswerStd);
        $show_data_array = $this->memorization_hide_data_four($question_name);
        $left_memorize_p_four = $question_name->left_memorize_p_four;
        $left_memorize_p_four = array_map('strtolower', $left_memorize_p_four);
        $show_correct_ans = array();
        $show_error_ans = array();

        foreach ($correctAnswer as $key=>$item) {
            if ($item == 1)
            {
                // $show_correct_ans[] = $left_memorize_p_four[$key];
                $show_correct_ans[] = $correctAnswerSession[$key];
            }else
            {
                $show_correct_ans[] = '';
            }
        }
        $data['show_data_array']=$show_data_array;
        if ($all_check_hint == 1)
        {
            foreach ($correctAnswer as $key=>$item) {
                if ($item != 1) {
                    // $show_error_ans[] = $left_memorize_p_four[$key];
                    $show_error_ans[] = $correctAnswerSession[$key];
                } else {
                    $show_error_ans[] = '';
                }
            }
            $data['show_data_array']=$show_error_ans;
            $data['all_check_hint']=1;
        }



        $array = array();
        foreach ($show_data_array as $sda => $value) {
            $right = $value['right'];
            if (in_array($right, $show_correct_ans)) {
                $array[$sda] = $right;
            }else{
                $array[$sda] = '';
            }
        }
        $data['show_correct_ans'] = $array;
        // $data['show_correct_ans'] = $show_correct_ans;

        echo json_encode($data);
    }
    

    public function module_creative_quiz_ans_matching()
    {
        $TutorClass=new \TutorClass();
        $response=array(
            'success'=> false,
            'error'=> false,
            'msg'=>'',
            'array_sequence' => '',
        );
        $clue_value = $this->request->getVar('clue_id');
        if ($clue_value >= 3)
        {
            $clue_id = $clue_value;
        }else
        {
            $clue_id = $clue_value+1;
        }

        $valueOfContent = $this->request->getVar('valueOfContent');
        $idOfContent = json_decode($this->request->getVar('idOfContent'));
        $AnswerData = array();
        $questionId = $this->request->getVar('questionId');
        $CreateParagraph = $this->request->getVar('createParagraphData');
        $data = $this->request->getVar('Pdata');
        $question_info = $TutorClass->getInfo('tbl_question', 'id', $questionId);
        $question_name = json_decode($question_info[0]['questionName']);
        $question_description = json_decode($question_info[0]['questionDescription']);
        $answer = json_decode($question_info[0]['answer']);
        $paragraphOrder = $question_name->paragraph_order;
        $sentences = $question_name->sentence;



        $ContentId = array();
        $matchResult = array();
        $NotMatchResult = array();
        if (!empty($idOfContent))
        {
            $idcount = count($idOfContent);

            for ($i=0;$i<$idcount;$i++)
            {
                $idJcount = count($idOfContent[$i]);
                for ($j=0;$j<$idJcount;$j++)
                {
                    $ContentId[] = $idOfContent[$i][$j]+1;
                }
            }
        }

        $notInParagraph =array();
        $ContentId_length = count($ContentId);
        $answer_length = count($answer);
        $ContentId_length = count($ContentId);
        $answer_length = count($answer);
        $test = array();
        for ($x = 0;$x<$answer_length;$x++)
        {
            if (isset($ContentId[$x]))
            {
                if($answer[$x] != $ContentId[$x])
                {
                    $test[] = $ContentId[$x];
                }
            }
        }
        $notInParagraph= $test;
        $notInParagraphR = array();
        $ncount = count($notInParagraph);
        for ($n = 0;$n<$ncount;$n++)
        {
            $notInParagraphR[] = $notInParagraph[$n]-1;
        }

        $Idlength = count($answer);
        for($i =0;$i<$Idlength;$i++)
        {
            $ansValue =  $answer[$i];

            if (!empty($ContentId[$i]))
            {
                if ($ansValue == $ContentId[$i])
                {
                    $matchResult[]=$ContentId[$i];
                }else
                {
                    $NotMatchResult[]=$ContentId[$i];
                }
            }

        }


//        $NotMatchResult this array for answer sequence are not match id

        $matchingError = array();
        $paraIndex = array();

        $ansCount = count($paragraphOrder);
        for ($i = 0;$i<$ansCount;$i++)
        {
            if ($paragraphOrder[$i] == '')
            {
                $paraIndex[0][] = $i;
            }else{
                $paraIndex[$paragraphOrder[$i]][] = $i;
            }
        }

        $countIndex = count($paraIndex);


        if (!empty($paraIndex[0]))
        {
            for ($x = 0;$x<$countIndex;$x++)
            {
                if (!empty($idOfContent[$x]))
                {
                    $acb  = array_diff($idOfContent[$x],$paraIndex[$x]);
                    $matchingError[$x]= array_values($acb);
                }else
                {
                    $matchingError[$x] = [];
                }

            }
        }else
        {
            for ($x = 1;$x<$countIndex;$x++)
            {
                if (!empty($idOfContent[$x]))
                {
                    $acb  = array_diff($idOfContent[$x],$paraIndex[$x]);
                    $matchingError[$x]= array_values($acb);
                }else
                {
                    $matchingError[$x] = [];
                }
            }
        }

//        $matchingError this array is paragraph sequence id


        $NotMatchResults = array();
        if (!empty($NotMatchResult))
        {
            $idcount = count($NotMatchResult);

            for ($i=0;$i<$idcount;$i++)
            {
                $NotMatchResults[]=$NotMatchResult[$i]-1;
            }
        }

        $matchingErrors = array();
        if (!empty($matchingError))
        {
            $idcount = count($matchingError);

            for ($i=0;$i<$idcount;$i++)
            {
                if(!empty($matchingError[$i]))
                {
                    $countK = count($matchingError[$i]);
                    for ($k = 0;$k<$countK;$k++)
                    {
                        $matchingErrors[]=$matchingError[$i][$k];
                    }
                }
            }
        }

        $ErrorMessage = array();
        $userId = array();
        if (!empty($idOfContent))
        {
            $idcount = count($idOfContent);

            for ($i=0;$i<$idcount;$i++)
            {
                $idJcount = count($idOfContent[$i]);
                for ($j=0;$j<$idJcount;$j++)
                {
                    $userId[] = $idOfContent[$i][$j];
                }
            }
        }

        $cCount = count($userId);
        for ($c = 0;$c<$cCount;$c++)
        {
            $id = $userId[$c];
            $msg = $this->MessageCheck($id,$question_description,$test);
            if (!empty($msg))
            {
                $ErrorMessage[$id]=$msg;
            }
        }

        $msgArrayId = array_values($ErrorMessage);
        $TestMsg = array();
        $msgCount = count($msgArrayId);

        for ($f = 0;$f<$msgCount;$f++)
        {
            if ($msgArrayId[$f] == '')
            {

            }else
            {
                $TestMsg = $msgArrayId[$f];
            }
        }

        $data = array();

        $data['ErrorMessage'] = $ErrorMessage;

        $questionId = $this->request->getVar('questionId');
        $question_order_id = $this->request->getVar('current_order');
        $module_id = $this->request->getVar('module_id');
        $question_marks =$question_info[0]['questionMarks'];


        if (!empty($TestMsg) && !empty($test))
        {
            if (!empty($NotMatchResults))
            {
                $text = 0;
                $text_1 = 1;
                $this->take_decesion($question_marks, $questionId, $module_id, $question_order_id, $text, $text_1, array());
                $response=array(
                    'success'=> false,
                    'error'=> false,
                    'msg'=>'failed',
                    'data'=>$data,
                    'clue_id'=>$clue_id,
                    'array_sequence' => 'Paragraph order is Not correct.',
                );
            }else
            {

                $text = 0;
                $text_1 = 1;
                $this->take_decesion($question_marks, $questionId, $module_id, $question_order_id, $text, $text_1, array());
                $response = array(
                    'success'=> false,
                    'error'=> true,
                    'msg'=>'failed',
                    'data'=>$data,
                    'clue_id'=>$clue_id,
                    'array_sequence' => '',
                );
            }


        }elseif ($ContentId_length != $answer_length ) {

                $text = 0;
                $text_1 = 1;
                $this->take_decesion($question_marks, $questionId, $module_id, $question_order_id, $text, $text_1, array());
            $response=array(
                'success'=> false,
                'error'=> true,
                'msg'=>'failed',
                'data'=>$data,
                'clue_id'=>$clue_id,
                'array_sequence' => 'Paragraph order is Not correct.',
            );
        }
        else
        {
            $text = 0;
            $text_1 = 0;
            $this->take_decesion($question_marks, $questionId, $module_id, $question_order_id, $text, $text_1, array());
            $response=array(
                'success'=> true,
                'error'=> false,
                'msg'=>'success',
                'clue_id'=>$clue_id,
            );
        }
        
        echo json_encode($response);

    }

    public function MessageCheck($id,$question_description,$matchingErrors)
    {

        $desCount = count($question_description);
        for ($d=0;$d<$desCount;$d++)
        {
            if ($question_description[$id])
            {
                return $question_description[$id];
            }else
            {
                $notCP =  $this->checkNotCP($id,$matchingErrors);
                return $notCP;
            }
        }
    }


    public function checkNotCP($id,$matchingErrors)
    {
        $mECount = count($matchingErrors);
        for ($x=0;$x<$mECount;$x++)
        {
            if ($matchingErrors[$x] == $id)
            {
                return 'not in the right paragraph.';
            }
        }
    }

    public function st_answer_skip()
    {
        $TutorClass=new \TutorClass();
        $module_id = $_POST['module_id'];
        //$question_order_id = $_POST['next_question'] - 1;
        $question_order_id = $_POST['current_order'];
        $post = $this->request->getVar();
        $questionId = $this->request->getVar('question_id');
        $givenAns = $this->indexQuesAns($post['given_ans']);

        $temp = $TutorClass->getInfo('tbl_question', 'id', $questionId);
        $question_marks = $temp[0]['questionMarks'];
        $savedAns = $this->indexQuesAns(json_decode($temp[0]['answer']));

        $temp2 = json_decode($temp[0]['questionName']);
        $numOfRows = $temp2->numOfRows;
        $numOfCols = $temp2->numOfCols;
        //echo $numOfRows .' ' . $numOfCols;
        $wrongAnsIndices = [];

        $text = 0;
        $text_1 = 0;
        for ($row = 1; $row <= $numOfRows; $row++) {
            for ($col = 1; $col <= $numOfCols; $col++) {
                if (isset($savedAns[$row][$col])) {
                    if (isset($givenAns[$row][$col]))
                    {
                        $wrongAnsIndices[] = ($savedAns[$row][$col] != $givenAns[$row][$col]) ? $row . '_' . $col : null;
                    }else {
                    $wrongAnsIndices[] = $row . '_' . $col;

                    }
                }
            }
        }

        $wrongAnsIndices = array_filter($wrongAnsIndices);
        if (count($wrongAnsIndices)) {//For False Condition
            $text_1 = 1;
        }

        $this->take_decesion($question_marks, $questionId, $module_id, $question_order_id, $text, $text_1);
    }

    public function mudule_answer_sentence_matching()
    {
        // echo "<pre>";print_r($this->request->getVar());die();
        error_report_check();
        $PreviewClass=new \PreviewClass();
        $total = count($_POST['answer']);

        $question_id = $_POST['question_id'];
        $st_ans = array();
        
        for ($i = 0; $i < $total; $i++) {
            $find_ans = explode(',,', $_POST['answer'][$i]);
            $st_ans[] = $find_ans[0];
        }
        

        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);
        $question_marks = $answer[0]['questionMarks'];
        $answer_info['student_ans'] = $st_ans;
        
        $module_id = $_POST['module_id'];
        $question_order_id = $_POST['current_order'];
        $text = 0;
        $text_1 = 0;
        $flag = 1;
        for ($k = 0; $k < sizeof($answer_info['student_ans']); $k++) {
            if ($answer_info['student_ans'][$k] != $answer_info['tutor_ans'][$k]) {
                $text++;
                $flag = 0;
            }
        }
        if($text == 0){
            $answer_info = 2;
        }else{
            $answer_info = 3;
        }
        
        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, json_encode($answer_info));
    }

    public function module_answer_word_memorization()
    {
        // print_r($_POST);die();

        error_report_check();
        $PreviewClass=new \PreviewClass();
        $total = count($_POST['answers']);
        
        $question_id = $_POST['question_id'];
        $st_ans = array();
        
        for ($i = 0; $i < $total; $i++) {
            $st_ans[] = $_POST['answers'][$i];
        }
        

        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);
        $question_marks = $answer[0]['questionMarks'];
        $answer_info['student_ans'] = $st_ans;
        // print_r($answer_info['student_ans']);die();

        $module_id = $_POST['module_id'];
        $question_order_id = $_POST['current_order'];
        $text = 0;
        $text_1 = 0;
        $flag = 1;
        for ($k = 0; $k < sizeof($answer_info['student_ans']); $k++) {
            if ($answer_info['student_ans'][$k] != $answer_info['tutor_ans'][$k]) {
                $text++;
                $flag = 0;
            }
        }
        //echo $text.'//'.$text_1;die();zzz
        if($text == 0){
            $answer_info = 2;
        }else{
            $answer_info = 3;
        }
        
        
        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, json_encode($answer_info));
    }
	
	 public function question_answer_matching_comprehension()
    {
        $PreviewClass=new \PreviewClass();
        $answer = strtolower($this->request->getVar('answer'));

        if($answer=='write_ans'){
            echo 4;
        }else{ 
            if (!$this->validate('answer_machingValidate')) {
                echo 1;
            } else {
                $answer = strtolower($this->request->getVar('answer'));
                $question_id = $this->request->getVar('question_id');

                $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
                $text_1 = strtolower($answer_info[0]['answer']);
                
                if ($answer == $text_1) {
                    echo 2;
                } else {
                    echo 3;
                }
            }
        }
    }

    public function question_answer_matching_grammer()
    {
        $PreviewClass=new \PreviewClass();    
        $answer = strtolower($this->request->getVar('answer'));

            // print_r($this->input->post());die();
            if (!$this->validate('answer_machingValidate')) {
                echo 1;
            } else {
                $answer = strtolower($this->request->getVar('answer'));
                $question_id = $this->request->getVar('question_id');

                $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
                $text_1 = strtolower($answer_info[0]['answer']);
                if ($answer == $text_1) {
                    echo 2;
                } else {
                    echo 3;
                }
            }

    }
	
	public function module_answer_matching_comprehension()
    {
        $PreviewClass=new \PreviewClass(); 
        $answer = strtolower($this->request->getVar('answer'));
        
        // print_r($_POST);die();
        $PreviewClass=new \PreviewClass();
        $question_id = $_POST['question_id'];
        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);
        $question_marks = $answer[0]['questionMarks'];

        if($answer=='write_ans'){
            $answer_info['tutor_ans'] = '';
            $text = 1;
            $flag = 0;
        }else{ 
            

            if(isset($_POST['answer'])){
                $answer_info['student_ans'] = $_POST['answer'];
            }else{
                $answer_info['student_ans'] = '';
            }
            $module_id = $_POST['module_id'];
            $question_order_id = $_POST['current_order'];
            $text = 0;
            $text_1 = 0;
            $flag = 1;

            if($answer_info['student_ans']!=$answer_info['tutor_ans']){
                $text = 1;
                $flag = 0;
            }
        }
            //echo $text.'//'.$text_1;die();
            if($text == 0){
                $answer_info = 2;
            }else{
                $answer_info = 3;
            }


            $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, json_encode($answer_info));

       
    }
    

    public function module_answer_matching_grammer()
    {
        //echo "<pre>";print_r($_POST);die();
        $PreviewClass=new \PreviewClass();
        $question_id = $_POST['question_id'];
        
        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);
        $question_marks = $answer[0]['questionMarks'];

        
        if(isset($_POST['answer'])){
            $answer_info['student_ans'] = $_POST['answer'];
        }else{
            $answer_info['student_ans'] = '';
        }
        // print_r($answer_info);die();

        $module_id = $_POST['module_id'];
        $question_order_id = $_POST['current_order'];
        $text = 0;
        $text_1 = 0;
        $flag = 1;

        if($answer_info['student_ans']!=$answer_info['tutor_ans']){
            $text = 1;
            $flag = 0;
        }
        //echo $text.'//'.$text_1;die();
        if($text == 0){
            $answer_info = 2;
        }else{
            $answer_info = 3;
        }
        
        
        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, json_encode($answer_info));
    }

    public function module_preview_answer_glossary(){
        $PreviewClass=new \PreviewClass();
        $question_id = $_POST['question_id'];
        $question_type = $_POST['question_type'];
        
        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);
        $question_marks = $answer[0]['questionMarks'];

        $module_id = $_POST['module_id'];
        $question_order_id = $_POST['current_order'];
        $text = 0;
        $text_1 = 0;
        $flag = 1;
        $answer_info = 2;
        
        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, json_encode($answer_info));
    }

    public function module_answer_multiple_matching()
    {
        $PreviewClass=new \PreviewClass(); 
        
        if(isset($_POST['total_ans'])){
            $total = $_POST['total_ans'];
        }else{
            $total = 0;
        }

        $question_id = $_POST['question_id'];
        $st_ans = array();
        
        for ($i = 1; $i <= $total; $i++) {
            $ans_id = 'answer_' . $i;
            $st_ans[] = $_POST[$ans_id];
        }
        
        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);
        $question_marks = $answer[0]['questionMarks'];
        $answer_info['student_ans'] = $st_ans;

        $module_id = $_POST['module_id'];
        //$question_order_id = $_POST['next_question'] - 1;
        $question_order_id = $_POST['current_order'];
        $text = 0;
        $text_1 = 0;
        $flag = 1;
        for ($k = 0; $k < sizeof($answer_info['student_ans']); $k++) {
            if ($answer_info['student_ans'][$k] != $answer_info['tutor_ans'][$k]) {
                $text++;
                $flag = 0;
            }
        }
        $answer_info['student_ans'] = $st_ans;
        $answer_info['flag'] = $flag;

        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, json_encode($answer_info));
    }

    public function preview_save_answer_idea(){
        
        $PreviewClass=new \PreviewClass();
        $question_id = $_POST['question_id'];
        
        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);
        $question_marks = $answer[0]['questionMarks'];

        $module_id = $_POST['module_id'];
        $question_order_id = $_POST['current_order'];
        $text = 0;
        $text_1 = 0;
        $flag = 1;
        $answer_info = 2;
        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, $text, $text_1, json_encode($answer_info));
    }

    public function question_answer_matching_image_quiz()
    {
        $PreviewClass=new \PreviewClass();
        $answer = strtolower($_POST['answer']);

        if($answer=='write_ans'){
            echo 4;
        }else{ 
            if (!$this->validate('answer_machingValidate')) {
                echo 1;
            } else {
                $answer = strtolower($_POST['answer']);
                $question_id = $_POST['question_id'];

                $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
                $text_1 = strtolower($answer_info[0]['answer']);
                
                if ($answer == $text_1) {
                    echo 2;
                } else {
                    echo 3;
                }
            }
        }
    }
    
}
