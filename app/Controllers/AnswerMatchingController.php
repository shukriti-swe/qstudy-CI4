<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use TutorClass;

class AnswerMatchingController extends BaseController
{
    public function answer_matching()
    {
        $PreviewClass=new \PreviewClass();
        $question_id = $_POST['id'];

        $text = $_POST['user_answer'];
        $find = array('&nbsp;', '\n', '\t', '\r');
        $repleace = array('', '', '', '');
        $text = strip_tags($text);
        $text = str_replace($find, $repleace, $text);
        $text = trim($text);
  
        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);

        $text_1 = $answer_info[0]['answer'];
        $find = array('&nbsp;', '\n', '\t', '\r');
        $repleace = array('', '', '', '');
        $text_1 = strip_tags($text_1);
        $text_1 = str_replace($find, $repleace, $text_1);
        $text_1 = trim($text_1);
        if ($text == $text_1) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function answer_matching_true_false()
    {
        $PreviewClass=new \PreviewClass();
        $input = $this->validate([
            'answer' => 'required',
        ]);
        if (!$input) {
            echo 1;
        } else {
            $answer = $this->request->getVar('answer');
            $question_id = $this->request->getVar('question_id');
            $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
            $text_1 = $answer_info[0]['answer'];
            if ($answer == $text_1) {
                echo 2;
            } else {
                echo 3;
            }
        }
    }

