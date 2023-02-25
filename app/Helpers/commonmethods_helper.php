<?php

if (!function_exists("trailPeriod")) {


    function trailPeriod(){
        // $ci =& get_instance();
        // $ci->load->model('Parent_model');
    
        // $user_info = $ci->Parent_model->getInfo('tbl_setting', 'setting_id', 15 );
        $db = \Config\Database::connect();

        $builder =$db->table('tbl_setting');     
        $builder->where('setting_id',15);
        $query_result = $builder->get();
        $data=$query_result->getResultArray();

        return $data;
    }
}
// if (!function_exists("getParentIDPaymetStatus")) {


//     function getParentIDPaymetStatus($id){
//         // $ci =& get_instance();
//         // $ci->load->model('Parent_model');
    
//         // $user_info = $ci->Parent_model->getInfo('tbl_setting', 'setting_id', 15 );
//         $db = \Config\Database::connect();
//         $StudentClass = new \StudentClass();

//         $user_info = $StudentClass->getInfo('tbl_useraccount', 'id', $id );

//         return $user_info;
//     }
// }

if (!function_exists("sendMail")) {


    function sendMail($mail_data)
    {
        //echo 'sendMail';die();
        $email = \Config\Services::email();
        // echo '<pre>';
        // print_r($mail_data);die;
        $mailTo        =   $mail_data['to'];
        $mailSubject   =   $mail_data['subject'];
        if($mail_data['message'])
        {
           $message       =   $mail_data['message'];
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
} 

if(!function_exists("getTrailDate"))
{
    function getTrailDate($date,$this_db){

        $TblSetting = new App\Models\TblSetting();

        $tbl_setting = $TblSetting->where('setting_key','days')->first();
        // echo '<pre>';
        // print_r($tbl_setting );
        // die();
        $duration = $tbl_setting->setting_value;
        $trail_start_date = date('Y-m-d',$date);
        $trail_end_date  = date('Y-m-d', strtotime('+'.$duration.' days', strtotime($trail_start_date)));
        $today = date('Y-m-d');
        //$trail_days = $trail_end_date - $trail_start_date;
        $diff = strtotime($trail_end_date) - strtotime($today);
        $days = $diff/(60*60*24);
        // $diff = abs(strtotime($trail_end_date) - strtotime($today));
        // $years = floor($diff / (365*60*60*24));
        // $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        // $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        return $days;
    
    }
}
if(!function_exists("getParentIDPaymetStatus"))
{
    function getParentIDPaymetStatus($id){
        $ParentClass=new \ParentClass();
        $user_info = $ParentClass->getInfo('tbl_useraccount', 'id', $id );
        return $user_info;
    }
}

if(!function_exists("error_report_check"))
{
    function error_report_check(){
        return error_reporting(0);
    }
}

if(!function_exists("userRegMail"))
{
    function userRegMail($userName, $userType, $email_new, $password, $additionalData = [])
    {
        $email = \Config\Services::email();
        $Name=$userName;
        $email_to=$email_new;
        // echo "<pre>";
        // print_r($email);
        // die();
        $Password=$password;
        //$template = $ci->RegisterModel->getInfo('table_email_template', 'email_template_type', 9999);
        $data['userType'] = $userType;
        
        //if user is a parent mail him/her child acc info too
        if (($userType==1 || $userType==4) && count($additionalData)) {//parent, school
            $data['childInfo'] = $additionalData;
        }

        $template=require_once(APPPATH.'views/email_templates/user_registration.php');

        //$template = $ci->load->view('email_templates/user_registration', $data, true);
        if ($template) {
            $subject = 'Q-study registration';//$template[0]['email_template_subject'];
            $template_message = $template;//$template[0]['email_template'];
            
            $find = array("{{userName}}","{{userEmail}}","{{userPassword}}");
            $replace = array($Name,$email_to,$Password);
            $message = str_replace($find, $replace, $template_message);
         

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
    }
}

if(!function_exists("get_question_tutorial"))
{
    function get_question_tutorial($question_id)
    {
        $TutorClass=new \TutorClass();
        $tutorialInfo = $TutorClass->getInfo('tbl_question_tutorial', 'question_id', $question_id);
    
        if (!empty($tutorialInfo))
        {
            return true;
        }else
        {
            return false;
        }
    }
}


if(!function_exists("indexQuesAns"))
{
    function indexQuesAns(array $items)
    {

        $arr = [];
        
        foreach ($items as $item) {
            $temp            = json_decode($item);
            $cr              = explode('_', $temp->cr);
            $col             = $cr[0];
            $row             = $cr[1];
            $arr[$col][$row] = [
                'type' => $temp->type,
                'val'  => $temp->val,
            ];
        }

        return $arr;
    }//end indexQuesAns()
}

if(!function_exists("renderSkpQuizPrevTable"))
{
   function renderSkpQuizPrevTable($items, $rows, $cols, $showAns = 0)
    {

        $row = '';
        for ($i=1; $i<=$rows; $i++) {
            $row .='<tr>';
            for ($j=1; $j<=$cols; $j++) {
                if ($items[$i][$j]['type']=='q') {
                    $row .= '<td><input type="button" data_q_type="0" data_num_colofrow="" value="'.$items[$i][$j]['val'].'" name="skip_counting[]" class="form-control input-box  rsskpinpt'.$i.'_'.$j.'" readonly style="min-width:50px; max-width:50px; background-color: rgb(255, 183, 197);"></td>';
                } else {
                    $ansObj = array(
                        'cr'=>$i.'_'.$j,
                        'val'=> $items[$i][$j]['val'],
                        'type'=> 'a',
                    );
                    $ansObj = json_encode($ansObj);
                    $val = ($showAns==1)?' value="'.$items[$i][$j]['val'].'"' : '';
                    
                    $row .= '<td><input autocomplete="off" type="text" '.$val.' data_q_type="0" data_num_colofrow="'.$i.'_'.$j.'" value="" name="skip_counting[]" class="form-control input-box ans_input  rsskpinpt'.$i.'_'.$j.'" readonly style="min-width:50px; max-width:50px;background-color: rgb(186, 255, 186); ">';
                    $row .= '<input type="hidden" value="" name="given_ans[]" id="given_ans">';
                    $row .='</td>';
                }
            }
            $row .= '</tr>';
        }
        
        return $row;
    }

}

if(!function_exists("get_question_tutorial"))
{
    function get_question_tutorial($question_id)
    {
        $TutorClass=new \TutorClass();
        $tutorialInfo = $TutorClass->getInfo('tbl_question_tutorial', 'question_id', $question_id);

        if (!empty($tutorialInfo))
        {
            return true;
        }else
        {
            return false;
        }
    }
}

if (!function_exists("sendMailAttachment")) {

    function sendMailAttachment($mail_data,$attachmants,$id)
    {
        $email = \Config\Services::email();

        $mailTo        =   $mail_data['to'];
        $mailSubject   =   $mail_data['subject'];
        $message       =   $mail_data['message'];
            
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
        
        $folder = 'assets/uploads/feedback/';
        $path = FCPATH.$folder;
        foreach ($attachmants as $key => $value) {
            $file = $value['filename'];
            $email->attach($path.$file);
        }
        $email->send();
    
        if (!$email->send()) {
            return $email->printDebugger();
        } else {
            return true;
        }
        
    }

}

if (!function_exists("renderAssignmentTasks")) {
function renderAssignmentTasks(array $items, $pageType = '')
{
    $row = '';
    foreach ($items as $task) {
        $task     = json_decode($task);
        $qMark    = $task->qMark;
        $obtnMark = '';//$task->obtnMark;
        if ($pageType == 'edit') {
            $qMark    = '<input name="qMark[]" class="form-control" type="text" value="'.$qMark.'"'.' type="number" step="0.1" required>';
            $obtnMark = '<input name="obtnMark[]" class="form-control" type="text" value="'.$obtnMark.'"'.'type="number" step="0.1" required>';
        }

        $row .= '<tr id="'.($task->serial + 1).'">';
        $row .= '<td>'.($task->serial + 1).'</td>';
        $row .= '<td>'.$qMark.'</td>';
        $row .= '<td>'.$obtnMark.'</td>';
        $row .= '<td><i class="fa fa-eye qDtlsOpenModIcon" data-toggle="modal" data-target="#quesDtlsModal"></i></td>';
        $row .= '<input name="descriptions[]" type="hidden" id="hiddenTaskDesc" value="'.$task->description.'">';
        $row .= '</tr>';
    }

    return $row;
}
}