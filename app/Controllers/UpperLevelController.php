<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TblSetting;

class UpperLevelController extends BaseController
{
    public function index()
    {
        $UpperLevelClass=new  \UpperLevelClass();
        $AdminClass=new  \AdminClass();

        $data['user_info']=$UpperLevelClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
		$TblSetting=new TblSetting();
		
        $tbl_setting = $TblSetting->where('setting_key','days')->first();
 
        $duration = $tbl_setting->setting_value;
        $date = date('Y-m-d');
        $d1  = date('Y-m-d', strtotime('-'.$duration.' days', strtotime($date)));
        $trialEndDate = strtotime($d1);
        $inactive_user_info = $AdminClass->getInfoInactiveUserCheck('tbl_useraccount', 'subscription_type', 'trial',$trialEndDate,$this->session->get('user_id'));
        $data['inactive_user_check'] = count($inactive_user_info);
        
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('upper_level_students/upper_level_dashboard',$data);
    }
	
	   public function view_course()
    {
        error_report_check();
        $StudentClass=new  \StudentClass();
        $data['user_info'] = $StudentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));

        $parent_detail = getParentIDPaymetStatus($data['user_info'][0]['parent_id']);

        if ($parent_detail[0]['subscription_type'] == "direct_deposite") {
            if ($parent_detail[0]['direct_deposite'] == 0) {
                return redirect()->to(base_url($_SERVER['HTTP_REFERER']));
            }
        }

     return view('students/student_course/view_course',$data);
  }
}
