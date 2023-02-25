<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
       
        $user=$this->session->get('userType');
        $user_id=$this->session->get('user_id');

        if ($user == 1) {
            //echo 'hiii';die();
            return redirect()->to(base_url('/parents'));
        }
        if ($user == 2) {
            return redirect()->to(base_url('/upper_level'));
        }
        if ($user == 3) {
            return redirect()->to(base_url('/tutor'));
        }
        if ($user == 4) {
            return redirect()->to(base_url('/school'));
        }
        if ($user == 5) {
            return redirect()->to(base_url('/corporate'));
        }
        if ($user == 6) {
            return redirect()->to(base_url('/student'));
        }
        if ($user == 7) {

           return redirect()->to(base_url('/qstudy'));
     
        }
        if ($user == 0) {
            return redirect()->to(base_url('/admin'));
        }
    }

    public function view_course()
    {
        //echo $this->session->get('userType');die();
        if ($this->session->get('userType') == 3 ||
                $this->session->get('userType') == 4 ||
                $this->session->get('userType') == 5 ) { //tutor, School, Corporation
            return redirect()->to(base_url('tutor/view_course'));
        }
        if ($this->session->get('userType') == 2) { //upper level student
            return redirect()->to(base_url('student/view_course'));
        }if ($this->session->get('userType') == 6) { //student
            return redirect()->to(base_url('student/view_course'));
        }
        if ($this->session->get('userType') == 7) { //qstudy
            return redirect()->to(base_url('qstudy/view_course'));
        }
    }

	
    public function subscription_cancel(){
        $id = $this->session->get('user_id');
        $user = $this->db->table('tbl_useraccount')->where('id',$id)->get()->getRow();
        if ($user->user_type == 6) {
            $data['user_id'] = $user->parent_id;
        }else{
            $data['user_id'] = $this->session->get('user_id');
        }
        $data['end_subscription'] = $user->end_subscription;
        $data['cancel_date'] = date('Y-m-d');

        $check_user = $this->db->table('tbl_cancel_subscription')->where('user_id',$data['user_id'])->get()->getResultArray();
        if (count($check_user) == 0) {
            $result = $this->db->table('tbl_cancel_subscription')->insert($data);
           
        }else{
            $result = $this->db->table('tbl_cancel_subscription')->update($data);

        }

        if($result){
            $this->db->table('tbl_useraccount')->where('id',$data['user_id'])->update(['payment_status'=>'Cancel']);
            if ($user->user_type == 6) {
               $this->db->table('tbl_useraccount')->where('id',$this->session->userdata('user_id'))->update(['payment_status'=>'Cancel']);
            }
        }
        echo "success";
    }

    
    public function subscription_renew(){

        $id = $this->session->get('user_id');
        $user = $this->db->table('tbl_useraccount')->where('id',$id)->get()->getRow();
        if ($user->user_type == 6) {
            $data['user_id'] = $user->parent_id;
        }else{
            $data['user_id'] = $this->session->get('user_id');
        }
        $result = $this->db->table('tbl_cancel_subscription')->where('user_id',$data['user_id'])->delete();
        if($result){
            $this->db->table('tbl_useraccount')->where('id',$data['user_id'])->update(['payment_status'=>'Completed']);

            if ($user->user_type == 6) {
               $this->db->table('tbl_useraccount')->where('id',$this->session->userdata('user_id'))->update(['payment_status'=>'Completed']);
            }

        }
        echo "success";
    }
}
