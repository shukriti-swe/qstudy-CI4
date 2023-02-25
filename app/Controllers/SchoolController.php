<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SchoolController extends BaseController
{
    public function index()
    {
        $FaqClass = new \FaqClass();
        $SchoolClass = new \SchoolClass();
        if ($this->session->get('userType') == 4) {
            $data['video_help'] = $FaqClass->videoSerialize(18, 'video_helps');
            $data['video_help_serial'] = 18;
        }

		$data['user_info']=$SchoolClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
		$data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return view('school/school_dashboard',$data);
    }

    public function school_setting(){

        $FaqClass = new \FaqClass();
        $SchoolClass = new \SchoolClass();
		$data['video_help'] = $FaqClass->videoSerialize(19, 'video_helps');
        $data['video_help_serial'] = 19;

		$data['user_info']=$SchoolClass->getInfo('tbl_useraccount','id',$this->session->get('user_id'));
		$data['page_title'] = '.:: Q-Study :: Tutor yourself...';       
        return view('school/school_setting', $data);
        
	}

    public function school_info_details(){
        $SchoolClass = new \SchoolClass();
		$data['user_info']=$SchoolClass->userInfo($this->session->get('user_id'));

		$data['tutor_info']=$SchoolClass->getTutorInfo($this->session->get('user_id'));
		//echo '<pre>';print_r($data['tutor_info']);die;
		$data['page_title'] = '.:: Q-Study :: Tutor yourself...';       
		return view('school/school_details',$data);
	}

    public function update_school_details() {
        $SchoolClass = new \SchoolClass();

        $update_password_array = $this->request->getVar('passwords');
        $update_name_array = $this->request->getVar('name');
        $update_teacher_id = $this->request->getVar('update_teacher_id');

        $insert_passwords_array = $this->request->getVar('insert_passwords');
        $insert_name_array = $this->request->getVar('insert_name');
        $SCT_link = $this->request->getVar('SCT_link');
        $country_id = $this->request->getVar('country_id');



        if (!empty($insert_name_array)) {
            $i = 0;
            foreach ($insert_name_array as $insertName) {
                $data['name'] = $insertName;
                $data['user_pawd'] = md5($insert_passwords_array[$i]);
                $data['user_email'] = $insertName;
                $data['SCT_link'] = $SCT_link;
                $data['country_id'] = $country_id;
				$data['user_type'] = 3;
                $data['created'] = time();
                $data['parent_id'] = $this->session->get('user_id');
                $SchoolClass->insertId('tbl_useraccount', $data);
                $i++;
            }
        }


        if (!empty($update_name_array)) {
            $j = 0;
            foreach ($update_name_array as $upName) {
                $user_pawd = md5($update_password_array[$j]);
                $teacher_own_id = $update_teacher_id [$j];
                $update_data = array(
                    'name' => $upName,
                    'user_email' => $upName,
                    'user_pawd' => $user_pawd
                );
                // $this->School_model->tutorUpdateInfo('tbl_useraccount', 'id', $teacher_own_id, $update_data);
                $SchoolClass->updateInfo('tbl_useraccount', 'id', $teacher_own_id, $update_data);

                $j++;
            }
        }
		 
        if ($this->request->getVar('password') != '') {
            if (!$this->validate('schollDetailsValidate')) 
            {
                echo 0;
            } else {
                $password = md5($this->request->getVar('password'));
                $data = array(
                    'user_pawd' => $password
                );
                $SchoolClass->updateInfo('tbl_useraccount', 'id', $this->session->get('user_id'), $data);
                echo 1;
            }
        }else{	
        	echo 1;
        }
    }

    public function school_logo(){
        $SchoolClass = new \SchoolClass();
		$data['user_info']=$SchoolClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
		$data['page_title'] = '.:: Q-Study :: Tutor yourself...'; 
        return view('school/upload',$data);
	}
    
    public function school_logo_upload()
	{
        $SchoolClass = new \SchoolClass();
		$profile_image = $this->request->getFile('file'); 

        if ($profile_image != '') {
            // if (file_exists('./admin/uploads/banner_section/' . $old_image)) {

            //     unlink('./admin/uploads/banner_section/' . $old_image);
            // }
            $profile_images_name = $profile_image->getRandomName();
            $image=$profile_image->move(ROOTPATH . 'public/assets/uploads', $profile_images_name);
            if($image)
            {
                $school_profile_picture=$profile_images_name ;
                $data = array(
                    'image' =>$school_profile_picture
                );
                $rs['res']=$SchoolClass->updateInfo('tbl_useraccount','id',$this->session->get('user_id'),$data);
                echo 1;
            }   
            else
            {
                echo 0;  
            } 
        }
	
	}
}