    public function answer_matching_vocabolary()
    {
        $PreviewClass=new \PreviewClass();
        $input = $this->validate([
            'answer' => 'required',
        ]);
        if (!$input) {
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

    // public function answer_matching_multiple_choice()
    // {
    //     error_report_check();
    //     $PreviewClass=new \PreviewClass();
    //     $question_id = $_POST['id'];
    //     $answer_reply = $_POST['answer_reply'];
        
    //     $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
       
        
    //     // $answer_store = $answer_info[0]['answer'];
        
    //     // if ($answer_store == $answer_reply) {
    //     //     echo 1;
    //     // } else {
    //     //     echo 0;
    //     // }
        
    //     //ADDED AS
    //     $answer_store = json_decode($answer_info[0]['answer']);
        
    //     $result_count = count(array_intersect($answer_reply, $answer_store));
        
    //     if ($result_count == count($answer_store) && count($answer_reply) == count($answer_store)) {
    //         echo 1;
    //     } else {
    //         echo 0;
    //     }
    // }

    public function answer_matching_multiple_choice()
    {
        error_report_check();
        $PreviewClass=new \PreviewClass();

        $question_id = $_POST['id'];
        $answer_reply = $_POST['answer_reply'];
        
        $answer_info =  $PreviewClass->getInfo('tbl_question', 'id', $question_id);
       
        //ADDED AS
        $answer_store = json_decode($answer_info[0]['answer']);
        
        $result_count = count(array_intersect($answer_reply, $answer_store));
        
        if ($result_count == count($answer_store) && count($answer_reply) == count($answer_store)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function answer_matching_multiple_response()
    {
     
        $PreviewClass=new \PreviewClass();

        $question_id = $_POST['question_id'];
        $text_1 = $_POST['answer_reply'];

        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        //$text = $answer_info[0]['answer'];
        $question_marks = $answer_info[0]['questionMarks'];
        $text = json_decode($answer_info[0]['answer']);
        $result_count = count(array_intersect($text_1, $text));

        $module_id = $_POST['module_id'];
        // $question_order_id = $_POST['next_question'] - 1;
        $question_order_id = $_POST['current_order'];

        $this->take_decesion($question_marks, $question_id, $module_id, $question_order_id, count($text), $result_count);
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
                    $memorization_answer = $this->require->getVar('memorization_answer');
                    $memorization_part = $this->require->getVar('memorization_one_part');
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

                $link2 = $link1 . '/get_tutor_tutorial_module/' . $module_id . '/' . $question_order_id;

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

                $link2 = $link1 . '/get_tutor_tutorial_module/' . $module_id . '/' . $question_order_id;

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

            $link2 = $link1 . '/get_tutor_tutorial_module/' . $module_id . '/' . $question_order_id;

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

    public function check_Skip_boxAnswer()
    {

        $TutorClass=new \TutorClass();
        $post = $this->request->getVar();
        $questionId = $this->request->getVar('questionId');
        $givenAns = $this->indexQuesAns($post['given_ans']);

        $temp = $TutorClass->getInfo('tbl_question', 'id', $questionId);
        $savedAns = $this->indexQuesAns(json_decode($temp[0]['answer']));

        $temp2 = json_decode($temp[0]['questionName']);
        $numOfRows = $temp2->numOfRows;
        $numOfCols = $temp2->numOfCols;
        //echo $numOfRows .' ' . $numOfCols;
        $wrongAnsIndices = [];
        
        for ($row=1; $row<=$numOfRows; $row++) {
            for ($col=1; $col<=$numOfCols; $col++) {
                if (isset($savedAns[$row][$col]) && isset($givenAns[$row][$col])) {
                    $wrongAnsIndices[] = ($savedAns[$row][$col] != $givenAns[$row][$col]) ? $row.'_'.$col:null;
                }
            }
        }
        
        $wrongAnsIndices = array_filter($wrongAnsIndices);
        //echo count($savedAns);
        if (count($wrongAnsIndices) || count($givenAns) != count($savedAns)) {
            echo 3;
        } else {
            echo 2;
        }
    }

    public function indexQuesAns($items)
    {
        $arr = [];
        foreach ($items as $item) {
            $temp            = json_decode($item);
            if ($temp) {
                $cr              = explode('_', $temp->cr);
                $col             = $cr[0];
                $row             = $cr[1];
                $arr[$col][$row] = [
                    'type' => $temp->type,
                    'val'  => $temp->val,
                ];
            }
        }

        return $arr;
    }//end indexQuesAns()
    public function answer_times_table()
    {
        $PreviewClass=new \PreviewClass();
        $question_id = $this->request->getVar('question_id');
        $result = $this->request->getVar('result');
        
        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);
        
        $answer_info['student_ans'] = $result;
        
        $result_count = count(array_intersect($answer_info['student_ans'], $answer_info['tutor_ans']));
        
        if ($result_count == count($answer_info['tutor_ans']) && count($answer_info['student_ans']) == count($answer_info['tutor_ans'])) {
            echo 1;
        } else {
            echo 0;
        }
    }

   
    public function answer_algorithm()
    {
        $PreviewClass=new \PreviewClass();
        $question_id = $this->request->getVar('question_id');
        $result = $this->request->getVar('answer');

        $answer = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $question_info = json_decode($answer[0]['questionName'], true);
        $answer_info['tutor_ans'] = json_decode($answer[0]['answer']);


        $answer_info['student_ans'] = $result;

        if ($question_info['operator'] != '/' && $answer_info['student_ans'] == $answer_info['tutor_ans']) {
            echo 1;
        } elseif ($question_info['operator'] == '/') {// && $result[1] == $answer_info['tutor_ans']
            $stGivenQuotient = $result[0];
            $stGivenRemainder = $result[1];

            $recordedQuotient = $question_info['quotient'];
            $recordedRemainder = $answer_info['tutor_ans'];

            echo ($stGivenQuotient==$recordedQuotient) && ($stGivenRemainder==$recordedRemainder) ? 1 : 0;
        } else {
            echo 0;
        }
    }

    public function st_answer_matching_without_form_workout_two()
    {
        $PreviewClass=new \PreviewClass();
        $student_answer = $_POST['checkAllFiled'];
        $question_id = $_POST['question_id'];
        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $correct_ans = $answer_info[0]['answer'];
        $percentage_array = json_decode($answer_info[0]['questionName'])->percentage_array;
        $data['student_answer'] = $student_answer;
        $data['correct_ans'] = $correct_ans;
        $data['percentage_array'] = $percentage_array;
        $correct = 1;
        $i = 1;
        foreach ($student_answer as $ans) {
            $object = 'percentage_' . $i;
            if ($ans != $percentage_array->$object) {
                $correct = 0;
            }
            $i++;
        }

        if ($_POST['ansFiled'] != $correct_ans) {
            $correct = 0;
        }
        $data['correct'] = $correct;

        echo json_encode($data);
    }

    public function answer_matching_workout_two()
    {
        $PreviewClass=new \PreviewClass();
        $question_id = $_POST['question_id'];
        $answer_info = $PreviewClass->getInfo('tbl_question', 'id', $question_id);
        $answer_store = $answer_info[0]['answer'];

        
         echo 1;
       
    }

    public function preview_memorization_pattern_one_matching()
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
                $show_data_array[$key][0] = '';
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

    public function preview_memorization_pattern_one_ans_matching()
    {
        $TutorClass=new \TutorClass();
        $data = array();
        $question_id = $this->request->getVar('question_id');
        $word_matching = $this->request->getVar('word_matching');
        $submit_cycle = $this->request->getVar('submit_cycle');
        //echo $submit_cycle;
        //die();
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
                    if (preg_replace('/\s+/', '', strtolower($show_data))   == preg_replace('/\s+/', '', strtolower($word_matching_item)) )
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
                    $data_array[] =trim($left_memorize_p_one[$key]);
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
                if ( preg_replace('/\s+/', '', strtolower($left_memorize_p_one[$key])) == preg_replace('/\s+/', '', strtolower($word_matching[$key] )) )
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

    public function preview_memorization_pattern_one_ok()
    {
        $TutorClass=new \TutorClass();
        $data = array();
        $question_id = $this->request->getVar('question_id');
        $question_info = $TutorClass->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        $data['all_correct_ans'] =  $this->memorization_ans_data($question_name);
        echo json_encode($data);
    }

    public function preview_memorization_pattern_two_matching()
    {
        $TutorClass=new \TutorClass();
        $data = array();
        $question_id = $this->request->getVar('question_id');
        $question_info = $TutorClass->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        $left_memorize_p_one = $question_name->left_memorize_p_one;
        $first_alph = array();
        $col = array();
        $row = array();
        $i = 1;
        foreach ($left_memorize_p_one as $item)
        {
            $split_array = str_split(trim($item), 1);
            $col[] = count($split_array);
            $row[] = $i;
            $first_alph[] = $split_array[0];
            $i++;
        }
        $data['col'] = $col;
        $data['row'] = count($row);
        $data['first_alph'] = $first_alph;
        echo json_encode($data);
    }

    public function preview_memorization_pattern_two_ans_matching()
    {
        $TutorClass=new \TutorClass();
        $data = array();
        $question_id = $this->request->getVar('question_id');
        $submit_cycle = $this->request->getVar('submit_cycle');
        $left_memorize_p_one_alpha_ans = $this->request->getVar('left_memorize_p_one_alpha_ans');
        $question_info = $TutorClass->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        $left_memorize_p_one = $question_name->left_memorize_p_one;
        $left_memorize_p_one = array_map('strtolower', $left_memorize_p_one);
        $reply_ans = array();
        $reply_hints = array();
        $correct = 1;
        $correctAnswer = array();
        foreach ($left_memorize_p_one as $key=>$item)
        {
            if (isset($left_memorize_p_one_alpha_ans[$key]) && $left_memorize_p_one_alpha_ans[$key] != '')
            {
                if ( preg_replace('/\s+/', '', strtolower($item))  == preg_replace('/\s+/', '', strtolower($left_memorize_p_one_alpha_ans[$key])) )
                {
                    $reply_ans[$key][0] = $item;
                    $reply_ans[$key][1] = 1;
                    $correctAnswer[] = 1;
                }else{
                    $reply_ans[$key][0] = $left_memorize_p_one_alpha_ans[$key];
                    $reply_ans[$key][1] = 0;
                    $correct = 0;
                    $correctAnswer[] = 0;
                }
            }else
            {
                $reply_ans[$key][0] = $left_memorize_p_one_alpha_ans[$key];
                $reply_ans[$key][1] = 0;
                $correct = 0;
                $correctAnswer[] = 0;
            }

        }

        foreach($left_memorize_p_one as $key=>$item)
        {

            if ($reply_ans[$key][1] == 0)
            {
                $split_array = str_split(trim($item), 1);
                $countHints = count($split_array);

                $maxShow = $countHints -3;

                for($hints = 0;$hints<$countHints;$hints++)
                {
                    //if ($hints<$maxShow)
                    //{
                    if (isset($split_array[$hints]))
                    {
                        //$cycle = $submit_cycle;
                        $cycle = $submit_cycle;
                        if ( $hints <= $cycle)
                        {
                            $reply_hints[$key][0][] = $split_array[$hints];
                        }else{
                            $reply_hints[$key][0][] = '';
                        }
                    }
                    //}
                }
                $reply_hints[$key][1] = 1;
            }else{
                $split_array = str_split(trim($item), 1);
                $reply_hints[$key][0] = $split_array;
                $reply_hints[$key][1] = 0;
            }
        }

        if ($correct == 0)
        {
            $submit_cycle = $submit_cycle + 1;
        }
        $data['submit_cycle'] = $submit_cycle ;
        $data['correct'] = $correct ;
        $data['correctAnswer'] = $correctAnswer ;
        $data['reply_ans'] = $reply_ans;
        $data['reply_hints'] = $reply_hints;
        echo json_encode($data);
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

    public function preview_memorization_pattern_four_try()
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
        $left_memorize_p_four= $question_name->left_memorize_p_four;
        $left_memorize_p_four = array_map('strtolower', $left_memorize_p_four);

        $show_correct_ans = array();
        $show_error_ans = array();

            foreach ($correctAnswer as $key=>$item) {
                if ($item == 1)
                {
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
        // echo "<pre>";print_r($data);die();

        echo json_encode($data);
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

    // four patten
    public function preview_memorization_pattern_four_ans_matching()
    {
        $TutorClass=new \TutorClass();
        $data = array();
        $question_id = $this->request->getVar('question_id');
        $word_matching = $this->request->getVar('word_matching');
        $submit_cycle = $this->request->getVar('submit_cycle');
        //echo $submit_cycle;
        //die();
        // echo '<pre>';
        // print_r($this->input->post());
        // die();
        $pattern = $this->request->getVar('pattern');
        $question_info = $TutorClass->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        $show_data_array = array();
        $word_matching_answer = array();
        $all_correct_status = 1;

        $left_memorize_h_p_four = $question_name->left_memorize_h_p_four;
        $right_memorize_h_p_four = $question_name->right_memorize_h_p_four;

        $left_memorize_p_four = $question_name->left_memorize_p_four;
        $left_memorize_p_four = array_map('strtolower', $left_memorize_p_four);

        $right_memorize_p_four = $this->request->getVar('right_memorize_p_four');
        $right_memorize_p_four = array_map('strtolower', $right_memorize_p_four);
        $this->session->set('correct_answer', $right_memorize_p_four);
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
        // echo '<pre>';
        // print_r($show_data_array);
        // die();
            foreach($show_data_array as $key=>$show_data)
            {
                if ($show_data != '')
                {
                    $word_matching_item = $word_matching[$key];
                    if (preg_replace('/\s+/', '', strtolower($show_data))   == preg_replace('/\s+/', '', strtolower($word_matching_item)) )
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

            // echo '<pre>';
            // print_r($data);
            // die();
        }else{

            $word_matching = $this->request->getVar('word_matching');
            $show_data_array = array();
            $left_memorize_h_p_four = $question_name->left_memorize_h_p_four;
            $left_memorize_p_four = $question_name->left_memorize_p_four;
            $left_memorize_p_four = array_map('strtolower', $left_memorize_p_four);
            $correct_status = 1;
            $leftSileData = array();
            $word_matching_answer = array();
            foreach ($left_memorize_p_four as $key=>$item) {
                if ( preg_replace('/\s+/', '', strtolower($left_memorize_p_four[$key])) == preg_replace('/\s+/', '', strtolower($word_matching[$key] )) )
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

        $data['correct_answer'] =  $this->session->get['correct_answer'];
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

    public function getQuestionById($question_id)
    {
        $TutorClass=new \TutorClass();
        $question_info = $TutorClass->getInfo('tbl_question', 'id', $question_id);
        $question_name = json_decode($question_info[0]['questionName']);
        return $question_name;
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

    public function contentModify($data)
    {
        $modifyData = array();
        
        foreach($data as $key => $value)
        {
            $modifyData[$key]['left'] = $value[0];
            $modifyData[$key]['sl']   = $key+1;
        }

        shuffle($modifyData);
        //echo "<pre>";print_r($modifyData);die();
        return $modifyData;
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
            $TAns = str_replace(array(' ', "\n", "\t", "\r"), '', strip_tags($tutorAns[$key][0]));
//            $TAns = strtolower($TAns);
            $SAns = str_replace(array(' ', "\n", "\t", "\r"), '', $stdAns[$key]);
//            $SAns = strtolower($SAns);

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

    public function MemorizationAnswerMatchingTwo($left_memorize_p_two_ans,$right_memorize_p_two_ans){
        $tutorAns=[];
        $hiddenContent=[];
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
            $cycle='';
            $data['clue']= $this->clueArray($cycle,$word);
        }

        $data['matchingAnswer']=$matchingAnswer;
        $data['correct']=$correct;
        return $data;
        // echo "<pre>";print_r($right_result_val);die();
    }

    public function preview_memorization_pattern_two_try_again()
    {
        $data = array();
        $question_id = $this->request->getVar('question_id');
        $question_name = $this->getQuestionById($question_id);
        $left_memorize_p_two = $question_name->left_memorize_p_two;
        $right_memorize_p_two = $question_name->right_memorize_p_two;
        $pattern_two_hidden_ans_left = $this->request->getVar('pattern_two_hidden_ans_left');
        $pattern_two_hidden_ans_right = $this->request->getVar('pattern_two_hidden_ans_right');
        $pattern_two_hidden_ans_left = explode(",",$pattern_two_hidden_ans_left);
        $pattern_two_hidden_ans_right = explode(",",$pattern_two_hidden_ans_right);
        $stdAnsLeft = array();
        $stdAnsRight = array();
        $returnLeft = array();
        $returnRight = array();
        $countL = count($pattern_two_hidden_ans_left);
        $countR = count($pattern_two_hidden_ans_right);
        if ($countL >1)
        {
            for ($i = 1;$i<$countL;$i = $i+2)
            {
                $stdAnsLeft[] = $pattern_two_hidden_ans_left[$i];
            }
            foreach ($left_memorize_p_two as $key=>$item)
            {
                if ($stdAnsLeft[$key] == 0)
                {
                    $returnLeft[] = '';
                }else{
                    $returnLeft[] = $item[0];
                }
            }
        }
        if ($countR >1)
        {
            for ($i = 1;$i<$countR;$i = $i+2)
            {
                $stdAnsRight[] = $pattern_two_hidden_ans_right[$i];
            }
            foreach ($right_memorize_p_two as $key=>$item)
            {
                if ($stdAnsRight[$key] == 0)
                {
                    $returnRight[] = '';
                }else{
                    $returnRight[] = $item[0];
                }
            }
        }

        $firstleftSerial = $this->session->set('firstleftSerial');
        $returnLeft = array();
        foreach ($firstleftSerial as $lmpt=>$item)
        {
            $returnLeft[$lmpt]['left']  = $left_memorize_p_two[$item-1];
            $returnLeft[$lmpt]['right'] = $right_memorize_p_two[$item-1];
            $returnLeft[$lmpt]['result_status'] = $stdAnsRight[$lmpt];
            $returnLeft[$lmpt]['sl'] = $item;
        }

        shuffle($returnLeft);
        
        $data['returnLeft'] = $returnLeft;
        $data['returnRight'] = $returnRight;
        $data['stdAnsLeft'] = $stdAnsLeft;
        $data['stdAnsRight'] = $stdAnsRight;
        echo json_encode($data);
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

    public function preview_memorization_pattern_three_ans_matching()
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

   
}
