<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ParentsController extends BaseController
{
    public function index()
    {
        $ParentClass=new  \ParentClass();

        $data['user_info']=$ParentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
		$data['page_title'] = '.:: Q-Study :: Tutor yourself...';
		
        return view('parent/parent_dashboard',$data);  
    }

    public function parent_setting()
    {
        $ParentClass=new \ParentClass();
        $data['user_info']=$ParentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
		if ($data['user_info'][0]['subscription_type'] == "direct_deposite" && $data['user_info'][0]['direct_deposite'] == 0 ) {
          	redirect($_SERVER['HTTP_REFERER']);
          }  
		$data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('parent/parent_setting',$data);
    }

    public function my_details()
    {
        
        $ParentClass=new \ParentClass();

        $data['user_info']=$ParentClass->userInfo($this->session->get('user_id'));
		$data['user_child_info']=$ParentClass->userChildInfo($this->session->get('user_id'));
		$data['total_child']=count($data['user_child_info']);
	   // echo "<pre>"; print_r($data);die;

        return view('parent/my_details',$data);
    }

    public function upload_photo()
	{
        $ParentClass=new \ParentClass();

		$data['user_info']=$ParentClass->getInfo('tbl_useraccount', 'id', $this->session->get('user_id'));
		$data['page_title'] = '.:: Q-Study :: Tutor yourself...';
		// $data['headerlink'] = $this->load->view('dashboard_template/headerlink', $data, true);
        // $data['header'] = $this->load->view('dashboard_template/header', $data, true);
        // $data['footerlink'] = $this->load->view('dashboard_template/footerlink', $data, true);        
        // $data['maincontent'] = $this->load->view('parents/upload', $data, TRUE);
        // $this->load->view('master_dashboard', $data);
        return view('parent/upload',$data);
	}

    public function update_my_details()
	{
        $ParentClass=new \ParentClass();
        $RegisterClass=new  \RegisterClass();
        $AdminClass=new  \AdminClass();

        $input = $this->validate([
            'password' => 'trim|required|max_length[6]|min_length[5]',
            'passconf' => 'trim|required|matches[password]'
        ]);
		if(!$input)
		{
			echo 0;
		}else{
			$password=md5($this->request->getVar('password'));
			$data = array(
					'user_pawd' =>$password
			);
			$ParentClass->updateInfo('tbl_useraccount','id',$this->session->get('user_id'),$data);
			$newChild = $this->request->getVar('childName');
			$childgrade = $this->request->getVar('childgrade');
			if($newChild != null){
                $st['name'] = $newChild;
                $pieces = explode(" ", $st['name']);
                $random_number = rand(100, 999);
                $st['user_email']=$pieces[0];
                $raw_st_data['st_name']=$pieces[0];
                $raw_st_data['st_password']=$pieces[0].$random_number;
                $st['user_pawd']=md5($pieces[0].$random_number);

                $user_pswd[] = ($pieces[0].$random_number);
                $this->session->set('st_password', $user_pswd);
                $st['parent_id']=$this->session->get('user_id');
                $st['user_type']=6;
                $st['country_id']=$this->request->getVar('country_id');
                $st['subscription_type']='trial';

                $st['student_grade']=$childgrade;
                $st['created']=time();
                $this->load->helper('string');
                $st['SCT_link'] = random_string('alnum', 10);
                $student_id = $RegisterClass->basicInsert('tbl_useraccount', $st);
                
                //send details
                $settins_sms_status   = $AdminClass->getSmsType("Template Activate Status");

                if ($settins_sms_status[0]['setting_value'] ) {
                    
                    $settins_Api_key = $AdminClass->getSmsApiKeySettings();
                    $settins_sms_messsage = $AdminClass->getSmsType("Parent Registration");
                    
    
                    $register_code_string = $settins_sms_messsage[0]['setting_value'];
                    $message = str_replace( "{{ username }}" , $this->session->get('email') , $register_code_string);
                    $message = str_replace( "{{ password }}" , $this->session->get('password') , $message);
                    $message = str_replace( "{{ C_username }}" , $st['user_email'] , $message);
                    $message = str_replace( "{{ C_password }}" , $pieces[0].$random_number , $message);
    
                    $api_key = $settins_Api_key[0]['setting_value'];
                    $content = urlencode($message);
    
                    $url = "https://platform.clickatell.com/messages/http/send?apiKey=$api_key&to=" . $this->session->get('mobile') . "&content=$content";
    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
                    curl_setopt($ch, CURLOPT_VERBOSE, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    //execute post
                    $result = curl_exec($ch);
    
                    
                    curl_close($ch);
                    // print_r($result);die;
                    $send_msg_status = json_decode($result);
    
                }
                echo 2;
			}else{
			    echo 1;
			    
			}
		}
	}

    // private function upload_user_photo_options()
	// {
	// 	$config = array();
	// 	$config['upload_path'] = './assets/uploads/';
	// 	$config['allowed_types'] = 'gif|jpg|png';
	// 	// $config['max_width'] = 1080;
	// 	// $config['max_height'] = 640;
	// 	// $config['min_width']  = 150;
	// 	// $config['min_height'] = 150;
	// 	$config['overwrite']  = FALSE;
	// 	return $config;
	// }

    public function parent_dropzone_file()
	{
        $ParentClass=new \ParentClass();
		$profile_image = $this->request->getFile('file');  
    
        if ($profile_image != '') {
            // if (file_exists('./admin/uploads/banner_section/' . $old_image)) {

            //     unlink('./admin/uploads/banner_section/' . $old_image);
            // }
            $profile_images = $profile_image->getRandomName();
            $image=$profile_image->move(ROOTPATH . 'public/assets/uploads', $profile_images);
            if($image)
            {
                $user_profile_picture=$profile_images ;
                $data = array(
                    'image' =>$user_profile_picture
                );
                $rs['res']=$ParentClass->updateInfo('tbl_useraccount','id',$this->session->get('user_id'),$data);
                echo 1;
            }   
            else
            {
                echo 0;  
            } 
        }
	}
	
	
 public function cancel_subscription()
    {
        if ($this->request->isAJAX()) {
           $id=$this->request->getVar('student_id'); 
           $student_data=$this->db->table('tbl_useraccount')->where('id',$id)->get()->getResultArray();
           if($student_data[0]['subscription_type'] == 'trial')
           {
                echo 'trail';
           }
           else
           {
               
                $data['user_id'] = $student_data[0]['id'];
                $data['end_subscription'] = $student_data[0]['end_subscription'];
                $data['cancel_date'] = date('Y-m-d');
        
                $check_user = $this->db->table('tbl_cancel_subscription')->where('user_id',$student_data[0]['id'])->get()->getResultArray();
                if (count($check_user) == 0) {
                    $result = $this->db->table('tbl_cancel_subscription')->insert($data);
                
                }else{
                    $result = $this->db->table('tbl_cancel_subscription')->update($data);
        
                }
        
                if($result){
                    $this->db->table('tbl_useraccount')->where('id',$student_data[0]['id'])->update(['payment_status'=>'Cancel']);
                    if ($student_data[0]['user_type'] == 6) {
                        $this->db->table('tbl_useraccount')->where('id',$this->session->get('user_id'))->update(['payment_status'=>'Cancel']);
                    }
                }
                echo "success"; 
           }

        }
        else
        {
            $parent_id=$this->session->get('user_id');
            $data['parent_child']=$this->db->table('tbl_useraccount')->where('parent_id',$parent_id)->get()->getResultArray();
            
            return view('parent/cancel_subscription',$data); 
        }
    }
}
