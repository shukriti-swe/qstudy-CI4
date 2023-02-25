<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SubjectController extends BaseController
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

    public function showAllSubject()
    {
        $FaqClass=new \FaqClass();
        $data['video_help'] = $FaqClass->videoSerialize(24,'video_helps'); //rakesh
        $data['video_help_serial'] = 24;

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['allSubs'] = $this->renderSubs();
        return view('subjects/all_subjects',$data);
    }

    public function renderSubs()
    {
        $SubjectClass=new \SubjectClass();
        $conditions = ['created_by'=>$this->loggedUserId];
        $allSubs = $SubjectClass->all($conditions);
        $html = '';
        foreach ($allSubs as $sub) {
            $allchaps = $SubjectClass->chaptersOfSubject([$sub['subject_id']]);

           $html .= '<h3>'.$sub['subject_name'].'<button class="subject-edit-btn subject_edit_btn" data-subjectid="'.$sub['subject_id'].'" data-subject_name="'.$sub['subject_name'].'"><i class="fa fa-pencil subject-edit-icon"></i>Edit</button>'.'<button style="float:right;" subId="'.$sub['subject_id'].'" class="btn btn-default delSubBtn"><i class="fa fa-times" ></i> Delete</button></h3>';
            //$html .= '<input type="hidden" id="subId" value="'.$sub['subject_id'].'">';
            $html .= '<div>';
            $html .= '<table class="table">';
            if (count($allchaps)) {
                $html .= '<thead style="background-color:#CACACA"><tr> <td>Chapter Name</td> <td>Action</td></tr> </thead>';
            }
            $html .= '<tbody>';
            
            foreach ($allchaps as $chap) {
                $html .= '<tr>';
                $html .= '<td>'.$chap['chapterName'].'</td>';
                $html .= '<td  id="'.$chap['id'].'"><i class="fa fa-times delChapIcon"></i></td>';
                $html .= '</tr>';
            }
            $html .='</tbody></table></div>';
        }
        return $html;
    }

    public function deleteChapter($chapterId)
    {
        $SubjectClass=new \SubjectClass();
        $chapterExists = $SubjectClass->search('tbl_chapter', ['id'=>$chapterId]);
        if (count($chapterExists)) {
            $SubjectClass->deleteChapter($chapterId);
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function deleteSubject($subjectId)
    {
        $SubjectClass=new \SubjectClass();
        $subjectExists = $SubjectClass->search('tbl_subject', ['subject_id'=>$subjectId]);
        if (count($subjectExists)) {
            $SubjectClass->deleteSubject($subjectId);
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function update_subject_name(){
        $SubjectClass=new \SubjectClass();
        $data = array();
        $data['subject_name'] = $this->request->getVar('subject-name');
        $subject_id = $this->request->getVar('subject-id');
        $result = $SubjectClass->updateSubject($data,$subject_id);
        echo json_encode('Subject Updated Successfully');
    }
}
