<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class StudentProgress extends BaseController
{
    public function __construct()
    {
        $this->session=session();
        $user_id = $this->session->get('user_id');
        $user_type = $this->session->get('userType');
        $this->loggedUserId = $this->session->get('user_id');
        $this->loggedUserType = $this->session->get('userType');
        
        $PreviewClass=new \PreviewClass();
        $user_info = $PreviewClass->userInfo($user_id);
		
        if ($user_info[0]['countryCode'] == 'any') {
            $user_info[0]['zone_name'] = 'Australia/Lord_Howe';
        }

        $this->site_user_data = array(
            'userType' => $user_type,
            'zone_name' => $user_info[0]['zone_name']
        );
    }

    public function viewStudentProgress($id ='')
    {
        $StudentClass=new \StudentClass();
        $FaqClass=new \FaqClass();

        $data['video_help'] = $FaqClass->videoSerialize(14, 'video_helps');
        $data['video_help_serial'] = 14;

        if ($id == 7)
        {
            return redirect('student_progress_step_7');
        }

        $data = $this->commonPart();

        if ($this->loggedUserType==2 || $this->loggedUserType==6) { //upper,lower student will see their progress only
            $data['isStudent'] = $this->loggedUserId;
            $data['studentName'] = $StudentClass->studentName($this->loggedUserId);
            $data['studentClass'] = $StudentClass->studentClass($this->loggedUserId);
        }

        // $conditions = array(
            // 'sct_id'=> $this->loggedUserId,
        // );
        
        $sct_id = $this->loggedUserId;
        $country_id = '';
        
        if ($this->loggedUserType == 7) {
            $data['all_country'] = $StudentClass->getAllInfo('tbl_country');
        }

        $studentIds = $StudentClass->allStudents($sct_id, $country_id);
        $data['students'] = $this->renderStudents($studentIds);
        // echo $data['students'];die();
        //   $data['moduleTypes'] = $this->renderModuletypes($this->ModuleModel->allModuleType());
        if ($this->loggedUserType != 7)
        {
            $data['moduleTypes']   = $this->renderAllModuleTypeAllUser();
        }else
        { 
            $data['moduleTypes']   = $this->renderAllModuleType();
        }
        $data['module_user_type'] = $id;
        return view('students/student_progress',$data);

    }

    public function commonPart()
    {
        $FaqClass=new \FaqClass();
        $StudentClass=new \StudentClass();
        $TutorClass=new \TutorClass();

        $data['video_help'] = $FaqClass->videoSerialize(14, 'video_helps');
        $data['video_help_serial'] = 14;
        
        $user_id = $this->loggedUserId;
        //$data['user_info'] = $this->tutor_model->userInfo($user_id);
        $data['user_info']    = $StudentClass->userInfo($this->session->get('user_id'));

        $data['all_module'] = $TutorClass->getInfo('tbl_module', 'user_id', $user_id);
        $data['all_grade'] = $TutorClass->getAllInfo('tbl_studentgrade');
        $data['all_module_type'] = $TutorClass->getAllInfo('tbl_moduletype');
        $data['all_course'] = $TutorClass->getAllInfo('tbl_course');

        return $data;
    }

    public function renderStudents($studentIds)
    {
        $StudentClass=new \StudentClass();
        $options='';
        if (count($studentIds)) {
            foreach ($studentIds as $studentId) {
                $student = $StudentClass->userInfo($studentId);
                $student = $student[0];
                $options .= '<option value="'.$studentId.'">'.$student['name'].'</option>';
            }
        }
        return $options;
    }

    public function renderAllModuleTypeAllUser($selectedId = -1)
    {
        $ModuleClass=new \ModuleClass();
        $option      = '';
        $option     .= '<option value="">--Moduletype--</option>';
        $moduleTypes = $ModuleClass->allModuleType(array('condition'=>'module_type !=', 'value'=>'Sliding'));

        foreach ($moduleTypes as $moduleType) {
            $sel     = ($moduleType['id'] == $selectedId) ? 'selected' : '';
            $option .= '<option value="'.$moduleType['id'].'" '.$sel.'>'.$moduleType['module_type'].'</option>';
        }

        return $option;
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


    public function studentByClass()
    {
        $StudentClass=new \StudentClass();
        /*$post = $this->input->post();
        $class = isset($post['stClass']) ? $post['stClass'] : 0;

        $get_user_info = $this->Parent_model->userInfo($this->loggedUserId);

        if ($get_user_info[0]['user_type'] == 1) { // parent
            $get_all_child =  $this->Parent_model->get_all_child($get_user_info[0]['id'], $class);
            $options = '';
            foreach ($get_all_child as $row) {
                $options .= '<option value="'.$row['id'].'">'. $row['name'] .'</option>';
            }
        }

        if ($get_user_info[0]['user_type'] == 3 || $get_user_info[0]['user_type'] == 4 || $get_user_info[0]['user_type'] == 5) { //3=tutor, 4=school, 5=corporate,
            $studentsByClass = array_column($this->Student_model->studentByClass($class), 'student_id');
            $loggedUserStudents =  $this->Student_model->allStudents(['sct_id'=>$this->loggedUserId]);

            $students = array_intersect($studentsByClass, $loggedUserStudents);
            $options = '';
            foreach ($students as $student) {
                $studentName = $this->Student_model->studentName($student);
                $options .= '<option value="'.$student.'">'. $studentName .'</option>';
            }
        }

        echo $options;*/

        $post = $this->request->getVar();
        $country_id = $post['country'];
        $sct_id = $this->loggedUserId;
        
        $class = isset($post['stClass']) ? $post['stClass'] : 0;
        $studentsByClass = array_column($StudentClass->studentByClass($class), 'id');
        
        $loggedUserStudents = $StudentClass->allStudents($sct_id, $country_id);
//        $loggedUserStudents =  $this->Student_model->allStudents([
//                                                        'sct_id'=>$this->loggedUserId,
//                                                        'country_id' => $country_id
//                                                    ]);
        
        $students = array_intersect($studentsByClass, $loggedUserStudents);
        $options = '';
        foreach ($students as $student) {
            $studentName = $StudentClass->studentName($student);
            $options .= '<option value="'.$student.'">'. $studentName .'</option>';
        }
        echo $options;
    }


    public function StProgTableDataStd()
    {
        $StudentClass=new \StudentClass();
        $post = $this->request->getVar();
        $module_user_type = '';
        $course_id = '';

        if (isset($post['studentId'])) {
            $conditions['student_id'] = $post['studentId'];
        }
        if (isset($post['moduleTypeId'])) {
            $conditions['moduletype'] = $post['moduleTypeId'];
        }
        if (isset($post['module_user_type'])) {
            $module_user_type = $post['module_user_type'];
        }
        if (isset($post['course_id'])) {
            $course_id = $post['course_id'];
        }
        $allProgress= $StudentClass->studentProgressStd($conditions,$module_user_type,$course_id);

        $data['st_progress'] = $this->renderStProgress($allProgress);
        echo $data['st_progress'];
    }
    
    public function renderStProgress($items)
    {
        $ModuleClass=new \ModuleClass();
        $row = '';
        date_default_timezone_set($this->site_user_data['zone_name']);
        $examAttended = count($items);
        $totPercentage = 0;
		$row .='<table class="table table-bordered" id="st_progress_table">';
          $row .='<thead>';
          $row .='<tr>';
          $row .='<th style="width:90px;">Module Name</th>';
          $row .='<th>Module Type</th>';
          $row .='<th style="width:90px;">Answer Date</th>';
          $row .='<th>Answer Time</th>';
          $row .='<th>Time Taken</th>';
          $row .='<th>Original Mark</th>';
          $row .='<th>Student Mark</th>';
          $row .='<th>Percentage</th>';
        if ($this->loggedUserType == 3 || $this->loggedUserType == 7) {
            $row .= '<th>Action</th>';
        }
          $row .='</tr>';
          $row .='</thead>';
          $row .='<tbody id="stProgTableBody">';
        foreach ($items as $item) {
            $v_hours = floor($item['timeTaken'] / 3600);
            $remain_seconds = $item['timeTaken'] - ($v_hours * 3600);
            $v_minutes = floor($remain_seconds / 60);
            $v_seconds = $remain_seconds - $v_minutes * 60;
            $time_hour_minute_sec = $v_hours . " : "  . $v_minutes . " : " . $v_seconds ;
            if ($item['studentMark'] == 0 )
            {
                $percentGained = 0;
            }else
            {
                $percentGained = @(float)($item['studentMark']/$item['originalMark'])*100;
            }if ($item['originalMark'] == 0 )
            {
                $percentGained = 0;
            }else
            {
                $percentGained = @(float)($item['studentMark']/$item['originalMark'])*100;
            }
            $percentGained = round($percentGained, 2);
            $totPercentage += $percentGained;
            $row .= '<tr progId="'.$item['id'].'">';
            $row .= '<td>
                        <a href="check_student_copy/'.$item['module'].'/'.$item['student_id'].'/1/'.$item['id'].'">'.$ModuleClass->moduleName($item['module']).'</a>
                    </td>';
            $row .= '<td>'.$ModuleClass->moduleTypeName($item['moduletype']).'</td>';
            $row .= '<td data-order="'.$item['answerTime'].'">'.date('d M Y', $item['answerTime']).'</td>';
            $row .= '<td>'.date('h:i A', $item['answerTime']).'</td>';
            $row .= '<td>'.$time_hour_minute_sec.'</td>';
            $row .= '<td>'.$item['originalMark'].'</td>';
            $row .= '<td>'.$item['studentMark'].'</td>';
            //$row .= '<td>'.round($item['percentage'], 2).'</td>';
            $row .= '<td>'.$percentGained.'%</td>';
            if ($this->loggedUserType == 3 || $this->loggedUserType == 7) {
                $row .= '<td style="">
                            <i class="fa fa-close" onclick="delete_progress('.$item['id'].')"></i>
							<i class="fa fa-plus addMarks" data-toggle="tooltip" data-placement="top" title="Add Marks"></i>
						</td>';
            }
            $row .= '</tr>';
        }
        $avg = ($examAttended>0) ? round((float)($totPercentage / $examAttended), 2) : 0.0;
        //$row .= '<tr> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td style="background-color:#99D9EA">Total average mark</td> <td style="background-color:#99D9EA">'.$avg.'</td> <td></td></tr>';
        if ($this->loggedUserType == 3 || $this->loggedUserType == 7) {
            $row .= '<tr> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td style="background-color:#99D9EA">Total average mark</td> <td style="background-color:#99D9EA">' . $avg . '%</td> <td></td></tr>';
        }else{
            $row .= '<tr> <td></td> <td></td>  <td></td> <td></td> <td></td> <td style="background-color:#99D9EA">Total average mark</td> <td style="background-color:#99D9EA">' . $avg . '%</td> <td></td></tr>';
        }
		 $row .= '</tbody>';
        $row .= '</table>';
		return strlen($row) ? $row : 'No data found';
    }

    public function student_progress_course($course_id)
    {
        $StudentClass=new \StudentClass();
        $data = $this->commonPart();

        if ($this->loggedUserType==2 || $this->loggedUserType==6) { //upper,lower student will see their progress only
            $data['isStudent'] = $this->loggedUserId;
            $data['studentName'] = $StudentClass->studentName($this->loggedUserId);
            $data['studentClass'] = $StudentClass->studentClass($this->loggedUserId);
        }
        $sct_id = $this->loggedUserId;
        $country_id = '';

        if ($this->loggedUserType == 7) {
            $data['all_country'] = $StudentClass->getAllInfo('tbl_country');
        }

        $studentIds = $StudentClass->allStudents($sct_id, $country_id);
        $data['students'] = $this->renderStudents($studentIds);
//        $data['moduleTypes'] = $this->renderModuletypes($this->ModuleModel->allModuleType());
        if ($this->loggedUserType != 7)
        {
            $data['moduleTypes']   = $this->renderAllModuleTypeAllUser();
        }else
        {
            $data['moduleTypes']   = $this->renderAllModuleType();
        }
        $data['course_id'] = $course_id;
        $data['has_back_button'] = base_url().'/student_progress_step_7';

        
        return view('students/student_progress',$data);
    }

    public function addMarks()
    {
        $TutorClass=new \TutorClass();

        $post = $this->request->getVar();
        $marksToAdd = $post['marksToAdd'];
        $progressId = $post['progressId'];

        $progData = $TutorClass->getInfo('tbl_studentprogress', 'id', $progressId);
        $currentMarks = $progData[0]['studentMark'];
        $updatedMarks =  ($currentMarks + $marksToAdd);

        $TutorClass->updateInfo('tbl_studentprogress', 'id', $progressId, ['studentMark'=>$updatedMarks]);

        echo '1';
    }

    public function delete_progress()
    {
        $TutorClass=new \TutorClass();
        $StudentClass=new \StudentClass();

        $progress_id = $this->request->getVar('progress_id');
        $TutorClass->deleteInfo('tbl_studentprogress', 'id', $progress_id);
        
        $post = $this->request->getVar();
        if (isset($post['studentId'])) {
            $conditions['student_id'] = $post['studentId'];
        }if (isset($post['moduleTypeId'])) {
            $conditions['moduletype'] = $post['moduleTypeId'];
        }
        $allProgress=$StudentClass->studentProgress($conditions);
        $data['st_progress'] = $this->renderStProgress($allProgress);
        echo $data['st_progress'];
    }
	
	public function viewTutorStudentProgress()
    {
        $StudentClass=new \StudentClass();
        $sct_id = $this->loggedUserId;
        $country_id = '';
        $studentIds = $StudentClass->allStudents($sct_id,$country_id);
        $data['registered_courses'] = $StudentClass->get_register_courses($studentIds);
        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount','id', $this->session->get('user_id'));

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return view('students/student_progress_step_qstudy', $data);

    }
	
	public function TutorStProgTableDataStd()
    {
        $StudentClass=new \StudentClass();
        $post = $this->request->getVar();
        $module_user_type = '';
        $course_id = '';
        if (isset($post['studentId'])) {
            $conditions['student_id'] = $post['studentId'];
        }if (isset($post['moduleTypeId'])) {
            $conditions['moduletype'] = $post['moduleTypeId'];
        }
        if (isset($post['module_user_type'])) {
            $module_user_type = $post['module_user_type'];
        }
        if (isset($post['course_id'])) {
            $course_id = $post['course_id'];
        }
        $allProgress=$StudentClass->TutorStudentProgressStd($conditions,$module_user_type,$course_id);
        $data['st_progress'] = $this->renderStProgress($allProgress);
        echo $data['st_progress'];
    }
}
