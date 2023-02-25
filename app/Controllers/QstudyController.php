<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class QstudyController extends BaseController
{
    public function index()
    {
		$FaqClass = new \FaqClass();
        //qstudy--------------------
        $builder =$this->db->table('tbl_useraccount');     
        $builder->where('id',$this->session->get('user_id'));
        $query_result = $builder->get();
        $data['user_info'] = $query_result->getRow();
 		$data['video_help'] = $FaqClass->videoSerialize(1,'video_helps');
		$data['video_help_serial'] = 1;
        $userType = $_SESSION['userType'];
        //echo $userType;die();
        if($userType==0){
            return redirect()->to(base_url('admin'));
        }else if($userType==1){
            return redirect()->to(base_url('parents'));
        }else if($userType==2){
            return redirect()->to(base_url('upper_level_users'));
        }else if($userType==3){
            return redirect()->to(base_url('tutor'));
        }else if($userType==4){
            return redirect()->to(base_url('schoolList'));
        }else if($userType==5){
            return redirect()->to(base_url('corporateList'));
        }else if($userType==6){
            return redirect()->to(base_url('student'));
        }else if($userType==7){
            return view('qstudy/qstudy_dashboard',$data);
        }
        
    }

    public function courseCountrySelect()
    {
      
        $AdminClass = new \AdminClass();
        $this->session->remove('modInfo');
        $this->session->remove('selCountry');

        $data['countries'] = $this->db->table('tbl_country')->get()->getResultArray();
        
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
   
        return view('qstudy/course_country', $data);
    }

    public function view_course()
    {
        $AdminClass = new \AdminClass();
        $QstudyClass= new \QstudyClass();

        if(isset($_SESSION['list_submit']) && $_SESSION['list_submit'] == 1)
        {
            unset($_SESSION['list_submit']);
        }
        $data['countryScope'] = isset($_GET['country']) ? '?country='.$_GET['country'] : '';
        if (isset($_GET['country'])) {
            $this->session->set('selCountry', $_GET['country']);
            $countries = $AdminClass->getCountry($_GET['country']);
            
            if($_GET['country']==1){
                $this->session->set('setCountryName','Australia');
            }else{
                $this->session->set('setCountryName',$countries['countryName']);
            }
            
        }
        $data['user_info'] = $QstudyClass->userInfo($this->session->get('user_id'));
        if(!empty($data['user_info'])){
            $data['user_type'] = $data['user_info'][0]['user_type'];

            return view('tutor/view_course', $data);
        }else{
            return redirect()->to(base_url('/'));
        }
		
    }

}
